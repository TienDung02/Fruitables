<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import Swal from 'sweetalert2';


const props = defineProps({
    token: String
});
const form = useForm({
    username: '',
});
const submit = () => {
    form.post(route('register.username.submit', { token: props.token }), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Username đã được lưu!',
                timer: 1500,
                showConfirmButton: false,
            });
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire({
                icon: 'error',
                title: 'Lưu username thất bại!',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
};

</script>

<template>
    <Head :title="$t('messages.register')" />

    <div class="main-login-bg">
        <div class="login-container">
            <!-- Left Panel - Image Background -->
            <div class="login-left-pane">
                <img class="fruit-image-bg" src="/images/img/bg-login-2.png" alt="Fresh fruits background">
            </div>

            <!-- Right Panel - Login Form -->
            <div class="login-right-pane">
                <!-- Brand Logo -->
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

                <!-- Login Form -->
                <form @submit.prevent="submit">
                    <div class="mt-4">
                        <InputLabel for="username" class="form-label" :value="'Nhập tên người dùng'" />

                        <TextInput
                            id="username"
                            type="text"
                            class="form-control mt-1 block w-full"
                            v-model="form.username"
                            :placeholder="$t('messages.username_placeholder')"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <PrimaryButton
                        class="btn btn-fruit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ $t('messages.register') }}
                    </PrimaryButton>

                    <div class="flex-links justify-content-end mt-2">
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
        </div>
    </div>
</template>
