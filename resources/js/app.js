import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import vuetify from "./vuetify";
import { createI18n } from 'vue-i18n';

const appName = window.document.getElementsByTagName('title')[0]?.innerText;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Ensure translations are correctly structured
        const messages = props.initialPage.props.translations || {};
        const locale = props.initialPage.props.locale || 'id';

        // Configure i18n
        const i18n = createI18n({
            locale: locale,
            fallbackLocale: 'id',
            messages: {
                [locale]: messages
            }
        });

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
