import { afterEach, beforeEach, describe, expect, it, vi } from 'vite-plus/test'
import { effectScope, nextTick, ref } from 'vue'
import { AFTER_CALL_MS, CARD_DELAY_MS, isPbAttempt, useAutoPb } from '../../../resources/js/live/autoPb.js'

const BESTS = {
  found: true,
  bests: { squat: { value: 140 }, bench: { value: 65 }, deadlift: { value: 160 }, total: null },
}
const attempt = (overrides = {}) => ({
  id: '7:BP2',
  lifterId: '7',
  name: 'Anna',
  discipline: 'BP',
  weight: 67.5,
  ...overrides,
})

describe('isPbAttempt', () => {
  it('is a PB above the registered best only', () => {
    expect(isPbAttempt(attempt(), BESTS)).toBe(true)
    expect(isPbAttempt(attempt({ weight: 65 }), BESTS)).toBe(false)
    expect(isPbAttempt(attempt(), { found: false })).toBe(false)
    expect(isPbAttempt(attempt(), undefined)).toBe(false)
    // Only the lift counts: a deadlift below the best, with a total above the best total, is not a PB here.
    const deadlift = attempt({
      discipline: 'DL',
      weight: 150,
      lifts: { SQ: [{ weight: 150, outcome: 'good' }], BP: [{ weight: 70, outcome: 'good' }] },
    })
    expect(isPbAttempt(deadlift, { found: true, bests: { ...BESTS.bests, total: { value: 300 } } })).toBe(false)
  })

  it('is never a PB in the first competition: there is nothing registered to beat', () => {
    const none = { squat: null, bench: null, deadlift: null, total: null }
    expect(isPbAttempt(attempt(), { found: true, competitions: 0, bests: none })).toBe(false)
    // Nor in a lift never registered before, e.g. the squat of a lifter who has only competed in bench press.
    expect(
      isPbAttempt(attempt({ discipline: 'SQ', weight: 100 }), {
        found: true,
        bests: { ...none, bench: { value: 65 } },
      }),
    ).toBe(false)
  })

  it('compares an equipped lifter with equipped results only', () => {
    const answer = { ...BESTS, equipped_bests: { squat: null, bench: { value: 90 }, deadlift: null, total: null } }
    expect(isPbAttempt(attempt({ classic: false, weight: 80 }), answer)).toBe(false)
    expect(isPbAttempt(attempt({ classic: false, weight: 92.5 }), answer)).toBe(true)
    expect(isPbAttempt(attempt({ classic: true, weight: 80 }), answer)).toBe(true)
  })
})

describe('useAutoPb', () => {
  let scope
  let state
  let shown
  let moments
  let good

  beforeEach(() => {
    vi.useFakeTimers()
    shown = []
    state = {
      attempt: ref(null),
      card: ref(null),
      busy: ref(false),
      clock: ref(null),
      enabled: ref(true),
      result: ref(null),
    }
    moments = []
    good = []
    scope = effectScope()
    scope.run(() =>
      useAutoPb({
        attempt: () => state.attempt.value,
        card: () => state.card.value,
        busy: () => state.busy.value || Boolean(state.card.value),
        clock: () => state.clock.value,
        enabled: () => state.enabled.value,
        bestsFor: () => BESTS,
        show: (shownAttempt, moment) => {
          shown.push(shownAttempt.id)
          moments.push(moment)
        },
        result: () => state.result.value,
        showGood: (judged) => good.push(judged.id),
      }),
    )
  })

  afterEach(() => {
    scope.stop()
    vi.useRealTimers()
  })

  // The lifter is called: with the big card (the default), or without it (e.g. the same lifter again).
  async function call(next, { withCard = true, covered = false } = {}) {
    state.attempt.value = next
    state.card.value = withCard ? next : null
    state.busy.value = covered
    await nextTick()
  }

  async function cardDown() {
    state.card.value = null
    await nextTick()
  }

  async function startClock() {
    state.clock.value = 60
    await nextTick()
  }

  it('shows the stamp on the card once it is in, and again when the clock starts', async () => {
    await call(attempt())
    vi.advanceTimersByTime(CARD_DELAY_MS - 1)
    expect(shown).toEqual([])
    vi.advanceTimersByTime(1)
    expect(moments).toEqual(['card'])

    await cardDown()
    vi.advanceTimersByTime(AFTER_CALL_MS * 2)
    expect(moments).toEqual(['card']) // not again after the card

    await startClock()
    expect(shown).toEqual(['7:BP2', '7:BP2'])
    expect(moments).toEqual(['card', 'clock'])
  })

  it('shows it after the call when there is no card', async () => {
    await call(attempt(), { withCard: false })
    vi.advanceTimersByTime(AFTER_CALL_MS)
    expect(moments).toEqual(['call'])
  })

  it('shows it to the side after the call when the clock is already running', async () => {
    await call(attempt(), { withCard: false, covered: true })
    await startClock()
    state.busy.value = false
    await nextTick()
    vi.advanceTimersByTime(AFTER_CALL_MS)
    expect(moments).toEqual(['clock', 'clock'])
  })

  it('shows the clock stamp once the card is down when the clock starts while it is up', async () => {
    await call(attempt())
    await startClock()
    vi.advanceTimersByTime(CARD_DELAY_MS)
    expect(moments).toEqual(['card'])
    await cardDown()
    expect(moments).toEqual(['card', 'clock'])
  })

  it('shows it once per attempt and moment, also when the clock is restarted', async () => {
    await call(attempt(), { withCard: false })
    vi.advanceTimersByTime(AFTER_CALL_MS)
    await startClock()
    state.clock.value = null
    await nextTick()
    await startClock()
    state.busy.value = true
    await nextTick()
    state.busy.value = false
    await nextTick()
    vi.advanceTimersByTime(AFTER_CALL_MS)
    expect(shown).toEqual(['7:BP2', '7:BP2'])
  })

  it('shows nothing for an attempt below the best, or when turned off', async () => {
    await call(attempt({ id: '7:BP1', weight: 62.5 }))
    vi.advanceTimersByTime(CARD_DELAY_MS)
    await startClock()

    state.clock.value = null
    state.enabled.value = false
    await call(attempt())
    vi.advanceTimersByTime(CARD_DELAY_MS)
    await startClock()
    expect(shown).toEqual([])
  })

  it('starts over for the next lifter', async () => {
    await call(attempt())
    vi.advanceTimersByTime(CARD_DELAY_MS)
    await call(attempt({ id: '8:BP2', lifterId: '8' }))
    vi.advanceTimersByTime(CARD_DELAY_MS)
    expect(shown).toEqual(['7:BP2', '8:BP2'])
  })

  it('says "NYTT PB!" once when a PB attempt is judged good', async () => {
    state.result.value = attempt({ outcome: null })
    await nextTick()
    state.result.value = attempt({ outcome: 'good' })
    await nextTick()
    state.result.value = { ...attempt({ outcome: 'good' }), confirmed: true }
    await nextTick()
    expect(good).toEqual(['7:BP2'])
  })

  it('says nothing for a failed PB attempt, a good lift below the best, or when turned off', async () => {
    state.result.value = attempt({ outcome: 'failed' })
    await nextTick()
    state.result.value = attempt({ id: '7:BP1', weight: 62.5, outcome: 'good' })
    await nextTick()
    state.enabled.value = false
    state.result.value = attempt({ id: '7:BP3', weight: 70, outcome: 'good' })
    await nextTick()
    expect(good).toEqual([])
  })
})
