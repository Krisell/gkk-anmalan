import mqtt from 'mqtt'

/**
 * Listens to Powerlifting Live's referee decisions as they are published, and keeps the latest one.
 *
 * Powerlifting Live's own screens get their updates over MQTT (WebSocket), and read feed.php only to catch up after
 * (re)connecting. That works for every topic except the referee decision: feed.php never holds it (it stays `{}`), it
 * only exists as a live message, followed by referee/clear about 1.5 s later. So the overlay subscribes to it
 * directly, like a screen in the hall would. One connection per overlay, carrying only the decisions.
 *
 * Messages missed while disconnected are lost; the result is then still shown from the scoresheet, without lights.
 *
 * The referee system sometimes publishes the same decision several times within a second (each with a new
 * `updated`). A repeat of the same lights within REPEAT_WINDOW_MS is ignored, so that a late repeat can't be taken
 * as a new decision for the next lifter.
 *
 * It also follows referee/signal, sent as each referee decides ({ referee: 'left', decision: 'w' }), so that the
 * overlay can light each lamp as soon as that referee has decided (Powerlifting Live's own screens only show *that*
 * a referee has decided, and reveal the lights together). Signals are cleared by referee/clear or the decision, and
 * dropped after SIGNAL_MAX_AGE_MS in case those were missed.
 */
const REPEAT_WINDOW_MS = 5000
const SIGNAL_MAX_AGE_MS = 20000
const REFEREES = ['left', 'center', 'right']

export function createRefereeFeed(url, competition, now = () => Date.now()) {
  let state = initialRefereeState()

  const client = mqtt.connect(url, { keepalive: 5, reconnectPeriod: 2000, connectTimeout: 10000 })
  const prefix = `competition/${competition}/referee/`

  client.on('connect', () => client.subscribe(['decision', 'signal', 'clear'].map((topic) => prefix + topic)))
  client.on('message', (topic, payload) => {
    state = reduceRefereeMessage(state, topic.slice(prefix.length), payload.toString(), now())
  })

  return {
    // The raw decision ({ updated, left, center, right }), in the format feed.php uses for the other topics.
    latest: () => state.decision,
    // The referees' decisions so far ({ left, center, right }: 'w', 'r', 'ry', … or null), or null when none has.
    signals: () => currentSignals(state, now()),
    stop: () => client.end(true),
  }
}

export function initialRefereeState() {
  return { decision: null, signals: {}, signalledAt: null }
}

// The referee feed's state after a message on referee/decision, referee/signal or referee/clear.
export function reduceRefereeMessage(state, topic, text, now) {
  let message
  try {
    message = JSON.parse(text)
  } catch {
    return state
  }

  if (topic === 'decision' && message?.updated != null) {
    // A decision clears the signals: the lights are on now.
    return { decision: isRepeat(state.decision, message) ? state.decision : message, signals: {}, signalledAt: null }
  }
  if (topic === 'signal' && REFEREES.includes(message?.referee) && typeof message.decision === 'string') {
    return { ...state, signals: { ...state.signals, [message.referee]: message.decision }, signalledAt: now }
  }
  if (topic === 'clear') {
    return { ...state, signals: {}, signalledAt: null }
  }
  return state
}

export function currentSignals(state, now) {
  if (!state.signalledAt || now - state.signalledAt > SIGNAL_MAX_AGE_MS) {
    return null
  }
  return Object.fromEntries(REFEREES.map((referee) => [referee, state.signals[referee] ?? null]))
}

// Whether a decision repeats the previous one: the same lights, published within REPEAT_WINDOW_MS.
export function isRepeat(previous, decision) {
  const lights = (d) => [d.left, d.center, d.right].join(',')
  return Boolean(
    previous &&
    lights(previous) === lights(decision) &&
    Math.abs(decision.updated - previous.updated) < REPEAT_WINDOW_MS,
  )
}

// Powerlifting Live's MQTT endpoint: wss://<host>/ws for its https site.
export function refereeFeedUrl(site = 'https://se.powerliftinglive.com') {
  const url = new URL('/ws', site)
  url.protocol = url.protocol === 'https:' ? 'wss:' : 'ws:'
  return url.toString()
}
