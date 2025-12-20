<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
                    <div>
                        <InputLabel for="name" class="form-label" :value="$t('messages.name')" />

                        <TextInput
                            id="name"
                            type="text"
                            class="form-control mt-1 block w-full"
                            v-model="form.name"
                            :placeholder="$t('messages.username_placeholder')"
                            required
                            autofocus
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" class="form-label" :value="$t('messages.email')" />

                        <TextInput
                            id="email"
                            type="email"
                            class="form-control mt-1 block w-full"
                            v-model="form.email"
                            :placeholder="$t('messages.email_placeholder')"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" class="form-label" :value="$t('messages.password')" />

                        <TextInput
                            id="password"
                            type="password"
                            class="form-control mt-1 block w-full"
                            :placeholder="$t('messages.password_placeholder')"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel class="form-label"
                            for="password_confirmation"
                            :value="$t('messages.confirm_password')"
                        />

                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="form-control mt-1 block w-full"
                            :placeholder="$t('messages.confirm_password_placeholder')"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.password_confirmation"
                        />
                    </div>

                    <PrimaryButton
                        class="btn btn-fruit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ $t('messages.register') }}
                    </PrimaryButton>

                    <div class="flex-links">
                        <Link
                            :href="route('login')"
                            class="signup-link rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            {{ $t('messages.already_registered') }}
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
