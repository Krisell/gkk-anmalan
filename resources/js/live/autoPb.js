import { onScopeDispose, watch } from 'vue'
import { bestsFor as bestsOfAttempt, compareWithBests } from './control/personalBest.js'

/**
 * The automatic PB stamp (the live control's "Automatisk PB-stämpel"): for an attempt above the lifter's best
 * registered result in that lift (not the total), the stamp is shown twice:
 * - when the lifter is called: on the corner of the big lifter card, once it is in (CARD_DELAY_MS after it comes);
 *   without a card (the same lifter again, or the cards turned off) AFTER_CALL_MS after nothing covers the picture,
 * - when the attempt clock starts (once per attempt); if the card, the lights or a result are up then, as soon as
 *   they are gone. Both are shown, however close together.
 * (On the broadcast the clock's is "PB-försök", which stays until the decision.) And, if `showGood` is given (the
 * broadcast), when such an attempt is judged good ("NYTT PB!").
 *
 * attempt(): the attempt coming up (no outcome), or null; card(): the attempt on the big lifter card, or null;
 * busy(): whether the card, the lights or a result cover the picture; clock(): the clock's seconds, or null when it
 * isn't running; enabled(); bestsFor(lifterId); show(attempt, moment), moment 'card' (on the card), 'call' (no card,
 * the clock not running) or 'clock'; result(): the attempt last judged (with its outcome), or null; showGood(attempt).
 */
export const CARD_DELAY_MS = 1800
export const AFTER_CALL_MS = 3000

export function isPbAttempt(attempt, lifterBests) {
  // Never for a lifter without results (e.g. their first competition): every lift is then 'first', not 'pb'.
  return compareWithBests(attempt, bestsOfAttempt(attempt, lifterBests)).some(
    (row) => row.key !== 'total' && row.verdict === 'pb',
  )
}

export function useAutoPb({
  attempt,
  card = () => null,
  busy,
  clock,
  enabled,
  bestsFor,
  show,
  result = () => null,
  showGood = null,
}) {
  let key = null
  let shownCall = false
  let shownClock = false
  let clockPending = false
  let timer = null

  function fire(moment) {
    const current = attempt()
    if (!current || current.outcome || !enabled()) {
      return
    }
    if (isPbAttempt(current, bestsFor(current.lifterId))) {
      show(current, moment)
    }
  }

  // When the lifter is called: on the card, or after the call. A string, so that only a change starts a timer over.
  watch(
    () => {
      const current = attempt()
      const id = current && !current.outcome ? current.id : null
      const onCard = Boolean(id && card()?.lifterId === current.lifterId)
      return `${id ?? ''}|${onCard ? 'card' : busy() ? 'covered' : 'clear'}`
    },
    (state) => {
      const [id, picture] = state.split('|')
      if ((id || null) !== key) {
        key = id || null
        shownCall = false
        shownClock = false
        clockPending = false
      }
      clearTimeout(timer)
      timer = null
      if (!key) {
        return
      }
      if (!shownCall && picture === 'card') {
        timer = setTimeout(() => {
          shownCall = true
          fire('card')
        }, CARD_DELAY_MS)
      } else if (!shownCall && picture === 'clear') {
        timer = setTimeout(() => {
          shownCall = true
          // Once the clock runs the lifter is on their way to the bar: off the middle then.
          fire(clock() != null ? 'clock' : 'call')
        }, AFTER_CALL_MS)
      }
      if (clockPending && picture === 'clear') {
        clockPending = false
        shownClock = true
        fire('clock')
      }
    },
    { immediate: true },
  )

  // When the clock starts (not one already running when the lifter is called).
  watch(
    () => clock() != null,
    (running, wasRunning) => {
      if (!running || wasRunning || !key || shownClock) {
        return
      }
      if (busy()) {
        clockPending = true
      } else {
        shownClock = true
        fire('clock')
      }
    },
  )

  // A PB attempt judged good: once per attempt, as soon as its outcome is known (the lights, or the recorded result).
  if (showGood) {
    let goodShownFor = null
    watch(
      () => {
        const judged = result()
        return judged?.outcome === 'good' ? judged : null
      },
      (judged) => {
        if (judged && judged.id !== goodShownFor && enabled() && isPbAttempt(judged, bestsFor(judged.lifterId))) {
          goodShownFor = judged.id
          showGood(judged)
        }
      },
    )
  }

  onScopeDispose(() => clearTimeout(timer))
}
