<?php

namespace App\Services\ServiceDiscovery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Consul Service Discovery Client
 */
class ConsulClient
{
    private string $consulHost;
    private int $consulPort;

    public function __construct()
    {
        $this->consulHost = config('services.consul.host', 'localhost');
        $this->consulPort = config('services.consul.port', 8500);
    }

    /**
     * Register a service with Consul
     */
    public function register(string $serviceName, array $config): bool
    {
        $payload = [
            'ID' => $config['id'] ?? "{$serviceName}-" . gethostname(),
            'Name' => $serviceName,
            'Address' => $config['host'] ?? 'localhost',
            'Port' => $config['port'] ?? 8000,
            'Tags' => $config['tags'] ?? [],
            'Meta' => $config['meta'] ?? [],
            'Check' => [
                'HTTP' => "{$config['protocol'] ?? 'http'}://{$config['host']}:{$config['port']}/api/health",
                'Interval' => $config['check_interval'] ?? '10s',
                'Timeout' => $config['check_timeout'] ?? '5s',
                'DeregisterCriticalServiceAfter' => '30s',
            ],
        ];

        try {
            $response = Http::put(
                "http://{$this->consulHost}:{$this->consulPort}/v1/agent/service/register",
                $payload
            );

            if ($response->successful()) {
                Log::info("Service registered with Consul", [
                    'service' => $serviceName,
                    'id' => $payload['ID'],
                ]);
                return true;
            }

            Log::error("Failed to register service with Consul", [
                'service' => $serviceName,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error("Consul registration error", [
                'service' => $serviceName,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Discover a service from Consul
     */
    public function discover(string $serviceName, bool $healthyOnly = true): ?array
    {
        // Check cache first
        $cacheKey = "consul:service:{$serviceName}";
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            return $cached;
        }

        try {
            $url = "http://{$this->consulHost}:{$this->consulPort}/v1/health/service/{$serviceName}";
            
            if ($healthyOnly) {
                $url .= '?passing=true';
            }

            $response = Http::timeout(5)->get($url);

            if (!$response->successful()) {
                Log::warning("Failed to discover service from Consul", [
                    'service' => $serviceName,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $services = $response->json();

            if (empty($services)) {
                Log::warning("No instances found for service", [
                    'service' => $serviceName,
                ]);
                return null;
            }

            // Pick first healthy service
            $service = $services[0];
            $serviceInfo = [
                'id' => $service['Service']['ID'],
                'name' => $service['Service']['Service'],
                'host' => $service['Service']['Address'],
                'port' => $service['Service']['Port'],
                'tags' => $service['Service']['Tags'] ?? [],
                'meta' => $service['Service']['Meta'] ?? [],
            ];

            // Cache for 30 seconds
            Cache::put($cacheKey, $serviceInfo, 30);

            return $serviceInfo;

        } catch (\Exception $e) {
            Log::error("Consul discovery error", [
                'service' => $serviceName,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get all instances of a service
     */
    public function getAll(string $serviceName, bool $healthyOnly = true): array
    {
        try {
            $url = "http://{$this->consulHost}:{$this->consulPort}/v1/health/service/{$serviceName}";
            
            if ($healthyOnly) {
                $url .= '?passing=true';
            }

            $response = Http::timeout(5)->get($url);

            if (!$response->successful()) {
                return [];
            }

            $services = $response->json();

            return array_map(function ($service) {
                return [
                    'id' => $service['Service']['ID'],
                    'name' => $service['Service']['Service'],
                    'host' => $service['Service']['Address'],
                    'port' => $service['Service']['Port'],
                    'tags' => $service['Service']['Tags'] ?? [],
                    'health' => $service['Checks'][0]['Status'] ?? 'unknown',
                ];
            }, $services);

        } catch (\Exception $e) {
            Log::error("Consul getAll error", [
                'service' => $serviceName,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Deregister a service from Consul
     */
    public function deregister(string $serviceId): bool
    {
        try {
            $response = Http::put(
                "http://{$this->consulHost}:{$this->consulPort}/v1/agent/service/deregister/{$serviceId}"
            );

            if ($response->successful()) {
                Log::info("Service deregistered from Consul", [
                    'service_id' => $serviceId,
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Consul deregistration error", [
                'service_id' => $serviceId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get service URL (convenience method)
     */
    public function getServiceUrl(string $serviceName, string $protocol = 'http'): ?string
    {
        $service = $this->discover($serviceName);

        if (!$service) {
            return null;
        }

        return "{$protocol}://{$service['host']}:{$service['port']}";
    }
}
