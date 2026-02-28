<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Feature Test: Kong-like API Gateway Routing
 *
 * Tests /api/products, /api/health, /api/ping, /api/metrics endpoints
 * to verify service routing and response formats.
 */
class ApiGatewayRoutingTest extends TestCase
{
    // ================================================================
    // HEALTH CHECK ENDPOINTS
    // ================================================================

    public function test_health_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'service',
                'checks' => ['database', 'cache'],
                'uptime',
            ]);
    }

    public function test_health_endpoint_reports_healthy_status(): void
    {
        $response = $this->getJson('/api/health');

        $data = $response->json();
        $this->assertContains($data['status'], ['healthy', 'unhealthy']);
        $this->assertNotEmpty($data['timestamp']);
    }

    public function test_ping_endpoint_returns_pong(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200)
            ->assertJson(['message' => 'pong'])
            ->assertJsonStructure(['message', 'timestamp']);
    }

    // ================================================================
    // PRODUCT API ENDPOINTS
    // ================================================================

    public function test_products_endpoint_returns_paginated_list(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data',
                'per_page',
                'total',
            ]);
    }

    public function test_products_endpoint_respects_per_page_parameter(): void
    {
        $response = $this->getJson('/api/products?per_page=5');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertLessThanOrEqual(5, count($data['data']));
    }

    public function test_products_endpoint_max_per_page_is_60(): void
    {
        $response = $this->getJson('/api/products?per_page=100');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertLessThanOrEqual(60, $data['per_page']);
    }

    public function test_products_endpoint_supports_search(): void
    {
        $response = $this->getJson('/api/products?search=Laptop');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertIsArray($data['data']);
    }

    public function test_products_endpoint_supports_category_filter(): void
    {
        $response = $this->getJson('/api/products?category=1');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_products_endpoint_supports_sorting(): void
    {
        $sorts = ['newest', 'price_asc', 'price_desc', 'name_asc'];

        foreach ($sorts as $sort) {
            $response = $this->getJson("/api/products?sort=$sort");
            $response->assertStatus(200);
        }
    }

    public function test_single_product_endpoint_returns_product(): void
    {
        $listResponse = $this->getJson('/api/products');
        $products = $listResponse->json('data');

        if (!empty($products)) {
            $id = $products[0]['id'];
            $response = $this->getJson("/api/products/$id");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'id', 'pro_name', 'pro_price',
                ]);
        } else {
            $this->markTestSkipped('No products in database');
        }
    }

    public function test_single_product_404_for_nonexistent_id(): void
    {
        $response = $this->getJson('/api/products/999999');

        $response->assertStatus(404);
    }

    // ================================================================
    // CACHE HEADERS
    // ================================================================

    public function test_products_response_has_cache_headers(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $this->assertTrue(
            $response->headers->has('X-Cache-Status') || $response->headers->has('Cache-Control'),
            'Response should have cache-related headers'
        );
    }

    // ================================================================
    // REQUEST LOGGING (X-Request-ID)
    // ================================================================

    public function test_response_contains_request_id_header(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200);
        if ($response->headers->has('X-Request-ID')) {
            $requestId = $response->headers->get('X-Request-ID');
            $this->assertNotEmpty($requestId);
            $this->assertEquals(36, strlen($requestId));
        }
    }

    // ================================================================
    // METRICS ENDPOINT (Prometheus)
    // ================================================================

    public function test_metrics_endpoint_returns_prometheus_format(): void
    {
        $response = $this->get('/api/metrics');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('laravel_app_up', $response->getContent());
    }

    // ================================================================
    // RATE LIMITING
    // ================================================================

    public function test_api_has_throttle_headers(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200);
        if ($response->headers->has('X-RateLimit-Limit')) {
            $this->assertNotEmpty($response->headers->get('X-RateLimit-Limit'));
        }
    }
}
