import { describe, expect, it } from 'vite-plus/test'
import { initialOverlayState, reduceOverlayState } from '../../../resources/js/live/overlayState.js'

function attempt(id, extra = {}) {
  const [lifterId] = id.split(':')
  return { id, lifterId, name: `Lifter ${lifterId}`, attempts: [], outcome: null, ...extra }
}

function snapshot({ current = null, lastResult = null, decision = null } = {}) {
  return { discipline: 'SQ', group: null, current, lastResult, decision }
}

const lights = (...values) => values.map((good) => ({ good, cards: [] }))

function run(snapshots) {
  let state = initialOverlayState()
  let now = 0
  return snapshots.map((snap) => {
    now += 1000
    state = reduceOverlayState(state, snap, now)
    return state
  })
}

describe('reduceOverlayState', () => {
  it('does not show results that already existed when the overlay started', () => {
    const [state] = run([
      snapshot({
        current: attempt('2:SQ1'),
        lastResult: attempt('1:SQ1', { outcome: 'good' }),
        decision: { id: 'd1', lights: lights(true, true, true) },
      }),
    ])

    expect(state.result).toBeNull()
  })

  it('shows a newly recorded result once and does not replay it for unchanged data', () => {
    const before = snapshot({ current: attempt('1:SQ1') })
    const after = snapshot({ current: attempt('2:SQ1'), lastResult: attempt('1:SQ1', { outcome: 'failed' }) })

    const states = run([before, after, after, after])

    expect(states[1].result).toMatchObject({ id: '1:SQ1', outcome: 'failed', decisions: null, shownAt: 2000 })
    expect(states[2].result).toBe(states[1].result)
    expect(states[3].result).toBe(states[1].result)
  })

  it('shows referee lights for the lifter on the platform before the result is recorded', () => {
    const decision = { id: 'd1', lights: lights(true, false, false) }
    const states = run([
      snapshot({ current: attempt('1:SQ1', { weight: 100 }) }),
      snapshot({ current: attempt('1:SQ1', { weight: 100 }), decision }),
      snapshot({
        current: attempt('2:SQ1'),
        decision,
        lastResult: attempt('1:SQ1', { outcome: 'failed', weight: 100 }),
      }),
    ])

    expect(states[1].result).toMatchObject({
      id: '1:SQ1',
      outcome: 'failed',
      decisions: decision.lights,
      shownAt: 2000,
    })
    // The recorded result confirms the same attempt: same shownAt, so no new animation.
    expect(states[2].result).toMatchObject({
      id: '1:SQ1',
      outcome: 'failed',
      decisions: decision.lights,
      shownAt: 2000,
    })
  })

  it('attributes lights to the previous lifter when decision and next lifter arrive together', () => {
    const decision = { id: 'd1', lights: lights(true, true, false) }
    const states = run([
      snapshot({ current: attempt('1:SQ1') }),
      snapshot({ current: attempt('2:SQ1'), decision, lastResult: attempt('1:SQ1', { outcome: 'good' }) }),
    ])

    expect(states[1].result).toMatchObject({ id: '1:SQ1', outcome: 'good', decisions: decision.lights })
  })

  it("records how a good lift changed the lifter's place", () => {
    const states = run([
      snapshot({ current: attempt('1:SQ2', { place: 3 }) }),
      snapshot({ current: attempt('2:SQ2'), lastResult: attempt('1:SQ2', { outcome: 'good', place: 1 }) }),
      snapshot({ current: attempt('3:SQ2'), lastResult: attempt('2:SQ2', { outcome: 'failed', place: 2 }) }),
    ])

    expect(states[1].result.placeChange).toEqual({ from: 3, to: 1 })
    expect(states[2].result.placeChange).toBeNull()
  })

  it('marks the last lift of a round', () => {
    const lift = (id, round, outcome = null) => attempt(id, { attempt: round, discipline: 'SQ', outcome })
    const states = run([
      snapshot({ current: lift('1:SQ1', 1) }),
      // The next lifter is on the same attempt: the round goes on.
      snapshot({ current: lift('2:SQ1', 1), lastResult: lift('1:SQ1', 1, 'good') }),
      // The next lifter is on the second attempt: the first round is over.
      snapshot({ current: lift('1:SQ2', 2), lastResult: lift('2:SQ1', 1, 'failed') }),
      // Nobody is called any more: the group is done.
      snapshot({ current: null, lastResult: lift('1:SQ2', 2, 'good') }),
    ])

    expect(states[1].result.endsRound).toBe(false)
    expect(states[2].result.endsRound).toBe(true)
    expect(states[3].result.endsRound).toBe(true)
  })

  it("does not end a round when the next lifter's attempt is unknown", () => {
    const states = run([
      snapshot({ current: attempt('1:SQ1', { attempt: 1, discipline: 'SQ' }) }),
      snapshot({
        current: attempt('2:SQ?', { attempt: null, discipline: 'SQ' }),
        lastResult: attempt('1:SQ1', { attempt: 1, discipline: 'SQ', outcome: 'good' }),
      }),
    ])

    expect(states[1].result.endsRound).toBe(false)
  })

  it('lets a new result replace one that is still shown', () => {
    const states = run([
      snapshot({ current: attempt('1:SQ1') }),
      snapshot({ current: attempt('2:SQ1'), lastResult: attempt('1:SQ1', { outcome: 'good' }) }),
      snapshot({ current: attempt('3:SQ1'), lastResult: attempt('2:SQ1', { outcome: 'failed' }) }),
    ])

    expect(states[2].result).toMatchObject({ id: '2:SQ1', outcome: 'failed', shownAt: 3000 })
  })

  it('does not reuse an old decision for a manually recorded result', () => {
    const decision = { id: 'd1', lights: lights(true, true, true) }
    const states = run([
      snapshot({ current: attempt('1:SQ1') }),
      snapshot({ current: attempt('2:SQ1'), decision, lastResult: attempt('1:SQ1', { outcome: 'good' }) }),
      snapshot({ current: attempt('3:SQ1'), decision, lastResult: attempt('2:SQ1', { outcome: 'good' }) }),
    ])

    expect(states[2].result).toMatchObject({ id: '2:SQ1', decisions: null })
  })

  it('moves the lights to the recorded result if they were shown for the wrong lifter', () => {
    const decision = { id: 'd1', lights: lights(true, true, true) }
    const states = run([
      snapshot({ current: attempt('1:SQ1') }),
      snapshot({ current: attempt('1:SQ1'), decision }),
      snapshot({ current: attempt('3:SQ1'), decision, lastResult: attempt('2:SQ1', { outcome: 'good' }) }),
    ])

    expect(states[2].result).toMatchObject({ id: '2:SQ1', decisions: decision.lights })
  })
})
