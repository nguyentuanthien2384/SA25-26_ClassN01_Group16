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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key' => env('MOMO_ACCESS_KEY'),
        'secret_key' => env('MOMO_SECRET_KEY'),
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'redirect_url' => env('MOMO_REDIRECT_URL'),
        'ipn_url' => env('MOMO_IPN_URL'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'endpoint' => env('PAYPAL_ENDPOINT', 'https://api-m.sandbox.paypal.com'),
        'return_url' => env('PAYPAL_RETURN_URL'),
        'cancel_url' => env('PAYPAL_CANCEL_URL'),
    ],

    'vietqr' => [
        'bank' => env('VIETQR_BANK'),
        'account' => env('VIETQR_ACCOUNT'),
        'name' => env('VIETQR_NAME'),
        'template' => env('VIETQR_TEMPLATE', 'compact2'),
    ],

    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL'),
        'ipn_url' => env('VNPAY_IPN_URL'),
    ],

];
