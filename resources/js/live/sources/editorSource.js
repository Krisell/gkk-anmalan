/**
 * Demo editor (/live/overlay?demo=true): the lifter, attempt and result are set by hand in a form (DemoEditor.vue),
 * and this source turns that state into the same raw format as the Laravel proxy. Everything after it
 * (normalizing, result detection, animations) therefore runs exactly as with a real competition.
 */

const ORDER = ['SQ', 'BP', 'DL']

// Rough relation between the three lifts, used to make up plausible results in the other disciplines.
const RATIO = { SQ: 1, BP: 0.65, DL: 1.2 }
const INCREMENTS = [2.5, 5, 7.5, 10]

// The rest of the group in the results list (fictional): two weight classes (the edited lifter's and the next one
// up), each with a Senior and a Junior category, like a real group mixing classes and age categories.
const FIELD = [
  ['Erik Lund', 'Göteborg KK', 'same', 'Senior'],
  ['Johan Berg', 'Malmö AK', 'same', 'Senior'],
  ['Oskar Holm', 'Stockholms KK', 'same', 'Junior'],
  ['Viktor Ek', 'Västerås SK', 'same', 'Junior'],
  ['Theo Lind', 'Lunds TK', 'same', 'Junior'],
  ['Ali Hassan', 'Örebro KK', 'next', 'Senior'],
  ['Martin Sjö', 'Gävle KK', 'next', 'Senior'],
  ['Daniel Åberg', 'Täby AK', 'next', 'Senior'],
  ['Filip Nyström', 'IKSU Kraftsport', 'next', 'Junior'],
  ['Elias Kron', 'Sundbybergs TK', 'next', 'Junior'],
  ['Noah Strand', 'Visby SK', 'next', 'Junior'],
]

// Men's and women's weight classes, to find the class above the one entered in the editor.
const WEIGHT_CLASSES = [
  ['59', '66', '74', '83', '93', '105', '120', '120+'],
  ['47', '52', '57', '63', '69', '76', '84', '84+'],
]

// The heavier class and juniors are, on average, a bit stronger and weaker respectively.
const CLASS_STRENGTH = { same: 1, next: 1.08 }
const CATEGORY_STRENGTH = { Senior: 1, Junior: 0.9 }
const CATEGORY_ORDER = ['Senior', 'Junior']

// IPF GL point coefficients for classic (raw) powerlifting: points = total × 100 / (A − B·e^(−C·bodyweight)).
const GL = {
  men: { a: 1199.72839, b: 1025.18162, c: 0.00921 },
  women: { a: 610.32796, b: 1045.59282, c: 0.03048 },
}

// Referee lights per result button: left, center, right ('w' white; red with reason cards r/y/b).
export const LIGHTS = {
  www: ['w', 'w', 'w'],
  wwr: ['w', 'w', 'r'],
  wrr: ['w', 'r', 'by'],
  rrr: ['r', 'ry', 'r'],
}

export function defaultEditorState(random = Math.random) {
  return {
    mode: 'card', // 'card' (big lifter card, shown first), 'bar' (lower third), 'list' (results list) or 'slate'
    slate: 'start', // which slate: 'start', 'pause' or 'end'
    title: 'Distriktsmästerskapen 2026', // the competition's name, for the slates
    clockStartedAt: null, // when the demo's 60-second attempt clock was started (ms timestamp)
    lastInRound: false, // a result ends the round, so the results list is shown (as between rounds live)
    name: 'Anders Andersson',
    team: 'Stark AK',
    weightClass: '93',
    discipline: 'SQ',
    attempt: 1,
    weight: 200,
    nextName: 'Bertil Svensson',
    nextWeight: 192.5,
    result: null, // a key of LIGHTS once a result has been given
    decisions: 0, // counts result button presses, so that each press is a new referee decision
    history: randomHistory(random),
  }
}

