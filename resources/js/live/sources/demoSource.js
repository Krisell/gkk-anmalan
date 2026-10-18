/**
 * Simulated competition for demo mode (/live/overlay?demo=true). It produces the same raw format as the
 * Laravel proxy, so the whole pipeline (normalizing, result detection, rendering) is exercised without
 * any network requests. Apart from Johanna Hassel (pictured in the demo backdrop) the lifters are fictional.
 */

const LIFTERS = {
  johanna: {
    id: 'd1',
    name: 'Johanna Hassel',
    team: 'Göteborg KK',
    weightclass: '63',
    category: 'Senior',
    place: 2,
    prog_place: 1,
  },
  erik: {
    id: 'd2',
    name: 'Erik Johansson',
    team: 'Göteborg KK',
    weightclass: '93',
    category: 'Senior',
    place: 1,
    prog_place: 1,
  },
  sara: {
    id: 'd3',
    name: 'Sara Nilsson',
    team: 'Malmö AK',
    weightclass: '76',
    category: 'Junior 19-23',
    place: 1,
    prog_place: 2,
  },
  ahmed: {
    id: 'd4',
    name: 'Ahmed Karimi',
    team: 'Västerås SK',
    weightclass: '120+',
    category: 'Master 1',
    place: 1,
    prog_place: 1,
  },
}

// Each step mutates the simulated competition. A step's state is the result of all steps up to it.
export const DEMO_STEPS = [
  { label: 'Före start – ingen lyftare uppropad', apply: () => {} },
  {
    label: 'Johanna förbereder knäböj, försök 2',
    apply: (sim) => {
      sim.discipline = 'SQ'
      sim.round = 2
      Object.assign(sim.lifts.johanna, { SQ1: '122.5', SQ1_status: 'good', SQ2: '130.0' })
      Object.assign(sim.lifts.erik, { SQ1: '195.0', SQ1_status: 'good', SQ2: '200.0' })
      Object.assign(sim.lifts.sara, { SQ1: '140.0', SQ1_status: 'good', SQ2: '145.0' })
      Object.assign(sim.lifts.ahmed, { SQ1: '240.0', SQ1_status: 'good', SQ2: '250.0' })
      sim.queue = ['johanna', 'erik', 'sara']
    },
  },
  { label: 'Domarbeslut: tre vita lampor', apply: (sim) => decide(sim, 'w', 'w', 'w') },
  {
    label: 'Godkänt registrerat, Erik ropas upp',
    apply: (sim) => {
      record(sim, 'johanna', 'SQ2', 'good')
      sim.lifts.johanna.SQ3 = '137.5'
      sim.queue = ['erik', 'sara', 'johanna']
    },
  },
  { label: 'Ny begärd vikt för Erik: 202,5 kg', apply: (sim) => (sim.lifts.erik.SQ2 = '202.5') },
  { label: 'Domarbeslut: en vit, två röda', apply: (sim) => decide(sim, 'w', 'r', 'by') },
  {
    label: 'Underkänt registrerat, Sara ropas upp',
    apply: (sim) => {
      record(sim, 'erik', 'SQ2', 'failed')
      sim.lifts.erik.SQ3 = '202.5'
      sim.queue = ['sara', 'johanna', 'erik']
    },
  },
  {
    label: 'Godkänt utan domarlampor (manuellt), Ahmed ropas upp',
    apply: (sim) => {
      record(sim, 'sara', 'SQ2', 'good')
      sim.queue = ['ahmed', 'johanna', 'erik']
    },
  },
  {
    label: 'Ahmed godkänd, byte till bänkpress – Johanna försök 2',
    apply: (sim) => {
      record(sim, 'ahmed', 'SQ2', 'good')
      sim.discipline = 'BP'
      sim.round = 2
      Object.assign(sim.lifts.johanna, { BP1: '72.5', BP1_status: 'good', BP2: '77.5' })
      Object.assign(sim.lifts.sara, { BP1: '85.0', BP1_status: 'good', BP2: '90.0' })
      sim.queue = ['johanna', 'sara']
    },
  },
  {
    label: 'Domarbeslut och resultat samtidigt: två vita, en röd',
    apply: (sim) => {
      decide(sim, 'w', 'w', 'r')
      record(sim, 'johanna', 'BP2', 'good')
      Object.assign(sim.lifts.ahmed, { BP1: '160.0', BP1_status: 'good', BP2: '167.5' })
      sim.queue = ['sara', 'ahmed']
    },
  },
  {
    label: 'Byte till marklyft, Erik försök 3',
    apply: (sim) => {
      sim.discipline = 'DL'
      sim.round = 3
      Object.assign(sim.lifts.erik, {
        DL1: '240.0',
        DL1_status: 'good',
        DL2: '255.0',
        DL2_status: 'good',
        DL3: '265.0',
      })
      Object.assign(sim.lifts.sara, {
        DL1: '170.0',
        DL1_status: 'good',
        DL2: '180.0',
        DL2_status: 'good',
        DL3: '187.5',
      })
      sim.queue = ['erik', 'sara']
    },
  },
  { label: 'Domarbeslut: tre röda lampor', apply: (sim) => decide(sim, 'r', 'ry', 'r') },
  {
    label: 'Underkänt registrerat, gruppen avslutad',
    apply: (sim) => {
      record(sim, 'erik', 'DL3', 'failed')
      sim.queue = []
    },
  },
]

