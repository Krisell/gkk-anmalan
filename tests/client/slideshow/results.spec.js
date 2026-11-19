import { describe, expect, it } from 'vite-plus/test'
import { pageDuration, resultPages, resultsDuration } from '../../../resources/js/components/Slideshow/results.js'
import { rotation } from '../../../resources/js/components/Slideshow/remote.js'

const plain = (id) => ({ id, records: [], personal_bests: [], debut: false })
const pb = (id) => ({ ...plain(id), personal_bests: ['squat'] })
const record = (id) => ({ ...plain(id), records: ['total'] })

describe('results pages', () => {
  it('shows the results two at a time, the last page with one when odd', () => {
    expect(resultPages([1, 2, 3, 4, 5])).toEqual([[1, 2], [3, 4], [5]])
    expect(resultPages([])).toEqual([])
  })

  it('shows a page for 5 seconds, 2 more with a personal best and 10 more with a club record', () => {
    expect(pageDuration([plain(1), plain(2)])).toBe(5000)
    expect(pageDuration([plain(1), pb(2)])).toBe(7000)
    expect(pageDuration([record(1), plain(2)])).toBe(15000)
    expect(pageDuration([record(1), pb(2)])).toBe(17000)
    // A debut's lifts are all personal bests, so they don't count.
    expect(pageDuration([{ ...pb(1), debut: true }])).toBe(5000)
  })

  it('shows the slide until every page is shown, but at least 10 seconds', () => {
    expect(resultsDuration([plain(1)])).toBe(10000)
    expect(resultsDuration([plain(1), plain(2), plain(3), plain(4), pb(5)])).toBe(17000)
  })

  it('gives the results slide its time in the rotation', () => {
    const results = [record(1), ...Array.from({ length: 39 }, (_, id) => plain(id + 2))]
    const slide = rotation([], [], results).find((s) => s.type === 'Resultat')
    expect(slide.duration).toBe(110000)
  })
})
