import { describe, expect, it } from 'vite-plus/test'
import { SESSIONS, sessionStates, summary } from '../../../resources/js/components/Slideshow/dm2026.js'

describe('DM 2026', () => {
  it('counts the lifters, clubs and groups in the start lists', () => {
    expect(SESSIONS.map((session) => session.lifters.length)).toEqual([17, 21, 18])
    expect(summary()).toEqual({ lifters: 56, clubs: 13, homeLifters: 13, groups: 6 })
  })

  it('follows the sessions on the competition day only', () => {
    const at = (hours, minutes = 0) => sessionStates(new Date(2026, 10, 14, hours, minutes))
    expect(sessionStates(new Date(2026, 10, 13, 10))).toEqual([null, null, null])
    expect(at(7)).toEqual([null, null, null])
    expect(at(7, 30)).toEqual(['weighIn', null, null])
    expect(at(9, 30)).toEqual(['live', null, null])
    expect(at(11, 15)).toEqual(['live', 'weighIn', null])
    expect(at(13)).toEqual(['done', 'live', null])
    expect(at(17, 30)).toEqual(['done', 'done', 'live'])
    expect(at(21)).toEqual(['done', 'done', 'done'])
  })
})
