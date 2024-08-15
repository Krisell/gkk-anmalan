import File from './File.js'
import Parser from '@krisell/parser'

const PowerliftingLive = {
  download(lifters) {
    let csv =
      'Licens;Kategori;Viktklass;Förnamn;Efternamn;Förening;Lott;Grupp\n'

    lifters.forEach((lifter) => {
      if (Number(lifter.status) === 0) {
        return
      }

      const weightClass = String(lifter.weight_class).includes('+') ? lifter.weight_class : `-${lifter.weight_class}`

      csv += `${lifter.licence_number};Senior;${weightClass};${lifter.user.first_name};${lifter.user.last_name};Göteborg KK;;Grupp 1\n`;
    })

    File.save('powerlifting-live-import.csv', csv, 'data:text/csv;charset=utf-8')
  },
}

export default PowerliftingLive
