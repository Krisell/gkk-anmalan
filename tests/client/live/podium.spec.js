import { describe, expect, it } from 'vite-plus/test'
import { podiumClasses, podiumOf } from '../../../resources/js/live/podium.js'

const lifter = (id, place, weightClass, category, extra = {}) => ({
  id,
  name: `Lyftare ${id}`,
  team: 'GKK',
  place,
  weightClass,
  category,
  sex: 'm',
  total: 500 - place * 10,
  points: 80 - place,
  ...extra,
})

const scoreboard = {
  lifters: [
    lifter('a', 2, '74', 'Senior'),
    lifter('b', 4, '74', 'Senior'),
    lifter('c', 1, '74', 'Senior'),
    lifter('d', 3, '74', 'Senior'),
    lifter('e', 1, '83', 'Senior'),
    lifter('f', 1, '66', 'Ungdom koefficient'),
    lifter('g', 2, '93', 'Ungdom koefficient'),
  ],
}

describe('podiumClasses', () => {
  it("lists a group's classes once each, in the results list's order", () => {
    expect(podiumClasses(scoreboard)).toEqual([
      { key: '74|Senior', label: '-74 kg · Senior' },
      { key: '83|Senior', label: '-83 kg · Senior' },
      { key: 'm|Ungdom koefficient', label: 'Ungdom koefficient' },
    ])
    expect(podiumClasses(null)).toEqual([])
  })
})

describe('podiumOf', () => {
  it('is the top three of the class, by place', () => {
    const podium = podiumOf(scoreboard, '74|Senior')
    expect(podium.label).toBe('-74 kg · Senior')
    expect(podium.coefficient).toBe(false)
    expect(podium.lifters.map((entry) => entry.id)).toEqual(['c', 'a', 'd'])
  })

  it('has fewer when fewer are placed, and is placed by points in a coefficient class', () => {
    expect(podiumOf(scoreboard, '83|Senior').lifters).toHaveLength(1)
    const coefficient = podiumOf(scoreboard, 'm|Ungdom koefficient')
    expect(coefficient.coefficient).toBe(true)
    expect(coefficient.lifters.map((entry) => entry.id)).toEqual(['f', 'g'])
  })

  it('is null for a class the group does not have', () => {
    expect(podiumOf(scoreboard, '120|Senior')).toBeNull()
  })
})
