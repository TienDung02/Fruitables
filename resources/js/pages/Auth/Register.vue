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
    <Head title="Register" />

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
                <div class="login-title">Sign in to your account</div>
                <div class="login-sub">
                    Welcome back to <b>Fruitables</b>!<br>
                    Enjoy fresh and clean agricultural products every day.
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" >


                    <div>
                        <InputLabel for="name" class="form-label" value="Name" />

                        <TextInput
                            id="name"
                            type="text"
                            class="form-control mt-1 block w-full"
                            v-model="form.name"
                            placeholder="Username"
                            required
                            autofocus
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" class="form-label" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            class="form-control mt-1 block w-full"
                            v-model="form.email"
                            placeholder="Email"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" class="form-label" value="Password" />

                        <TextInput
                            id="password"
                            type="password"
                            class="form-control mt-1 block w-full"
                            placeholder="Password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel class="form-label"
                            for="password_confirmation"
                            value="Confirm Password"
                        />

                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="form-control mt-1 block w-full"
                            placeholder="Comfirm Password"
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
                        Register
                    </PrimaryButton>

                    <div class="flex-links">
                        <Link
                            :href="route('login')"
                            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Already registered?
                        </Link>
                        <span>
                            Don't have an account?
                            <Link :href="route('register')" class="signup-link">
                                Register
                            </Link>
                        </span>
                    </div>


                </form>
            </div>
        </div>
    </div>


</template>
