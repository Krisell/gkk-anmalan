/**
 * Whose lift the referee lights on the warm-up hall screen belong to, and when the next lifter may be introduced.
 *
 * It must never look as if a lifter has already lifted: the lights must never be shown with the card of the lifter
 * called next, and the next lifter's card must not come before the lights of the lift before. Powerlifting Live
 * doesn't publish things in a fixed order, though: the next lifter can be called (and the result recorded) a moment
 * before the referee decision reaches us, or while the referees are still deciding.
 *
 * So:
 * - The attempt the lights belong to (judged) is decided when they come on: the attempt on the platform, unless it was
 *   replaced less than CALL_RACE_MS ago (then the next lifter was called just before the lights reached us, and the
 *   lights belong to the replaced attempt). A lift takes far longer than that after its call, so the attempt called
 *   just before can't be the one judged.
 * - While the lights are up, only a newly recorded result changes it (Powerlifting Live's own record of which attempt
 *   was judged), never a change on the platform.
 * - A newly called lifter is introduced (readyToIntroduce) INTRO_DELAY_MS after the call at the earliest, and, once
 *   the page has seen referee lights, only when the attempt before has had its lights, or after INTRO_MAX_WAIT_MS
 *   (e.g. a decision that never reached us).
 *
 * Attempts are the normalized Attempts of normalize.js, identified by id (lifter + lift, e.g. '110845:BP2').
 */

export const CALL_RACE_MS = 5000
export const INTRO_DELAY_MS = 3000
export const INTRO_MAX_WAIT_MS = 8000
const JUDGED_IDS_KEPT = 10

export function initialHallState() {
  return {
    initialized: false,
    // The attempt on the platform, the one it replaced, and when.
    platform: null,
    previous: null,
    replacedAt: null,
    // The attempt the lights shown belong to (null while no lights are up).
    judged: null,
    lit: false,
    // Whether this page has seen any referee lights (a competition judged through Powerlifting Live).
    lightsSeen: false,
    // Recently judged attempts.
    judgedIds: [],
    seenResultId: null,
  }
}

/**
 * The next state, given the lifter on the platform (current), whether the lights are up (lit), the most recently
 * recorded result (lastResult) and the time.
 */
export function reduceHallState(state, { current, lit, lastResult, now }) {
  if (!state.initialized) {
    return {
      ...initialHallState(),
      initialized: true,
      platform: current ?? null,
      // Lights already up when the page opens belong to the lifter on the platform.
      judged: lit ? (current ?? null) : null,
      lit: Boolean(lit),
      lightsSeen: Boolean(lit),
      judgedIds: lit && current ? [current.id] : [],
      seenResultId: lastResult?.id ?? null,
    }
  }

  let { platform, previous, replacedAt, judged, judgedIds } = state

  // Follow the platform. Another attempt (also the same lifter's next attempt) replaces the one there.
  if ((current?.id ?? null) !== (platform?.id ?? null)) {
    previous = platform
    replacedAt = now
  }
  platform = current ?? null

  const newResult = lastResult && lastResult.id !== state.seenResultId ? lastResult : null

  if (lit && !state.lit) {
    const justReplaced = previous && replacedAt != null && now - replacedAt < CALL_RACE_MS
    judged = justReplaced ? previous : platform
  }
  if (lit && newResult) {
    judged = newResult
  }
  if (!lit) {
    judged = null
  }
  if (judged && !judgedIds.includes(judged.id)) {
    judgedIds = [...judgedIds, judged.id].slice(-JUDGED_IDS_KEPT)
  }

  return {
    initialized: true,
    platform,
    previous,
    replacedAt,
    judged,
    lit: Boolean(lit),
    lightsSeen: state.lightsSeen || Boolean(lit),
    judgedIds,
    seenResultId: lastResult?.id ?? state.seenResultId,
  }
}

/**
 * Whether the lifter on the platform, called after the page opened, may be introduced now.
 */
export function readyToIntroduce(state, now) {
  if (!state.platform || state.lit || state.replacedAt == null) {
    return false
  }
  const since = now - state.replacedAt
  if (since < INTRO_DELAY_MS) {
    return false
  }
  if (!state.lightsSeen || since >= INTRO_MAX_WAIT_MS) {
    return true
  }
  return !state.previous || state.judgedIds.includes(state.previous.id)
}
