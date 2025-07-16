<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'esewa' => [
        'merchant_id' => env('ESEWA_MERCHANT_ID', 'EPAYTEST'),
        'secret_key' => env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q'),
        'base_url' => env('ESEWA_BASE_URL', 'https://epay.esewa.com.np'),
        'payment_url' => env('ESEWA_PAYMENT_URL', 'https://epay.esewa.com.np/api/epay/main/v2/form'),
        'status_check_url' => env('ESEWA_STATUS_CHECK_URL', 'https://epay.esewa.com.np/api/epay/transaction/status/'),
        'success_url' => env('ESEWA_SUCCESS_URL', env('APP_URL') . '/payment/esewa/success'),
        'failure_url' => env('ESEWA_FAILURE_URL', env('APP_URL') . '/payment/esewa/failure'),
    ],

    'khalti' => [
        'public_key' => env('KHALTI_PUBLIC_KEY', 'test_public_key_dc74e0fd57cb46cd93832aee0a390234'),
        'secret_key' => env('KHALTI_SECRET_KEY', 'test_secret_key_f59e8b7d18b4499ca40f68195a846e9b'),
        'base_url' => env('KHALTI_BASE_URL', 'https://dev.khalti.com/api/v2'), // Test environment
        'success_url' => env('KHALTI_SUCCESS_URL', env('APP_URL', 'http://127.0.0.1:8000') . '/payment/khalti/success'),
        'failure_url' => env('KHALTI_FAILURE_URL', env('APP_URL', 'http://127.0.0.1:8000') . '/payment/khalti/failure'),
    ],

];
