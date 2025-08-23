import axios from 'axios';

class ProductService {
    constructor() {
        this.baseURL = 'http://127.0.0.1:8000/api';
    }

    // Fetch products with filters
    async getProducts(filters = {}) {
        try {
            const { page = 1, categoryId, search, sort, priceMin, priceMax } = filters;

            let url = `${this.baseURL}/products?page=${page}`;

            if (categoryId) url += `&category_id=${categoryId}`;
            if (search?.trim()) url += `&search=${encodeURIComponent(search.trim())}`;
            if (sort) url += `&sort=${sort}`;
            if (priceMin > 0) url += `&price_min=${priceMin}`;
            if (priceMax < 100) url += `&price_max=${priceMax}`;

            console.log('🔍 Fetching from:', url);

            const response = await axios.get(url);
            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            console.error('❌ ProductService Error:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Fetch categories
    async getCategories() {
        try {
            const response = await axios.get(`${this.baseURL}/categories`);
            return {
                success: true,
                data: response.data.data
            };
        } catch (error) {
            console.error('❌ Categories Error:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Fetch featured products
    async getFeaturedProducts() {
        try {
            const response = await axios.get(`${this.baseURL}/products/featured`);
            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            console.error('❌ Featured Products Error:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Fetch products with sorting and filters
    async getSortedProducts({ page = 1, categoryId, search, sort, priceMin, priceMax } = {}) {
        try {
            let url = `${this.baseURL}/products?page=${page}`;
            if (categoryId) url += `&category_id=${categoryId}`;
            if (search?.trim()) url += `&search=${encodeURIComponent(search.trim())}`;
            if (sort) url += `&sort_by=${sort}`;
            if (priceMin > 0) url += `&price_min=${priceMin}`;
            if (priceMax < 100) url += `&price_max=${priceMax}`;
            const response = await axios.get(url);
            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            console.error('❌ getSortedProducts Error:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }
}

export default new ProductService();
