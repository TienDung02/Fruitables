<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Register from '@/Pages/Auth/Register.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head :title="$t('messages.log_in')" />
    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
    </div>
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
                    <div class="mb-3">
                        <InputLabel for="email" class="form-label" :value="$t('messages.email_or_username')" />

                        <TextInput
                            id="email"
                            type="email"
                            class="form-control mt-1 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            :placeholder="$t('messages.enter_email_username')"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div>
                        <InputLabel class="form-label" for="password" :value="$t('messages.password')" />

                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full form-control"
                            :placeholder="$t('messages.enter_password')"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div class="mt-4 block " style="text-align: end">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600">{{ $t('messages.remember_me') }}</span>
                        </label>
                    </div>

                    <PrimaryButton
                        class="btn btn-fruit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ $t('messages.log_in') }}
                    </PrimaryButton>

                    <div class="flex-links">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="rounded-md text-sm forgot-link text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
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
