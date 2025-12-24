<template>
    <Head :title="$t('messages.cart')"/>
    <Menu></Menu>
    <Search></Search>
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $t('messages.cart') }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.pages') }}</a></li>
            <li class="breadcrumb-item active text-white">{{ $t('messages.cart') }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

        <!-- Cart Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div v-if="cartItems.length === 0" class="alert alert-warning">{{ $t('messages.cart_empty') }}</div>
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
                                <th scope="col">{{ $t('messages.products') }}</th>
                                <th scope="col">{{ $t('messages.name') }}</th>
                                <th scope="col">{{ $t('messages.size') }}</th>
                                <th scope="col">{{ $t('messages.price') }}</th>
                                <th scope="col">{{ $t('messages.quantity') }}</th>
                                <th scope="col">{{ $t('messages.total') }}</th>
                                <th scope="col">{{ $t('messages.handle') }}</th>
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
                        <input type="text" class="border-0 border-bottom rounded me-5 py-3 mb-2" :placeholder="$t('messages.coupon_code')" style=" text-indent: 15px;w">
                        <button class="btn border-secondary rounded-pill px-4 py-2 my-2 text-primary" type="button">{{ $t('messages.apply_coupon') }}</button>
                    </div>
                    <div class="row d-flex rounded bg-light">
                        <div class="col-7 d-flex">
                            <div class="d-flex align-items-center ms-4 me-4">
                                <input class="fs-5 form-check-input mt-0" style="font-size: 20px;"
                                    type="checkbox"
                                    :checked="isAllSelected"
                                    @change="toggleAllSelection"
                                />
                                <h5 class="mb-0 ms-3">{{ $t('messages.select_all') }} ({{this.productSelected}})</h5>
                            </div>
                            <div class="d-flex align-items-center ms-4 me-4 fs-5">{{ $t('messages.delete') }}</div>
                            <div class="d-flex align-items-center ms-4 me-4 fs-5">{{ $t('messages.move_to_wishlist') }}</div>
                        </div>
                        <div class="col-5 rounded d-flex row">
                            <div class="col-6">
                                <div class="p-2">
                                    <div class="d-flex justify-content-between ">
                                        <h5 class="mb-0 me-4">{{ $t('messages.total') }} ({{ this.productSelected }} {{ $t('messages.items') }}):</h5>
                                        <p class="mb-0">${{ this.totalPriceSelected }}</p>
                                    </div>
                                </div>
                                <div class="py-2  border-top d-flex justify-content-end">
                                    <p class="mb-0 ps-4 me-4">{{ $t('messages.saved') }}:</p>
                                    <p class="mb-0 pe-4">${{ totalSave }}</p>
                                </div>
                            </div>
                            <div class="col-1"></div>
                            <button class="col-5 btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase my-3" @click="handleCheckout()" type="button">{{ $t('messages.proceed_checkout') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart Page End -->


        <!-- Footer Start -->
        <Footer />
        <!-- Footer End -->

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
import Footer from '@/Pages/Frontend/Includes/Footer.vue';
import Swal from "sweetalert2";
axios.defaults.withCredentials = true;
export default {
    components: {
        Checkbox,
        Menu,
        Head,
        Search,
        Footer,
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
                this.showNotification(this.$t('messages.please_select_at_least_one_product'), 'warning');
                return;
            }

            try {
                this.loading = true;
                // Đảm bảo có CSRF token
                await axios.get('/sanctum/csrf-cookie');

                console.log('this.cartItems:', this.cartItems);
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
};
</script>

<style lang="scss" scoped>

</style>
