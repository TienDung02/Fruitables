<script>
import { Head } from '@inertiajs/vue3';
import Menu from './Frontend/Includes/Menu.vue';
import Search from './Frontend/Includes/Search.vue';
import axios from 'axios';
import  { Link } from '@inertiajs/vue3';
import Footer from "@/Pages/Frontend/Includes/Footer.vue";
export default {
    name: 'Dashboard',
    components: {
        Footer,
        Head,
        Menu,
        Search,
        Link,
    },
    data() {
        return {
            categories: [],
            productsByCategory: {},
            featuredProducts: [],
            bestsellingProducts: [],
            statistics: {
                total_products: 0,
            },
            loading: true,
            activeTab: 'all'
        }
    },
    computed: {

        currentProducts() {
            return this.productsByCategory[this.activeTab] || [];
        }
    },
    methods: {
        async loadDashboardData() {
            try {
                this.loading = true;
                const response = await axios.get('/api/dashboard/data');
                const data = response.data.data;

                this.categories = data.categories || [];
                this.productsByCategory = data.products_by_category || {};
                this.statistics = data.statistics || this.statistics;


            } catch (error) {
                console.error('Error loading dashboard data:', error);
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
        async fetchBestsellingProducts() {
            try {
                const response = await axios.get('/api/products/bestsellers');
                this.bestsellingProducts = response.data.data;
                console.log('bestsellingProducts:', this.bestsellingProducts);
            } catch (error) {
                console.error('API Error:', error); // Debug
                this.error = 'Failed to load products: ' + error.message;
                this.bestsellingProducts = [];
            }
        },
        getProductImage(product) {
            if (product.media && product.media.length > 0) {
                const primaryImage = product.media.find(m => m.is_primary) || product.media[0];
                return primaryImage.file_path;
            }
            return '/images/img/fruite-item-5.jpg';
        },

        getProductPrice(product) {
            if (product.variants && product.variants.length > 0) {
                return product.variants[0];
            }
            return product;
        },

        formatPrice(price) {
            return `$${parseFloat(price).toFixed(2)}`;
        },

        changeTab(tabSlug) {
            console.log('Switching to tab:', tabSlug);
            this.activeTab = tabSlug;
            console.log('Products for this tab:', this.currentProducts.length);
        },

        // Thêm method để debug
        debugCurrentState() {
            console.log('Current tab:', this.activeTab);
            console.log('Available categories:', this.categories);
            console.log('Products by category:', Object.keys(this.productsByCategory));
            console.log('Current products count:', this.currentProducts.length);
        }
    },
    async mounted() {
        try {

            await Promise.all([
                this.loadDashboardData(),
                this.fetchFeaturedProducts(),
                this.fetchBestsellingProducts(),
            ]);
        } catch (error) {
            console.error('❌ Error in mounted:', error);
        }
    }
}
</script>

<template>
    <Head title="Home" />
    <Menu></Menu>

    <!-- Hero Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-12 col-lg-7">
                    <h4 class="mb-3 text-secondary">{{ $t('messages.organic_foods') }}</h4>
                    <h1 class="mb-5 display-3 text-primary">{{ $t('messages.organic_veggies_fruits') }}</h1>
                    <div class="position-relative mx-auto">
                    </div>
                </div>
                <div class="col-md-12 col-lg-5">
                    <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active rounded">
                                <img src="images/img/hero-img-1.png" class="img-fluid w-100 h-100 bg-secondary rounded" alt="First slide">
                                <a href="#" class="btn px-4 py-2 text-white rounded">{{ $t('messages.fruits') }}</a>
                            </div>
                            <div class="carousel-item rounded">
                                <img src="images/img/hero-img-2.jpg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                <a href="#" class="btn px-4 py-2 text-white rounded">{{ $t('messages.vegetables') }}</a>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">{{ $t('messages.previous') }}</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">{{ $t('messages.next') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Statistics Section Start -->
    <div class="container-fluid py-3">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white text-center p-4 rounded">
                        <h3 class="mb-2">{{ statistics.total_products }}</h3>
                        <p class="mb-0">{{ $t('messages.total_products') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white text-center p-4 rounded">
                        <h3 class="mb-2">{{ this.featuredProducts.length }}</h3>
                        <p class="mb-0">{{ $t('messages.featured_products') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white text-center p-4 rounded">
                        <h3 class="mb-2">{{ this.bestsellingProducts.length }}</h3>
                        <p class="mb-0">{{ $t('messages.best_selling') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white text-center p-4 rounded">
                        <h3 class="mb-2">{{ statistics.out_of_stock_count }}</h3>
                        <p class="mb-0">{{ $t('messages.out_of_stock') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Statistics Section End -->

    <!-- Featurs Section Start -->
    <div class="container-fluid featurs py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-car-side fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>{{ $t('messages.free_shipping') }}</h5>
                            <p class="mb-0">{{ $t('messages.free_on_order') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-user-shield fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>{{ $t('messages.security_payment') }}</h5>
                            <p class="mb-0">{{ $t('messages.security_payment_desc') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-exchange-alt fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>{{ $t('messages.day_return') }}</h5>
                            <p class="mb-0">{{ $t('messages.money_guarantee') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fa fa-phone-alt fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>{{ $t('messages.support') }}</h5>
                            <p class="mb-0">{{ $t('messages.support_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Featurs Section End -->

    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="tab-class text-center">
                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>{{ $t('messages.our_organic_products') }}</h1>
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <!-- Tab All Products -->
                            <li class="nav-item">
                                <a
                                    @click.prevent="changeTab('all')"
                                    class="d-flex m-2 py-2 bg-light rounded-pill"
                                    :class="{ 'active': activeTab === 'all' }"
                                    href="#"
                                >
                                    <span class="text-dark" style="width: 130px;">{{ $t('messages.all_products') }}</span>
                                </a>
                            </li>

                            <!-- Tabs cho từng category -->
                            <li class="nav-item" v-for="category in categories" :key="category.id">
                                <a
                                    @click.prevent="changeTab(category.slug)"
                                    class="d-flex m-2 py-2 bg-light rounded-pill"
                                    :class="{ 'active': activeTab === category.slug }"
                                    href="#"
                                >
                                    <span class="text-dark" style="width: 130px;">{{ category.name }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <!-- Hiển thị sản phẩm của tab hiện tại -->
                    <div class="tab-pane fade show active p-0">
                        <div v-if="loading" class="text-center py-5">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ $t('messages.loading') }}</span>
                            </div>
                        </div>

                        <div v-else-if="currentProducts.length === 0" class="text-center py-5">
                            <p class="text-muted">{{ $t('messages.no_products_category') }}</p>
                        </div>

                        <div v-else class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4 col-xl-3" v-for="product in currentProducts" :key="product.id">
                                        <div class="rounded position-relative fruite-item border border-secondary">
                                            <div class="fruite-img border-secondary border-bottom" style="border: 1px solid #000;">
                                                <img :src="getProductImage(product)" class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                                {{ product.category ? product.category.name : $t('messages.product') }}
                                            </div>
                                            <Link :href="route('detail.index', product.id)">
                                                <div class="p-4  border-top-0 rounded-bottom">
                                                    <h4>{{ product.name }}</h4>
                                                    <p>{{ product.short_description || 'Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt' }}</p>
                                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                                        <p class="text-dark fs-5 fw-bold mb-0">
                                                            {{ formatPrice(getProductPrice(product).price || product.price) }} / {{ $t('messages.kg') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
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

    <!-- Featurs Start -->
    <div class="container-fluid service py-5">
        <div class="container py-5">
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-secondary rounded border border-secondary">
                            <img src="images/img/featur-1.jpg" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-primary text-center p-4 rounded">
                                    <h5 class="text-white">{{ $t('messages.fresh_apples') }}</h5>
                                    <h3 class="mb-0">20% {{ $t('messages.off') }}</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-dark rounded border border-dark">
                            <img src="images/img/featur-2.jpg" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-light text-center p-4 rounded">
                                    <h5 class="text-primary">{{ $t('messages.tasty_fruits') }}</h5>
                                    <h3 class="mb-0">{{ $t('messages.free_delivery') }}</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-primary rounded border border-primary">
                            <img src="images/img/featur-3.jpg" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-secondary text-center p-4 rounded">
                                    <h5 class="text-white">{{ $t('messages.exotic_vegetable') }}</h5>
                                    <h3 class="mb-0">{{ $t('messages.discount') }} 30$</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Featurs End -->

    <!-- Vesitable Shop Start-->
    <div class="container-fluid vesitable py-5">
        <div class="container py-5">
            <h1 class="mb-0">{{ $t('messages.fresh_organic_vegetables') }}</h1>
            <div class="owl-carousel vegetable-carousel justify-content-center">
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="images/img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $t('messages.parsely') }}</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">$4.99 / {{ $t('messages.kg') }}</p>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="images/img/vegetable-item-1.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $t('messages.parsely') }}</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">$4.99 / {{ $t('messages.kg') }}</p>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="images/img/vegetable-item-3.png" class="img-fluid w-100 rounded-top bg-light" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $t('messages.banana') }}</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">$7.99 / {{ $t('messages.kg') }}</p>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="images/img/vegetable-item-4.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $t('messages.bell_pepper') }}</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">$7.99 / {{ $t('messages.kg') }}</p>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="images/img/vegetable-item-5.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $t('messages.potatoes') }}</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">$7.99 / {{ $t('messages.kg') }}</p>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="images/img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $t('messages.parsely') }}</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">$7.99 / {{ $t('messages.kg') }}</p>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="images/img/vegetable-item-5.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $t('messages.potatoes') }}</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">$7.99 / {{ $t('messages.kg') }}</p>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="images/img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $t('messages.parsely') }}</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">$7.99 / {{ $t('messages.kg') }}</p>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Vesitable Shop End -->


    <!-- Banner Section Start-->
    <div class="container-fluid banner bg-secondary my-5">
        <div class="container py-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="py-4">
                        <h1 class="display-3 text-white">{{ $t('messages.fresh_exotic_fruits') }}</h1>
                        <p class="fw-normal display-3 text-dark mb-4">{{ $t('messages.in_our_store') }}</p>
                        <p class="mb-4 text-dark">{{ $t('messages.lorem_description') }}</p>
                        <a href="#" class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">{{ $t('messages.buy') }}</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="images/img/baner-1.png" class="img-fluid w-100 rounded" alt="">
                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute" style="width: 140px; height: 140px; top: 0; left: 0;">
                            <h1 style="font-size: 100px;">1</h1>
                            <div class="d-flex flex-column">
                                <span class="h2 mb-0">50$</span>
                                <span class="h4 text-muted mb-0">{{ $t('messages.kg') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner Section End -->


    <!-- Bestsaler Product Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h1 class="display-4">{{ $t('messages.bestseller_products') }}</h1>
                <p>{{ $t('messages.latin_description') }}</p>
            </div>
            <div class="row g-4 fruite">
                <div v-for="bestsellingProduct in bestsellingProducts" :key="bestsellingProduct.id" class="col-lg-6 col-xl-4  ">
                    <div class="p-4 rounded bg-light fruite-item" style="z-index: 1;">
                        <Link :href="route('detail.index', bestsellingProduct.id)" class="d-block h-100 w-100">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <img
                                        :src="`/${bestsellingProduct.media?.find(m => m.is_primary)?.file_path || bestsellingProduct.media?.[0]?.file_path || 'products/default.jpg'}`"
                                        class="img-fluid rounded-circle w-100 object-fit-cover fruite-img" alt="">
                                </div>
                                <div class="col-6">
                                    <a href="#" class="h5">{{ bestsellingProduct.name }}</a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div v-if="bestsellingProduct.variants[0].sale_price" class="d-flex mb-2 align-items-center">
                                        <h5  class=" me-2 mb-0">${{ bestsellingProduct.variants[0].sale_price }} / {{ bestsellingProduct.variants[0].size }}</h5>
                                        <h6 class="text-danger text-decoration-line-through mb-0">${{ bestsellingProduct.variants[0].price }} / {{ bestsellingProduct.variants[0].size }}</h6>
                                    </div>
                                    <div v-else class="d-flex mb-2">
                                        <h5  class=" me-2">${{ bestsellingProduct.variants[0].price }} / {{ bestsellingProduct.variants[0].size }}</h5>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h1 class="display-4">{{ $t('messages.featured_products') }}</h1>
                <p>{{ $t('messages.latin_description') }}</p>
            </div>
            <div class="row g-4 fruite">
                <div v-for="featuredProduct in featuredProducts" :key="featuredProduct.id" class="col-md-6 col-lg-6 col-xl-3 ">
                    <div class="text-center h-100  border border-secondary border-radius-10" >
                        <div class="fruite-item h-auto position-relative">
                            <img
                                :src="`/${featuredProduct.media?.find(m => m.is_primary)?.file_path || featuredProduct.media?.[0]?.file_path || 'products/default.jpg'}`"
                                class="w-100 rounded-top object-fit-cover fruite-img" alt="" style="border: 1px solid #ffb524 !important">
                        </div>
                        <Link :href="route('detail.index', featuredProduct.id)">
                            <div class="py-4">
                                <a href="#" class="h5">{{ featuredProduct.name || 'Fruits' }}</a>
                                <div class="d-flex my-3 justify-content-center">
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <div v-if="featuredProduct.variants[0].sale_price" class="d-flex mb-2 align-items-center justify-content-center">
                                    <h5  class=" me-2 mb-0">${{ featuredProduct.variants[0].sale_price }} / {{ featuredProduct.variants[0].size }}</h5>
                                    <h6 class="text-danger text-decoration-line-through mb-0">${{ featuredProduct.variants[0].price }} / {{ featuredProduct.variants[0].size }}</h6>
                                </div>
                                <div v-else class="d-flex mb-2 justify-content-center">
                                    <h5  class=" me-2">${{ featuredProduct.variants[0].price }} / {{ featuredProduct.variants[0].size }}</h5>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bestsaler Product End -->


    <!-- Fact Start -->
    <div class="container-fluid py-5">

        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>satisfied customers</h4>
                            <h1>1963</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>quality of service</h4>
                            <h1>99%</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>quality certificates</h4>
                            <h1>33</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Available Products</h4>
                            <h1>789</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact Start -->

    <Footer>
    </Footer>

    <!-- Copyright End -->
    <!--    </AuthenticatedLayout>-->
</template>
