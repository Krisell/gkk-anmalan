import { describe, expect, it } from 'vite-plus/test'
import {
  CALL_RACE_MS,
  INTRO_DELAY_MS,
  INTRO_MAX_WAIT_MS,
  initialHallState,
  readyToIntroduce,
  reduceHallState,
} from '../../../resources/js/live/hallState.js'

const attempt = (lifterId, lift) => ({ id: `${lifterId}:${lift}`, lifterId, name: `Lifter ${lifterId}` })
const result = (lifterId, lift) => ({ ...attempt(lifterId, lift), outcome: 'good' })

const anna = attempt('1', 'DL2')
const bo = attempt('2', 'DL2')

// Runs the steps ({ at, current, lit, lastResult }) from a page opened at time 0 with Anna on the platform.
function run(steps, start = { current: anna, lit: false, lastResult: null }) {
  let state = reduceHallState(initialHallState(), { ...start, now: 0 })
  const states = [state]
  let last = { ...start }
  for (const { at, ...change } of steps) {
    last = { ...last, ...change }
    state = reduceHallState(state, { ...last, now: at })
    states.push(state)
  }
  return states
}

describe('whose lift the lights belong to', () => {
  it('is the lifter on the platform when the lights come on', () => {
    const states = run([
      { at: 60000, lit: true },
      { at: 61000, lastResult: result('1', 'DL2') },
      { at: 62000, current: bo },
    ])

    expect(states.slice(1).map((state) => state.judged?.lifterId)).toEqual(['1', '1', '1'])
  })

  it('stays with that lifter when the next lifter is called while the referees are deciding', () => {
    const [, lit, called] = run([
      { at: 60000, lit: true },
      { at: 60500, current: bo },
    ])

    expect(lit.judged.lifterId).toBe('1')
    expect(called.judged.lifterId).toBe('1')
  })

  it('is the replaced lifter when the next lifter is called just before the lights reach us', () => {
    const [, , lit] = run([
      { at: 60000, current: bo },
      { at: 61500, lit: true },
    ])

    expect(lit.judged).toEqual(anna)
  })

  it('is the replaced attempt when the same lifter is called for the next attempt just before', () => {
    const [, , lit] = run([
      { at: 60000, current: attempt('1', 'DL3') },
      { at: 60800, lit: true },
    ])

    expect(lit.judged.id).toBe('1:DL2')
  })

  it('is the recorded result when it comes, even if the result and the call came before the lights', () => {
    const [, , , lit] = run([
      { at: 60000, lastResult: result('1', 'DL2') },
      { at: 60100, current: bo },
      { at: 62000, lit: true },
    ])

    expect(lit.judged.lifterId).toBe('1')
  })

  it('follows a result recorded while the lights are up', () => {
    const [, , recorded] = run([
      { at: 60000, lit: true },
      { at: 61000, lastResult: { ...result('1', 'DL2'), total: 500 } },
    ])

    expect(recorded.judged).toMatchObject({ lifterId: '1', total: 500 })
  })

  it('is the lifter on the platform for lights long after the call', () => {
    const [, , lit] = run([
      { at: 60000, current: bo },
      { at: 60000 + CALL_RACE_MS + 30000, lit: true },
    ])

    expect(lit.judged.lifterId).toBe('2')
  })

  it('is nobody once the lights are gone', () => {
    const [, , gone] = run([
      { at: 60000, lit: true },
      { at: 70000, lit: false },
    ])

    expect(gone.judged).toBeNull()
  })
})

describe('introducing the next lifter', () => {
  it('waits for the lights of the attempt before', () => {
    // Cecilia lifted (with lights) before Anna was called; then Bo is called before Anna's lights reach us.
    const states = run(
      [
        { at: 1000, lit: true },
        { at: 11000, lit: false },
        { at: 12000, current: anna },
        { at: 60000, current: bo },
      ],
      { current: attempt('3', 'DL2'), lit: false, lastResult: null },
    )
    const called = states.at(-1)

    expect(readyToIntroduce(called, 60000 + INTRO_DELAY_MS + 1000)).toBe(false)

    const lit = reduceHallState(called, { current: bo, lit: true, lastResult: null, now: 64500 })
    expect(lit.judged.lifterId).toBe('1')
    expect(readyToIntroduce(lit, 64600)).toBe(false)

    const gone = reduceHallState(lit, { current: bo, lit: false, lastResult: null, now: 74500 })
    expect(readyToIntroduce(gone, 74600)).toBe(true)
  })

  it('goes ahead when the lights came before the call', () => {
    const states = run([
      { at: 60000, lit: true },
      { at: 70000, lit: false },
      { at: 72000, current: bo },
    ])
    const called = states.at(-1)

    expect(readyToIntroduce(called, 72000 + INTRO_DELAY_MS - 1)).toBe(false)
    expect(readyToIntroduce(called, 72000 + INTRO_DELAY_MS)).toBe(true)
  })

  it('gives up waiting for lights that never come', () => {
    const states = run(
      [
        { at: 1000, lit: true },
        { at: 11000, lit: false },
        { at: 12000, current: anna },
        { at: 60000, current: bo },
      ],
      { current: attempt('3', 'DL2'), lit: false, lastResult: null },
    )

    expect(readyToIntroduce(states.at(-1), 60000 + INTRO_MAX_WAIT_MS - 1)).toBe(false)

    expect(readyToIntroduce(states.at(-1), 60000 + INTRO_MAX_WAIT_MS)).toBe(true)
  })

  it('only waits the short delay in a competition without referee lights', () => {
    const [, called] = run([{ at: 60000, current: bo }])

    expect(readyToIntroduce(called, 60000 + INTRO_DELAY_MS)).toBe(true)
  })

  it('never while the lights are up, nor before anyone was called', () => {
    const [opened] = run([])
    expect(readyToIntroduce(opened, 999999)).toBe(false)

    const [, , lit] = run([
      { at: 60000, current: bo },
      { at: 100000, lit: true },
    ])
    expect(readyToIntroduce(lit, 200000)).toBe(false)
  })
})
