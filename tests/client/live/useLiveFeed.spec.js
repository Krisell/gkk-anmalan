import { afterEach, beforeEach, describe, expect, it, vi } from 'vite-plus/test'
import { createApp, h } from 'vue'
import { useLiveFeed } from '../../../resources/js/live/useLiveFeed.js'

const raw = (name) => ({ next_lifters: { lifters: [{ weight: '100', lifter: { id: '1', name } }] } })

function mount(source) {
  let feed
  const app = createApp({
    setup() {
      feed = useLiveFeed(source, { interval: 1000 })
      return () => h('div')
    },
  })
  app.mount(document.createElement('div'))
  return { feed, unmount: () => app.unmount() }
}

describe('useLiveFeed', () => {
  beforeEach(() => vi.useFakeTimers())
  afterEach(() => vi.useRealTimers())

  it('never overlaps requests', async () => {
    let inFlight = 0
    let maxInFlight = 0
    const source = {
      load: vi.fn(async () => {
        maxInFlight = Math.max(maxInFlight, ++inFlight)
        await new Promise((resolve) => setTimeout(resolve, 2500))
        inFlight--
        return raw('A')
      }),
    }

    const { unmount } = mount(source)
    await vi.advanceTimersByTimeAsync(10000)
    unmount()

    expect(maxInFlight).toBe(1)
    expect(source.load.mock.calls.length).toBeGreaterThan(1)
  })

  it('keeps the latest valid snapshot during failures and recovers', async () => {
    let fail = false
    const source = { load: vi.fn(async () => (fail ? Promise.reject(new Error('down')) : raw('A'))) }

    const { feed, unmount } = mount(source)
    await vi.advanceTimersByTimeAsync(0)
    const first = feed.snapshot.value
    expect(first.current.name).toBe('A')

    fail = true
    await vi.advanceTimersByTimeAsync(3000)
    expect(feed.failures.value).toBeGreaterThan(0)
    expect(feed.error.value).toBe('down')
    expect(feed.snapshot.value).toBe(first)

    fail = false
    await vi.advanceTimersByTimeAsync(6000)
    expect(feed.failures.value).toBe(0)
    unmount()
  })

  it('only replaces the snapshot when the data changed', async () => {
    let name = 'A'
    const source = { load: async () => raw(name) }

    const { feed, unmount } = mount(source)
    await vi.advanceTimersByTimeAsync(0)
    const first = feed.snapshot.value

    await vi.advanceTimersByTimeAsync(3000)
    expect(feed.snapshot.value).toBe(first)

    name = 'B'
    await vi.advanceTimersByTimeAsync(1000)
    expect(feed.snapshot.value.current.name).toBe('B')
    unmount()
  })

  it('shows nothing while the source has nothing yet, without treating it as an outage', async () => {
    let data = null
    const source = { load: async () => data }

    const { feed, unmount } = mount(source)
    await vi.advanceTimersByTimeAsync(0)
    expect(feed.snapshot.value).toBeNull()
    expect(feed.lastSuccessAt.value).not.toBeNull()
    expect(feed.failures.value).toBe(0)

    data = raw('A')
    await vi.advanceTimersByTimeAsync(1000)
    expect(feed.snapshot.value.current.name).toBe('A')
    unmount()
  })

  it('stops polling when unmounted', async () => {
    const source = { load: vi.fn(async () => raw('A')) }

    const { unmount } = mount(source)
    await vi.advanceTimersByTimeAsync(2500)
    unmount()
    const calls = source.load.mock.calls.length

    await vi.advanceTimersByTimeAsync(10000)
    expect(source.load.mock.calls.length).toBe(calls)
  })
})
