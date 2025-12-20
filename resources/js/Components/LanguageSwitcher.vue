<template>
    <div class="dropdown language-switcher" ref="dropdown">
        <!-- Nút hiển thị ngôn ngữ hiện tại -->
        <button
            class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2"
            @click="toggleDropdown"
        >
            <img
                :src="currentLang.flag"
                class="flag"
                alt=""
            >
            <span class="text-white">{{ currentLang.label }}</span>
        </button>

        <!-- Dropdown -->
        <ul class="dropdown-menu dropdown-menu-end" :class="{ show: isDropdownOpen }">
            <li
                v-for="lang in languages"
                :key="lang.code"
            >
                <button
                    class="dropdown-item d-flex align-items-center gap-2"
                    :class="{ active: lang.code === $i18n.locale }"
                    @click="selectLang(lang.code)"
                >
                    <img :src="lang.flag" class="flag square" alt="">
                    <span>{{ lang.label }}</span>
                </button>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    name: 'LanguageSwitcher',

    data() {
        return {
            isDropdownOpen: false,
            languages: [
                { code: 'ru', label: 'RU', flag: '/images/flag/ru.png' },
                { code: 'en', label: 'EN', flag: '/images/flag/en.png' },
                { code: 'jp', label: 'JP', flag: '/images/flag/jp.png' },
                { code: 'vi', label: 'VI', flag: '/images/flag/vi.png' },
            ],
        }
    },

    computed: {
        currentLang() {
            return (
                this.languages.find(
                    l => l.code === this.$i18n.locale
                ) || this.languages[0]
            )
        },
    },

    methods: {
        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen
        },

        selectLang(lang) {
            this.$i18n.locale = lang
            localStorage.setItem('locale', lang)
            this.isDropdownOpen = false
        },

        handleClickOutside(e) {
            if (!this.$refs.dropdown.contains(e.target)) {
                this.isDropdownOpen = false
            }
        },
    },

    mounted() {
        const saved = localStorage.getItem('locale')
        if (saved) {
            this.$i18n.locale = saved
        }

        document.addEventListener('click', this.handleClickOutside)
    },

    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside)
    },
}

</script>
<style>
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
    border-radius: 10px ;
    background: #fff;
    color: #81c408;
    font-weight: bolder;
    text-shadow: 1px 1px 1px #ffb524;
}
.language-switcher li, .language-switcher button {
    border-radius: 10px ;
}

.language-switcher .dropdown-item.active {
    background-color: #81c408 !important;
    font-weight: 600;
    color: #fff !important;
}
</style>
