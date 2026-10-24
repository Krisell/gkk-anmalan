import { computed, onBeforeUnmount, shallowRef, watch } from 'vue'
import { normalizeResults } from './normalize.js'

const REFRESH_MS = 30000

/**
 * The results of one group of the competition (`group`, a ref with its name, or null), from /live/results/{id}.
 * Only fetched while a group is asked for, and refreshed every 30 s meanwhile (the proxy caches as long).
 */
export function useGroupResults(competition, group) {
  const results = shallowRef([])
  let timer = null
  let controller = null

  async function load() {
    clearTimeout(timer)
    controller?.abort()
    controller = new AbortController()
    try {
      const response = await fetch(new URL(`results/${encodeURIComponent(competition)}`, window.location.href), {
        signal: controller.signal,
        headers: { Accept: 'application/json' },
      })
      if (response.ok) {
        results.value = normalizeResults(await response.json())
      }
    } catch {
      // Keep what we have; try again shortly.
    }
    if (group.value) {
      timer = setTimeout(load, REFRESH_MS)
    }
  }

  watch(
    group,
    (name) => {
      clearTimeout(timer)
      if (name) {
        void load()
      }
    },
    { immediate: true },
  )

  onBeforeUnmount(() => {
    clearTimeout(timer)
    controller?.abort()
  })

  return computed(() => results.value.find((entry) => entry.name === group.value) ?? null)
}
