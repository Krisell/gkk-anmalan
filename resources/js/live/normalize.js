/**
 * Converts the Powerlifting Live feed (as returned by /live/feed/{competition}) into the
 * internal model used by the overlay, so that components never depend on the external schema.
 *
 * Snapshot: {
 *   discipline: 'SQ' | 'BP' | 'DL' | null,   competition's current discipline
 *   group: string | null,
 *   current: Attempt | null,                 lifter on the platform (next in line)
 *   next: Attempt | null,                    lifter after that
 *   order: [{ lifterId, name, team, discipline, attempt, weight }]   the lifting order, starting with current
 *   lastResult: Attempt | null,              most recently judged attempt, outcome set
 *   decision: Decision | null,               latest referee decision
 *   scoreboard: Scoreboard,                  the current group's results (for ?view=scoreboard)
 *   clock: number | null,                    attempt clock: seconds left when last published, null if not running
 * }
 *
 * Attempt: {
 *   id: string,              stable identity of the attempt, e.g. '110845:BP2'
 *   lifterId, name, team, weightClass, category,
 *   discipline, attempt (1-3), weight (kg),
 *   outcome: 'good' | 'failed' | null,
 *   attempts: [{ weight, outcome }] x 3      the lifter's attempts in this discipline
 *   lifts: { SQ, BP, DL: [{ weight, outcome }] x 3 }   all of the lifter's attempts
 *   total: number | null,                    the lifter's current total
 *   place: number | null
 * }
 *
 * Decision: { id: string, lights: [{ good: boolean, cards: ['red'|'yellow'|'blue'] }] x 3 (left, center, right) }
 *
 * Scoreboard: {
 *   disciplines: ['SQ', 'BP', 'DL'],         the disciplines of the competition (a bench-only meet: ['BP'])
 *   lifters: [{ id, name, team, weightClass, category, place, total, points, lifts: { SQ, BP, DL: [{ weight, outcome }] x 3 } }]
 *                                            points: IPF GL points (Powerlifting Live calls the field 'wilks')
 * }
 */

export const DISCIPLINES = ['SQ', 'BP', 'DL']

const CARDS = { r: 'red', y: 'yellow', b: 'blue' }

export function normalizeFeed(raw) {
  const nextLifters = raw?.next_lifters ?? {}
  const scoresheet = raw?.scoresheet ?? {}
  const discipline = toDiscipline(scoresheet.info?.current_discipline)
  const round = toInt(nextLifters.info?.current_round)

  // Entries marked next_group belong to the following group (the proxy removes them too).
  const entries = (Array.isArray(nextLifters.lifters) ? nextLifters.lifters : []).filter(
    (entry) => entry && typeof entry === 'object' && entry.next_group == null,
  )
  const [currentEntry, nextEntry] = entries
  const [lastEntry] = Array.isArray(scoresheet.last_lifts) ? scoresheet.last_lifts : []

  return {
    discipline,
    group: toText(nextLifters.info?.current_group ?? scoresheet.info?.current_group),
    current: currentEntry ? normalizeCurrent(currentEntry, discipline, round) : null,
    next: nextEntry ? normalizeCurrent(nextEntry, discipline, null) : null,
    order: entries.map((entry, index) => {
      const {
        lifterId,
        name,
        team,
        discipline: d,
        attempt,
        weight,
      } = normalizeCurrent(entry, discipline, index === 0 ? round : null)
      return { lifterId, name, team, discipline: d, attempt, weight }
    }),
    lastResult: lastEntry ? normalizeLastLift(lastEntry) : null,
    decision: normalizeDecision(raw?.decision),
    signals: normalizeSignals(raw?.referee_signals),
    scoreboard: normalizeScoreboard(scoresheet.lifters, discipline),
    clock: normalizeClock(raw?.clock),
  }
}

// Powerlifting Live publishes the seconds left when the clock is started (or changed); 0 or less means stopped.
// Its screens count down locally from there, and so does the overlay.
function normalizeClock(raw) {
  const seconds = toInt(raw?.clock)
  return seconds > 0 ? seconds : null
}

/**
 * The whole competition's results (/live/results/{id}, every group), per group in name order ("Grupp 01", …), for
 * showing another group than the one on the platform. Within a group the lifters are listed by weight class and
 * category (in Powerlifting Live's order), then by place.
 *
 * status: 'upcoming' (nothing judged yet), 'done' (every lifter has finished the deadlift) or 'ongoing'.
 */
