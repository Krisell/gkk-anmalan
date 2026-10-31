import { onBeforeUnmount, shallowRef } from 'vue'
import {
  get,
  limitToLast,
  onChildAdded,
  onDisconnect,
  onValue,
  query,
  ref as databaseRef,
  serverTimestamp,
  set,
} from 'firebase/database'
import { liveDatabase } from '../../live/remote/firebase.js'

/**
 * Live control of the screen slideshow (/slides-for-screen) from /admin/slideshow/live, through Firebase's Realtime
 * Database under slideshow/ (see database.rules.json), in the same way as the live overlay's remote control:
 *
 * - control: settings that persist until changed (paused, a slide held on screen, a message),
 * - commands: one-off actions ({ type, at } with the server time it was given),
 * - presence/{session}: written by each open screen ({ at, slide, showing, held }), so the admin page can show which
 *   screens are on and what they show,
 * - published: written by the app when the slides or the code change (see App\Services\SlideshowSnapshot).
 *
 * Everything coming from the database is normalized here, so a missing or odd value never breaks the screen.
 */

export const COMMANDS = ['next', 'previous', 'reload']
export const MESSAGE_MAX_LENGTH = 120

// Commands older than this are not run, e.g. when a screen is (re)loaded after they were given.
export const COMMAND_MAX_AGE_MS = 15000

// A screen that hasn't reported for this long is considered off (it reports at least every 30 s).
export const PRESENCE_TIMEOUT_MS = 75000
const HEARTBEAT_MS = 30000

export const RECORDS_KEY = 'records'

// A stable key for a slide in the rotation: the club records slide has no id of its own.
export function slideKey(slide) {
  return slide?.type === 'Klubbrekord' ? RECORDS_KEY : `slide-${slide?.id}`
}

export function slideLabel(slide) {
  if (slide?.type === 'Klubbrekord') {
    return 'Senaste klubbrekorden'
  }
  return text(slide?.data?.text, 80) ?? text(slide?.data?.subText, 80) ?? (slide?.data?.image ? 'Bild' : 'Tom slide')
}

const text = (value, max) => (typeof value === 'string' && value.trim() ? value.trim().slice(0, max) : null)

export function normalizeControl(raw) {
  const control = raw && typeof raw === 'object' ? raw : {}
  const message = text(control.message?.text, MESSAGE_MAX_LENGTH)

  return {
    paused: control.paused === true,
    slide: typeof control.slide === 'string' && /^(records|slide-\d{1,9})$/.test(control.slide) ? control.slide : null,
    message: message ? { id: String(control.message.id ?? message), text: message } : null,
  }
}

// Whether a command should still be run, given the server's current time.
export function isFreshCommand(command, serverNow) {
  return (
    COMMANDS.includes(command?.type) &&
    typeof command.at === 'number' &&
    Math.abs(serverNow - command.at) < COMMAND_MAX_AGE_MS
  )
}

// The screens that have reported recently, newest first.
export function onlineScreens(presence, now) {
  return Object.values(presence && typeof presence === 'object' ? presence : {})
    .filter((entry) => typeof entry?.at === 'number' && now - entry.at < PRESENCE_TIMEOUT_MS)
    .sort((a, b) => b.at - a.at)
}

/**
 * The screen's side: follows slideshow/control, runs new commands, and reports what it shows.
 *
 * If the connection drops, the last control state stays in effect and the slideshow carries on.
 */
export function useSlideshowRemote({ onCommand = () => {} } = {}) {
  const database = liveDatabase()
  const control = shallowRef(normalizeControl(null))
  let connected = false
  let serverOffset = 0
  let stopped = false
  const seen = new Set()

  const unsubscribes = [
    onValue(databaseRef(database, '.info/serverTimeOffset'), (snapshot) => (serverOffset = snapshot.val() ?? 0)),
    onValue(
      databaseRef(database, 'slideshow/control'),
      (snapshot) => (control.value = normalizeControl(snapshot.val())),
    ),
  ]

  // Commands: only those given while the screen runs. The ones already there when it starts are noted as seen first,
  // so that a reload never replays them (a "reload" command would otherwise reload forever).
  const commands = query(databaseRef(database, 'slideshow/commands'), limitToLast(10))
  get(commands)
    .then((existing) => existing.forEach((command) => void seen.add(command.key)))
    .catch(() => {})
    .finally(() => {
      if (stopped) {
        return
      }
      unsubscribes.push(
        onChildAdded(commands, (snapshot) => {
          if (seen.has(snapshot.key)) {
            return
          }
          seen.add(snapshot.key)
          const command = snapshot.val()
          if (isFreshCommand(command, Date.now() + serverOffset)) {
            onCommand(command)
          }
        }),
      )
    })

  // Presence: one entry per screen, removed by the server when the connection is lost.
  const session = databaseRef(database, `slideshow/presence/${randomId()}`)
  let reported = { slide: '', showing: '', held: false }

  function write() {
    if (connected) {
      set(session, { at: serverTimestamp(), ...reported }).catch(() => {})
    }
  }

  unsubscribes.push(
    onValue(databaseRef(database, '.info/connected'), (snapshot) => {
      connected = snapshot.val() === true
      if (connected) {
        onDisconnect(session)
          .remove()
          .then(write)
          .catch(() => {})
      }
    }),
  )
  const heartbeat = setInterval(write, HEARTBEAT_MS)

  function report({ slide, showing, held }) {
    const next = { slide: String(slide ?? '').slice(0, 40), showing: String(showing ?? '').slice(0, 200), held: !!held }
    if (next.slide !== reported.slide || next.showing !== reported.showing || next.held !== reported.held) {
      reported = next
      write()
    }
  }

  function stop() {
    stopped = true
    clearInterval(heartbeat)
    unsubscribes.forEach((unsubscribe) => unsubscribe())
    set(session, null).catch(() => {})
  }

  onBeforeUnmount(stop)

  return { control, report }
}

function randomId() {
  const bytes = crypto.getRandomValues(new Uint8Array(12))
  return [...bytes].map((byte) => byte.toString(36).padStart(2, '0')).join('')
}
