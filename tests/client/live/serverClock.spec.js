import { describe, expect, it } from 'vite-plus/test'
import { createServerClock } from '../../../resources/js/live/remote/serverClock.js'

// A device whose wall clock can be set, next to a monotonic clock that only moves forward.
function device(wall) {
  const time = { wall, monotonic: 0 }
  return {
    time,
    tick: (ms) => {
      time.wall += ms
      time.monotonic += ms
    },
    clock: createServerClock({ wallNow: () => time.wall, monotonicNow: () => time.monotonic }),
  }
}

const SERVER = 1_800_000_000_000

describe('createServerClock', () => {
  it('tells the server time from the measured offset', () => {
    const { clock, tick } = device(SERVER - 5000)
    clock.setOffset(5000)
    expect(clock.now()).toBe(SERVER)
    tick(1000)
    expect(clock.now()).toBe(SERVER + 1000)
  })

  it('stays right when the device clock is corrected after connecting', () => {
    // A Raspberry Pi that boots with yesterday's time, connects, and then gets the right time from NTP.
    const { clock, time, tick } = device(SERVER - 86_400_000)
    clock.setOffset(86_400_000)
    tick(10_000)
    time.wall = SERVER + 10_000
    tick(2000)
    expect(clock.now()).toBe(SERVER + 12_000)
  })

  it('takes the time again on a new offset or connection', () => {
    const { clock, time } = device(SERVER)
    clock.setOffset(0)
    time.wall = SERVER + 60_000 // e.g. a laptop that slept, while the monotonic clock stood still
    clock.sync()
    expect(clock.now()).toBe(SERVER + 60_000)
    clock.setOffset(-60_000)
    expect(clock.now()).toBe(SERVER)
  })

  it('ignores an odd offset', () => {
    const { clock } = device(SERVER)
    clock.setOffset(null)
    expect(clock.now()).toBe(SERVER)
  })
})
