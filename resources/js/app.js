import Vue from 'vue'
import VModal from 'vue-js-modal'

require('./bootstrap')

window.Vue = Vue
Vue.use(VModal)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

let files = require.context('./components', false, /\.vue$/i)
files.keys().map(key => {
    console.log('registering', 'Gkk' + key.split('/').pop().split('.')[0])
    Vue.component('Gkk' + key.split('/').pop().split('.')[0], files(key).default)
})

files = require.context('./components/admin', false, /\.vue$/i)
files.keys().map(key => {
    console.log('registering', 'Gkk' + key.split('/').pop().split('.')[0])
    Vue.component('GkkAdmin' + key.split('/').pop().split('.')[0], files(key).default)
})

const app = new Vue({
    el: '#app',
})
