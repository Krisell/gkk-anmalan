import { describe, expect, it } from 'vite-plus/test'
import {
  normalizeFeed,
  normalizeNextGroup,
  normalizeResults,
  normalizeStartLists,
} from '../../../resources/js/live/normalize.js'

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

  it('reads the lifting order, starting with the lifter on the platform', () => {
    const raw = feed()
    raw.next_lifters.lifters.push(
      { weight: '200.0', next_group: 'Grupp 02', lifter: { id: '2', name: 'Next Group' } },
      { weight: '140.0', attempt: '2', lift: 'SQ2', lifter: { id: '3', name: 'Same Group', team: 'Täby AK' } },
    )

    const { order, current } = normalizeFeed(raw)
    expect(order).toHaveLength(2)
    expect(order[0]).toMatchObject({ lifterId: current.lifterId, name: current.name, weight: current.weight })
    expect(order[1]).toEqual({
      lifterId: '3',
      name: 'Same Group',
      team: 'Täby AK',
      discipline: 'SQ',
      attempt: 2,
      weight: 140,
    })
  })

  it('reads the attempt clock as seconds left, and nothing when it is stopped', () => {
    expect(normalizeFeed(feed({ clock: { clock: 42 } })).clock).toBe(42)
    expect(normalizeFeed(feed({ clock: { clock: 0 } })).clock).toBeNull()
    expect(normalizeFeed(feed({ clock: { clock: -5 } })).clock).toBeNull()
  })

  it('builds the results list of the current group', () => {
    const raw = feed()
    raw.scoresheet.lifters = [
      { ...lifter, weight: '56.35', place: '1', total: 120, wilks: 28.12345 },
      { id: '2', name: 'Bo Berg', team: 'Malmö AK', weightclass: '57', place: '0', lifts: { BP1: '60.0' } },
    ]

    const { scoreboard } = normalizeFeed(raw)

    expect(scoreboard.disciplines).toEqual(['SQ', 'BP'])
    expect(scoreboard.lifters[0]).toMatchObject({
      id: '110845',
      name: 'Anna Andersson',
      bodyWeight: 56.35,
      place: 1,
      total: 120,
      points: 28.12345,
    })
    expect(scoreboard.lifters[0].lifts.SQ).toEqual([
      { weight: 120, outcome: 'good' },
      { weight: 127.5, outcome: 'failed' },
      { weight: 127.5, outcome: null },
    ])
    expect(scoreboard.lifters[1]).toMatchObject({ name: 'Bo Berg', bodyWeight: null, place: null, total: null })
  })

  it('keeps a coefficient class together across weight classes, by place', () => {
    const raw = feed()
    const entry = (name, weightclass, category, place) => ({ id: name, name, weightclass, category, place, lifts: {} })
    // As Powerlifting Live lists them: by weight class within the category.
    raw.scoresheet.lifters = [
      entry('A', '63', 'Senior koefficient', '1'),
      entry('B', '63', 'Senior koefficient', '3'),
      entry('C', '84', 'Senior koefficient', '2'),
      entry('D', '84+', 'Senior', '1'),
      entry('E', '52', 'Senior koefficient', '4'),
    ]

    const { scoreboard } = normalizeFeed(raw)

    expect(scoreboard.lifters.map((l) => l.name)).toEqual(['A', 'C', 'B', 'E', 'D'])
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
      order: [],
      lastResult: null,
      decision: null,
      signals: null,
      scoreboard: expect.any(Object),
      clock: null,
    })
  })

  it('tolerates missing and malformed data', () => {
    expect(normalizeFeed(null)).toEqual({
      discipline: null,
      group: null,
      current: null,
      next: null,
      order: [],
      lastResult: null,
      decision: null,
      signals: null,
      scoreboard: expect.any(Object),
      clock: null,
    })
    expect(normalizeFeed({ next_lifters: { lifters: [{}] } }).current).toMatchObject({ name: null, weight: null })
    expect(normalizeFeed({ decision: { updated: 1, left: 'w' } }).decision).toBeNull()
  })
})

describe('normalizeResults', () => {
  const lifter = (id, group, weightclass, place, lifts) => ({
    id,
    name: `Lyftare ${id}`,
    group,
    weightclass,
    category: 'Senior',
    place,
    lifts,
  })
  const finished = { SQ1: '100', SQ1_status: 'good', BP1: '60', BP1_status: 'good', DL1: '150', DL1_status: 'good' }

  it('groups the lifters, in group order, with each group status', () => {
    const groups = normalizeResults({
      lifters: [
        lifter('3', 'Grupp 10', '63', 0, { SQ1: '90' }),
        lifter('1', 'Grupp 02', '57', 2, finished),
        lifter('2', 'Grupp 02', '57', 1, { ...finished, DL2: '160', DL2_status: 'failed' }),
        lifter('4', 'Grupp 03', '63', 1, { ...finished, DL2: '170' }),
      ],
    })

    expect(groups.map(({ name, status }) => ({ name, status }))).toEqual([
      { name: 'Grupp 02', status: 'done' },
      { name: 'Grupp 03', status: 'ongoing' },
      { name: 'Grupp 10', status: 'upcoming' },
    ])
    // Within a group: by place.
    expect(groups[0].scoreboard.lifters.map((l) => l.id)).toEqual(['2', '1'])
    expect(groups[0].scoreboard.disciplines).toEqual(['SQ', 'BP', 'DL'])
  })

  it("gives a group without declared openers the competition's disciplines", () => {
    const groups = normalizeResults({
      lifters: [lifter('1', 'Grupp 01', '57', 1, { BP1: '60' }), lifter('2', 'Grupp 02', '63', null, {})],
    })

    expect(groups[1].scoreboard.disciplines).toEqual(['BP'])
  })

  it('assumes a full powerlifting competition before anyone has declared anything', () => {
    const groups = normalizeResults({ lifters: [lifter('1', 'Grupp 02', '63', null, {})] })

    expect(groups[0].scoreboard.disciplines).toEqual(['SQ', 'BP', 'DL'])
  })

  it('marks a bench press group done once the bench press is finished', () => {
    const groups = normalizeResults({
      lifters: [
        lifter('1', 'Grupp 01', '63', 1, { BP1: '60', BP1_status: 'good', BP2: '65', BP2_status: 'failed' }),
        lifter('2', 'Grupp 02', '63', 1, { BP1: '60', BP1_status: 'good', BP2: '65' }),
      ],
    })

    expect(groups.map(({ status }) => status)).toEqual(['done', 'ongoing'])
  })

  it('copes with missing data', () => {
    expect(normalizeResults(null)).toEqual([])
  })
})

