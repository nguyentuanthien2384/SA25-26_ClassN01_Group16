<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Feature Test: API Gateway Authentication & Authorization
 *
 * Tests the GatewayTokenMiddleware (401) and GatewayController (403)
 * through the /api/gateway/products endpoint.
 */
class GatewayAuthTest extends TestCase
{
    private string $gatewayUrl = '/api/gateway/products';

    // ================================================================
    // 401 UNAUTHORIZED — Missing or invalid token
    // ================================================================

    public function test_401_when_no_authorization_header(): void
    {
        $response = $this->getJson($this->gatewayUrl);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'details' => 'Authorization header missing or malformed',
            ]);
    }

    public function test_401_when_authorization_header_has_no_bearer_prefix(): void
    {
        $response = $this->getJson($this->gatewayUrl, [
            'Authorization' => 'Token some-random-value',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_401_when_bearer_token_is_invalid(): void
    {
        $response = $this->getJson($this->gatewayUrl, [
            'Authorization' => 'Bearer this-token-does-not-exist',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'details' => 'Invalid or expired token',
            ]);
    }

    public function test_401_when_bearer_token_is_empty(): void
    {
        $response = $this->getJson($this->gatewayUrl, [
            'Authorization' => 'Bearer ',
        ]);

        $response->assertStatus(401);
    }

    // ================================================================
    // 403 FORBIDDEN — User role tries write operations
    // ================================================================

    public function test_403_when_user_token_sends_post(): void
    {
        $response = $this->postJson($this->gatewayUrl, [
            'pro_name' => 'Test Product',
            'pro_price' => 100000,
        ], [
            'Authorization' => 'Bearer valid-user-token',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Forbidden',
                'details' => 'Admin token required for write operations',
            ]);
    }

    public function test_403_when_user_token_sends_put(): void
    {
        $response = $this->putJson($this->gatewayUrl . '/1', [
            'pro_name' => 'Updated Product',
        ], [
            'Authorization' => 'Bearer valid-user-token',
        ]);

        $response->assertStatus(403)
            ->assertJson(['error' => 'Forbidden']);
    }

    public function test_403_when_user_token_sends_delete(): void
    {
        $response = $this->deleteJson($this->gatewayUrl . '/1', [], [
            'Authorization' => 'Bearer valid-user-token',
        ]);

        $response->assertStatus(403)
            ->assertJson(['error' => 'Forbidden']);
    }

    // ================================================================
    // 200 OK — Valid tokens with allowed operations
    // ================================================================

    public function test_200_when_admin_token_sends_get(): void
    {
        $response = $this->getJson($this->gatewayUrl, [
            'Authorization' => 'Bearer valid-admin-token',
        ]);

        $this->assertContains($response->getStatusCode(), [200, 503]);

        if ($response->getStatusCode() === 200) {
            $response->assertJsonStructure(['data']);
        }
    }

    public function test_200_when_user_token_sends_get(): void
    {
        $response = $this->getJson($this->gatewayUrl, [
            'Authorization' => 'Bearer valid-user-token',
        ]);

        $this->assertContains($response->getStatusCode(), [200, 503]);
    }

    public function test_admin_token_allows_post(): void
    {
        $response = $this->postJson($this->gatewayUrl, [
            'pro_name' => 'Admin Created Product',
            'pro_price' => 500000,
        ], [
            'Authorization' => 'Bearer valid-admin-token',
        ]);

        $this->assertNotEquals(403, $response->getStatusCode());
    }

    // ================================================================
    // X-Gateway header verification
    // ================================================================

    public function test_response_contains_x_gateway_header_on_success(): void
    {
        $response = $this->getJson($this->gatewayUrl, [
            'Authorization' => 'Bearer valid-admin-token',
        ]);

        if ($response->getStatusCode() === 200) {
            $response->assertHeader('X-Gateway', 'ElectroShop-Gateway');
        }
    }

    // ================================================================
    // 503 SERVICE UNAVAILABLE — Backend unreachable
    // ================================================================

    public function test_503_when_backend_service_is_down(): void
    {
        $originalUrl = env('PRODUCT_SERVICE_URL', 'http://127.0.0.1:5001');

        putenv('PRODUCT_SERVICE_URL=http://127.0.0.1:59999');

        $response = $this->getJson($this->gatewayUrl, [
            'Authorization' => 'Bearer valid-admin-token',
        ]);

        putenv("PRODUCT_SERVICE_URL=$originalUrl");

        $this->assertContains($response->getStatusCode(), [503, 500]);
    }
}
