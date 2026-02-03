<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * External API Service với Circuit Breaker
 * 
 * Wrapper cho external API calls (MoMo, PayPal, VNPay)
 * Implement Circuit Breaker pattern
 */
class ExternalApiService
{
    private const STATE_CLOSED = 'closed';
    private const STATE_OPEN = 'open';
    private const STATE_HALF_OPEN = 'half_open';

    private int $failureThreshold = 5;
    private int $timeout = 60;
    private int $requestTimeout = 30;

    /**
     * Call external API với circuit breaker protection
     */
    public function call(string $serviceName, string $url, array $options = [])
    {
        $circuitKey = "circuit_breaker:{$serviceName}";
        $state = Cache::get("{$circuitKey}:state", self::STATE_CLOSED);

        // Check circuit state
        if ($state === self::STATE_OPEN) {
            $openedAt = Cache::get("{$circuitKey}:opened_at");
            
            if (time() - $openedAt > $this->timeout) {
                Cache::put("{$circuitKey}:state", self::STATE_HALF_OPEN);
                Log::info("Circuit breaker {$serviceName}: OPEN → HALF_OPEN");
            } else {
                Log::warning("Circuit breaker OPEN for {$serviceName}");
                throw new \Exception("Circuit breaker is OPEN for {$serviceName}");
            }
        }

        try {
            // Make request with timeout
            $response = Http::timeout($this->requestTimeout)
                ->withOptions($options)
                ->post($url, $options['data'] ?? []);

            // Success
            if ($state === self::STATE_HALF_OPEN) {
                Cache::put("{$circuitKey}:state", self::STATE_CLOSED);
                Cache::forget("{$circuitKey}:failures");
                Log::info("Circuit breaker {$serviceName}: HALF_OPEN → CLOSED");
            }

            return $response;

        } catch (\Exception $e) {
            // Record failure
            $failures = Cache::increment("{$circuitKey}:failures", 1);
            
            Log::error("External API call failed", [
                'service' => $serviceName,
                'failures' => $failures,
                'error' => $e->getMessage(),
            ]);

            // Open circuit if threshold reached
            if ($failures >= $this->failureThreshold) {
                Cache::put("{$circuitKey}:state", self::STATE_OPEN);
                Cache::put("{$circuitKey}:opened_at", time());
                Log::critical("Circuit breaker {$serviceName}: CLOSED → OPEN");
            }

            throw $e;
        }
    }

    /**
     * Call với retry và exponential backoff
     */
    public function callWithRetry(string $serviceName, string $url, array $options = [], int $maxRetries = 3)
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                return $this->call($serviceName, $url, $options);
            } catch (\Exception $e) {
                $attempt++;
                $lastException = $e;

                if ($attempt < $maxRetries) {
                    $waitSeconds = pow(2, $attempt); // Exponential backoff: 2, 4, 8 seconds
                    Log::warning("Retry {$attempt}/{$maxRetries} for {$serviceName}, waiting {$waitSeconds}s");
                    sleep($waitSeconds);
                }
            }
        }

        Log::error("Max retries reached for {$serviceName}");
        throw $lastException;
    }

    /**
     * Reset circuit breaker (admin tool)
     */
    public function reset(string $serviceName): void
    {
        $circuitKey = "circuit_breaker:{$serviceName}";
        Cache::forget("{$circuitKey}:state");
        Cache::forget("{$circuitKey}:failures");
        Cache::forget("{$circuitKey}:opened_at");
        
        Log::info("Circuit breaker reset for {$serviceName}");
    }

    /**
     * Get circuit breaker status
     */
    public function getStatus(string $serviceName): array
    {
        $circuitKey = "circuit_breaker:{$serviceName}";
        
        return [
            'service' => $serviceName,
            'state' => Cache::get("{$circuitKey}:state", self::STATE_CLOSED),
            'failures' => Cache::get("{$circuitKey}:failures", 0),
            'opened_at' => Cache::get("{$circuitKey}:opened_at"),
        ];
    }
}
