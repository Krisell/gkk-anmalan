import { describe, expect, it } from 'vite-plus/test'
import { ageCategory, ageCategoryWarnings, commentAgeGroups } from '../../resources/js/utils/ageCategories'

describe('ageCategory', () => {
  it('uses the age reached during the competition year', () => {
    expect(ageCategory(2008, 2026).short).toBe('U')
    expect(ageCategory(2007, 2026).short).toBe('J')
    expect(ageCategory(2003, 2026).short).toBe('J')
    expect(ageCategory(2002, 2026)).toBeNull()
  })

  it('splits masters into M1–M5', () => {
    expect(ageCategory(1986, 2026).short).toBe('M1')
    expect(ageCategory(1960, 2026).short).toBe('M3')
    expect(ageCategory(1940, 2026).short).toBe('M5')
  })

  it('returns null without a birth year', () => {
    expect(ageCategory(null, 2026)).toBeNull()
  })
})

describe('commentAgeGroups', () => {
  it('finds the categories mentioned in free text', () => {
    expect(commentAgeGroups('Jag vill tävla i ungdoms klassen')).toEqual(['u'])
    expect(commentAgeGroups('Junior men kör eventuellt senior')).toEqual(['j', 's'])
    expect(commentAgeGroups('Kör M2')).toEqual(['m'])
    expect(commentAgeGroups(null)).toEqual([])
  })
})

describe('ageCategoryWarnings', () => {
  it('accepts a comment that matches the lifter age', () => {
    expect(ageCategoryWarnings({ birthYear: 2006, year: 2026, comment: 'Junior!' })).toEqual([])
  })

  it('ignores senior, which everyone can compete in', () => {
    expect(ageCategoryWarnings({ birthYear: 2006, year: 2026, comment: 'Senior' })).toEqual([])
  })

  it('flags a category the lifter is too old or young for', () => {
    expect(ageCategoryWarnings({ birthYear: 1990, year: 2026, comment: 'Junior' })).toEqual([
      'Kommentaren nämner Junior, men lyftaren är född 1990',
    ])
  })

  it('flags a missing birth year', () => {
    expect(ageCategoryWarnings({ birthYear: null, year: 2026, comment: 'Ungdom' })).toEqual([
      'Kommentaren nämner Ungdom, men födelseår saknas',
    ])
  })
})
