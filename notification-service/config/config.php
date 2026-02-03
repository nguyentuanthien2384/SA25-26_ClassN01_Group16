<?php

return [
    'redis' => [
        'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
        'port' => (int)($_ENV['REDIS_PORT'] ?? 6379),
        'password' => $_ENV['REDIS_PASSWORD'] ?? null,
        'queue' => $_ENV['REDIS_QUEUE'] ?? 'notifications',
    ],
    
    'smtp' => [
        'host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
        'port' => (int)($_ENV['SMTP_PORT'] ?? 587),
        'username' => $_ENV['SMTP_USERNAME'] ?? '',
        'password' => $_ENV['SMTP_PASSWORD'] ?? '',
        'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
        'from' => [
            'email' => $_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@example.com',
            'name' => $_ENV['SMTP_FROM_NAME'] ?? 'Notification Service',
        ],
    ],
    
    'service' => [
        'name' => $_ENV['SERVICE_NAME'] ?? 'notification-service',
        'log_level' => $_ENV['LOG_LEVEL'] ?? 'info',
        'poll_interval' => (int)($_ENV['POLL_INTERVAL'] ?? 5),
    ],
];
