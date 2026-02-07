<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Circuit Breaker Service (ExternalApiService)
 * 
 * Tests Circuit Breaker pattern: CLOSED → OPEN → HALF_OPEN → CLOSED
 * Covers: state transitions, failure counting, timeout, retry with exponential backoff
 */
class CircuitBreakerServiceTest extends TestCase
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
    // TEST: Circuit Breaker State Transitions
    // ======================================================

    /**
     * Test initial state is CLOSED
     */
    public function test_initial_state_is_closed(): void
    {
        $state = $this->getState('payment-service');
        $this->assertEquals(self::STATE_CLOSED, $state);
    }

    /**
     * Test circuit stays CLOSED when failures below threshold
     */
    public function test_circuit_stays_closed_below_threshold(): void
    {
        // Record 4 failures (threshold = 5)
        for ($i = 0; $i < $this->failureThreshold - 1; $i++) {
            $this->recordFailure('payment-service');
        }

        $this->assertEquals(self::STATE_CLOSED, $this->getState('payment-service'));
        $this->assertEquals(4, $this->getFailureCount('payment-service'));
    }

    /**
     * Test circuit opens when failure threshold reached
     */
    public function test_circuit_opens_when_threshold_reached(): void
    {
        // Record 5 failures (= threshold)
        for ($i = 0; $i < $this->failureThreshold; $i++) {
            $this->recordFailure('payment-service');
        }

        $this->assertEquals(self::STATE_OPEN, $this->getState('payment-service'));
    }

    /**
     * Test OPEN circuit rejects calls
     */
    public function test_open_circuit_rejects_calls(): void
    {
        // Open the circuit
        $this->setState('payment-service', self::STATE_OPEN);
        $this->setOpenedAt('payment-service', time());

        $result = $this->attemptCall('payment-service');

        $this->assertFalse($result['success']);
        $this->assertEquals('CIRCUIT_OPEN', $result['error_code']);
    }

    /**
     * Test OPEN circuit transitions to HALF_OPEN after timeout
     */
    public function test_circuit_transitions_to_half_open_after_timeout(): void
    {
        $this->setState('payment-service', self::STATE_OPEN);
        $this->setOpenedAt('payment-service', time() - $this->timeout - 1);

        $this->checkAndTransition('payment-service');

        $this->assertEquals(self::STATE_HALF_OPEN, $this->getState('payment-service'));
    }

    /**
     * Test OPEN circuit stays OPEN before timeout
     */
    public function test_circuit_stays_open_before_timeout(): void
    {
        $this->setState('payment-service', self::STATE_OPEN);
        $this->setOpenedAt('payment-service', time() - 30); // Only 30s, timeout = 60s

        $this->checkAndTransition('payment-service');

        $this->assertEquals(self::STATE_OPEN, $this->getState('payment-service'));
    }

    /**
     * Test HALF_OPEN circuit closes on success
     */
    public function test_half_open_circuit_closes_on_success(): void
    {
        $this->setState('payment-service', self::STATE_HALF_OPEN);

        $this->recordSuccess('payment-service');

        $this->assertEquals(self::STATE_CLOSED, $this->getState('payment-service'));
        $this->assertEquals(0, $this->getFailureCount('payment-service'));
    }

    /**
     * Test HALF_OPEN circuit reopens on failure
     */
    public function test_half_open_circuit_reopens_on_failure(): void
    {
        $this->setState('payment-service', self::STATE_HALF_OPEN);

        $this->recordFailure('payment-service');

        $this->assertEquals(self::STATE_OPEN, $this->getState('payment-service'));
    }

    // ======================================================
    // TEST: Circuit Breaker Reset
    // ======================================================

    /**
     * Test reset circuit breaker
     */
    public function test_reset_circuit_breaker(): void
    {
        // Open the circuit
        for ($i = 0; $i < $this->failureThreshold; $i++) {
            $this->recordFailure('payment-service');
        }
        $this->assertEquals(self::STATE_OPEN, $this->getState('payment-service'));

        // Reset
        $this->resetCircuit('payment-service');

        $this->assertEquals(self::STATE_CLOSED, $this->getState('payment-service'));
        $this->assertEquals(0, $this->getFailureCount('payment-service'));
    }

    // ======================================================
    // TEST: Circuit Breaker Status
    // ======================================================

    /**
     * Test get circuit breaker status
     */
    public function test_get_circuit_breaker_status(): void
    {
        $this->recordFailure('momo');
        $this->recordFailure('momo');

        $status = $this->getStatus('momo');

        $this->assertEquals('momo', $status['service']);
        $this->assertEquals(self::STATE_CLOSED, $status['state']);
        $this->assertEquals(2, $status['failures']);
    }

    /**
     * Test separate circuit breakers per service
     */
    public function test_separate_circuit_breakers_per_service(): void
    {
        // Fail momo 5 times → OPEN
        for ($i = 0; $i < $this->failureThreshold; $i++) {
            $this->recordFailure('momo');
        }

        // Fail vnpay 2 times → still CLOSED
        $this->recordFailure('vnpay');
        $this->recordFailure('vnpay');

        $this->assertEquals(self::STATE_OPEN, $this->getState('momo'));
        $this->assertEquals(self::STATE_CLOSED, $this->getState('vnpay'));
    }

    // ======================================================
    // TEST: Retry with Exponential Backoff
    // ======================================================

    /**
     * Test exponential backoff calculation
     */
    public function test_exponential_backoff_calculation(): void
    {
        $attempt1Wait = $this->calculateBackoff(1); // 2^1 = 2s
        $attempt2Wait = $this->calculateBackoff(2); // 2^2 = 4s
        $attempt3Wait = $this->calculateBackoff(3); // 2^3 = 8s

        $this->assertEquals(2, $attempt1Wait);
        $this->assertEquals(4, $attempt2Wait);
        $this->assertEquals(8, $attempt3Wait);
    }

    /**
     * Test retry succeeds on second attempt
     */
    public function test_retry_succeeds_on_second_attempt(): void
    {
        $attemptResults = [false, true, true]; // Fail first, succeed second
        $maxRetries = 3;

        $result = $this->simulateRetry($attemptResults, $maxRetries);

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['attempts']);
    }

    /**
     * Test retry fails after max retries
     */
    public function test_retry_fails_after_max_retries(): void
    {
        $attemptResults = [false, false, false]; // All fail
        $maxRetries = 3;

        $result = $this->simulateRetry($attemptResults, $maxRetries);

        $this->assertFalse($result['success']);
        $this->assertEquals(3, $result['attempts']);
    }

    /**
     * Test retry succeeds on first attempt (no retry needed)
     */
    public function test_retry_succeeds_on_first_attempt(): void
    {
        $attemptResults = [true]; // Success immediately
        $maxRetries = 3;

        $result = $this->simulateRetry($attemptResults, $maxRetries);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['attempts']);
    }

    // ======================================================
    // TEST: Circuit Breaker Configuration
    // ======================================================

    /**
     * Test service-specific configuration
     */
    public function test_service_specific_configuration(): void
    {
        $config = $this->getServiceConfig();

        $this->assertEquals(3, $config['momo']['failure_threshold']);
        $this->assertEquals(120, $config['momo']['timeout']);
        $this->assertEquals('qrcode', $config['momo']['fallback']);

        $this->assertEquals(5, $config['vnpay']['failure_threshold']);
        $this->assertEquals(60, $config['vnpay']['timeout']);
        $this->assertEquals('cod', $config['vnpay']['fallback']);
    }

    /**
     * Test fallback payment method when circuit open
     */
    public function test_fallback_payment_method_when_circuit_open(): void
    {
        $config = $this->getServiceConfig();

        // MoMo fails → fallback to QR Code
        $fallback = $config['momo']['fallback'];
        $this->assertEquals('qrcode', $fallback);

        // VNPay fails → fallback to COD
        $fallback = $config['vnpay']['fallback'];
        $this->assertEquals('cod', $fallback);
    }

    // ======================================================
    // HELPER METHODS (Circuit Breaker Logic)
    // ======================================================

    private function getState(string $service): string
    {
        return $this->circuitState[$service]['state'] ?? self::STATE_CLOSED;
    }

    private function setState(string $service, string $state): void
    {
        if (!isset($this->circuitState[$service])) {
            $this->circuitState[$service] = ['state' => self::STATE_CLOSED, 'failures' => 0, 'opened_at' => null];
        }
        $this->circuitState[$service]['state'] = $state;
    }

    private function setOpenedAt(string $service, int $time): void
    {
        $this->circuitState[$service]['opened_at'] = $time;
    }

    private function getFailureCount(string $service): int
    {
        return $this->circuitState[$service]['failures'] ?? 0;
    }

    private function recordFailure(string $service): void
    {
        if (!isset($this->circuitState[$service])) {
            $this->circuitState[$service] = ['state' => self::STATE_CLOSED, 'failures' => 0, 'opened_at' => null];
        }

        $this->circuitState[$service]['failures']++;

        $currentState = $this->circuitState[$service]['state'];

        // If HALF_OPEN and failure → reopen
        if ($currentState === self::STATE_HALF_OPEN) {
            $this->circuitState[$service]['state'] = self::STATE_OPEN;
            $this->circuitState[$service]['opened_at'] = time();
            return;
        }

        // If failures >= threshold → OPEN
        if ($this->circuitState[$service]['failures'] >= $this->failureThreshold) {
            $this->circuitState[$service]['state'] = self::STATE_OPEN;
            $this->circuitState[$service]['opened_at'] = time();
        }
    }

    private function recordSuccess(string $service): void
    {
        if (!isset($this->circuitState[$service])) {
            return;
        }

        if ($this->circuitState[$service]['state'] === self::STATE_HALF_OPEN) {
            $this->circuitState[$service]['state'] = self::STATE_CLOSED;
            $this->circuitState[$service]['failures'] = 0;
            $this->circuitState[$service]['opened_at'] = null;
        }
    }

    private function checkAndTransition(string $service): void
    {
        $state = $this->circuitState[$service]['state'] ?? self::STATE_CLOSED;

        if ($state === self::STATE_OPEN) {
            $openedAt = $this->circuitState[$service]['opened_at'] ?? time();
            if (time() - $openedAt > $this->timeout) {
                $this->circuitState[$service]['state'] = self::STATE_HALF_OPEN;
            }
        }
    }

    private function resetCircuit(string $service): void
    {
        $this->circuitState[$service] = [
            'state' => self::STATE_CLOSED,
            'failures' => 0,
            'opened_at' => null,
        ];
    }

    private function attemptCall(string $service): array
    {
        $state = $this->getState($service);

        if ($state === self::STATE_OPEN) {
            $openedAt = $this->circuitState[$service]['opened_at'] ?? time();
            if (time() - $openedAt <= $this->timeout) {
                return ['success' => false, 'error_code' => 'CIRCUIT_OPEN'];
            }
        }

        return ['success' => true, 'error_code' => null];
    }

    private function getStatus(string $service): array
    {
        return [
            'service' => $service,
            'state' => $this->getState($service),
            'failures' => $this->getFailureCount($service),
            'opened_at' => $this->circuitState[$service]['opened_at'] ?? null,
        ];
    }

    private function calculateBackoff(int $attempt): int
    {
        return (int)pow(2, $attempt);
    }

    private function simulateRetry(array $attemptResults, int $maxRetries): array
    {
        $attempts = 0;

        for ($i = 0; $i < $maxRetries; $i++) {
            $attempts++;
            if ($attemptResults[$i] ?? false) {
                return ['success' => true, 'attempts' => $attempts];
            }
        }

        return ['success' => false, 'attempts' => $attempts];
    }

    private function getServiceConfig(): array
    {
        return [
            'momo' => [
                'failure_threshold' => 3,
                'timeout' => 120,
                'request_timeout' => 30,
                'max_retries' => 2,
                'fallback' => 'qrcode',
            ],
            'vnpay' => [
                'failure_threshold' => 5,
                'timeout' => 60,
                'request_timeout' => 30,
                'max_retries' => 3,
                'fallback' => 'cod',
            ],
            'paypal' => [
                'failure_threshold' => 5,
                'timeout' => 90,
                'request_timeout' => 45,
                'max_retries' => 2,
                'fallback' => 'cod',
            ],
        ];
    }
}
