// resources/js/stores/cart.ts

import { defineStore } from 'pinia';
import axios from 'axios';

// Định nghĩa kiểu dữ liệu cho state
interface CartState {
    count: number;
    cartItems: any[];
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
                const response = await axios.get('/api/cart/count');
                this.count = response.data.count;
            } catch (error) {
                console.error('Lỗi khi lấy số lượng giỏ hàng:', error);
            }
        },
        async addToCart(productVariant_id: number, quantity: number = 1) {
            try {
                const response = await axios.post('/api/cart', {
                    productVariant_id: productVariant_id,
                    quantity: quantity
                });
                this.count = Number(this.count) + quantity;
            } catch (error) {
                console.error('Lỗi khi thêm vào giỏ hàng:', error);
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
