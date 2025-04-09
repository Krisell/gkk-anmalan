import { createApp } from 'vue'

import ToastPlugin from 'vue-toast-notification'
import FloatingVue from 'floating-vue'
import 'vue-toast-notification/dist/theme-bootstrap.css'
import axios from 'axios'

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
import Navigation from './components/Navigation.vue'
import Agreements from './components/Agreements.vue'
import AdminEvent from './components/AdminEvent.vue'
import AdminEvents from './components/AdminEvents.vue'
import Competition from './components/Competition.vue'
import AdminResults from './components/AdminResults.vue'
import Competitions from './components/Competitions.vue'
import AdminAccounts from './components/AdminAccounts.vue'
import Slideshow from './components/Slideshow/Slideshow.vue'
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

const app = createApp({
  config: {
    isCustomComponent: tag => tag.startsWith('trix-editor')
  }
});


app.use(ToastPlugin)
app.use(FloatingVue)

import 'floating-vue/dist/style.css'

app.mixin({
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

app.component('GkkNews', News)
app.component('GkkLogin', Login)
app.component('GkkEvent', Event)
app.component('GkkHeader', Header)
app.component('GkkEvents', Events)
app.component('GkkProfile', Profile)
app.component('GkkRecords', Records)
app.component('GkkAdminNews', AdminNews)
app.component('GkkDocuments', Documents)
app.component('GkkEventCard', EventCard)
app.component('GkkSlideshow', Slideshow)
app.component('GkkNavigation', Navigation)
app.component('GkkAdminEvent', AdminEvent)
app.component('GkkAgreements', Agreements)
app.component('GkkAdminEvents', AdminEvents)
app.component('GkkCompetition', Competition)
app.component('GkkAdminResults', AdminResults)
app.component('GkkCompetitions', Competitions)
app.component('GkkAdminAccounts', AdminAccounts)
app.component('GkkAdminDocuments', AdminDocuments)
app.component('GkkAdminNewsEmail', AdminNewsEmail)
app.component('GkkCompetitionCard', CompetitionCard)
app.component('GkkAdminCompetition', AdminCompetition)
app.component('GkkAdminCompetitions', AdminCompetitions)
app.component('LagSM', LagSM)

app.mount('#app')
