/**
 * IPF GL points (the IPF's formula since 2020): total × 100 / (A − B · e^(−C · bodyweight)), with coefficients per sex,
 * classic or equipped, and powerlifting or bench press.
 *
 * Powerlifting Live publishes the points of each lifter (its 'wilks' field), but always with the powerlifting
 * coefficients, also in bench press competitions, where they come out far too low (182,5 kg at 70,91 kg gives 27,39
 * instead of 99,57). So bench press points are computed here. For powerlifting these coefficients give the same
 * points as Powerlifting Live (checked on a whole competition, to 0,001).
 */
const COEFFICIENTS = {
  powerlifting: {
    m: { classic: [1199.72839, 1025.18162, 0.00921], equipped: [1236.25115, 1449.21864, 0.01644] },
    f: { classic: [610.32796, 1045.59282, 0.03048], equipped: [758.63878, 949.31382, 0.02435] },
  },
  bench: {
    m: { classic: [320.98041, 281.40258, 0.01008], equipped: [381.22073, 733.79378, 0.02398] },
    f: { classic: [142.40398, 442.52671, 0.04724], equipped: [221.82209, 357.00377, 0.02937] },
  },
}

/**
 * The points, or null when something needed is missing (no total yet, an unknown sex or bodyweight).
 *
 * event: 'powerlifting' or 'bench'; sex: 'm' or 'f'; classic: false for equipped.
 */
export function ipfGlPoints({ total, bodyWeight, sex, classic = true, event }) {
  const coefficients = COEFFICIENTS[event]?.[sex]?.[classic ? 'classic' : 'equipped']
  if (!coefficients || !(total > 0) || !(bodyWeight > 0)) {
    return null
  }
  const [a, b, c] = coefficients
  return (total * 100) / (a - b * Math.exp(-c * bodyWeight))
}
