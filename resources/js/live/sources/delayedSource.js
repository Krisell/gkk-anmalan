/**
 * Wraps a source so that the overlay shows the competition as it was `delayMs` ago (?delay=N).
 *
 * Meant for lining the HUD up with an external video stream, which runs several seconds behind reality
 * (e.g. YouTube live). Every poll still fetches fresh data, so staleness detection keeps working; the
 * responses are buffered and handed out once they are old enough. During the first `delayMs` after start the
 * oldest response is used, so that the state the overlay starts from is real (and old results never replay).
 */
export function createDelayedSource(source, delayMs, now = () => Date.now()) {
  let buffer = []

  return {
    async load(signal) {
      buffer.push({ at: now(), data: await source.load(signal) })

      const cutoff = now() - delayMs
      const ready = buffer.filter((entry) => entry.at <= cutoff)
      if (!ready.length) {
        return buffer[0].data
      }

      // Keep the newest ready entry (it is what is shown) and everything that is not ready yet.
      const latest = ready.at(-1)
      buffer = buffer.filter((entry) => entry === latest || entry.at > cutoff)
      return latest.data
    },
  }
}

// Accepts a YouTube video id or any common YouTube URL (watch?v=, youtu.be/, /live/, /embed/).
export function youTubeId(value) {
  if (!value) {
    return null
  }

  const match =
    value.match(/(?:v=|youtu\.be\/|\/live\/|\/embed\/|\/shorts\/)([\w-]{11})/) ?? value.match(/^([\w-]{11})$/)
  return match ? match[1] : null
}