// The made-up parts: steps between attempts, which attempts were good, the lifter's body weight within the class,
// and the rest of the group (relative strength and their own attempts).
export function randomHistory(random = Math.random) {
  const pick = (list) => list[Math.floor(random() * list.length)]
  const attempts = () => ({
    increments: Object.fromEntries(ORDER.map((d) => [d, [pick(INCREMENTS), pick(INCREMENTS)]])),
    outcomes: Object.fromEntries(ORDER.map((d) => [d, [random() < 0.9, random() < 0.75, random() < 0.6]])),
  })

  return {
    ...attempts(),
    bodyweight: random(),
    field: FIELD.map(([name, team, weightClass, category]) => ({
      name,
      team,
      weightClass,
      category,
      strength: (0.85 + random() * 0.25) * CLASS_STRENGTH[weightClass] * CATEGORY_STRENGTH[category],
      // How far under the class limit the lifter weighed in (0–1 of the gap down to the class below).
      bodyweight: random(),
      ...attempts(),
    })),
  }
}

export function createEditorSource(state) {
  return {
    async load() {
      return editorFeed(state)
    },
  }
}

export const CLOCK_SECONDS = 60

export function editorFeed(state, now = Date.now()) {
  const discipline = ORDER.includes(state.discipline) ? state.discipline : 'SQ'
  const attempt = Math.min(3, Math.max(1, Number(state.attempt) || 1))
  const weight = roundWeight(Number(state.weight) || 0)
  const lift = `${discipline}${attempt}`
  const lights = LIGHTS[state.result] ?? null

  const lifts = madeUpLifts(state.history, discipline, attempt, weight)
  lifts[lift] = format(weight)
  if (lights) {
    lifts[`${lift}_status`] = lights.filter((light) => light.includes('w')).length >= 2 ? 'good' : 'failed'
  }

  // The rest of the group: the same discipline and round, scaled to their strength. They have done the attempts
  // before the current round, declared the current one, and declared openers in the disciplines to come.
  const field = state.history.field.map((other, index) => {
    const theirWeight = roundWeight(weight * other.strength)
    const theirLifts = madeUpLifts(other, discipline, attempt, theirWeight)
    theirLifts[lift] = format(theirWeight)
    for (const later of ORDER.slice(ORDER.indexOf(discipline) + 1)) {
      theirLifts[`${later}1`] = format(roundWeight((theirWeight * RATIO[later]) / RATIO[discipline]))
    }
    return {
      id: `field-${index}`,
      name: other.name,
      team: other.team,
      weightclass: other.weightClass === 'next' ? nextWeightClass(state.weightClass) : state.weightClass,
      category: other.category,
      lifts: theirLifts,
      total: total(theirLifts),
      wilks: ipfPoints(
        total(theirLifts),
        other.weightClass === 'next' ? nextWeightClass(state.weightClass) : state.weightClass,
        other.bodyweight,
      ),
    }
  })

  const lifter = {
    id: 'editor',
    name: state.name,
    team: state.team,
    weightclass: state.weightClass,
    category: 'Senior',
    lifts,
    total: total(lifts),
    wilks: ipfPoints(total(lifts), state.weightClass, state.history.bodyweight),
  }
  const ranked = rank([lifter, ...field])
  Object.assign(
    lifter,
    ranked.find((one) => one.id === 'editor'),
  )

  const nextWeight = roundWeight(Number(state.nextWeight) || 0)
  const next = state.nextName?.trim()
    ? [{ weight: format(nextWeight), lifter: { id: 'editor-next', name: state.nextName, lifts: {} } }]
    : []

  return {
    next_lifters: {
      info: { current_group: 'Grupp 1', current_round: attempt },
      lifters: [{ attempt: String(attempt), lift, discipline, weight: format(weight), lifter }, ...next],
    },
    scoresheet: {
      info: { current_discipline: discipline, current_group: 'Grupp 1' },
      lifters: ranked,
      last_lifts: lights
        ? [
            {
              lifter_id: 'editor',
              lift,
              attempt: String(attempt),
              discipline,
              weight: format(weight),
              status: lifts[`${lift}_status`],
              lifter,
            },
          ]
        : [],
    },
    decision: lights ? { updated: state.decisions, left: lights[0], center: lights[1], right: lights[2] } : {},
    clock: {
      clock: state.clockStartedAt ? Math.max(0, Math.ceil(CLOCK_SECONDS - (now - state.clockStartedAt) / 1000)) : 0,
    },
  }
}

