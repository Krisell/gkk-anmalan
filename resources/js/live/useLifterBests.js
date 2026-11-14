import { ref, watch } from 'vue'

/**
 * The lifters' best registered results (/live/lifter-bests, from the federation's results database), looked up one at
 * a time as they are wanted and kept in this browser for the day (they don't change during a competition: its results
 * are registered afterwards). Used by the live control, the overlay and the hall screens.
 *
 * bests: { [lifterId]: { found, bests: { squat, bench, deadlift, total }, …, savedAt } }
 * want(ids): looks up the lifters not looked up yet, in the order given; one that failed is tried again after a minute.
 * refresh(id): looks one up again; true if it got an answer.
 */
const STORAGE_KEY = 'live-control-bests'
const MAX_AGE_MS = 12 * 60 * 60 * 1000
const RETRY_MS = 60 * 1000
const PAUSE_MS = 1000

export function useLifterBests(competition) {
  const bests = ref({})
  const failedAt = new Map()
  let wanted = []
  let running = false

  watch(
    competition,
    (id) => {
      failedAt.clear()
      wanted = []
      bests.value = id ? restore(id) : {}
    },
    { immediate: true },
  )

  async function fetchOne(id, lifterId) {
    try {
      const response = await fetch(`/live/lifter-bests/${encodeURIComponent(id)}/${encodeURIComponent(lifterId)}`, {
        headers: { Accept: 'application/json' },
      })
      if (response.status !== 404 && !response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }
      const answer = response.status === 404 ? { found: false } : await response.json()
      if (id === competition()) {
        bests.value = { ...bests.value, [lifterId]: { ...answer, savedAt: Date.now() } }
        save(id, bests.value)
      }
      failedAt.delete(lifterId)
      return true
    } catch {
      failedAt.set(lifterId, Date.now())
      return false
    }
  }

  async function run() {
    if (running) {
      return
    }
    running = true
    try {
      for (;;) {
        const id = competition()
        const next = wanted.find(
          (lifterId) => !bests.value[lifterId] && !(Date.now() - (failedAt.get(lifterId) ?? -Infinity) < RETRY_MS),
        )
        if (!id || !next) {
          return
        }
        await fetchOne(id, next)
        await new Promise((resolve) => setTimeout(resolve, PAUSE_MS))
      }
    } finally {
      running = false
    }
  }

  function want(ids) {
    wanted = [...new Set(ids.filter(Boolean).map(String))]
    void run()
  }

  function refresh(lifterId) {
    const id = competition()
    return id && lifterId ? fetchOne(id, String(lifterId)) : Promise.resolve(false)
  }

  return { bests, want, refresh }
}

function save(competition, bests) {
  try {
    localStorage.setItem(`${STORAGE_KEY}-${competition}`, JSON.stringify(bests))
  } catch {
    // Private browsing or full: kept for this page only.
  }
}

function restore(competition) {
  try {
    const saved = JSON.parse(localStorage.getItem(`${STORAGE_KEY}-${competition}`) ?? '{}')
    return Object.fromEntries(
      Object.entries(saved ?? {}).filter(([, entry]) => Date.now() - (entry?.savedAt ?? 0) < MAX_AGE_MS),
    )
  } catch {
    return {}
  }
}
