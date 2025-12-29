<template>
    <div class="dropdown language-switcher" ref="dropdown">
        <!-- Nút hiển thị ngôn ngữ hiện tại -->
        <button
            class="btn btn-sm dropdown-toggle bg-primary d-flex align-items-center gap-2 btn-language-switcher"
            @click="toggleDropdown"
        >
            <img
                :src="currentLang.flag"
                class="flag"
                alt=""
            >
            <span class="text-white me-2">{{ currentLang.label }}</span>
        </button>

        <!-- Dropdown -->
        <ul class="dropdown-menu dropdown-menu-end" :class="{ show: isDropdownOpen }">
            <li
                v-for="lang in languages"
                :key="lang.code"
            >
                <button
                    class="dropdown-item d-flex align-items-center gap-2"
                    :class="{ active: lang.code === currentLocale }"
                    @click="selectLang(lang.code)"
                >
                    <img :src="lang.flag" class="flag square" alt="">
                    <span>{{ lang.label }}</span>
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
const dropdown = ref(null)
const isDropdownOpen = ref(false)

const languages = ref([
    { code: 'ru', label: 'RU', flag: '/images/flag/ru.png' },
    { code: 'en', label: 'EN', flag: '/images/flag/en.png' },
    { code: 'jp', label: 'JP', flag: '/images/flag/jp.png' },
    { code: 'vi', label: 'VI', flag: '/images/flag/vi.png' },
])

const currentLocale = computed(() => locale.value)

const currentLang = computed(() => {
    return languages.value.find(l => l.code === currentLocale.value) || languages.value[3] // default to Vietnamese
})

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const selectLang = (langCode) => {
    try {
        // Update i18n locale
        locale.value = langCode

        // Save to localStorage
        if (typeof window !== 'undefined') {
            localStorage.setItem('locale', langCode)
        }

        // Emit custom event for currency system
        if (typeof window !== 'undefined') {
            window.dispatchEvent(new CustomEvent('locale-changed', {
                detail: { locale: langCode }
            }))
        }

        console.log('🌐 Language changed to:', langCode)
    } catch (error) {
        console.error('Error changing language:', error)
    }

    isDropdownOpen.value = false
}

const handleClickOutside = (e) => {
    if (dropdown.value && !dropdown.value.contains(e.target)) {
        isDropdownOpen.value = false
    }
}

onMounted(() => {
    // Initialize locale from storage if different from current
    try {
        const saved = localStorage.getItem('locale')
        if (saved && saved !== locale.value) {
            selectLang(saved)
        }
    } catch (e) {
        console.warn('Could not restore locale from localStorage:', e)
    }

    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.language-switcher {
    position: relative;
    margin-right: 2rem;
}

.language-switcher .flag {
    width: 20px;
    height: 20px;
    border-radius: 100%;
    border: 1px solid #666;
    object-fit: cover;
}

.language-switcher .dropdown-menu {
    min-width: 72px;
    padding: 0;
    margin-top: 1rem;
}

.language-switcher li:hover, .language-switcher button:hover {
    border: 1px solid #81c408;
    border-radius: 10px;
    background: #fff !important;
    color: #81c408;
    font-weight: bolder;
    text-shadow: 1px 1px 1px #ffb524;
}
.language-switcher li button{
    padding: 0.25rem;
    justify-content: center;
}

.language-switcher li, .language-switcher button {
    border-radius: 10px;
}
.btn-language-switcher:hover > .text-white{
    color: #8bc34a !important;
}

.language-switcher .dropdown-item.active {
    background-color: #81c408 !important;
    font-weight: 600;
    color: #fff !important;
}
</style>
