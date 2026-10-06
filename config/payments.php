<?php
return [
    'currency' => env('PAYMENT_CURRENCY', 'XAF'),
    'airtel' => [
        'enabled' => env('AIRTEL_MONEY_ENABLED', false),
        'base_url' => env('AIRTEL_MONEY_BASE_URL', 'https://openapi.airtel.africa'),
        'client_id' => env('AIRTEL_MONEY_CLIENT_ID'),
        'client_secret' => env('AIRTEL_MONEY_CLIENT_SECRET'),
        'country' => env('AIRTEL_MONEY_COUNTRY', 'TD'),
        'currency' => env('AIRTEL_MONEY_CURRENCY', 'XAF'),
        'collection_path' => env('AIRTEL_MONEY_COLLECTION_PATH', '/merchant/v1/payments/'),
        'status_path' => env('AIRTEL_MONEY_STATUS_PATH', '/standard/v1/payments/{id}'),
        'callback_secret' => env('AIRTEL_MONEY_CALLBACK_SECRET'),
    ],
    'moov' => [
        'enabled' => env('MOOV_MONEY_ENABLED', false),
        'base_url' => env('MOOV_MONEY_BASE_URL'),
        'api_key' => env('MOOV_MONEY_API_KEY'),
        'merchant_id' => env('MOOV_MONEY_MERCHANT_ID'),
        'country' => env('MOOV_MONEY_COUNTRY', 'TD'),
        'currency' => env('MOOV_MONEY_CURRENCY', 'XAF'),
        'collection_path' => env('MOOV_MONEY_COLLECTION_PATH', '/payments'),
        'status_path' => env('MOOV_MONEY_STATUS_PATH', '/payments/{id}'),
        'callback_secret' => env('MOOV_MONEY_CALLBACK_SECRET'),
    ],
];
