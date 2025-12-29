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
            <div class="login-right-pane position-relative">
                <div class="me-3 languageSwitcher">
                    <LanguageSwitcher />
                </div>
                <!-- Brand -->
                <div class="fruitable-brand mb-3">
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
                        <InputError
                            :message="
                            typeof form.errors.emails === 'object'
                              ? t(`messages.${form.errors.emails.key}`, form.errors.emails.params || {})
                              : form.errors.emails
                          " class="mt-2 text-danger"
                        />

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
                        <Link :href="route('password.reset')" class="forgot-link"
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

<script>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';

export default {
    components: {
        Head,
        Link,
        InputError,
        InputLabel,
        PrimaryButton,
        TextInput,
        Checkbox,
        LanguageSwitcher,
    },

    props: {
        canResetPassword: Boolean,
        status: String,
        flash: Object,
    },

    setup() {
        const { t } = useI18n();
        return { t };
    },

    data() {
        return {
            form: useForm({
                emails: '',
                password: '',
                remember: false,
            }),
        };
    },

    methods: {
        /**
         * ✅ Translate error messages
         * Xử lý cả error dạng string và error dạng object với key/params
         */
        translateError(errors) {
            if (!errors) return '';

            // Nếu errors là array
            if (Array.isArray(errors)) {
                return errors.map(error => {
                    // Nếu error là object với key và params
                    if (typeof error === 'object' && error.key) {
                        return this.$t(error.key, error.params || {});
                    }
                    // Nếu error là string thông thường
                    return error;
                }).join(', ');
            }

            // Nếu errors là string
            return errors;
        },

        /**
         * ✅ Check nếu là rate limit error
         */
        isRateLimitError(errors) {
            if (!errors || !errors.emails) return false;

            const emailErrors = Array.isArray(errors.emails)
                ? errors.emails
                : [errors.emails];

            return emailErrors.some(error =>
                typeof error === 'object' &&
                error.key &&
                error.key.includes('rate_limit')
            );
        },

        /**
         * Submit form
         */
        submit() {
            console.log('🚀 Sending login request...');

            this.form.post(route('login'), {
                onStart: () => {
                    console.log('⏳ Request started');
                },

                onSuccess: (page) => {
                    console.log('✅ Login successful');
                    console.log('📦 Page props:', page.props);

                    if (page.props.flash?.success) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: this.$t('messages.login_success'),
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                },

                onError: (errors) => {
                    console.log('❌ Login failed');
                    console.log('🐛 Errors:', errors);

                    // ✅ Kiểm tra nếu là rate limit error
                    if (this.isRateLimitError(errors)) {
                        const emailError = Array.isArray(errors.emails)
                            ? errors.emails[0]
                            : errors.emails;

                        const minutes = emailError.params?.minutes || 1;
                        const translatedMessage =
                            emailError?.key
                                ? this.t('messages.rate_limit.login_attempts', { minutes })
                                : emailError;



                        Swal.fire({
                            icon: 'warning',
                            title: this.$t('messages.rate_limit_title'),
                            text: translatedMessage,
                            confirmButtonText: this.$t('messages.ok'),
                        });
                    } else {
                        // ✅ Error thông thường (sai mật khẩu)
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: this.$t('messages.invalid_credentials'),
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                },

                onFinish: () => {
                    console.log('✅ Request finished');
                    console.log('🔍 Form errors:', this.form.errors);
                    this.form.reset('password');
                },
            });
        },
    },
};
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
