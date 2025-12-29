import axios from 'axios'

// ✅ Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['Accept'] = 'application/json'

// ✅ Request interceptor: Tự động thêm locale vào mỗi request
axios.interceptors.request.use(
    (config) => {
        // Lấy locale từ localStorage (LanguageSwitcher đã lưu sẵn)
        const locale = localStorage.getItem('locale') || 'vi'

        // Thêm vào header
        config.headers['X-Locale'] = locale

        // Log để debug (có thể tắt trong production)
        if (import.meta.env.DEV) {
            console.log('🌐 API Request with locale:', locale, '| URL:', config.url)
        }

        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// ✅ Response interceptor: Handle errors globally
axios.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {
        if (import.meta.env.DEV) {
            console.error('❌ API Error:', {
                url: error.config?.url,
                status: error.response?.status,
                data: error.response?.data
            })
        }

        // Có thể thêm logic handle error chung ở đây
        // Ví dụ: redirect to login nếu 401
        if (error.response?.status === 401) {
            // window.location.href = '/login'
        }

        return Promise.reject(error)
    }
)

export default axios
