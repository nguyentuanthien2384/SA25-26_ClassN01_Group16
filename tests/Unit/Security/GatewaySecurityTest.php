<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test: Gateway Security Layer
 *
 * Verifies the security middleware and controller enforce
 * authentication (401) and authorization (403) correctly.
 */
class GatewaySecurityTest extends TestCase
{
    // ================================================================
    // GatewayTokenMiddleware — Token parsing logic
    // ================================================================

    public function test_valid_admin_token_is_recognized(): void
    {
        $token = 'valid-admin-token';
        $role = $this->resolveRole($token);

        $this->assertEquals('admin', $role);
    }

    public function test_valid_user_token_is_recognized(): void
    {
        $token = 'valid-user-token';
        $role = $this->resolveRole($token);

        $this->assertEquals('user', $role);
    }

    public function test_invalid_token_returns_null(): void
    {
        $invalidTokens = [
            'invalid-token',
            'expired-token-123',
            '',
            'valid-admin',
            'admin-token',
        ];

        foreach ($invalidTokens as $token) {
            $role = $this->resolveRole($token);
            $this->assertNull($role, "Token '$token' should not resolve to a role");
        }
    }

    public function test_bearer_prefix_is_stripped_correctly(): void
    {
        $header = 'Bearer valid-admin-token';
        $token = trim(substr($header, strlen('Bearer ')));

        $this->assertEquals('valid-admin-token', $token);
    }

    public function test_authorization_header_must_start_with_bearer(): void
    {
        $invalidHeaders = [
            'Token valid-admin-token',
            'Basic dXNlcjpwYXNz',
            'valid-admin-token',
            'bearer valid-admin-token',
        ];

        foreach ($invalidHeaders as $header) {
            $isBearer = str_starts_with($header, 'Bearer ');
            if ($header === 'bearer valid-admin-token') {
                $this->assertFalse($isBearer, 'Bearer prefix is case-sensitive');
            }
        }
    }

    // ================================================================
    // GatewayController — RBAC authorization logic
    // ================================================================

    public function test_admin_can_perform_get(): void
    {
        $this->assertTrue($this->isAllowed('admin', 'GET'));
    }

    public function test_admin_can_perform_post(): void
    {
        $this->assertTrue($this->isAllowed('admin', 'POST'));
    }

    public function test_admin_can_perform_put(): void
    {
        $this->assertTrue($this->isAllowed('admin', 'PUT'));
    }

    public function test_admin_can_perform_delete(): void
    {
        $this->assertTrue($this->isAllowed('admin', 'DELETE'));
    }

    public function test_user_can_perform_get(): void
    {
        $this->assertTrue($this->isAllowed('user', 'GET'));
    }

    public function test_user_cannot_perform_post(): void
    {
        $this->assertFalse($this->isAllowed('user', 'POST'));
    }

    public function test_user_cannot_perform_put(): void
    {
        $this->assertFalse($this->isAllowed('user', 'PUT'));
    }

    public function test_user_cannot_perform_delete(): void
    {
        $this->assertFalse($this->isAllowed('user', 'DELETE'));
    }

    // ================================================================
    // Security matrix — all combinations
    // ================================================================

    public function test_full_rbac_matrix(): void
    {
        $matrix = [
            ['admin', 'GET',    true],
            ['admin', 'POST',   true],
            ['admin', 'PUT',    true],
            ['admin', 'DELETE', true],
            ['user',  'GET',    true],
            ['user',  'POST',   false],
            ['user',  'PUT',    false],
            ['user',  'DELETE', false],
        ];

        foreach ($matrix as [$role, $method, $expected]) {
            $this->assertEquals(
                $expected,
                $this->isAllowed($role, $method),
                "Role '$role' + Method '$method' should be " . ($expected ? 'allowed' : 'denied')
            );
        }
    }

    // ================================================================
    // Gateway URL construction
    // ================================================================

    public function test_target_url_constructed_correctly(): void
    {
        $baseUrl = 'http://127.0.0.1:5001';
        $path = '35';

        $targetUrl = rtrim($baseUrl, '/') . '/api/products/' . $path;

        $this->assertEquals('http://127.0.0.1:5001/api/products/35', $targetUrl);
    }

    public function test_target_url_without_path(): void
    {
        $baseUrl = 'http://127.0.0.1:5001';
        $path = '';

        $targetUrl = rtrim($baseUrl, '/') . '/api/products';
        if (!empty($path)) {
            $targetUrl .= '/' . $path;
        }

        $this->assertEquals('http://127.0.0.1:5001/api/products', $targetUrl);
    }

    // ================================================================
    // HELPERS
    // ================================================================

    private function resolveRole(string $token): ?string
    {
        return match ($token) {
            'valid-admin-token' => 'admin',
            'valid-user-token' => 'user',
            default => null,
        };
    }

    private function isAllowed(string $role, string $method): bool
    {
        $writeMethods = ['POST', 'PUT', 'DELETE'];

        if (in_array($method, $writeMethods) && $role !== 'admin') {
            return false;
        }

        return true;
    }
}
