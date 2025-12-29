<template>
    <Head title="Hoàn tất đăng ký | Fruitables" />

    <div class="main-login-bg">
        <div class="login-container">
            <!-- Left Panel - Image Background -->
            <div class="login-left-pane">
                <img class="fruit-image-bg" src="/images/img/bg-login-2.png" alt="Fresh fruits background">
            </div>

            <!-- Right Panel -->
            <div class="login-right-pane position-relative">
                <div class="me-3 languageSwitcher">
                    <LanguageSwitcher />
                </div>
                <!-- Brand Logo -->
                <div class="fruitable-brand">
                    <span class="fruitable-logo-circle"><img class="w-75 h-75" src="/images/logo.png" alt=""></span>
                    Fruitables
                </div>

                <!-- Title -->
                <div class="login-title">{{ $t('messages.generate_secure_password') }}</div>
                <div class="login-sub">
                    {{ $t('messages.auto_generated_password_info') }}
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label class="form-label">{{ $t('messages.strong_password') }}</label>
                    <div class="d-flex align-items-stretch">
                        <input type="text" class="form-control me-2 w-75" :value="password" readonly ref="passwordInput">
                        <button type="button" class="btn btn-secondary w-25" @click="copyPassword">
                            {{ $t('messages.copy') }}
                        </button>
                    </div>
                </div>

                <!-- Next Button -->
                <div class="mt-4">
                    <Link :href="route('login')" class="btn btn-fruit w-100">
                        {{ $t('messages.go_to_login') }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
export default {
    components: {
        GuestLayout,
        PrimaryButton,
        Head,
        Link,
        LanguageSwitcher,
    },

    props: {
        token: {
            type: String,
            required: false, // token nhận từ route
        },
    },

    data() {
        return {
            generatedPassword: this.password || '',
        }
    },
    computed: {
        page() {
            return usePage()
        },

        password() {
            return this.page.props.password
        },
    },
    methods: {
        copyPassword() {
            const input = this.$refs.passwordInput;
            if (input) {
                input.select();
                document.execCommand('copy');
                Swal.fire({
                    icon: 'success',
                    title: 'Password đã được copy!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Copy thất bại!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            }
        },
    },
}
</script>
<style scoped>
.languageSwitcher{
    position: absolute;
    padding: 30px 20px;
    top: 0;
    right: 0;
}
.dropdown-item span{
    font-size: 13px;
    margin-left: 5px;
}
.language-switcher .flag[data-v-44967e4d] {
    width: 20px;
    height: 20px;
}
.text-white{
    padding-right: 0.5rem;
    font-size: 15px;
}
.dropdown-toggle{
    background: linear-gradient(95deg, #6da02ca8 62%, #9bea00 100%) !important;
    padding: 2px 0.75rem;
}
.dropdown-toggle:hover {
    border: 1px solid #6da02c !important;
}
.dropdown-toggle:hover > .text-white {
    color: #8bc34a !important;
}
</style>