export function createDemoSource() {
  let index = 0

  return {
    get index() {
      return index
    },
    get steps() {
      return DEMO_STEPS
    },
    go(next) {
      index = (next + DEMO_STEPS.length) % DEMO_STEPS.length
    },
    async load() {
      return demoFeed(index)
    },
  }
}

export function demoFeed(index) {
  const sim = {
    discipline: null,
    round: 1,
    queue: [],
    lifts: Object.fromEntries(Object.keys(LIFTERS).map((key) => [key, {}])),
    lastLifts: [],
    decision: {},
    decisions: 0,
  }

  DEMO_STEPS.slice(0, index + 1).forEach((step) => step.apply(sim))

  const lifter = (key) => ({ ...LIFTERS[key], lifts: { ...sim.lifts[key] }, total: total(sim.lifts[key]) })

  return {
    next_lifters: {
      info: { current_group: 'Grupp 1', current_round: sim.queue.length ? sim.round : null },
      lifters: sim.queue.map((key) => ({
        weight: currentWeight(sim, key),
        lifter: lifter(key),
      })),
    },
    scoresheet: {
      info: { current_discipline: sim.discipline, current_group: 'Grupp 1' },
      last_lifts: sim.lastLifts.map((lift) => ({ ...lift, lifter: lifter(lift.key) })),
    },
    decision: sim.decision,
  }
}

// Sum of the best good lift in each discipline, like Powerlifting Live's running total.
function total(lifts) {
  return ['SQ', 'BP', 'DL'].reduce((sum, d) => {
    const good = [1, 2, 3].filter((n) => lifts[`${d}${n}_status`] === 'good').map((n) => Number(lifts[`${d}${n}`]))
    return sum + (good.length ? Math.max(...good) : 0)
  }, 0)
}

function currentWeight(sim, key) {
  const lifts = sim.lifts[key]
  const open = [1, 2, 3].find((n) => lifts[`${sim.discipline}${n}`] && !lifts[`${sim.discipline}${n}_status`])
  return open ? lifts[`${sim.discipline}${open}`] : null
}

function decide(sim, left, center, right) {
  sim.decisions++
  sim.decision = { updated: 1700000000 + sim.decisions, left, center, right }
}

function record(sim, key, lift, status) {
  sim.lifts[key][`${lift}_status`] = status
  sim.lastLifts.unshift({
    key,
    lifter_id: LIFTERS[key].id,
    lift,
    attempt: lift.slice(2),
    discipline: lift.slice(0, 2),
    weight: sim.lifts[key][lift],
    status,
  })
}