export function normalizeResults(raw) {
  const lifters = (Array.isArray(raw?.lifters) ? raw.lifters : []).filter(
    (lifter) => lifter && typeof lifter === 'object',
  )
  const groups = new Map()
  for (const lifter of lifters) {
    const name = toText(lifter.group) ?? 'Okänd grupp'
    groups.set(name, [...(groups.get(name) ?? []), lifter])
  }

  return [...groups.entries()]
    .sort(([a], [b]) => a.localeCompare(b, 'sv', { numeric: true }))
    .map(([name, members]) => {
      const scoreboard = normalizeScoreboard(sortForResults(members), null)
      return { name, status: groupStatus(scoreboard.lifters), scoreboard }
    })
}

/**
 * The start order of the group that lifts next (/live/next-group/{id}), for the warm-up hall: Powerlifting Live's own
 * "Nästa grupp" list, in its order. With groups lifting in turns the next block can be the same group in the next
 * discipline, so the discipline (and attempt) is part of it.
 *
 * NextGroup: { group, discipline, attempt, lifters: [{ id, name, team, weightClass, category, weight }] } or null
 * when there is no next group (during the last block).
 */
export function normalizeNextGroup(raw) {
  const entries = (Array.isArray(raw?.lifters) ? raw.lifters : []).filter(
    (entry) => entry && typeof entry === 'object' && entry.lifter && typeof entry.lifter === 'object',
  )
  if (!entries.length) {
    return null
  }

  const [first] = entries
  const lift = parseLift(first.lift)
  return {
    group: toText(first.next_group) ?? toText(first.lifter.group),
    discipline: toDiscipline(first.discipline) ?? lift?.discipline ?? null,
    attempt: validAttempt(toInt(first.attempt) ?? lift?.attempt),
    lifters: entries.map((entry) => ({
      id: toText(entry.lifter.id ?? entry.lifter_id),
      name: toText(entry.lifter.name),
      team: toText(entry.lifter.team),
      weightClass: toText(entry.lifter.weightclass_short_alt ?? entry.lifter.weightclass),
      category: toText(entry.lifter.category),
      weight: toWeight(entry.weight),
    })),
  }
}

function sortForResults(lifters) {
  const order = (key) => {
    const seen = [...new Set(lifters.map(key))]
    return (lifter) => seen.indexOf(key(lifter))
  }
  const classIndex = order((lifter) => toText(lifter.weightclass_short_alt ?? lifter.weightclass))
  const categoryIndex = order((lifter) => toText(lifter.category))

  return [...lifters].sort(
    (a, b) =>
      classIndex(a) - classIndex(b) ||
      categoryIndex(a) - categoryIndex(b) ||
      (toPlace(a.place) ?? Infinity) - (toPlace(b.place) ?? Infinity),
  )
}

function groupStatus(lifters) {
  const judged = (lifter) => DISCIPLINES.some((d) => lifter.lifts[d].some((attempt) => attempt.outcome))
  // Finished: a deadlift has been judged and no declared deadlift attempt is left.
  const finished = (lifter) =>
    lifter.lifts.DL.some((attempt) => attempt.outcome) &&
    lifter.lifts.DL.every((attempt) => attempt.outcome || attempt.weight == null)

  if (!lifters.some(judged)) {
    return 'upcoming'
  }
  return lifters.every(finished) ? 'done' : 'ongoing'
}

function normalizeScoreboard(rawLifters, discipline) {
  const lifters = (Array.isArray(rawLifters) ? rawLifters : []).filter((lifter) => lifter && typeof lifter === 'object')

  return {
    // A discipline belongs to the competition if it is the current one or anyone has declared a weight in it.
    disciplines: DISCIPLINES.filter(
      (d) => d === discipline || lifters.some((lifter) => [1, 2, 3].some((n) => toWeight(lifter.lifts?.[`${d}${n}`]))),
    ),
    lifters: lifters.map((lifter) => ({
      id: toText(lifter.id),
      name: toText(lifter.name),
      team: toText(lifter.team),
      weightClass: toText(lifter.weightclass_short_alt ?? lifter.weightclass),
      category: toText(lifter.category),
      place: toPlace(lifter.place),
      total: toWeight(lifter.total),
      points: toWeight(lifter.wilks),
      lifts: Object.fromEntries(DISCIPLINES.map((d) => [d, disciplineAttempts(lifter.lifts ?? {}, d)])),
    })),
  }
}

