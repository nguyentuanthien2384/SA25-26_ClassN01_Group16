<?php

namespace NotificationService;

use Predis\Client;
use Psr\Log\LoggerInterface;

class RedisConsumer
{
    private Client $redis;
    private LoggerInterface $logger;
    private array $config;
    private bool $running = true;

    public function __construct(Client $redis, LoggerInterface $logger, array $config)
    {
        $this->redis = $redis;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Start consuming messages from Redis queue
     */
    public function consume(callable $handler): void
    {
        $queue = $this->config['redis']['queue'];
        $pollInterval = $this->config['service']['poll_interval'];

        $this->logger->info("Starting Redis consumer for queue: {$queue}");

        // Handle SIGINT and SIGTERM for graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'shutdown']);
            pcntl_signal(SIGINT, [$this, 'shutdown']);
        }

        while ($this->running) {
            try {
                // Blocking pop with timeout (BRPOP)
                $message = $this->redis->brpop([$queue], $pollInterval);

                if ($message) {
                    [$queueName, $payload] = $message;
                    
                    $this->logger->info("Received message from queue", [
                        'queue' => $queueName,
                        'payload_length' => strlen($payload),
                    ]);

                    // Decode Laravel queue job format
                    $job = json_decode($payload, true);
                    
                    if ($job && isset($job['data'])) {
                        $handler($job['data']);
                    } else {
                        $this->logger->warning("Invalid job format", ['payload' => $payload]);
                    }
                }

                // Check for signals
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }

            } catch (\Exception $e) {
                $this->logger->error("Error consuming message", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                // Wait before retry
                sleep(5);
            }
        }

        $this->logger->info("Consumer stopped gracefully");
    }

    /**
     * Graceful shutdown
     */
    public function shutdown(): void
    {
        $this->logger->info("Received shutdown signal, stopping consumer...");
        $this->running = false;
    }
}
