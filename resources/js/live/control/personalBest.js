/**
 * The attempt on the platform against the lifter's best registered results (/live/lifter-bests, from the federation's
 * results database), for the live control: would it be a personal best? Today's competition isn't registered yet, so
 * the total is compared as it would be if the attempt is good: the best good lifts so far plus this one.
 */

const LIFTS = { SQ: 'squat', BP: 'bench', DL: 'deadlift' }

/**
 * @param current the snapshot's current attempt (normalizeFeed): discipline, attempt, weight, lifts
 * @param bests the bests by lift ('squat', 'bench', 'deadlift', 'total'): { value, date, competition } or null
 * @returns [{ key, label, weight, best, verdict: 'pb' | 'equal' | 'below' | 'first' }]
 */
export function compareWithBests(current, bests) {
  const lift = LIFTS[current?.discipline]
  if (!lift || current.weight == null || !bests) {
    return []
  }
  const rows = [row(lift, 'Försöket', current.weight, bests[lift])]

  // Only with all three lifts is there a total: on the deadlift, after a good squat and bench.
  if (current.discipline === 'DL') {
    const squat = bestGood(current.lifts?.SQ)
    const bench = bestGood(current.lifts?.BP)
    if (squat != null && bench != null) {
      rows.push(row('total', 'Total om godkänt', squat + bench + current.weight, bests.total))
    }
  }
  return rows
}

function row(key, label, weight, best) {
  const verdict = best == null ? 'first' : weight > best.value ? 'pb' : weight === best.value ? 'equal' : 'below'
  return { key, label, weight, best, verdict }
}

function bestGood(attempts) {
  const good = (attempts ?? []).filter((attempt) => attempt?.outcome === 'good' && attempt.weight != null)
  return good.length ? Math.max(...good.map((attempt) => attempt.weight)) : null
}
