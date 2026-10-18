import { describe, expect, it } from 'vite-plus/test'
import { normalizeFeed } from '../../../resources/js/live/normalize.js'

// Shapes follow Powerlifting Live's feed.php topics (as trimmed by LiveOverlayController); names are fictional.
const lifter = {
  id: '110845',
  name: 'Anna Andersson',
  team: 'Göteborg KK',
  weightclass: '57',
  weightclass_short_alt: null,
  category: 'Senior',
  lifts: {
    SQ1: '120.0',
    SQ1_status: 'good',
    SQ2: '127.5',
    SQ2_status: 'failed',
    SQ3: '127.5',
    BP1: '60.0',
  },
}

function feed(overrides = {}) {
  return {
    next_lifters: {
      info: { current_group: 'Grupp 01', current_round: 3, lifter_count: 11 },
      lifters: [{ weight: '127.5', lifter }],
    },
    scoresheet: {
      info: { current_discipline: 'SQ', current_group: 'Grupp 01' },
      last_lifts: [
        {
          lifter_id: '110863',
          attempt: '2',
          discipline: 'SQ',
          lift: 'SQ2',
          weight: '230.0',
          status: 'failed',
          lifter: { id: '110863', name: 'Bo Berg', team: 'Malmö AK', weightclass: '120+', lifts: {} },
        },
      ],
    },
    decision: {},
    ...overrides,
  }
}

describe('normalizeFeed', () => {
  it('derives the current attempt from the lifter on top of next_lifters', () => {
    const snapshot = normalizeFeed(feed())

    expect(snapshot.discipline).toBe('SQ')
    expect(snapshot.group).toBe('Grupp 01')
    expect(snapshot.current).toMatchObject({
      id: '110845:SQ3',
      name: 'Anna Andersson',
      team: 'Göteborg KK',
      weightClass: '57',
      category: 'Senior',
      discipline: 'SQ',
      attempt: 3,
      weight: 127.5,
      outcome: null,
    })
    expect(snapshot.current.attempts).toEqual([
      { weight: 120, outcome: 'good' },
      { weight: 127.5, outcome: 'failed' },
      { weight: 127.5, outcome: null },
    ])
  })

  it('normalizes the lifter after the current one', () => {
    const raw = feed()
    raw.next_lifters.lifters.push({
      weight: '140.0',
      lifter: { id: '110850', name: 'Cecilia Ek', lifts: { SQ1: '130.0', SQ1_status: 'good', SQ2: '140.0' } },
    })

    expect(normalizeFeed(raw).next).toMatchObject({ id: '110850:SQ2', name: 'Cecilia Ek', attempt: 2, weight: 140 })
    expect(normalizeFeed(feed()).next).toBeNull()
  })

  it('skips lifters from the next group', () => {
    const raw = feed()
    raw.next_lifters.lifters.push(
      { weight: '200.0', next_group: 'Grupp 02', lifter: { id: '2', name: 'Next Group' } },
      { weight: '140.0', lifter: { id: '3', name: 'Same Group', lifts: {} } },
    )

    expect(normalizeFeed(raw).next).toMatchObject({ name: 'Same Group', weight: 140 })
  })

  it('builds the results list of the current group', () => {
    const raw = feed()
    raw.scoresheet.lifters = [
      { ...lifter, place: '1', total: 120, wilks: 28.12345 },
      { id: '2', name: 'Bo Berg', team: 'Malmö AK', weightclass: '57', place: '0', lifts: { BP1: '60.0' } },
    ]

    const { scoreboard } = normalizeFeed(raw)

    expect(scoreboard.disciplines).toEqual(['SQ', 'BP'])
    expect(scoreboard.lifters[0]).toMatchObject({
      id: '110845',
      name: 'Anna Andersson',
      place: 1,
      total: 120,
      points: 28.12345,
    })
    expect(scoreboard.lifters[0].lifts.SQ).toEqual([
      { weight: 120, outcome: 'good' },
      { weight: 127.5, outcome: 'failed' },
      { weight: 127.5, outcome: null },
    ])
    expect(scoreboard.lifters[1]).toMatchObject({ name: 'Bo Berg', place: null, total: null })
  })

  it('normalizes the most recent judged lift', () => {
    expect(normalizeFeed(feed()).lastResult).toMatchObject({
      id: '110863:SQ2',
      name: 'Bo Berg',
      weightClass: '120+',
      discipline: 'SQ',
      attempt: 2,
      weight: 230,
      outcome: 'failed',
    })
  })

  it('prefers lift information on the entry itself when available', () => {
    const raw = feed()
    raw.next_lifters.lifters[0] = { ...raw.next_lifters.lifters[0], lift: 'BP1', weight: '60.0' }

    expect(normalizeFeed(raw).current).toMatchObject({ id: '110845:BP1', discipline: 'BP', attempt: 1, weight: 60 })
  })

  it('falls back to the current round when the attempt cannot be derived from the lifts', () => {
    const raw = feed()
    raw.next_lifters.lifters[0] = { weight: '130', lifter: { ...lifter, lifts: {} } }

    expect(normalizeFeed(raw).current).toMatchObject({ id: '110845:SQ3', attempt: 3, weight: 130 })
  })

  it('parses referee decisions including reason cards', () => {
    const snapshot = normalizeFeed(feed({ decision: { updated: 1791711790, left: 'w', center: 'ry', right: 'b' } }))

    expect(snapshot.decision).toEqual({
      id: '1791711790:w,ry,b',
      lights: [
        { good: true, cards: [] },
        { good: false, cards: ['red', 'yellow'] },
        { good: false, cards: ['blue'] },
      ],
    })
  })

  it('handles an empty or finished competition', () => {
    const empty = {
      next_lifters: { lifters: [], info: { current_group: 'Grupp 06', current_round: null } },
      scoresheet: { info: { current_discipline: 'DL' }, last_lifts: [] },
      decision: {},
    }

    expect(normalizeFeed(empty)).toEqual({
      discipline: 'DL',
      group: 'Grupp 06',
      current: null,
      next: null,
      lastResult: null,
      decision: null,
      scoreboard: expect.any(Object),
    })
  })

  it('tolerates missing and malformed data', () => {
    expect(normalizeFeed(null)).toEqual({
      discipline: null,
      group: null,
      current: null,
      next: null,
      lastResult: null,
      decision: null,
      scoreboard: expect.any(Object),
    })
    expect(normalizeFeed({ next_lifters: { lifters: [{}] } }).current).toMatchObject({ name: null, weight: null })
    expect(normalizeFeed({ decision: { updated: 1, left: 'w' } }).decision).toBeNull()
  })
})
