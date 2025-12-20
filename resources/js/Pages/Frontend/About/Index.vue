<script setup lang="ts">
import Menu from "@/Pages/Frontend/Includes/Menu.vue";
import Footer from "@/Pages/Frontend/Includes/Footer.vue";
import { Head } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';

// Remove useTranslation; rely on global $t

onMounted(() => {
    if (window.history.scrollRestoration) {
        window.history.scrollRestoration = 'manual';
    }
});

onUnmounted(() => {
    if (window.history.scrollRestoration) {
        window.history.scrollRestoration = 'auto';
    }
});
</script>

<template>
    <Head :title="$t('messages.about_title')" />
    <Menu />

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $t('messages.about_title') }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">{{ $t('messages.home') }}</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">{{ $t('messages.about') }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- About Start -->
    <div class="container-fluid about py-5" style="font-size: 1.3rem;">
        <div class="container py-5">
            <!-- Hero Section -->
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="about-img">
                        <img src="images/img/aboutus.jpg" class="img-fluid rounded" alt="About Fruitables">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h1 class="mb-4 fw-bold">{{ $t('messages.about_welcome') }} <span class="text-primary">Fruitables</span></h1>
                    <div class="alert alert-info border-primary">
                        <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>{{ $t('messages.about_introduction') }}</h5>
                        <p class="mb-3">
                            {{ $t('messages.about_intro_text') }}
                        </p>
                        <p class="mb-0">
                            {{ $t('messages.about_data_text') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Project Purpose -->
            <div class="row g-5 mt-5">
                <div class="col-12">
                    <div class="text-center mb-5">
                        <h2 class="text-primary fw-bold">{{ $t('messages.project_purpose') }}</h2>
                        <p class="lead">{{ $t('messages.project_purpose_subtitle') }}</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 h-100 text-center">
                        <i class="fas fa-code fa-4x text-primary mb-4"></i>
                        <h4 class="text-primary mb-3">{{ $t('messages.skill_practice') }}</h4>
                        <p>
                            {{ $t('messages.skill_practice_desc') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 h-100 text-center">
                        <i class="fas fa-project-diagram fa-4x text-primary mb-4"></i>
                        <h4 class="text-primary mb-3">{{ $t('messages.system_simulation') }}</h4>
                        <p>
                            {{ $t('messages.system_simulation_desc') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 h-100 text-center">
                        <i class="fas fa-graduation-cap fa-4x text-primary mb-4"></i>
                        <h4 class="text-primary mb-3">{{ $t('messages.learning_reference') }}</h4>
                        <p>
                            {{ $t('messages.learning_reference_desc') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Technology Stack -->
            <div class="row g-5 mt-5">
                <div class="col-12">
                    <div class="text-center mb-5">
                        <h2 class="text-primary fw-bold">{{ $t('messages.technology_stack') }}</h2>
                        <p class="lead">{{ $t('messages.tech_stack_subtitle') }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="tech-icon text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: linear-gradient(135deg, #8993be 0%, #6f7ad3 100%);">
                            <i class="fab fa-php fa-2x"></i>
                        </div>
                        <h4>{{ $t('messages.backend') }}</h4>
                        <p class="text-muted">PHP – Laravel 12</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="tech-icon text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: linear-gradient(135deg, #4fc08d 0%, #44a37d 100%);">
                            <i class="fab fa-vuejs fa-2x"></i>
                        </div>
                        <h4>{{ $t('messages.frontend') }}</h4>
                        <p class="text-muted">Vue.js 3 + Inertia.js</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="tech-icon text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: linear-gradient(135deg, #f29111 0%, #e27d00 100%);">
                            <i class="fas fa-database fa-2x"></i>
                        </div>
                        <h4>{{ $t('messages.database') }}</h4>
                        <p class="text-muted">MySQL</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="tech-icon text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: linear-gradient(135deg, #2496ed 0%, #0db7ed 100%);">
                            <i class="fab fa-docker fa-2x"></i>
                        </div>
                        <h4>{{ $t('messages.devops') }}</h4>
                        <p class="text-muted">Docker & Container</p>
                    </div>
                </div>
            </div>

            <!-- Scope & Important Notes -->
            <div class="row g-5 mt-5">
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4 h-100">
                        <h3 class="text-primary mb-4 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>{{ $t('messages.scope_of_use') }}</h3>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                {{ $t('messages.project_learning_only') }}
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-times-circle text-warning me-2"></i>
                                {{ $t('messages.no_guarantee') }}
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-ban text-danger me-2"></i>
                                {{ $t('messages.not_production') }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-light rounded p-4 h-100">
                        <h3 class="text-primary mb-4 fw-bold"><i class="fas fa-shield-alt me-2"></i>{{ $t('messages.important_notice') }}</h3>
                        <div class="alert alert-warning mb-3">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>{{ $t('messages.please_do_not') }}</strong>
                            <p class="mb-2 mt-2">
                                {{ $t('messages.security_warning') }}
                            </p>
                        </div>
                        <div class="alert alert-info mb-0">
                            <strong><i class="fas fa-bug me-2"></i>{{ $t('messages.security_feedback') }}</strong>
                            <p class="mb-2 mt-2">
                                {{ $t('messages.feedback_text') }}
                            </p>
                            <ul class="mb-2">
                                <li>{{ $t('messages.attack_methods') }}</li>
                                <li>{{ $t('messages.vulnerabilities') }}</li>
                                <li>{{ $t('messages.suggestions') }}</li>
                            </ul>
                            <p class="mb-0">
                                <small>{{ $t('messages.contributions_help') }}</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Author Section -->
            <div class="row g-5 mt-5">
                <div class="col-12">
                    <div class="text-center mb-5">
                        <h2 class="text-primary fw-bold">{{ $t('messages.author') }}</h2>
                    </div>
                </div>
                <div class="col-lg-8 mx-auto">
                    <div class="text-center bg-light rounded p-5">
                        <div class="mb-4">
                            <div class="bg-primary text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        </div>
                        <h4>Nong Tien Dung</h4>
                        <p class="text-primary mb-3">{{ $t('messages.full_stack_developer') }}</p>
                        <p class="mb-4">
                            {{ $t('messages.author_description') }}
                        </p>
                        <div class="team-social">
                            <a href="https://www.facebook.com/nongtiendung.2309/" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://github.com/TienDung02" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="https://www.linkedin.com/in/ti%E1%BA%BFn-d%C5%A9ng-n%C3%B4ng-243a68303/" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact for Learning -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="bg-primary text-white rounded p-5 text-center">
                        <h3 class="mb-4">{{ $t('messages.interested_project') }}</h3>
                        <p class="lead mb-4">
                            {{ $t('messages.contact_text') }}
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="/contact" class="btn btn-light px-4 py-2">
                                <i class="fas fa-envelope me-2"></i>{{ $t('messages.contact') }}
                            </a>
                            <a href="https://www.facebook.com/nongtiendung.2309/" target="_blank" class="btn btn-outline-light px-4 py-2">
                                <i class="fab fa-facebook me-2"></i>Facebook
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

   <Footer/>
</template>

<style scoped lang="scss">
</style>
