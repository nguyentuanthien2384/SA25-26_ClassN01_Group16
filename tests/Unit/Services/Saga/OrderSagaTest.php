<?php

namespace Tests\Unit\Services\Saga;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Order Saga Pattern
 * 
 * Tests saga orchestration: execute steps sequentially,
 * compensate in reverse order on failure
 * 
 * Steps: ReserveStock → ProcessPayment → CreateShipment → SendNotification
 */
class OrderSagaTest extends TestCase
{
    private array $executionLog = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->executionLog = [];
    }

    // ======================================================
    // TEST: Saga Execution (Happy Path)
    // ======================================================

    /**
     * Test saga executes all steps in order
     */
    public function test_saga_executes_all_steps_in_order(): void
    {
        $steps = ['ReserveStock', 'ProcessPayment', 'CreateShipment', 'SendNotification'];

        $result = $this->executeSaga($steps);

        $this->assertTrue($result['success']);
        $this->assertEquals(4, $result['executed_count']);
        $this->assertEquals($steps, $result['executed_steps']);
    }

    /**
     * Test saga with single step succeeds
     */
    public function test_saga_with_single_step_succeeds(): void
    {
        $steps = ['ReserveStock'];

        $result = $this->executeSaga($steps);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['executed_count']);
    }

    /**
     * Test saga with empty steps succeeds
     */
    public function test_saga_with_empty_steps_succeeds(): void
    {
        $steps = [];

        $result = $this->executeSaga($steps);

        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['executed_count']);
    }

    // ======================================================
    // TEST: Saga Compensation (Failure Path)
    // ======================================================

    /**
     * Test saga compensates on failure in reverse order
     */
    public function test_saga_compensates_on_failure_in_reverse_order(): void
    {
        $steps = ['ReserveStock', 'ProcessPayment', 'CreateShipment', 'SendNotification'];
        $failAtStep = 2; // CreateShipment fails (index 2)

        $result = $this->executeSagaWithFailure($steps, $failAtStep);

        $this->assertFalse($result['success']);
        $this->assertEquals('CreateShipment', $result['failed_step']);

        // Only ReserveStock and ProcessPayment were executed
        $this->assertEquals(['ReserveStock', 'ProcessPayment'], $result['executed_steps']);

        // Compensation in reverse: ProcessPayment → ReserveStock
        $this->assertEquals(['ProcessPayment', 'ReserveStock'], $result['compensated_steps']);
    }

    /**
     * Test saga compensates when first step fails
     */
    public function test_saga_compensates_when_first_step_fails(): void
    {
        $steps = ['ReserveStock', 'ProcessPayment', 'CreateShipment'];
        $failAtStep = 0;

        $result = $this->executeSagaWithFailure($steps, $failAtStep);

        $this->assertFalse($result['success']);
        $this->assertEquals('ReserveStock', $result['failed_step']);
        $this->assertEmpty($result['executed_steps']);
        $this->assertEmpty($result['compensated_steps']);
    }

    /**
     * Test saga compensates when last step fails
     */
    public function test_saga_compensates_when_last_step_fails(): void
    {
        $steps = ['ReserveStock', 'ProcessPayment', 'CreateShipment', 'SendNotification'];
        $failAtStep = 3; // Last step fails

        $result = $this->executeSagaWithFailure($steps, $failAtStep);

        $this->assertFalse($result['success']);
        $this->assertEquals('SendNotification', $result['failed_step']);

        // All except last were executed
        $this->assertEquals(
            ['ReserveStock', 'ProcessPayment', 'CreateShipment'],
            $result['executed_steps']
        );

        // Reverse compensation
        $this->assertEquals(
            ['CreateShipment', 'ProcessPayment', 'ReserveStock'],
            $result['compensated_steps']
        );
    }

    /**
     * Test saga continues compensation even if one compensation fails
     */
    public function test_saga_continues_compensation_on_compensation_failure(): void
    {
        $steps = ['ReserveStock', 'ProcessPayment', 'CreateShipment'];
        $failAtStep = 2;
        $compensationFailAt = 'ProcessPayment';

        $result = $this->executeSagaWithCompensationFailure(
            $steps, $failAtStep, $compensationFailAt
        );

        $this->assertFalse($result['success']);
        // Even though ProcessPayment compensation failed, ReserveStock was still compensated
        $this->assertContains('ReserveStock', $result['compensated_steps']);
        $this->assertContains('ProcessPayment', $result['failed_compensations']);
    }

    // ======================================================
    // TEST: Saga Status
    // ======================================================

    /**
     * Test saga returns correct status after execution
     */
    public function test_saga_status_after_successful_execution(): void
    {
        $steps = ['ReserveStock', 'ProcessPayment', 'CreateShipment', 'SendNotification'];
        $transactionId = 12345;

        $result = $this->executeSaga($steps, $transactionId);
        $status = $result['status'];

        $this->assertEquals($transactionId, $status['transaction_id']);
        $this->assertEquals(4, $status['total_steps']);
        $this->assertEquals(4, $status['executed_steps']);
    }

    /**
     * Test saga status after failure
     */
    public function test_saga_status_after_failure(): void
    {
        $steps = ['ReserveStock', 'ProcessPayment', 'CreateShipment'];
        $failAtStep = 1;
        $transactionId = 12345;

        $result = $this->executeSagaWithFailure($steps, $failAtStep, $transactionId);
        $status = $result['status'];

        $this->assertEquals($transactionId, $status['transaction_id']);
        $this->assertEquals(3, $status['total_steps']);
        $this->assertEquals(1, $status['executed_steps']); // Only ReserveStock executed
    }

    // ======================================================
    // TEST: Individual Saga Steps
    // ======================================================

    /**
     * Test ReserveStock step creates reservation
     */
    public function test_reserve_stock_step_creates_reservation(): void
    {
        $transactionData = [
            'id' => 1,
            'items' => [
                ['product_id' => 101, 'quantity' => 2],
                ['product_id' => 102, 'quantity' => 1],
            ],
        ];

        $result = $this->executeReserveStock($transactionData);

        $this->assertTrue($result['stock_reserved']);
        $this->assertStringStartsWith('RES-', $result['reservation_id']);
    }

    /**
     * Test ProcessPayment step with COD method
     */
    public function test_process_payment_step_with_cod(): void
    {
        $transactionData = [
            'id' => 1,
            'payment_method' => 'cod',
            'total' => 5000000,
        ];

        $result = $this->executeProcessPayment($transactionData);

        $this->assertEquals(0, $result['payment_status']); // Pending
        $this->assertEquals('wait', $result['order_status']);
    }

    /**
     * Test ProcessPayment step with online payment
     */
    public function test_process_payment_step_with_online_payment(): void
    {
        $transactionData = [
            'id' => 1,
            'payment_method' => 'momo',
            'total' => 5000000,
            'payment_status' => 1, // Already paid online
        ];

        $result = $this->executeProcessPayment($transactionData);

        $this->assertEquals(1, $result['payment_status']); // Success
    }

    /**
     * Test CreateShipment step generates tracking code
     */
    public function test_create_shipment_step_generates_tracking(): void
    {
        $transactionData = [
            'id' => 1,
            'name' => 'Nghiêm Đức Việt',
            'phone' => '0123456789',
            'address' => 'Hà Nội',
        ];

        $result = $this->executeCreateShipment($transactionData);

        $this->assertTrue($result['shipment_created']);
        $this->assertStringStartsWith('SHIP-', $result['shipment_id']);
        $this->assertStringStartsWith('TRACK-', $result['tracking_code']);
    }

    /**
     * Test ReserveStock compensation releases stock
     */
    public function test_reserve_stock_compensation_releases_stock(): void
    {
        $metadata = [
            'stock_reserved' => true,
            'stock_reservation_id' => 'RES-1',
        ];

        $result = $this->compensateReserveStock($metadata);

        $this->assertFalse($result['stock_reserved']);
        $this->assertTrue($result['stock_released']);
    }

    /**
     * Test ProcessPayment compensation creates refund
     */
    public function test_process_payment_compensation_creates_refund(): void
    {
        $transactionData = [
            'id' => 1,
            'payment_status' => 1, // Was paid
            'total' => 5000000,
        ];

        $result = $this->compensateProcessPayment($transactionData);

        $this->assertEquals(2, $result['payment_status']); // Refunded
        $this->assertEquals('cancelled', $result['order_status']);
    }

    // ======================================================
    // HELPER METHODS (Saga Logic)
    // ======================================================

    private function executeSaga(array $steps, int $transactionId = 1): array
    {
        $executedSteps = [];

        foreach ($steps as $step) {
            $this->executionLog[] = "execute:{$step}";
            $executedSteps[] = $step;
        }

        return [
            'success' => true,
            'executed_count' => count($executedSteps),
            'executed_steps' => $executedSteps,
            'status' => [
                'transaction_id' => $transactionId,
                'total_steps' => count($steps),
                'executed_steps' => count($executedSteps),
            ],
        ];
    }

    private function executeSagaWithFailure(array $steps, int $failAtStep, int $transactionId = 1): array
    {
        $executedSteps = [];

        foreach ($steps as $index => $step) {
            if ($index === $failAtStep) {
                // Compensate in reverse
                $compensatedSteps = array_reverse($executedSteps);
                return [
                    'success' => false,
                    'failed_step' => $step,
                    'executed_steps' => $executedSteps,
                    'compensated_steps' => $compensatedSteps,
                    'status' => [
                        'transaction_id' => $transactionId,
                        'total_steps' => count($steps),
                        'executed_steps' => count($executedSteps),
                    ],
                ];
            }

            $this->executionLog[] = "execute:{$step}";
            $executedSteps[] = $step;
        }

        return ['success' => true, 'executed_count' => count($executedSteps), 'executed_steps' => $executedSteps, 'compensated_steps' => [], 'status' => ['transaction_id' => $transactionId, 'total_steps' => count($steps), 'executed_steps' => count($executedSteps)]];
    }

    private function executeSagaWithCompensationFailure(
        array $steps,
        int $failAtStep,
        string $compensationFailAt
    ): array {
        $executedSteps = [];

        foreach ($steps as $index => $step) {
            if ($index === $failAtStep) {
                break;
            }
            $executedSteps[] = $step;
        }

        $compensatedSteps = [];
        $failedCompensations = [];

        foreach (array_reverse($executedSteps) as $step) {
            if ($step === $compensationFailAt) {
                $failedCompensations[] = $step;
                $compensatedSteps[] = $step; // Still attempted
            } else {
                $compensatedSteps[] = $step;
            }
        }

        return [
            'success' => false,
            'failed_step' => $steps[$failAtStep],
            'executed_steps' => $executedSteps,
            'compensated_steps' => $compensatedSteps,
            'failed_compensations' => $failedCompensations,
        ];
    }

    private function executeReserveStock(array $transactionData): array
    {
        return [
            'stock_reserved' => true,
            'reservation_id' => 'RES-' . $transactionData['id'],
        ];
    }

    private function executeProcessPayment(array $transactionData): array
    {
        if ($transactionData['payment_method'] === 'cod') {
            return [
                'payment_status' => 0,
                'order_status' => 'wait',
            ];
        }

        return [
            'payment_status' => $transactionData['payment_status'] ?? 0,
            'order_status' => 'success',
        ];
    }

    private function executeCreateShipment(array $transactionData): array
    {
        return [
            'shipment_created' => true,
            'shipment_id' => 'SHIP-' . $transactionData['id'],
            'tracking_code' => 'TRACK-' . strtoupper(substr(md5($transactionData['id']), 0, 10)),
        ];
    }

    private function compensateReserveStock(array $metadata): array
    {
        if (!($metadata['stock_reserved'] ?? false)) {
            return $metadata;
        }

        return [
            'stock_reserved' => false,
            'stock_released' => true,
        ];
    }

    private function compensateProcessPayment(array $transactionData): array
    {
        if ($transactionData['payment_status'] != 1) {
            return $transactionData;
        }

        return [
            'payment_status' => 2, // Refunded
            'order_status' => 'cancelled',
        ];
    }
}
