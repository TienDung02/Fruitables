<template>
    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
        <div class="container py-5">
            <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                <div class="row g-4">
                    <div class="col-lg-3">
                        <a href="#">
                            <h1 class="text-primary mb-0">{{ $t('messages.your_site_name') }}</h1>
                        </a>
                    </div>
                    <div class="col-lg-6">

                    </div>
                    <div class="col-lg-3 mt-2">
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
                        <h4 class="text-light mb-3">{{ $t('messages.why_people_like') }}</h4>
                        <p class="mb-4">{{ $t('messages.footer_description') }}</p>
                        <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">{{ $t('messages.read_more') }}</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">{{ $t('messages.shop_info') }}</h4>
                        <Link :href="route('about.index')" class="btn-link">{{ $t('messages.about') }}</Link>
                        <Link :href="route('contact.index')" class="btn-link">{{ $t('messages.contact') }}</Link>
                        <a class="btn-link" href="">{{ $t('messages.privacy_policy') }}</a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">{{ $t('messages.contact_info') }}</h4>
                        <p>{{ $t('messages.address') }}: {{ $t('messages.dormitory_area_hcmu') }}</p>
                        <p>{{ $t('messages.email') }}: nongtiendung2309@gmail.com</p>
                        <p>{{ $t('messages.phone') }}: (+84) 335 594 771</p>
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
                    <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>{{ $t('messages.your_site_name') }}</a>, {{ $t('messages.all_rights_reserved') }}.</span>
                </div>
                <div class="col-md-6 my-auto text-center text-md-end text-white">
                    <!--/*** This template is free as long as you keep the below author's credit link/attribution link/backlink. ***/-->
                    <!--/*** If you'd like to use the template without the below author's credit link/attribution link/backlink, ***/-->
                    <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                    {{ $t('messages.designed_by') }} <a class="border-bottom text-info" href="https://www.facebook.com/nongtiendung.2309/">Nong Tien Dung</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->
</template>

<script>
import { Link } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/cart';
import { useAuthStore } from '@/stores/auth';
import { useTranslation } from '@/composables/useTranslation';

export default {
    name: 'Footer',
    components: {
        Link,
    },
    setup() {
        // rely on global $t
        return {};
    },
    data() {
        return {
            isDropdownOpen: false
        };
    },
    computed: {
        authStore() {
            return useAuthStore();
        },
        cartStore() {
            return useCartStore();
        },
        flashMessages() {
            return this.$page.props.flash || {};
        }
    },
}
</script>
<style scoped lang="scss">

</style>
