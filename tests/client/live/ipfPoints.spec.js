import { describe, expect, it } from 'vite-plus/test'
import { ipfGlPoints } from '../../../resources/js/live/ipfPoints.js'
import { normalizeResults } from '../../../resources/js/live/normalize.js'

describe('ipfGlPoints', () => {
  it("gives Powerlifting Live's points for powerlifting", () => {
    // From a real competition (Powerlifting Live's 'wilks' in parentheses).
    expect(ipfGlPoints({ total: 342.5, bodyWeight: 54.55, sex: 'f', event: 'powerlifting' })).toBeCloseTo(83.1203, 3)
    expect(ipfGlPoints({ total: 182.5, bodyWeight: 70.91, sex: 'm', event: 'powerlifting' })).toBeCloseTo(27.39, 2)
  })

  it('uses the bench press formula for bench press', () => {
    expect(ipfGlPoints({ total: 182.5, bodyWeight: 70.91, sex: 'm', event: 'bench' })).toBeCloseTo(99.57, 2)
    expect(ipfGlPoints({ total: 92.5, bodyWeight: 64.35, sex: 'f', event: 'bench' })).toBeGreaterThan(70)
    expect(ipfGlPoints({ total: 182.5, bodyWeight: 70.91, sex: 'm', classic: false, event: 'bench' })).toBeLessThan(
      ipfGlPoints({ total: 182.5, bodyWeight: 70.91, sex: 'm', event: 'bench' }),
    )
  })

  it('has no points without a total, a bodyweight or a known sex', () => {
    expect(ipfGlPoints({ total: null, bodyWeight: 70, sex: 'm', event: 'bench' })).toBeNull()
    expect(ipfGlPoints({ total: 100, bodyWeight: null, sex: 'm', event: 'bench' })).toBeNull()
    expect(ipfGlPoints({ total: 100, bodyWeight: 70, sex: null, event: 'bench' })).toBeNull()
  })
})

describe('points in the results list', () => {
  const lifter = (lifts, extra = {}) => ({
    id: '1',
    name: 'Neo Yngström',
    group: 'Grupp 03',
    sex: 'm',
    raw: '1',
    weight: '70.91',
    total: 182.5,
    wilks: 27.3925,
    lifts,
    ...extra,
  })

  it('are computed with the bench press formula in a bench press group', () => {
    const [group] = normalizeResults({ lifters: [lifter({ BP1: '170.0', BP1_status: 'good', BP2: '182.5' })] })

    expect(group.scoreboard.disciplines).toEqual(['BP'])
    expect(group.scoreboard.lifters[0].points).toBeCloseTo(99.57, 2)
  })

  it("are Powerlifting Live's in a powerlifting group", () => {
    const [group] = normalizeResults({
      lifters: [lifter({ SQ1: '150.0', SQ1_status: 'good', BP1: '100.0', DL1: '200.0' }, { total: 450, wilks: 61.5 })],
    })

    expect(group.scoreboard.lifters[0].points).toBe(61.5)
  })
})
