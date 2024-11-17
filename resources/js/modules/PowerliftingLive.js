import File from './File.js'

const PowerliftingLive = {
  download(lifters) {
    let csv =
      'Licens;Kategori;Viktklass;Förnamn;Efternamn;Förening;Lott;Grupp\n'

    lifters.forEach((lifter) => {
      if (Number(lifter.status) === 0) {
        return
      }

      let weightClass = String(lifter.weight_class).includes('+') ? lifter.weight_class : `-${lifter.weight_class}`

      // Lifters that haven't declared a weight class are temporarily placed in the highest weight class
      if (weightClass.includes('ツ')) {
        weightClass = (lifter.gender === 'Män') ? '120+' : '84+'
      }

      csv += `${lifter.licence_number};Senior;${weightClass};${lifter.user.first_name};${lifter.user.last_name};Göteborg KK;;1\n`;
    })

    File.save('powerlifting-live-import.csv', csv, 'data:text/csv;charset=utf-8')
  },
}

export default PowerliftingLive
