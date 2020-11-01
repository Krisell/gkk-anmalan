import moment from 'moment'

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

    return `${day}, ${moment(date).date()} ${moment(date).locale('sv').format('MMMM')}`
  },
  withinAYear(date) {
    return moment(new window.Date()).diff(moment(date), 'days') < 365
  },
}

export default Date
