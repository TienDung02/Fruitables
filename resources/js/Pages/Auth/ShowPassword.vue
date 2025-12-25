<template>
    <Head title="Hoàn tất đăng ký | Fruitables" />

    <div class="main-login-bg">
        <div class="login-container">
            <!-- Left Panel - Image Background -->
            <div class="login-left-pane">
                <img class="fruit-image-bg" src="/images/img/bg-login-2.png" alt="Fresh fruits background">
            </div>

            <!-- Right Panel -->
            <div class="login-right-pane">
                <!-- Brand Logo -->
                <div class="fruitable-brand">
                    <span class="fruitable-logo-circle"><img class="w-75 h-75" src="/images/logo.png" alt=""></span>
                    Fruitables
                </div>

                <!-- Title -->
                <div class="login-title">Tạo mật khẩu an toàn</div>
                <div class="login-sub">
                    Đây là mật khẩu mạnh được hệ thống tạo tự động. Bạn có thể copy và lưu lại.
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label class="form-label">Mật khẩu mạnh</label>
                    <div class="d-flex align-items-center">
                        <input type="text" class="form-control me-2" :value="password" readonly ref="passwordInput">
                        <button type="button" class="btn btn-secondary" @click="copyPassword">
                            Copy
                        </button>
                    </div>
                </div>

                <!-- Next Button -->
                <div class="mt-4">
                    <Link :href="route('login')" class="btn btn-fruit w-100">
                        Đi đến trang đăng nhập
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

export default {
    components: {
        GuestLayout,
        PrimaryButton,
        Head,
        Link,
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