describe('referee signals', () => {
  it('turns the referees decided so far into lights, before the decision', () => {
    const snapshot = normalizeFeed({ referee_signals: { left: 'w', center: null, right: 'ry' } })
    expect(snapshot.signals).toEqual([{ good: true, cards: [] }, null, { good: false, cards: ['red', 'yellow'] }])
    expect(normalizeFeed({ referee_signals: { left: null, center: null, right: null } }).signals).toBeNull()
    expect(normalizeFeed({}).signals).toBeNull()
  })
})

describe('normalizeNextGroup', () => {
  it("reads Powerlifting Live's start order of the next group, in its order", () => {
    const entry = (id, name, weight) => ({
      lifter_id: id,
      attempt: '1',
      discipline: 'DL',
      lift: 'DL1',
      weight,
      lifter: { id, name, team: 'Göteborg KK', group: 'Grupp 01', weightclass: '84', category: 'Senior' },
    })

    expect(
      normalizeNextGroup({ lifters: [entry('1', 'Anna Andersson', '120.0'), 'junk', entry('2', 'Bea Berg', '122.5')] }),
    ).toEqual({
      group: 'Grupp 01',
      discipline: 'DL',
      attempt: 1,
      lifters: [
        { id: '1', name: 'Anna Andersson', team: 'Göteborg KK', weightClass: '84', category: 'Senior', weight: 120 },
        { id: '2', name: 'Bea Berg', team: 'Göteborg KK', weightClass: '84', category: 'Senior', weight: 122.5 },
      ],
    })
  })

  it('is empty during the last block', () => {
    expect(normalizeNextGroup({})).toBeNull()
    expect(normalizeNextGroup({ lifters: [] })).toBeNull()
    expect(normalizeNextGroup(null)).toBeNull()
  })
})

describe('normalizeStartLists', () => {
  const lifter = (id, group, lot, sex = 'f', extra = {}) => ({
    id,
    name: `Lyftare ${id}`,
    team: 'Göteborg KK',
    group,
    lot,
    sex,
    category: 'Senior',
    weightclass: '63',
    weightclass_short_alt: null,
    ...extra,
  })

  it('lists each group by lot number, groups in natural order', () => {
    const groups = normalizeStartLists({
      lifters: [
        lifter('1', 'Grupp 10', '3'),
        lifter('2', 'Grupp 2', '12'),
        lifter('3', 'Grupp 2', '9'),
        lifter('4', 'Grupp 2', '10'),
      ],
    })

    expect(groups.map((group) => group.name)).toEqual(['Grupp 2', 'Grupp 10'])
    expect(groups[0].lifters.map((entry) => entry.lot)).toEqual([9, 10, 12])
    expect(groups[0].lifters[0]).toEqual({
      id: '3',
      lot: 9,
      name: 'Lyftare 3',
      team: 'Göteborg KK',
      category: 'Senior',
      weightClass: '63',
      license: null,
      sex: 'f',
    })
  })

  it('puts lifters without a lot last, by name', () => {
    const [group] = normalizeStartLists({
      lifters: [lifter('b', 'Grupp 1', null), lifter('a', 'Grupp 1', ''), lifter('c', 'Grupp 1', '4')],
    })

    expect(group.lifters.map((entry) => [entry.id, entry.lot])).toEqual([
      ['c', 4],
      ['a', null],
      ['b', null],
    ])
  })

  it('tells whether a group is women, men or both', () => {
    const groups = normalizeStartLists({
      lifters: [
        lifter('1', 'Grupp 1', 1, 'f'),
        lifter('2', 'Grupp 2', 2, 'm'),
        lifter('3', 'Grupp 3', 3, 'f'),
        lifter('4', 'Grupp 3', 4, 'm'),
      ],
    })

    expect(groups.map((group) => group.sex)).toEqual(['f', 'm', null])
  })

  it('prefers the short weight class name and survives odd data', () => {
    const [group] = normalizeStartLists({
      lifters: [lifter('1', 'Grupp 1', 1, 'f', { weightclass: '84', weightclass_short_alt: '84+' }), null, 'x'],
    })

    expect(group.lifters).toHaveLength(1)
    expect(group.lifters[0].weightClass).toBe('84+')

    const [withLicense] = normalizeStartLists({ lifters: [lifter('1', 'Grupp 1', 1, 'f', { license: '010101ab1' })] })
    expect(withLicense.lifters[0].license).toBe('010101ab1')
    expect(normalizeStartLists(null)).toEqual([])
  })
})
