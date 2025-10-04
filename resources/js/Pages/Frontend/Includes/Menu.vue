<template>
    <!-- Navbar start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">Dormitory Area A, Ho Chi Minh City National University</a></small>
                    <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">nongtiendung2309@gmail.com</a></small>
                </div>
                <div v-if="!authStore.isLoggedIn">
                    <Link :href="route('login')" class="signup-link text-white mx-2 hover:text-gray-900">
                        Login
                    </Link>
                    /
                    <Link :href="route('register')" class="signup-link text-white mx-2 hover:text-gray-900">
                        Register
                    </Link>
                </div>
                <div v-else>
                    Chào mừng, {{ authStore.user.name }}
                </div>
            </div>
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <a href="index.html" class="navbar-brand"><h1 class="text-primary display-6">Fruitables</h1></a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                    <div class="navbar-nav mx-auto">
                        <Link :href="route('dashboard')" class="nav-item nav-link active">Home</Link>
                        <Link :href="route('products.index')" class="nav-item nav-link">Shop</Link>
                        <a href="contact.html" class="nav-item nav-link">Contact</a>
                    </div>
                    <div class="d-flex m-3 me-0">
                        <a href="#" class="position-relative me-4 my-auto">
                            <i class="fa fa-shopping-bag fa-2x"></i>
                            <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">{{ cartStore?.count || 0 }}</span>
                        </a>
                        <div class="dropdown" @click.stop>
                            <a href="#" class="my-auto dropdown-toggle" @click.prevent="toggleDropdown">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" :class="{ show: isDropdownOpen }">
                                <!--                                <li><Link class="dropdown-item"><i class="fas fa-user-edit me-2"></i>Profile</Link></li>-->
                                <!--                                <li><Link  class="dropdown-item"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</Link></li>-->
                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <Link :href="route('cart.index')" class="dropdown-item">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Cart
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('wishlist.index')" class="dropdown-item">
                                        <i class="fas fa-heart me-2"></i>
                                        Wishlist
                                    </Link>
                                </li>
                                <li v-if="authStore.isLoggedIn"><hr class="dropdown-divider"></li>
                                <li v-if="authStore.isLoggedIn">
                                    <Link :href="route('profile.index')" class="dropdown-item">
                                        <i class="fas fa-user me-2"></i>
                                        My Account
                                    </Link>
                                </li>
                                <li v-if="authStore.isLoggedIn">
                                    <button
                                        @click="handleLogout"
                                        class="dropdown-item text-danger"
                                        style="border: none; background: none; width: 100%; text-align: left;"
                                    >
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
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

export default {
    name: 'Menu',
    components: {
        Link
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
                console.log('Page props changed:', { newProps, oldProps });
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
