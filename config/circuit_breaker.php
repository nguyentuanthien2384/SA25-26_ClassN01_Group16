<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Circuit Breaker Configuration
    |--------------------------------------------------------------------------
    |
    | Configure circuit breaker behavior for external services
    |
    */

    'enabled' => env('CIRCUIT_BREAKER_ENABLED', true),

    // Default settings for all services
    'defaults' => [
        'failure_threshold' => env('CIRCUIT_BREAKER_FAILURE_THRESHOLD', 5),
        'timeout' => env('CIRCUIT_BREAKER_TIMEOUT', 60), // seconds
        'half_open_timeout' => env('CIRCUIT_BREAKER_HALF_OPEN_TIMEOUT', 30),
        'request_timeout' => env('CIRCUIT_BREAKER_REQUEST_TIMEOUT', 30),
        'max_retries' => env('CIRCUIT_BREAKER_MAX_RETRIES', 3),
    ],

    // Service-specific overrides
    'services' => [
        'momo' => [
            'failure_threshold' => 3,
            'timeout' => 120,
            'request_timeout' => 30,
            'max_retries' => 2,
            'fallback' => 'qrcode', // Fallback payment method
        ],
        'vnpay' => [
            'failure_threshold' => 5,
            'timeout' => 60,
            'request_timeout' => 30,
            'max_retries' => 3,
            'fallback' => 'cod',
        ],
        'paypal' => [
            'failure_threshold' => 5,
            'timeout' => 90,
            'request_timeout' => 45,
            'max_retries' => 2,
            'fallback' => 'cod',
        ],
        'notification-service' => [
            'failure_threshold' => 10,
            'timeout' => 30,
            'request_timeout' => 10,
            'max_retries' => 5,
        ],
    ],

    // Monitoring
    'alerts' => [
        'enabled' => env('CIRCUIT_BREAKER_ALERTS_ENABLED', false),
        'email' => env('CIRCUIT_BREAKER_ALERT_EMAIL', 'admin@example.com'),
        'slack_webhook' => env('CIRCUIT_BREAKER_SLACK_WEBHOOK'),
    ],
];
