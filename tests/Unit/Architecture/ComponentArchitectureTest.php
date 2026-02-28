<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\TestCase;

/**
 * Verifies that every architectural component required by the report
 * actually exists as a file in the codebase, and that key classes
 * follow the expected structural contracts.
 */
class ComponentArchitectureTest extends TestCase
{
    private string $base;

    protected function setUp(): void
    {
        parent::setUp();
        $this->base = realpath(__DIR__ . '/../../..') ?: '';
    }

    // ================================================================
    // PRESENTATION LAYER — Controllers exist
    // ================================================================

    public function test_gateway_controller_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Http/Controllers/Gateway/GatewayController.php');
    }

    public function test_product_api_controller_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Http/Controllers/Api/ProductApiController.php');
    }

    public function test_lab03_product_controller_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Lab03/Controllers/ProductController.php');
    }

    // ================================================================
    // MIDDLEWARE LAYER — Security & Cross-cutting
    // ================================================================

    public function test_gateway_token_middleware_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Http/Middleware/GatewayTokenMiddleware.php');
    }

    public function test_circuit_breaker_middleware_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Http/Middleware/CircuitBreaker.php');
    }

    public function test_log_requests_middleware_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Http/Middleware/LogRequests.php');
    }

    // ================================================================
    // BUSINESS LOGIC LAYER — Services
    // ================================================================

    public function test_product_service_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Lab03/Services/ProductService.php');
    }

    public function test_cqrs_command_service_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Services/CQRS/ProductCommandService.php');
    }

    public function test_cqrs_query_service_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Services/CQRS/ProductQueryService.php');
    }

    public function test_saga_orchestrator_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Services/Saga/OrderSaga.php');
    }

    public function test_saga_step_interface_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Services/Saga/SagaStepInterface.php');
    }

    public function test_all_saga_steps_exist(): void
    {
        $steps = [
            'ReserveStockStep.php',
            'ProcessPaymentStep.php',
            'CreateShipmentStep.php',
            'SendNotificationStep.php',
        ];
        foreach ($steps as $step) {
            $this->assertFileExists(
                $this->base . '/app/Services/Saga/Steps/' . $step,
                "Saga step missing: $step"
            );
        }
    }

    public function test_external_api_service_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Services/ExternalApiService.php');
    }

    public function test_service_discovery_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Services/ServiceDiscovery.php');
    }

    // ================================================================
    // PERSISTENCE LAYER — Repository pattern (Lab03)
    // ================================================================

    public function test_product_repository_interface_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Lab03/Repositories/ProductRepositoryInterface.php');
    }

    public function test_product_repository_implementation_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Lab03/Repositories/ProductRepository.php');
    }

    public function test_repository_interface_defines_required_methods(): void
    {
        $content = file_get_contents($this->base . '/app/Lab03/Repositories/ProductRepositoryInterface.php');

        $requiredMethods = ['getAllPaginated', 'findById', 'create', 'update', 'delete', 'searchByName'];
        foreach ($requiredMethods as $method) {
            $this->assertStringContainsString(
                $method,
                $content,
                "Interface missing method: $method"
            );
        }
    }

    public function test_lab03_service_provider_binds_interface(): void
    {
        $content = file_get_contents($this->base . '/app/Lab03/Providers/Lab03ServiceProvider.php');

        $this->assertStringContainsString('ProductRepositoryInterface', $content);
        $this->assertStringContainsString('ProductRepository', $content);
        $this->assertStringContainsString('bind', $content);
    }

    // ================================================================
    // DATA LAYER — Models
    // ================================================================

    public function test_all_required_models_exist(): void
    {
        $models = ['Product', 'Category', 'User', 'Transaction', 'Order', 'Cart', 'OutboxMessage'];
        foreach ($models as $model) {
            $this->assertFileExists(
                $this->base . '/app/Models/Models/' . $model . '.php',
                "Model missing: $model"
            );
        }
    }

    // ================================================================
    // EVENT-DRIVEN ARCHITECTURE — Events + Listeners + Jobs
    // ================================================================

    public function test_order_placed_event_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Events/OrderPlaced.php');
    }

    public function test_product_events_exist(): void
    {
        $events = ['ProductCreated.php', 'ProductUpdated.php', 'ProductDeleted.php'];
        foreach ($events as $event) {
            $this->assertFileExists(
                $this->base . '/app/Events/' . $event,
                "Event missing: $event"
            );
        }
    }

    public function test_outbox_listener_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Listeners/SaveOrderPlacedToOutbox.php');
    }

    public function test_publish_outbox_job_exists(): void
    {
        $this->assertFileExists($this->base . '/app/Jobs/PublishOutboxMessages.php');
    }

    // ================================================================
    // INFRASTRUCTURE — Docker + Notification Service
    // ================================================================

    public function test_dockerfile_exists(): void
    {
        $this->assertFileExists($this->base . '/Dockerfile');
    }

    public function test_docker_compose_monolith_exists(): void
    {
        $this->assertFileExists($this->base . '/docker-compose.yml');
    }

    public function test_docker_compose_microservices_exists(): void
    {
        $this->assertFileExists($this->base . '/docker-compose.microservices.yml');
    }

    public function test_notification_service_exists(): void
    {
        $this->assertFileExists($this->base . '/notification-service/consumer.php');
        $this->assertFileExists($this->base . '/notification-service/bootstrap.php');
        $this->assertFileExists($this->base . '/notification-service/composer.json');
    }

    // ================================================================
    // ROUTES — All route files present
    // ================================================================

    public function test_api_routes_exist(): void
    {
        $this->assertFileExists($this->base . '/routes/api.php');
    }

    public function test_gateway_routes_exist(): void
    {
        $this->assertFileExists($this->base . '/routes/gateway.php');
    }

    public function test_gateway_route_uses_middleware(): void
    {
        $content = file_get_contents($this->base . '/routes/gateway.php');
        $this->assertStringContainsString('gateway.token', $content);
        $this->assertStringContainsString('GatewayController', $content);
    }

    // ================================================================
    // MODULES — All 8 domain modules present
    // ================================================================

    public function test_all_eight_modules_exist(): void
    {
        $modules = ['Admin', 'Cart', 'Catalog', 'Content', 'Customer', 'Payment', 'Review', 'Support'];
        foreach ($modules as $module) {
            $this->assertDirectoryExists(
                $this->base . '/Modules/' . $module,
                "Module missing: $module"
            );
        }
    }

    public function test_each_module_has_routes(): void
    {
        $modules = ['Admin', 'Cart', 'Catalog', 'Content', 'Customer', 'Payment', 'Review', 'Support'];
        foreach ($modules as $module) {
            $routeFile = $this->base . '/Modules/' . $module . '/routes/web.php';
            $this->assertFileExists($routeFile, "Module $module missing routes/web.php");
        }
    }

    // ================================================================
    // DESIGN DOCUMENTS
    // ================================================================

    public function test_c4_diagrams_exist(): void
    {
        $diagrams = [
            'c4-level1-context.puml',
            'c4-level2-container.puml',
            'c4-level3-catalog-component.puml',
        ];
        foreach ($diagrams as $d) {
            $this->assertFileExists(
                $this->base . '/Design/' . $d,
                "C4 diagram missing: $d"
            );
        }
    }

    public function test_deployment_diagram_exists(): void
    {
        $this->assertFileExists($this->base . '/Design/deployment-diagram.puml');
    }

    public function test_sequence_diagrams_exist(): void
    {
        $diagrams = [
            'sequence-checkout-flow.puml',
            'sequence-payment-flow.puml',
            'sequence-message-broker-flow.puml',
        ];
        foreach ($diagrams as $d) {
            $this->assertFileExists(
                $this->base . '/Design/' . $d,
                "Sequence diagram missing: $d"
            );
        }
    }
}
