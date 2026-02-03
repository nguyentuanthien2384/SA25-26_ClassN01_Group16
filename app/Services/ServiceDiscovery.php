<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service Discovery using Consul
 * 
 * Provides service registration and discovery functionality
 */
class ServiceDiscovery
{
    private Client $client;
    private string $consulHost;
    private int $consulPort;
    private string $serviceName;
    private string $serviceHost;
    private int $servicePort;

    public function __construct()
    {
        $this->consulHost = config('services.consul.host', 'localhost');
        $this->consulPort = config('services.consul.port', 8500);
        $this->serviceName = config('app.name', 'laravel-app');
        $this->serviceHost = config('services.consul.service_host', 'localhost');
        $this->servicePort = config('services.consul.service_port', 8000);

        $this->client = new Client([
            'base_uri' => "http://{$this->consulHost}:{$this->consulPort}",
            'timeout' => 5,
        ]);
    }

    /**
     * Register service with Consul
     */
    public function register(): bool
    {
        try {
            $serviceId = $this->getServiceId();

            $response = $this->client->put('/v1/agent/service/register', [
                'json' => [
                    'ID' => $serviceId,
                    'Name' => $this->serviceName,
                    'Tags' => ['laravel', 'microservice', 'web'],
                    'Address' => $this->serviceHost,
                    'Port' => $this->servicePort,
                    'Check' => [
                        'HTTP' => "http://{$this->serviceHost}:{$this->servicePort}/api/health",
                        'Interval' => '10s',
                        'Timeout' => '5s',
                        'DeregisterCriticalServiceAfter' => '30s',
                    ],
                    'Meta' => [
                        'version' => config('app.version', '1.0.0'),
                        'environment' => config('app.env', 'production'),
                    ],
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                Log::info("Service registered with Consul", [
                    'service_id' => $serviceId,
                    'service_name' => $this->serviceName,
                    'address' => "{$this->serviceHost}:{$this->servicePort}",
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Failed to register service with Consul", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Deregister service from Consul
     */
    public function deregister(): bool
    {
        try {
            $serviceId = $this->getServiceId();

            $response = $this->client->put("/v1/agent/service/deregister/{$serviceId}");

            if ($response->getStatusCode() === 200) {
                Log::info("Service deregistered from Consul", [
                    'service_id' => $serviceId,
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Failed to deregister service from Consul", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Discover a service by name
     * 
     * Returns array of service instances with their addresses
     */
    public function discover(string $serviceName): array
    {
        $cacheKey = "consul:service:{$serviceName}";

        // Try cache first (30 seconds TTL)
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = $this->client->get("/v1/health/service/{$serviceName}", [
                'query' => ['passing' => 'true'], // Only healthy instances
            ]);

            $services = json_decode($response->getBody(), true);
            $instances = [];

            foreach ($services as $service) {
                $instances[] = [
                    'id' => $service['Service']['ID'],
                    'address' => $service['Service']['Address'],
                    'port' => $service['Service']['Port'],
                    'tags' => $service['Service']['Tags'] ?? [],
                    'meta' => $service['Service']['Meta'] ?? [],
                ];
            }

            // Cache for 30 seconds
            Cache::put($cacheKey, $instances, 30);

            Log::debug("Service discovered", [
                'service_name' => $serviceName,
                'instances_count' => count($instances),
            ]);

            return $instances;

        } catch (\Exception $e) {
            Log::error("Failed to discover service", [
                'service_name' => $serviceName,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get service URL (load balanced)
     */
    public function getServiceUrl(string $serviceName): ?string
    {
        $instances = $this->discover($serviceName);

        if (empty($instances)) {
            return null;
        }

        // Simple round-robin load balancing
        $instance = $instances[array_rand($instances)];
        
        return "http://{$instance['address']}:{$instance['port']}";
    }

    /**
     * Get all registered services
     */
    public function getAllServices(): array
    {
        try {
            $response = $this->client->get('/v1/catalog/services');
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error("Failed to get all services", [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Health check
     */
    public function healthCheck(): bool
    {
        try {
            $response = $this->client->get('/v1/status/leader');
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get unique service ID
     */
    private function getServiceId(): string
    {
        return $this->serviceName . '-' . gethostname() . '-' . $this->servicePort;
    }
}
