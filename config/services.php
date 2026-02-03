<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | Payment Services
    |--------------------------------------------------------------------------
    */

    'momo' => [
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key' => env('MOMO_ACCESS_KEY'),
        'secret_key' => env('MOMO_SECRET_KEY'),
        'redirect_url' => env('MOMO_REDIRECT_URL'),
        'ipn_url' => env('MOMO_IPN_URL'),
    ],

    'vnpay' => [
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL'),
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
    ],

    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
    ],

    'vietqr' => [
        'bank' => env('VIETQR_BANK', 'MB'),
        'account' => env('VIETQR_ACCOUNT', '0123456789'),
        'name' => env('VIETQR_NAME', 'NGUYEN VAN A'),
        'template' => env('VIETQR_TEMPLATE', 'compact2'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Microservices Infrastructure
    |--------------------------------------------------------------------------
    */

    'elasticsearch' => [
        'host' => env('ELASTICSEARCH_HOST', 'http://localhost:9200'),
        'index_prefix' => env('ELASTICSEARCH_INDEX_PREFIX', 'laravel'),
    ],

    'consul' => [
        'host' => env('CONSUL_HOST', 'localhost'),
        'port' => env('CONSUL_PORT', 8500),
        'service_host' => env('CONSUL_SERVICE_HOST', 'localhost'),
        'service_port' => env('CONSUL_SERVICE_PORT', 8000),
    ],

    'jaeger' => [
        'host' => env('JAEGER_HOST', 'localhost'),
        'port' => env('JAEGER_PORT', 6831),
        'enabled' => env('JAEGER_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Internal Microservices URLs
    |--------------------------------------------------------------------------
    | These can be discovered via Consul or hard-coded
    */

    'notification_service' => [
        'url' => env('NOTIFICATION_SERVICE_URL', 'http://localhost:9001'),
    ],

    'inventory_service' => [
        'url' => env('INVENTORY_SERVICE_URL', 'http://localhost:9002'),
    ],

    'shipping_service' => [
        'url' => env('SHIPPING_SERVICE_URL', 'http://localhost:9003'),
    ],

];
