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
                        <a href="shop-detail.html" class="nav-item nav-link">Shop Detail</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                <a href="cart.html" class="dropdown-item">Cart</a>
                                <a href="chackout.html" class="dropdown-item">Chackout</a>
                                <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                <a href="404.html" class="dropdown-item">404 Page</a>
                            </div>
                        </div>
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
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="dropdown-item text-danger"
                                    >
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </Link>
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
import  { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { useCartStore } from '@/stores/cart';
import { useAuthStore  } from '@/stores/auth';
import { ref, onMounted, onBeforeUnmount } from 'vue';
export default {
    setup() {
        const authStore = useAuthStore();
        const isDropdownOpen = ref(false);
        const toggleDropdown = () => {
            isDropdownOpen.value = !isDropdownOpen.value;
        };
        const closeDropdown = () => {
            isDropdownOpen.value = false;
        };
        // Đóng dropdown khi click ra ngoài
        const handleClickOutside = (event) => {
            if (!event.target.closest('.dropdown')) {
                closeDropdown();
            }
        };
        onMounted(() => {
            document.addEventListener('click', handleClickOutside);
        });
        onBeforeUnmount(() => {
            document.removeEventListener('click', handleClickOutside);
        });
        return {
            authStore,
            isDropdownOpen,
            toggleDropdown,
            closeDropdown,
        };
    },
    created() {
        this.authStore.checkAuth();
    },
    components: {
        Link,
    },
    props: {
        auth: {
            type: Object,
            required: false,
            default: () => ({})
        }
    },
    computed: {
        cartStore() {
            return useCartStore();
        }
    },
    async mounted() {

        try {
            await this.cartStore.fetchCartCount();
        } catch (error) {
            console.error('❌ Error in mounted:', error);
        }
    },
    methods: {
    }
}
</script>

<style lang="scss" scoped>

</style>
