<template>
    <Head title="Checkout"/>
    <Menu></Menu>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Checkout</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Checkout</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Checkout Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <h1 class="mb-4">Billing details</h1>
            <form @submit.prevent="placeOrder">
                <div class="row g-5">
                    <div class="col-md-12 col-lg-6 col-xl-6">
                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                <div class="form-item w-100">
                                    <label class="form-label my-3">First Name<sup>*</sup></label>
                                    <input type="text" class="form-control" v-model="formData.first_name" required>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="form-item w-100">
                                    <label class="form-label my-3">Last Name<sup>*</sup></label>
                                    <input type="text" class="form-control" v-model="formData.last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Company Name</label>
                            <input type="text" class="form-control" v-model="formData.company_name">
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Address <sup>*</sup></label>
                            <input type="text" class="form-control" placeholder="House Number Street Name" v-model="formData.address" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Town/City<sup>*</sup></label>
                            <input type="text" class="form-control" v-model="formData.city" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Country<sup>*</sup></label>
                            <input type="text" class="form-control" v-model="formData.country" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Postcode/Zip<sup>*</sup></label>
                            <input type="text" class="form-control" v-model="formData.postcode" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Mobile<sup>*</sup></label>
                            <input type="tel" class="form-control" v-model="formData.mobile" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Email Address<sup>*</sup></label>
                            <input type="email" class="form-control" v-model="formData.email" required>
                        </div>
                        <hr>
                        <div class="form-item">
                            <textarea name="text" class="form-control" spellcheck="false" cols="30" rows="11" placeholder="Order Notes (Optional)" v-model="formData.notes"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6 col-xl-6">
                        <div class="table-responsive">
                            <table class="table">
                                <colgroup>
                                    <col width="120">
                                    <col >
                                    <col width="80">
                                    <col width="90">
                                    <col width="100">
                                    <col width="100">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">Products</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Size</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in selectedItems" :key="item.id || item.productVariant_id">
                                        <th scope="row">
                                            <div class="d-flex align-items-center mt-2">
                                                <img :src="(item.product_variant?.product?.media?.[0]?.file_path ? '../' + item.product_variant.product.media[0].file_path : '/images/default-product.jpg')"
                                                     class="img-fluid rounded-circle"
                                                     style="width: 90px; height: 90px; object-fit: contain"
                                                     :alt="(item.product_variant?.product?.name) || 'Product'">
                                            </div>
                                        </th>
                                        <td class="py-5">{{ item.product_variant?.product?.name || 'Product Name' }}</td>
                                        <td class="py-5">{{ item.product_variant?.size }}</td>
                                        <td class="py-5">${{ item.product_variant.sale_price ?? item.product_variant.price }}</td>
