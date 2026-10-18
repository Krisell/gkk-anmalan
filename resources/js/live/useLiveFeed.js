import { onBeforeUnmount, onMounted, ref, shallowRef } from 'vue'
import { normalizeFeed } from './normalize.js'

const REQUEST_TIMEOUT_MS = 5000
const MAX_BACKOFF_MS = 5000

/**
 * Polls a source (HTTP or demo) and exposes the latest normalized snapshot.
 *
 * - The next request is scheduled only after the previous one finished, so requests never overlap.
 * - On failure the last valid snapshot is kept and polling backs off; callers decide when the data is
 *   too old to show, using lastSuccessAt.
 * - The snapshot ref is only replaced when the normalized data actually changed.
 */
export function useLiveFeed(source, { interval = 1000 } = {}) {
  const snapshot = shallowRef(null)
  const lastSuccessAt = ref(null)
  const failures = ref(0)
  const error = ref(null)

  let timer = null
  let controller = null
  let stopped = true
  let lastJson = null

  async function poll() {
    timer = null
    controller = new AbortController()
    const timeout = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS)

    try {
      const next = normalizeFeed(await source.load(controller.signal))
      const json = JSON.stringify(next)

      if (json !== lastJson) {
        lastJson = json
        snapshot.value = next
      }

      lastSuccessAt.value = Date.now()
      failures.value = 0
      error.value = null
    } catch (e) {
      if (stopped) {
        return
      }

      failures.value++
      error.value = e?.name === 'AbortError' ? 'Timeout' : (e?.message ?? String(e))
    } finally {
      clearTimeout(timeout)
      controller = null
    }

    schedule()
  }

  function schedule() {
    if (stopped) {
      return
    }

    const delay = failures.value ? Math.min(interval * 2 ** failures.value, MAX_BACKOFF_MS) : interval
    timer = setTimeout(poll, delay)
  }

  function start() {
    if (!stopped) {
      return
    }

    stopped = false
    void poll()
  }

  function stop() {
    stopped = true
    clearTimeout(timer)
    timer = null
    controller?.abort()
  }

  // Poll right away (e.g. after a demo step), unless a request is already in flight.
  function refresh() {
    if (stopped || controller) {
      return
    }

    clearTimeout(timer)
    void poll()
  }

  onMounted(start)
  onBeforeUnmount(stop)

  return { snapshot, lastSuccessAt, failures, error, refresh, start, stop }
}
