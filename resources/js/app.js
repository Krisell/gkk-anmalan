import Vue from 'vue'
import VModal from 'vue-js-modal'
import VTooltip from 'v-tooltip'

window.axios = require('axios')
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

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./components', false, /\.vue$/i)
files.keys().forEach((key) => {
  Vue.component('Gkk' + key.split('/').pop().split('.')[0], files(key).default)
})

// eslint-disable-next-line no-new
new Vue({
  el: '#app',
})
