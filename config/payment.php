<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for payment gateways and bank account information
    |
    */

    'bank_account' => [
        'account_number' => env('BANK_ACCOUNT_NUMBER', '10002954850'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'NONG TIEN DUNG'),
        'bank_code' => env('BANK_CODE', '970423'),
        'bank_name' => env('BANK_NAME', 'TP BANK'),
    ],

    'sepay' => [
        'api_token' => env('SEPAY_API_TOKEN'),
        'secret_key' => env('SEPAY_SECRET_KEY'),
        'account_number' => env('SEPAY_ACCOUNT_NUMBER'),
        'account_name' => env('SEPAY_ACCOUNT_NAME'),
        'bank_code' => env('SEPAY_BANK_CODE'),
    ],

    'vietqr' => [
        'enabled' => env('VIETQR_ENABLED', true),
        'merchant_city' => env('VIETQR_MERCHANT_CITY', 'HO CHI MINH'),
        'country_code' => env('VIETQR_COUNTRY_CODE', 'VN'),
        'currency_code' => env('VIETQR_CURRENCY_CODE', '704'), // VND
    ],
];
