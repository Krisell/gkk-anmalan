window._ = require('lodash')

try {
  window.Popper = require('popper.js').default
  window.$ = window.jQuery = require('jquery')
} catch (e) {}

window.axios = require('axios')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

firebase.initializeApp({
  apiKey: 'AIzaSyCe-FSgSLultZJpVCVJwXOKDGrY31IFZ4E',
  authDomain: 'goteborg-kraftsportklubb.firebaseapp.com',
  databaseURL: 'https://goteborg-kraftsportklubb.firebaseio.com',
  projectId: 'goteborg-kraftsportklubb',
  storageBucket: 'goteborg-kraftsportklubb.appspot.com',
  messagingSenderId: '52915573324',
  appId: '1:52915573324:web:2d7620cec6430501c35ae9',
  measurementId: 'G-B8J19RFZ1R',
})

firebase.analytics()
