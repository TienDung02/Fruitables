import { createI18n } from 'vue-i18n'
import vi from '@/lang/vi'
import en from '@/lang/en'
import ru from '@/lang/ru'
import jp from '@/lang/jp'

const i18n = createI18n({
    legacy: true,
    locale: 'vi',
    fallbackLocale: 'vi',
    messages: {
        vi,
        en,
        ru,
        jp,
    },
})

export default i18n
