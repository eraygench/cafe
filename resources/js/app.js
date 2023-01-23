import './bootstrap';
import '../css/app.css';

import Vue from 'vue'
import VueSweetalert2 from 'vue-sweetalert2';
import Layout from "@/Shared/Layout.vue";
import { createInertiaApp, Link, Head } from '@inertiajs/vue2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { ZiggyVue } from 'ziggy-js/dist/vue';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher/*new Pusher('23350e9fcf4a7f58a717', {
    cluster: 'eu'
})*/;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

/*window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '23350e9fcf4a7f58a717',
    cluster: 'eu',
    encrypted: true,
    enabledTransports: ['ws', 'wss'],
});*/

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        let page = pages[`./Pages/${name}.vue`]
        if (page.default.layout === undefined /*&& !name.startsWith('Public/')*/) {
            page.default.layout = Layout
        }
        return page
    },
    setup({ el, App, props, plugin }) {
        Vue.use(plugin)
        Vue.use(VueSweetalert2);
        Vue.use(ZiggyVue);
        Vue.component('Link', Link)
        Vue.component('Head', Head)
        new Vue({
            render: h => h(App, props),
        }).$mount(el)
    },
    title: title => `Site - ${title}`,
})
