require('./bootstrap');

window.Vue = require('vue');


Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('action-card', require('./components/ActionCard.vue').default);

const app = new Vue({
    el: '#app',
});
