<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExternalApiService;
use Illuminate\Http\Request;

/**
 * Admin Controller for Circuit Breaker Monitoring
 */
class CircuitBreakerController extends Controller
{
    protected ExternalApiService $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Show all circuit breakers status
     */
    public function index()
    {
        $services = config('circuit_breaker.services', []);
        $statuses = [];

        foreach (array_keys($services) as $service) {
            $statuses[$service] = $this->apiService->getStatus($service);
        }

        return response()->json([
            'timestamp' => now()->toIso8601String(),
            'services' => $statuses,
        ]);
    }

    /**
     * Show specific service status
     */
    public function show(string $service)
    {
        $status = $this->apiService->getStatus($service);
        $config = config("circuit_breaker.services.{$service}", []);

        return response()->json([
            'service' => $service,
            'status' => $status,
            'config' => $config,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Reset circuit breaker for a service
     */
    public function reset(Request $request, string $service)
    {
        $this->apiService->reset($service);

        return response()->json([
            'message' => "Circuit breaker for '{$service}' has been reset",
            'status' => $this->apiService->getStatus($service),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
