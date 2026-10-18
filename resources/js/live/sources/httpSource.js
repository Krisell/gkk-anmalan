/**
 * Reads the live competition state from the Laravel proxy (LiveOverlayController@feed).
 */
export function createHttpSource(competition) {
  // Relative to /live/overlay, so the overlay also works if the site is served from a sub path.
  const url = new URL(`feed/${encodeURIComponent(competition)}`, window.location.href)

  return {
    async load(signal) {
      const response = await fetch(url, { signal, cache: 'no-store', headers: { Accept: 'application/json' } })

      if (!response.ok) {
        throw new Error(`Feed responded with HTTP ${response.status}`)
      }

      return response.json()
    },
  }
}
