/**
 * Decides what the overlay shows, given successive normalized snapshots.
 *
 * Results are identified by attempt id (lifter + lift, e.g. '110845:BP2') and referee decisions by their
 * own id, so a result is only shown when something actually changed. Polls returning the same data, or
 * a reload of the page mid-competition, never replay an old result.
 *
 * With an integrated referee system the decision is what records the result, so it arrives before or
 * together with the recorded result and the call of the next lifter:
 * - a new decision alone shows the lights right away for the lifter on the platform,
 * - a new recorded result confirms it (or, without referee lights, is shown on its own).
 *
 * Without referee lights in the feed (Powerlifting Live publishes them only when the referees judge through it), the
 * secretariat's entry records the result, and Powerlifting Live publishes the new lifter order a second or two before
 * the scoresheet with the result. When the lifter on the platform is replaced before their attempt has a result, the
 * overlay therefore keeps showing them (awaiting) for up to RESULT_WAIT_MS, so that the next lifter isn't introduced
 * just before the result. Only a different lifter on the platform counts (the demo editor changes attempts in place),
 * and a lifter who is only moved back (a changed weight) isn't waited for: they are still in line.
 *
 * A recorded result gets endsRound when it was the last lift of a round: the lifter called next is on another
 * attempt or discipline, or nobody is called (the group is done). The overlay then shows the results list.
 */

export const RESULT_DURATION_MS = 10000

export const RESULT_WAIT_MS = 5000

export function initialOverlayState() {
  return {
    initialized: false,
    seenResultId: null,
    seenDecisionId: null,
    platform: null,
    awaiting: null,
    result: null,
  }
}

export function reduceOverlayState(state, snapshot, now) {
  const { decision, lastResult } = snapshot

  if (!state.initialized) {
    // Baseline: whatever is already in the feed when the overlay starts is old news.
    return {
      initialized: true,
      seenResultId: lastResult?.id ?? null,
      seenDecisionId: decision?.id ?? null,
      platform: snapshot.current,
      awaiting: null,
      result: null,
    }
  }

  const newDecision = decision && decision.id !== state.seenDecisionId ? decision : null
  const newResult = lastResult && lastResult.id !== state.seenResultId ? lastResult : null
  let result = state.result

  if (newResult) {
    // An unconfirmed result is one shown from referee lights only; its lights belong to this recorded result.
    const pending = result && !result.confirmed ? result : null
    const decisions = newDecision?.lights ?? pending?.decisions ?? null
    // If the lights were already shown for this very attempt, keep shownAt so nothing replays.
    const shownAt = pending?.id === newResult.id ? pending.shownAt : now

    // An unknown attempt number of the next lifter says nothing about the round.
    const next = snapshot.current
    const endsRound =
      !next ||
      (next.attempt != null && (next.attempt !== newResult.attempt || next.discipline !== newResult.discipline))

    result = { ...newResult, decisions, confirmed: true, shownAt, endsRound }
  } else if (newDecision) {
    // The lifter we were showing (the previous snapshot's current lifter) is the one being judged.
    const judged = state.awaiting?.attempt ?? state.platform ?? snapshot.current
    if (judged) {
      const outcome = majority(newDecision.lights)
      result = { ...judged, outcome, decisions: newDecision.lights, confirmed: false, shownAt: now }
    }
  }

  return {
    initialized: true,
    seenResultId: lastResult?.id ?? state.seenResultId,
    seenDecisionId: decision?.id ?? state.seenDecisionId,
    platform: snapshot.current,
    awaiting: newResult || newDecision ? null : awaitedAttempt(state, snapshot, now),
    result,
  }
}

// The attempt still waiting for its result, if the lifter on the platform was replaced before it was recorded.
function awaitedAttempt(state, snapshot, now) {
  if (state.awaiting && now < state.awaiting.until) {
    return state.awaiting
  }

  const previous = state.platform
  const replaced =
    previous &&
    previous.lifterId !== snapshot.current?.lifterId &&
    previous.id !== snapshot.next?.id &&
    previous.id !== state.seenResultId &&
    previous.id !== state.result?.id

  return replaced ? { attempt: previous, until: now + RESULT_WAIT_MS } : null
}

function majority(lights) {
  return lights.filter((light) => light.good).length >= 2 ? 'good' : 'failed'
}
