<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Search from "@/Pages/Frontend/Includes/Search.vue";
import Menu from "@/Pages/Frontend/Includes/Menu.vue";
import Footer from "@/Pages/Frontend/Includes/Footer.vue";
</script>

<template>
    <Head :title="$t('messages.error_404')"/>
    <Menu></Menu>
    <Search></Search>
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $t('messages.error_404') }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.pages') }}</a></li>
            <li class="breadcrumb-item active text-white">404</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- 404 Start -->
    <div class="container-fluid py-5">
        <div class="container py-5 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <i class="bi bi-exclamation-triangle display-1 text-secondary"></i>
                    <h1 class="display-1">404</h1>
                    <h1 class="mb-4">{{ $t('messages.page_not_found') }}</h1>
                    <p class="mb-4">{{ $t('messages.page_not_found_message') }}</p>
                    <a class="btn border-secondary rounded-pill py-3 px-5" :href="route('dashboard')">{{ $t('messages.go_back_to_home') }}</a>
                </div>
            </div>
        </div>
    </div>
    <!-- 404 End -->

    <Footer></Footer>


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>
</template>

<style scoped lang="scss">

</style>
