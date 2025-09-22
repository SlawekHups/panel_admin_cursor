<?php

return [
    'prestashop' => [
        'base_url' => env('PRESTASHOP_BASE_URL'),
        'api_key' => env('PRESTASHOP_API_KEY'),
        'timeout' => 30,
    ],

    'inpost' => [
        'base_url' => env('INPOST_BASE_URL', 'https://api-shipx-pl.easypack24.net'),
        'api_token' => env('INPOST_API_TOKEN'),
        'timeout' => 30,
    ],

    'smsapi' => [
        'base_url' => env('SMSAPI_BASE_URL', 'https://api.smsapi.pl'),
        'token' => env('SMSAPI_TOKEN'),
        'timeout' => 30,
    ],
];
