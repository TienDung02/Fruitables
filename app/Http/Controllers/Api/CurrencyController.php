<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get currency for specific locale
     */
    public function getCurrencyByLocale(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        $currency = $this->currencyService->getCurrencyByLocale($locale);

        return response()->json([
            'success' => true,
            'currency' => $currency,
            'locale' => $locale
        ]);
    }

    /**
     * Get all active currency mappings
     */
    public function getActiveMappings()
    {
        try {
            $mappings = $this->currencyService->getActiveCurrencyMappings();
            return response()->json([
                'success' => true,
                'mappings' => $mappings
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getActiveMappings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'mappings' => [],
                'error' => 'Failed to fetch currency mappings'
            ], 500);
        }
    }

    /**
     * Convert product prices to target locale
     */
    public function convertPrices(Request $request)
    {
        try {
            $products = $request->get('products', []);
            $targetLocale = $request->get('locale', app()->getLocale());

            Log::info('Converting prices for locale: ' . $targetLocale);
            Log::info('Number of products to convert: ' . count($products));

            $convertedProducts = $this->currencyService->convertProductPrices($products, $targetLocale);

            return response()->json([
                'success' => true,
                'products' => $convertedProducts,
                'locale' => $targetLocale,
                'currency' => $this->currencyService->getCurrencyByLocale($targetLocale)
            ]);
        } catch (\Exception $e) {
            Log::error('Error converting prices: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to convert prices',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suggest currency for unsupported locale
     */
    public function suggestCurrency(Request $request)
    {
        $locale = $request->get('locale');
        $currency = $this->currencyService->suggestCurrencyForLocale($locale);

        return response()->json([
            'success' => true,
            'suggested_currency' => $currency,
            'locale' => $locale
        ]);
    }

    /**
     * Convert single price between currencies
     */
    public function convertPrice(Request $request)
    {
        try {
            $price = $request->get('price');
            $fromCurrency = $request->get('from', 'VND');
            $toCurrency = $request->get('to', 'VND');

            $convertedPrice = $this->currencyService->convertPrice($price, $fromCurrency, $toCurrency);

            return response()->json([
                'success' => true,
                'original_price' => $price,
                'converted_price' => $convertedPrice,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency
            ]);
        } catch (\Exception $e) {
            Log::error('Error converting single price: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to convert price'
            ], 500);
        }
    }
}
