import LiftingCast from '../modules/LiftingCast.js'

it('Returns the empty string on missing licence number', () => {
  expect(LiftingCast.getBirthdate()).toEqual('')
  expect(LiftingCast.getBirthdate(null)).toEqual('')
  expect(LiftingCast.getBirthdate('')).toEqual('')
  expect(LiftingCast.getBirthdate(undefined)).toEqual('')
  expect(LiftingCast.getBirthdate(false)).toEqual('')
})

it('Creates a LiftingCast compatible bithdate from a licence number', () => {
  expect(LiftingCast.getBirthdate('870819mk')).toEqual('1987/08/19')
  expect(LiftingCast.getBirthdate('910128ea')).toEqual('1991/01/28')
})

it('Considers lifters with year below 30 as being born after 2000', () => {
  expect(LiftingCast.getBirthdate('310304mk')).toEqual('1931/03/04')
  expect(LiftingCast.getBirthdate('300304mk')).toEqual('1930/03/04')
  expect(LiftingCast.getBirthdate('290304mk')).toEqual('2029/03/04')
  expect(LiftingCast.getBirthdate('000304mk')).toEqual('2000/03/04')
  expect(LiftingCast.getBirthdate('040304mk')).toEqual('2004/03/04')
})

it("Doesn't crash on incorrect dates", () => {
  expect(LiftingCast.getBirthdate('870231mk')).toEqual('1987/02/31')
  expect(LiftingCast.getBirthdate('999999mk')).toEqual('1999/99/99')
})
