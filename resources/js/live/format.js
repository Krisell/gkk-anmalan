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
