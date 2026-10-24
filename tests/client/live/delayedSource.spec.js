import { describe, expect, it } from 'vite-plus/test'
import { createDelayedSource, youTubeId } from '../../../resources/js/live/sources/delayedSource.js'

describe('createDelayedSource', () => {
  it('hands out data once it is older than the delay, and nothing while warming up', async () => {
    let time = 0
    let value = 'a'
    const source = createDelayedSource({ load: async () => value }, 3000, () => time)

    expect(await source.load()).toBeNull()
    time = 1000
    value = 'b'
    expect(await source.load()).toBeNull()
    time = 3000
    value = 'c'
    expect(await source.load()).toBe('a')
    time = 4500
    value = 'd'
    expect(await source.load()).toBe('b')
    time = 4600
    expect(await source.load()).toBe('b')
    time = 6000
    expect(await source.load()).toBe('c')
  })

  it('passes errors through so that outages are still detected', async () => {
    const source = createDelayedSource({ load: async () => Promise.reject(new Error('down')) }, 3000)
    await expect(source.load()).rejects.toThrow('down')
  })
})

describe('youTubeId', () => {
  it('extracts the video id from common YouTube links', () => {
    expect(youTubeId('https://www.youtube.com/watch?v=dQw4w9WgXcQ')).toBe('dQw4w9WgXcQ')
    expect(youTubeId('https://youtu.be/dQw4w9WgXcQ?t=10')).toBe('dQw4w9WgXcQ')
    expect(youTubeId('https://www.youtube.com/live/dQw4w9WgXcQ?si=abc')).toBe('dQw4w9WgXcQ')
    expect(youTubeId('dQw4w9WgXcQ')).toBe('dQw4w9WgXcQ')
    expect(youTubeId('https://example.com/video')).toBeNull()
    expect(youTubeId(null)).toBeNull()
  })
})
