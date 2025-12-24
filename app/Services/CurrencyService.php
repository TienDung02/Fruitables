<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Get currency by locale (dynamic from database)
     */
    public function getCurrencyByLocale(string $locale): ?Currency
    {
        return Cache::remember("currency_{$locale}", 3600, function () use ($locale) {
            // First try exact match
            $currency = Currency::where('locale', $locale)
                              ->where('is_active', true)
                              ->first();

            if ($currency) {
                return $currency;
            }

            // If not found, try language code only (e.g., 'en' for 'en-US')
            $languageCode = explode('-', $locale)[0];
            $currency = Currency::where('locale', 'LIKE', "{$languageCode}%")
                              ->where('is_active', true)
                              ->first();

            if ($currency) {
                return $currency;
            }

            // If still not found, check if any currency has matching language code
            $currency = Currency::whereRaw("SUBSTRING_INDEX(locale, '-', 1) = ?", [$languageCode])
                              ->where('is_active', true)
                              ->first();

            // If no match found, return default currency
            return $currency ?: Currency::where('is_default', true)->first();
        });
    }

    /**
     * Get all active locale-currency mappings
     */
    public function getActiveCurrencyMappings()
    {
        try {
            Log::info('Fetching active currency mappings from database');

            return Cache::remember('active_currency_mappings', 3600, function () {
                try {
                    $mappings = Currency::where('is_active', true)
                                      ->select('locale', 'code', 'symbol', 'name')
                                      ->get();

                    if ($mappings->isEmpty()) {
                        Log::warning('No active currencies found in database');
                        return collect([]);
                    }

                    Log::info('Found ' . $mappings->count() . ' active currencies');
                    return $mappings->keyBy('locale');

                } catch (\Exception $e) {
                    Log::error('Database error in getActiveCurrencyMappings: ' . $e->getMessage());
                    return collect([]);
                }
            });

        } catch (\Exception $e) {
            Log::error('Cache error in getActiveCurrencyMappings: ' . $e->getMessage());
            // Fallback to direct database query without cache
            try {
                $mappings = Currency::where('is_active', true)
                                  ->select('locale', 'code', 'symbol', 'name')
                                  ->get();
                return $mappings->keyBy('locale');
            } catch (\Exception $fallbackError) {
                Log::error('Fallback query failed: ' . $fallbackError->getMessage());
                return collect([]);
            }
        }
    }

    /**
     * Convert price between currencies
     */
    public function convertPrice(float $price, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $price;
        }

        $fromCurr = Currency::where('code', $fromCurrency)->first();
        $toCurr = Currency::where('code', $toCurrency)->first();

        if (!$fromCurr || !$toCurr) {
            return $price;
        }

        // Convert to VND first (base currency), then to target currency
        $vndPrice = $price * $fromCurr->exchange_rate;
        $convertedPrice = $vndPrice / $toCurr->exchange_rate;

        return round($convertedPrice, $toCurr->decimal_places);
    }

    /**
     * Format price with currency symbol
     */
    public function formatPrice(float $price, Currency $currency): string
    {
        $formattedPrice = number_format($price, $currency->decimal_places);

        if ($currency->position === 'left') {
            return $currency->symbol . $formattedPrice;
        } else {
            return $formattedPrice . $currency->symbol;
        }
    }

    /**
     * Get all active currencies
     */
    public function getActiveCurrencies()
    {
        return Cache::remember('active_currencies', 3600, function () {
            return Currency::where('is_active', true)
                          ->orderBy('is_default', 'desc')
                          ->orderBy('name')
                          ->get();
        });
    }

    /**
     * Convert product prices for given locale (dynamic)
     */
    public function convertProductPrices($products, string $targetLocale)
    {
        $targetCurrency = $this->getCurrencyByLocale($targetLocale);

        if (!$targetCurrency) {
            return $products;
        }

        $baseCurrency = Currency::where('is_default', true)->first();

        // Handle both array and collection
        if (is_array($products)) {
            $products = collect($products);
        }

        $products->transform(function ($product) use ($targetCurrency, $baseCurrency) {
            if (isset($product->variants)) {
                foreach ($product->variants as $variant) {
                    // Convert regular price
                    if (isset($variant->price)) {
                        $variant->original_price = $variant->price; // Store original
                        $variant->price = $this->convertPrice(
                            $variant->price,
                            $baseCurrency->code,
                            $targetCurrency->code
                        );
                        $variant->formatted_price = $this->formatPrice($variant->price, $targetCurrency);
                    }

                    // Convert sale price
                    if (isset($variant->sale_price) && $variant->sale_price) {
                        $variant->original_sale_price = $variant->sale_price; // Store original
                        $variant->sale_price = $this->convertPrice(
                            $variant->sale_price,
                            $baseCurrency->code,
                            $targetCurrency->code
                        );
                        $variant->formatted_sale_price = $this->formatPrice($variant->sale_price, $targetCurrency);
                    }
                }
            }
            return $product;
        });

        return $products;
    }

    /**
     * Auto-detect locale for unsupported languages and suggest closest currency
     */
    public function suggestCurrencyForLocale(string $locale): ?Currency
    {
        // First try to find exact match
        $currency = $this->getCurrencyByLocale($locale);
        if ($currency) {
            return $currency;
        }

        // Language region mappings for common cases
        $regionMappings = [
            'zh' => ['locale' => 'zh', 'fallback' => 'en'], // Chinese -> CNY or USD
            'ko' => ['locale' => 'ko', 'fallback' => 'en'], // Korean -> KRW or USD
            'th' => ['locale' => 'th', 'fallback' => 'en'], // Thai -> THB or USD
            'id' => ['locale' => 'id', 'fallback' => 'en'], // Indonesian -> IDR or USD
            'ms' => ['locale' => 'ms', 'fallback' => 'en'], // Malay -> MYR or USD
            'tl' => ['locale' => 'tl', 'fallback' => 'en'], // Filipino -> PHP or USD
            'hi' => ['locale' => 'hi', 'fallback' => 'en'], // Hindi -> INR or USD
        ];

        $languageCode = explode('-', $locale)[0];

        if (isset($regionMappings[$languageCode])) {
            $mapping = $regionMappings[$languageCode];

            // Try to find currency for this language
            $currency = Currency::where('locale', $mapping['locale'])->first();
            if ($currency) {
                return $currency;
            }

            // Fallback to mapped fallback locale
            return $this->getCurrencyByLocale($mapping['fallback']);
        }

        // Default fallback
        return Currency::where('is_default', true)->first();
    }

    /**
     * Clear currency cache
     */
    public function clearCache()
    {
        Cache::forget('active_currencies');
        Cache::forget('active_currency_mappings');

        // Clear individual currency caches
        $locales = Currency::pluck('locale')->unique();
        foreach ($locales as $locale) {
            Cache::forget("currency_{$locale}");
        }
    }
}
