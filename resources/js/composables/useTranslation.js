import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTranslation() {
    const page = usePage();

    // Inertia exposes props as a reactive object directly
    const props = computed(() => page.props || {});

    const locale = computed(() => props.value.locale || 'en');

    const translations = computed(() => {
        const t = props.value.translations;
        return t && typeof t === 'object' ? t : {};
    });

    const t = (key, fallback = null) => {
        const segments = String(key).split('.');
        let current = translations.value;
        for (const seg of segments) {
            if (current && typeof current === 'object' && Object.prototype.hasOwnProperty.call(current, seg)) {
                current = current[seg];
            } else {
                return fallback ?? key;
            }
        }
        return current ?? fallback ?? key;
    };

    const switchLanguage = (newLocale) => {
        window.location.href = `/language/switch?locale=${encodeURIComponent(newLocale)}`;
    };

    return { locale, translations, t, switchLanguage };
}
