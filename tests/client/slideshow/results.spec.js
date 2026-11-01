import { describe, expect, it } from 'vite-plus/test'
import { resultsLayout } from '../../../resources/js/components/Slideshow/results.js'
import { rotation } from '../../../resources/js/components/Slideshow/remote.js'

describe('resultsLayout', () => {
  it('fits up to 12 results on the slide, shown for the usual 30 seconds', () => {
    expect(resultsLayout(1)).toMatchObject({ columns: 1, rows: 1, scrolls: false, durationMs: 30000 })
    expect(resultsLayout(5)).toMatchObject({ columns: 3, rows: 2, scrolls: false, durationMs: 30000 })
    expect(resultsLayout(9)).toMatchObject({ columns: 3, rows: 3, scrolls: false })
    expect(resultsLayout(12)).toMatchObject({ columns: 4, rows: 3, scrolls: false, durationMs: 30000 })
  })

  it('scrolls more, three rows of four at a time, and stays up until the scroll is done', () => {
    expect(resultsLayout(13)).toMatchObject({ columns: 4, rows: 4, visibleRows: 3, scrolls: true, durationMs: 30000 })
    // 10 rows, 7 of them scrolled into view at 4.5 s each, with 6 s at the top and at the bottom.
    expect(resultsLayout(40)).toMatchObject({ rows: 10, visibleRows: 3, scrollMs: 31500, durationMs: 43500 })
  })

  it('gives the results slide its time in the rotation', () => {
    const results = Array.from({ length: 40 }, (_, id) => ({ id }))
    const slide = rotation([], [], results).find((s) => s.type === 'Resultat')
    expect(slide.duration).toBe(43500)
  })
})
