import { defineStore } from 'pinia';
import axios from 'axios';
import { useCartStore } from './cart';

interface User {
    id: number;
    name: string;
    email: string;
    [key: string]: any;
}

export const useAuthStore = defineStore('auth', {
    state: () => ({
        isLoggedIn: false,
        user: null as User | null,
    }),
    actions: {
        async checkAuth() {
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
                console.log('User data loaded:', this.user);
                this.isLoggedIn = true;

                // Sync session data when user is authenticated
                await this.syncSessionData();
            } catch (error) {
                this.user = null;
                this.isLoggedIn = false;
            }
        },

        async syncSessionData() {
            try {
                const cartStore = useCartStore();

                // Sync session data to database
                await axios.post('/api/sync-session');

                // Update cart count after sync
                await cartStore.fetchCartCount();

            } catch (error) {
                console.error('Lỗi khi đồng bộ session data:', error);
            }
        },

        setUser(user: User | null) {
            this.user = user;
            this.isLoggedIn = !!user;
        },

        logout() {
            this.user = null;
            this.isLoggedIn = false;
            console.log('User logged out from store');
        },

        async login(credentials: { email: string; password: string }) {
            try {
                const response = await axios.post('/login', credentials);
                await this.checkAuth(); // Reload user data after login
                return response;
            } catch (error) {
                console.error('Login error:', error);
                throw error;
            }
        }
    },
});
