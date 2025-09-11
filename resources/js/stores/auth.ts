import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        isLoggedIn: false,
        user: null,
    }),
    actions: {
        async checkAuth() {
            try {
                const response = await axios.get('/api/user'); // Gọi API kiểm tra
                this.user = response.data;
                this.isLoggedIn = true;
            } catch (error) {
                this.user = null;
                this.isLoggedIn = false;
                console.error('Lỗi khi lấy số lượng giỏ hàng:', error);
            }
        }
    }
});
