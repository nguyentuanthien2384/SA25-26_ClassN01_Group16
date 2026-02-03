<?php

/**
 * Notification Service - Redis Queue Consumer
 * 
 * Consumes messages from Redis queue and sends email notifications
 */

use NotificationService\RedisConsumer;
use NotificationService\EmailSender;
use Predis\Client;

// Bootstrap application
$app = require __DIR__ . '/bootstrap.php';
$config = $app['config'];
$logger = $app['logger'];

$logger->info("=== Notification Service Starting ===");
$logger->info("Redis: {$config['redis']['host']}:{$config['redis']['port']}");
$logger->info("Queue: {$config['redis']['queue']}");

try {
    // Connect to Redis
    $redis = new Client([
        'scheme' => 'tcp',
        'host' => $config['redis']['host'],
        'port' => $config['redis']['port'],
        'password' => $config['redis']['password'],
    ]);

    // Test connection
    $redis->ping();
    $logger->info("✓ Connected to Redis successfully");

    // Initialize email sender
    $emailSender = new EmailSender($logger, $config);
    $logger->info("✓ Email sender initialized");

    // Initialize consumer
    $consumer = new RedisConsumer($redis, $logger, $config);
    $logger->info("✓ Consumer initialized");

    // Start consuming messages
    $logger->info("🚀 Starting to consume messages...");
    
    $consumer->consume(function ($eventData) use ($emailSender, $logger) {
        try {
            $logger->info("Processing event", [
                'event_type' => $eventData['event_type'] ?? 'unknown',
                'aggregate_id' => $eventData['aggregate_id'] ?? null,
            ]);

            $emailSender->handleEvent($eventData);

            $logger->info("✓ Event processed successfully");

        } catch (\Exception $e) {
            $logger->error("✗ Failed to process event", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    });

} catch (\Exception $e) {
    $logger->error("Fatal error", [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    exit(1);
}
