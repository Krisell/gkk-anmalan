import { describe, expect, it } from 'vite-plus/test'
import { placing } from '../../../resources/js/live/placing.js'

const slots = (...entries) => [0, 1, 2].map((i) => entries[i] ?? { weight: null, outcome: null })
const good = (weight) => ({ weight, outcome: 'good' })
const failed = (weight) => ({ weight, outcome: 'failed' })

const lifter = (id, bodyWeight, lifts, extra = {}) => ({
  id,
  name: `Lyftare ${id}`,
  weightClass: '83',
  category: 'Senior',
  sex: 'm',
  bodyWeight,
  lifts: { SQ: slots(), BP: slots(), DL: slots(), ...lifts },
  ...extra,
})

const attempt = (lifterId, discipline, n, weight) => ({ lifterId, discipline, attempt: n, weight })

describe('placing', () => {
  const board = {
    disciplines: ['SQ', 'BP', 'DL'],
    lifters: [
      lifter('a', 82, { SQ: slots(good(200)), BP: slots(good(130)) }), // 330
      lifter('b', 80, { SQ: slots(good(180)), BP: slots(good(120)) }), // 300
      lifter('c', 81, { SQ: slots(good(170)), BP: slots(good(115), failed(120)) }), // 285
      lifter('d', 79, { SQ: slots(good(160)), BP: slots(good(100)) }), // 260
      // Another class, and one who is out.
      lifter('e', 92, { SQ: slots(good(300)), BP: slots(good(200)) }, { weightClass: '93' }),
      lifter('f', 83, { SQ: slots(failed(250), failed(250), failed(250)), BP: slots(good(200)) }),
    ],
  }

  it('tells the place now and if the attempt is good, within the class', () => {
    expect(placing(attempt('c', 'BP', 3, 135), board)).toEqual({ now: 3, after: 2 })
    expect(placing(attempt('d', 'BP', 2, 105), board)).toEqual({ now: 4, after: 4 })
    expect(placing(attempt('d', 'BP', 2, 175), board)).toEqual({ now: 4, after: 1 })
  })

  it('puts the lighter lifter first on the same subtotal, and the one who had it first on the same weight too', () => {
    // c to 300, like b (80 kg); c is heavier.
    expect(placing(attempt('c', 'BP', 3, 130), board).after).toBe(3)
    expect(
      placing(attempt('c', 'BP', 3, 130), {
        ...board,
        lifters: board.lifters.map((l) => (l.id === 'c' ? { ...l, bodyWeight: 79 } : l)),
      }).after,
    ).toBe(2)
    expect(
      placing(attempt('c', 'BP', 3, 130), {
        ...board,
        lifters: board.lifters.map((l) => (l.id === 'c' ? { ...l, bodyWeight: 80 } : l)),
      }).after,
    ).toBe(3)
  })

  it('has no place now for a lifter without a good lift yet', () => {
    const first = { ...board, lifters: [...board.lifters, lifter('g', 75, {})] }
    expect(placing(attempt('g', 'SQ', 1, 250), first)).toEqual({ now: null, after: 5 })
  })

  it('tells nothing for a lifter who is out, or not in the results', () => {
    expect(placing(attempt('f', 'DL', 1, 250), board)).toBeNull()
    expect(placing(attempt('x', 'DL', 1, 250), board)).toBeNull()
    expect(placing(attempt('a', 'DL', 1, null), board)).toBeNull()
  })

  it('ranks a coefficient class by IPF GL points', () => {
    const coefficient = {
      disciplines: ['BP'],
      lifters: [
        lifter('h', 60, { BP: slots(good(100)) }, { category: 'Junior koefficient' }),
        lifter('i', 100, { BP: slots(good(120)) }, { category: 'Junior koefficient', weightClass: '105' }),
      ],
    }
    // 100 kg at 60 kg body weight is more points than 120 at 100.
    expect(placing(attempt('i', 'BP', 2, 125), coefficient)).toEqual({ now: 2, after: 2 })
    expect(placing(attempt('i', 'BP', 2, 170), coefficient)).toEqual({ now: 2, after: 1 })
  })
})
