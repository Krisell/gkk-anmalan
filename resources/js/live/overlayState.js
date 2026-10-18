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
 */

export const RESULT_DURATION_MS = 7000

export function initialOverlayState() {
  return {
    initialized: false,
    seenResultId: null,
    seenDecisionId: null,
    platform: null,
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

    result = { ...newResult, decisions, confirmed: true, shownAt }
  } else if (newDecision) {
    // The lifter we were showing (the previous snapshot's current lifter) is the one being judged.
    const judged = state.platform ?? snapshot.current
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
    result,
  }
}

function majority(lights) {
  return lights.filter((light) => light.good).length >= 2 ? 'good' : 'failed'
}
