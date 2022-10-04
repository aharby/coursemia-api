import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import App from './App'
import Timetable from './components/Timetable'
//import VuetifyDialog from 'vuetify-dialog'
//import 'vuetify-dialog/dist/vuetify-dialog.css'

import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css'
import 'vuetify/dist/vuetify.min.js'
import VueInternationalization from 'vue-i18n';
import Locales from '../../js/vue-i18n-locales.generated';
Vue.use(VueInternationalization);

Vue.use(Vuetify);


// Object.keys(Locales).forEach(function (lang) {
//     Vue.locale(lang, Locales[lang])
//     //console.log(Locales[lang]);
// });
//const lang = localStorage.getItem('locale') || 'en';
const lang = document.querySelector('meta[name="app_local"]').getAttribute('content');
console.log(lang);
const i18n = new VueInternationalization({
    locale: lang,
    messages: Locales
});
const routesWithPrefix = (prefix, routes) => {
    return routes.map(route => {
        route.path = `${prefix}${route.path}`

        return route
    })
}
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/:locale/school-branch-supervisor/spa/class/:id/timetable',
            name: 'Timetable',
            component: Timetable
        },
    ],
});

const app = new Vue({
    el: '#app',
    i18n,
    router,               // <-- register router with Vue
    vuetify: new Vuetify(),
    render: (h) => h(App) // <-- render App component
});

import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ":" + window.laravel_echo_port
});
