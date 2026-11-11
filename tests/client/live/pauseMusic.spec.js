import { describe, expect, it } from 'vite-plus/test'
import { shuffled } from '../../../resources/js/live/pauseMusic.js'

describe('shuffled', () => {
  it('keeps every track once', () => {
    const tracks = ['a.mp3', 'b.mp3', 'c.mp3', 'd.mp3']
    expect(shuffled(tracks).toSorted((a, b) => a.localeCompare(b))).toEqual(tracks)
  })

  it('never starts the next round with the track just played', () => {
    for (let i = 0; i < 200; i++) {
      expect(shuffled(['a.mp3', 'b.mp3', 'c.mp3'], 'b.mp3')[0]).not.toBe('b.mp3')
    }
  })

  it('plays a single track over and over', () => {
    expect(shuffled(['a.mp3'], 'a.mp3')).toEqual(['a.mp3'])
  })
})
