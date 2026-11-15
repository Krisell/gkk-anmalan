import { ipfGlPoints } from './ipfPoints.js'

/**
 * The results list's prognosis ("Prog. total", "Prog. poäng"): the total if every declared attempt not yet judged is
 * good, per discipline the heaviest of the good and the declared lifts, and its IPF GL points (the bench formula in a
 * bench-only competition, like the list's points).
 *
 * Null for a lifter who is out (three failed attempts in a discipline), or who has nothing good or declared in one of
 * the competition's disciplines.
 */
export function prognosisTotal(lifter, disciplines) {
  if (!disciplines?.length) {
    return null
  }
  let total = 0
  for (const discipline of disciplines) {
    const possible = (lifter?.lifts?.[discipline] ?? [])
      .filter((slot) => slot.weight > 0 && slot.outcome !== 'failed')
      .map((slot) => slot.weight)
    if (!possible.length) {
      return null
    }
    total += Math.max(...possible)
  }
  return total
}

export function prognosisPoints(lifter, disciplines) {
  const total = prognosisTotal(lifter, disciplines)
  const benchOnly = disciplines?.length === 1 && disciplines[0] === 'BP'
  return ipfGlPoints({
    total,
    bodyWeight: lifter?.bodyWeight,
    sex: lifter?.sex,
    classic: lifter?.classic !== false,
    event: benchOnly ? 'bench' : 'powerlifting',
  })
}
