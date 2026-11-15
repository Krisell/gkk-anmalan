import { competitionClass, isCoefficientClass } from './format.js'
import { ipfGlPoints } from './ipfPoints.js'
import { DISCIPLINES } from './normalize.js'

/**
 * The place of the lifter on the platform now and if the attempt is good, for the big lifter card ("Placering 4 → 2",
 * the live control's "Placering på lyftarkortet"), for competitions with places and medals.
 *
 * Within the lifter's competition class among the group's results (snapshot.scoreboard), the prize ceremony's podiums
 * too. Ranked like Powerlifting Live during the competition: by the subtotal so far (the best good lift of each
 * discipline), a coefficient class by its IPF GL points; on the same, the lighter lifter first, and on the same body
 * weight too, the one who had it first (so the lifter on the platform comes after). A lifter without a good lift yet
 * has no place, nor one who has failed all three attempts in a discipline.
 *
 * Returns { now, after } (now null when the lifter has no place yet), or null when it can't be told: the lifter isn't
 * in the results, is out, or the attempt has no weight.
 */
export function placing(attempt, scoreboard) {
  const lifters = scoreboard?.lifters ?? []
  const lifter = lifters.find((entry) => entry.id === attempt?.lifterId)
  if (!lifter || !attempt.discipline || !(attempt.attempt >= 1) || !(attempt.weight > 0)) {
    return null
  }

  const classKey = competitionClass(lifter).key
  const coefficient = isCoefficientClass(lifter.category)
  const benchOnly = scoreboard.disciplines?.length === 1 && scoreboard.disciplines[0] === 'BP'
  const score = (entry, lifts) => {
    const total = subtotal(lifts)
    if (!total || !coefficient) {
      return total
    }
    return ipfGlPoints({
      total,
      bodyWeight: entry.bodyWeight,
      sex: entry.sex,
      classic: entry.classic !== false,
      event: benchOnly ? 'bench' : 'powerlifting',
    })
  }

  const others = lifters
    .filter((entry) => entry !== lifter && competitionClass(entry).key === classKey)
    .map((entry) => ({ score: score(entry, entry.lifts), bodyWeight: entry.bodyWeight }))
    .filter((entry) => entry.score)

  const placeWith = (mine) =>
    mine
      ? 1 +
        others.filter(
          (other) => other.score > mine || (other.score === mine && !(lifter.bodyWeight < other.bodyWeight)),
        ).length
      : null

  const ifGood = {
    ...lifter.lifts,
    [attempt.discipline]: lifter.lifts[attempt.discipline].map((slot, index) =>
      index + 1 === attempt.attempt ? { weight: attempt.weight, outcome: 'good' } : slot,
    ),
  }
  const after = placeWith(score(lifter, ifGood))
  return after ? { now: placeWith(score(lifter, lifter.lifts)), after } : null
}

// The best good lift of each discipline added up; null for a lifter out of the competition (three failed attempts
// in a discipline) and 0 for one without a good lift yet.
function subtotal(lifts) {
  let total = 0
  for (const discipline of DISCIPLINES) {
    const slots = lifts?.[discipline] ?? []
    const good = slots.filter((slot) => slot.outcome === 'good' && slot.weight > 0)
    if (good.length) {
      total += Math.max(...good.map((slot) => slot.weight))
    } else if (slots.length === 3 && slots.every((slot) => slot.outcome === 'failed')) {
      return null
    }
  }
  return total
}
