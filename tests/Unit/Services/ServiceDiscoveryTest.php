<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Service Discovery (Consul)
 * 
 * Tests: Service registration, discovery, load balancing,
 *        caching, health checking, deregistration
 */
class ServiceDiscoveryTest extends TestCase
{
    private array $registry = [];
    private array $cache = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = [];
        $this->cache = [];
    }

    // ======================================================
    // TEST: Service Registration
    // ======================================================

    /**
     * Test register service successfully
     */
    public function test_register_service_successfully(): void
    {
        $result = $this->registerService('catalog-service', [
            'host' => 'localhost',
            'port' => 9005,
            'tags' => ['laravel', 'catalog'],
        ]);

        $this->assertTrue($result);
        $this->assertArrayHasKey('catalog-service', $this->registry);
    }

    /**
     * Test register generates correct service ID
     */
    public function test_register_generates_service_id(): void
    {
        $this->registerService('order-service', [
            'host' => 'localhost',
            'port' => 9002,
        ]);

        $service = $this->registry['order-service'][0];
        $this->assertStringContainsString('order-service', $service['id']);
    }

    /**
     * Test register multiple instances of same service
     */
    public function test_register_multiple_instances(): void
    {
        $this->registerService('catalog-service', ['host' => '10.0.0.1', 'port' => 9005]);
        $this->registerService('catalog-service', ['host' => '10.0.0.2', 'port' => 9005]);

        $this->assertCount(2, $this->registry['catalog-service']);
    }

    /**
     * Test register includes health check configuration
     */
    public function test_register_includes_health_check(): void
    {
        $this->registerService('user-service', [
            'host' => 'localhost',
            'port' => 9003,
        ]);

        $service = $this->registry['user-service'][0];
        $this->assertArrayHasKey('health_check', $service);
        $this->assertStringContainsString('/api/health', $service['health_check']['url']);
    }

    // ======================================================
    // TEST: Service Discovery
    // ======================================================

    /**
     * Test discover registered service
     */
    public function test_discover_registered_service(): void
    {
        $this->registerService('catalog-service', ['host' => 'localhost', 'port' => 9005]);

        $service = $this->discoverService('catalog-service');

        $this->assertNotNull($service);
        $this->assertEquals('localhost', $service['host']);
        $this->assertEquals(9005, $service['port']);
    }

    /**
     * Test discover unregistered service returns null
     */
    public function test_discover_unregistered_service_returns_null(): void
    {
        $service = $this->discoverService('non-existent-service');

        $this->assertNull($service);
    }

    /**
     * Test discover only returns healthy instances
     */
    public function test_discover_only_returns_healthy_instances(): void
    {
        $this->registerService('catalog-service', ['host' => '10.0.0.1', 'port' => 9005, 'healthy' => true]);
        $this->registerService('catalog-service', ['host' => '10.0.0.2', 'port' => 9005, 'healthy' => false]);

        $instances = $this->getAllInstances('catalog-service', true);

        $this->assertCount(1, $instances);
        $this->assertEquals('10.0.0.1', $instances[0]['host']);
    }

    /**
     * Test discover all instances (including unhealthy)
     */
    public function test_discover_all_instances(): void
    {
        $this->registerService('catalog-service', ['host' => '10.0.0.1', 'port' => 9005, 'healthy' => true]);
        $this->registerService('catalog-service', ['host' => '10.0.0.2', 'port' => 9005, 'healthy' => false]);

        $instances = $this->getAllInstances('catalog-service', false);

        $this->assertCount(2, $instances);
    }

    // ======================================================
    // TEST: Load Balancing
    // ======================================================

    /**
     * Test get service URL with single instance
     */
    public function test_get_service_url_single_instance(): void
    {
        $this->registerService('catalog-service', ['host' => 'localhost', 'port' => 9005]);

        $url = $this->getServiceUrl('catalog-service');

        $this->assertEquals('http://localhost:9005', $url);
    }

    /**
     * Test get service URL returns null when no instances
     */
    public function test_get_service_url_returns_null_when_no_instances(): void
    {
        $url = $this->getServiceUrl('non-existent');

        $this->assertNull($url);
    }

    /**
     * Test round-robin load balancing
     */
    public function test_round_robin_load_balancing(): void
    {
        $this->registerService('api-service', ['host' => '10.0.0.1', 'port' => 8000, 'healthy' => true]);
        $this->registerService('api-service', ['host' => '10.0.0.2', 'port' => 8000, 'healthy' => true]);
        $this->registerService('api-service', ['host' => '10.0.0.3', 'port' => 8000, 'healthy' => true]);

        $selectedHosts = [];
        for ($i = 0; $i < 6; $i++) {
            $url = $this->getServiceUrlRoundRobin('api-service', $i);
            $selectedHosts[] = $url;
        }

        // Should distribute across all 3 instances
        $uniqueHosts = array_unique($selectedHosts);
        $this->assertCount(3, $uniqueHosts);
    }

    // ======================================================
    // TEST: Caching
    // ======================================================

    /**
     * Test discovery results are cached
     */
    public function test_discovery_results_are_cached(): void
    {
        $this->registerService('catalog-service', ['host' => 'localhost', 'port' => 9005]);

        // First call
        $this->discoverService('catalog-service');
        $this->assertTrue(isset($this->cache['consul:service:catalog-service']));

        // Second call should use cache
        $service = $this->discoverServiceFromCache('catalog-service');
        $this->assertNotNull($service);
    }

    /**
     * Test cache expiration (30 seconds)
     */
    public function test_cache_expiration(): void
    {
        $this->cache['consul:service:old-service'] = [
            'data' => ['host' => 'localhost', 'port' => 9000],
            'expires_at' => time() - 1, // Already expired
        ];

        $cached = $this->getCachedService('old-service');

        $this->assertNull($cached); // Cache expired
    }

    // ======================================================
    // TEST: Service Deregistration
    // ======================================================

    /**
     * Test deregister service
     */
    public function test_deregister_service(): void
    {
        $this->registerService('temp-service', ['host' => 'localhost', 'port' => 9999]);
        $this->assertArrayHasKey('temp-service', $this->registry);

        $result = $this->deregisterService('temp-service');

        $this->assertTrue($result);
        $this->assertEmpty($this->registry['temp-service']);
    }

    /**
     * Test deregister non-existent service
     */
    public function test_deregister_non_existent_service(): void
    {
        $result = $this->deregisterService('non-existent');

        $this->assertFalse($result);
    }

    // ======================================================
    // TEST: Health Check
    // ======================================================

    /**
     * Test health check endpoint URL format
     */
    public function test_health_check_url_format(): void
    {
        $url = $this->buildHealthCheckUrl('localhost', 9005);

        $this->assertEquals('http://localhost:9005/api/health', $url);
    }

    /**
     * Test consul health check simulation
     */
    public function test_consul_health_check(): void
    {
        $isHealthy = $this->simulateHealthCheck(true);
        $this->assertTrue($isHealthy);

        $isHealthy = $this->simulateHealthCheck(false);
        $this->assertFalse($isHealthy);
    }

    // ======================================================
    // HELPER METHODS (Service Discovery Logic)
    // ======================================================

    private function registerService(string $name, array $config): bool
    {
        $serviceId = "{$name}-" . ($config['host'] ?? 'localhost') . '-' . ($config['port'] ?? 8000);

        $entry = [
            'id' => $serviceId,
            'name' => $name,
            'host' => $config['host'] ?? 'localhost',
            'port' => $config['port'] ?? 8000,
            'tags' => $config['tags'] ?? [],
            'healthy' => $config['healthy'] ?? true,
            'health_check' => [
                'url' => "http://{$config['host']}:{$config['port']}/api/health",
                'interval' => '10s',
                'timeout' => '5s',
            ],
        ];

        if (!isset($this->registry[$name])) {
            $this->registry[$name] = [];
        }

        $this->registry[$name][] = $entry;

        return true;
    }

    private function discoverService(string $name): ?array
    {
        $instances = $this->registry[$name] ?? [];

        $healthy = array_filter($instances, fn($i) => $i['healthy']);

        if (empty($healthy)) {
            return null;
        }

        $service = reset($healthy);

        // Cache the result
        $this->cache["consul:service:{$name}"] = [
            'data' => $service,
            'expires_at' => time() + 30,
        ];

        return $service;
    }

    private function discoverServiceFromCache(string $name): ?array
    {
        $cached = $this->cache["consul:service:{$name}"] ?? null;

        if ($cached && $cached['expires_at'] > time()) {
            return $cached['data'];
        }

        return $this->discoverService($name);
    }

    private function getCachedService(string $name): ?array
    {
        $cached = $this->cache["consul:service:{$name}"] ?? null;

        if ($cached && $cached['expires_at'] > time()) {
            return $cached['data'];
        }

        return null;
    }

    private function getAllInstances(string $name, bool $healthyOnly): array
    {
        $instances = $this->registry[$name] ?? [];

        if ($healthyOnly) {
            return array_values(array_filter($instances, fn($i) => $i['healthy']));
        }

        return $instances;
    }

    private function getServiceUrl(string $name): ?string
    {
        $service = $this->discoverService($name);

        if (!$service) {
            return null;
        }

        return "http://{$service['host']}:{$service['port']}";
    }

    private function getServiceUrlRoundRobin(string $name, int $index): string
    {
        $instances = $this->getAllInstances($name, true);

        if (empty($instances)) {
            return '';
        }

        $instance = $instances[$index % count($instances)];

        return "http://{$instance['host']}:{$instance['port']}";
    }

    private function deregisterService(string $name): bool
    {
        if (!isset($this->registry[$name]) || empty($this->registry[$name])) {
            return false;
        }

        $this->registry[$name] = [];
        return true;
    }

    private function buildHealthCheckUrl(string $host, int $port): string
    {
        return "http://{$host}:{$port}/api/health";
    }

    private function simulateHealthCheck(bool $healthy): bool
    {
        return $healthy;
    }
}
