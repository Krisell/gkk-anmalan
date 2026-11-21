import moment from 'moment'

// Moment's Swedish locale is not bundled, so .locale('sv') silently falls back to English.
const MONTHS = [
  'januari',
  'februari',
  'mars',
  'april',
  'maj',
  'juni',
  'juli',
  'augusti',
  'september',
  'oktober',
  'november',
  'december',
]

const Date = {
  string(date) {
    if (!date) {
      return 'Datum ännu ej bestämt'
    }

    let day = {
      1: 'Måndag',
      2: 'Tisdag',
      3: 'Onsdag',
      4: 'Torsdag',
      5: 'Fredag',
      6: 'Lördag',
      7: 'Söndag',
    }[moment(date).isoWeekday()]

    return `${day}, ${moment(date).date()} ${Date.month(date)}`
  },
  month(date) {
    return MONTHS[moment(date).month()]
  },
  withinAYear(date) {
    return moment(new window.Date()).diff(moment(date), 'days') < 365
  },
}

export default Date
