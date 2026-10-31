import { describe, expect, it } from 'vite-plus/test'
import {
  COMMAND_MAX_AGE_MS,
  PRESENCE_TIMEOUT_MS,
  isFreshCommand,
  normalizeControl,
  onlineScreens,
  pageLabel,
  pageUrl,
  slideKey,
  slideLabel,
} from '../../../resources/js/components/Slideshow/remote.js'

describe('normalizeControl', () => {
  it('rotates as usual when nothing has been set', () => {
    const rotating = { paused: false, slide: null, message: null, page: null }
    expect(normalizeControl(null)).toEqual(rotating)
    expect(
      normalizeControl({
        paused: 'yes',
        slide: 'slide-x',
        message: { text: '   ' },
        page: { url: 'javascript:alert(1)' },
      }),
    ).toEqual(rotating)
  })

  it('reads what the live control saved', () => {
    expect(normalizeControl({ paused: true, slide: 'slide-12', message: { id: 'm1', text: ' Stänger 21! ' } })).toEqual(
      {
        paused: true,
        slide: 'slide-12',
        message: { id: 'm1', text: 'Stänger 21!' },
        page: null,
      },
    )
    expect(normalizeControl({ slide: 'records' }).slide).toBe('records')
  })

  it('cuts long messages', () => {
    expect(normalizeControl({ message: { text: 'x'.repeat(500) } }).message.text).toHaveLength(120)
  })
})

describe('web pages', () => {
  it('shows only https pages', () => {
    expect(pageUrl(' https://se.powerliftinglive.com/screen?id=3001 ')).toBe(
      'https://se.powerliftinglive.com/screen?id=3001',
    )
    expect(pageUrl('se.powerliftinglive.com')).toBe('https://se.powerliftinglive.com/')
    expect(pageUrl('http://example.com')).toBeNull()
    expect(pageUrl('javascript:alert(1)')).toBeNull()
    expect(pageUrl('data:text/html,<p>hi</p>')).toBeNull()
    expect(pageUrl('https://')).toBeNull()
    expect(pageUrl(`https://example.com/${'x'.repeat(2000)}`)).toBeNull()
    expect(pageUrl(42)).toBeNull()
  })

  it('reads the page the live control saved', () => {
    expect(normalizeControl({ page: { id: 'p1', url: 'https://example.com/a' } }).page).toEqual({
      id: 'p1',
      url: 'https://example.com/a',
    })
  })

  it('names a page by its address', () => {
    expect(pageLabel('https://example.com/')).toBe('Webbsida: example.com')
    expect(pageLabel('https://example.com/live/3001?x=1')).toBe('Webbsida: example.com/live/3001')
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
