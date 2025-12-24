<template>
    <Head :title="$t('messages.shop')"/>
    <Menu></Menu>
    <Search></Search>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $t('messages.shop') }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.pages') }}</a></li>
            <li class="breadcrumb-item active text-white">{{ $t('messages.shop') }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <h1 class="mb-4">{{ $t('messages.fresh_fruits_shop') }}</h1>
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <div class="col-xl-3">
                            <div class="input-group w-100 mx-auto d-flex">
                                <input
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                    @keyup.enter="performSearch"
                                    aria-describedby="search-icon-1"
                                    class="form-control p-3"
                                    :placeholder="$t('messages.search_products')"
                                    type="search">
                                <span id="search-icon-1"
                                      class="input-group-text p-3"
                                      @click="performSearch"
                                      style="cursor: pointer;">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>

                        </div>
                        <div class="col-6"></div>
                        <div class="col-xl-3">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                <label for="fruits">{{ $t('messages.default_sorting') }}</label>
                                <select
                                    id="fruits"
                                    v-model="sortBy"
                                    @change="applySorting"
                                    class="border-0 form-select-sm bg-light me-3 no-focus-border"
                                    form="fruitform"
                                    name="fruitlist">
                                    <option value="">{{ $t('messages.default') }}</option>
                                    <option value="name_asc">{{ $t('messages.name_a_z') }}</option>
                                    <option value="name_desc">{{ $t('messages.name_z_a') }}</option>
                                    <option value="price_asc">{{ $t('messages.price_low_to_high') }}</option>
                                    <option value="price_desc">{{ $t('messages.price_high_to_low') }}</option>
                                    <option value="newest">{{ $t('messages.newest_first') }}</option>
                                    <option value="oldest">{{ $t('messages.oldest_first') }}</option>
                                    <option value="featured">{{ $t('messages.featured_first') }}</option>
                                    <option value="popularity">{{ $t('messages.most_popular') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>{{ $t('messages.categories') }}</h4>

                                        <!-- Loading state for categories -->
                                        <div v-if="categoriesLoading" class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">{{ $t('messages.loading_categories') }}</span>
                                            </div>
                                        </div>

                                        <!-- Dynamic Categories as Dropdowns -->
                                        <div v-else class="fruite-categorie">
                                            <!-- All Products option -->
                                            <div class="mb-3 pt-2" style="border-top: 1px solid #eee;">
                                                <div
                                                    :class="{ 'text-primary fw-bold': selectedCategoryId === null }"
                                                    class="d-flex justify-content-between align-items-center fruite-name"
                                                    style="cursor: pointer; padding: 8px 12px; background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;"
                                                    @click="clearCategoryFilter()">

                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-list me-2"></i>
                                                        <span>{{ $t('messages.all_products') }}</span>
                                                    </div>

                                                    <span class="text-muted">({{ totalProductsCount }})</span>
                                                </div>
                                            </div>
                                            <template v-for="category in categories" :key="category.id">

                                                <!-- Main Category (Level 1) - Dropdown Toggle -->
                                                <div class="mb-2">
                                                    <div
                                                        :class="{ 'text-primary fw-bold': selectedCategoryId === category.id }"
                                                        class="d-flex justify-content-between align-items-center fruite-name category-header"
                                                        style="cursor: pointer; padding: 8px 12px; background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;"
                                                        @click="toggleCategory(category.id)">

                                                        <div class="d-flex align-items-center">
                                                            <!-- Expand/Collapse Icon -->
                                                            <i :class="[
                                                                'me-2',
                                                                category.children && category.children.length > 0
                                                                    ? (expandedCategories.includes(category.id) ? 'fas fa-chevron-down' : 'fas fa-chevron-right')
                                                                    : 'fas fa-circle'
                                                            ]" style="font-size: 10px; opacity: 0.7;"></i>

                                                            <!-- Category Icon -->
                                                            <i :class="getCategoryIcon(category.name)" class="me-2"></i>

                                                            <!-- Category Name -->
                                                            <span
                                                                :class="{ 'text-primary fw-bold': selectedCategoryId === category.id }"
                                                                class="category-name"
                                                                @click.stop="filterByCategory(category.id)">
                                                                {{ category.name }}
                                                            </span>
                                                        </div>

                                                        <!-- Total Products Count -->
                                                        <span class="text-muted">({{
                                                                category.total_products_count || 0
                                                            }})</span>
                                                    </div>

                                                    <!-- Subcategories (Level 2) - Collapsible -->
                                                    <div v-if="category.children && category.children.length > 0"
                                                         v-show="expandedCategories.includes(category.id)"
                                                         class="subcategories-container"
                                                         style="margin-left: 20px; margin-top: 8px; border-left: 2px solid #dee2e6; padding-left: 15px;">

                                                        <div v-for="subcategory in category.children"
                                                             :key="subcategory.id"
                                                             class="mb-1">
                                                            <div :class="{ 'text-primary fw-bold': selectedCategoryId === subcategory.id }"
                                                                 class="d-flex justify-content-between align-items-center fruite-name subcategory-item"
                                                                 @click="filterByCategory(subcategory.id)">

                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-circle me-2" style="font-size: 6px; opacity: 0.6;"></i>
                                                                    <span class="text-sm">{{ subcategory.name }}</span>
                                                                </div>

                                                                <span class="text-muted small">({{ subcategory.products_count || 0 }})</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4 class="mb-2">{{ $t('messages.price_range') }}</h4>

                                        <!-- Dual Range Slider Container -->
                                        <div class="price-range-container mb-3">
                                            <div class="price-slider-wrapper">
                                                <!-- Background track -->
                                                <div class="price-track-bg"></div>

                                                <!-- Active range track -->
                                                <div class="price-track-active" :style="rangeTrackStyle"></div>

                                                <!-- Min Price Slider -->
                                                <input
                                                    id="minPriceRange"
                                                    v-model.number="priceRange.min"
                                                    @input="handleMinRangeChange"
                                                    type="range"
                                                    min="0"
                                                    max="100"
                                                    step="1"
                                                    class="price-range-input price-range-min">

                                                <!-- Max Price Slider -->
                                                <input
                                                    id="maxPriceRange"
                                                    v-model.number="priceRange.max"
                                                    @input="handleMaxRangeChange"
                                                    type="range"
                                                    min="0"
                                                    max="100"
                                                    step="1"
                                                    class="price-range-input price-range-max">
                                            </div>

                                            <!-- Price Display Only -->
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="price-display">
                                                    ${{ priceRange.min }}
                                                </span>
                                                <span class="text-muted mx-2">{{ $t('messages.to') }}</span>
                                                <span class="price-display">
                                                    ${{ priceRange.max }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Clear Price Filter -->
                                        <div v-if="(priceRange.min > 0) || (priceRange.max < 100)" class="text-center">
                                            <button @click="clearPriceFilter" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-times me-1"></i>
                                                {{ $t('messages.clear_price_filter') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <h4 class="mb-3">{{ $t('messages.featured_products') }}</h4>
                                    <div v-for="featuredProduct in featuredProducts" :key="featuredProduct.id">
                                        <Link :href="route('detail.index', featuredProduct.id)">

                                            <div class="d-flex align-items-center justify-content-start mb-2" >
                                                <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                    <img
                                                        :src="`/${featuredProduct.media?.find(m => m.is_primary)?.file_path || featuredProduct.media?.[0]?.file_path || 'products/default.jpg'}`"
                                                        alt=""
                                                        class="img-fluid rounded h-100" style="object-fit: contain">
                                                </div>
                                                <div>
                                                    <h6 class="mb-2">{{ featuredProduct.name || $t('messages.fruits') }}</h6>
                                                    <div class="d-flex mb-2">
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                    <div v-if="featuredProduct.variants[0].sale_price" class="d-flex mb-2 align-items-center">
                                                        <h5  class=" me-2 mb-0">${{ featuredProduct.variants[0].sale_price }} / {{ featuredProduct.variants[0].size }}</h5>
                                                        <h6 class="text-danger text-decoration-line-through mb-0">${{ featuredProduct.variants[0].price }}/ {{ featuredProduct.variants[0].size }}</h6>
                                                    </div>
                                                    <div v-else class="d-flex mb-2">
                                                        <h5  class=" me-2">${{ featuredProduct.variants[0].price }} / {{ featuredProduct.variants[0].size }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </Link>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="position-relative">
                                        <img src="images/img/banner-fruits.jpg" class="img-fluid w-100 rounded" alt="">
                                        <div class="position-absolute"
                                             style="top: 50%; right: 10px; transform: translateY(-50%);">
                                            <h3 class="text-secondary fw-bold">{{ $t('messages.fresh_fruits_banner') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <!-- Search & Filter indicators -->
                            <div v-if="searchQuery || selectedCategoryId || sortBy || priceRange.min > 0 || priceRange.max < 100" class="mb-4">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="text-muted">{{ $t('messages.active_filters') }}</span>

                                    <!-- Search filter -->
                                    <span v-if="searchQuery" class="badge bg-primary">
                                        <i class="fas fa-search me-1"></i>
                                        {{ $t('messages.search') }}: "{{ searchQuery }}"
                                        <button @click="clearSearch" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Category filter -->
                                    <span v-if="selectedCategoryId" class="badge bg-secondary">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ $t('messages.category') }}: {{ getCategoryName(selectedCategoryId) }}
                                        <button @click="clearCategoryFilter" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Sorting indicator -->
                                    <span v-if="sortBy" class="badge bg-info">
                                        <i class="fas fa-sort me-1"></i>
                                        {{ $t('messages.sort') }}: {{ getSortLabel(sortBy) }}
                                        <button @click="clearSorting" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Price range indicator -->
                                    <span v-if="priceRange.min > 0 || priceRange.max < 100" class="badge bg-success">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        {{ $t('messages.price_range_filter') }}: ${{ priceRange.min }} <i class="fas fa-arrow-right"></i> ${{ priceRange.max }}
                                        <button @click="clearPriceRange" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>
                                    <!-- Clear all filters -->
                                    <button @click="clearAllFilters" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times me-1"></i>
                                        {{ $t('messages.clear_all') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Loading indicator for search -->
                            <div v-if="isSearching" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">{{ $t('messages.searching') }}</span>
                                </div>
                                <small class="text-muted ms-2">{{ $t('messages.searching_products') }}</small>
                            </div>

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
                                        <div class="alert alert-warning">{{ $t('messages.no_products_found') }}</div>
                                    </div>
                                </template>

                                <!-- Dynamic Products -->
                                <template v-else-if="Array.isArray(products) && products.length > 0">
                                    <div v-for="product in products" :key="product.id"
                                         class="col-md-6 col-lg-6 col-xl-4 d-flex">
                                        <div class="rounded position-relative fruite-item border border-secondary d-flex flex-column h-100" >
                                                <div class="fruite-img border-secondary border-bottom flex-shrink-0" style="border: 1px solid #000;">
                                                    <img
                                                        :alt="product.name"
                                                        :src="`/${product.media?.find(m => m.is_primary)?.file_path || product.media?.[0]?.file_path || 'products/default.jpg'}`"
                                                        class="img-fluid w-100 rounded-top"
                                                    >
                                                </div>
                                                <!-- Nút trái tim thêm vào wishlist -->
                                                <button class="btn btn-outline-danger position-absolute" style="top:10px; right:10px; z-index:2;" @click="toggleWishlist(product.id)" :title="$t('messages.add_to_wishlist')">
                                                    <i v-if="isInWishlist(product.id)" class="fa fa-heart" style="color: #f00"></i>
                                                    <i v-else class="far fa-heart" style="color: #f00"></i>
                                                </button>
                                                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                     style="top: 10px; left: 10px;">
                                                    {{ product.category?.name || $t('messages.fruits') }}
                                                </div>

                                            <Link :href="route('detail.index', product.id)" class="flex-grow-1 overflow-auto">
                                                <div class="p-4 border-top-0 rounded-bottom h-100">
                                                    <h4>{{ product.name }}</h4>
                                                    <p>{{
                                                            product.short_description?.substring(0, 100) || $t('messages.lorem_ipsum_text')
                                                        }}...</p>
                                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                                        <p class="text-dark fs-5 fw-bold mb-0">
                                                            <div  v-if="product.variants[0].sale_price"> <span class="text-danger">${{ product.variants[0].sale_price }} / {{ product.variants[0].unit }}</span>  &nbsp; <span class="text-decoration-line-through opacity-75 fs-6"> ${{ product.variants[0].price }}/ {{ product.variants[0].unit }}</span></div>
                                                            <span v-else>${{ product.variants[0].price }} / {{ product.variants[0].unit }}</span>
                                                        </p>

                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                    </div>
                                </template>

                                <!-- Pagination -->
                                <div
                                    v-if="!loading && !error && Array.isArray(products) && products.length > 0 && pagination.last_page > 1"
                                    class="col-12">
                                    <div class="pagination d-flex justify-content-center mt-5">

                                        <!--  Previous Button -->
                                        <a href="#"
                                           @click.prevent="goToPrevPage()"
                                           :class="['rounded', 'me-1', { 'disabled text-muted': pagination.current_page === 1 }]"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            &laquo;
                                        </a>

                                        <!--  First Page -->
                                        <a href="#"
                                           v-if="pagination.current_page > 4"
                                           @click.prevent="goToFirstPage()"
                                           class="rounded me-1"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            {{ $t('messages.first') }}
                                        </a>

                                        <!-- Dots if needed -->
                                        <span v-if="pagination.current_page > 4"
                                              class="rounded me-1"
                                              style="padding: 8px 12px; border: 1px solid #ddd;">
                                            ...
                                        </span>

                                        <!-- Page Numbers -->
                                        <template v-for="page in getVisiblePages()" :key="page">
                                            <a href="#"
                                               @click.prevent="changePage(page)"
                                               :class="['rounded', 'me-1', { 'active bg-primary text-white': page === pagination.current_page }]"
                                               style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                                {{ page }}
                                            </a>
                                        </template>

                                        <span v-if="pagination.current_page < pagination.last_page - 3"
                                              class="rounded me-1"
                                              style="padding: 8px 12px; border: 1px solid #ddd;">
                                            ...
                                        </span>

                                        <!--  Last Page -->
                                        <a href="#"
                                           v-if="pagination.current_page < pagination.last_page - 3"
                                           @click.prevent="goToLastPage()"
                                           class="rounded me-1"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            {{ pagination.last_page }}
                                        </a>

                                        <!--  Next Button -->
                                        <a href="#"
                                           @click.prevent="goToNextPage()"
                                           :class="['rounded', { 'disabled text-muted': pagination.current_page === pagination.last_page }]"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            &raquo;
                                        </a>
                                    </div>

                                    <!--  Pagination Info -->
                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            {{ $t('messages.showing') }} {{ pagination.from || 0 }} {{ $t('messages.to') }} {{ pagination.to || 0 }}
                                            {{ $t('messages.of') }} {{ pagination.total || 0 }} {{ $t('messages.products') }}
                                            ({{ $t('messages.page') }} {{ pagination.current_page }} {{ $t('messages.of') }} {{ pagination.last_page }})
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->


    <Footer></Footer>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router } from '@inertiajs/vue3';
import Menu from '../Includes/Menu.vue';
import Footer from '../Includes/Footer.vue';
import Search from '../Includes/Search.vue';
import axios from "axios";
import { useCartStore } from '@/stores/cart';
import  { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
axios.defaults.withCredentials = true;
export default {
    components: {
        AuthenticatedLayout,
        Menu,
        Footer,
        Head,
        Search,
        Link,
    },
    props: {
        auth: Object,
        csrf_token: String,
    },
    data() {
        return {
            products: [],
            categories: [],
            featuredProducts: [],
            wishlistIds: [], // Danh sách id sản phẩm trong wishlist
            wishlistProducts: [], // Danh sách sản phẩm wishlist từ database
            user: null, // Thông tin user, nếu có
            selectedCategoryId: null,
            expandedCategories: [],           //  Track expanded categories
            categoriesLoading: false,
            totalProductsCount: 0,
            searchQuery: '',                  // Search query
            searchTimeout: null,              // Debounce timeout
            isSearching: false,              // Search loading state
            sortBy: '',                      // Sorting option
            priceRange: {                    // Price range with default values
                min: 0,
                max: 100
            },
            priceTimeout: null,
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 9,
                total: 0,
                from: 0,
                to: 0
            },
            loading: true,
            error: null,
            addToCartLoading: {},
        }
    },
    async mounted() {

        console.log('🔑 Auth prop:', this.auth);
        this.user = this.auth?.user || null;

        try {
            await Promise.all([
                this.fetchProducts(),
                this.fetchCategories(),
                this.fetchFeaturedProducts()
            ]);
        } catch (error) {
            console.error('❌ Error in mounted:', error);
        }
        console.log(this.featuredProducts)
        // Initialize wishlist
        await this.initializeWishlist();
    },
    computed: {
        // Calculate range track fill style
        cartStore() {
            return useCartStore();
        },
        rangeTrackStyle() {
            const min = this.priceRange.min;
            const max = this.priceRange.max;
            const left = (min / 100) * 100;
            const width = ((max - min) / 100) * 100;

            return {
                left: `${left}%`,
                width: `${width}%`
            };
        }
    },
    methods: {
        async fetchProducts(page = 1) {
            try {
                this.loading = true;

                let url = `/api/products?page=${page}`;

                // Add category filter
                if (this.selectedCategoryId) {
                    url += `&category_id=${this.selectedCategoryId}`;
                }
                if (this.searchQuery && this.searchQuery.trim()) {
                    url += `&search=${encodeURIComponent(this.searchQuery.trim())}`;
                }
                if (this.sortBy) {
                    url += `&sort=${this.sortBy}`;
                }
                if (this.priceRange.min > 0) {
                    url += `&price_min=${this.priceRange.min}`;
                }
                if (this.priceRange.max < 100) {
                    url += `&price_max=${this.priceRange.max}`;
                }
                const response = await axios.get(url);
                this.products = response.data.data;
                this.pagination = {
                    current_page: response.data.current_page || 1,
                    last_page: response.data.last_page || 1,
                    per_page: response.data.per_page || 9,
                    total: response.data.total || 0,
                    from: response.data.from || 0,
                    to: response.data.to || 0
                }

                this.totalProductsCount = response.data.total || 0; // Update total products count


            } catch (error) {
                console.error('API Error:', error); // Debug
                this.error = 'Failed to load products: ' + error.message;
                this.products = []; // Ensure products is always an array
            } finally {
                this.loading = false;
                this.isSearching = false; //  ADD: Stop search loading
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get(`/api/categories`);
                this.categories = response.data.data;

            } catch (error) {
                console.error('API Error:', error); // Debug
                this.error = 'Failed to load products: ' + error.message;
                this.products = []; // Ensure products is always an array
            } finally {
                this.loading = false;
            }
        },
        async fetchFeaturedProducts() {
            try {
                const response = await axios.get('/api/products/featured');
                this.featuredProducts = response.data;
            } catch (error) {
                console.error('API Error:', error); // Debug
                this.error = 'Failed to load products: ' + error.message;
                this.featuredProducts = [];
            }
        },
        // ✅ Method to change page
        async changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page && page !== this.pagination.current_page) {
                await this.fetchProducts(page);

                this.$nextTick(() => {
                    document.querySelector('.col-lg-9')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            }
        },

        async filterByCategory(categoryId) {
            console.log('1')
            this.selectedCategoryId = categoryId;
            this.pagination.current_page = 1;
            await this.fetchProducts(1);

            // Scroll to products section
            this.$nextTick(() => {
                document.querySelector('.col-lg-9')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        },

        async clearCategoryFilter() {
            this.selectedCategoryId = null;
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },

        getCategoryIcon(categoryName) {
            const icons = {
                'Fresh Fruits': 'fas fa-apple-alt',
                'Fresh Vegetables': 'fas fa-carrot',
                'Dried Products': 'fas fa-leaf',
                'Jam Products': 'fas fa-prescription-bottle',
                'Berries': 'fas fa-apple-alt',
                'Citrus': 'fas fa-lemon',
                'Stone Fruits': 'fas fa-seedling',
                'Tropical': 'fas fa-umbrella-beach',
                'Others': 'fas fa-box'
            };

            return icons[categoryName] || 'fas fa-apple-alt';
        },

        handleMinRangeChange() {
            // Ensure min doesn't exceed max
            if (parseInt(this.priceRange.min) >= parseInt(this.priceRange.max)) {
                this.priceRange.min = this.priceRange.max - 1;
            }
            this.debouncePriceFilter();
        },

        handleMaxRangeChange() {
            // Ensure max doesn't go below min
            if (parseInt(this.priceRange.max) <= parseInt(this.priceRange.min)) {
                this.priceRange.max = parseInt(this.priceRange.min) + 1;
            }
            this.debouncePriceFilter();
        },

        /**
         * Debounced price filter - chờ user ngừng thay đổi giá rồi mới filter
         * Giảm số lượng API calls khi user kéo slider
         */
        debouncePriceFilter() {
            // Clear timeout cũ nếu có
            if (this.priceTimeout) {
                clearTimeout(this.priceTimeout);
            }

            // Hiển thị loading state
            this.isSearching = true;

            // Set timeout mới - chờ 800ms sau khi user ngừng thay đổi
            this.priceTimeout = setTimeout(async () => {
                try {
                    console.log('🏷️ Applying price filter:', {
                        min: this.priceRange.min,
                        max: this.priceRange.max
                    });

                    await this.applyPriceFilter();
                } catch (error) {
                    console.error('Price filter error:', error);
                    this.error = 'Failed to apply price filter';
                } finally {
                    this.isSearching = false;
                }
            }, 800);
        },

        /**
         * Apply price filter - gọi API với khoảng giá mới
         */
        async applyPriceFilter() {
            // Validate price range
            if (this.priceRange.min < 0) this.priceRange.min = 0;
            if (this.priceRange.max > 100) this.priceRange.max = 100;
            if (this.priceRange.min >= this.priceRange.max) {
                this.priceRange.min = Math.max(0, this.priceRange.max - 1);
            }

            // Reset về trang đầu khi apply filter mới
            this.pagination.current_page = 1;

            // Fetch products với price filter
            await this.fetchProducts(1);

            // Scroll to products section để user thấy kết quả
            this.$nextTick(() => {
                document.querySelector('.col-lg-9')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        },

        async clearPriceFilter() {
            this.priceRange.min = 0;
            this.priceRange.max = 100;
            await this.applyPriceFilter();
        },


        toggleCategory(categoryId) {
            const index = this.expandedCategories.indexOf(categoryId);
            if (index > -1) {
                // Collapse category
                this.expandedCategories.splice(index, 1);
            } else {
                // Expand category
                this.expandedCategories.push(categoryId);
            }
        },

        async goToFirstPage() {
            await this.changePage(1);
        },

        async goToLastPage() {
            await this.changePage(this.pagination.last_page);
        },

        async goToNextPage() {
            await this.changePage(this.pagination.current_page + 1);
        },

        async goToPrevPage() {
            await this.changePage(this.pagination.current_page - 1);
        },

        getVisiblePages() {
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            const pages = [];

            // Calculate start and end
            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);

            // Adjust if we're near the beginning
            if (current <= 3) {
                end = Math.min(last, 5);
            }

            // Adjust if we're near the end
            if (current >= last - 2) {
                start = Math.max(1, last - 4);
            }

            // Generate page numbers
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        },

        // Handle search with debounce
        handleSearch() {
            // Clear existing timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.isSearching = true;

            // Set new timeout for debounced search
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 500); // Wait 500ms after user stops typing
        },

        // Perform search
        async performSearch() {
            console.log('🔍 Searching for:', this.searchQuery);
            this.pagination.current_page = 1; // Reset to first page
            await this.fetchProducts(1);
        },

        //  Clear search
        async clearSearch() {
            this.searchQuery = '';
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },

        // Get category name by ID
        getCategoryName(categoryId) {
            const findCategory = (categories, id) => {
                for (const category of categories) {
                    if (category.id === id) return category.name;
                    if (category.children && category.children.length > 0) {
                        const found = findCategory(category.children, id);
                        if (found) return found;
                    }
                }
                return null;
            };

            return findCategory(this.categories, categoryId) || 'Unknown';
        },

        // Clear all filters
        async clearAllFilters() {
            this.searchQuery = '';
            this.selectedCategoryId = null;
            this.sortBy = '';
            this.priceRange.min = 0;
            this.priceRange.max = 100;
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },

        //  Apply sorting
        async applySorting() {
            this.pagination.current_page = 1; // Reset to first page
            await this.fetchProducts(1);

            // Scroll to products section
            this.$nextTick(() => {
                document.querySelector('.col-lg-9')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        },

        // Get sort label for display
        getSortLabel(sortValue) {
            const labels = {
                'name_asc': this.$t('messages.name_a_z'),
                'name_desc': this.$t('messages.name_z_a'),
                'price_asc': this.$t('messages.price_low_to_high'),
                'price_desc': this.$t('messages.price_high_to_low'),
                'newest': this.$t('messages.newest_first'),
                'oldest': this.$t('messages.oldest_first'),
                'featured': this.$t('messages.featured_first'),
                'popularity': this.$t('messages.most_popular')
            };
            return labels[sortValue] || sortValue;
        },

        //  Clear sorting
        async clearSorting() {
            this.sortBy = '';
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },
        //  Clear price range
        async clearPriceRange() {
            this.priceRange.min = 0;
            this.priceRange.max = 100;
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },
        isInWishlist(productId) {
            console.log('🔍 isInWishlist check:', {
                productId: productId,
                productIdType: typeof productId,
                wishlistIds: this.wishlistIds,
                wishlistIdsTypes: this.wishlistIds.map(id => typeof id),
                result: this.wishlistIds.includes(productId)
            });
            return this.wishlistIds.includes(productId);
        },
        async toggleWishlist(productId) {
            if (this.user) {
                // User is logged in, use database API
                const token = this.csrf_token || (this.$page && this.$page.props && this.$page.props.csrf_token) || '';
                if (!token) {
                    this.showNotification(this.$t('messages.csrf_token_error'), 'error');
                    return;
                }

                if (this.isInWishlist(productId)) {
                    try {
                        await axios.delete(`/api/wishlist/${productId}`, {
                            headers: { 'X-XSRF-TOKEN': token }
                        });
                        this.wishlistIds = this.wishlistIds.filter(id => id !== productId);
                        this.showNotification(this.$t('messages.removed_from_wishlist'), 'success');
                    } catch (error) {
                        this.showNotification(this.$t('messages.removed_from_wishlist_failed'), 'error');
                        console.error(error);
                    }
                } else {
                    try {
                        await axios.post('/api/wishlist', { product_id: productId }, {
                            headers: { 'X-XSRF-TOKEN': token }
                        });
                        this.wishlistIds.push(productId);
                        this.showNotification(this.$t('messages.added_to_wishlist'), 'success');
                    } catch (error) {
                        if (error.response && error.response.status === 409) {
                            this.showNotification(this.$t('messages.product_already_in_wishlist'), 'warning');
                        } else {
                            this.showNotification(this.$t('messages.added_to_wishlist_failed'), 'error');
                        }
                        console.error(error);
                    }
                }
            } else {
                // User not logged in, use session API
                if (this.isInWishlist(productId)) {
                    console.log('Removing from session wishlist:', productId);
                    try {
                        await axios.delete(`/api/session/wishlist`, {
                            data: { product_id: productId }
                        });
                        this.wishlistIds = this.wishlistIds.filter(id => id !== productId);
                        this.showNotification(this.$t('messages.removed_from_wishlist'), 'success');
                    } catch (error) {
                        this.showNotification(this.$t('messages.removed_from_wishlist_failed'), 'error');
                        console.error(error);
                    }
                } else {
                    try {
                        await axios.post('/api/session/wishlist', { product_id: productId });
                        this.wishlistIds.push(productId);
                        this.showNotification(this.$t('messages.added_to_wishlist'), 'success');
                    } catch (error) {
                        this.showNotification(this.$t('messages.added_to_wishlist_failed'), 'error');
                        console.error(error);
                    }
                }
            }
        },
        async initializeWishlist() {
            console.log('Initializing wishlist...');
            if (this.user) {
                console.log('🔐 User is logged in, syncing wishlist from database.');
                // User is logged in, sync session wishlist to database first
                try {
                    await axios.get('/sanctum/csrf-cookie');
                    await axios.post('/api/wishlist/sync');

                    // Get wishlist from database
                    const res = await axios.get('/api/wishlist/ids');
                    this.wishlistIds = res.data.data || [];
                } catch (error) {
                    console.error('❌ Wishlist sync error:', error);
                    this.wishlistIds = [];
                }
            } else {
                console.log('🔓 User not logged in, loading wishlist from session.');
                // User not logged in, get from session (API will handle this)
                try {
                    const res = await axios.get('/api/session/wishlist');

                    // ✅ Extract product IDs from complex data structure
                    const wishlistData = res.data.data || [];
                    this.wishlistIds = wishlistData.map(item => {
                        // Handle the complex object structure
                        if (item['App\\Models\\Product']) {
                            return item['App\\Models\\Product'].id;
                        } else if (item.id) {
                            return item.id;
                        } else if (typeof item === 'number') {
                            return item;
                        }
                        return null;
                    }).filter(id => id !== null);

                    console.log('✅ Session wishlist loaded:', this.wishlistIds);
                } catch (error) {
                    console.error('❌ Session wishlist error:', error);
                    this.wishlistIds = [];
                }
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


    },
}
</script>

<style lang="scss" scoped>

</style>
