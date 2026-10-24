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
 */
const REPEAT_WINDOW_MS = 5000

export function createRefereeFeed(url, competition) {
  let latest = null

  const client = mqtt.connect(url, { keepalive: 5, reconnectPeriod: 2000, connectTimeout: 10000 })
  const topic = `competition/${competition}/referee/decision`

  client.on('connect', () => client.subscribe(topic))
  client.on('message', (_, payload) => {
    try {
      const decision = JSON.parse(payload.toString())
      if (decision?.updated == null) {
        return
      }
      if (!isRepeat(latest, decision)) {
        latest = decision
      }
    } catch {
      // Not a decision; keep the last one.
    }
  })

  return {
    // The raw decision ({ updated, left, center, right }), in the format feed.php uses for the other topics.
    latest: () => latest,
    stop: () => client.end(true),
  }
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
