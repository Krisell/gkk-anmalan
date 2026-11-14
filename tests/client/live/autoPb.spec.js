import { afterEach, beforeEach, describe, expect, it, vi } from 'vite-plus/test'
import { effectScope, nextTick, ref } from 'vue'
import { AFTER_CARD_MS, isPbAttempt, useAutoPb } from '../../../resources/js/live/autoPb.js'

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
  })
})

describe('useAutoPb', () => {
  let scope
  let state
  let shown

  beforeEach(() => {
    vi.useFakeTimers()
    shown = []
    state = { attempt: ref(null), busy: ref(false), clock: ref(null), enabled: ref(true) }
    scope = effectScope()
    scope.run(() =>
      useAutoPb({
        attempt: () => state.attempt.value,
        busy: () => state.busy.value,
        clock: () => state.clock.value,
        enabled: () => state.enabled.value,
        bestsFor: () => BESTS,
        show: (shownAttempt) => shown.push(shownAttempt.id),
      }),
    )
  })

  afterEach(() => {
    scope.stop()
    vi.useRealTimers()
  })

  async function call(next, { covered = true } = {}) {
    state.attempt.value = next
    state.busy.value = covered
    await nextTick()
  }

  async function uncover() {
    state.busy.value = false
    await nextTick()
  }

  async function startClock() {
    state.clock.value = 60
    await nextTick()
  }

  it('shows the stamp a few seconds after the card is down, and again when the clock starts', async () => {
    await call(attempt())
    vi.advanceTimersByTime(AFTER_CARD_MS * 2)
    expect(shown).toEqual([]) // the card is still up

    await uncover()
    vi.advanceTimersByTime(AFTER_CARD_MS - 1)
    expect(shown).toEqual([])
    vi.advanceTimersByTime(1)
    expect(shown).toEqual(['7:BP2'])

    await startClock()
    expect(shown).toEqual(['7:BP2', '7:BP2'])
  })

  it('shows it after the call when there is no card (the same lifter again)', async () => {
    await call(attempt(), { covered: false })
    vi.advanceTimersByTime(AFTER_CARD_MS)
    expect(shown).toEqual(['7:BP2'])
  })

  it('waits for the card when the clock starts while it is up', async () => {
    await call(attempt())
    await startClock()
    expect(shown).toEqual([])
    await uncover()
    vi.advanceTimersByTime(AFTER_CARD_MS)
    expect(shown).toEqual(['7:BP2'])
  })

  it('shows it once per attempt and moment, also when the clock is restarted', async () => {
    await call(attempt(), { covered: false })
    vi.advanceTimersByTime(AFTER_CARD_MS)
    await startClock()
    state.clock.value = null
    await nextTick()
    await startClock()
    state.busy.value = true
    await nextTick()
    await uncover()
    vi.advanceTimersByTime(AFTER_CARD_MS)
    expect(shown).toEqual(['7:BP2', '7:BP2'])
  })

  it('shows nothing for an attempt below the best, or when turned off', async () => {
    await call(attempt({ id: '7:BP1', weight: 62.5 }), { covered: false })
    vi.advanceTimersByTime(AFTER_CARD_MS)
    await startClock()

    state.clock.value = null
    state.enabled.value = false
    await call(attempt(), { covered: false })
    vi.advanceTimersByTime(AFTER_CARD_MS)
    await startClock()
    expect(shown).toEqual([])
  })

  it('starts over for the next lifter', async () => {
    await call(attempt(), { covered: false })
    vi.advanceTimersByTime(AFTER_CARD_MS)
    await call(attempt({ id: '8:BP2', lifterId: '8' }))
    await uncover()
    vi.advanceTimersByTime(AFTER_CARD_MS)
    expect(shown).toEqual(['7:BP2', '8:BP2'])
  })
})
