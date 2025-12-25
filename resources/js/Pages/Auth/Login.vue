<template>
    <Head :title="$t('messages.log_in')" />

    <!-- Hiển thị flash login_error -->
    <div v-if="flash?.login_error" class="mb-4 text-sm font-medium text-red-600">
        {{ flash.login_error }}
    </div>

    <div class="main-login-bg">
        <div class="login-container">
            <!-- Left Panel -->
            <div class="login-left-pane">
                <img class="fruit-image-bg" src="/images/img/bg-login-2.png" alt="Fresh fruits background">
            </div>

            <!-- Right Panel -->
            <div class="login-right-pane">
                <!-- Brand -->
                <div class="fruitable-brand">
                    <span class="fruitable-logo-circle"><img class="w-75 h-75" src="/images/logo.png" alt=""></span>
                    Fruitables
                </div>

                <!-- Title -->
                <div class="login-title">{{ $t('messages.sign_in_to_account') }}</div>
                <div class="login-sub">
                    {{ $t('messages.welcome_back') }} <b>Fruitables</b>!<br>
                    {{ $t('messages.enjoy_fresh_products') }}
                </div>

                <!-- Form -->
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <InputLabel for="emails" class="form-label" :value="$t('messages.email_or_username')" />
                        <TextInput
                            id="emails"
                            type="email"
                            class="form-control mt-1 block w-full"
                            v-model="form.emails"
                            required
                            autofocus
                            autocomplete="username"
                            :placeholder="$t('messages.enter_email_username')"
                        />
                        <InputError :message="form.errors.emails" class="mt-2 text-danger"/>
                    </div>

                    <div>
                        <InputLabel for="password" class="form-label" :value="$t('messages.password')" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full form-control"
                            :placeholder="$t('messages.enter_password')"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <InputError :message="form.errors.password" class="mt-2 text-danger"/>
                    </div>
                    <div v-if="flash?.login_error" class="mb-4 text-sm font-medium text-danger">
                        {{ $t('messages.invalid_credentials') }}
                    </div>
                    <div class="mt-4 block " style="text-align: end">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600">{{ $t('messages.remember_me') }}</span>
                        </label>
                    </div>

                    <PrimaryButton :disabled="form.processing" class="btn btn-fruit">
                        {{ $t('messages.log_in') }}
                    </PrimaryButton>

                    <div class="flex-links mt-2">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="forgot-link"
                        >
                            {{ $t('messages.forgot_password') }}
                        </Link>
                        <span>
                            {{ $t('messages.dont_have_account') }}
                            <Link :href="route('register')" class="signup-link">
                                {{ $t('messages.register') }}
                            </Link>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Swal from "sweetalert2";
import { useI18n } from 'vue-i18n';

defineProps({
    canResetPassword: Boolean,
    status: String,
    flash: Object,
});

const form = useForm({
    emails: '',
    password: '',
    remember: false,
});
const { t } = useI18n();
const submit = () => {
    console.log('🚀 Sending login request...');

    form.post(route('login'), {
        onStart: () => {
            console.log('⏳ Request started');
        },
        onSuccess: (page) => {
            console.log('✅ Login successful');
            console.log('📦 Page props:', page.props);

            if (page.props.flash?.success) {
                Swal.fire('Thành công!', page.props.flash.success, 'success');
            }
        },
        onError: (errors) => {
            console.log('❌ Login failed');
            console.log('🐛 Errors:', errors);

            // Hiển thị lỗi đầu tiên
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: t('messages.invalid_credentials'),
                showConfirmButton: false,
                timer: 1500
            });
        },
        onFinish: () => {
            console.log('✅ Request finished');
            console.log('🔍 Form errors:', form.errors);
            form.reset('password');
        },
    });
};
</script>
