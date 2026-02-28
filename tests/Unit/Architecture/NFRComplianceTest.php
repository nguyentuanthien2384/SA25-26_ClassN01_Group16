<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test: Non-Functional Requirements (NFR) Compliance
 *
 * Verifies that code-level implementations satisfy each NFR
 * defined in the architecture document.
 */
class NFRComplianceTest extends TestCase
{
    private string $base;

    protected function setUp(): void
    {
        parent::setUp();
        $this->base = realpath(__DIR__ . '/../../..') ?: '';
    }

    // ================================================================
    // NFR-1: PERFORMANCE
    // ================================================================

    public function test_product_api_uses_cache_remember(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Controllers/Api/ProductApiController.php');

        $this->assertStringContainsString('Cache::remember', $content);
    }

    public function test_product_api_uses_select_for_query_optimization(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Controllers/Api/ProductApiController.php');

        $this->assertStringContainsString("::select(", $content);
    }

    public function test_product_api_uses_eager_loading(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Controllers/Api/ProductApiController.php');

        $this->assertStringContainsString("->with(", $content);
    }

    public function test_product_api_uses_pagination(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Controllers/Api/ProductApiController.php');

        $this->assertStringContainsString('paginate', $content);
    }

    public function test_api_routes_use_cache_headers(): void
    {
        $content = file_get_contents($this->base . '/routes/api.php');

        $this->assertStringContainsString('X-Cache-Status', $content);
        $this->assertStringContainsString('Cache-Control', $content);
    }

    public function test_per_page_has_maximum_limit(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Controllers/Api/ProductApiController.php');

        $this->assertStringContainsString('min(max(', $content);
    }

    // ================================================================
    // NFR-2: SCALABILITY
    // ================================================================

    public function test_microservices_docker_compose_has_separate_databases(): void
    {
        $content = file_get_contents($this->base . '/docker-compose.microservices.yml');

        $this->assertStringContainsString('catalog_db', $content);
        $this->assertStringContainsString('order_db', $content);
        $this->assertStringContainsString('user_db', $content);
    }

    public function test_microservices_docker_compose_has_separate_mysql_containers(): void
    {
        $content = file_get_contents($this->base . '/docker-compose.microservices.yml');

        $this->assertStringContainsString('mysql-catalog', $content);
        $this->assertStringContainsString('mysql-order', $content);
        $this->assertStringContainsString('mysql-user', $content);
    }

    public function test_services_use_environment_variables_for_config(): void
    {
        $content = file_get_contents($this->base . '/docker-compose.microservices.yml');

        $this->assertStringContainsString('SERVICE_NAME=catalog-service', $content);
        $this->assertStringContainsString('SERVICE_NAME=order-service', $content);
        $this->assertStringContainsString('SERVICE_NAME=user-service', $content);
    }

    public function test_eight_domain_modules_exist(): void
    {
        $modules = ['Admin', 'Cart', 'Catalog', 'Content', 'Customer', 'Payment', 'Review', 'Support'];

        foreach ($modules as $module) {
            $this->assertDirectoryExists($this->base . '/Modules/' . $module);
        }
    }

    // ================================================================
    // NFR-3: AVAILABILITY
    // ================================================================

    public function test_circuit_breaker_has_three_states(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Middleware/CircuitBreaker.php');

        $this->assertStringContainsString('closed', $content);
        $this->assertStringContainsString('open', $content);
        $this->assertStringContainsString('half_open', $content);
    }

    public function test_circuit_breaker_has_failure_threshold(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Middleware/CircuitBreaker.php');

        $this->assertStringContainsString('failureThreshold', $content);
    }

    public function test_external_api_service_has_retry_logic(): void
    {
        $content = file_get_contents($this->base . '/app/Services/ExternalApiService.php');

        $this->assertStringContainsString('retry', strtolower($content));
    }

    public function test_health_check_endpoint_exists_in_routes(): void
    {
        $content = file_get_contents($this->base . '/routes/api.php');

        $this->assertStringContainsString("'/health'", $content);
    }

    public function test_health_check_verifies_database_connection(): void
    {
        $content = file_get_contents($this->base . '/routes/api.php');

        $this->assertStringContainsString('DB::connection()->getPdo()', $content);
    }

    public function test_health_check_verifies_redis_connection(): void
    {
        $content = file_get_contents($this->base . '/routes/api.php');

        $this->assertStringContainsString("Cache::get('health_check')", $content);
    }

    public function test_cqrs_query_service_has_fallback(): void
    {
        $content = file_get_contents($this->base . '/app/Services/CQRS/ProductQueryService.php');

        $this->assertStringContainsString('fallbackSearch', $content);
    }

    // ================================================================
    // NFR-4: SECURITY
    // ================================================================

    public function test_gateway_middleware_checks_bearer_token(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Middleware/GatewayTokenMiddleware.php');

        $this->assertStringContainsString('Bearer', $content);
        $this->assertStringContainsString('401', $content);
    }

    public function test_gateway_controller_enforces_admin_for_writes(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Controllers/Gateway/GatewayController.php');

        $this->assertStringContainsString("'POST', 'PUT', 'DELETE'", $content);
        $this->assertStringContainsString('403', $content);
        $this->assertStringContainsString("role !== 'admin'", $content);
    }

    public function test_csrf_middleware_is_registered(): void
    {
        $this->assertFileExists($this->base . '/app/Http/Middleware/VerifyCsrfToken.php');
    }

    public function test_kernel_registers_gateway_middleware(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Kernel.php');

        $this->assertStringContainsString('gateway.token', $content);
        $this->assertStringContainsString('GatewayTokenMiddleware', $content);
    }

    // ================================================================
    // NFR-5: MONITORING & OBSERVABILITY
    // ================================================================

    public function test_log_requests_middleware_generates_uuid(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Middleware/LogRequests.php');

        $this->assertStringContainsString('uuid', strtolower($content));
        $this->assertStringContainsString('X-Request-ID', $content);
    }

    public function test_log_requests_middleware_tracks_duration(): void
    {
        $content = file_get_contents($this->base . '/app/Http/Middleware/LogRequests.php');

        $this->assertStringContainsString('microtime', $content);
    }

    public function test_metrics_endpoint_exists(): void
    {
        $content = file_get_contents($this->base . '/routes/api.php');

        $this->assertStringContainsString("'/metrics'", $content);
        $this->assertStringContainsString('laravel_app_up', $content);
    }

    public function test_prometheus_configured_in_docker(): void
    {
        $content = file_get_contents($this->base . '/docker-compose.microservices.yml');

        $this->assertStringContainsString('prometheus', $content);
        $this->assertStringContainsString('9090', $content);
    }

    public function test_grafana_configured_in_docker(): void
    {
        $content = file_get_contents($this->base . '/docker-compose.microservices.yml');

        $this->assertStringContainsString('grafana', $content);
        $this->assertStringContainsString('3000', $content);
    }

    public function test_jaeger_tracing_configured_in_docker(): void
    {
        $content = file_get_contents($this->base . '/docker-compose.microservices.yml');

        $this->assertStringContainsString('jaeger', $content);
        $this->assertStringContainsString('16686', $content);
    }
}
