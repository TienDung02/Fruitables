// resources/js/stores/cart.ts

import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

// Định nghĩa kiểu dữ liệu cho state
interface CartItem {
    quantity: number;
    productVariant_id: number;
}
interface CartState {
    count: number;
    cartItems: CartItem[];
}

export const useCartStore = defineStore('cart', {
    // Khai báo kiểu dữ liệu cho state
    state: (): CartState => ({
        count: 0,
        cartItems: [],
    }),
    actions: {
        async fetchCartCount() {
            try {
                const authStore = useAuthStore();
                const user = authStore.user;

                if (user) {
                    // User is logged in, get from database
                    const response = await axios.get('/api/cart/count');
                    this.count = response.data.count;
                } else {
                    // User not logged in, get from session
                    const response = await axios.get('/api/session/cart/count');
                    this.count = response.data.count;
                }
            } catch (error) {
                console.error('Lỗi khi lấy số lượng giỏ hàng:', error);
                this.count = 0;
            }
        },

        async addToCart(productVariant_id: number, quantity: number = 1) {
            try {
                const authStore = useAuthStore();
                const user = authStore.user;

                if (user) {
                    // User is logged in, add to database
                    await axios.post('/api/cart', {
                        productVariant_id: productVariant_id,
                        quantity: quantity
                    });
                } else {
                    // User not logged in, add to session
                    await axios.post('/api/session/cart', {
                        productVariant_id: productVariant_id,
                        quantity: quantity
                    });
                }

                // Update count
                await this.fetchCartCount();
            } catch (error) {
                console.error('Lỗi khi thêm vào giỏ hàng:', error);
                throw error;
            }
        },

        async syncSessionToDatabase() {
            try {
                const authStore = useAuthStore();
                if (authStore.user) {
                    await axios.post('/api/sync-session');
                    await this.fetchCartCount();
                }
            } catch (error) {
                console.error('Lỗi khi đồng bộ session:', error);
            }
        },

        setCartCount(count: number) {
            this.count = count;
        },

        setCartItems(items: any[]) {
            this.cartItems = items;
        },
    },
});
