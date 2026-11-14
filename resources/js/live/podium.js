import { competitionClass, isCoefficientClass } from './format.js'

/**
 * The prize ceremony's podiums: per competition class (see competitionClass in format.js) of a group's results
 * (normalizeResults in normalize.js).
 */

// The classes of a group, in the results list's order: [{ key, label, places }], places being how many podium places are
// taken (3 with a bronze).
export function podiumClasses(scoreboard) {
  const classes = new Map()
  for (const lifter of scoreboard?.lifters ?? []) {
    const { key, label } = competitionClass(lifter)
    if (!classes.has(key)) {
      classes.set(key, { key, label: label ?? 'Okänd klass', places: 0 })
    }
    // The podium's lowest place (3 bronze, …), 0 when nobody is placed yet.
    const entry = classes.get(key)
    if (lifter.place >= 1 && lifter.place <= 3) {
      entry.places = Math.max(entry.places, lifter.place)
    }
  }
  return [...classes.values()]
}

// The podium of one class: its top three (or fewer, as far as they are placed), by place. Null when the group has no
// such class.
export function podiumOf(scoreboard, classKey) {
  const members = (scoreboard?.lifters ?? []).filter((lifter) => competitionClass(lifter).key === classKey)
  if (!members.length) {
    return null
  }
  return {
    label: competitionClass(members[0]).label ?? '',
    coefficient: isCoefficientClass(members[0].category),
    lifters: members
      .filter((lifter) => lifter.place >= 1 && lifter.place <= 3)
      .sort((a, b) => a.place - b.place)
      .slice(0, 3),
  }
}
