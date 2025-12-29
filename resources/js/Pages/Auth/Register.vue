<template>
    <Head :title="$t('messages.register')" />

    <div class="main-login-bg">
        <div class="login-container">
            <!-- Left Panel - Image Background -->
            <div class="login-left-pane">
                <img class="fruit-image-bg" src="/images/img/bg-login-2.png" alt="Fresh fruits background">
            </div>

            <!-- Right Panel - Login Form -->
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
                <div class="login-title">{{ $t('messages.create_account') }}</div>
                <div class="login-sub">
                    {{ $t('messages.welcome_back') }} <b>Fruitables</b>!<br>
                    {{ $t('messages.enjoy_fresh_products') }}
                </div>

                <div v-if="emailSent" class="alert alert-success mt-4 text-center">
                    {{ $t('messages.registration_email_sent') }}
                </div>
                <div v-else>
                    <form @submit.prevent="submit">
                        <div class="mt-4">
                            <InputLabel for="email" class="form-label" :value="$t('messages.emails')" />
                            <TextInput
                                id="email"
                                type="email"
                                class="form-control mt-1 block w-full"
                                v-model="form.email"
                                :placeholder="$t('messages.email_placeholder')"
                                required
                                autocomplete="username"
                            />
                            <InputError
                                :message="
                            typeof form.errors.emails === 'object'
                              ? t(`messages.${form.errors.emails.key}`, form.errors.emails.params || {})
                              : form.errors.emails
                          " class="mt-2 text-danger"
                            />
                        </div>
                        <PrimaryButton
                            class="btn btn-fruit"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ $t('messages.register') }}
                        </PrimaryButton>
                        <div class="flex-links justify-content-end">
                            <span class="text-center">
                                <Link
                                    :href="route('login')"
                                    class="signup-link rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                {{ $t('messages.already_registered') }}
                            </Link>
                            </span>
                        </div>
                    </form>
                </div>
                <div v-if="errorMsg" class="alert alert-danger mt-2">{{ errorMsg }}</div>
            </div>
        </div>
    </div>
</template>
<script>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import Swal from "sweetalert2";
import { useI18n } from 'vue-i18n';

export default {
    components: {
        InputError,
        InputLabel,
        PrimaryButton,
        TextInput,
        Head,
        Link,
        LanguageSwitcher,
    },
    setup() {
        const { t } = useI18n();
        return { t };
    },
    data() {
        return {
            form: useForm({
                email: '',
            }),
        }
    },

    computed: {
        page() {
            return usePage()
        },

        emailSent() {
            return this.page.props.email_sent
        },

        sentEmail() {
            return this.page.props.email
        },

        errorMsg() {
            return this.page.props.error
        },
    },

    mounted() {
        console.log('Register props (mounted):', {
            emailSent: this.emailSent,
            sentEmail: this.sentEmail,
            errorMsg: this.errorMsg,
        })
    },

    watch: {
        emailSent(newVal) {
            console.log('emailSent changed:', newVal)
        },
        sentEmail(newVal) {
            console.log('sentEmail changed:', newVal)
        },
        errorMsg(newVal) {
            console.log('errorMsg changed:', newVal)
        },
    },

    methods: {
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

        submit() {
            this.form.post(route('register.emails.submit'), {
                onFinish: () => this.form.reset('email'),
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
                                ? this.t('messages.rate_limit.register_attempts', { minutes })
                                : emailError;



                        Swal.fire({
                            icon: 'warning',
                            title: this.$t('messages.rate_limit.register_attempts'),
                            text: translatedMessage,
                            confirmButtonText: this.$t('messages.ok'),
                        });
                    }
                },
            })
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
