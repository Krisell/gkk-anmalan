import { describe, expect, it } from 'vite-plus/test'
import { compareWithBests } from '../../../resources/js/live/control/personalBest.js'

const bests = {
  squat: { value: 140, date: '2025-03-01', competition: 'SM' },
  bench: { value: 65, date: '2025-03-01', competition: 'SM' },
  deadlift: { value: 160, date: '2025-03-01', competition: 'SM' },
  total: { value: 360, date: '2025-03-01', competition: 'SM' },
}
const lifts = (...attempts) => attempts.map(([weight, outcome]) => ({ weight, outcome }))

describe('compareWithBests', () => {
  it('compares the attempt with the best in its lift', () => {
    expect(compareWithBests({ discipline: 'BP', weight: 67.5 }, bests)).toEqual([
      { key: 'bench', label: 'Försöket', weight: 67.5, best: bests.bench, verdict: 'pb' },
    ])
    expect(compareWithBests({ discipline: 'SQ', weight: 140 }, bests)[0].verdict).toBe('equal')
    expect(compareWithBests({ discipline: 'SQ', weight: 130 }, bests)[0].verdict).toBe('below')
    expect(compareWithBests({ discipline: 'SQ', weight: 130 }, { ...bests, squat: null })[0].verdict).toBe('first')
  })

  it('adds the total on the deadlift, from the best good squat and bench', () => {
    const current = {
      discipline: 'DL',
      weight: 170,
      lifts: {
        SQ: lifts([130, 'good'], [140, 'good'], [150, 'failed']),
        BP: lifts([60, 'good'], [65, 'failed'], [65, 'failed']),
      },
    }
    expect(compareWithBests(current, bests)[1]).toEqual({
      key: 'total',
      label: 'Total om godkänt',
      weight: 370,
      best: bests.total,
      verdict: 'pb',
    })
  })

  it('has no total without a good squat and bench', () => {
    const current = { discipline: 'DL', weight: 170, lifts: { SQ: lifts([130, 'failed']), BP: lifts([60, 'good']) } }
    expect(compareWithBests(current, bests)).toHaveLength(1)
  })

  it('is empty without bests or an attempt', () => {
    expect(compareWithBests({ discipline: 'SQ', weight: 100 }, null)).toEqual([])
    expect(compareWithBests(null, bests)).toEqual([])
  })
})
