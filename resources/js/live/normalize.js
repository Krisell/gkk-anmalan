/**
 * Converts the Powerlifting Live feed (as returned by /live/feed/{competition}) into the
 * internal model used by the overlay, so that components never depend on the external schema.
 *
 * Snapshot: {
 *   discipline: 'SQ' | 'BP' | 'DL' | null,   competition's current discipline
 *   group: string | null,
 *   current: Attempt | null,                 lifter on the platform (next in line)
 *   next: Attempt | null,                    lifter after that
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
 *   place: number | null, predictedPlace: number | null
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
  const [currentEntry, nextEntry] = (Array.isArray(nextLifters.lifters) ? nextLifters.lifters : []).filter(
    (entry) => entry && entry.next_group == null,
  )
  const [lastEntry] = Array.isArray(scoresheet.last_lifts) ? scoresheet.last_lifts : []

  return {
    discipline,
    group: toText(nextLifters.info?.current_group ?? scoresheet.info?.current_group),
    current: currentEntry ? normalizeCurrent(currentEntry, discipline, round) : null,
    next: nextEntry ? normalizeCurrent(nextEntry, discipline, null) : null,
    lastResult: lastEntry ? normalizeLastLift(lastEntry) : null,
    decision: normalizeDecision(raw?.decision),
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

  const current = buildAttempt(lifter, entry.lifter_id, discipline, attempt, weight, null)
  const predictedPlace = toPlace(entry.predict_place ?? lifter.prog_place)

  return { ...current, predictedPlace }
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
    predictedPlace: toPlace(lifter.prog_place),
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
    lights: positions.map((value) => ({
      good: value.includes('w'),
      cards: value.includes('w') ? [] : [...value].map((code) => CARDS[code]).filter(Boolean),
    })),
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