function normalizeCurrent(entry, competitionDiscipline, round) {
  const lifter = entry.lifter ?? {}
  const lifts = lifter.lifts ?? {}
  const fromLift = parseLift(entry.lift)

  const discipline = toDiscipline(entry.discipline) ?? fromLift?.discipline ?? competitionDiscipline
  const attempt =
    toInt(entry.attempt) ?? fromLift?.attempt ?? firstUnjudgedAttempt(lifts, discipline) ?? validAttempt(round)
  const weight = toWeight(entry.weight) ?? (discipline && attempt ? toWeight(lifts[`${discipline}${attempt}`]) : null)

  return buildAttempt(lifter, entry.lifter_id, discipline, attempt, weight, null)
}

function normalizeLastLift(entry) {
  const lift = parseLift(entry.lift)
  const discipline = toDiscipline(entry.discipline) ?? lift?.discipline ?? null
  const attempt = toInt(entry.attempt) ?? lift?.attempt ?? null
  const outcome = toOutcome(entry.status)

  if (!outcome) {
    return null
  }

  return buildAttempt(entry.lifter ?? {}, entry.lifter_id, discipline, attempt, toWeight(entry.weight), outcome)
}

function buildAttempt(lifter, fallbackLifterId, discipline, attempt, weight, outcome) {
  const lifterId = toText(lifter.id ?? fallbackLifterId)
  const lifts = lifter.lifts ?? {}

  return {
    id: `${lifterId ?? lifter.name}:${discipline ?? '?'}${attempt ?? '?'}`,
    lifterId,
    name: toText(lifter.name),
    team: toText(lifter.team),
    weightClass: toText(lifter.weightclass_short_alt ?? lifter.weightclass),
    category: toText(lifter.category),
    discipline,
    attempt,
    weight,
    outcome,
    attempts: discipline ? disciplineAttempts(lifts, discipline) : disciplineAttempts({}, null),
    lifts: Object.fromEntries(DISCIPLINES.map((d) => [d, disciplineAttempts(lifts, d)])),
    total: toWeight(lifter.total),
    place: toPlace(lifter.place),
  }
}

function disciplineAttempts(lifts, discipline) {
  return [1, 2, 3].map((n) => ({
    weight: toWeight(lifts[`${discipline}${n}`]),
    outcome: toOutcome(lifts[`${discipline}${n}_status`]),
  }))
}

// Places are 1-based; Powerlifting Live uses 0 for "no place yet".
function toPlace(value) {
  const place = toInt(value)
  return place > 0 ? place : null
}

// The lights of the referees who have decided so far, before the decision: [left, center, right], each a light
// ({ good, cards }) or null for a referee who hasn't decided yet. Null when none has.
function normalizeSignals(raw) {
  if (!raw || typeof raw !== 'object') {
    return null
  }
  const lights = [raw.left, raw.center, raw.right].map((value) => (typeof value === 'string' ? toLight(value) : null))
  return lights.some(Boolean) ? lights : null
}

function toLight(value) {
  return {
    good: value.includes('w'),
    cards: value.includes('w') ? [] : [...value].map((code) => CARDS[code]).filter(Boolean),
  }
}

function normalizeDecision(raw) {
  if (!raw || raw.updated == null) {
    return null
  }

  const positions = [raw.left, raw.center, raw.right]
  if (positions.some((value) => typeof value !== 'string')) {
    return null
  }

  return {
    id: `${raw.updated}:${positions.join(',')}`,
    lights: positions.map(toLight),
  }
}

/**
 * The first attempt in the discipline that has a weight but no result is the one being lifted.
 */
function firstUnjudgedAttempt(lifts, discipline) {
  if (!discipline) {
    return null
  }

  for (const n of [1, 2, 3]) {
    if (!toOutcome(lifts[`${discipline}${n}_status`])) {
      return toWeight(lifts[`${discipline}${n}`]) ? n : null
    }
  }

  return null
}

function parseLift(lift) {
  const match = typeof lift === 'string' ? lift.match(/^(SQ|BP|DL)([123])$/i) : null
  return match ? { discipline: match[1].toUpperCase(), attempt: Number(match[2]) } : null
}

function toDiscipline(value) {
  const upper = typeof value === 'string' ? value.toUpperCase() : null
  return DISCIPLINES.includes(upper) ? upper : null
}

function toOutcome(status) {
  return status === 'good' || status === 'failed' ? status : null
}

function toWeight(value) {
  const number = Number.parseFloat(value)
  return Number.isFinite(number) && number > 0 ? number : null
}

function toInt(value) {
  const number = Number.parseInt(value, 10)
  return Number.isFinite(number) ? number : null
}

function validAttempt(value) {
  return value >= 1 && value <= 3 ? value : null
}

function toText(value) {
  if (value == null) {
    return null
  }

  const text = String(value).trim()
  return text === '' ? null : text
}
