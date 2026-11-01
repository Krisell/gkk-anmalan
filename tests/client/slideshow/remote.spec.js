import { describe, expect, it } from 'vite-plus/test'
import {
  COMMAND_MAX_AGE_MS,
  PRESENCE_TIMEOUT_MS,
  controlOrder,
  flashText,
  isFreshCommand,
  nextInRotation,
  normalizeControl,
  onlineScreens,
  pageLabel,
  pageUrl,
  rotation,
  slideKey,
  slideLabel,
} from '../../../resources/js/components/Slideshow/remote.js'

describe('normalizeControl', () => {
  it('rotates as usual when nothing has been set', () => {
    const rotating = { paused: false, slide: null, message: null, page: null, footer: true, clock: false }
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
        footer: true,
        clock: false,
      },
    )
    expect(normalizeControl({ slide: 'records' }).slide).toBe('records')
    expect(normalizeControl({ slide: 'dm2026' }).slide).toBe('dm2026')
    expect(normalizeControl({ slide: 'swish' }).slide).toBe('swish')
    expect(normalizeControl({ slide: 'gm2027' }).slide).toBe('gm2027')
    expect(normalizeControl({ slide: 'swishx' }).slide).toBeNull()
  })

  it('hides the bottom bar only when asked, and shows the clock on its own only when asked', () => {
    expect(normalizeControl({ footer: false, clock: true })).toMatchObject({ footer: false, clock: true })
    expect(normalizeControl({ footer: 0, clock: 'yes' })).toMatchObject({ footer: true, clock: false })
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
    expect(pageUrl('www.example.com/a?b=1')).toBe('https://www.example.com/a?b=1')
    expect(pageUrl('HTTP://example.com/page')).toBe('https://example.com/page')
    expect(pageUrl('ftp://example.com')).toBeNull()
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
    expect(slideKey({ type: 'Dm2026', data: {} })).toBe('dm2026')
    expect(slideKey({ type: 'Swish', data: {} })).toBe('swish')
  })

  it('rotates the club records and the visible slides, with the DM, Swish and GM slides only held by hand', () => {
    const slides = [
      { id: 1, type: 'Slide', data: {}, is_visible: true },
      { id: 2, type: 'Slide', data: {}, is_visible: false },
    ]
    const all = rotation(slides)
    expect(all.map(slideKey)).toEqual(['records', 'slide-1', 'dm2026', 'swish', 'gm2027'])
    expect(all.filter((slide) => slide.manual).map(slideKey)).toEqual(['dm2026', 'swish', 'gm2027'])
    expect(rotation(null)).toHaveLength(4)
  })

  it('rotates the competition results after the club records, only when there are any', () => {
    const all = rotation([{ id: 1, type: 'Slide', data: {}, is_visible: true }], [], [{ id: 5 }])
    expect(all.map(slideKey)).toEqual(['records', 'results', 'slide-1', 'dm2026', 'swish', 'gm2027'])
    expect(all[1].data.results).toEqual([{ id: 5 }])
    expect(slideLabel(all[1])).toBe('Senaste tävlingsresultaten')
    expect(rotation([], [], []).map(slideKey)).not.toContain('results')
    expect(rotation([], [], null).map(slideKey)).not.toContain('results')
  })

  it('lists the DM, Swish and GM slides first in the live control', () => {
    const all = rotation([{ id: 1, type: 'Slide', data: {}, is_visible: true }])
    expect(controlOrder(all).map(slideKey)).toEqual(['dm2026', 'swish', 'gm2027', 'records', 'slide-1'])
    expect(all.filter((slide) => slide.hidesMessage).map(slideKey)).toEqual(['dm2026', 'swish', 'gm2027'])
  })

  it('steps past the manual slides', () => {
    const all = rotation([{ id: 1, type: 'Slide', data: {}, is_visible: true }])
    expect(nextInRotation(all, 0)).toBe(1)
    expect(nextInRotation(all, 1)).toBe(0) // past the manual slides, back to the club records
    expect(nextInRotation(all, 2)).toBe(0) // from the DM welcome when let go
    expect(nextInRotation(all, 3)).toBe(0) // from the Swish slide when let go
    expect(nextInRotation(all, 4)).toBe(0) // from the 2027 invitation when let go
    expect(nextInRotation(all, 0, -1)).toBe(1)
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
    expect(isFreshCommand({ type: 'flash', text: 'Hej', at: 1000 }, 1000)).toBe(true)
    expect(isFreshCommand({ type: 'clearFlash', at: 1000 }, 1000)).toBe(true)
  })

  it('reads the text of a flash', () => {
    expect(flashText({ type: 'flash', text: ' Invägning nu! ' })).toBe('Invägning nu!')
    expect(flashText({ type: 'flash', text: 'x'.repeat(500) })).toHaveLength(120)
    expect(flashText({ type: 'flash', text: '  ' })).toBeNull()
    expect(flashText({ type: 'next', text: 'Hej' })).toBeNull()
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
