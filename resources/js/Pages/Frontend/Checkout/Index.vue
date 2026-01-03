<template>
    <Head :title="$t('messages.checkout')" />
    <Menu></Menu>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $t('messages.checkout') }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.pages') }}</a></li>
            <li class="breadcrumb-item active text-white">{{ $t('messages.checkout') }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Checkout Page Start -->
    <div class="container-fluid bg-gray py-5">
        <!-- Show when no shipping info available -->
        <div class="container py-5" id="not-have-shipping-info" v-show="!hasShippingInfo">
            <h1 class="mb-4">{{ $t('messages.billing_details') }}</h1>
            <form @submit.prevent="placeOrder">
                <div class="row g-5">
                    <div class="col-md-12 col-lg-6 col-xl-6">
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-item w-100">
                                    <label class="form-label my-3">{{ $t('messages.full_name') }}<sup>*</sup></label>
                                    <input type="text" class="form-control" :placeholder="$t('messages.placeholder_full_name')" v-model="formData.name" required>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative mb-3 ">
                            <label class="form-label my-3">{{ $t('messages.province_district_ward') }}<sup>*</sup></label>
                            <div class="rounded bg-white d-flex justify-content-between align-items-center form-control" style="cursor: pointer; min-height: 38px;" @click="toggleLocationDropdown">
                                    <span :class="{ 'text-muted': !locationDisplayText }">
                                        {{ defaultAddress !== '' && locationDisplayText === '' && isEditMode ? defaultAddress : locationDisplayText !== '' ? locationDisplayText : $t('messages.city_district_ward') }}
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
                                        {{ $t('messages.city') }}
                                    </div>
                                    <div
                                        class="flex-fill text-center py-2 border"
                                        :class="{ 'text-danger border-bottom border-secondary border-2': currentLocationLevel === 'district' }"
                                        style="cursor: pointer;"
                                        @click="setLocationLevel('district')"
                                    >
                                        {{ $t('messages.district') }}
                                    </div>
                                    <div
                                        class="flex-fill text-center py-2 border"
                                        :class="{ 'text-danger border-bottom border-secondary border-2': currentLocationLevel === 'ward' }"
                                        style="cursor: pointer;"
                                        @click="setLocationLevel('ward')"
                                    >
                                        {{ $t('messages.ward') }}
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
                            <label class="form-label my-3">{{ $t('messages.detailed_address') }} <sup>*</sup></label>
                            <input type="text" class="form-control" :placeholder="$t('messages.house_number_street_name')" v-model="formData.address" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">{{ $t('messages.phone') }}<sup>*</sup></label>
                            <input type="tel" class="form-control" pattern="^(0|\+84)[0-9]{9}$" title="Số điện thoại không hợp lệ" :placeholder="$t('messages.placeholder_phone')" v-model="formData.phone" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">{{ $t('messages.email_address') }} <sup>*</sup></label>
                            <input type="email" class="form-control" :placeholder="$t('messages.placeholder_email_address')" v-model="formData.email" required>
                        </div>
                        <hr>
                        <div class="form-item">
                            <textarea name="text" class="form-control" spellcheck="false" cols="30" rows="11" :placeholder="$t('messages.order_notes_optional')" v-model="formData.notes"></textarea>
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
                                    <th scope="col">{{ $t('messages.products') }}</th>
                                    <th scope="col">{{ $t('messages.name') }}</th>
                                    <th scope="col">{{ $t('messages.size') }}</th>
                                    <th scope="col">{{ $t('messages.price') }}</th>
                                    <th scope="col">{{ $t('messages.quantity') }}</th>
                                    <th scope="col">{{ $t('messages.total') }}</th>
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
                                    <td class="py-5">{{ formatCurrency(item.product_variant.sale_price ?? item.product_variant.price) }} VND</td>
                                    <!--                                        <td class="py-5">$1</td>-->
                                    <td class="py-5">{{ item.quantity || 0 }}</td>
                                    <td class="py-5">{{ formatCurrency(((item.product_variant?.sale_price || item.product_variant?.price || item.price || 0)) * (item.quantity || 0)) }} VND</td>
                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    <td class="py-5"></td>
                                    <td class="py-5"></td>
                                    <td class="py-5">
                                        <p class="mb-0 text-dark py-3">{{ $t('messages.subtotal') }}</p>
                                    </td>
                                    <td class="py-5">
                                        <div class="py-3 border-bottom border-top">
                                            <p class="mb-0 text-dark">{{ formatCurrency(subtotal) }} VND</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    <td class="py-5">
                                        <p class="mb-0 text-dark text-uppercase py-3">{{ $t('messages.total') }}</p>
                                    </td>
                                    <td class="py-5"></td>
                                    <td class="py-5"></td>
                                    <td class="py-5">
                                        <div class="py-3 border-bottom border-top">
                                            <p class="mb-0 text-dark">{{ formatCurrency(total) }} VND</p>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Shipping Options -->
                        <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                            <div class="col-12">
                                <h5 class="text-start mb-3">{{ $t('messages.shipping') }}</h5>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="shipping-free" value="free" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-free">
                                        {{ $t('messages.free_shipping') }} - $0.00
                                        <br><small class="text-muted">{{ $t('messages.estimated_delivery') }}: {{ deliveryDates.free.range }}</small>
                                    </label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="shipping-standard" value="standard" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-standard">
                                        {{ $t('messages.standard_shipping') }} - $3.00
                                        <br><small class="text-muted">{{ $t('messages.estimated_delivery') }}: {{ deliveryDates.standard.range }}</small>
                                    </label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="shipping-fast" value="fast" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-fast">
                                        {{ $t('messages.fast_shipping') }} - $6.00
                                        <br><small class="text-muted">{{ $t('messages.estimated_delivery') }}: {{ deliveryDates.fast.range }}</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                            <div class="col-12">
                                <h5 class="text-start mb-3">{{ $t('messages.payment_methods') }}</h5>

                                <!-- SePay Payment Option -->
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="payment-sepay" value="sepay" v-model="selectedPaymentMethod">
                                    <label class="form-check-label" for="payment-sepay">
                                        <i class="fas fa-university me-2 text-primary"></i>
                                        {{ $t('messages.sepay_banking_qr') }}
                                        <span class="badge bg-success ms-2 small">{{ $t('messages.recommended') }}</span>
                                    </label>
                                </div>
                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input" id="payment-cod" value="cod" v-model="selectedPaymentMethod">
                                    <label class="form-check-label" for="payment-cod">{{ $t('messages.cash_on_delivery') }}</label>
                                </div>

                                <!-- Payment Method Info -->
                                <div v-if="selectedPaymentMethod === 'sepay'" class="mt-3 alert alert-success">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-university text-success me-2 mt-1 fs-2"></i>
                                        <div class="m-auto">
                                            <strong>{{ $t('messages.sepay_banking_payment') }}</strong>
                                            <p class="mb-0 small">{{ $t('messages.sepay_payment_description') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="selectedPaymentMethod === 'bank_transfer'" class="mt-3 alert alert-info">
                                    <p class="mb-0">{{ $t('messages.bank_transfer_description') }}</p>
                                </div>
                                <div v-if="selectedPaymentMethod === 'cod'" class="mt-3 alert alert-info">
                                    <p class="mb-0">{{ $t('messages.cod_description') }}</p>
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
                                    {{ $t('messages.processing') }}...
                                </span>
                                <span v-else>{{ $t('messages.place_order') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="container p-5" id="have-shipping-info" v-show="showShippingSection">

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
                    {{ $t('messages.delivery_address') }}
                </div>
                <div class="delivery-address-content pt-0">
                    <div class="address-info">
                        <div class="store-name">Fresh Produce Store</div>
                        <div class="store-phone">{{ shippingInfo?.shipping_info?.current?.phone || '' }}</div>
                        <div class="store-address">{{ shippingInfo?.shipping_info?.current?.full_address || '' }}</div>
                    </div>
                    <div class="address-actions">
                        <span class="border rounded border-secondary py-2 px-3 text-uppercase text-primary">{{ $t('messages.default') }}</span>
                        <a href="#" class="change-link text-secondary">{{ $t('messages.change') }}</a>
                    </div>
                </div>
            </div>

            <!-- Products Section 1 -->
            <div class="products-section">
                <div class="products-header">
                    <div>{{ $t('messages.product') }}</div>
                    <div class="text-end">{{ $t('messages.variant') }}</div>
                    <div class="text-end">{{ $t('messages.unit_price') }}</div>
                    <div class="text-end">{{ $t('messages.quantity') }}</div>
                    <div class="text-end">{{ $t('messages.total') }}</div>
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
                    <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3 width-60">
                        <div class="col-12 px-5">
                            <h5 class="text-start mb-3">{{ $t('messages.payment_methods') }}</h5>

                            <!-- SePay Payment Option -->
                            <div class="form-check text-start my-3">
                                <input type="radio" class="form-check-input" id="payment-sepay" value="sepay" v-model="selectedPaymentMethod">
                                <label class="form-check-label" for="payment-sepay">
                                    <i class="fas fa-university me-2 text-primary"></i>
                                    {{ $t('messages.sepay_banking_qr') }}
                                    <span class="badge bg-success ms-2 small">{{ $t('messages.recommended') }}</span>
                                </label>
                            </div>
                            <div class="form-check text-start my-3">
                                <input type="radio" class="form-check-input" id="payment-cod" value="cod" v-model="selectedPaymentMethod">
                                <label class="form-check-label" for="payment-cod">{{ $t('messages.cash_on_delivery') }}</label>
                            </div>

                            <!-- Payment Method Info -->
                            <div v-if="selectedPaymentMethod === 'sepay'" class="mt-3 alert alert-success">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-university text-success me-2 mt-1 fs-2"></i>
                                    <div class="m-auto">
                                        <strong>{{ $t('messages.sepay_banking_payment') }}</strong>
                                        <p class="mb-0 small">{{ $t('messages.sepay_payment_description') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="selectedPaymentMethod === 'bank_transfer'" class="mt-3 alert alert-info">
                                <p class="mb-0">{{ $t('messages.bank_transfer_description') }}</p>
                            </div>
                            <div v-if="selectedPaymentMethod === 'cod'" class="mt-3 alert alert-info">
                                <p class="mb-0">{{ $t('messages.cod_description') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="padding-20 width-40"  style="border-left: 1px dashed #dee2e6;">
                        <div class="row g-4 text-center align-items-center justify-content-center">
                            <div class="col-12">
                                <div class="shipping-header border-bottom">
                                    <div>{{ $t('messages.shipping') }}</div>
                                    <div></div>
                                    <div class="text-end">{{ $t('messages.date_of_receipt') }}</div>
                                    <div class="text-end">{{ $t('messages.amount') }}</div>
                                </div>
                                <div class="form-check text-start my-3 shipping-header">
                                    <input type="radio" class="form-check-input" id="shipping-free-2" value="free" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-free-2">{{ $t('messages.free_shipping') }}</label>
                                    <label class="form-check-label text-end" for="shipping-free-2">{{ deliveryDates.free.range }}</label>
                                    <label class="form-check-label text-end" for="shipping-free-2">$0.00</label>
                                </div>
                                <div class="form-check text-start my-3 shipping-header">
                                    <input type="radio" class="form-check-input" id="shipping-standard-2" value="standard" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-standard-2">{{ $t('messages.standard_shipping') }}</label>
                                    <label class="form-check-label text-end" for="shipping-standard-2">{{ deliveryDates.standard.range }}</label>
                                    <label class="form-check-label text-end" for="shipping-standard-2">$3.00</label>
                                </div>
                                <div class="form-check text-start my-3 shipping-header">
                                    <input type="radio" class="form-check-input" id="shipping-fast-2" value="fast" v-model="selectedShipping" @change="updateShipping">
                                    <label class="form-check-label" for="shipping-fast-2">{{ $t('messages.fast_shipping') }}</label>
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
                    <textarea name="text" class="form-control" spellcheck="false" cols="30" rows="8" :placeholder="$t('messages.order_notes_optional')" v-model="formData.notes"></textarea>
                </div>
                <div class="width-65 padding-20">
                    <div class="summary-row">
                        <span>{{ $t('messages.total_product_cost') }}</span>
                        <span>{{ this.subtotal.toFixed(2) }} VND</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ $t('messages.shipping_cost') }}</span>
                        <span>{{ this.shippingCost }} VND</span>
                    </div>
                    <div class="summary-row total">
                        <span>{{ $t('messages.total_payment') }}</span>
                        <span class="price">{{ this.total }} VND</span>
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
                                    {{ $t('messages.processing') }}...
                                </span>
                            <span v-else>{{ $t('messages.place_order') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Checkout Page End -->

    <!-- SePay QR Code Modal -->
    <div class="modal fade" id="sepayQRModal" tabindex="-1" aria-labelledby="sepayQRModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title d-flex align-items-center" id="sepayQRModalLabel">
                        <i class="fas fa-university me-2"></i>
                        {{ $t('messages.sepay_banking_payment') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <div v-if="sepayPaymentData.qr_formats">
                        <!-- Timer Bar -->
                        <div class="px-4 pt-3">
                            <div class="alert mb-3" :class="sepayQrExpiryTime > 60 ? 'alert-info' : sepayQrExpiryTime > 0 ? 'alert-warning' : 'alert-danger'">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-clock me-2"></i>
                                        <span v-if="sepayQrExpiryTime > 0">
                                        {{ $t('messages.qr_code_expires_in') }}: <strong>{{ formatTime(sepayQrExpiryTime) }}</strong>
                                    </span>
                                        <span v-else>
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        {{ $t('messages.qr_code_has_expired') }}
                                    </span>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-light border mb-0">
                                <i class="fas fa-mobile-alt me-2 text-success"></i>
                                <small>{{ $t('messages.scan_qr_code_with_banking_app') }}</small>
                            </div>
                        </div>

                        <!-- Main Content: 2 Column Layout -->
                        <div class="row g-0 p-4">
                            <!-- Left Column: QR Code -->
                            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center border-end pe-md-4">
                                <div class="position-relative mb-3">
                                    <!-- Sử dụng qr_formats thay vì qr_code_url -->
                                    <img :src="getQRImageUrl()"
                                         alt="VietQR Code"
                                         class="img-fluid rounded shadow-sm"
                                         :class="sepayQrExpiryTime === 0 ? 'opacity-50' : ''"
                                         style="max-width: 280px; border: 3px solid #198754;"
                                         @error="onQRImageError">

                                    <!-- Expired overlay -->
                                    <div v-if="sepayQrExpiryTime === 0"
                                         class="position-absolute top-50 start-50 translate-middle">
                                        <div class="bg-white rounded-circle p-3 shadow-lg border border-danger">
                                            <i class="fas fa-clock text-danger" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Debug QR URLs -->
                                <div class="text-center" v-if="sepayPaymentData.qr_formats">
                                    <small class="text-muted d-block mb-2">
                                        Current QR: {{ currentQRFormat }}
                                    </small>
                                    <div class="btn-group btn-group-sm mb-2" role="group">
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm"
                                                :class="{ 'active': currentQRFormat === 'quickchart_png' }"
                                                @click="switchQRFormat('quickchart_png')">
                                            QuickChart
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm"
                                                :class="{ 'active': currentQRFormat === 'qr_server' }"
                                                @click="switchQRFormat('qr_server')">
                                            QRServer
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm"
                                                :class="{ 'active': currentQRFormat === 'quickchart_svg' }"
                                                @click="switchQRFormat('quickchart_svg')">
                                            SVG
                                        </button>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <p class="small text-muted mb-2">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $t('messages.expires_in') }}: <strong>{{ formatTime(sepayQrExpiryTime) }}</strong>
                                    </p>
                                    <button class="btn btn-outline-success btn-sm"
                                            @click="reloadSepayQR"
                                            :disabled="isReloadingSepayQR">
                                        <i class="fas fa-sync-alt me-1" :class="{ 'fa-spin': isReloadingSepayQR }"></i>
                                        {{ isReloadingSepayQR ? $t('messages.reloading') : $t('messages.reload_qr_code') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Right Column: Payment Details -->
                            <div class="col-md-6 ps-md-4">
                                <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                    {{ $t('messages.payment_details') }}
                                </h6>

                                <!-- Amount - Most Important -->
                                <div class="mb-4 p-3 bg-light rounded border border-success">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-money-bill-wave text-success me-3" style="font-size: 1.5rem;"></i>
                                        <div class="d-flex">
                                            <span class="d-flex align-items-center"> {{ $t('messages.amount') }}: </span>
                                            <h3 class="mb-0 text-success fw-bold">
                                               {{ formatCurrency(sepayPaymentData.amount) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recipient Information -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-user text-primary me-3 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <small class="text-muted d-block">{{ $t('messages.account_name') }}</small>
                                            <p class="mb-0 fw-semibold">{{ sepayPaymentData.bank_info.account_name || 'Nguyen Van A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-university text-primary me-3 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <small class="text-muted d-block">{{ $t('messages.bank_name') }}</small>
                                            <p class="mb-0 fw-semibold">{{ sepayPaymentData.bank_info.bank_name || 'ABC Bank' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-credit-card text-primary me-3 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <small class="text-muted d-block">{{ $t('messages.account_number') }}</small>
                                            <p class="mb-0 fw-semibold font-monospace">
                                                ****{{ sepayPaymentData.bank_info.account_number ? sepayPaymentData.bank_info.account_number.slice(-4) : '9012' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Instructions -->
                                <div class="border-top pt-3">
                                    <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.85rem;">
                                        <i class="fas fa-list-ol me-2"></i>{{ $t('messages.payment_instructions') }}
                                    </h6>
                                    <ol class="ps-3 mb-0 small">
                                        <li class="mb-2">{{ $t('messages.open_banking_app') }}</li>
                                        <li class="mb-2">{{ $t('messages.scan_qr_code_to_make_payment') }} <strong>{{ formatCurrency(sepayPaymentData.amount) }}</strong></li>
                                        <li class="mb-0">{{ $t('messages.alternatively_transfer_manually') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer bg-light border-top">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt text-success me-1"></i>
                                    {{ $t('messages.payment_secured_by_sepay') }}
                                </small>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('messages.close') }}</button>
                            </div>
                        </div>

                    </div>

                    <!-- Loading State -->
                    <div v-else class="text-center py-5">
                        <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0 text-muted">{{ $t('messages.generating_payment_information') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                notes: '',
                detail_address: '',
            },
            // user_info:
            defaultAddress: '',
            showLocationDropdown: false,
            currentLocationLevel: 'city',
            selectedCity: null,
            selectedDistrict: null,
            selectedWard: null,
            selectedShipping: 'free',
            selectedPaymentMethod: 'sepay', // Đặt SePay làm mặc định
            isProcessingOrder: false,
            sepayPaymentData: {}, // Data cho SePay
            sepayQrExpiryTime: 0, // Thời gian hết hạn QR SePay (giây)
            sepayQrCountdownInterval: null, // Interval cho countdown SePay
            isReloadingSepayQR: false, // Flag cho reload QR SePay
            lastSepayOrderData: null, // Lưu order data cuối cùng để reload
            isCheckingStatus: false, // Flag check status
            locationData: {
                cities: [],
                districts: [],
                wards: [],
            },
            currentQRFormat: 'quickchart_png', // Định dạng QR hiện tại
            sepayStatusInterval: null, // Interval cho kiểm tra status SePay
            paymentStatusCheckInterval: 3, // Kiểm tra mỗi 3 giây (thay vì 5 giây)
            isModalOpen: false, // Track trạng thái modal
            isPaymentSuccessful: false, // Track trạng thái thanh toán thành công
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

        // javascript
        hasShippingInfo() {
            console.log('hasShippingInfo: shippingInfo raw:', this.shippingInfo);
            if (!this.shippingInfo || !this.shippingInfo.shipping_info) {
                console.log('hasShippingInfo => false (missing shippingInfo or shipping_info)');
                return false;
            }

            const shippingData = this.shippingInfo.shipping_info;
            if (!shippingData || typeof shippingData !== 'object') {
                console.log('hasShippingInfo => false (invalid shippingData format)');
                return false;
            }

            try { console.log('hasShippingInfo: shippingData keys =', Object.keys(shippingData)); } catch(e) { console.log(e); }

            if (this.shippingInfo.is_logged_in) {
                console.log('hasShippingInfo: user is logged in, synced_from_db =', !!shippingData.synced_from_db);

                if (!shippingData.synced_from_db) {
                    console.log('hasShippingInfo => false (logged in but not synced_from_db)');
                    return false;
                }

                // check total_addresses
                const hasAddresses = Number(shippingData.total_addresses) > 0;
                // check current object fields if present
                const current = shippingData.current;
                const currentExists = !!current && typeof current === 'object';
                const currentFields = {
                    name: currentExists ? (current.name ?? '') : '',
                    phone: currentExists ? (current.phone ?? '') : '',
                    address: currentExists ? (current.address ?? '') : '',
                    full_address: currentExists ? (current.full_address ?? '') : ''
                };
                const presentCurrent = Object.values(currentFields).filter(v => String(v).trim() !== '').length > 0;

                // check top-level shippingData fields (some responses place fields at root)
                const topFields = {
                    name: shippingData.name ?? '',
                    phone: shippingData.phone ?? '',
                    address: shippingData.address ?? '',
                    full_address: shippingData.full_address ?? ''
                };
                const presentTop = Object.values(topFields).filter(v => String(v).trim() !== '').length > 0;

                console.log('hasShippingInfo: total_addresses =', shippingData.total_addresses, ', hasAddresses =', hasAddresses);
                console.log('hasShippingInfo: current exists =', currentExists, ', presentCurrent =', presentCurrent, ', presentTop =', presentTop);
                console.log('hasShippingInfo: present top fields =', topFields, ' current fields =', current);

                const result = hasAddresses || presentCurrent || presentTop;
                console.log('hasShippingInfo (logged in) =>', !!result, ' (reason: ' + (hasAddresses ? 'total_addresses>0' : presentCurrent ? 'current has fields' : presentTop ? 'top-level has fields' : 'none') + ')');
                return !!result;
            }

            // guest path unchanged
            const keysToCheck = ['name', 'address', 'phone', 'ward_id', 'full_address'];
            const available = [];
            const unavailable = [];

            keysToCheck.forEach(k => {
                const v = shippingData[k];
                const ok = v !== null && v !== undefined && String(v).trim() !== '';
                if (ok) available.push({ key: k, value: v });
                else unavailable.push(k);
            });

            console.log('hasShippingInfo (guest): available keys =', available, ', unavailable =', unavailable);

            const hasAny = available.length > 0;
            console.log('hasShippingInfo (guest) =>', !!hasAny, (hasAny ? 'some guest fields present' : 'no useful guest fields'));
            return !!hasAny;
        },

        showShippingSection() {
            console.log('showShippingSection: auth =', this.auth);
            const authPresent = !!(this.auth && this.auth.user);
            console.log('showShippingSection: auth.user present =', authPresent);
            const shippingPresent = !!this.hasShippingInfo;
            console.log('showShippingSection: hasShippingInfo =', shippingPresent);
            const visible = shippingPresent && authPresent;
            if (!visible) {
                if (!shippingPresent) console.log('showShippingSection => false (no shipping info)');
                if (!authPresent) console.log('showShippingSection => false (no authenticated user)');
            } else {
                console.log('showShippingSection => true (will display section)');
            }
            return visible;
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
        this.loadShippingInfoToForm();

        console.log('auth', this.auth)
        console.log('auth.user', this.auth.user)
        // this.loadUserInfo()
        this.loadLocationData();
        // Khởi tạo modal
        try {
            const modalElement = document.getElementById('sepayQRModal');
            if (modalElement && typeof Modal !== 'undefined') {
                this.sepayModal = new Modal(modalElement);

                // Lắng nghe sự kiện mở modal
                modalElement.addEventListener('shown.bs.modal', () => {
                    console.log('💳 Modal opened - Starting payment verification');
                    this.isModalOpen = true;
                    this.isPaymentSuccessful = false;
                });

                // Lắng nghe sự kiện đóng modal
                modalElement.addEventListener('hidden.bs.modal', () => {
                    console.log('❌ Modal closed - Stopping payment verification');
                    this.isModalOpen = false;
                    this.stopAllPaymentChecking();
                });
            } else {
                console.warn('Modal element not found or Bootstrap Modal not available');
            }

        } catch (error) {
            console.error('Modal initialization error:', error);
        }
    },
    beforeUnmount() {
        if (this.sepayQrCountdownInterval) {
            clearInterval(this.sepayQrCountdownInterval)
        }
        if (this.sepayStatusInterval) {
            clearInterval(this.sepayStatusInterval)
        }
    },
    methods: {
        // Method để tự động điền formData từ shippingInfo
        loadShippingInfoToForm() {
            if (this.shippingInfo && this.shippingInfo.shipping_info) {
                const shippingData = this.shippingInfo.shipping_info;
                console.log('Loading shipping info to form:', shippingData);

                // Nếu user đã đăng nhập và có d��� liệu từ database
                if (this.shippingInfo.is_logged_in && shippingData.synced_from_db && shippingData.current) {
                    const current = shippingData.current;
                    this.formData.name = current.name || '';
                    this.formData.address = current.full_address || '';
                    this.formData.phone = current.phone || '';
                    this.formData.ward_id = current.ward_id || '';
                    this.formData.email = current.email || '';

                    // Gán emails từ user auth nếu có
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
            console.log('$checkoutId:', this.checkoutId);

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
                    payment_method: this.selectedPaymentMethod,
                    checkoutId: this.checkoutId,
                    ward_id: this.selectedWard ?? '',
                }

                console.log('Order data prepared:', orderData);
                console.log('customer_info:', this.formData);

                if (this.selectedPaymentMethod === 'sepay') {
                    console.log('Processing SePay payment...');
                    await this.processSepayPayment(orderData)
                } else {
                    console.log('Processing other payment method...');
                    let paymentResult = await this.processOtherPayment(orderData);
                    if (!this.shippingInfo && this.auth && this.auth.user || !this.shippingInfo.shipping_info && this.auth && this.auth.user) {
                        await this.saveShippingInfo();
                    }

                    // Kiểm tra kết quả thanh toán
                    if (paymentResult && paymentResult.success) {
                        Swal.fire({
                            title: this.$t('messages.payment_cod_success'),
                            icon: "success",
                            draggable: true,
                            confirmButtonText: this.$t('messages.view_orders'),
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (this.auth && this.auth.user) {
                                    this.$inertia.visit('/profile#orders');
                                } else {
                                    this.$inertia.visit(route('dashboard'));
                                }
                            }
                        });
                    } else {
                        // Hiển thị thông báo lỗi nếu thanh toán thất bại
                        Swal.fire({
                            icon: 'error',
                            title: this.$t('messages.payment_failed') || 'Thanh toán thất bại!',
                            text: paymentResult && paymentResult.message ? paymentResult.message : (this.$t('messages.try_again') || 'Vui lòng thử lại.'),
                            showConfirmButton: true
                        });
                    }
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
        async saveShippingInfo() {
            const result = await Swal.fire({
                title: this.$t('messages.save_address_title'),
                text: this.$t('messages.save_address_text'),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Có',
                cancelButtonText: 'Không',
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            if (result.isConfirmed) {
                // Gọi API lưu địa chỉ
                try {
                    await axios.post('/api/profile/addresses', {
                        name: this.formData.name,
                        phone: this.formData.phone,
                        address: this.formData.address + this.formData.detail_address,
                        ward_id: this.formData.ward_id,
                        is_default: true,
                        label: 'home'
                    });
                    Swal.fire({
                        icon: 'success',
                        title: this.$t('messages.save_address_success'),
                        showConfirmButton: false,
                        timer: 1500
                    });
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: this.$t('messages.save_address_failed'),
                        text: e.response?.data?.message || this.$t('messages.try_again'),
                        showConfirmButton: true
                    });
                }
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
        async processSepayPayment(orderData) {
            try {
                console.log('Processing SePay payment with existing PaymentController:', orderData);

                let response;
                if (this.auth && this.auth.user) {
                    response = await axios.post('/api/payment/sepay/create', orderData, {
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                }else {
                    response = await axios.post('/api/session/orders', {
                        ...orderData,
                        payment_method: this.selectedPaymentMethod
                    }, {
                        headers: { 'Accept': 'application/json' }
                    });
                }
                console.log('SePay API response:', response.data);

                if (response.data.success) {
                    this.sepayPaymentData = response.data
                    this.lastSepayOrderData = orderData; // Lưu order data để reload sau này
                    console.log('SePay payment data:', this.sepayPaymentData);

                    // Khởi động timer đếm ngược 5 phút (300 giây) khi tạo QR thành công
                    this.startSepayQRCountdown(300);

                    this.sepayModal.show()
                    this.startSepayPaymentStatusCheck()
                    return response.data;
                } else {
                    throw new Error(response.data.message || 'Không thể tạo thanh toán SePay')
                }
            } catch (error) {
                console.error('SePay payment error:', error)
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
                let response;
                if (this.auth && this.auth.user) {
                    response = await axios.post('/api/orders', {
                        ...orderData,
                        payment_method: this.selectedPaymentMethod
                    }, {
                        headers: { 'Accept': 'application/json' }
                    });
                }else {
                    response = await axios.post('/api/session/orders', {
                        ...orderData,
                        payment_method: this.selectedPaymentMethod
                    }, {
                        headers: { 'Accept': 'application/json' }
                    });
                }

                if (response.data.success) {
                    console.log('response', response.data)
                    return response.data;
                } else {
                    throw new Error(response.data.message || 'Đặt hàng thất bại');
                }
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
            // Sync ward_id to formData
            this.formData.ward_id = ward.id;
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

        startSepayPaymentStatusCheck() {
            // Kiểm tra trạng thái thanh toán SePay mỗi 5 giây
            this.sepayStatusInterval = setInterval(async () => {
                await this.checkSepayPaymentStatus()
            }, 5000)
        },

        async checkSepayPaymentStatus() {
            // Kiểm tra các điều kiện để dừng việc check status
            if (!this.sepayPaymentData.order_id ||
                this.isCheckingStatus ||
                !this.isModalOpen ||
                this.isPaymentSuccessful) {
                console.log('Skipping SePay status check - conditions not met');
                return;
            }

            this.isCheckingStatus = true;

            try {
                const response = await axios.get('/api/payment/sepay/check-status', {
                    params: { orderId: this.sepayPaymentData.order_id },
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                console.log('🔍 Checking SePay payment status:', response.data);

                if (response.data.success) {
                    if (response.data.status === 'paid') {
                        // Đánh dấu thanh toán thành công để dừng check tiếp
                        this.isPaymentSuccessful = true;

                        // Dừng tất cả interval
                        this.stopAllPaymentChecking();

                        // Đóng modal
                        if (this.sepayModal) {
                            this.sepayModal.hide();
                        }
                        if (!this.shippingInfo || !this.shippingInfo.shipping_info) {
                            await this.saveShippingInfo();
                        }

                        Swal.fire({
                            title: this.$t('messages.payment_success'),
                            icon: "success",
                            draggable: true,
                            confirmButtonText: this.$t('messages.view_orders'),
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (this.auth && this.auth.user) {
                                    this.$inertia.visit('/profile#orders');
                                } else {
                                    this.$inertia.visit(route('dashboard'));
                                }
                            }
                        });

                    } else if (response.data.status === 'expired') {
                        // QR code đã hết hạn
                        this.stopAllPaymentChecking();

                        Swal.fire({
                            position: "center",
                            icon: "warning",
                            title: this.$t('messages.payment_session_expired'),
                            text: this.$t('messages.please_try_again'),
                            confirmButtonText: this.$t('messages.ok'),
                            showConfirmButton: true
                        });
                    }
                }
            } catch (error) {
                console.error('❌ Check SePay payment status error:', error);

                // Chỉ hiển thị lỗi nếu không phải lỗi mạng thông thường
                if (!error.code || error.code !== 'NETWORK_ERROR') {
                    console.warn('Payment status check failed:', error.message);
                }
            } finally {
                this.isCheckingStatus = false;
            }
        },

        async showPaymentSuccessNotification() {
            // Hiển thị thông báo thành công với SweetAlert2
            return new Promise((resolve) => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: this.$t('messages.payment_successful'),
                    html: `
                        <div class="d-flex flex-column align-items-center">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <p class="mb-2">${this.$t('messages.payment_completed_successfully')}</p>
                            <p class="text-muted small">${this.$t('messages.order_id')}: #${this.sepayPaymentData.order_id}</p>
                            <p class="text-muted small">${this.$t('messages.redirecting_to_order_details')}</p>
                        </div>
                    `,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    backdrop: true,
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'payment-success-popup'
                    },
                    didOpen: () => {
                        // Animation cho icon
                        const icon = Swal.getPopup().querySelector('.fa-check-circle');
                        if (icon) {
                            icon.style.animation = 'bounce 0.6s ease-in-out';
                        }
                    },
                    willClose: () => {
                        resolve();
                    }
                });
            });
        },

        async updateOrderStatus(orderId, status) {
            try {
                const response = await axios.put(`/api/orders/${orderId}/status`, {
                    status: status,
                    payment_status: 'paid',
                    payment_method: 'sepay',
                    paid_at: new Date().toISOString()
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('✅ Order status updated using PaymentController:', response.data);
                return response.data;
            } catch (error) {
                console.error('❌ Update order status error:', error);
                throw error;
            }
        },


        // Add missing validation method
        // javascript
        validateForm() {
            // normalize and inspect inputs
            const raw = {
                name: this.formData.name,
                phone: this.formData.phone,
                email: this.formData.email,
                address: this.formData.address,
                ward_id: this.formData.ward_id,
                detail_address: this.formData.detail_address
            };

            const name = (raw.name ?? '').toString().trim();
            const phoneRaw = (raw.phone ?? '').toString();
            const phoneSanitized = phoneRaw.replace(/\D/g, '');
            const email = (raw.email ?? '').toString().trim();
            const address = (raw.address ?? '').toString().trim();
            const detail = (raw.detail_address ?? '').toString().trim();

            // console.log('validateForm start — raw:', raw);
            // console.log('validateForm — normalized:', { name, phoneRaw, phoneSanitized, email, address, detail });

            // required fields check
            const missing = [];
            if (!name) missing.push('name');
            if (!phoneSanitized) missing.push('phone');
            if (!email) missing.push('email');

            if (missing.length) {
                console.log('validateForm => missing required fields:', missing);
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: this.$t('messages.please_fill_required_fields'),
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }

            // email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const emailValid = emailRegex.test(email);
            // console.log('validateForm => email check:', { email, emailValid });
            if (!emailValid) {
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: this.$t('messages.invalid_email_format'),
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }

            // phone format (sanitized)
            const phoneRegex = /^[0-9]{10,11}$/;
            const phoneValid = phoneRegex.test(phoneSanitized);
            // console.log('validateForm => phone check:', { phoneRaw, phoneSanitized, phoneValid });

            const phoneAllowedRegex = /^[0-9+\s]+$/;

            if (!phoneAllowedRegex.test(phoneRaw)) {
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: this.$t('messages.invalid_phone_format'),
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }

            if (!phoneRegex.test(phoneSanitized)) {
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: this.$t('messages.invalid_phone_format'),
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }

            // address presence
            const hasAddress = address !== '' || detail !== '';
            // console.log('validateForm => address check:', { address, detail, hasAddress });
            if (!hasAddress) {
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: this.$t('messages.please_enter_address'),
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }

            // console.log('validateForm => all checks passed');
            return true;
        },

        formatCurrency(amount) {
            // Format currency với dấu phẩy phân cách hàng nghìn
            if (!amount) return '0';
            return new Intl.NumberFormat('vi-VN').format(amount);
        },

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        startSepayQRCountdown(seconds) {
            // Dừng timer cũ nếu có
            if (this.sepayQrCountdownInterval) {
                clearInterval(this.sepayQrCountdownInterval);
            }

            this.sepayQrExpiryTime = seconds;

            this.sepayQrCountdownInterval = setInterval(() => {
                this.sepayQrExpiryTime--;

                if (this.sepayQrExpiryTime <= 0) {
                    this.sepayQrExpiryTime = 0;
                    clearInterval(this.sepayQrCountdownInterval);
                    this.sepayQrCountdownInterval = null;
                }
            }, 1000);
        },

        async reloadSepayQR() {
            if (!this.lastSepayOrderData || this.isReloadingSepayQR) return;

            this.isReloadingSepayQR = true;

            try {
                const response = await axios.post('/api/payment/sepay/create', this.lastSepayOrderData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.data.success) {
                    this.sepayPaymentData = response.data;
                    this.startSepayQRCountdown(300); // 5 phút mới

                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: this.$t('messages.qr_code_reloaded'),
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            } catch (error) {
                console.error('Reload SePay QR error:', error);
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: this.$t('messages.reload_qr_failed'),
                    showConfirmButton: false,
                    timer: 3000
                });
            } finally {
                this.isReloadingSepayQR = false;
            }
        },

        onQRImageError() {
            console.error('QR Image failed to load, trying fallback');
            // Thử format khác nếu hiện tại bị lỗi
            const formats = ['quickchart_png', 'qr_server', 'quickchart_svg'];
            const currentIndex = formats.indexOf(this.currentQRFormat);
            const nextIndex = (currentIndex + 1) % formats.length;

            if (nextIndex !== currentIndex) {
                console.log(`Switching from ${this.currentQRFormat} to ${formats[nextIndex]}`);
                this.currentQRFormat = formats[nextIndex];
            }
        },

        // Hàm lấy URL hình ảnh QR dựa trên định dạng hiện tại
        getQRImageUrl() {
            if (!this.sepayPaymentData.qr_formats) {
                console.error('No QR formats available');
                return '';
            }

            const format = this.currentQRFormat;
            const url = this.sepayPaymentData.qr_formats[format];

            console.log(`Getting QR URL for format: ${format}`, url);
            return url || '';
        },

        // Hàm chuyển đổi định dạng QR
        switchQRFormat(format) {
            console.log(`Switching QR format to: ${format}`);
            this.currentQRFormat = format;
        },

        // Method để dừng tất cả việc kiểm tra thanh toán
        stopAllPaymentChecking() {
            console.log('🛑 Stopping all payment checking intervals');

            // Dừng tất cả các interval liên quan đến thanh toán
            if (this.sepayStatusInterval) {
                clearInterval(this.sepayStatusInterval);
                this.sepayStatusInterval = null;
                console.log('✅ SePay status checking stopped');
            }

            if (this.paymentStatusInterval) {
                clearInterval(this.paymentStatusInterval);
                this.paymentStatusInterval = null;
                console.log('✅ General payment status checking stopped');
            }

            if (this.sepayQrCountdownInterval) {
                clearInterval(this.sepayQrCountdownInterval);
                this.sepayQrCountdownInterval = null;
                console.log('✅ QR countdown stopped');
            }

            // Reset flags
            this.isCheckingStatus = false;
            console.log('🏁 All payment checking processes stopped');
        }
    }
}
</script>
