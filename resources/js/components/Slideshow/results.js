/**
 * The pages of the competition results slide (ResultatSlide.vue), and how long it is shown.
 *
 * The results are shown two at a time, side by side, turning to the next two like the page of a book. A page is shown
 * for 5 seconds, 2 more with a personal best on it and 10 more with a club record (both: 12 more). The slide stays up
 * until every page has been shown, but at least 10 seconds.
 */

export const RESULTS_PER_PAGE = 2
export const PAGE_MS = 5000
export const PB_EXTRA_MS = 2000
export const RECORD_EXTRA_MS = 10 * 1000
export const FLIP_MS = 1100
export const MIN_SLIDE_MS = 10 * 1000

export function resultPages(results) {
  const pages = []
  for (let i = 0; i < results.length; i += RESULTS_PER_PAGE) {
    pages.push(results.slice(i, i + RESULTS_PER_PAGE))
  }
  return pages
}

// A personal best as the card shows it: not at a debut, where every lift is one.
function hasPersonalBest(result) {
  return !result.debut && (result.personal_bests?.length ?? 0) > 0
}

export function pageDuration(page) {
  return (
    PAGE_MS +
    (page.some(hasPersonalBest) ? PB_EXTRA_MS : 0) +
    (page.some((result) => (result.records?.length ?? 0) > 0) ? RECORD_EXTRA_MS : 0)
  )
}

export function resultsDuration(results) {
  const total = resultPages(results).reduce((sum, page) => sum + pageDuration(page), 0)
  return Math.max(total, MIN_SLIDE_MS)
}
