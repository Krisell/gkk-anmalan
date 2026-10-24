import { onBeforeUnmount, ref, shallowRef } from 'vue'
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
import { liveDatabase } from './firebase.js'
import { DEFAULT_CONTROL, isFreshCommand, normalizeControl } from './controlState.js'

const HEARTBEAT_MS = 30000

/**
 * The overlay's side of the remote control: follows live/control, runs new commands, and (unless `presence` is
 * false, e.g. for the control page's preview) reports that it is online, which competition it shows and what is on
 * air.
 *
 * If the connection drops, the last control state stays in effect and the automatic graphics carry on.
 */
export function useRemoteControl(competition, { onCommand = () => {}, presence = true } = {}) {
  const database = liveDatabase()
  const path = 'live'
  const control = shallowRef(normalizeControl(DEFAULT_CONTROL))
  const connected = ref(false)

  let serverOffset = 0
  let stopped = false
  const seen = new Set()
  const unsubscribes = [
    onValue(databaseRef(database, '.info/serverTimeOffset'), (snapshot) => (serverOffset = snapshot.val() ?? 0)),
    onValue(databaseRef(database, `${path}/control`), (snapshot) => (control.value = normalizeControl(snapshot.val()))),
  ]

  // Commands: only those given while the overlay runs. The ones already there when it starts are noted as seen
  // first, so that a reload never replays e.g. a PB stamp; and one that arrives late (after a lost connection) is
  // skipped if it is no longer fresh.
  const commands = query(databaseRef(database, `${path}/commands`), limitToLast(10))
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

  // Presence: one entry per overlay, removed by the server when the connection is lost.
  const session = presence ? databaseRef(database, `${path}/presence/${randomId()}`) : null
  let reported = { showing: '', feed: '' }
  let heartbeat = null

  function write() {
    if (session && connected.value) {
      set(session, { at: serverTimestamp(), competition: String(competition), ...reported }).catch(() => {})
    }
  }

  unsubscribes.push(
    onValue(databaseRef(database, '.info/connected'), (snapshot) => {
      connected.value = snapshot.val() === true
      if (connected.value && session) {
        onDisconnect(session)
          .remove()
          .then(write)
          .catch(() => {})
      }
    }),
  )
  if (session) {
    heartbeat = setInterval(write, HEARTBEAT_MS)
  }

  function report({ showing, feed }) {
    const next = { showing: String(showing ?? '').slice(0, 200), feed: String(feed ?? '').slice(0, 20) }
    if (next.showing !== reported.showing || next.feed !== reported.feed) {
      reported = next
      write()
    }
  }

  function stop() {
    stopped = true
    clearInterval(heartbeat)
    unsubscribes.forEach((unsubscribe) => unsubscribe())
    if (session) {
      set(session, null).catch(() => {})
    }
  }

  onBeforeUnmount(stop)

  return { control, connected, report }
}

function randomId() {
  const bytes = crypto.getRandomValues(new Uint8Array(12))
  return [...bytes].map((byte) => byte.toString(36).padStart(2, '0')).join('')
}
