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
                            <span class="stat-number" id="totalValue">{{ formatPrice(totalValue) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <p class="stat-label">{{ $t('messages.savings') }}</p>
                            <span class="stat-number" id="savedAmount">{{ formatPrice(saveValue) }}</span>
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
                                                        <span v-if="product.variants[0].sale_price" class="text-danger">{{ formatPrice(product.variants[0].sale_price) }} / {{product.variants[0].size}}</span>
                                                        <span v-if="product.variants[0].sale_price" class="text-decoration-line-through opacity-75 fs-6 ms-3">{{ formatPrice(product.variants[0].price) }} / {{product.variants[0].size}}</span>
                                                        <span v-else>{{ formatPrice(product.variants[0].price) }} / kg</span>
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
                                                            <span v-if="addToCartLoading[product.id]" class="spinner-border spinner-border-sm me-2" role="status"></span>
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
    <FooterComponent />
    <!-- Footer End -->
</template>

<script lang="ts">
import MenuComponent from '../Includes/Menu.vue';
import Search from '../Includes/Search.vue';
import FooterComponent from '@/Pages/Frontend/Includes/Footer.vue';
import axios from "axios";
import Swal from 'sweetalert2';
import { useCurrency } from '@/composables/useCurrency';
import { onUnmounted } from 'vue';

interface Product {
    id: number;
    name: string;
    description?: string;
    category?: {
        name: string;
    };
    media?: Array<{
        file_path: string;
        is_primary: boolean;
    }>;
    variants: Array<{
        id: number;
        price: number;
        sale_price?: number;
        size: string;
    }>;
}

interface CartItem {
    id: number;
    productVariant_id: number;
    quantity: number;
}

export default {
    components: {
        MenuComponent,
        Search,
        FooterComponent,
    },
    props: {
        auth: {
            type: Object,
            required: false,
            default: () => ({})
        }
    },
    setup() {
        const {
            currentCurrency,
            currencyInfo,
            formatPrice,
            convertAndFormatPrice,
            setCurrencyFromLocale,
            initializeCurrency,
            cleanup
        } = useCurrency();

        // Cleanup on unmount
        onUnmounted(() => {
            cleanup();
        });

        return {
            currentCurrency,
            currencyInfo,
            formatPrice,
            convertAndFormatPrice,
            setCurrencyFromLocale,
            initializeCurrency,
            cleanup
        };
    },
    data() {
        return {
            products: [] as Product[],
            wishlistIds: [] as number[],
            user: null as any,
            loading: true,
            error: null as string | null,
            totalValue: 0,
            saveValue: 0,
            quantities: {} as Record<number, number>,
            addToCartLoading: {} as Record<number, boolean>,
        }
    },
    computed: {
        totalOriginalPrice(): number {
            if (!Array.isArray(this.products)) return 0;
            return this.products.reduce((sum: number, p: Product) => sum + (p.variants[0]?.price || 0), 0);
        },
        totalDiscountedPrice(): number {
            if (!Array.isArray(this.products)) return 0;
            return this.products.reduce((sum: number, p: Product) => sum + (p.variants[0]?.sale_price || p.variants[0]?.price || 0), 0);
        },
        totalSavedAmount(): number {
            return this.totalOriginalPrice - this.totalDiscountedPrice;
        },
    },
    async mounted() {
        // Initialize currency system first
        await this.initializeCurrency();

        // Get user from auth prop or API
        this.user = this.auth?.user || null;

        // Initialize wishlist from session or database
        await this.initializeWishlist();

        // Watch for locale changes
        this.watchLocaleChanges();

        this.loading = false;
    },
    methods: {
        async initializeWishlist() {
            if (this.user) {
                // User is logged in, sync session wishlist & cart to database first
                try {
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
                    this.wishlistIds = this.products.map((p: Product) => p.id);
                } catch (error) {
                    console.error('❌ Session wishlist error:', error);
                    this.products = [];
                    this.wishlistIds = [];
                }
            }

            // Initialize quantities for each product
            this.initializeQuantities();

            // Convert prices after loading products
            await this.convertProductPrices();
            await this.fetchWishlistTotalValue();
            await this.fetchWishlistSaveValue();
        },

        initializeQuantities() {
            this.products.forEach((product: Product) => {
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

        // New method to convert product prices based on current locale
        async convertProductPrices() {
            if (!this.products.length || !this.currentCurrency) return;

            try {
                const response = await axios.post('/api/currency/convert-prices', {
                    products: this.products,
                    locale: (this as any).$i18n?.locale || 'vi'
                });

                if (response.data.success) {
                    this.products = response.data.products;
                }
            } catch (error) {
                console.error('Failed to convert product prices:', error);
            }
        },

        // Watch for locale changes and update currency accordingly
        watchLocaleChanges() {
            // Listen to i18n locale changes
            this.$watch(() => (this as any).$i18n?.locale, async (newLocale: string) => {
                await this.setCurrencyFromLocale(newLocale);
                await this.convertProductPrices();
                await this.fetchWishlistTotalValue();
                await this.fetchWishlistSaveValue();
            });
        },

        async fetchWishlistTotalValue() {
            this.totalValue = 0;
            for (const product of this.products) {
                this.totalValue += Number(product.variants[0]?.price || 0);
            }
            this.totalValue = Math.round(this.totalValue * Math.pow(10, this.currencyInfo?.decimals || 2)) / Math.pow(10, this.currencyInfo?.decimals || 2);
        },

        async fetchWishlistSaveValue() {
            let totalvalue2 = 0;
            this.saveValue = 0;
            for (const product of this.products) {
                if (product.variants[0]?.sale_price) {
                    totalvalue2 += Number(product.variants[0].sale_price);
                } else {
                    totalvalue2 += Number(product.variants[0]?.price || 0);
                }
            }
            this.saveValue = this.totalValue - totalvalue2;
            this.saveValue = Math.round(this.saveValue * Math.pow(10, this.currencyInfo?.decimals || 2)) / Math.pow(10, this.currencyInfo?.decimals || 2);
        },

        async toggleWishlist(productId: number) {
            if (this.user) {
                // User is logged in, use database API
                if (this.isInWishlist(productId)) {
                    try {
                        await axios.delete(`/api/wishlist/${productId}`);
                        this.wishlistIds = this.wishlistIds.filter((id: number) => id !== productId);
                        this.products = this.products.filter((p: Product) => p.id !== productId);
                        this.showNotification((this as any).$t('messages.wishlist_removed_success'), 'success');
                    } catch (error) {
                        this.showNotification((this as any).$t('messages.wishlist_remove_failed'), 'error');
                        console.error(error);
                    }
                } else {
                    try {
                        await axios.post('/api/wishlist', { product_id: productId });
                        this.wishlistIds.push(productId);
                        // Refresh products list
                        await this.fetchWishlistProductsFromApi();
                        this.showNotification((this as any).$t('messages.wishlist_added_success'), 'success');
                    } catch (error) {
                        this.showNotification((this as any).$t('messages.wishlist_add_failed'), 'error');
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
                        this.wishlistIds = this.wishlistIds.filter((id: number) => id !== productId);
                        this.products = this.products.filter((p: Product) => p.id !== productId);
                        this.showNotification((this as any).$t('messages.wishlist_removed_success'), 'success');
                    } catch (error) {
                        this.showNotification((this as any).$t('messages.wishlist_remove_failed'), 'error');
                        console.error(error);
                    }
                } else {
                    try {
                        await axios.post('/api/session/wishlist', { product_id: productId });
                        this.wishlistIds.push(productId);
                        // Refresh products list
                        await this.fetchWishlistProductsFromApi();
                        this.showNotification((this as any).$t('messages.wishlist_added_success'), 'success');
                    } catch (error) {
                        this.showNotification((this as any).$t('messages.wishlist_add_failed'), 'error');
                        console.error(error);
                    }
                }
            }

            await this.fetchWishlistTotalValue();
            await this.fetchWishlistSaveValue();
        },

        isInWishlist(productId: number): boolean {
            return this.wishlistIds.includes(productId);
        },

        increaseQuantity(productId: number) {
            if (!this.quantities[productId]) {
                this.quantities[productId] = 1;
            }
            this.quantities[productId]++;
        },

        decreaseQuantity(productId: number) {
            if (this.quantities[productId] && this.quantities[productId] > 1) {
                this.quantities[productId]--;
            }
        },

        async addToCart(product: Product) {
            const productId = product.id;
            const productVariantId = product.variants[0]?.id; // Lấy variant đầu tiên
            const quantity = this.quantities[productId] || 1;

            this.addToCartLoading[productId] = true;

            try {
                if (this.user) {
                    const cartRes = await axios.get('/api/cart');
                    const cartItems: CartItem[] = cartRes.data.data || [];
                    const existingItem = cartItems.find((item: CartItem) => item.productVariant_id === productVariantId);

                    if (existingItem) {
                        await axios.put(`/api/cart/${existingItem.id}`, {
                            quantity: existingItem.quantity + quantity
                        });
                    } else {
                        await axios.post('/api/cart', {
                            productVariant_id: productVariantId,
                            quantity: quantity
                        });
                    }
                } else {
                    await axios.post('/api/session/cart', {
                        productVariant_id: productVariantId,
                        quantity: quantity
                    });
                }

                this.showNotification((this as any).$t('messages.cart_add_success'), 'success');
            } catch (error) {
                console.error('Add to cart error:', error);
            } finally {
                this.addToCartLoading[productId] = false;
            }
        },

        showNotification(message: string, type: string = 'success') {
            let icon: 'success' | 'error' | 'warning' | 'info' = 'success';

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
                title: message,
                showConfirmButton: false,
                timer: 1500,
            });
        },
    }
}
</script>

<style lang="scss" scoped>
</style>
