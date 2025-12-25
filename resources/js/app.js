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
    resolve: (name) => {
        return resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        );
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(ZiggyVue)
            .use(pinia);

        // Enhanced global $t function that works with both Vue i18n and Inertia props
        app.config.globalProperties.$t = (key, fallback = null) => {
            try {
                // First try Vue i18n
                if (i18n.global.t && typeof i18n.global.t === 'function') {
                    const i18nResult = i18n.global.t(key);
                    if (i18nResult && i18nResult !== key) {
                        return i18nResult;
                    }
                }

                // Fallback to Inertia's shared props if i18n doesn't have the key
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
                console.warn('Translation error for key:', key, e);
                return fallback ?? key;
            }
        };

        // Enhanced error handler for better debugging in dev mode
        app.config.errorHandler = (err, instance, info) => {
            // Only log in development mode
            if (import.meta.env.DEV) {
                console.error('Vue Error:', err);
                console.error('Component:', instance);
                console.error('Error Info:', info);
            }
            return false;
        };

        // Initialize locale from storage after app is mounted
        app.mount(el);

        // Restore locale after mounting to avoid hydration mismatches
        if (typeof window !== 'undefined') {
            const savedLocale = localStorage.getItem('locale');
            if (savedLocale && i18n.global.locale.value !== savedLocale) {
                console.log('🔄 Restoring saved locale:', savedLocale);
                i18n.global.locale.value = savedLocale;

                // Emit event for currency system
                window.dispatchEvent(new CustomEvent('locale-changed', {
                    detail: { locale: savedLocale }
                }));
            }
        }

        return app;
    },
});
