<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Register from '@/pages/Auth/Register.vue';

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

    <Head title="Log in" />
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
                    <div class="mb-3">
                        <InputLabel for="email" class="form-label" value="Email or Username" />

                        <TextInput
                            id="email"
                            type="email"
                            class="form-control mt-1 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Enter email or username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div>
                        <InputLabel class="form-label" for="password" value="Password" />

                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full form-control"
                            placeholder="Enter your password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div class="mt-4 block " style="text-align: end">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600"
                                >Remember me</span
                            >
                        </label>
                    </div>


                    <PrimaryButton
                        class="btn btn-fruit"
                        :class=" { 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>

                    <div class="flex-links">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="rounded-md text-sm forgot-link text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Forgot your password?
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
