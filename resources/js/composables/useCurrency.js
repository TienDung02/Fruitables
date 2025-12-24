import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

export function useCurrency() {
    const currentLocale = ref(localStorage.getItem('locale') || 'vi')
    const currentCurrency = ref(null)
    const currencyMappings = ref({})
    const isLoading = ref(false)

    // Get current currency info
    const currencyInfo = computed(() => ({
        code: currentCurrency.value?.code || 'VND',
        symbol: currentCurrency.value?.symbol || '₫',
        position: currentCurrency.value?.position || 'right',
        decimals: currentCurrency.value?.decimal_places || 0
    }))

    // Listen to locale changes from i18n
    const handleLocaleChange = async (event) => {
        const newLocale = event.detail.locale
        console.log('Currency system detected locale change:', newLocale)
        await setCurrencyFromLocale(newLocale)
    }

    /**
     * Load currency mappings from API
     */
    const loadCurrencyMappings = async () => {
        try {
            const response = await axios.get('/api/currency/mappings')
            currencyMappings.value = response.data.mappings
            console.log('Loaded currency mappings:', currencyMappings.value)
        } catch (error) {
            console.error('Failed to load currency mappings:', error)
        }
    }

    /**
     * Get currency for specific locale
     */
    const getCurrencyForLocale = async (locale) => {
        try {
            isLoading.value = true
            const response = await axios.get(`/api/currency/by-locale?locale=${locale}`)
            return response.data.currency
        } catch (error) {
            console.error('Failed to get currency for locale:', error)
            return await suggestCurrencyForLocale(locale)
        } finally {
            isLoading.value = false
        }
    }

    /**
     * Suggest currency for unsupported locale
     */
    const suggestCurrencyForLocale = async (locale) => {
        try {
            const response = await axios.get(`/api/currency/suggest?locale=${locale}`)
            return response.data.suggested_currency
        } catch (error) {
            console.error('Failed to suggest currency:', error)
            return null
        }
    }

    /**
     * Set currency based on locale
     */
    const setCurrencyFromLocale = async (locale) => {
        console.log('Setting currency for locale:', locale)
        currentLocale.value = locale

        // Check if we have mapping cached
        if (currencyMappings.value[locale]) {
            currentCurrency.value = currencyMappings.value[locale]
            console.log('Using cached currency:', currentCurrency.value)
        } else {
            // Get from API
            currentCurrency.value = await getCurrencyForLocale(locale)
            console.log('Fetched currency from API:', currentCurrency.value)
        }

        // Store in localStorage for persistence
        if (currentCurrency.value) {
            localStorage.setItem('current_currency', JSON.stringify(currentCurrency.value))
        }
        localStorage.setItem('locale', locale)
    }

    /**
     * Format price with current currency
     */
    const formatPrice = (price) => {
        if (!price || !currentCurrency.value) return price

        const formattedPrice = Number(price).toLocaleString('en-US', {
            minimumFractionDigits: currencyInfo.value.decimals,
            maximumFractionDigits: currencyInfo.value.decimals
        })

        if (currencyInfo.value.position === 'left') {
            return `${currencyInfo.value.symbol}${formattedPrice}`
        } else {
            return `${formattedPrice}${currencyInfo.value.symbol}`
        }
    }

    /**
     * Convert price between currencies
     */
    const convertPrice = async (price, fromCurrency, toCurrency) => {
        try {
            const response = await axios.post('/api/currency/convert', {
                price,
                from: fromCurrency,
                to: toCurrency
            })
            return response.data.converted_price
        } catch (error) {
            console.error('Failed to convert price:', error)
            return price
        }
    }

    /**
     * Convert and format price for current locale
     */
    const convertAndFormatPrice = async (price, fromCurrency = 'VND') => {
        if (!currentCurrency.value) return formatPrice(price)

        const convertedPrice = await convertPrice(price, fromCurrency, currencyInfo.value.code)
        return formatPrice(convertedPrice)
    }

    /**
     * Initialize currency system
     */
    const initializeCurrency = async () => {
        // Add event listener for locale changes
        window.addEventListener('locale-changed', handleLocaleChange)

        // Load mappings first
        await loadCurrencyMappings()

        // Try to get from localStorage
        const storedCurrency = localStorage.getItem('current_currency')
        const storedLocale = localStorage.getItem('locale')

        if (storedCurrency && storedLocale) {
            try {
                currentCurrency.value = JSON.parse(storedCurrency)
                currentLocale.value = storedLocale
                console.log('Restored currency from localStorage:', currentCurrency.value)
            } catch (e) {
                console.error('Failed to parse stored currency:', e)
                await setCurrencyFromLocale(currentLocale.value)
            }
        } else {
            // Get default for current locale
            await setCurrencyFromLocale(currentLocale.value)
        }
    }

    /**
     * Cleanup event listeners
     */
    const cleanup = () => {
        window.removeEventListener('locale-changed', handleLocaleChange)
    }

    // Watch for locale changes
    watch(currentLocale, async (newLocale) => {
        await setCurrencyFromLocale(newLocale)
    })

    return {
        currentLocale,
        currentCurrency,
        currencyMappings,
        currencyInfo,
        isLoading,
        loadCurrencyMappings,
        getCurrencyForLocale,
        setCurrencyFromLocale,
        formatPrice,
        convertPrice,
        convertAndFormatPrice,
        initializeCurrency,
        cleanup
    }
}