// Earlier attempts in the current discipline, and all attempts in earlier disciplines, scaled from the
// requested weight so that they stay plausible whatever weight is entered.
function madeUpLifts(history, discipline, attempt, weight) {
  const lifts = {}

  for (const d of ORDER.slice(0, ORDER.indexOf(discipline) + 1)) {
    const isCurrent = d === discipline
    const [first, second] = history.increments[d]
    const top = isCurrent ? weight : roundWeight((weight * RATIO[d]) / RATIO[discipline])
    const lastIndex = isCurrent ? attempt - 1 : 3
    // Weights counted down from the top attempt (the current one, or the third in an earlier discipline).
    const weights = isCurrent
      ? [top - (attempt === 3 ? first + second : first), attempt === 3 ? top - second : top, top]
      : [top - first - second, top - second, top]

    for (let n = 1; n <= lastIndex; n++) {
      lifts[`${d}${n}`] = format(Math.max(2.5, weights[n - 1]))
      lifts[`${d}${n}_status`] = history.outcomes[d][n - 1] ? 'good' : 'failed'
    }
  }

  return lifts
}

// Ordered like Powerlifting Live's scoresheet: by weight class, then category (Senior before Junior), then total.
// Places are counted within each class and category (0 = no total yet).
function rank(lifters) {
  const classIndex = (lifter) =>
    Number.parseFloat(lifter.weightclass) + (String(lifter.weightclass).includes('+') ? 1 : 0)
  const sorted = [...lifters].sort(
    (a, b) =>
      classIndex(a) - classIndex(b) ||
      CATEGORY_ORDER.indexOf(a.category) - CATEGORY_ORDER.indexOf(b.category) ||
      b.total - a.total,
  )

  const counters = {}
  return sorted.map((lifter) => {
    const key = `${lifter.weightclass}|${lifter.category}`
    counters[key] = (counters[key] ?? 0) + 1
    return { ...lifter, place: lifter.total > 0 ? counters[key] : 0 }
  })
}

// IPF GL points for a total in a weight class, with a made-up body weight inside the class.
function ipfPoints(lifted, weightClass, position) {
  const value = String(weightClass ?? '').replace(/^-/, '')
  const women = WEIGHT_CLASSES[1].includes(value)
  const classes = WEIGHT_CLASSES[women ? 1 : 0]
  const index = classes.indexOf(value)
  const limit = Number.parseFloat(value)
  if (!lifted || !Number.isFinite(limit)) {
    return 0
  }

  const below = index > 0 ? Number.parseFloat(classes[index - 1]) : limit - 8
  const bodyweight = value.includes('+') ? limit + 2 + position * 25 : limit - position * (limit - below)
  const { a, b, c } = GL[women ? 'women' : 'men']
  return Math.round(((lifted * 100) / (a - b * Math.exp(-c * bodyweight))) * 100) / 100
}

function nextWeightClass(weightClass) {
  const value = String(weightClass ?? '').replace(/^-/, '')
  for (const classes of WEIGHT_CLASSES) {
    const index = classes.indexOf(value)
    if (index !== -1) {
      return classes[Math.min(index + 1, classes.length - 1)]
    }
  }
  const number = Number.parseFloat(value)
  return Number.isFinite(number) ? String(number + 10) : '+'
}

function total(lifts) {
  return ORDER.reduce((sum, d) => {
    const good = [1, 2, 3].filter((n) => lifts[`${d}${n}_status`] === 'good').map((n) => Number(lifts[`${d}${n}`]))
    return sum + (good.length ? Math.max(...good) : 0)
  }, 0)
}

function roundWeight(value) {
  return Math.max(0, Math.round(value / 2.5) * 2.5)
}

function format(value) {
  return value.toFixed(1)
}
