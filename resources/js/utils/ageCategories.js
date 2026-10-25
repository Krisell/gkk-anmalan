// Age categories follow the age a lifter turns during the competition's calendar year.
const CATEGORIES = [
  { group: 'u', label: 'Ungdom', short: 'U', min: 14, max: 18 },
  { group: 'j', label: 'Junior', short: 'J', min: 19, max: 23 },
  { group: 'm', label: 'Masters 1', short: 'M1', min: 40, max: 49 },
  { group: 'm', label: 'Masters 2', short: 'M2', min: 50, max: 59 },
  { group: 'm', label: 'Masters 3', short: 'M3', min: 60, max: 69 },
  { group: 'm', label: 'Masters 4', short: 'M4', min: 70, max: 79 },
  { group: 'm', label: 'Masters 5', short: 'M5', min: 80, max: Infinity },
]

const GROUP_LABELS = { u: 'Ungdom', j: 'Junior', m: 'Masters', s: 'Senior' }

const COMMENT_PATTERNS = {
  u: /ungdom/i,
  j: /junior/i,
  m: /master|veteran|\bm[1-5]\b/i,
  s: /senior/i,
}

export function ageCategory(birthYear, year) {
  if (!birthYear || !year) {
    return null
  }

  const age = year - birthYear
  return CATEGORIES.find((category) => age >= category.min && age <= category.max) ?? null
}

export function commentAgeGroups(comment) {
  return Object.keys(COMMENT_PATTERNS).filter((group) => COMMENT_PATTERNS[group].test(comment ?? ''))
}

// Flags comments asking for an age category the lifter can't compete in.
export function ageCategoryWarnings({ birthYear, year, comment }) {
  const category = ageCategory(birthYear, year)

  return commentAgeGroups(comment)
    .filter((group) => group !== 's')
    .flatMap((group) => {
      const label = GROUP_LABELS[group]

      if (!birthYear) {
        return [`Kommentaren nämner ${label}, men födelseår saknas`]
      }

      if (category?.group !== group) {
        return [`Kommentaren nämner ${label}, men lyftaren är född ${birthYear}`]
      }

      return []
    })
}
