import File from './File.js'
import Parser from '@krisell/parser'

const LiftingCast = {
  download(lifters) {
    let csv =
      '"name","team","lot","platform","session","flight","birthDate","memberNumber","gender","rawOrEquipped","division","declaredAwardsWeightClass","bodyWeight","squatRackHeight","benchRackHeight","squat1","bench1","dead1","wasDrugTested","phoneNumber","streetAddress","city","state","zipCode","email","emergencyContactName","emergencyContactPhoneNumber","additionalItems"\n'

    // "Martin","GKK",,"1","1","A","1987/08/19","","Male","Raw","Män","Open",,"","",,,,"N","","","",,"","","","",""
    lifters.forEach((lifter) => {
      if (Number(lifter.status) === 0) {
        return
      }

      const flight = lifter.gender === 'Män' ? 'B' : 'A'
      const gender = lifter.gender === 'Män' ? 'Male' : 'Female'

      const birthdate = LiftingCast.getBirthdate(lifter.licence_number)

      const events = Parser.json(lifter.events)

      let division = events.kbp ? 'BP' : 'SL'
      division += lifter.gender === 'Män' ? ' Herr' : ' Dam'

      csv += `"${lifter.user.first_name} ${lifter.user.last_name}","GKK",,"1","1","${flight}","${birthdate}","","${gender}","Raw","${division}","Open",,"","",,,,"N","","","",,"","","","",""\n`
    })

    File.save('liftingcast-import.csv', csv, 'data:text/csv;charset=utf-8')
  },
  getBirthdate(licenceNumber) {
    if (!licenceNumber) {
      return ''
    }

    let [_, Y, M, D] = licenceNumber.match(/(\d\d)(\d\d)(\d\d).*/)
    if (Y < 30) {
      return `20${Y}/${M}/${D}`
    }

    return `19${Y}/${M}/${D}`
  },
}

export default LiftingCast
