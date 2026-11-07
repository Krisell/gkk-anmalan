const DISCIPLINE_NAMES = { SQ: 'Knäböj', BP: 'Bänkpress', DL: 'Marklyft' }

const weightFormat = new Intl.NumberFormat('sv-SE', { maximumFractionDigits: 2 })
const pointsFormat = new Intl.NumberFormat('sv-SE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })

export function disciplineName(discipline) {
  return DISCIPLINE_NAMES[discipline] ?? null
}

export function formatWeight(weight) {
  return weight == null ? '–' : weightFormat.format(weight)
}

// Bodyweights are weighed to the hundredth and written so ('88,90 kg').
export function formatBodyWeight(weight) {
  return weight == null ? '–' : `${pointsFormat.format(weight)} kg`
}

export function formatPoints(points) {
  return points == null ? '–' : pointsFormat.format(points)
}

// Weight classes are named by their upper limit ('63' -> '-63 kg'); the open class keeps its plus ('120+').
export function formatWeightClass(weightClass) {
  if (!weightClass) {
    return null
  }

  const value = weightClass.replace(/\s*kg$/i, '')
  return value.includes('+') || value.startsWith('-') ? `${value} kg` : `-${value} kg`
}

// A coefficient class ('Junior koefficient', for lifters too few in their weight class) ranks lifters of all weight
// classes together. Powerlifting Live has no flag for it, only the category's name.
export function isCoefficientClass(category) {
  return /koefficient|coefficient/i.test(category ?? '')
}

// The class a lifter competes (and is placed) in: the weight class and category, or for a coefficient class only the
// category (and sex, should men and women lift in the same group). label: e.g. '-63 kg · Senior', 'Junior koefficient'.
export function competitionClass({ weightClass, category, sex }) {
  if (isCoefficientClass(category)) {
    return { key: `${sex ?? ''}|${category}`, label: category }
  }
  return {
    key: `${weightClass}|${category}`,
    label: [formatWeightClass(weightClass), category].filter(Boolean).join(' · ') || null,
  }
}
