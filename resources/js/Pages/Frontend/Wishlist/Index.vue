<template>
    <MenuComponent />
    <Search />
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Wishlist</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Wishlist</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <h1 class="mb-4">
                <i class="fa fa-heart" style="color: #fbc531;"></i> &nbsp;
                Wishlist</h1>

            <div class="wishlist-stats">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-item">
                            <p class="stat-label">Sản phẩm yêu thích</p>
                            <span class="stat-number" id="totalItems">{{ products.length }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <p class="stat-label">Tổng giá trị</p>
                            <span class="stat-number" id="totalValue">${{ totalValue }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <p class="stat-label">Tiết kiệm được</p>
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
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading products...</p>
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
                                        <div class="alert alert-warning">No products found in your wishlist</div>
                                    </div>
                                </template>

                                <!-- Dynamic Products -->
                                <template v-else-if="Array.isArray(products) && products.length > 0">
                                    <div v-for="product in products" :key="product.id" class="col-md-6 col-lg-6 col-xl-3">
                                        <div class="rounded position-relative fruite-item ">
                                            <div class="fruite-img border-secondary" style="border: 1px solid #000; position: relative;">
                                                <img
                                                    :alt="product.name"
                                                    :src="`/${product.media?.find((m) => m.is_primary)?.file_path || product.media?.[0]?.file_path || 'products/default.jpg'}`"
                                                    class="img-fluid w-100 rounded-top"
                                                >
                                                <!-- Nút trái tim -->
                                                <button class="btn btn-outline-danger position-absolute" style="top:10px; right:10px; z-index:2;" @click="toggleWishlist(product.id)" title="Bỏ/Thêm yêu thích">
                                                    <i v-if="isInWishlist(product.id)" class="fa fa-heart" style="color: #f00"></i>
                                                    <i v-else class="far fa-heart" style="color: #f00"></i>
                                                </button>
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                {{ product.category?.name || 'Fruits' }}
                                            </div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>{{ product.name }}</h4>
                                                <p>{{ product.description?.substring(0, 100) || 'Lorem ipsum dolor sit amet...' }}...</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">
                                                        <span v-if="product.variants[0].sale_price" class="text-danger">${{ product.variants[0].sale_price }} / {{product.variants[0].size}}</span>
                                                        <span v-if="product.variants[0].sale_price" class="text-decoration-line-through opacity-75 fs-6">${{ product.variants[0].price }} / {{product.variants[0].size}}</span>
                                                        <span v-else>${{ product.variants[0].price }} / kg</span>
                                                    </p>
                                                    <a class="btn border border-secondary rounded-pill px-3 text-primary" href="#">
                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                    </a>
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
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
        <div class="container py-5">
            <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                <div class="row g-4">
                    <div class="col-lg-3">
                        <a href="#">
                            <h1 class="text-primary mb-0">Fruitables</h1>
                            <p class="text-secondary mb-0">Fresh products</p>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative mx-auto">
                            <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" placeholder="Your Email"
                                   type="number">
                            <button
                                class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white"
                                style="top: 0; right: 0;"
                                type="submit">Subscribe Now
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-end pt-3">
                            <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i
                                class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Why People Like us!</h4>
                        <p class="mb-4">typesetting, remaining essentially unchanged. It was
                            popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.</p>
                        <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Shop Info</h4>
                        <a class="btn-link" href="">About Us</a>
                        <a class="btn-link" href="">Contact Us</a>
                        <a class="btn-link" href="">Privacy Policy</a>
                        <a class="btn-link" href="">Terms & Condition</a>
                        <a class="btn-link" href="">Return Policy</a>
                        <a class="btn-link" href="">FAQs & Help</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Account</h4>
                        <a class="btn-link" href="">My Account</a>
                        <a class="btn-link" href="">Shop details</a>
                        <a class="btn-link" href="">Shopping Cart</a>
                        <a class="btn-link" href="">Wishlist</a>
                        <a class="btn-link" href="">Order History</a>
                        <a class="btn-link" href="">International Orders</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Contact</h4>
                        <p>Address: 1429 Netus Rd, NY 48247</p>
                        <p>Email: Example@gmail.com</p>
                        <p>Phone: +0123 4567 8910</p>
                        <p>Payment Accepted</p>
                        <img src="/images/img/payment.png" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>Your Site Name</a>, All right reserved.</span>
                </div>
                <div class="col-md-6 my-auto text-center text-md-end text-white">
                    Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a> Distributed By <a
                    class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->
</template>

<script lang="ts">
import MenuComponent from '../Includes/Menu.vue';
import Search from '../Includes/Search.vue';
import axios from "axios";

export default {
    components: {
        MenuComponent,
        Search,
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
                    await axios.get('/sanctum/csrf-cookie');
                    await axios.post('/api/sync-session');

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

            await this.fetchWishlistTotalValue();
            await this.fetchWishlistSaveValue();
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

        showNotification(message, type) {
            console.log(`Notification (${type}): ${message}`);
            alert(message);
        }
    }
}
</script>

<style lang="scss" scoped>
</style>
