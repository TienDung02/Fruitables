


<template>
    <Head title="Products" />
    <Menu></Menu>
    <Search></Search>

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
            <h1 class="mb-4">Fresh fruits shop</h1>
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <div class="col-xl-3">
                            <div class="input-group w-100 mx-auto d-flex">
                                <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                                <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                        <div class="col-6"></div>
                        <div class="col-xl-3">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                <label for="fruits">Default Sorting:</label>
                                <select id="fruits" name="fruitlist" class="border-0 form-select-sm bg-light me-3" form="fruitform">
                                    <option value="volvo">Nothing</option>
                                    <option value="saab">Popularity</option>
                                    <option value="opel">Organic</option>
                                    <option value="audi">Fantastic</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Categories</h4>
                                        <ul class="list-unstyled fruite-categorie">
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="#"><i class="fas fa-apple-alt me-2"></i>Apples</a>
                                                    <span>(3)</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="#"><i class="fas fa-apple-alt me-2"></i>Oranges</a>
                                                    <span>(5)</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="#"><i class="fas fa-apple-alt me-2"></i>Strawbery</a>
                                                    <span>(2)</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="#"><i class="fas fa-apple-alt me-2"></i>Banana</a>
                                                    <span>(8)</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="#"><i class="fas fa-apple-alt me-2"></i>Pumpkin</a>
                                                    <span>(5)</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4 class="mb-2">Price</h4>
                                        <input type="range" class="form-range w-100" id="rangeInput" name="rangeInput" min="0" max="500" value="0" oninput="amount.value=rangeInput.value">
                                        <output id="amount" name="amount" min-velue="0" max-value="500" for="rangeInput">0</output>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Additional</h4>
                                        <div class="mb-2">
                                            <input type="radio" class="me-2" id="Categories-1" name="Categories-1" value="Beverages">
                                            <label for="Categories-1"> Organic</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="radio" class="me-2" id="Categories-2" name="Categories-1" value="Beverages">
                                            <label for="Categories-2"> Fresh</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="radio" class="me-2" id="Categories-3" name="Categories-1" value="Beverages">
                                            <label for="Categories-3"> Sales</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="radio" class="me-2" id="Categories-4" name="Categories-1" value="Beverages">
                                            <label for="Categories-4"> Discount</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="radio" class="me-2" id="Categories-5" name="Categories-1" value="Beverages">
                                            <label for="Categories-5"> Expired</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <h4 class="mb-3">Featured products</h4>
                                    <div v-for="featuredProduct in featuredProducts" :key="featuredProduct.id">
                                        <div class="d-flex align-items-center justify-content-start mb-2">
                                            <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                <img :src="`/${featuredProduct.media?.find(m => m.is_primary)?.file_path || featuredProduct.media?.[0]?.file_path || 'products/default.jpg'}`" style="object-fit: contain"
                                                     class="img-fluid rounded h-100" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-2">{{ featuredProduct.name || 'Fruits' }}</h6>
                                                <div class="d-flex mb-2">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <h5 class="fw-bold me-2">2.99 $</h5>
                                                    <h5 class="text-danger text-decoration-line-through">4.11 $</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


