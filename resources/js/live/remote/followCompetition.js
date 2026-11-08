import { onValue, ref as databaseRef } from 'firebase/database'
import { liveDatabase } from './firebase.js'
import { normalizeCompetition } from './controlState.js'

// The last competition chosen, so that the overlay starts on it even if Firebase is slow or unreachable.
const REMEMBERED_KEY = 'live-overlay-competition'

/**
 * The competition chosen on the control page (live/control/competition), for an overlay whose address has no
 * ?competition=. Calls `onChange(id)` with the remembered competition first (if any), and then every time the control
 * page chooses another one. Clearing the choice never takes a running overlay off its competition.
 */
export function followCompetition(onChange) {
  let current = normalizeCompetition(remembered())
  if (current) {
    onChange(current)
  }

  return onValue(
    databaseRef(liveDatabase(), 'live/control/competition'),
    (snapshot) => {
      const chosen = normalizeCompetition(snapshot.val())
      if (chosen && chosen !== current) {
        current = chosen
        remember(chosen)
        onChange(chosen)
      }
    },
    (error) => console.error('[live-overlay] competition from the control page', error),
  )
}

function remembered() {
  try {
    return localStorage.getItem(REMEMBERED_KEY)
  } catch {
    return null
  }
}

function remember(id) {
  try {
    localStorage.setItem(REMEMBERED_KEY, id)
  } catch {
    // Not remembered; the control page's choice still applies once Firebase answers.
  }
}
