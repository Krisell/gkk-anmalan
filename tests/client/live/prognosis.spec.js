import { describe, expect, it } from 'vite-plus/test'
import { prognosisPoints, prognosisTotal } from '../../../resources/js/live/prognosis.js'
import { ipfGlPoints } from '../../../resources/js/live/ipfPoints.js'

const slots = (...entries) => [0, 1, 2].map((i) => entries[i] ?? { weight: null, outcome: null })
const good = (weight) => ({ weight, outcome: 'good' })
const failed = (weight) => ({ weight, outcome: 'failed' })
const declared = (weight) => ({ weight, outcome: null })

const ALL = ['SQ', 'BP', 'DL']

describe('prognosisTotal', () => {
  it('counts every declared attempt not yet judged as good', () => {
    const lifter = {
      lifts: {
        SQ: slots(good(200), failed(210), good(210)),
        BP: slots(good(130), declared(137.5)),
        DL: slots(declared(250)),
      },
    }
    expect(prognosisTotal(lifter, ALL)).toBe(210 + 137.5 + 250)
  })

  it('keeps the best good lift after a failed last attempt', () => {
    const lifter = {
      lifts: { SQ: slots(good(200), failed(210), failed(210)), BP: slots(good(130)), DL: slots(declared(250)) },
    }
    expect(prognosisTotal(lifter, ALL)).toBe(580)
  })

  it('has none for a lifter who is out, or without a declared opener', () => {
    const out = {
      lifts: { SQ: slots(failed(200), failed(200), failed(200)), BP: slots(good(130)), DL: slots(declared(250)) },
    }
    const noOpener = { lifts: { SQ: slots(good(200)), BP: slots(good(130)), DL: slots() } }
    expect(prognosisTotal(out, ALL)).toBeNull()
    expect(prognosisTotal(noOpener, ALL)).toBeNull()
    expect(prognosisTotal({ lifts: {} }, [])).toBeNull()
  })

  it('only counts the disciplines of the competition', () => {
    expect(prognosisTotal({ lifts: { BP: slots(good(100), declared(105)) } }, ['BP'])).toBe(105)
  })
})

describe('prognosisPoints', () => {
  it('gives the IPF GL points of the prognosis, with the bench formula in a bench-only competition', () => {
    const lifter = { sex: 'f', bodyWeight: 64.35, lifts: { BP: slots(good(90), declared(92.5)) } }
    expect(prognosisPoints(lifter, ['BP'])).toBeCloseTo(
      ipfGlPoints({ total: 92.5, bodyWeight: 64.35, sex: 'f', event: 'bench' }),
      6,
    )
    expect(prognosisPoints({ ...lifter, bodyWeight: null }, ['BP'])).toBeNull()
  })
})
