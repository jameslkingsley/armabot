require('./bootstrap')
require('./filters')

Vue.component('grid', require('./components/Grid.vue'))
Vue.component('grid-child', require('./components/GridChild.vue'))

import App from './components/App.vue'

const app = new Vue({
    el: '#app',

    render: h => h(App)
})
