import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import i18n from './i18n'
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => title,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(ZiggyVue)
            .use(pinia);

        // Global $t using Inertia's shared props
        app.config.globalProperties.$t = (key, fallback = null) => {
            try {
                const pageProps = app.config.globalProperties.$page?.props || {};
                const translations = pageProps.translations || {};
                // Resolve dot-notation
                const segments = String(key).split('.');
                let current = translations;
                for (const seg of segments) {
                    if (current && typeof current === 'object' && Object.prototype.hasOwnProperty.call(current, seg)) {
                        current = current[seg];
                    } else {
                        return fallback ?? key;
                    }
                }
                return current ?? fallback ?? key;
            } catch (e) {
                return fallback ?? key;
            }
        };

        // Optional: basic error handler
        app.config.errorHandler = (err, instance, info) => {
            console.error('Vue Error:', err, info);
            return false;
        };

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
