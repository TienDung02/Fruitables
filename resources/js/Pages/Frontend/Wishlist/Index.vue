<template>
    <MenuComponent />
    <Search />
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $t('messages.wishlist') }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.pages') }}</a></li>
            <li class="breadcrumb-item active text-white">{{ $t('messages.wishlist') }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <h1 class="mb-4">
                <i class="fa fa-heart" style="color: #fbc531;"></i> &nbsp;
                {{ $t('messages.wishlist') }}
            </h1>

            <div class="wishlist-stats">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-item">
                            <p class="stat-label">{{ $t('messages.favorite_products') }}</p>
                            <span class="stat-number" id="totalItems">{{ products.length }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <p class="stat-label">{{ $t('messages.total_value') }}</p>
                            <span class="stat-number" id="totalValue">${{ totalValue }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <p class="stat-label">{{ $t('messages.savings') }}</p>
                            <span class="stat-number" id="savedAmount">${{ saveValue }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="row g-4 justify-content-center">
                                <!-- Loading State -->
                                <template v-if="loading">
                                    <div class="col-12 text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">{{ $t('messages.loading') }}</span>
                                        </div>
                                        <p class="mt-2">{{ $t('messages.loading_products') }}</p>
                                    </div>
                                </template>

                                <!-- Error State -->
                                <template v-else-if="error">
                                    <div class="col-12">
                                        <div class="alert alert-danger">{{ error }}</div>
                                    </div>
                                </template>

                                <!-- No Products -->
                                <template v-else-if="!Array.isArray(products) || products.length === 0">
                                    <div class="col-12">
                                        <div class="alert alert-warning">{{ $t('messages.no_products_wishlist') }}</div>
                                    </div>
                                </template>

                                <!-- Dynamic Products -->
                                <template v-else-if="Array.isArray(products) && products.length > 0">
                                    <div v-for="product in products" :key="product.id" class="col-md-6 col-lg-6 col-xl-3">
                                        <div class="rounded position-relative fruite-item border border-secondary">
                                            <div class="fruite-img border-secondary" style="border: 1px solid #000; position: relative;">
                                                <img
                                                    :alt="product.name"
                                                    :src="`/${product.media?.find((m) => m.is_primary)?.file_path || product.media?.[0]?.file_path || 'products/default.jpg'}`"
                                                    class="img-fluid w-100 rounded-top"
                                                >
                                                <!-- Nút trái tim -->
                                                <button class="btn btn-outline-danger position-absolute" style="top:10px; right:10px; z-index:2;" @click="toggleWishlist(product.id)" :title="$t('messages.add_remove_favorite')">
                                                    <i v-if="isInWishlist(product.id)" class="fa fa-heart" style="color: #f00"></i>
                                                    <i v-else class="far fa-heart" style="color: #f00"></i>
                                                </button>
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                {{ product.category?.name || $t('messages.fruits') }}
                                            </div>
                                            <div class="p-4 border-top-0 rounded-bottom">
                                                <h4>{{ product.name }}</h4>
                                                <p>{{ product.description?.substring(0, 100) || $t('messages.lorem_ipsum_short') }}...</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0 d-flex">
                                                        <span v-if="product.variants[0].sale_price" class="text-danger">${{ product.variants[0].sale_price }} / {{product.variants[0].size}}</span>
                                                        <span v-if="product.variants[0].sale_price" class="text-decoration-line-through opacity-75 fs-6 ms-3">${{ product.variants[0].price }} / {{product.variants[0].size}}</span>
                                                        <span v-else>${{ product.variants[0].price }} / kg</span>
                                                    </p>

                                                    <!-- Add to Cart Button -->
                                                    <div class="d-flex align-items-center w-100 mt-2 justify-content-between">
                                                        <!-- Quantity Selector -->
                                                        <div class="input-group ms-3 width-35">
                                                            <button
                                                                @click="decreaseQuantity(product.id)"
                                                                class="btn btn-sm btn-outline-secondary "
                                                                type="button">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                            <input
                                                                v-model="quantities[product.id]"
                                                                type="number"
                                                                min="1"
                                                                class="form-control form-control-sm text-center "
                                                                style="padding: 4px;">
                                                            <button
                                                                @click="increaseQuantity(product.id)"
                                                                class="btn btn-sm btn-outline-secondary "
                                                                type="button">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>

                                                        <!-- Add to Cart Button -->
                                                        <button
                                                            @click="addToCart(product)"
                                                            :disabled="addToCartLoading[product.id]"
                                                            class="btn border border-secondary rounded-pill h-100 px-3 text-primary fs-5">
                                                            <div v-if="addToCartLoading[product.id]" class="spinner-border spinner-border-sm me-2" role="status"></div>
                                                            <i v-else class="fa fa-shopping-bag me-2 text-primary"></i>
                                                            {{ addToCartLoading[product.id] ? $t('messages.adding') : $t('messages.add_to_cart') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->

    <!-- Footer Start -->
    <Footer />
    <!-- Footer End -->
</template>

<script lang="ts">
import MenuComponent from '../Includes/Menu.vue';
import Search from '../Includes/Search.vue';
import Footer from '@/Pages/Frontend/Includes/Footer.vue';
import axios from "axios";
import Swal from 'sweetalert2';

export default {
    components: {
        MenuComponent,
        Search,
        Footer,
    },
    props: {
        auth: {
            type: Object,
            required: false,
            default: () => ({})
        }
    },
    data() {
        return {
            products: [],
            wishlistIds: [],
            user: null,
            loading: true,
            error: null,
            totalValue: 0,
            saveValue: 0,
            quantities: {},
            addToCartLoading: {},
        }
    },
    computed: {
        totalOriginalPrice() {
            if (!Array.isArray(this.products)) return 0;
            return this.products.reduce((sum, p) => sum + (p.price || 0), 0);
        },
        totalDiscountedPrice() {
            if (!Array.isArray(this.products)) return 0;
            return this.products.reduce((sum, p) => sum + (p.sale_price || p.price || 0), 0);
        },
        totalSavedAmount() {
            return this.totalOriginalPrice - this.totalDiscountedPrice;
        },
    },
    async mounted() {
        // Get user from auth prop or API
        this.user = this.auth?.user || null;

        // Initialize wishlist from session or database
        await this.initializeWishlist();

        this.loading = false;
    },
    methods: {
        async initializeWishlist() {
            if (this.user) {
                // User is logged in, sync session wishlist & cart to database first
                try {
                    // await axios.get('/sanctum/csrf-cookie');
                    // await axios.post('/api/sync-session');

                    // Get wishlist products from database
                    const res = await axios.get('/api/wishlist');
                    this.products = res.data || [];

                    // Get wishlist IDs
                    const idsRes = await axios.get('/api/wishlist/ids');
                    this.wishlistIds = idsRes.data.data || [];
                } catch (error) {
                    console.error('❌ Wishlist sync error:', error);
                    this.products = [];
                    this.wishlistIds = [];
                }
            } else {
                // User not logged in, get from session
                try {
                    const res = await axios.get('/api/session/wishlist');
                    this.products = res.data.data || [];
                    console.log('this.products', this.products)
                    // Get wishlist IDs from session
                    this.wishlistIds = this.products.map(p => p.id);
                } catch (error) {
                    console.error('❌ Session wishlist error:', error);
                    this.products = [];
                    this.wishlistIds = [];
                }
            }

            // Initialize quantities for each product
            this.initializeQuantities();

            await this.fetchWishlistTotalValue();
            await this.fetchWishlistSaveValue();
        },

        initializeQuantities() {
            this.products.forEach(product => {
                // Vue 3: Sử dụng direct assignment thay vì this.$set
                this.quantities[product.id] = 1;
            });
        },

        // Lấy chi tiết sản phẩm wishlist từ API (khi đã đăng nhập)
        async fetchWishlistProductsFromApi() {
            try {
                if (this.user) {
                    const res = await axios.get('/api/wishlist');
                    this.products = res.data || [];
                } else {
                    const res = await axios.get('/api/session/wishlist');
                    this.products = res.data.data || [];
                }
            } catch {
                this.products = [];
                this.error = 'Không thể tải sản phẩm yêu thích.';
            }
        },

        async fetchWishlistTotalValue() {
            this.totalValue = 0;
            console.log('products', this.products);
            for (const product of this.products) {
                // console.log('product', product)
                // console.log('product.variants', product.variants)
                // console.log('product.variants[0].price', product.variants[0].price)

                this.totalValue += Number(product.variants[0].price);
            }
            this.totalValue = Math.round(this.totalValue * 100) / 100;
        },

        async fetchWishlistSaveValue() {
            let totalvalue2 = 0;
            this.saveValue = 0;
            for (const product of this.products) {
                if (product.variants[0].sale_price) {
                    totalvalue2 += Number(product.variants[0].sale_price);
                } else {
                    totalvalue2 += Number(product.variants[0].price);
                }
            }
            this.saveValue = this.totalValue - totalvalue2;
            this.saveValue = Math.round(this.saveValue * 100) / 100;
        },

        async toggleWishlist(productId) {
            if (this.user) {
                // User is logged in, use database API
                if (this.isInWishlist(productId)) {
                    try {
                        await axios.delete(`/api/wishlist/${productId}`);
                        this.wishlistIds = this.wishlistIds.filter(id => id !== productId);
                        this.products = this.products.filter(p => p.id !== productId);
                        this.showNotification('Đã xóa khỏi danh sách yêu thích!', 'success');
                    } catch (error) {
                        this.showNotification('Xóa khỏi yêu thích thất bại!', 'error');
                        console.error(error);
                    }
                } else {
                    try {
                        await axios.post('/api/wishlist', { product_id: productId });
                        this.wishlistIds.push(productId);
                        // Refresh products list
                        await this.fetchWishlistProductsFromApi();
                        this.showNotification('Đã thêm vào danh sách yêu thích!', 'success');
                    } catch (error) {
                        this.showNotification('Thêm vào yêu thích thất bại!', 'error');
                        console.error(error);
                    }
                }
            } else {
                // User not logged in, use session API
                if (this.isInWishlist(productId)) {
                    try {
                        await axios.delete('/api/session/wishlist', {
                            data: { product_id: productId }
                        });
                        this.wishlistIds = this.wishlistIds.filter(id => id !== productId);
                        this.products = this.products.filter(p => p.id !== productId);
                        this.showNotification('Đã xóa khỏi danh sách yêu thích!', 'success');
                    } catch (error) {
                        this.showNotification('Xóa khỏi yêu thích thất bại!', 'error');
                        console.error(error);
                    }
                } else {
                    try {
                        await axios.post('/api/session/wishlist', { product_id: productId });
                        this.wishlistIds.push(productId);
                        // Refresh products list
                        await this.fetchWishlistProductsFromApi();
                        this.showNotification('Đã thêm vào danh sách yêu thích!', 'success');
                    } catch (error) {
                        this.showNotification('Thêm vào yêu thích thất bại!', 'error');
                        console.error(error);
                    }
                }
            }

            await this.fetchWishlistTotalValue();
            await this.fetchWishlistSaveValue();
        },

        isInWishlist(productId) {
            return this.wishlistIds.includes(productId);
        },



        increaseQuantity(productId) {
            if (!this.quantities[productId]) {
                this.quantities[productId] = 1;
            }
            this.quantities[productId]++;
        },
        decreaseQuantity(productId) {
            if (this.quantities[productId] && this.quantities[productId] > 1) {
                this.quantities[productId]--;
            }
        },
        async addToCart(product) {
            const productId = product.id;
            const productVariantId = product.variants[0].id; // Lấy variant đầu tiên
            const quantity = this.quantities[productId] || 1;

            this.addToCartLoading[productId] = true;

            try {
                if (this.user) {
                    // User đã đăng nhập - sử dụng database API
                    const cartRes = await axios.get('/api/cart');
                    const cartItems = cartRes.data.data || [];

                    const existingItem = cartItems.find(item => item.productVariant_id === productVariantId);

                    if (existingItem) {
                        // Nếu variant đã có trong giỏ hàng, cập nhật số lượng
                        await axios.put(`/api/cart/${existingItem.id}`, {
                            quantity: existingItem.quantity + quantity
                        });
                    } else {
                        // Nếu variant chưa có, thêm mới
                        await axios.post('/api/cart', {
                            productVariant_id: productVariantId,
                            quantity: quantity
                        });
                    }
                } else {
                    // User chưa đăng nhập - sử dụng session API
                    await axios.post('/api/session/cart', {
                        productVariant_id: productVariantId,
                        quantity: quantity
                    });
                }

                this.showNotification('Sản phẩm đã được thêm vào giỏ hàng!', 'success');

                // Reset quantity về 1 sau khi thêm thành công
                this.quantities[productId] = 1;

            } catch (error) {
                this.showNotification('Thêm vào giỏ hàng thất bại!', 'error');
                console.error('Add to cart error:', error);
            } finally {
                this.addToCartLoading[productId] = false;
            }
        },
        showNotification(message, type = 'success') {
            let icon = 'success';
            const title = message;

            switch(type) {
                case 'success':
                    icon = 'success';
                    break;
                case 'error':
                    icon = 'error';
                    break;
                case 'warning':
                    icon = 'warning';
                    break;
                case 'info':
                    icon = 'info';
                    break;
                default:
                    icon = 'success';
            }

            Swal.fire({
                position: "top-end",
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 1500,
            });
        },
    }
}
</script>

<style lang="scss" scoped>
</style>
