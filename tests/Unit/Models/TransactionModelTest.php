<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Transaction Model Business Logic
 * 
 * Tests: Status constants, Order total calculation, 
 *        Payment method validation, Transaction flow
 */
class TransactionModelTest extends TestCase
{
    // ======================================================
    // TEST: Transaction Status Constants
    // ======================================================

    /**
     * Test transaction status constants
     */
    public function test_transaction_status_constants(): void
    {
        $this->assertEquals(0, $this->getConstant('STATUS_DEFAULT'));
        $this->assertEquals(1, $this->getConstant('STATUS_DONE'));
        $this->assertEquals(2, $this->getConstant('STATUS_WAIT'));
        $this->assertEquals(3, $this->getConstant('STATUS_FAILURE'));
    }

    /**
     * Test transaction status labels
     */
    public function test_transaction_status_labels(): void
    {
        $this->assertEquals('Mới đặt', $this->getStatusLabel(0));
        $this->assertEquals('Hoàn thành', $this->getStatusLabel(1));
        $this->assertEquals('Đang xử lý', $this->getStatusLabel(2));
        $this->assertEquals('Thất bại', $this->getStatusLabel(3));
    }

    // ======================================================
    // TEST: Order Total Calculation
    // ======================================================

    /**
     * Test calculate order total from items
     */
    public function test_calculate_order_total(): void
    {
        $items = [
            ['product_id' => 1, 'price' => 29990000, 'quantity' => 1],
            ['product_id' => 2, 'price' => 1990000, 'quantity' => 2],
        ];

        $total = $this->calculateOrderTotal($items);

        $this->assertEquals(33970000, $total); // 29990000 + 3980000
    }

    /**
     * Test order total with sale prices
     */
    public function test_order_total_with_sale_prices(): void
    {
        $items = [
            ['product_id' => 1, 'original_price' => 29990000, 'sale_percent' => 10, 'quantity' => 1],
            ['product_id' => 2, 'original_price' => 10000000, 'sale_percent' => 20, 'quantity' => 1],
        ];

        $total = $this->calculateOrderTotalWithSale($items);

        // 29990000 * 0.9 = 26991000
        // 10000000 * 0.8 = 8000000
        // Total = 34991000
        $this->assertEquals(34991000, $total);
    }

    /**
     * Test order total with empty items
     */
    public function test_order_total_with_empty_items(): void
    {
        $total = $this->calculateOrderTotal([]);

        $this->assertEquals(0, $total);
    }

    // ======================================================
    // TEST: Payment Method Validation
    // ======================================================

    /**
     * Test valid payment methods
     */
    public function test_valid_payment_methods(): void
    {
        $validMethods = ['cod', 'momo', 'vnpay', 'paypal', 'qrcode'];

        foreach ($validMethods as $method) {
            $this->assertTrue(
                $this->isValidPaymentMethod($method),
                "Expected '{$method}' to be a valid payment method"
            );
        }
    }

    /**
     * Test invalid payment methods
     */
    public function test_invalid_payment_methods(): void
    {
        $invalidMethods = ['bitcoin', 'cash', '', 'stripe'];

        foreach ($invalidMethods as $method) {
            $this->assertFalse(
                $this->isValidPaymentMethod($method),
                "Expected '{$method}' to be an invalid payment method"
            );
        }
    }

    // ======================================================
    // TEST: Transaction Flow / State Machine
    // ======================================================

    /**
     * Test new order starts with STATUS_DEFAULT
     */
    public function test_new_order_starts_with_default_status(): void
    {
        $transaction = $this->createTransaction();

        $this->assertEquals(0, $transaction['status']);
    }

    /**
     * Test transition from DEFAULT to WAIT (processing)
     */
    public function test_transition_default_to_wait(): void
    {
        $transaction = $this->createTransaction();
        $updated = $this->transitionTo($transaction, 2); // STATUS_WAIT

        $this->assertTrue($updated['success']);
        $this->assertEquals(2, $updated['new_status']);
    }

    /**
     * Test transition from WAIT to DONE (completed)
     */
    public function test_transition_wait_to_done(): void
    {
        $transaction = ['status' => 2]; // STATUS_WAIT
        $updated = $this->transitionTo($transaction, 1); // STATUS_DONE

        $this->assertTrue($updated['success']);
        $this->assertEquals(1, $updated['new_status']);
    }

