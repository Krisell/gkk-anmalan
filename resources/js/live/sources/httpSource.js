/**
 * Reads the live competition state from the Laravel proxy (LiveOverlayController@feed).
 *
 * The proxy can't see referee decisions (see refereeFeed.js); with a referee feed, its latest decision takes the place
 * of the proxy's, and which referees have decided so far is added (referee_signals), so that both go through the same
 * path as everything else (including ?delay=).
 */
export function createHttpSource(competition, referee = null) {
  // Relative to /live/overlay, so the overlay also works if the site is served from a sub path.
  const url = new URL(`feed/${encodeURIComponent(competition)}`, window.location.href)

  return {
    async load(signal) {
      const response = await fetch(url, { signal, cache: 'no-store', headers: { Accept: 'application/json' } })

      if (!response.ok) {
        throw new Error(`Feed responded with HTTP ${response.status}`)
      }

      const data = await response.json()
      const decision = referee?.latest()
      const signals = referee?.signals?.()
      return { ...data, ...(decision ? { decision } : {}), ...(signals ? { referee_signals: signals } : {}) }
    },
  }
}
