require('./bootstrap')

window.Vue = require('vue')


Vue.component('navigation', require('./components/Navigation.vue').default)
Vue.component('action-card', require('./components/ActionCard.vue').default)

const app = new Vue({
    el: '#app',
})
