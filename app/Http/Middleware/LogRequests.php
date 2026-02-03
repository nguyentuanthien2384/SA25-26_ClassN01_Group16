<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Request Logging Middleware for ELK Stack
 * 
 * Logs all HTTP requests with structured data for Elasticsearch
 */
class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate unique request ID for tracing
        $requestId = Str::uuid()->toString();
        $request->attributes->set('request_id', $requestId);

        // Start time
        $startTime = microtime(true);

        // Process request
        $response = $next($request);

        // End time
        $duration = round((microtime(true) - $startTime) * 1000, 2); // milliseconds

        // Log structured data
        Log::info('HTTP Request', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'timestamp' => now()->toIso8601String(),
        ]);

        // Add request ID to response header
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
