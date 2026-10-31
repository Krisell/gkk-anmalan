import { describe, expect, it } from 'vite-plus/test'
import {
  COMMAND_MAX_AGE_MS,
  PRESENCE_TIMEOUT_MS,
  isFreshCommand,
  normalizeControl,
  onlineScreens,
  slideKey,
  slideLabel,
} from '../../../resources/js/components/Slideshow/remote.js'

describe('normalizeControl', () => {
  it('rotates as usual when nothing has been set', () => {
    const rotating = { paused: false, slide: null, message: null }
    expect(normalizeControl(null)).toEqual(rotating)
    expect(normalizeControl({ paused: 'yes', slide: 'slide-x', message: { text: '   ' } })).toEqual(rotating)
  })

  it('reads what the live control saved', () => {
    expect(normalizeControl({ paused: true, slide: 'slide-12', message: { id: 'm1', text: ' Stänger 21! ' } })).toEqual(
      {
        paused: true,
        slide: 'slide-12',
        message: { id: 'm1', text: 'Stänger 21!' },
      },
    )
    expect(normalizeControl({ slide: 'records' }).slide).toBe('records')
  })

  it('cuts long messages', () => {
    expect(normalizeControl({ message: { text: 'x'.repeat(500) } }).message.text).toHaveLength(120)
  })
})

describe('slides', () => {
  it('has a stable key for each slide, including the club records', () => {
    expect(slideKey({ type: 'Klubbrekord', data: {} })).toBe('records')
    expect(slideKey({ id: 7, type: 'Slide', data: {} })).toBe('slide-7')
  })

  it('names a slide by its text, or what it shows', () => {
    expect(slideLabel({ type: 'Klubbrekord' })).toBe('Senaste klubbrekorden')
    expect(slideLabel({ type: 'Slide', data: { text: ' DM i helgen ' } })).toBe('DM i helgen')
    expect(slideLabel({ type: 'Slide', data: { subText: 'Anmäl dig' } })).toBe('Anmäl dig')
    expect(slideLabel({ type: 'Slide', data: { image: 'https://example.com/a.jpg' } })).toBe('Bild')
    expect(slideLabel({ type: 'Slide', data: {} })).toBe('Tom slide')
  })
})

describe('isFreshCommand', () => {
  it('runs known commands given just now', () => {
    expect(isFreshCommand({ type: 'next', at: 1000 }, 1500)).toBe(true)
    expect(isFreshCommand({ type: 'reload', at: 1000 }, 1000 + COMMAND_MAX_AGE_MS + 1)).toBe(false)
    expect(isFreshCommand({ type: 'explode', at: 1000 }, 1000)).toBe(false)
    expect(isFreshCommand({ type: 'next' }, 1000)).toBe(false)
  })
})

describe('onlineScreens', () => {
  it('lists the screens that reported recently, newest first', () => {
    const now = 1_000_000
    const presence = {
      a: { at: now - 5000, slide: 'records' },
      b: { at: now - PRESENCE_TIMEOUT_MS - 1, slide: 'slide-1' },
      c: { at: now - 1000, slide: 'slide-2' },
      d: 'junk',
    }

    expect(onlineScreens(presence, now).map((screen) => screen.slide)).toEqual(['slide-2', 'records'])
    expect(onlineScreens(null, now)).toEqual([])
  })
})
