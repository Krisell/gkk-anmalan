/**
 * The pages of the competition results slide (ResultatSlide.vue), and how long it is shown.
 *
 * The results are shown two at a time, side by side, for 5 seconds each, turning to the next two like the page of a
 * book; the slide stays up until every page has been shown (but at least 10 seconds, with only one or two results).
 */

export const RESULTS_PER_PAGE = 2
export const PAGE_MS = 5000
export const FLIP_MS = 1100
export const MIN_SLIDE_MS = 10 * 1000

export function resultPages(results) {
  const pages = []
  for (let i = 0; i < results.length; i += RESULTS_PER_PAGE) {
    pages.push(results.slice(i, i + RESULTS_PER_PAGE))
  }
  return pages
}

export function resultsDuration(count) {
  const pages = Math.max(Math.ceil((Number(count) || 0) / RESULTS_PER_PAGE), 1)
  return Math.max(pages * PAGE_MS, MIN_SLIDE_MS)
}
