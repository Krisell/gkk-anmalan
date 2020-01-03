require('./bootstrap')

window.Vue = require('vue')

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => {
    console.log('registering', 'Gkk' + key.split('/').pop().split('.')[0])
    Vue.component('Gkk' + key.split('/').pop().split('.')[0], files(key).default)
})

// Vue.component('gkk-admin', require('./components/Admin.vue').default)
// Vue.component('gkk-admin-organizer-event', require('./components/AdminOrganizerEvent.vue').default)
// Vue.component('gkk-navigation', require('./components/Navigation.vue').default)
// Vue.component('action-card', require('./components/ActionCard.vue').default)

const app = new Vue({
    el: '#app',
})
