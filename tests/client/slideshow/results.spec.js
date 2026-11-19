import { describe, expect, it } from 'vite-plus/test'
import { resultPages, resultsDuration } from '../../../resources/js/components/Slideshow/results.js'
import { rotation } from '../../../resources/js/components/Slideshow/remote.js'

describe('results pages', () => {
  it('shows the results two at a time, the last page with one when odd', () => {
    expect(resultPages([1, 2, 3, 4, 5])).toEqual([[1, 2], [3, 4], [5]])
    expect(resultPages([])).toEqual([])
  })

  it('shows each page for 5 seconds, but the slide for at least 10', () => {
    expect(resultsDuration(1)).toBe(10000)
    expect(resultsDuration(4)).toBe(10000)
    expect(resultsDuration(5)).toBe(15000)
    expect(resultsDuration(40)).toBe(100000)
  })

  it('gives the results slide its time in the rotation', () => {
    const results = Array.from({ length: 40 }, (_, id) => ({ id }))
    const slide = rotation([], [], results).find((s) => s.type === 'Resultat')
    expect(slide.duration).toBe(100000)
  })
})
