/**
 * The layout of the competition results slide (ResultatSlide.vue), and how long it is shown.
 *
 * Up to 12 results fit on the slide at once, the cards growing to fill it. After a large competition there can be
 * 30–40: then they are shown in rows of four, three rows at a time, scrolling slowly from the top to the bottom, and
 * the slide stays up until the scroll is done.
 */

export const SLIDE_MS = 30 * 1000
export const MAX_VISIBLE_ROWS = 3
export const SCROLL_PAUSE_MS = 6000
export const SCROLL_ROW_MS = 4500

export function resultsLayout(count) {
  const total = Math.max(Number(count) || 0, 1)
  const columns = total <= 1 ? 1 : total <= 4 ? 2 : total <= 9 ? 3 : 4
  const rows = Math.ceil(total / columns)
  const visibleRows = Math.min(rows, MAX_VISIBLE_ROWS)
  const hiddenRows = rows - visibleRows
  const scrollMs = hiddenRows * SCROLL_ROW_MS
  return {
    columns,
    rows,
    visibleRows,
    scrolls: hiddenRows > 0,
    scrollMs,
    // A pause at the top, the scroll, and a pause at the bottom; never shorter than the other slides.
    durationMs: hiddenRows > 0 ? Math.max(SLIDE_MS, SCROLL_PAUSE_MS + scrollMs + SCROLL_PAUSE_MS) : SLIDE_MS,
  }
}
