const DISCIPLINE_NAMES = { SQ: 'Knäböj', BP: 'Bänkpress', DL: 'Marklyft' }

const weightFormat = new Intl.NumberFormat('sv-SE', { maximumFractionDigits: 2 })

export function disciplineName(discipline) {
  return DISCIPLINE_NAMES[discipline] ?? null
}

export function formatWeight(weight) {
  return weight == null ? '–' : weightFormat.format(weight)
}

// Weight classes are named by their upper limit ('63' -> '-63 kg'); the open class keeps its plus ('120+').
export function formatWeightClass(weightClass) {
  if (!weightClass) {
    return null
  }

  const value = weightClass.replace(/\s*kg$/i, '')
  return value.includes('+') || value.startsWith('-') ? `${value} kg` : `-${value} kg`
}
