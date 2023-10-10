import Vue from 'vue/dist/vue.esm.js'
import VModal from 'vue-js-modal'
import VTooltip from 'v-tooltip'
import axios from 'axios'

import Link from './components/Link.vue'
import News from './components/News.vue'
import Login from './components/Login.vue'
import Event from './components/Event.vue'
import Header from './components/Header.vue'
import Events from './components/Events.vue'
import Profile from './components/Profile.vue'
import Records from './components/Records.vue'
import AdminNews from './components/AdminNews.vue'
import Documents from './components/Documents.vue'
import EventCard from './components/EventCard.vue'
import MusicAdmin from './components/MusicAdmin.vue'
import Navigation from './components/Navigation.vue'
import ActionCard from './components/ActionCard.vue'
import Agreements from './components/Agreements.vue'
import AdminEvent from './components/AdminEvent.vue'
import AdminEvents from './components/AdminEvents.vue'
import Competition from './components/Competition.vue'
import AdminResults from './components/AdminResults.vue'
import Competitions from './components/Competitions.vue'
import AdminAccounts from './components/AdminAccounts.vue'
import MusicHelpLive from './components/MusicHelpLive.vue'
import AdminNewsEmail from './components/AdminNewsEmail.vue'
import AdminDocuments from './components/AdminDocuments.vue'
import CompetitionCard from './components/CompetitionCard.vue'
import AdminCompetition from './components/AdminCompetition.vue'
import AdminCompetitions from './components/AdminCompetitions.vue'
import LagSM from './components/LagSM.vue'

window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.firebase.initializeApp({
  apiKey: 'AIzaSyCe-FSgSLultZJpVCVJwXOKDGrY31IFZ4E', // firebaseConfig apiKey is not a secret: https://stackoverflow.com/questions/37482366/is-it-safe-to-expose-firebase-apikey-to-the-public, https://firebase.google.com/docs/web/setup
  authDomain: 'goteborg-kraftsportklubb.firebaseapp.com',
  databaseURL: 'https://goteborg-kraftsportklubb.firebaseio.com',
  projectId: 'goteborg-kraftsportklubb',
  storageBucket: 'goteborg-kraftsportklubb.appspot.com',
  messagingSenderId: '52915573324',
  appId: '1:52915573324:web:2d7620cec6430501c35ae9',
  measurementId: 'G-B8J19RFZ1R',
})

if (typeof window.firebase.analytics === 'function') {
  window.firebase.analytics()
}

window.Vue = Vue
Vue.config.ignoredElements = ['trix-editor']
Vue.use(VModal)
Vue.use(VTooltip)

Vue.mixin({
  methods: {
    location(url) {
      window.location = url
    },
    reload() {
      window.location.reload()
    },
    log(text) {
      console.log(text)
    },
  },
})

Vue.component('GkkLink', Link)
Vue.component('GkkNews', News)
Vue.component('GkkLogin', Login)
Vue.component('GkkEvent', Event)
Vue.component('GkkHeader', Header)
Vue.component('GkkEvents', Events)
Vue.component('GkkProfile', Profile)
Vue.component('GkkRecords', Records)
Vue.component('GkkAdminNews', AdminNews)
Vue.component('GkkDocuments', Documents)
Vue.component('GkkEventCard', EventCard)
Vue.component('GkkNavigation', Navigation)
Vue.component('GkkActionCard', ActionCard)
Vue.component('GkkAdminEvent', AdminEvent)
Vue.component('GkkAgreements', Agreements)
Vue.component('GkkMusicAdmin', MusicAdmin)
Vue.component('GkkAdminEvents', AdminEvents)
Vue.component('GkkCompetition', Competition)
Vue.component('GkkAdminResults', AdminResults)
Vue.component('GkkCompetitions', Competitions)
Vue.component('GkkAdminAccounts', AdminAccounts)
Vue.component('GkkMusicHelpLive', MusicHelpLive)
Vue.component('GkkAdminDocuments', AdminDocuments)
Vue.component('GkkAdminNewsEmail', AdminNewsEmail)
Vue.component('GkkCompetitionCard', CompetitionCard)
Vue.component('GkkAdminCompetition', AdminCompetition)
Vue.component('GkkAdminCompetitions', AdminCompetitions)
Vue.component('LagSM', LagSM)

// eslint-disable-next-line no-new
new Vue({
  el: '#app',
})
