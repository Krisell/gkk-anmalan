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
 * A recorded good lift also gets placeChange { from, to }: the lifter's place before (the last place seen in
 * earlier snapshots) and after the lift, for "Tar ledningen" / "↑ 2 placeringar".
 *
 * A recorded result gets endsRound when it was the last lift of a round: the lifter called next is on another
 * attempt or discipline, or nobody is called (the group is done). The overlay then shows the results list.
 */

export const RESULT_DURATION_MS = 7000

export function initialOverlayState() {
  return {
    initialized: false,
    seenResultId: null,
    seenDecisionId: null,
    platform: null,
    result: null,
    places: {},
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
      places: knownPlaces({}, snapshot),
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

    const placeChange =
      newResult.outcome === 'good' ? { from: state.places[newResult.lifterId] ?? null, to: newResult.place } : null

    // An unknown attempt number of the next lifter says nothing about the round.
    const next = snapshot.current
    const endsRound =
      !next ||
      (next.attempt != null && (next.attempt !== newResult.attempt || next.discipline !== newResult.discipline))

    result = { ...newResult, decisions, confirmed: true, shownAt, placeChange, endsRound }
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
    places: knownPlaces(state.places, snapshot),
  }
}

// The latest known place of each lifter, from the results list and the lifters in the snapshot.
function knownPlaces(places, snapshot) {
  const next = { ...places }
  for (const lifter of [...(snapshot.scoreboard?.lifters ?? []), snapshot.current, snapshot.lastResult]) {
    const id = lifter?.lifterId ?? lifter?.id
    if (id && lifter.place != null) {
      next[id] = lifter.place
    }
  }
  return next
}

function majority(lights) {
  return lights.filter((light) => light.good).length >= 2 ? 'good' : 'failed'
}
