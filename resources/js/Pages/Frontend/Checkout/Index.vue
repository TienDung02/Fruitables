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
    <div class="container-fluid bg-gray py-5">
        <!-- Show when no shipping info available -->
        <div class="container py-5" id="not-have-shipping-info" v-show="!hasShippingInfo">
            <h1 class="mb-4">Billing details</h1>
            <form @submit.prevent="placeOrder">
                <div class="row g-5">
                    <div class="col-md-12 col-lg-6 col-xl-6">
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-item w-100">
                                    <label class="form-label my-3">Full Name<sup>*</sup></label>
                                    <input type="text" class="form-control" v-model="formData.name" required>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative mb-3 ">
                            <label class="form-label my-3">Province/City, District, Ward/Commune<sup>*</sup></label>
                            <div class="rounded bg-white d-flex justify-content-between align-items-center form-control" style="cursor: pointer; min-height: 38px;" @click="toggleLocationDropdown">
                                    <span :class="{ 'text-muted': !locationDisplayText }">
                                        {{ defaultAddress !== '' && locationDisplayText === '' && isEditMode ? defaultAddress : locationDisplayText !== '' ? locationDisplayText : 'City, District, Ward' }}
                                    </span>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm p-0" @click.stop="clearLocation" v-if="locationDisplayText" style="border: none; background: none;">
                                        <i class="bi bi-x-circle-fill text-muted"></i>
                                    </button>
                                    <i class="bi bi-chevron-down text-muted"></i>
                                </div>
                            </div>

                            <div v-if="showLocationDropdown" class="border rounded mt-1 bg-white position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow: hidden;">
                                <div class="d-flex border-bottom">
                                    <div
                                        class="flex-fill text-center py-2 border"
                                        :class="{ 'text-danger border-bottom border-secondary rounded-top-left border-2': currentLocationLevel === 'city' }"
                                        style="cursor: pointer;"
                                        @click="setLocationLevel('city')"
                                    >
                                        City
                                    </div>
                                    <div
                                        class="flex-fill text-center py-2 border"
                                        :class="{ 'text-danger border-bottom border-secondary border-2': currentLocationLevel === 'district' }"
                                        style="cursor: pointer;"
                                        @click="setLocationLevel('district')"
                                    >
                                        District
                                    </div>
                                    <div
                                        class="flex-fill text-center py-2 border"
                                        :class="{ 'text-danger border-bottom border-secondary border-2': currentLocationLevel === 'ward' }"
                                        style="cursor: pointer;"
                                        @click="setLocationLevel('ward')"
                                    >
                                        Ward
                                    </div>
                                </div>
                                <div style="max-height: 250px; overflow-y: auto;">
                                    <div
                                        v-for="item in currentLocationList"
                                        :key="item.id"
                                        class="py-2 px-4 border-bottom"
                                        style="cursor: pointer;"
                                        @click="currentLocationLevel === 'city' ? selectCity(item) : currentLocationLevel === 'district' ? selectDistrict(item) : selectWard(item)"
                                        @mouseover="$event.target.style.backgroundColor = '#f5f5f5'"
                                        @mouseout="$event.target.style.backgroundColor = 'white'"
                                    >
                                        {{ item.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Detailed Address <sup>*</sup></label>
                            <input type="text" class="form-control" placeholder="House Number Street Name" v-model="formData.address" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Phone<sup>*</sup></label>
                            <input type="tel" class="form-control" v-model="formData.phone" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Email Address <sup>*</sup></label>
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
                                    <label class="form-check-label" for="shipping-free">
                                        Free Shipping - $0.00
                                        <br><small class="text-muted">Estimated delivery: {{ deliveryDates.free.range }}</small>
                                    </label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="shipping-standard" value="standard" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-standard">
                                        Standard - $3.00
                                        <br><small class="text-muted">Estimated delivery: {{ deliveryDates.standard.range }}</small>
                                    </label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="shipping-fast" value="fast" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-fast">
                                        Fast - $6.00
                                        <br><small class="text-muted">Estimated delivery: {{ deliveryDates.fast.range }}</small>
                                    </label>
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
                                type="button"
                                class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary"
                                :disabled="isProcessingOrder"
                                @click="placeOrder"
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
        <div class="container p-5" id="have-shipping-info" v-show="hasShippingInfo && auth && auth.user">

            <!-- Delivery Address Section -->
            <div class="delivery-address">
                <div class="dashed-line-pattern">
                    <span></span><span></span><span></span><span></span><span></span>
                    <span></span><span></span><span></span><span></span><span></span>
                    <span></span><span></span><span></span><span></span><span></span>
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
                <div class="delivery-address-header text-primary">
                    <i class="fas fa-map-marker-alt"></i>
                    Delivery Address
                </div>
                <div class="delivery-address-content pt-0">
                    <div class="address-info">
                        <div class="store-name">Fresh Produce Store</div>
                        <div class="store-phone">{{ shippingInfo?.shipping_info?.current?.phone || '' }}</div>
                        <div class="store-address">{{ shippingInfo?.shipping_info?.current?.full_address || '' }}</div>
                    </div>
                    <div class="address-actions">
                        <span class="border rounded border-secondary py-2 px-3 text-uppercase text-primary">Default</span>
                        <a href="#" class="change-link text-secondary">Change</a>
                    </div>
                </div>
            </div>

            <!-- Products Section 1 -->
            <div class="products-section">
                <div class="products-header">
                    <div>Product</div>
                    <div class="text-end">Variant</div>
                    <div class="text-end">Unit Price</div>
                    <div class="text-end">Quantity</div>
                    <div class="text-end">Total</div>
                </div>

                <!-- Product 1 -->
                <div class="product-item" v-for="item in selectedItems" :key="item.id || item.productVariant_id">
                    <div class="product-info d-flex">
                        <div class="product-image">
                            <img :src="(item.product_variant?.product?.media?.[0]?.file_path ? '../' + item.product_variant.product.media[0].file_path : '/images/default-product.jpg')"
                                 class="img-fluid rounded"
                                 style="width: 90px; height: 90px; object-fit: contain"
                                 :alt="(item.product_variant?.product?.name) || 'Product'">
                            <!--                            <i class="fas fa-shoe-prints" style="font-size: 40px; color: #ddd;"></i>-->
                        </div>
                        <div class="product-details padding-left-15 padding-right-15 d-flex justify-content-between">
                            <div class="product-name">{{ item.product_variant?.product?.name || 'Product Name' }}</div>
                            <div class="product-variant">{{ item.product_variant?.size }}</div>
                        </div>
                    </div>
                    <div class="product-price">${{ item.product_variant.sale_price ?? item.product_variant.price }}</div>
                    <div class="product-quantity text-end">{{ item.quantity || 0 }}</div>
                    <div class="product-total">${{  (((item.product_variant?.sale_price || item.product_variant?.price || item.price || 0)) * (item.quantity || 0)).toFixed(2) }}</div>
                </div>
            </div>

            <div class="order-summary p-0">
                <div class="d-flex">
                    <div class="padding-20 pb-0 width-60">
                        <div class="row g-4 text-center align-items-center justify-content-center">
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
                    </div>
                    <div class="padding-20 width-40"  style="border-left: 1px dashed #dee2e6;">
                        <div class="row g-4 text-center align-items-center justify-content-center">
                            <div class="col-12">
                                <div class="shipping-header border-bottom">
                                    <div>Shipping</div>
                                    <div></div>
                                    <div class="text-end">Date of receipt</div>
                                    <div class="text-end">Amount</div>
                                </div>
                                <div class="form-check text-start my-3 shipping-header">
                                    <input type="radio" class="form-check-input" id="shipping-free-2" value="free" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-free-2">Free Shipping</label>
                                    <label class="form-check-label text-end" for="shipping-free-2">{{ deliveryDates.free.range }}</label>
                                    <label class="form-check-label text-end" for="shipping-free-2">$0.00</label>
                                </div>
                                <div class="form-check text-start my-3 shipping-header">
                                    <input type="radio" class="form-check-input" id="shipping-standard-2" value="standard" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-standard-2">Standard</label>
                                    <label class="form-check-label text-end" for="shipping-standard-2">{{ deliveryDates.standard.range }}</label>
                                    <label class="form-check-label text-end" for="shipping-standard-2">$3.00</label>
                                </div>
                                <div class="form-check text-start my-3 shipping-header">
                                    <input type="radio" class="form-check-input" id="shipping-fast-2" value="fast" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-fast-2">Fast</label>
                                    <label class="form-check-label text-end" for="shipping-fast-2">{{ deliveryDates.fast.range }}</label>
                                    <label class="form-check-label text-end" for="shipping-fast-2">$6.00</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary p-0 d-flex">
                <div class="form-item width-35 padding-20">
                    <label class="form-check-label text-end mb-2" for="shipping-free">Note (Optional) </label>
                    <textarea name="text" class="form-control" spellcheck="false" cols="30" rows="8" placeholder="Order Notes (Optional)" v-model="formData.notes"></textarea>
                </div>
                <div class="width-65 padding-20">
                    <div class="summary-row">
                        <span>Total Product Cost</span>
                        <span>${{ this.subtotal.toFixed(2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping Cost</span>
                        <span>${{ this.shippingCost }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total Payment</span>
                        <span class="price">${{ this.total }}</span>
                    </div>
                    <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                        <button
                            type="button"
                            class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary"
                            :disabled="isProcessingOrder"
                            @click="placeOrder"
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
    <Footer></Footer>


</template>

<script>
import { Head, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import Menu from '../Includes/Menu.vue';
import Footer from '../Includes/Footer.vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import QRCode from 'qrcode';
import Swal from "sweetalert2";

export default {
    name: 'Checkout',
    components: {
        Head,
        Menu,
        Footer,
    },
    props: {
        checkoutId: String,
        csrf_token: String,
        selectedItems: {
            type: Array,
            default: () => []
        },
        shippingInfo: {
            type: Object,
            default: () => ({})
        },
        auth: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            formData: {
                name: '',
                address: '',
                ward_id: '',
                phone: '',
                email: '',
                notes: ''
            },
            defaultAddress: '',
            showLocationDropdown: false,
            currentLocationLevel: 'city',
            selectedCity: null,
            selectedDistrict: null,
            selectedWard: null,
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
            locationData: {
                cities: [],
                districts: [],
                wards: [],
            },
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
        currentLocationList() {
            if (this.currentLocationLevel === 'city') {
                return this.locationData.cities;
            } else if (this.currentLocationLevel === 'district' && this.selectedCity) {
                return this.locationData.districts;
            } else if (this.currentLocationLevel === 'ward' && this.selectedDistrict) {
                return this.locationData.wards;
            }
            return [];
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
        locationDisplayText() {
            const parts = [];
            if (this.selectedCity) parts.push(this.selectedCity.name);
            if (this.selectedDistrict) parts.push(this.selectedDistrict.name);
            if (this.selectedWard) parts.push(this.selectedWard.name);
            return parts.join(', ');
        },
        total() {
            return parseFloat((this.subtotal + this.shippingCost).toFixed(2));
        },
        hasShippingInfo() {
            // Check if shippingInfo exists and has meaningful data
            console.log('shippingInfo shipping_info:', this.shippingInfo.shipping_info);
            // console.log('shippingInfo :', this.shippingInfo);

            // Kiểm tra xem có shipping_info không
            if (!this.shippingInfo || !this.shippingInfo.shipping_info) {
                return false;
            }

            const shippingData = this.shippingInfo.shipping_info;

            // Nếu shipping data null hoặc rỗng
            if (!shippingData || typeof shippingData !== 'object') {
                return false;
            }

            // Kiểm tra nếu user đã đăng nhập và có dữ liệu từ database
            if (this.shippingInfo.is_logged_in && shippingData.synced_from_db) {
                // Kiểm tra xem có địa chỉ nào không
                return shippingData.total_addresses > 0 ||
                    (shippingData.current && (
                        shippingData.current.name ||
                        shippingData.current.phone ||
                        shippingData.current.address
                    ));
            }

            // Nếu chưa đăng nhập, kiểm tra dữ liệu session
            if (!this.shippingInfo.is_logged_in) {
                // Kiểm tra các trường cơ bản có dữ liệu không
                const keys = Object.keys(shippingData);
                if (keys.length === 0) return false;

                // Kiểm tra có ít nhất một trường có dữ liệu không rỗng
                return keys.some(key => {
                    const value = shippingData[key];
                    return value !== null && value !== undefined && value !== '';
                });
            }

            return false;
        },

        // Tính toán ngày nhận hàng dự kiến
        deliveryDates() {
            const today = new Date();
            const addDays = (date, days) => {
                const result = new Date(date);
                result.setDate(result.getDate() + days);
                return result;
            };

            const formatDate = (date) => {
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric'
                });
            };

            return {
                free: {
                    min: formatDate(addDays(today, 10)),
                    max: formatDate(addDays(today, 15)),
                    range: `${formatDate(addDays(today, 10))} - ${formatDate(addDays(today, 15))}`
                },
                standard: {
                    estimated: formatDate(addDays(today, 7)),
                    range: `${formatDate(addDays(today, 6))} - ${formatDate(addDays(today, 8))}`
                },
                fast: {
                    estimated: formatDate(addDays(today, 3)),
                    range: `${formatDate(addDays(today, 2))} - ${formatDate(addDays(today, 4))}`
                }
            };
        },

        // Lấy thông tin giao hàng hiện tại đang chọn
        currentDeliveryInfo() {
            const deliveryInfo = this.deliveryDates[this.selectedShipping];
            return {
                type: this.selectedShipping,
                cost: this.shippingCost,
                deliveryDate: deliveryInfo.estimated || deliveryInfo.range,
                ...deliveryInfo
            };
        }
    },
    mounted() {
        // console.log('=== CHECKOUT COMPONENT MOUNTED ===');
        // console.log('Selected items:', this.selectedItems);
        // console.log('Auth:', this.auth);
        // console.log('Window.axios:', window.axios);
        // console.log('Axios imported:', axios);
        // console.log('shippingInfo:', this.shippingInfo);
        // console.log('hasShippingInfo computed:', this.hasShippingInfo);
        // console.log('shippingInfo type:', typeof this.shippingInfo);


        // Tự động điền formData từ shippingInfo nếu có
        this.loadShippingInfoToForm();

        // this.loadUserInfo()
        this.loadLocationData();
        // Khởi tạo modal
        try {
            this.momoModal = new Modal(document.getElementById('momoQRModal'))
            // console.log('Modal initialized successfully');
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
        // Method để tự động điền formData từ shippingInfo
        loadShippingInfoToForm() {
            if (this.shippingInfo && this.shippingInfo.shipping_info) {
                const shippingData = this.shippingInfo.shipping_info;
                console.log('Loading shipping info to form:', shippingData);

                // Nếu user đã đăng nhập và có dữ liệu từ database
                if (this.shippingInfo.is_logged_in && shippingData.synced_from_db && shippingData.current) {
                    const current = shippingData.current;
                    this.formData.name = current.name || '';
                    this.formData.address = current.address || '';
                    this.formData.phone = current.phone || '';
                    this.formData.ward_id = current.ward_id || '';

                    // Gán email từ user auth nếu có
                    if (this.auth && this.auth.user && this.auth.user.email) {
                        this.formData.email = this.auth.user.email;
                    }

                    console.log('Form data loaded from database:', this.formData);
                }
                // Nếu chưa đăng nhập, lấy dữ liệu từ session
                else if (!this.shippingInfo.is_logged_in) {
                    this.formData.name = shippingData.name || '';
                    this.formData.address = shippingData.address || '';
                    this.formData.phone = shippingData.phone || '';
                    this.formData.email = shippingData.email || '';
                    this.formData.ward_id = shippingData.ward_id || '';
                    this.formData.notes = shippingData.notes || '';

                    console.log('Form data loaded from session:', this.formData);
                }
            }
        },

        updateShipping() {
            // Method để cập nhật phí ship khi người dùng thay đổi
            console.log('Shipping updated:', this.selectedShipping);
            console.log('Current shipping cost:', this.shippingCost);
            console.log('New total:', this.total);
        },


        async placeOrder() {
            console.log('=== PLACE ORDER CLICKED ===');
            console.log('hasShippingInfo:', this.hasShippingInfo);
            console.log('selectedPaymentMethod:', this.selectedPaymentMethod);
            console.log('selectedItems:', this.selectedItems);
            console.log('selectedShipping:', this.selectedShipping);

            if (!this.validateForm()) {
                console.log('Form validation failed');
                return;
            }

            console.log('Form validation passed, processing order...');
            this.isProcessingOrder = true

            console.log('shipping info', this.shippingInfo.shipping_info)

            // Kiểm tra xem user có đăng nhập không và lấy thông tin tương ứng

            if (this.auth && this.auth.user) {
                console.log('is logged in user:', this.auth.user);

                // User đã đăng nhập - có thể dùng thông tin từ auth làm dự phòng
                if (!this.formData.name) this.formData.name = this.auth.user.name || this.auth.user.email;
                if (!this.formData.email) this.formData.email = this.auth.user.email;
            }
            // Nếu user chưa đăng nhập, formData sẽ được lấy từ form nhập liệu
            try {
                const orderData = {
                    items: this.selectedItems,
                    shipping_type: this.selectedShipping,
                    customer_info: this.formData,
                    payment_method: this.selectedPaymentMethod
                }

                console.log('Order data prepared:', orderData);

                if (this.selectedPaymentMethod === 'momo') {
                    console.log('Processing MoMo payment...');
                    await this.processMoMoPayment(orderData)
                } else {
                    console.log('Processing other payment method...');
                    await this.processOtherPayment(orderData)
                }

            } catch (error) {
                console.error('Order error:', error)
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: "Có lỗi xảy ra khi đặt hàng: " + (error.response?.data?.message || error.message),
                    showConfirmButton: false,
                    timer: 3000
                });
            } finally {
                this.isProcessingOrder = false
            }
        },
        // Location data methods
        async loadLocationData() {
            try {
                // Chỉ load provinces khi component mount
                const response = await axios.get('/api/locations/provinces');
                // console.log('Provinces loaded:', response.data);
                if (response.data.success) {
                    this.locationData.cities = response.data.data || [];
                }
            } catch (error) {
                console.error('Error loading provinces:', error);
            }
        },

        async loadDistricts(provinceId) {
            try {
                const response = await axios.get(`/api/locations/districts/${provinceId}`);
                if (response.data.success) {
                    this.locationData.districts = response.data.data || [];
                }
            } catch (error) {
                console.error('Error loading districts:', error);
            }
        },

        async loadWards(districtId) {
            try {
                const response = await axios.get(`/api/locations/wards/${districtId}`);
                if (response.data.success) {
                    this.locationData.wards = response.data.data || [];
                }
            } catch (error) {
                console.error('Error loading wards:', error);
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
        resetAddressForm() {
            this.addressForm = {
                id: null,
                name: '',
                phone: '',
                address: '',
                ward_id: null,
                district_id: null,
                province_id: null,
                is_default: false,
            };
            this.selectedCity = null;
            this.selectedDistrict = null;
            this.selectedWard = null;
        },
        toggleLocationDropdown() {
            this.showLocationDropdown = !this.showLocationDropdown;
        },

        setLocationLevel(level) {
            this.currentLocationLevel = level;
        },

        async selectCity(city) {
            this.selectedCity = city;
            this.selectedDistrict = null;
            this.selectedWard = null;
            this.currentLocationLevel = 'district';

            // Load districts for selected city
            await this.loadDistricts(city.id);
        },

        async selectDistrict(district) {
            this.selectedDistrict = district;
            this.selectedWard = null;
            this.currentLocationLevel = 'ward';

            // Load wards for selected district
            await this.loadWards(district.id);
        },

        selectWard(ward) {
            this.selectedWard = ward;
            this.showLocationDropdown = false;
        },

        clearLocation() {
            this.selectedCity = null;
            this.selectedDistrict = null;
            this.selectedWard = null;
            this.currentLocationLevel = 'city';
            // Reset districts and wards
            this.locationData.districts = [];
            this.locationData.wards = [];
        },
        validateAddressForm() {
            if (!this.addressForm.name || !this.addressForm.phone || !this.addressForm.address) {
                Swal.fire('Error', 'Please fill in all required fields', 'error');
                return false;
            }
            if (!this.selectedWard || !this.selectedDistrict || !this.selectedCity) {
                Swal.fire('Error', 'Please select city, district, and ward', 'error');
                return false;
            }

            this.addressForm.ward_id = this.selectedWard.id;
            this.addressForm.district_id = this.selectedDistrict.id;
            this.addressForm.province_id = this.selectedCity.id;

            return true;
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
            // Nếu đã có shipping info, không cần validate form nhập liệu
            if (this.hasShippingInfo) {
                // Chỉ kiểm tra những thứ cần thiết cho checkout
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

            // Nếu chưa có shipping info, validate form nhập liệu
            // Cập nhật thông tin location vào formData
            if (this.selectedCity) {
                this.formData.city = this.selectedCity.name;
            }
            if (this.selectedDistrict && this.selectedWard) {
                this.formData.country = `${this.selectedDistrict.name}, ${this.selectedWard.name}`;
            }
            this.formData.postcode = '00000'; // Default postcode

            // Kiểm tra các trường bắt buộc thực tế
            const required = ['name', 'address', 'phone', 'email']

            for (let field of required) {
                if (!this.formData[field]) {
                    alert(`Please fill in ${field.replace('_', ' ')}`)
                    return false
                }
            }

            // Kiểm tra location dropdown
            if (!this.selectedCity || !this.selectedDistrict || !this.selectedWard) {
                alert('Please select City, District, and Ward')
                return false
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
