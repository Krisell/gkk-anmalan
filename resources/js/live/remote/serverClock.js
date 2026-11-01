import { onValue, ref as databaseRef } from 'firebase/database'

/**
 * The server's current time, for judging whether a remote command is fresh, that stays right when the device's own
 * clock is changed while the page runs.
 *
 * Firebase measures the offset between the server's and the device's clock (.info/serverTimeOffset) only when it
 * connects. A device without a battery-backed clock, such as the Raspberry Pi behind the gym's screen, can start with
 * the wrong time and have it corrected a little later; Date.now() + offset is then off by that correction for as long
 * as the connection lasts, and every command looks stale. So the server time is taken once from the offset, and then
 * carried forward with the monotonic clock (performance.now()), which a change of the device's time does not affect.
 * It is taken again whenever the offset changes or the connection comes back (e.g. after a laptop has slept).
 */
export function createServerClock({ wallNow = () => Date.now(), monotonicNow = () => performance.now() } = {}) {
  let offset = 0
  let anchor = null

  function sync() {
    anchor = { server: wallNow() + offset, monotonic: monotonicNow() }
  }

  sync()

  return {
    // The offset Firebase measured, in ms (server time - device time).
    setOffset(value) {
      offset = typeof value === 'number' && Number.isFinite(value) ? value : 0
      sync()
    },
    sync,
    now() {
      return anchor.server + (monotonicNow() - anchor.monotonic)
    },
  }
}

/**
 * A server clock that follows the database's connection. `stop()` removes its listeners.
 */
export function watchServerClock(database) {
  const clock = createServerClock()
  const unsubscribes = [
    onValue(databaseRef(database, '.info/serverTimeOffset'), (snapshot) => clock.setOffset(snapshot.val())),
    onValue(databaseRef(database, '.info/connected'), (snapshot) => {
      if (snapshot.val() === true) {
        clock.sync()
      }
    }),
  ]

  return {
    now: () => clock.now(),
    stop: () => unsubscribes.forEach((unsubscribe) => unsubscribe()),
  }
}
