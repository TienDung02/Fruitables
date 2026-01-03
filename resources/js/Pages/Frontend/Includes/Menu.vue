<template>
    <!-- Navbar start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="https://www.google.com/maps/place/K%C3%BD+t%C3%BAc+x%C3%A1+Khu+A+%C4%90H+Qu%E1%BB%91c+gia+TP.+H%E1%BB%93+Ch%C3%AD+Minh/@10.8782516,106.8037156,17z/data=!3m1!4b1!4m6!3m5!1s0x3174d8a5f4e477e9:0x29d5aeb365cee20b!8m2!3d10.8782463!4d106.8062905!16s%2Fg%2F1tncqcyc?authuser=0&entry=ttu&g_ep=EgoyMDI1MTIwOS4wIKXMDSoKLDEwMDc5MjA3M0gBUAM%3D" class="text-white">{{ $t('messages.dormitory_area_hcmu') }}</a></small>
                    <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">nongtiendung2309@gmail.com</a></small>
                </div>
                <div class="d-flex align-items-center">
                    <!-- Language Switcher -->
                    <div class="me-3">
                        <LanguageSwitcher />
                    </div>
                    <div v-if="!authStore.isLoggedIn">
                        <Link :href="route('login')" class="signup-link text-white mx-2 hover:text-gray-900">
                            {{ $t('messages.login') }}
                        </Link>
                        /
                        <Link :href="route('register')" class="signup-link text-white mx-2 hover:text-gray-900">
                            {{ $t('messages.register') }}
                        </Link>
                    </div>
                    <div v-else>
                        {{ authStore.user.full_name  }}
                    </div>
                </div>
            </div>
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <Link :href="route('dashboard')" >
                    <a><h1 class="text-primary display-6">Fruitables</h1></a>
                </Link>

                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                    <div class="navbar-nav mx-auto">
                        <Link :href="route('dashboard')" :class="['nav-item', 'nav-link', { active: isHome }]">{{ $t('messages.home') }}</Link>
                        <Link :href="route('products.index')" :class="['nav-item', 'nav-link', { active: isShop }]">{{ $t('messages.shop') }}</Link>
                        <Link :href="route('contact.index')" :class="['nav-item', 'nav-link', { active: isContact }]">{{ $t('messages.contact') }}</Link>
                        <Link :href="route('about.index')" :class="['nav-item', 'nav-link', { active: isAbout }]">{{ $t('messages.about') }}</Link>
                    </div>
                    <div class="d-flex m-3 me-0">
                        <a href="#" class="position-relative me-4 my-auto">
                            <Link :href="route('cart.index')">
                                <i class="fa fa-shopping-bag fa-2x"></i>
                            </Link>
                            <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">{{ cartStore?.count || 0 }}</span>
                        </a>
                        <div class="dropdown" @click.stop>
                            <a href="#" class="my-auto dropdown-toggle" @click.prevent="toggleDropdown">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" :class="{ show: isDropdownOpen }">
                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <Link :href="route('cart.index')" class="dropdown-item">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        {{ $t('messages.cart') }}
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('wishlist.index')" class="dropdown-item">
                                        <i class="fas fa-heart me-2"></i>
                                        {{ $t('messages.wishlist') }}
                                    </Link>
                                </li>
                                <li v-if="authStore.isLoggedIn"><hr class="dropdown-divider"></li>
                                <li v-if="authStore.isLoggedIn">
                                    <Link :href="route('profile.index')" class="dropdown-item">
                                        <i class="fas fa-user me-2"></i>
                                        {{ $t('messages.profile') }}
                                    </Link>
                                </li>
                                <li v-if="authStore.isLoggedIn">
                                    <button
                                        @click="handleLogout"
                                        class="dropdown-item text-danger"
                                        style="border: none; background: none; width: 100%; text-align: left;"
                                    >
                                        <i class="fas fa-sign-out-alt me-2"></i> {{ $t('messages.logout') }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->
</template>

<script>
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { useCartStore } from '@/stores/cart';
import { useAuthStore } from '@/stores/auth';
import { useTranslation } from '@/composables/useTranslation';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { useI18n } from 'vue-i18n'
export default {
    name: 'Menu',
    components: {
        Link,
        LanguageSwitcher,
    },
    setup() {
        // remove local t, rely on global $t
        return {};
    },
    data() {
        return {
            isDropdownOpen: false
        };
    },
    computed: {
        authStore() {
            return useAuthStore();
        },
        cartStore() {
            return useCartStore();
        },
        flashMessages() {
            return this.$page.props.flash || {};
        },
        // Thêm computed property để xác định menu active
        currentRoute() {
            console.log('Current route:', this.$page.component);
            return this.$page.component;
        },
        isHome() {
            return this.currentRoute === 'Dashboard' || this.currentRoute === 'Frontend/Dashboard/Index';
        },
        isShop() {
            return this.currentRoute?.includes('Product')
                || this.currentRoute?.includes('Frontend/Products/Index')
                || this.currentRoute?.includes('Cart')
                || this.currentRoute?.includes('Frontend/Cart/Index')
                || this.currentRoute?.includes('Checkout')
                || this.currentRoute?.includes('Frontend/Checkout/Index')
                || this.currentRoute?.includes('Wishlist')
                || this.currentRoute?.includes('Frontend/Wishlist/Index');
        },
        isContact() {
            return this.currentRoute?.includes('Contact') || this.currentRoute?.includes('Frontend/Contact/Index');
        },
        isAbout() {
            return this.currentRoute?.includes('About') || this.currentRoute?.includes('Frontend/About/Index');
        }
    },
    watch: {
        // Watch for authentication changes to refresh cart count
        'authStore.isLoggedIn': {
            handler(newVal, oldVal) {
                // Khi logout (từ true -> false) hoặc login (từ false -> true)
                if (newVal !== oldVal) {
                    // Đợi một chút để backend hoàn thành việc lưu session
                    setTimeout(async () => {
                        await this.cartStore.fetchCartCount();
                        this.$forceUpdate(); // Force component update
                    }, 150);
                }
            },
            immediate: true
        },
        // Watch for flash messages to trigger menu reload
        flashMessages: {
            handler(newMessages) {
                if (newMessages?.reload_menu || newMessages?.logout_success) {
                    console.log('Reload menu signal detected!');
                    // Force reload cart count sau khi logout
                    setTimeout(async () => {
                        console.log('Reloading menu after logout...');
                        await this.authStore.checkAuth(); // Re-check auth status
                        await this.cartStore.fetchCartCount();
                        this.$forceUpdate(); // Force component update
                    }, 100);
                }
            },
            deep: true,
            immediate: true
        },
        // Watch for page props changes (detect navigation after logout)
        '$page.props': {
            handler(newProps, oldProps) {
                // If we detect logout success, reload immediately
                if (newProps?.flash?.logout_success && !oldProps?.flash?.logout_success) {
                    console.log('Logout success detected via page props!');
                    setTimeout(async () => {
                        await this.authStore.checkAuth();
                        await this.cartStore.fetchCartCount();
                        this.$forceUpdate();
                    }, 50);
                }
            },
            deep: true
        }
    },
    methods: {
        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen;
        },
        closeDropdown() {
            this.isDropdownOpen = false;
        },
        // Đóng dropdown khi click ra ngoài
        handleClickOutside(event) {
            if (!event.target.closest('.dropdown')) {
                this.closeDropdown();
            }
        },
        async handleLogout() {
            try {

                // Đóng dropdown trước
                this.closeDropdown();

                // Cập nhật auth store ngay lập tức
                this.authStore.logout();

                // Gọi logout API bằng Inertia
                this.$inertia.post(route('logout'), {}, {
                    preserveState: false,
                    preserveScroll: false,
                    onSuccess: () => {
                        console.log('Logout successful, reloading menu...');
                        // Force reload sau khi logout thành công
                        setTimeout(async () => {
                            await this.authStore.checkAuth();
                            await this.cartStore.fetchCartCount();
                            this.$forceUpdate();
                        }, 100);
                    },
                    onError: (errors) => {
                        console.error('Logout error:', errors);
                    }
                });

            } catch (error) {
                console.error('Logout process error:', error);
            }
        }
    },
    async mounted() {
        document.addEventListener('click', this.handleClickOutside);

        // Khởi tạo auth store trước
        await this.authStore.checkAuth();



        // Fetch cart count khi component được mount
        await this.cartStore.fetchCartCount();

        // Kiểm tra nếu có flash message reload_menu ngay khi mount
        if (this.flashMessages?.reload_menu) {
            setTimeout(async () => {
                console.log('Initial menu reload detected...');
                await this.cartStore.fetchCartCount();
            }, 100);
        }
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    }
};
</script>

<style lang="scss" scoped>

</style>
