<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Circuit Breaker Middleware
 * 
 * Implements Circuit Breaker pattern to prevent cascading failures
 * States: CLOSED (normal) → OPEN (failing) → HALF_OPEN (testing)
 */
class CircuitBreaker
{
    private const STATE_CLOSED = 'closed';
    private const STATE_OPEN = 'open';
    private const STATE_HALF_OPEN = 'half_open';

    private int $failureThreshold = 5;
    private int $timeout = 60; // seconds
    private int $halfOpenTimeout = 30; // seconds

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $service = 'default')
    {
        $circuitKey = "circuit_breaker:{$service}";
        $state = Cache::get("{$circuitKey}:state", self::STATE_CLOSED);

        // If circuit is OPEN, return fallback response
        if ($state === self::STATE_OPEN) {
            $openedAt = Cache::get("{$circuitKey}:opened_at");
            
            // Check if timeout has passed to move to HALF_OPEN
            if (time() - $openedAt > $this->timeout) {
                Cache::put("{$circuitKey}:state", self::STATE_HALF_OPEN, $this->halfOpenTimeout);
                Log::info("Circuit breaker for {$service} moved to HALF_OPEN");
            } else {
                Log::warning("Circuit breaker OPEN for {$service}, returning fallback");
                return $this->fallbackResponse($service);
            }
        }

        try {
            $response = $next($request);

            // Success - Reset failures if HALF_OPEN, or keep CLOSED
            if ($state === self::STATE_HALF_OPEN) {
                Cache::put("{$circuitKey}:state", self::STATE_CLOSED);
                Cache::forget("{$circuitKey}:failures");
                Log::info("Circuit breaker for {$service} moved to CLOSED");
            }

            return $response;

        } catch (\Exception $e) {
            // Failure - Increment failure count
            $failures = Cache::increment("{$circuitKey}:failures", 1);
            Cache::put("{$circuitKey}:failures", $failures, 3600);

            Log::error("Circuit breaker failure for {$service}", [
                'failures' => $failures,
                'threshold' => $this->failureThreshold,
                'error' => $e->getMessage(),
            ]);

            // If threshold reached, open circuit
            if ($failures >= $this->failureThreshold) {
                Cache::put("{$circuitKey}:state", self::STATE_OPEN);
                Cache::put("{$circuitKey}:opened_at", time(), $this->timeout);
                Log::critical("Circuit breaker for {$service} moved to OPEN");
            }

            throw $e;
        }
    }

    /**
     * Fallback response when circuit is open
     */
    private function fallbackResponse(string $service)
    {
        return response()->json([
            'error' => 'Service Unavailable',
            'message' => "The {$service} service is temporarily unavailable. Please try again later.",
            'code' => 'CIRCUIT_OPEN',
            'timestamp' => now()->toIso8601String(),
        ], 503);
    }
}
