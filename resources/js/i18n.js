import { createI18n } from 'vue-i18n'
import vi from '@/lang/vi'
import en from '@/lang/en'
import ru from '@/lang/ru'
import jp from '@/lang/jp'

// Safe localStorage access for SSR/dev mode
const getStoredLocale = () => {
    try {
        if (typeof window !== 'undefined') {
            return localStorage.getItem('locale') || 'vi'
        }
    } catch (e) {
        console.warn('localStorage not available:', e)
    }
    return 'vi'
}

const setStoredLocale = (locale) => {
    try {
        if (typeof window !== 'undefined') {
            localStorage.setItem('locale', locale)
        }
    } catch (e) {
        console.warn('Cannot save to localStorage:', e)
    }
}

const i18n = createI18n({
    legacy: false, // Use Composition API
    locale: getStoredLocale(),
    fallbackLocale: 'vi',
    messages: {
        vi,
        en,
        ru,
        jp,
    },
})

// Enhanced locale change handler that works with dev mode
const handleLocaleChange = (newLocale) => {
    console.log('🌐 Locale changing to:', newLocale)

    // Update i18n locale
    i18n.global.locale.value = newLocale

    // Save to localStorage
    setStoredLocale(newLocale)

    // Emit event for currency system
    if (typeof window !== 'undefined') {
        window.dispatchEvent(new CustomEvent('locale-changed', {
            detail: { locale: newLocale }
        }))
    }
}

// Expose locale change function globally for LanguageSwitcher
if (typeof window !== 'undefined') {
    window.changeLocale = handleLocaleChange
}

export default i18n
export { handleLocaleChange }
