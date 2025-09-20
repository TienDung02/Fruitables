<template>
    <Head title="Cart"/>
    <Menu></Menu>
    <Search></Search>
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Cart</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Cart</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

        <!-- Cart Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div v-if="cartItems.length === 0" class="alert alert-warning">Giỏ hàng của bạn đang trống.</div>
                <div v-else>
                    <div class="table-responsive">
                        <table class="table">
                            <colgroup>
                                <col width="100">
                                <col width="200">
                                <col >
                                <col width="150">
                                <col width="150">
                                <col width="180">
                                <col width="180">
                                <col width="100">
                            </colgroup>
                            <thead>
                            <tr>
                                <th scope="col" class="text-center">
                                    <input
                                        type="checkbox" class="form-check-input"
                                        :checked="isAllSelected"
                                        @change="toggleAllSelection"
                                    />
                                </th>
                                <th scope="col">Products</th>
                                <th scope="col">Name</th>
                                <th scope="col">size</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Total</th>
                                <th scope="col">Handle</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in cartItems" :key="item.id">
                                <td class="position-relative" v-if="item.selected === 1">
                                    <input type="checkbox" class="center-position bg-primary form-check-input"  checked @click="handleCheckboxItem(item)">
                                </td>
                                <td class="position-relative" v-else>
                                    <input type="checkbox" class="center-position" @click="handleCheckboxItem(item)">
                                </td>
                                <th scope="row">
                                    <div class="d-flex align-items-center">
                                        <img
                                            :alt="item.product_variant.product.name"
                                            :src="`/${item.product_variant.product.media?.find(m => m.is_primary)?.file_path || item.product_variant.media?.[0]?.file_path || 'products/default.jpg'}`"
                                            class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px; object-fit: contain" >
                                    </div>
                                </th>
                                <td>
                                    <p class="mb-0 mt-4">{{ item.product_variant.product.name }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">{{ item.product_variant.size == 'kg' ? '1kg' :  item.product_variant.size}}</p>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">${{  item.product_variant.sale_price ?? item.product_variant.price }}</p>
                                </td>
                                <td>
                                    <div class="input-group quantity mt-4" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-minus rounded-circle bg-light border" @click="handleQuantityChange(item, -1)">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 p-0" :value="item.quantity">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-plus rounded-circle bg-light border" @click="handleQuantityChange(item, 1)">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">${{  (item.product_variant.price * item.quantity).toFixed(2) }}</p>
                                </td>
                                <td>
                                    <button class="btn btn-md rounded-circle bg-light border mt-4" @click="handleDeleteItem(item)">
                                        <i class="fa fa-times text-danger"></i>
                                    </button>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class=" g-4 mt-5 justify-content-end">
                    <div class=" border-top">
                        <input type="text" class="border-0 border-bottom rounded me-5 py-3 mb-2" placeholder="Coupon Code" style=" text-indent: 15px;w">
                        <button class="btn border-secondary rounded-pill px-4 py-2 my-2 text-primary" type="button">Apply Coupon</button>
                    </div>
                    <div class="row d-flex rounded bg-light">
                        <div class="col-7 d-flex">
                            <div class="d-flex align-items-center ms-4 me-4">
                                <input class="fs-5 form-check-input mt-0" style="font-size: 20px;"
                                    type="checkbox"
                                    :checked="isAllSelected"
                                    @change="toggleAllSelection"
                                />
                                <h5 class="mb-0 ms-3">Select all ({{this.productSelected}})</h5>
                            </div>
                            <div class="d-flex align-items-center ms-4 me-4 fs-5">Delete</div>
                            <div class="d-flex align-items-center ms-4 me-4 fs-5">Move to wishlist</div>
                        </div>
                        <div class="col-5 rounded d-flex row">
                            <div class="col-6">
                                <div class="p-2">
                                    <div class="d-flex justify-content-between ">
                                        <h5 class="mb-0 me-4">Total ({{ this.productSelected }} items):</h5>
                                        <p class="mb-0">${{ this.totalPriceSelected }}</p>
                                    </div>
                                </div>
                                <div class="py-2  border-top d-flex justify-content-end">
                                    <p class="mb-0 ps-4 me-4">Saved:</p>
                                    <p class="mb-0 pe-4">${{ totalSave }}</p>
                                </div>
                            </div>
                            <div class="col-1"></div>
                            <button class="col-5 btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase my-3" @click="handleCheckout()" type="button">Proceed Checkout</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart Page End -->


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
                            <img src="img/payment.png" class="img-fluid" alt="">
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



        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>
</template>

<script>
import {Head} from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import Menu from '../Includes/Menu.vue';
import Search from '../Includes/Search.vue';
import axios from 'axios';
import { useCartStore } from '@/stores/cart';
import { Checkbox } from '@/Components/ui/checkbox/index.js';
axios.defaults.withCredentials = true;
export default {
    components: {
        Checkbox,
        Menu,
        Head,
        Search,
    },
    props: {
        auth: Object,
    },
    setup(){

    },
    data() {
        return {
            cartItems: [],
            cartTotal: 0,
            cartCount: 0,
            productSelected: 0,
            totalPriceSelected: 0,
            totalSave: 0,
            loading: true,
            error: null,
        };
    },
    computed: {
        // Calculate range track fill style
        cartStore() {
            return useCartStore();
        },
        isAllSelected() {
            return this.cartItems.every(item => item.selected === 1);
        }
    },
        async mounted() {
            // if (this.auth && this.auth.user) {
            //     await this.syncCart();
            // }

            await axios.get('/sanctum/csrf-cookie');
            await this.fetchCart();
            await this.getTotalPriceSelected();
        },
        methods: {
            async fetchCart() {
                this.loading = true;
                try {
                    let res;
                    if (this.auth && this.auth.user) {
                        // User is logged in, get from database
                        res = await axios.get('/api/cart');
                    } else {
                        // User not logged in, get from session
                        res = await axios.get('/api/session/cart');
                    }

                    const data = res.data?.data;
                    const itemsFromAPI = data?.items || [];

                    // Lấy selected từ API, không gán mặc định 1
                    this.cartItems = itemsFromAPI;
                    console.log('cartItems', this.cartItems);
                    this.cartTotal = data?.total || 0;
                    this.cartCount = data?.count || 0;

                    this.cartStore.setCartCount(this.cartCount);
                    this.cartStore.setCartItems(this.cartItems);

                } catch (error) {
                    this.error = 'Không thể lấy dữ liệu giỏ hàng!';
                } finally {
                    this.loading = false;
                }
            },

        async toggleAllSelection() {
            const newSelectedValue = this.isAllSelected ? 0 : 1;
            for (const item of this.cartItems) {
                item.selected = newSelectedValue;
                if (!this.auth || !this.auth.user) {
                    // Cập nhật selected vào session
                    await axios.put('/api/session/cart', {
                        productVariant_id: item.productVariant_id,
                        quantity: item.quantity,
                        selected: item.selected
                    });
                }
            }
            await this.getTotalPriceSelected();
        },

        getTotalPriceSelected() {
            console.log('this.cartitem', this.cartItems);
            this.totalPriceSelected = this.cartItems
                .filter(item => item.selected === 1)
                .reduce((total, item) => total + item.product_variant.price * item.quantity, 0)
                .toFixed(2);

            const salePrice = this.cartItems
                .filter(item => item.selected === 1)
                .reduce((total, item) => total + (item.product_variant.sale_price ?? item.product_variant.price) * item.quantity, 0)
                .toFixed(2);

            this.productSelected = this.cartItems
                .filter(item => item.selected === 1).length;

            this.totalSave = this.formatPrice(this.totalPriceSelected - salePrice);
        },

        formatPrice(price) {
            if (price === undefined || price === null) {
                return '$0.00';
            }
            return `${price.toFixed(2)}`;
        },

        // async syncCart() {
        //     try {
        //         const response = await axios.post('/api/cart/sync');
        //         console.log('Giỏ hàng đã được đồng bộ.');
        //     } catch (error) {
        //         console.error('Không thể đồng bộ giỏ hàng:', error);
        //     }
        // },

        async updateCartServer(itemId, quantity) {
            try {
                await axios.put(`/api/cart/${itemId}`, {
                    quantity: quantity
                });
                await this.fetchCart();
            } catch (error) {
                console.error('Lỗi khi cập nhật giỏ hàng:', error);
                this.error = 'Không thể cập nhật giỏ hàng!';
            }
        },

        async handleQuantityChange(item, delta) {
            if (!this.auth || !this.auth.user) {
                // User not logged in, update session
                try {
                    const newQuantity = Math.max(1, item.quantity + delta);
                    await axios.put('/api/session/cart', {
                        productVariant_id: item.productVariant_id,
                        quantity: newQuantity,
                        selected: item.selected // gửi cả selected
                    });

                    // Update local item
                    const found = this.cartItems.find(i => i.productVariant_id === item.productVariant_id);
                    if (found) {
                        found.quantity = newQuantity;
                    }

                    // Update store
                    this.cartStore.setCartCount(this.cartItems.reduce((sum, i) => sum + i.quantity, 0));
                    this.cartStore.setCartItems(this.cartItems);
                } catch (error) {
                    console.error('Lỗi khi cập nhật giỏ hàng:', error);
                }
            } else {
                // User logged in, update database
                const found = this.cartItems.find(i => i.id === item.id);
                if (found) {
                    found.quantity = Math.max(1, found.quantity + delta);
                }
                await this.updateCartServer(item.id, found.quantity);
                // Update store
                this.cartStore.setCartCount(this.cartItems.reduce((sum, i) => sum + i.quantity, 0));
                this.cartStore.setCartItems(this.cartItems);
            }
            await this.getTotalPriceSelected();
        },

        async handleDeleteItem(item) {
            if (!this.auth || !this.auth.user) {
                // User not logged in, remove from session
                try {
                    await axios.delete('/api/session/cart', {
                        data: { productVariant_id: item.productVariant_id }
                    });

                    // Update local items
                    this.cartItems = this.cartItems.filter(i => i.productVariant_id !== item.productVariant_id);

                    // Update store
                    this.cartStore.setCartCount(this.cartItems.reduce((sum, i) => sum + i.quantity, 0));
                    this.cartStore.setCartItems(this.cartItems);
                } catch (error) {
                    this.error = 'Không thể xóa sản phẩm khỏi giỏ hàng!';
                }
            } else {
                // User logged in, remove from database
                try {
                    await axios.delete(`/api/cart/${item.id}`);
                    await this.fetchCart();
                    // Update store
                    this.cartStore.setCartCount(this.cartCount);
                    this.cartStore.setCartItems(this.cartItems);
                } catch (error) {
                    this.error = 'Không thể xóa sản phẩm khỏi giỏ hàng!';
                }
            }
            await this.getTotalPriceSelected();
        },

        async handleCheckboxItem(item) {
            const found = this.cartItems.find(i =>
                this.auth?.user ? i.id === item.id : i.productVariant_id === item.productVariant_id
            );
            if (found) {
                found.selected = found.selected === 1 ? 0 : 1;
                if (!this.auth || !this.auth.user) {
                    // Cập nhật selected vào session
                    await axios.put('/api/session/cart', {
                        productVariant_id: found.productVariant_id,
                        quantity: found.quantity,
                        selected: found.selected
                    });
                }
            }
            await this.getTotalPriceSelected();
        },
        async handleCheckout() {
            // Lọc ra các sản phẩm đã được chọn
            const selectedItems = this.cartItems.filter(item => item.selected === 1);
            if (selectedItems.length === 0) {
                alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
                return;
            }

            try {
                this.loading = true;
                // Đảm bảo có CSRF token
                await axios.get('/sanctum/csrf-cookie');

                console.log('Sending checkout request with items:', selectedItems);
                console.log('User authentication status:', this.auth?.user ? 'authenticated' : 'guest');

                // Chọn API endpoint phù hợp dựa vào trạng thái đăng nhập
                const checkoutUrl = this.auth?.user
                    ? '/api/cart/checkout'        // User đã đăng nhập
                    : '/api/session/cart/checkout'; // User chưa đăng nhập

                // Gửi API với chỉ những sản phẩm đã chọn
                const response = await axios.post(checkoutUrl, {
                    items: selectedItems
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Checkout response:', response.data);

                if (!response.data.success) {
                    alert(response.data.message || 'Có lỗi xảy ra khi tạo đơn hàng');
                    return;
                }

                const checkoutId = response.data.checkout_id;
                console.log('checkoutId:', checkoutId);
                console.log('User type:', response.data.user_type || 'session-based');

                if (!checkoutId) {
                    alert('Không lấy được mã đơn hàng, vui lòng thử lại!');
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
                        alert("Lỗi method không được hỗ trợ. Vui lòng thử lại sau.");
                    } else if (error.response.status === 401) {
                        alert("Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.");
                    } else {
                        alert(error.response.data.message || "Đã có lỗi xảy ra. Vui lòng thử lại sau.");
                    }
                } else {
                    alert("Đã có lỗi xảy ra. Vui lòng thử lại sau.");
                }
            } finally {
                this.loading = false;
            }
        },

    },
};
</script>

<style lang="scss" scoped>

</style>
