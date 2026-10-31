import { onBeforeUnmount, ref, shallowRef } from 'vue'
import { normalizeNextGroup } from './normalize.js'

const REFRESH_MS = 10000
const REQUEST_TIMEOUT_MS = 5000

/**
 * The start order of the group that lifts next (see normalizeNextGroup), from /live/next-group/{id}, refreshed every
 * 10 s. On errors the last start order is kept; lastSuccessAt tells how old it is.
 */
export function useNextGroup(competition) {
  const nextGroup = shallowRef(null)
  const lastSuccessAt = ref(null)
  const url = new URL(`next-group/${encodeURIComponent(competition)}`, window.location.href)
  let timer = null
  let controller = null
  let stopped = false
  let lastJson = null

  async function load() {
    controller = new AbortController()
    const timeout = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS)
    try {
      const response = await fetch(url, {
        signal: controller.signal,
        cache: 'no-store',
        headers: { Accept: 'application/json' },
      })
      if (response.ok) {
        const next = normalizeNextGroup(await response.json())
        const json = JSON.stringify(next)
        if (json !== lastJson) {
          lastJson = json
          nextGroup.value = next
        }
        lastSuccessAt.value = Date.now()
      }
    } catch {
      // Keep what we have; try again shortly.
    } finally {
      clearTimeout(timeout)
    }
    if (!stopped) {
      timer = setTimeout(load, REFRESH_MS)
    }
  }

  void load()

  onBeforeUnmount(() => {
    stopped = true
    clearTimeout(timer)
    controller?.abort()
  })

  return { nextGroup, lastSuccessAt }
}
