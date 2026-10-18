import gkk from './assets/gkk-logo.png'

/**
 * Club crests shown next to a lifter's name. As on TV, a crest means "this lifter's club", so lifters from clubs
 * without a crest here get none (rather than ours). To add a club: put its logo in assets/clubs/, import it here
 * and list the club's names as Powerlifting Live writes them (matched case-insensitively).
 */
const CRESTS = [{ names: ['Göteborg KK', 'Göteborg Kraftsportklubb', 'GKK'], logo: gkk }]

export const BROADCASTER_LOGO = gkk

export function clubCrest(team) {
  const key = normalize(team)
  return key ? (CRESTS.find((crest) => crest.names.some((name) => normalize(name) === key))?.logo ?? null) : null
}

function normalize(name) {
  return String(name ?? '')
    .trim()
    .toLowerCase()
    .replace(/\s+/g, ' ')
}
