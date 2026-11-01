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
import { watchServerClock } from '../../live/remote/serverClock.js'
import { resultsLayout } from './results.js'

/**
 * Live control of the screen slideshow (/slides-for-screen) from /admin/slideshow, through Firebase's Realtime
 * Database under slideshow/ (see database.rules.json), in the same way as the live overlay's remote control:
 *
 * - control: settings that persist until changed (paused, a slide held on screen, a message, a web page shown instead
 *   of the slideshow, the bottom bar hidden with or without a clock),
 * - commands: one-off actions ({ type, at } with the server time it was given; a flash also has its text),
 * - presence/{session}: written by each open screen ({ at, slide, showing, held }), so the admin page can show which
 *   screens are on and what they show,
 * - published: written by the app when the slides or the code change (see App\Services\SlideshowSnapshot).
 *
 * Everything coming from the database is normalized here, so a missing or odd value never breaks the screen.
 */

export const COMMANDS = ['next', 'previous', 'reload', 'flash', 'clearFlash']
export const MESSAGE_MAX_LENGTH = 120

// A flash: the text over the whole screen, then down in the message bar, then gone.
export const FLASH_FULL_MS = 6000
export const FLASH_BAR_MS = 30000

// Commands older than this are not run, e.g. when a screen is (re)loaded after they were given.
export const COMMAND_MAX_AGE_MS = 15000

// A screen that hasn't reported for this long is considered off (it reports at least every 30 s).
export const PRESENCE_TIMEOUT_MS = 75000
const HEARTBEAT_MS = 30000

export const RECORDS_KEY = 'records'
export const RESULTS_KEY = 'results'

// What a screen reports as its slide while it shows a web page instead of the slideshow.
export const PAGE_KEY = 'page'
export const PAGE_URL_MAX_LENGTH = 2000

/**
 * A web page address that can be shown on the screen, or null. Only https: the screen's own page is served over https,
 * and the browser blocks http pages inside it. So an address without a scheme gets https://, and http:// is changed
 * to https:// (most sites serve both). Anything else (javascript:, data:, …) is never shown.
 */
export function pageUrl(value) {
  const input = typeof value === 'string' ? value.trim() : ''
  if (!input || input.length > PAGE_URL_MAX_LENGTH) {
    return null
  }
  try {
    const url = new URL(/^[a-z][a-z0-9+.-]*:/i.test(input) ? input.replace(/^http:/i, 'https:') : `https://${input}`)
    return url.protocol === 'https:' && url.hostname ? url.href : null
  } catch {
    return null
  }
}

export function pageLabel(url) {
  try {
    const { hostname, pathname } = new URL(url)
    return `Webbsida: ${hostname}${pathname === '/' ? '' : pathname}`.slice(0, 200)
  } catch {
    return 'Webbsida'
  }
}

// The slides built into the screen (not in the database), with their keys for the live control.
const BUILT_IN_KEYS = {
  Klubbrekord: RECORDS_KEY,
  Resultat: RESULTS_KEY,
  Dm2026: 'dm2026',
  Gm2027: 'gm2027',
  Swish: 'swish',
  Lokalregler: 'lokalregler',
}
const BUILT_IN_LABELS = {
  Klubbrekord: 'Senaste klubbrekorden',
  Resultat: 'Senaste tävlingsresultaten',
  Dm2026: 'Välkommen till DM 2026',
  Gm2027: 'Götalandsmästerskapen 2027',
  Swish: 'Swisha en gåva till GKK',
  Lokalregler: 'Lokalregler',
}
const SLIDE_KEY = new RegExp(`^(${Object.values(BUILT_IN_KEYS).join('|')}|slide-\\d{1,9})$`)

/**
 * The screen's slides: the latest club records, GKK's competition results from the last 30 days (when there are any)
 * and the visible slides from the admin, in the order they rotate, and
 * then the manual ones (the DM welcome, the Swish slide and the invitation to the Götaland championships 2027 and the house rules), which
 * are left out of the rotation and only shown when held from the live control.
 */
export function rotation(slides, records = [], results = []) {
  const visible = Array.isArray(slides) ? slides.filter((slide) => slide?.is_visible) : []
  return [
    { type: 'Klubbrekord', data: { records } },
    // Shown for longer when there are so many results that they scroll.
    ...(Array.isArray(results) && results.length
      ? [{ type: 'Resultat', data: { results }, duration: resultsLayout(results.length).durationMs }]
      : []),
    ...visible,
    // These use the whole screen: the message bar is hidden while they are shown.
    { type: 'Dm2026', data: {}, manual: true, hidesMessage: true },
    { type: 'Swish', data: {}, manual: true, hidesMessage: true },
    { type: 'Gm2027', data: {}, manual: true, hidesMessage: true },
    { type: 'Lokalregler', data: {}, manual: true, hidesMessage: true },
  ]
}

// The slide after (or before, with -1) the given one in the rotation, skipping the manual slides.
export function nextInRotation(slides, index, direction = 1) {
  const count = slides.length
  let next = index
  for (let step = 0; step < count; step++) {
    next = (next + direction + count) % count
    if (!slides[next]?.manual) {
      return next
    }
  }
  return index
}

// The live control lists these first, in this order, before the rest of the slides.
const PINNED = ['Dm2026', 'Swish', 'Gm2027', 'Lokalregler']

export function controlOrder(slides) {
  const pinned = PINNED.flatMap((type) => slides.filter((slide) => slide.type === type))
  return [...pinned, ...slides.filter((slide) => !PINNED.includes(slide.type))]
}

// A stable key for a slide in the rotation: the built-in slides have no id of their own.
export function slideKey(slide) {
  return BUILT_IN_KEYS[slide?.type] ?? `slide-${slide?.id}`
}

export function slideLabel(slide) {
  if (BUILT_IN_LABELS[slide?.type]) {
    return BUILT_IN_LABELS[slide.type]
  }
  return text(slide?.data?.text, 80) ?? text(slide?.data?.subText, 80) ?? (slide?.data?.image ? 'Bild' : 'Tom slide')
}

const text = (value, max) => (typeof value === 'string' && value.trim() ? value.trim().slice(0, max) : null)

export function normalizeControl(raw) {
  const control = raw && typeof raw === 'object' ? raw : {}
  const message = text(control.message?.text, MESSAGE_MAX_LENGTH)
  const url = pageUrl(control.page?.url)

  return {
    paused: control.paused === true,
    slide: typeof control.slide === 'string' && SLIDE_KEY.test(control.slide) ? control.slide : null,
    message: message ? { id: String(control.message.id ?? message), text: message } : null,
    page: url ? { id: String(control.page.id ?? url), url } : null,
    // The bottom bar (date, week, slide dots and clock) is shown unless hidden; when hidden, the clock can still be
    // shown on its own.
    footer: control.footer !== false,
    clock: control.clock === true,
  }
}

// The text of a flash command, or null.
export function flashText(command) {
  return command?.type === 'flash' ? text(command.text, MESSAGE_MAX_LENGTH) : null
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
  let stopped = false
  const seen = new Set()

  const serverClock = watchServerClock(database)
  const unsubscribes = [
    serverClock.stop,
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
          if (isFreshCommand(command, serverClock.now())) {
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
