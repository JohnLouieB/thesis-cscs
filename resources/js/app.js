import './bootstrap';
import '../css/app.css';
import Antd from 'ant-design-vue';

import 'ant-design-vue/dist/antd.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
// import VueMoment from 'vue-moment'
// import moment from 'moment-timezone'
// import { Link } from '@inertiajs/inertia-vue3'
// import { Inertia } from '@inertiajs/inertia'

// h.prototype.$userId = document.querySelector("meta[name='user_id']").getAttribute('content');

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Antd)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
