import { describe, expect, it } from 'vite-plus/test'
import {
  currentSession,
  groupNumber,
  groupNumbers,
  speakerSessions,
} from '../../../resources/js/live/speaker/sessions.js'

const schedule = [
  { start: '09:30', groups: [1, 2] },
  { start: '13:00', groups: [3, 4] },
  { start: '17:00', groups: [5, 6] },
]

describe('speakerSessions', () => {
  it('puts the groups under their start time, in group order', () => {
    const tabs = speakerSessions(['Grupp 2', 'Grupp 4', 'Grupp 1', 'Grupp 3'], schedule)
    expect(tabs).toEqual([
      { key: 'start-09:30', label: 'Start 09:30', start: '09:30', groups: ['Grupp 1', 'Grupp 2'] },
      { key: 'start-13:00', label: 'Start 13:00', start: '13:00', groups: ['Grupp 3', 'Grupp 4'] },
    ])
  })

  it('gives groups outside the schedule a tab each, last', () => {
    const tabs = speakerSessions(['Grupp 10', 'Grupp 01', 'Övriga'], schedule)
    expect(tabs.map((tab) => tab.label)).toEqual(['Start 09:30', 'Grupp 10', 'Övriga'])
  })
})

describe('currentSession', () => {
  const tabs = speakerSessions(['Grupp 1', 'Grupp 3', 'Grupp 5'], schedule)

  it('opens the session that started last', () => {
    expect(currentSession(tabs, '14:10')).toBe('start-13:00')
    expect(currentSession(tabs, '21:00')).toBe('start-17:00')
  })

  it('opens the first before the day has started', () => {
    expect(currentSession(tabs, '08:00')).toBe('start-09:30')
    expect(currentSession([], '08:00')).toBeNull()
  })
})

describe('group numbers', () => {
  it('reads ranges and lists', () => {
    expect(groupNumbers('1–2')).toEqual([1, 2])
    expect(groupNumbers('3-5')).toEqual([3, 4, 5])
    expect(groupNumbers('1, 4')).toEqual([1, 4])
  })

  it("reads a group's number", () => {
    expect(groupNumber('Grupp 01')).toBe(1)
    expect(groupNumber('Grupp 12')).toBe(12)
    expect(groupNumber('Övriga')).toBeNull()
  })
})
