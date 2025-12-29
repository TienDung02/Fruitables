<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import Swal from 'sweetalert2';
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";

const props = defineProps({
    token: String
});
const form = useForm({
});
const submit = () => {
    form.post(route('password.retrieve.submit', { token: props.token }), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: $t('messages.username_saved_success'),
                timer: 1500,
                showConfirmButton: false,
            });
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire({
                icon: 'error',
                title: $t('messages.username_save_failed'),
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
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


<template>
    <Head :title="$t('messages.reset_password')" />

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

                <!-- Login Form -->
                <form @submit.prevent="submit">
                    <div class="mt-4">
                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <PrimaryButton
                        class="btn btn-fruit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ $t('messages.retrieve_button') }}
                    </PrimaryButton>

                    <div class="flex-links justify-content-end mt-2">
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
