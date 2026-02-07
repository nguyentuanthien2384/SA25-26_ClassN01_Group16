<?php

namespace Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Circuit Breaker Middleware
 * 
 * Tests: Request handling in different circuit states,
 *        Fallback responses, State transitions via middleware
 */
class CircuitBreakerMiddlewareTest extends TestCase
{
    private const STATE_CLOSED = 'closed';
    private const STATE_OPEN = 'open';
    private const STATE_HALF_OPEN = 'half_open';

    private int $failureThreshold = 5;
    private int $timeout = 60;
    private array $circuitState = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->circuitState = [];
    }

    // ======================================================
    // TEST: Request Handling
    // ======================================================

    /**
     * Test request passes through when circuit CLOSED
     */
    public function test_request_passes_when_circuit_closed(): void
    {
        $result = $this->handleRequest('payment-service', true);

        $this->assertTrue($result['passed']);
        $this->assertEquals(200, $result['status_code']);
    }

    /**
     * Test request blocked when circuit OPEN
     */
    public function test_request_blocked_when_circuit_open(): void
    {
        $this->setState('payment-service', self::STATE_OPEN, time());

        $result = $this->handleRequest('payment-service', true);

        $this->assertFalse($result['passed']);
        $this->assertEquals(503, $result['status_code']);
    }

    /**
     * Test fallback response format
     */
    public function test_fallback_response_format(): void
    {
        $this->setState('payment-service', self::STATE_OPEN, time());

        $result = $this->handleRequest('payment-service', true);
        $body = $result['body'];

        $this->assertEquals('Service Unavailable', $body['error']);
        $this->assertEquals('CIRCUIT_OPEN', $body['code']);
        $this->assertStringContainsString('payment-service', $body['message']);
        $this->assertArrayHasKey('timestamp', $body);
    }

    /**
     * Test request allowed in HALF_OPEN (one test request)
     */
    public function test_request_allowed_in_half_open(): void
    {
        $this->setState('payment-service', self::STATE_HALF_OPEN);

        $result = $this->handleRequest('payment-service', true);

        $this->assertTrue($result['passed']);
    }

    // ======================================================
    // TEST: Failure Tracking via Middleware
    // ======================================================

    /**
     * Test failure incremented on request error
     */
    public function test_failure_incremented_on_request_error(): void
    {
        $this->handleRequest('payment-service', false); // Simulate failed request

        $this->assertEquals(1, $this->getFailures('payment-service'));
    }

    /**
     * Test circuit opens after threshold failures
     */
    public function test_circuit_opens_after_threshold_failures(): void
    {
        for ($i = 0; $i < $this->failureThreshold; $i++) {
            $this->handleRequest('payment-service', false);
        }

        $this->assertEquals(self::STATE_OPEN, $this->getState('payment-service'));
    }

    /**
     * Test successful request resets failures in HALF_OPEN
     */
    public function test_success_resets_failures_in_half_open(): void
    {
        $this->setState('payment-service', self::STATE_HALF_OPEN);
        $this->circuitState['payment-service']['failures'] = 3;

        $this->handleRequest('payment-service', true);

        $this->assertEquals(self::STATE_CLOSED, $this->getState('payment-service'));
        $this->assertEquals(0, $this->getFailures('payment-service'));
    }

    /**
     * Test failed request in HALF_OPEN reopens circuit
     */
    public function test_failure_in_half_open_reopens_circuit(): void
    {
        $this->setState('payment-service', self::STATE_HALF_OPEN);

        $this->handleRequest('payment-service', false);

        $this->assertEquals(self::STATE_OPEN, $this->getState('payment-service'));
    }

    // ======================================================
    // TEST: Timeout Transition
    // ======================================================

    /**
     * Test OPEN circuit allows request after timeout
     */
    public function test_open_circuit_allows_request_after_timeout(): void
    {
        $this->setState('payment-service', self::STATE_OPEN, time() - $this->timeout - 1);

        $result = $this->handleRequest('payment-service', true);

        $this->assertTrue($result['passed']);
        $this->assertEquals(self::STATE_CLOSED, $this->getState('payment-service'));
    }

    /**
     * Test OPEN circuit blocks request before timeout
     */
    public function test_open_circuit_blocks_request_before_timeout(): void
    {
        $this->setState('payment-service', self::STATE_OPEN, time() - 30);

        $result = $this->handleRequest('payment-service', true);

        $this->assertFalse($result['passed']);
        $this->assertEquals(503, $result['status_code']);
    }

    // ======================================================
    // TEST: Multiple Services
    // ======================================================

    /**
     * Test different services have independent circuits
     */
    public function test_independent_circuits_per_service(): void
    {
        // Open momo circuit
        for ($i = 0; $i < $this->failureThreshold; $i++) {
            $this->handleRequest('momo', false);
        }

        // VNPay should still be CLOSED
        $momoResult = $this->handleRequest('momo', true);
        $vnpayResult = $this->handleRequest('vnpay', true);

        $this->assertFalse($momoResult['passed']);
        $this->assertTrue($vnpayResult['passed']);
    }

    /**
     * Test default service name when not specified
     */
    public function test_default_service_name(): void
    {
        $result = $this->handleRequest('default', true);

        $this->assertTrue($result['passed']);
        $this->assertEquals(self::STATE_CLOSED, $this->getState('default'));
    }

    // ======================================================
    // HELPER METHODS (Middleware Logic)
    // ======================================================

    private function handleRequest(string $service, bool $requestSucceeds): array
    {
        $state = $this->getState($service);

        // Check OPEN state
        if ($state === self::STATE_OPEN) {
            $openedAt = $this->circuitState[$service]['opened_at'] ?? time();

            if (time() - $openedAt > $this->timeout) {
                $this->setState($service, self::STATE_HALF_OPEN);
                // Allow test request
            } else {
                return [
                    'passed' => false,
                    'status_code' => 503,
                    'body' => $this->fallbackResponse($service),
                ];
            }
        }

        if ($requestSucceeds) {
            // Success
            $currentState = $this->getState($service);
            if ($currentState === self::STATE_HALF_OPEN) {
                $this->initState($service);
                $this->circuitState[$service]['state'] = self::STATE_CLOSED;
                $this->circuitState[$service]['failures'] = 0;
            }

            return ['passed' => true, 'status_code' => 200, 'body' => null];
        }

        // Failure
        $this->initState($service);
        $this->circuitState[$service]['failures']++;

        $currentState = $this->getState($service);

        if ($currentState === self::STATE_HALF_OPEN) {
            $this->circuitState[$service]['state'] = self::STATE_OPEN;
            $this->circuitState[$service]['opened_at'] = time();
        } elseif ($this->circuitState[$service]['failures'] >= $this->failureThreshold) {
            $this->circuitState[$service]['state'] = self::STATE_OPEN;
            $this->circuitState[$service]['opened_at'] = time();
        }

        return ['passed' => true, 'status_code' => 500, 'body' => null];
    }

    private function fallbackResponse(string $service): array
    {
        return [
            'error' => 'Service Unavailable',
            'message' => "The {$service} service is temporarily unavailable. Please try again later.",
            'code' => 'CIRCUIT_OPEN',
            'timestamp' => date('c'),
        ];
    }

    private function initState(string $service): void
    {
        if (!isset($this->circuitState[$service])) {
            $this->circuitState[$service] = [
                'state' => self::STATE_CLOSED,
                'failures' => 0,
                'opened_at' => null,
            ];
        }
    }

    private function getState(string $service): string
    {
        return $this->circuitState[$service]['state'] ?? self::STATE_CLOSED;
    }

    private function setState(string $service, string $state, ?int $openedAt = null): void
    {
        $this->circuitState[$service] = [
            'state' => $state,
            'failures' => $this->circuitState[$service]['failures'] ?? 0,
            'opened_at' => $openedAt,
        ];
    }

    private function getFailures(string $service): int
    {
        return $this->circuitState[$service]['failures'] ?? 0;
    }
}
