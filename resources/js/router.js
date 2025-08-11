import { createRouter, createWebHistory } from 'vue-router';
import  categories from '@/pages/Frontend/Categories/Index.vue';
import  products from '@/pages/Frontend/Products/Index.vue';

const routes = [
    {
        path: '/Categories',
        components: categories
    },
    {
        path: '/Products',
        components: products
    }
]
const router = createRouter({
    history: createWebHistory(),
    routes
});
export default router;
