import { describe, expect, it } from 'vite-plus/test'
import { defaultEditorState, editorFeed } from '../../../resources/js/live/sources/editorSource.js'
import { normalizeFeed } from '../../../resources/js/live/normalize.js'

// A deterministic "random" source for the made-up history.
function sequence(...values) {
  let i = 0
  return () => values[i++ % values.length]
}

const state = (overrides = {}) => ({ ...defaultEditorState(sequence(0.1, 0.5, 0.2, 0.7)), ...overrides })

describe('editorFeed', () => {
  it('shows the default lifter on the first squat attempt, with the next lifter', () => {
    const snapshot = normalizeFeed(editorFeed(state()))

    expect(snapshot.current).toMatchObject({
      id: 'editor:SQ1',
      name: 'Anders Andersson',
      team: 'Stark AK',
      weightClass: '93',
      discipline: 'SQ',
      attempt: 1,
      weight: 200,
      outcome: null,
    })
    expect(snapshot.current.attempts.map((slot) => slot.weight)).toEqual([200, null, null])
    expect(snapshot.next).toMatchObject({ name: 'Bertil Svensson', weight: 192.5 })
    expect(snapshot.lastResult).toBeNull()
    expect(snapshot.decision).toBeNull()
  })

  it('makes up earlier attempts below the requested weight', () => {
    const { current } = normalizeFeed(editorFeed(state({ attempt: 3, weight: 210 })))

    const [first, second, third] = current.attempts
    expect(third).toEqual({ weight: 210, outcome: null })
    expect(second.weight).toBeLessThan(210)
    expect(first.weight).toBeLessThan(second.weight)
    expect(['good', 'failed']).toContain(first.outcome)
    expect(['good', 'failed']).toContain(second.outcome)
  })

  it('makes up the earlier disciplines, total and placing', () => {
    const { current } = normalizeFeed(editorFeed(state({ discipline: 'DL', attempt: 2, weight: 240 })))

    expect(current).toMatchObject({ id: 'editor:DL2', discipline: 'DL', weight: 240 })
    expect(current.lifts.SQ.every((slot) => slot.weight > 0 && slot.outcome)).toBe(true)
    expect(current.lifts.BP.every((slot) => slot.weight > 0 && slot.outcome)).toBe(true)
    expect(current.lifts.SQ[2].weight).toBeGreaterThan(current.lifts.BP[2].weight)
    expect(current.total).toBeGreaterThan(0)
    expect(current.place).toBeGreaterThanOrEqual(1)
  })

  it('turns a result button into a referee decision and a recorded result', () => {
    const snapshot = normalizeFeed(editorFeed(state({ attempt: 2, result: 'wrr', decisions: 3 })))

    expect(snapshot.lastResult).toMatchObject({ id: 'editor:SQ2', outcome: 'failed' })
    expect(snapshot.decision).toEqual({
      id: '3:w,r,by',
      lights: [
        { good: true, cards: [] },
        { good: false, cards: ['red'] },
        { good: false, cards: ['blue', 'yellow'] },
      ],
    })
  })

  it('makes up a group with two weight classes and Senior and Junior, ranked within each', () => {
    const { scoreboard, current } = normalizeFeed(editorFeed(state({ discipline: 'BP', attempt: 2, weight: 140 })))

    const sections = [...new Set(scoreboard.lifters.map((lifter) => `${lifter.weightClass} ${lifter.category}`))]
    expect(sections).toEqual(['93 Senior', '93 Junior', '105 Senior', '105 Junior'])
    expect(scoreboard.disciplines).toEqual(['SQ', 'BP', 'DL'])

    for (const section of sections) {
      const lifters = scoreboard.lifters.filter((lifter) => `${lifter.weightClass} ${lifter.category}` === section)
      expect(lifters.map((lifter) => lifter.place)).toEqual(lifters.map((_, index) => index + 1))
      expect(lifters.map((lifter) => lifter.total)).toEqual(lifters.map((lifter) => lifter.total).sort((a, b) => b - a))
    }

    // IPF GL points: men's classic coefficients, plausible for these totals and body weights.
    for (const lifter of scoreboard.lifters) {
      expect(lifter.points).toBeGreaterThan(30)
      expect(lifter.points).toBeLessThan(120)
    }

    // The edited lifter's place on the lifter card is the same as in the list.
    expect(scoreboard.lifters.find((lifter) => lifter.id === 'editor').place).toBe(current.place)
  })

  it('hides the next lifter when no name is given', () => {
    expect(normalizeFeed(editorFeed(state({ nextName: ' ' }))).next).toBeNull()
  })
})