<!--                                    <div class="d-flex justify-content-center my-4">-->
<!--                                        <a href="#" class="btn border border-secondary px-4 py-3 rounded-pill text-primary w-100">Vew More</a>-->
<!--                                    </div>-->
                                </div>
                                <div class="col-lg-12">
                                    <div class="position-relative">
                                        <img src="images/img/banner-fruits.jpg" class="img-fluid w-100 rounded" alt="">
                                        <div class="position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%);">
                                            <h3 class="text-secondary fw-bold">Fresh <br> Fruits <br> Banner</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
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
                                    <div v-for="product in products" :key="product.id"
                                         class="col-md-6 col-lg-6 col-xl-4">
                                        <div class="rounded position-relative fruite-item ">
                                            <div class="fruite-img border-secondary" style="border: 1px solid #000;">
                                                <img  :src="`/${product.media?.find(m => m.is_primary)?.file_path || product.media?.[0]?.file_path || 'products/default.jpg'}`"
                                                      class="img-fluid w-100 rounded-top"
                                                      :alt="product.name"
                                                >
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                 style="top: 10px; left: 10px;">
                                                {{ product.category?.name || 'Fruits' }}
                                            </div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>{{ product.name }}</h4>
                                                <p>{{ product.description?.substring(0, 100) || 'Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt' }}...</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">
                                                        <span v-if="product.sale_price">${{ product.sale_price }}</span>
                                                        <span v-else>${{ product.price }}</span>
                                                        / kg
                                                    </p>
                                                    <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Pagination -->
                                <div class="col-12" v-if="!loading && !error && Array.isArray(products) && products.length > 0 && pagination.last_page > 1">
                                    <div class="pagination d-flex justify-content-center mt-5">

                                        <!-- ✅ Previous Button -->
                                        <a href="#"
                                           @click.prevent="goToPrevPage()"
                                           :class="['rounded', 'me-1', { 'disabled text-muted': pagination.current_page === 1 }]"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            &laquo;
                                        </a>

                                        <!-- ✅ First Page -->
                                        <a href="#"
                                           v-if="pagination.current_page > 4"
                                           @click.prevent="goToFirstPage()"
                                           class="rounded me-1"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            First
                                        </a>

                                        <!-- ✅ Dots if needed -->
                                        <span v-if="pagination.current_page > 4"
                                              class="rounded me-1"
                                              style="padding: 8px 12px; border: 1px solid #ddd;">
            ...
        </span>

                                        <!-- ✅ Page Numbers (show 5 pages around current) -->
                                        <template v-for="page in getVisiblePages()" :key="page">
                                            <a href="#"
                                               @click.prevent="changePage(page)"
                                               :class="['rounded', 'me-1', { 'active bg-primary text-white': page === pagination.current_page }]"
                                               style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                                {{ page }}
                                            </a>
                                        </template>

                                        <!-- ✅ Dots if needed -->
                                        <span v-if="pagination.current_page < pagination.last_page - 3"
                                              class="rounded me-1"
                                              style="padding: 8px 12px; border: 1px solid #ddd;">
            ...
        </span>

                                        <!-- ✅ Last Page -->
                                        <a href="#"
                                           v-if="pagination.current_page < pagination.last_page - 3"
                                           @click.prevent="goToLastPage()"
                                           class="rounded me-1"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            {{ pagination.last_page }}
                                        </a>

                                        <!-- ✅ Next Button -->
                                        <a href="#"
                                           @click.prevent="goToNextPage()"
                                           :class="['rounded', { 'disabled text-muted': pagination.current_page === pagination.last_page }]"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            &raquo;
                                        </a>
                                    </div>

                                    <!-- ✅ Pagination Info -->
                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }}
                                            of {{ pagination.total || 0 }} products
                                            (Page {{ pagination.current_page }} of {{ pagination.last_page }})
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
                            <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="Your Email">
                            <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Subscribe Now</button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-end pt-3">
                            <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
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
                    Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a> Distributed By <a class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Menu from '../Includes/Menu.vue';
import Search from '../Includes/Search.vue';
import axios  from "axios";
export default {
    components: {
        AuthenticatedLayout,
        Menu,
        Head,
        Search,
    },
    data(){
        return {
            products: [],
            featuredProducts: [],
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
        }
    },
    async mounted(){
        try {
            await Promise.all([
                this.fetchProducts(),
                this.fetchFeaturedProducts()
            ]);
        } catch (error) {
            console.error('❌ Error in mounted:', error);
        }
    },
    methods: {
        async fetchProducts(page = 1) {
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/products?page=${page}`);
                this.products = response.data.data;

                this.pagination = {
                    current_page: response.data.current_page || 1,
                    last_page: response.data.last_page || 1,
                    per_page: response.data.per_page || 9,
                    total: response.data.total || 0,
                    from: response.data.from || 0,
                    to: response.data.to || 0
                }

                console.log(response.data)

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
                const response = await axios.get('http://127.0.0.1:8000/api/products/featured');
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

                // ✅ Scroll to top of products section
                this.$nextTick(() => {
                    document.querySelector('.col-lg-9')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            }
        },

        // ✅ Helper methods for pagination
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
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
