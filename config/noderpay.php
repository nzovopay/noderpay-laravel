<?php

return [
    'api_key' => env('NODERPAY_API_KEY'),
    'store_id' => env('NODERPAY_STORE_ID'),
    'base_url' => env('NODERPAY_BASE_URL', 'https://api.noderpay.com'),
    'webhook_secret' => env('NODERPAY_WEBHOOK_SECRET'),
    'timeout' => env('NODERPAY_TIMEOUT', 25),
    'connect_timeout' => env('NODERPAY_CONNECT_TIMEOUT', 5),
    'max_retries' => env('NODERPAY_MAX_RETRIES', 2),
    'debug' => env('NODERPAY_DEBUG', false),
];