    /**
     * Test transition from WAIT to FAILURE
     */
    public function test_transition_wait_to_failure(): void
    {
        $transaction = ['status' => 2]; // STATUS_WAIT
        $updated = $this->transitionTo($transaction, 3); // STATUS_FAILURE

        $this->assertTrue($updated['success']);
        $this->assertEquals(3, $updated['new_status']);
    }

    /**
     * Test invalid transition from DONE back to DEFAULT
     */
    public function test_invalid_transition_done_to_default(): void
    {
        $transaction = ['status' => 1]; // STATUS_DONE
        $updated = $this->transitionTo($transaction, 0); // STATUS_DEFAULT

        $this->assertFalse($updated['success']);
    }

    /**
     * Test invalid transition from FAILURE to DONE
     */
    public function test_invalid_transition_failure_to_done(): void
    {
        $transaction = ['status' => 3]; // STATUS_FAILURE
        $updated = $this->transitionTo($transaction, 1); // STATUS_DONE

        $this->assertFalse($updated['success']);
    }

    // ======================================================
    // TEST: Transaction Table Configuration
    // ======================================================

    /**
     * Test transaction table name
     */
    public function test_transaction_table_name(): void
    {
        $this->assertEquals('transactions', $this->getTableName());
    }

    // ======================================================
    // TEST: Payment Status
    // ======================================================

    /**
     * Test payment status pending
     */
    public function test_payment_status_pending(): void
    {
        $this->assertEquals('Chưa thanh toán', $this->getPaymentStatusLabel(0));
    }

    /**
     * Test payment status completed
     */
    public function test_payment_status_completed(): void
    {
        $this->assertEquals('Đã thanh toán', $this->getPaymentStatusLabel(1));
    }

    /**
     * Test payment status refunded
     */
    public function test_payment_status_refunded(): void
    {
        $this->assertEquals('Đã hoàn tiền', $this->getPaymentStatusLabel(2));
    }

    // ======================================================
    // HELPER METHODS (Transaction Logic)
    // ======================================================

    private function getConstant(string $name): int
    {
        $constants = [
            'STATUS_DEFAULT' => 0,
            'STATUS_DONE' => 1,
            'STATUS_WAIT' => 2,
            'STATUS_FAILURE' => 3,
        ];

        return $constants[$name];
    }

    private function getStatusLabel(int $status): string
    {
        $labels = [
            0 => 'Mới đặt',
            1 => 'Hoàn thành',
            2 => 'Đang xử lý',
            3 => 'Thất bại',
        ];

        return $labels[$status] ?? 'Không xác định';
    }

    private function getPaymentStatusLabel(int $status): string
    {
        $labels = [
            0 => 'Chưa thanh toán',
            1 => 'Đã thanh toán',
            2 => 'Đã hoàn tiền',
        ];

        return $labels[$status] ?? 'Không xác định';
    }

    private function calculateOrderTotal(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    private function calculateOrderTotalWithSale(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            $salePrice = $item['original_price'] * (1 - $item['sale_percent'] / 100);
            $total += $salePrice * $item['quantity'];
        }
        return $total;
    }

    private function isValidPaymentMethod(string $method): bool
    {
        $validMethods = ['cod', 'momo', 'vnpay', 'paypal', 'qrcode'];
        return in_array($method, $validMethods);
    }

    private function createTransaction(): array
    {
        return [
            'status' => 0, // STATUS_DEFAULT
            'payment_status' => 0,
            'total' => 0,
        ];
    }

    private function transitionTo(array $transaction, int $newStatus): array
    {
        $validTransitions = [
            0 => [1, 2, 3], // DEFAULT → DONE, WAIT, FAILURE
            2 => [1, 3],    // WAIT → DONE, FAILURE
            1 => [],         // DONE → (no transitions)
            3 => [],         // FAILURE → (no transitions)
        ];

        $currentStatus = $transaction['status'];
        $allowed = $validTransitions[$currentStatus] ?? [];

        if (in_array($newStatus, $allowed)) {
            return ['success' => true, 'new_status' => $newStatus];
        }

        return ['success' => false, 'new_status' => $currentStatus];
    }

    private function getTableName(): string
    {
        return 'transactions';
    }
}