<!--                                        <td class="py-5">$1</td>-->
                                        <td class="py-5">{{ item.quantity || 0 }}</td>
                                        <td class="py-5">${{  (((item.product_variant?.sale_price || item.product_variant?.price || item.price || 0)) * (item.quantity || 0)).toFixed(2) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-5"></td>
                                        <td class="py-5"></td>
                                        <td class="py-5">
                                            <p class="mb-0 text-dark py-3">Subtotal</p>
                                        </td>
                                        <td class="py-5">
                                            <div class="py-3 border-bottom border-top">
                                                <p class="mb-0 text-dark">${{ subtotal.toFixed(2) }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-5">
                                            <p class="mb-0 text-dark text-uppercase py-3">TOTAL</p>
                                        </td>
                                        <td class="py-5"></td>
                                        <td class="py-5"></td>
                                        <td class="py-5">
                                            <div class="py-3 border-bottom border-top">
                                                <p class="mb-0 text-dark">${{ total.toFixed(2) }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Shipping Options -->
                        <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                            <div class="col-12">
                                <h5 class="text-start mb-3">Shipping</h5>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="shipping-free" value="free" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-free">Free Shipping (10 - 15 days) - $0.00</label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="shipping-standard" value="standard" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-standard">Standard (7 days) - $3.00</label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="shipping-fast" value="fast" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-fast">Fast (3 days) - $6.00</label>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                            <div class="col-12">
                                <h5 class="text-start mb-3">Payment Methods</h5>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="payment-momo" value="momo" v-model="selectedPaymentMethod">
                                    <label class="form-check-label" for="payment-momo">
                                        <i class="fas fa-qrcode me-2"></i>
                                        MoMo Payment (QR Code)
                                    </label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="payment-bank" value="bank_transfer" v-model="selectedPaymentMethod">
                                    <label class="form-check-label" for="payment-bank">Direct Bank Transfer</label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="payment-cod" value="cod" v-model="selectedPaymentMethod">
                                    <label class="form-check-label" for="payment-cod">Cash on Delivery (COD)</label>
                                </div>

                                <div v-if="selectedPaymentMethod === 'bank_transfer'" class="mt-3 alert alert-info">
                                    <p class="mb-0">Make your payment directly into our bank account. Please use your Order ID as the payment reference.</p>
                                </div>
                                <div v-if="selectedPaymentMethod === 'cod'" class="mt-3 alert alert-info">
                                    <p class="mb-0">Pay with cash upon delivery. Please prepare exact amount if possible.</p>
                                </div>
                                <div v-if="selectedPaymentMethod === 'momo'" class="mt-3 alert alert-success">
                                    <p class="mb-0">Pay securely with MoMo e-wallet using QR code. You will see QR code after placing order.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                            <button
                                type="submit"
                                class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary"
                                :disabled="isProcessingOrder"
                            >
                                <span v-if="isProcessingOrder">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Processing...
                                </span>
                                <span v-else>Place Order</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Checkout Page End -->

    <!-- MoMo QR Code Modal -->
    <div class="modal fade" id="momoQRModal" tabindex="-1" aria-labelledby="momoQRModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="momoQRModalLabel">
                        <i class="fas fa-qrcode me-2"></i>
                        MoMo Payment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div v-if="paymentData.qr_code_url">
                        <!-- QR Code Expiry Timer -->
                        <div class="alert alert-warning mb-3" v-if="qrExpiryTime > 0">
                            <i class="fas fa-clock me-2"></i>
                            QR Code expires in: <strong>{{ formatTime(qrExpiryTime) }}</strong>
                        </div>
                        <div class="alert alert-danger mb-3" v-else-if="qrExpiryTime === 0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            QR Code has expired. Please generate a new one.
                        </div>

                        <p class="mb-3">Scan QR code with MoMo app to complete payment</p>

                        <!-- QR Code with reload button -->
                        <div class="position-relative d-inline-block mb-3">
                            <img v-if="qrImage" :src="qrImage" alt="MoMo QR Code"
                                 class="img-fluid"
                                 :class="qrExpiryTime === 0 ? 'opacity-50' : ''"
                                 style="max-width: 250px; border: 2px solid #e91e63; border-radius: 10px;">

                            <!-- Reload button overlay when expired -->
                            <div v-if="qrExpiryTime === 0" class="position-absolute top-50 start-50 translate-middle">
                                <button class="btn btn-warning btn-lg" @click="reloadQRCode" :disabled="isReloadingQR">
                                    <i class="fas fa-redo-alt" :class="{ 'fa-spin': isReloadingQR }"></i>
                                    {{ isReloadingQR ? 'Generating...' : 'Generate New QR' }}
                                </button>
                            </div>
                        </div>

                        <div class="payment-info mb-3">
                            <p class="text-muted small mb-1">Order: <strong>{{ paymentData.order_id }}</strong></p>
                            <p class="text-muted small mb-1">Amount: <strong>${{ ((paymentData.amount/25000) || 0).toFixed(2) }}</strong></p>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-outline-primary me-2" @click="checkPaymentStatus">
                                <i class="fas fa-sync-alt"></i> Check Status
                            </button>
                            <button class="btn btn-success me-2" @click="openMoMoApp" v-if="paymentData.deeplink">
                                <i class="fas fa-mobile-alt"></i> Open MoMo App
                            </button>
                            <button class="btn btn-outline-warning" @click="reloadQRCode" :disabled="isReloadingQR">
                                <i class="fas fa-redo-alt" :class="{ 'fa-spin': isReloadingQR }"></i>
                                {{ isReloadingQR ? 'Reloading...' : 'Reload QR' }}
                            </button>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                The payment will be automatically verified within 30 seconds.
                            </small>
                        </div>

                    </div>
                    <div v-else class="text-center py-4">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0">Generating QR code...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


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

<script>
import { Head, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import Menu from '../Includes/Menu.vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import QRCode from 'qrcode';

export default {
    name: 'Checkout',
    components: {
        Head,
        Menu,
    },
    props: {
        checkoutId: String,
        csrf_token: String,
        selectedItems: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            formData: {
                first_name: '',
                last_name: '',
                company_name: '',
                address: '',
                city: '',
                country: '',
                postcode: '',
                mobile: '',
                email: '',
                notes: ''
            },
            selectedShipping: 'free',
            selectedPaymentMethod: 'momo',
            isProcessingOrder: false,
            paymentData: {},
            paymentStatusInterval: null,
            momoModal: null,
            qrImage: null,
            qrExpiryTime: 0,
            qrCountdownInterval: null,
            isReloadingQR: false,
        }
    },
    watch: {
        'paymentData.qr_code_url': {
            immediate: true,
            async handler(value) {
                if (value) {
                    try {
                        this.qrImage = await QRCode.toDataURL(value)
                    } catch (err) {
                        console.error('QR generate error:', err)
                    }
                }
            }
        }
    },
    computed: {
        auth() {
            return usePage().props.auth
        },
        subtotal() {
            return this.selectedItems.reduce((sum, item) => {
                const price = item.product_variant.sale_price || item.product_variant.price
                return sum + (price * item.quantity)
            }, 0)
        },
        shippingCost() {
            const costs = { free: 0, standard: 3, fast: 6 }
            return costs[this.selectedShipping] || 0
        },
        total() {
            return this.subtotal + this.shippingCost
        }
    },
    mounted() {
        console.log('=== CHECKOUT COMPONENT MOUNTED ===');
        console.log('Selected items:', this.selectedItems);
        console.log('Auth:', this.auth);
        console.log('Window.axios:', window.axios);
        console.log('Axios imported:', axios);

        this.loadUserInfo()

        // Khởi tạo modal
        try {
            this.momoModal = new Modal(document.getElementById('momoQRModal'))
            console.log('Modal initialized successfully');
        } catch (error) {
            console.error('Modal initialization error:', error);
        }
    },
    beforeUnmount() {
        if (this.paymentStatusInterval) {
            clearInterval(this.paymentStatusInterval)
        }
        if (this.qrCountdownInterval) {
            clearInterval(this.qrCountdownInterval)
        }
    },
    methods: {
        updateShipping() {
            // Method để cập nhật phí ship khi người dùng thay đổi
            console.log('Shipping updated:', this.selectedShipping)
        },

        loadUserInfo() {
            if (this.auth?.user) {
                this.formData.email = this.auth.user.email
                this.formData.first_name = this.auth.user.name?.split(' ')[0] || ''
                this.formData.last_name = this.auth.user.name?.split(' ').slice(1).join(' ') || ''
            }
        },

        async placeOrder() {
            console.log('place order clicked');
            if (!this.validateForm()) return

            this.isProcessingOrder = true

            try {
                const orderData = {
                    items: this.selectedItems,
                    shipping_type: this.selectedShipping,
                    customer_info: this.formData,
                    payment_method: this.selectedPaymentMethod
                }

                if (this.selectedPaymentMethod === 'momo') {
                    await this.processMoMoPayment(orderData)
                } else {
                    await this.processOtherPayment(orderData)
                }

            } catch (error) {
                console.error('Order error:', error)
                alert('Có lỗi xảy ra khi đặt hàng: ' + (error.response?.data?.message || error.message))
            } finally {
                this.isProcessingOrder = false
            }
        },

        async processMoMoPayment(orderData) {
            try {
                console.log('Processing MoMo payment with data:', orderData);

                const response = await axios.post('/api/payment/momo/create', orderData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('MoMo API response:', response.data);

                if (response.data.success) {
                    this.paymentData = response.data
                    console.log('Payment data:', this.paymentData);

                    // Khởi tạo thời gian đếm ngược QR (5 phút = 300 giây)
                    this.startQRCountdown(300);

                    this.momoModal.show()
                    this.startPaymentStatusCheck()
                } else {
                    throw new Error(response.data.message || 'Không thể tạo thanh toán MoMo')
                }
            } catch (error) {
                console.error('MoMo payment error:', error)
                if (error.response) {
                    console.error('Error response:', error.response.data);
                    throw new Error(error.response.data.message || 'Lỗi từ server')
                } else {
                    throw error
                }
            }
        },

        async processOtherPayment(orderData) {
            try {
                // Sử dụng Inertia để xử lý các phương thức thanh toán khác
                router.post('/checkout/process', {
                    ...orderData,
                    payment_method: this.selectedPaymentMethod
                })
            } catch (error) {
                console.error('Other payment error:', error)
                throw error
            }
        },

        startPaymentStatusCheck() {
            // Kiểm tra trạng thái thanh toán mỗi 5 giây
            this.paymentStatusInterval = setInterval(async () => {
                await this.checkPaymentStatus()
            }, 5000)
        },

        async checkPaymentStatus() {
            if (!this.paymentData.order_id) return

            try {
                const response = await axios.get('/api/payment/momo/check-status', {
                    params: { orderId: this.paymentData.order_id },
                    headers: {
                        'Accept': 'application/json'
                    }
                })

                if (response.data.success && response.data.status === 'paid') {
                    clearInterval(this.paymentStatusInterval)
                    this.momoModal.hide()

                    // Chuyển đến trang thành công
                    alert('Thanh toán thành công!')
                    window.location.href = `/orders/${this.paymentData.order_id}`
                }
            } catch (error) {
                console.error('Check payment status error:', error)
            }
        },

        openMoMoApp() {
            if (this.paymentData.deeplink) {
                window.open(this.paymentData.deeplink, '_blank')
            }
        },

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60)
            seconds = seconds % 60
            return `${mins}m ${seconds}s`
        },

        startQRCountdown(seconds) {
            // Dừng timer cũ nếu có
            if (this.qrCountdownInterval) {
                clearInterval(this.qrCountdownInterval);
            }

            this.qrExpiryTime = seconds;

            this.qrCountdownInterval = setInterval(() => {
                this.qrExpiryTime--;

                if (this.qrExpiryTime <= 0) {
                    this.qrExpiryTime = 0;
                    clearInterval(this.qrCountdownInterval);
                    this.qrCountdownInterval = null;
                }
            }, 1000);
        },

        async reloadQRCode() {
            if (this.isReloadingQR) return

            this.isReloadingQR = true

            try {
                const requestData = {
                    order_id: this.paymentData.order_id
                };

                console.log('Reloading QR with data:', requestData);

                const response = await axios.post('/api/payment/momo/regenerate-qr', requestData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Regenerate QR success response:', response.data);

                if (response.data.success) {
                    this.paymentData.qr_code_url = response.data.qr_code_url;
                    // Khởi động lại timer đếm ngược 5 phút
                    this.startQRCountdown(300);
                } else {
                    alert(response.data.message || 'Không thể tạo mã QR mới');
                }
            } catch (error) {
                console.error('Regenerate QR code error:', error);
                console.error('Error response data:', error.response?.data);
                console.error('Error status:', error.response?.status);
                console.error('Error headers:', error.response?.headers);
                alert('Lỗi khi tạo mã QR mới: ' + (error.response?.data?.message || error.message));
            } finally {
                this.isReloadingQR = false
            }
        },

        validateForm() {
            const required = ['first_name', 'last_name', 'address', 'city', 'country', 'postcode', 'mobile', 'email']

            for (let field of required) {
                if (!this.formData[field]) {
                    alert(`Please fill in ${field.replace('_', ' ')}`)
                    return false
                }
            }

            if (this.selectedItems.length === 0) {
                alert('No items to checkout')
                return false
            }

            if (!this.selectedPaymentMethod) {
                alert('Please select a payment method')
                return false
            }

            return true
        }
    }
}
</script>

<style scoped>
.payment-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}

.payment-status {
    border: 1px solid #dee2e6;
}

.modal-header {
    background: linear-gradient(135deg, #e91e63, #ad1457);
    color: white;
}

.modal-header .btn-close {
    filter: invert(1);
}

.form-check-input:checked {
    background-color: #e91e63;
    border-color: #e91e63;
}

.btn-primary {
    background-color: #e91e63;
    border-color: #e91e63;
}

.btn-primary:hover {
    background-color: #ad1457;
    border-color: #ad1457;
}
</style>
