import { describe, expect, it } from 'vite-plus/test'
import {
  COMMAND_MAX_AGE_MS,
  DEFAULT_CONTROL,
  isFreshCommand,
  normalizeCompetition,
  normalizeControl,
  parseNames,
} from '../../../resources/js/live/remote/controlState.js'

describe('normalizeControl', () => {
  it('falls back to the automatic defaults when nothing has been set', () => {
    expect(normalizeControl(null)).toEqual({ ...DEFAULT_CONTROL })
    expect(normalizeControl({ mode: 'party', slate: 'lunch', intros: 'yes' })).toEqual({ ...DEFAULT_CONTROL })
  })

  it('reads what the control page saved', () => {
    expect(
      normalizeControl({
        competition: ' 3001 ',
        mode: 'slate',
        listGroup: ' Grupp 03 ',
        slate: 'pause',
        pauseTime: '14.30',
        title: '  DM Klassisk styrkelyft 2026 ',
        message: { id: 'm1', text: ' Rekordförsök! ' },
        commentators: 'Anna Svensson, , Erik Berg',
        intros: false,
        roundBoards: false,
      }),
    ).toEqual({
      competition: '3001',
      mode: 'slate',
      listGroup: 'Grupp 03',
      slate: 'pause',
      pauseTime: '14:30',
      title: 'DM Klassisk styrkelyft 2026',
      message: { id: 'm1', text: 'Rekordförsök!' },
      commentators: ['Anna Svensson', 'Erik Berg'],
      intros: false,
      roundBoards: false,
    })
  })

  it('can show the club slideshow', () => {
    expect(normalizeControl({ mode: 'slideshow' }).mode).toBe('slideshow')
  })

  it('ignores an empty message and an invalid pause time', () => {
    const control = normalizeControl({ message: { id: 'm1', text: '   ' }, pauseTime: 'snart' })
    expect(control.message).toBeNull()
    expect(control.pauseTime).toBeNull()
  })
})

describe('normalizeCompetition', () => {
  it('accepts a Powerlifting Live id as a string or a number', () => {
    expect(normalizeCompetition('3001')).toBe('3001')
    expect(normalizeCompetition(3001)).toBe('3001')
  })

  it('rejects anything else', () => {
    expect(normalizeCompetition(null)).toBeNull()
    expect(normalizeCompetition('')).toBeNull()
    expect(normalizeCompetition('0')).toBeNull()
    expect(normalizeCompetition('30a1')).toBeNull()
    expect(normalizeCompetition('1234567890')).toBeNull()
    expect(normalizeCompetition(-3)).toBeNull()
  })
})

describe('parseNames', () => {
  it('accepts a comma separated string or a list, and keeps at most four names', () => {
    expect(parseNames('A, B,C')).toEqual(['A', 'B', 'C'])
    expect(parseNames(['A', ' ', 'B', 'C', 'D', 'E'])).toEqual(['A', 'B', 'C', 'D'])
    expect(parseNames(undefined)).toEqual([])
  })
})

describe('isFreshCommand', () => {
  it('runs known commands given within the last few seconds', () => {
    expect(isFreshCommand({ type: 'pb', at: 100_000 }, 101_000)).toBe(true)
  })

  it('skips old, unknown or malformed commands', () => {
    expect(isFreshCommand({ type: 'pb', at: 100_000 }, 100_000 + COMMAND_MAX_AGE_MS)).toBe(false)
    expect(isFreshCommand({ type: 'self-destruct', at: 100_000 }, 100_000)).toBe(false)
    expect(isFreshCommand({ type: 'pb' }, 100_000)).toBe(false)
    expect(isFreshCommand(null, 100_000)).toBe(false)
  })
})
