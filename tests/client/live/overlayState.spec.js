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
