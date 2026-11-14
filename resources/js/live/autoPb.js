import { onScopeDispose, watch } from 'vue'
import { compareWithBests } from './control/personalBest.js'

/**
 * The automatic PB stamp (the live control's "Automatisk PB-stämpel"): for an attempt above the lifter's best
 * registered result in that lift (not the total), the stamp is shown twice:
 * - a few seconds after the lifter is called, once the big lifter card is down (AFTER_CARD_MS after nothing covers
 *   the picture: the card, the lights or a result), or as soon after the call as that,
 * - when the attempt clock starts (once per attempt; not while the card or the lights are up, when the first one is
 *   still to come). Both are shown, however close together.
 * And, if `showGood` is given (the broadcast), once more when such an attempt is judged good ("NYTT PB!").
 *
 * attempt(): the attempt coming up (no outcome), or null; busy(): whether the card or the lights cover the picture;
 * clock(): the clock's seconds, or null when it isn't running; enabled(); bestsFor(lifterId); show(attempt, moment),
 * moment 'card' or 'clock'; result(): the attempt last judged (with its outcome), or null; showGood(attempt).
 */
export const AFTER_CARD_MS = 3000

export function isPbAttempt(attempt, lifterBests) {
  return (
    Boolean(lifterBests?.found) &&
    compareWithBests(attempt, lifterBests.bests).some((row) => row.key !== 'total' && row.verdict === 'pb')
  )
}

export function useAutoPb({ attempt, busy, clock, enabled, bestsFor, show, result = () => null, showGood = null }) {
  let key = null
  let shownCard = false
  let shownClock = false
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

  // After the call: once nothing covers the picture, and stays so for AFTER_CARD_MS.
  watch(
    () => {
      const current = attempt()
      return [current && !current.outcome ? current.id : null, Boolean(busy())]
    },
    ([id, covered]) => {
      if (id !== key) {
        key = id
        shownCard = false
        shownClock = false
      }
      clearTimeout(timer)
      if (id && !covered && !shownCard) {
        timer = setTimeout(() => {
          shownCard = true
          fire('card')
        }, AFTER_CARD_MS)
      }
    },
    { immediate: true },
  )

  // When the clock starts (not one already running when the lifter is called).
  watch(
    () => clock() != null,
    (running, wasRunning) => {
      if (running && !wasRunning && key && !shownClock && !busy()) {
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
