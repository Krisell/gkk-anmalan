import Vue from 'vue'
import VModal from 'vue-js-modal'
import VTooltip from 'v-tooltip'
import ToggleButton from 'vue-js-toggle-button'

require('./bootstrap')

window.Vue = Vue
Vue.config.ignoredElements = ['trix-editor']
Vue.use(VModal)
Vue.use(VTooltip)
Vue.use(ToggleButton)

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

let files = require.context('./components', false, /\.vue$/i)
files.keys().map((key) => {
  Vue.component('Gkk' + key.split('/').pop().split('.')[0], files(key).default)
})

files = require.context('./components/ui', false, /\.vue$/i)
files.keys().map((key) => {
  Vue.component('ui' + key.split('/').pop().split('.')[0], files(key).default)
})

const app = new Vue({
  el: '#app',
})
