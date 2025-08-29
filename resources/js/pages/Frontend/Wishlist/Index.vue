<template>
    <MenuComponent />
    <Search />
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Shop</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Shop</li>
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
                            <span class="stat-number" id="totalValue">${{ this.totalValue }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <p class="stat-label">Tiết kiệm được</p>
                            <span class="stat-number" id="savedAmount">${{ this.saveValue }}</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row g-4">
                <div class="col-lg-12">

                    <div class="row g-4">
                        <div class="col-lg-12">
                            <!-- Search & Filter indicators -->
                            <div v-if="searchQuery || selectedCategoryId || sortBy || priceRange.min > 0 || priceRange.max < 100" class="mb-4">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="text-muted">Active filters:</span>

                                    <!-- Search filter -->
                                    <span v-if="searchQuery" class="badge bg-primary">
                                        <i class="fas fa-search me-1"></i>
                                        Search: "{{ searchQuery }}"
                                        <button @click="clearSearch" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Category filter -->
                                    <span v-if="selectedCategoryId" class="badge bg-secondary">
                                        <i class="fas fa-tag me-1"></i>
                                        Category: {{ getCategoryName(selectedCategoryId) }}
                                        <button @click="clearCategoryFilter" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Sorting indicator -->
                                    <span v-if="sortBy" class="badge bg-info">
                                        <i class="fas fa-sort me-1"></i>
                                        Sort: {{ getSortLabel(sortBy) }}
                                        <button @click="clearSorting" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Sorting indicator -->
                                    <span v-if="priceRange.min > 0 || priceRange.max < 100" class="badge bg-success">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        Price range: ${{ getSortLabel(priceRange.min) }} <i class="	fas fa-arrow-right"></i> ${{ getSortLabel(priceRange.max)}}
                                        <button @click="clearPriceRange" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>
                                    <!-- Clear all filters -->
                                    <button @click="clearAllFilters" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times me-1"></i>
                                        Clear All
                                    </button>
                                </div>
                            </div>                            <!-- Loading indicator for search -->
                            <div v-if="isSearching" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Searching...</span>
                                </div>
                                <small class="text-muted ms-2">Searching products...</small>
                            </div>

                            <!-- Debug Info -->


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
                                        <div class="alert alert-warning">No products found</div>
                                    </div>
                                </template>

                                <!-- Dynamic Products -->
                                <template v-else-if="Array.isArray(products) && products.length > 0">
                                    <div v-for="product in products" :key="product.id" class="col-md-6 col-lg-6 col-xl-3">
                                        <div class="rounded position-relative fruite-item ">
                                            <div class="fruite-img border-secondary" style="border: 1px solid #000; position: relative;">
                                                <img
                                                    :alt="product.name"
                                                    :src="`/${product.media?.find(m => m.is_primary)?.file_path || product.media?.[0]?.file_path || 'products/default.jpg'}`"
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
                                                        <span v-if="product.sale_price" class="text-danger">${{ product.sale_price }} / kg</span>
                                                        <span v-if="product.sale_price" class="text-decoration-line-through opacity-75 fs-6">${{ product.price }} / kg</span>
                                                        <span v-else>${{ product.price }} / kg</span>
                                                    </p>
                                                    <a class="btn border border-secondary rounded-pill px-3 text-primary" href="#">
                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Pagination -->
<!--                                <div-->
<!--                                    v-if="!loading && !error && Array.isArray(products) && products.length > 0 && pagination.last_page > 1"-->
<!--                                    class="col-12">-->
<!--                                    <div class="pagination d-flex justify-content-center mt-5">-->

<!--                                        &lt;!&ndash;  Previous Button &ndash;&gt;-->
<!--                                        <a href="#"-->
<!--                                           @click.prevent="goToPrevPage()"-->
<!--                                           :class="['rounded', 'me-1', { 'disabled text-muted': pagination.current_page === 1 }]"-->
<!--                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">-->
<!--                                            &laquo;-->
<!--                                        </a>-->

<!--                                        &lt;!&ndash;  First Page &ndash;&gt;-->
<!--                                        <a href="#"-->
<!--                                           v-if="pagination.current_page > 4"-->
<!--                                           @click.prevent="goToFirstPage()"-->
<!--                                           class="rounded me-1"-->
<!--                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">-->
<!--                                            First-->
<!--                                        </a>-->

<!--                                        &lt;!&ndash; Dots if needed &ndash;&gt;-->
<!--                                        <span v-if="pagination.current_page > 4"-->
<!--                                              class="rounded me-1"-->
<!--                                              style="padding: 8px 12px; border: 1px solid #ddd;">-->
<!--                                            ...-->
<!--                                        </span>-->

<!--                                        &lt;!&ndash; Page Numbers &ndash;&gt;-->
<!--                                        <template v-for="page in getVisiblePages()" :key="page">-->
<!--                                            <a href="#"-->
<!--                                               @click.prevent="changePage(page)"-->
<!--                                               :class="['rounded', 'me-1', { 'active bg-primary text-white': page === pagination.current_page }]"-->
<!--                                               style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">-->
<!--                                                {{ page }}-->
<!--                                            </a>-->
<!--                                        </template>-->

<!--                                        <span v-if="pagination.current_page < pagination.last_page - 3"-->
<!--                                              class="rounded me-1"-->
<!--                                              style="padding: 8px 12px; border: 1px solid #ddd;">-->
<!--                                            ...-->
<!--                                        </span>-->

<!--                                        &lt;!&ndash;  Last Page &ndash;&gt;-->
<!--                                        <a href="#"-->
<!--                                           v-if="pagination.current_page < pagination.last_page - 3"-->
<!--                                           @click.prevent="goToLastPage()"-->
<!--                                           class="rounded me-1"-->
<!--                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">-->
<!--                                            {{ pagination.last_page }}-->
<!--                                        </a>-->

<!--                                        &lt;!&ndash;  Next Button &ndash;&gt;-->
<!--                                        <a href="#"-->
<!--                                           @click.prevent="goToNextPage()"-->
<!--                                           :class="['rounded', { 'disabled text-muted': pagination.current_page === pagination.last_page }]"-->
<!--                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">-->
<!--                                            &raquo;-->
<!--                                        </a>-->
<!--                                    </div>-->

<!--                                    &lt;!&ndash;  Pagination Info &ndash;&gt;-->
<!--                                    <div class="text-center mt-3">-->
<!--                                        <small class="text-muted">-->
<!--                                            Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }}-->
<!--                                            of {{ pagination.total || 0 }} products-->
<!--                                            (Page {{ pagination.current_page }} of {{ pagination.last_page }})-->
<!--                                        </small>-->
<!--                                    </div>-->
<!--                                </div>-->
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
                        <img src="images/img/payment.png" class="img-fluid" alt="">
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
                    <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                    <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                    <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
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
    data() {
        return {
            products: [],
            wishlistIds: [0],
            user: null,
            loading: true,
            error: null,
            selectedCategoryId: null,
            expandedCategories: [],
            categoriesLoading: false,
            totalProductsCount: 0,
            searchQuery: '',
            searchTimeout: null,
            isSearching: false,
            sortBy: '',
            priceRange: { min: 0, max: 100 },
            priceTimeout: null,
            totalValue: 0,
            saveValue: 0,
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 9,
                total: 0,
                from: 0,
                to: 0
            },
        }
    },
    computed: {
        totalOriginalPrice(): number {
            if (!Array.isArray(this.products)) return 0;
            return this.products.reduce((sum, p) => sum + (p.price || 0), 0);
        },
        totalDiscountedPrice(): number {
            if (!Array.isArray(this.products)) return 0;
            return this.products.reduce((sum, p) => sum + (p.sale_price || p.price || 0), 0);
        },
        totalSavedAmount(): number {
            return this.totalOriginalPrice - this.totalDiscountedPrice;
        },
    },
    async mounted() {
        // Lấy user từ API hoặc props nếu có
        try {
            const userRes = await axios.get('/api/user');
            this.user = userRes.data || null;
        } catch {
            this.user = null;
        }
        // Lấy wishlist từ localStorage
        const localWishlistIds = localStorage.getItem('wishlistIds');
        const localIds = localWishlistIds ? JSON.parse(localWishlistIds) : [];

        // Nếu đã đăng nhập, merge wishlist
        if (this.user) {
            // Lấy wishlist từ database
            let dbIds = [];
            try {
                const res = await axios.get('/api/wishlist');
                dbIds = res.data.map(p => p.id);
            } catch {
                dbIds = [];
            }
            // Merge 2 mảng id, loại bỏ trùng lặp
            const mergedIds = Array.from(new Set([...localIds, ...dbIds]));
            // Thêm các id từ local vào database nếu chưa có
            for (const productId of mergedIds) {
                if (!dbIds.includes(productId)) {
                    try {
                        await axios.post('/api/wishlist', { product_id: productId });
                    } catch (e) {}
                }
            }
            // Gán lại wishlistIds cho cả localStorage và state
            this.wishlistIds = mergedIds;
            localStorage.setItem('wishlistIds', JSON.stringify(mergedIds));
            // Lấy chi tiết sản phẩm wishlist từ database
            try {
                const res = await axios.get('/api/wishlist');
                this.products = res.data || [];
                console.log('product', this.products);

                // Đồng bộ lại localStorage wishlistProducts
                localStorage.setItem('wishlistProducts', JSON.stringify(this.products));
            } catch {
                this.products = [];
            }
        } else {
            // Nếu chưa đăng nhập, lấy wishlist từ localStorage
            this.wishlistIds = localIds;
            const localProducts = localStorage.getItem('wishlistProducts');
            this.products = localProducts ? JSON.parse(localProducts) : [];
            console.log('product', this.products);

        }
        await this.fetchWishlistTotalValue();
        await this.fetchWishlistSaveValue();
        this.loading = false;
    },
    methods: {
        // Lấy chi tiết sản phẩm wishlist từ API (khi đã đăng nhập)
        async fetchWishlistProductsFromApi() {
            try {
                const res = await axios.get('/api/wishlist');
                this.products = res.data || [];

            } catch {
                this.products = [];
                this.error = 'Không thể tải sản phẩm yêu thích.';
            }
        },
        async fetchWishlistTotalValue() {
            this.totalValue = 0;
            for (const product of this.products) {
                this.totalValue += Number(product.price);
            }
            this.totalValue = Math.round(this.totalValue * 100) / 100;
            console.log(this.totalValue)
        },
        async fetchWishlistSaveValue() {
            let totalvalue2 = 0;
            this.saveValue = 0;
            for (const product of this.products) {
                if (product.sale_price) {
                    totalvalue2 += Number(product.sale_price);
                } else {
                    totalvalue2 += Number(product.price);
                }
            }
            this.saveValue = this.totalValue - totalvalue2;
            this.saveValue = Math.round(this.saveValue * 100) / 100;
        },
        async toggleWishlist(productId: number) {
            if (this.user) {
                // Đã đăng nhập, thao tác với database
                if (this.isInWishlist(productId)) {
                    await axios.delete(`/api/wishlist/${productId}`);
                    this.wishlistIds = this.wishlistIds.filter((id: number) => id !== productId);
                } else {
                    await axios.post('/api/wishlist', { product_id: productId });
                    this.wishlistIds.push(productId);
                }
                // Đồng bộ lại localStorage
                localStorage.setItem('wishlistIds', JSON.stringify(this.wishlistIds));
                // Lấy lại chi tiết sản phẩm từ database
                await this.fetchWishlistProductsFromApi();
                await this.fetchWishlistTotalValue();
                await this.fetchWishlistSaveValue();
                localStorage.setItem('wishlistProducts', JSON.stringify(this.products));
            } else {
                // Chưa đăng nhập, thao tác với localStorage
                if (this.isInWishlist(productId)) {
                    this.wishlistIds = this.wishlistIds.filter(id => id !== productId);
                    localStorage.setItem('wishlistIds', JSON.stringify(this.wishlistIds));
                    // Xóa sản phẩm khỏi localStorage wishlistProducts
                    const localProducts = localStorage.getItem('wishlistProducts');
                    if (localProducts) {
                        const productsArr = JSON.parse(localProducts);
                        const updatedProducts = productsArr.filter((p: any) => p.id !== productId);
                        localStorage.setItem('wishlistProducts', JSON.stringify(updatedProducts));
                        this.products = updatedProducts;
                    } else {
                        this.products = [];
                    }
                    await this.fetchWishlistTotalValue();
                    await this.fetchWishlistSaveValue();
                } else {
                    this.wishlistIds.push(productId);
                    localStorage.setItem('wishlistIds', JSON.stringify(this.wishlistIds));
                    // Thêm sản phẩm vào localStorage wishlistProducts
                    const product = this.products.find(p => p.id === productId);
                    let productsArr = [];
                    const localProducts = localStorage.getItem('wishlistProducts');
                    if (localProducts) {
                        productsArr = JSON.parse(localProducts);
                    }
                    if (product && !productsArr.some((p: any) => p.id === productId)) {
                        productsArr.push(product);
                        localStorage.setItem('wishlistProducts', JSON.stringify(productsArr));
                        this.products = productsArr;
                    }
                    await this.fetchWishlistTotalValue();
                    await this.fetchWishlistSaveValue();
                }
            }
        },
        isInWishlist(productId: number) {
            return this.wishlistIds.includes(productId);
        },
    }
}
</script>

<style lang="scss" scoped>
</style>
