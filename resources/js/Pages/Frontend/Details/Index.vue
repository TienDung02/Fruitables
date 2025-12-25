<template>
    <Head :title="$t('messages.products')"/>
    <Menu></Menu>
    <Search></Search>

    <div>
        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">{{ $t('messages.shop_detail') }}</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#">{{ $t('messages.home') }}</a></li>
                <li class="breadcrumb-item"><a href="#">{{ $t('messages.pages') }}</a></li>
                <li class="breadcrumb-item active text-white">{{ $t('messages.shop_detail') }}</li>
            </ol>
        </div>
        <!-- Single Page Header End -->


        <!-- Single Product Start -->
        <div class="container-fluid py-5 mt-5">
            <div class="container py-5">
                <div class="row g-4 mb-5">
                    <div class="col-lg-8 col-xl-9">
                        <div class="row g-4">
                            <div class="col-lg-6 aspect-ratio">
                                <!-- Swiper slideshow cho hình ảnh sản phẩm -->
                                <Swiper
                                    :slides-per-view="1"
                                    :navigation="true"
                                    :pagination="true"
                                    :modules="modules"
                                    class="productSwiper border rounded w-100 h-100"
                                    style="height:100%"
                                >
                                    <SwiperSlide v-for="(media, idx) in data.media" :key="idx">
                                        <a href="#" class="w-100 h-100">
                                            <img
                                                :alt="data.name"
                                                :src="`/${media.file_path}`"
                                                class="img-fluid rounded w-100 h-100 object-fit-cover"
                                            >
                                        </a>
                                    </SwiperSlide>
                                </Swiper>
                                <!-- End Swiper slideshow -->
                            </div>
                            <div class="col-lg-6">
                                <h4 class="fw-bold mb-3">{{ data.name }}</h4>
                                <p class="mb-3 fs-5">{{ $t('messages.category') }}: {{ data.category.name }}</p>
                                <h3 class="fw-bold mb-3">
                                    <div v-if="this.sale_price">
                                        <span class="text-danger">${{ this.sale_price }}</span>
                                        &nbsp;
                                        <span class="text-decoration-line-through opacity-75 fs-4">${{ this.price }}</span>
                                    </div>
                                    <span v-else>${{ this.price }}</span>
                                </h3>
                                <div class="d-flex mb-4">
                                    <i class="fa fa-star text-secondary"></i>
                                    <i class="fa fa-star text-secondary"></i>
                                    <i class="fa fa-star text-secondary"></i>
                                    <i class="fa fa-star text-secondary"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                                <p class="mb-5">{{ data.short_description }}</p>
                                <div class="d-flex mb-4 ">
                                    <p class="mb-3">{{ $t('messages.size') }}</p>
                                    <ul  class="d-flex ps-0">
                                        <li
                                            v-for="variant in data.variants"
                                            :key="variant.id"
                                            class="mb-2 list-size-product"
                                            @click="onclickSize(variant.id)"
                                            :class="{ active: variant.id === selectedVariantId }">
                                            {{ variant.size }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="d-flex">
                                    <p class="mb-3">{{ $t('messages.quantity') }}</p>
                                    <div class="input-group quantity mb-5 ms-4" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-minus rounded-circle bg-light border" @click="decreaseQuantity">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 p-0" v-model.number="quantity">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-plus rounded-circle bg-light border" @click="increaseQuantity">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button class="btn border border-secondary rounded-pill px-3 text-primary me-4" @click="addToCart()">
                                    <i class="fa fa-shopping-bag me-2 text-primary"></i>
                                    <span>{{ $t('messages.add_to_cart') }}</span>
                                </button>

                                <button class="btn border border-secondary rounded-pill px-3 text-primary" @click="buyNow()">
                                    <i class="fa fa-shopping-bag me-2 text-primary"></i>
                                    <span>{{ $t('messages.buy_now') }}</span>
                                </button>
                            </div>
                            <div class="col-lg-12">
                                <nav>
                                    <div class="nav nav-tabs mb-3">
                                        <button class="nav-link active border-white border-bottom-0" type="button" role="tab"
                                                id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                                aria-controls="nav-about" aria-selected="true">{{ $t('messages.description') }}</button>
                                        <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                                id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                                aria-controls="nav-mission" aria-selected="false">{{ $t('messages.reviews') }}</button>
                                    </div>
                                </nav>
                                <div class="tab-content mb-5">
                                    <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                        <p>{{ data.description }}</p>

                                        <div class="px-2">
                                            <div class="row g-4">
                                                <div class="col-6">
                                                    <div class="row bg-light align-items-center text-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $t('messages.weight') }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">1 kg</p>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center align-items-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $t('messages.country_of_origin') }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $t('messages.agro_farm') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row bg-light text-center align-items-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $t('messages.quality') }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $t('messages.organic') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center align-items-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $t('messages.check') }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $t('messages.healthy') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row bg-light text-center align-items-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $t('messages.min_weight') }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">250 Kg</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                                        <div class="d-flex">
                                            <img src="img/avatar.jpg" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                            <div class="">
                                                <p class="mb-2" style="font-size: 14px;">April 12, 2024</p>
                                                <div class="d-flex justify-content-between">
                                                    <h5>Jason Smith</h5>
                                                    <div class="d-flex mb-3">
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <p>The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic
                                                    words etc. Susp endisse ultricies nisi vel quam suscipit </p>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <img src="img/avatar.jpg" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                            <div class="">
                                                <p class="mb-2" style="font-size: 14px;">April 12, 2024</p>
                                                <div class="d-flex justify-content-between">
                                                    <h5>Sam Peters</h5>
                                                    <div class="d-flex mb-3">
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <p class="text-dark">The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic
                                                    words etc. Susp endisse ultricies nisi vel quam suscipit </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="nav-vision" role="tabpanel">
                                        <p class="text-dark">Tempor erat elitr rebum at clita. Diam dolor diam ipsum et tempor sit. Aliqu diam
                                            amet diam et eos labore. 3</p>
                                        <p class="mb-0">Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam et eos labore.
                                            Clita erat ipsum et lorem et sit</p>
                                    </div>
                                </div>
                            </div>
                            <form action="#">
                                <h4 class="mb-5 fw-bold">{{ $t('messages.leave_a_reply') }}</h4>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="border-bottom rounded">
                                            <input type="text" class="form-control border-0 me-4" :placeholder="$t('messages.your_name') + ' *'">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="border-bottom rounded">
                                            <input type="email" class="form-control border-0" :placeholder="$t('messages.your_email') + ' *'">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="border-bottom rounded my-4">
                                            <textarea name="" id="" class="form-control border-0" cols="30" rows="8" :placeholder="$t('messages.your_review') + ' *'" spellcheck="false"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="d-flex justify-content-between py-3 mb-5">
                                            <div class="d-flex align-items-center">
                                                <p class="mb-0 me-3">{{ $t('messages.please_rate') }}:</p>
                                                <div class="d-flex align-items-center" style="font-size: 12px;">
                                                    <i class="fa fa-star text-muted"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                            <a href="#" class="btn border border-secondary text-primary rounded-pill px-4 py-3">{{ $t('messages.post_comment') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xl-3">
                        <div class="row g-4 fruite">
                            <div class="col-lg-12">
                                <h4 class="mb-4">{{ $t('messages.featured_products') }}</h4>
                                <div  v-for="featuredProduct in featuredProducts" :key="featuredProduct.id" class="d-flex align-items-center justify-content-start mb-5">
                                    <div class="rounded me-3" style="width: 100px; height: 100px;">
                                        <img
                                            :src="`/${featuredProduct.media?.find(m => m.is_primary)?.file_path || featuredProduct.media?.[0]?.file_path || 'products/default.jpg'}`"
                                            alt=""
                                            class="img-fluid rounded h-100" style="object-fit: contain">
                                    </div>
                                    <div>
                                        <h5 class="mb-2">{{featuredProduct.name}}</h5>
                                        <div class="d-flex mb-2">
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <h5 class="fw-bold mb-3">
                                                <div v-if="featuredProduct.sale_price">
                                                    <span class="text-danger">${{ featuredProduct.variants[0].sale_price }}</span>
                                                    &nbsp;
                                                    <span class="text-decoration-line-through opacity-75 fs-4">${{ featuredProduct.variants[0].price }}</span>
                                                </div>
                                                <span v-else>${{ featuredProduct.variants[0].price }}</span>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="position-relative">
                                    <img src="/images/img/banner-fruits.jpg" class="img-fluid w-100 rounded" alt="">
                                    <div class="position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%);">
                                        <h3 class="text-secondary fw-bold">{{ $t('messages.fresh_fruits_banner').replace(' ', '<br>').replace(' ', '<br>') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h1 class="fw-bold mb-0">{{ $t('messages.related_products') }}</h1>
                <div class="vesitable">
                    <div class="owl-carousel vegetable-carousel justify-content-center">
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <img src="/images/img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $t('messages.parsely') }}</h4>
                                <p>{{ $t('messages.lorem_ipsum_text') }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <img src="/images/img/vegetable-item-1.jpg" class="img-fluid w-100 rounded-top" alt="">
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $t('messages.parsely') }}</h4>
                                <p>{{ $t('messages.lorem_ipsum_text') }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <img src="/images/img/vegetable-item-3.png" class="img-fluid w-100 rounded-top bg-light" alt="">
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $t('messages.banana') }}</h4>
                                <p>{{ $t('messages.lorem_ipsum_text') }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <img src="/images/img/vegetable-item-4.jpg" class="img-fluid w-100 rounded-top" alt="">
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $t('messages.bell_pepper') }}</h4>
                                <p>{{ $t('messages.lorem_ipsum_text') }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <img src="/images/img/vegetable-item-5.jpg" class="img-fluid w-100 rounded-top" alt="">
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $t('messages.potatoes') }}</h4>
                                <p>{{ $t('messages.lorem_ipsum_text') }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <img src="/images/img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $t('messages.parsely') }}</h4>
                                <p>{{ $t('messages.lorem_ipsum_text') }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <img src="/images/img/vegetable-item-5.jpg" class="img-fluid w-100 rounded-top" alt="">
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $t('messages.potatoes') }}</h4>
                                <p>{{ $t('messages.lorem_ipsum_text') }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="border border-primary rounded position-relative vesitable-item">
                            <div class="vesitable-img">
                                <img src="/images/img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                            </div>
                            <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">{{ $t('messages.vegetable') }}</div>
                            <div class="p-4 pb-0 rounded-bottom">
                                <h4>{{ $t('messages.parsely') }}</h4>
                                <p>{{ $t('messages.lorem_ipsum_text') }}</p>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> {{ $t('messages.add_to_cart') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Single Product End -->


        <!-- Footer Start -->
        <Footer />
        <!-- Footer End -->
    </div>
</template>

<script>
import Menu from "@/Pages/Frontend/Includes/Menu.vue";
import Search from "@/Pages/Frontend/Includes/Search.vue";
import {Head, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Navigation, Pagination } from 'swiper/modules';
import axios from "axios";
import { useCartStore } from '@/stores/cart';
import Swal from 'sweetalert2';
import Footer from '@/Pages/Frontend/Includes/Footer.vue';
export default {
    components: {
        Search,
        Menu,
        Head,
        Swiper,
        SwiperSlide,
        Footer,
    },
    computed: {
        // Calculate range track fill style
        cartStore() {
            return useCartStore();
        },
    },
    props: {
        data: {
            type: Object,
            required: true,

        },
        auth: {
            type: Object,
            required: true,
        },
        csrf_token: {
            type: String,
            required: true,
        }
    },
    data (){
        return {
            modules: [Navigation, Pagination],
            price: 0,
            sale_price: 0,
            selectedVariantId: 0,
            quantity: 1,
            minQuantity: 1,
            maxQuantity: 10,
            featuredProducts: [],
            addToCartLoading: {},
        };
    },
    async mounted() {
        this.user = this.auth?.user || null;

        this.price = this.data.variants[0].price;
        this.sale_price = this.data.variants[0].sale_price;
        this.selectedVariantId = this.data.variants[0].id;
        console.log('array:', this.data);
        console.log('selectedVariantId:', this.selectedVariantId);

        try {
            await Promise.all([
                // this.onclickSize(variantId),
                this.fetchFeaturedProducts(),
            ]);
        } catch (error) {
            console.error('❌ Error in mounted:', error);
        }
    },
    methods: {
        async onclickSize(variantId) {
            console.log(variantId)
            for (const variant of this.data.variants) {
                if (variant.id === variantId) {
                    this.price = variant.price;
                    this.sale_price = variant.sale_price;
                    break;
                }
            }
            this.selectedVariantId = variantId;
        },
        async fetchFeaturedProducts() {
            try {
                const response = await axios.get('/api/products/featured');
                this.featuredProducts = response.data;
                console.log('Featured Products:', this.featuredProducts); // Debug
            } catch (error) {
                console.error('API Error:', error); // Debug
                this.error = 'Failed to load products: ' + error.message;
                this.featuredProducts = [];
            }
        },
        async addToCart() {
            try {
                if (this.user) {
                    await this.cartStore.addToCart(this.selectedVariantId, this.quantity);
                    this.showNotification(this.$t('messages.product_added_to_cart_success'), 'success');
                } else {
                    // User not logged in, add to session
                    await axios.post('/api/session/cart', {
                        productVariant_id: this.selectedVariantId,
                        quantity: this.quantity
                    });

                    // Update cart count
                    await this.cartStore.fetchCartCount();
                    this.showNotification(this.$t('messages.product_added_to_cart_success'), 'success');
                }
            } catch (error) {
                console.error('Lỗi khi thêm vào giỏ hàng:', error);
                this.showNotification(this.$t('messages.add_to_cart_failed'), 'error');
            }
        },
        async buyNow() {
            console.log('this this.selectedVariantId', this.selectedVariantId)
            const response_cartDraft = await axios.post('/api/session/cart/draft', {
                variantId: this.selectedVariantId,
                quantity: this.quantity
            });


            console.log('Checkout response_cartDraft:', response_cartDraft.data);

            try {
                this.loading = true;
                // Đảm bảo có CSRF token
                await axios.get('/sanctum/csrf-cookie');

                // console.log('Sending checkout request with items:', selectedItems);
                console.log('User authentication status:', this.auth?.user ? 'authenticated' : 'guest');

                const checkoutUrl = this.auth?.user
                    ? '/api/cart/checkout'        // User đã đăng nhập
                    : '/api/session/cart/checkout'; // User chưa đăng nhập

                // Gửi API với chỉ những sản phẩm đã chọn
                const response = await axios.post(checkoutUrl, {
                    items: response_cartDraft.data
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Checkout response:', response.data);

                if (!response.data.success) {
                    this.showNotification(response.data.message || this.$t('messages.error_creating_order'), 'error');
                    return;
                }

                const checkoutId = response.data.checkout_id;
                console.log('checkoutId:', checkoutId);
                console.log('User type:', response.data.user_type || 'session-based');

                if (!checkoutId) {
                    this.showNotification(this.$t('messages.cannot_get_order_code'), 'error');
                    return;
                }

                // Chỉ chuyển hướng nếu checkoutId hợp lệ
                this.$inertia.visit(`/checkout/${checkoutId}`);

            } catch (error) {
                console.error("Lỗi khi tạo đơn hàng:", error);

                if (error.response) {
                    console.error("Error response:", error.response.data);
                    console.error("Error status:", error.response.status);
                    console.error("Error headers:", error.response.headers);

                    if (error.response.status === 405) {
                        this.showNotification(this.$t('messages.method_not_supported_error'), 'error');
                    } else if (error.response.status === 401) {
                        this.showNotification(this.$t('messages.session_expired_error'), 'error');
                    } else {
                        this.showNotification(error.response.data.message || this.$t('messages.general_error_try_again'), 'error');
                    }
                } else {
                    this.showNotification(this.$t('messages.general_error_try_again'), 'error');
                }
            } finally {
                this.loading = false;
            }
        },
        decreaseQuantity() {
            if (this.quantity > this.minQuantity) {
                this.quantity--;
                console.log('quantity:', this.quantity);
            }
        },
        increaseQuantity() {
            if (this.quantity < this.maxQuantity) {
                this.quantity++;
                console.log('quantity:', this.quantity);
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
                timer: 1500
            });
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
