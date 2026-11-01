/**
 * The district championships in classic powerlifting 2026 (DM), hosted by GKK on Saturday 14 November, for the
 * welcome slide (Dm2026Slide.vue), which is only shown when held from the live control. From the start lists published on /dm2026 ("Startlistor DM SL 2026", updated
 * 29.10.2026 15:11): when the lists change, update the lifters here and the slide's numbers follow.
 */

export const DM2026_DATE = '2026-11-14'
export const HOME_CLUB = 'Göteborg KK'

// The three sessions of the day, each with two groups. Each lifter is [name, club].
export const SESSIONS = [
  {
    groups: '1–2',
    weighIn: '07:30',
    start: '09:30',
    description: 'Herrar · ungdom, junior, senior 74–93',
    lifters: [
      ['Vide Kvarnbom', 'Göteborgs SK'],
      ['Matheus Berghede', 'Föreningen Pure Power'],
      ['Edvin Frenning', 'Skövde KK'],
      ['Melker Wahlfors', 'Göteborgs SK'],
      ['Oscar Lagerstedt', 'Varbergs Kraftsportsklubb'],
      ['Jesper Eriksson', 'Borås AK'],
      ['Emil Gladh', 'Föreningen Mics Gym'],
      ['Melker Petersson', 'Göteborg KK'],
      ['Jesper Skoog', 'Lysekils KK'],
      ['Nils Weihard', 'Vedums AIS'],
      ['Leo Stålberg', 'Göteborg KK'],
      ['Liam Bodén', 'Vedums AIS'],
      ['Ragnar Kvillberg', 'Lysekils KK'],
      ['Linus Samuelsson', 'Skövde KK'],
      ['Tomas Pettersson', 'Vedums AIS'],
      ['Jonathan Ask', 'Borås AK'],
      ['Fredric Markarström', 'Kungälvs Atletklubb'],
    ],
  },
  {
    groups: '3–4',
    weighIn: '11:00',
    start: '13:00',
    description: 'Damer · alla klasser',
    lifters: [
      ['Vilma Helge', 'Borås AK'],
      ['Hannah Forssblad', 'Föreningen Pure Power'],
      ['Hanna Karlsson', 'Göteborgs SK'],
      ['Ann Jordebo', 'Göteborg KK'],
      ['Louise Liljeqvist', 'Kungälvs Atletklubb'],
      ['Camilla Swensson', 'Falkenbergs KSK'],
      ['Janina Berggren', 'Göteborg KK'],
      ['Moa Larsson', 'Föreningen Pure Power'],
      ['Robin Ferm', 'Kungälvs Atletklubb'],
      ['Lale Avanesian', 'Göteborg KK'],
      ['Vanessa Becker', 'Föreningen Pure Power'],
      ['Ellinor Hermansson', 'Göteborg KK'],
      ['Stella Strindberg', 'Göteborg KK'],
      ['Madelen Ohlsson', 'Borås AK'],
      ['Annemarie Ammer', 'Göteborgs SK'],
      ['Carin Isacson', 'Göteborg KK'],
      ['Tilda Silver', 'Göteborg KK'],
      ['Torill Fonn', 'Vedums AIS'],
      ['Linda Bengtsson', 'Göteborg KK'],
      ['Sara Andersson', 'Borås AK'],
      ['Carolina Skeppstedt', 'Borås AK'],
    ],
  },
  {
    groups: '5–6',
    weighIn: '15:00',
    start: '17:00',
    description: 'Herrar · senior 83–120+',
    lifters: [
      ['Harry Lans', 'Skövde KK'],
      ['Ulf Corneliusson', 'Göteborg KK'],
      ['Johannes Bjärehall', 'Borås AK'],
      ['Mikael Karlsson', 'Sommarhemmets Kraftsport'],
      ['Fredrik Hilmertz', 'Göteborg KK'],
      ['Börje Lindh', 'Falkenbergs KSK'],
      ['Ludovic Bêche', 'Skövde KK'],
      ['Marek Hom', 'Göteborgs SK'],
      ['Steffan Martin', 'Borås AK'],
      ['Oskar Kullner', 'Föreningen Pure Power'],
      ['David Tåhlin', 'Göteborgs SK'],
      ['Teo Person', 'KK Eleiko'],
      ['Edvin Brovik', 'Vedums AIS'],
      ['Abdelmajed Swan', 'Göteborgs SK'],
      ['Karl Hermansson', 'Vedums AIS'],
      ['Per Tillqvist', 'Varbergs Kraftsportsklubb'],
      ['John Kumar', 'Göteborg KK'],
      ['Leo Ottosson Möllborg', 'Sommarhemmets Kraftsport'],
    ],
  },
]

// How long the last session is expected to last, for showing it as under way.
const LAST_SESSION_HOURS = 4

export function summary(sessions = SESSIONS) {
  const lifters = sessions.flatMap((session) => session.lifters)
  return {
    lifters: lifters.length,
    clubs: new Set(lifters.map(([, club]) => club)).size,
    homeLifters: lifters.filter(([, club]) => club === HOME_CLUB).length,
    groups: sessions.length * 2,
  }
}

// The local date as YYYY-MM-DD.
function localDate(now) {
  const pad = (value) => String(value).padStart(2, '0')
  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`
}

/**
 * What each session is doing at `now`, on the competition day only: 'weighIn' (weigh-in under way), 'live' (lifting),
 * 'done', or null (later, or not the competition day).
 */
export function sessionStates(now = new Date(), sessions = SESSIONS) {
  if (localDate(now) !== DM2026_DATE) {
    return sessions.map(() => null)
  }
  const minutes = now.getHours() * 60 + now.getMinutes()
  const at = (time) => {
    const [hours, mins] = time.split(':').map(Number)
    return hours * 60 + mins
  }

  return sessions.map((session, index) => {
    const end = sessions[index + 1] ? at(sessions[index + 1].start) : at(session.start) + LAST_SESSION_HOURS * 60
    if (minutes >= end) {
      return 'done'
    }
    if (minutes >= at(session.start)) {
      return 'live'
    }
    if (minutes >= at(session.weighIn)) {
      return 'weighIn'
    }
    return null
  })
}
