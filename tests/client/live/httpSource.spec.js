import { afterEach, describe, expect, it, vi } from 'vite-plus/test'
import { createHttpSource } from '../../../resources/js/live/sources/httpSource.js'
import { isRepeat, refereeFeedUrl } from '../../../resources/js/live/sources/refereeFeed.js'

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
