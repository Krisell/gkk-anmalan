import { afterEach, describe, expect, it, vi } from 'vite-plus/test'
import { createHttpSource } from '../../../resources/js/live/sources/httpSource.js'
import {
  currentSignals,
  initialRefereeState,
  isRepeat,
  reduceRefereeMessage,
  refereeFeedUrl,
} from '../../../resources/js/live/sources/refereeFeed.js'

describe('http source', () => {
  afterEach(() => vi.unstubAllGlobals())

  function stubFeed(data) {
    vi.stubGlobal(
      'fetch',
      vi.fn(async () => ({ ok: true, json: async () => data })),
    )
  }

  it('uses the referee feed for the decision, which the proxy never has', async () => {
    stubFeed({ next_lifters: {}, decision: {} })
    const decision = { updated: 1, left: 'w', center: 'w', right: 'r' }
    const source = createHttpSource('2939', { latest: () => decision })

    expect(await source.load()).toEqual({ next_lifters: {}, decision })
  })

  it("keeps the proxy's data when no decision has been received", async () => {
    stubFeed({ next_lifters: {}, decision: {} })
    const source = createHttpSource('2939', { latest: () => null })

    expect(await source.load()).toEqual({ next_lifters: {}, decision: {} })
  })
})

describe('refereeFeedUrl', () => {
  it("points at Powerlifting Live's MQTT WebSocket", () => {
    expect(refereeFeedUrl('https://se.powerliftinglive.com')).toBe('wss://se.powerliftinglive.com/ws')
    expect(refereeFeedUrl(undefined)).toBe('wss://se.powerliftinglive.com/ws')
    expect(refereeFeedUrl('http://localhost:8080/')).toBe('ws://localhost:8080/ws')
  })
})

describe('isRepeat', () => {
  const decision = (updated, left, center, right) => ({ updated, left, center, right })

  it('treats the same lights published again within a few seconds as the same decision', () => {
    // Seen at SM 2026: one decision published six times within half a second.
    expect(isRepeat(decision(1000, 'w', 'w', 'y'), decision(1090, 'w', 'w', 'y'))).toBe(true)
  })

  it('accepts different lights, or the same lights later on, as a new decision', () => {
    expect(isRepeat(decision(1000, 'w', 'w', 'y'), decision(1090, 'w', 'r', 'w'))).toBe(false)
    expect(isRepeat(decision(1000, 'w', 'w', 'w'), decision(61000, 'w', 'w', 'w'))).toBe(false)
    expect(isRepeat(null, decision(1000, 'w', 'w', 'w'))).toBe(false)
  })
})

describe('referee feed', () => {
  const run = (messages) =>
    messages.reduce(
      (state, [topic, message, at]) => reduceRefereeMessage(state, topic, JSON.stringify(message), at),
      initialRefereeState(),
    )

  it('lights each lamp as its referee decides, until the decision', () => {
    const state = run([
      ['signal', { referee: 'center', decision: 'r' }, 1000],
      ['signal', { referee: 'left', decision: 'w' }, 1500],
    ])
    expect(currentSignals(state, 2000)).toEqual({ left: 'w', center: 'r', right: null })

    const decided = reduceRefereeMessage(
      state,
      'decision',
      JSON.stringify({ updated: 2, left: 'w', center: 'r', right: 'w' }),
      3000,
    )
    expect(currentSignals(decided, 3000)).toBeNull()
    expect(decided.decision).toEqual({ updated: 2, left: 'w', center: 'r', right: 'w' })
  })

  it('forgets the signals on referee/clear, or after a while', () => {
    const state = run([['signal', { referee: 'left', decision: 'w' }, 1000]])
    expect(currentSignals(reduceRefereeMessage(state, 'clear', '{}', 2000), 2000)).toBeNull()
    expect(currentSignals(state, 1000 + 21000)).toBeNull()
  })

  it('ignores malformed messages', () => {
    const state = run([['signal', { referee: 'middle', decision: 'w' }, 1000]])
    expect(currentSignals(state, 1000)).toBeNull()
    expect(reduceRefereeMessage(state, 'signal', 'not json', 1000)).toBe(state)
  })
})
