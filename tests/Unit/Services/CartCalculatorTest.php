<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Cart Calculator
 * 
 * Tests business logic for cart total calculation
 * No database, no HTTP requests - pure logic testing
 */
class CartCalculatorTest extends TestCase
{
    /**
     * Test calculate total with empty cart
     * Expected: return 0
     */
    public function test_calculate_total_with_empty_cart(): void
    {
        // Arrange
        $items = [];
        
        // Act
        $total = $this->calculateTotal($items);
        
        // Assert
        $this->assertEquals(0, $total);
    }

    /**
     * Test calculate total with single item
     * Expected: price * quantity
     */
    public function test_calculate_total_with_single_item(): void
    {
        // Arrange
        $items = [
            ['price' => 1000000, 'quantity' => 2]
        ];
        
        // Act
        $total = $this->calculateTotal($items);
        
        // Assert
        $this->assertEquals(2000000, $total);
    }

    /**
     * Test calculate total with multiple items
     */
    public function test_calculate_total_with_multiple_items(): void
    {
        // Arrange
        $items = [
            ['price' => 1000000, 'quantity' => 2],  // 2,000,000
            ['price' => 500000, 'quantity' => 3],   // 1,500,000
            ['price' => 2000000, 'quantity' => 1],  // 2,000,000
        ];
        
        // Act
        $total = $this->calculateTotal($items);
        
        // Assert
        $this->assertEquals(5500000, $total); // Total: 5,500,000
    }

    /**
     * Test calculate total with discount percentage
     */
    public function test_calculate_total_with_discount_percentage(): void
    {
        // Arrange
        $items = [
            ['price' => 1000000, 'quantity' => 2]
        ];
        $discountPercent = 10; // 10% discount
        
        // Act
        $total = $this->calculateTotal($items, $discountPercent);
        
        // Assert
        // 2,000,000 - (2,000,000 * 10 / 100) = 1,800,000
        $this->assertEquals(1800000, $total);
    }

    /**
     * Test calculate total with shipping fee
     */
    public function test_calculate_total_with_shipping_fee(): void
    {
        // Arrange
        $items = [
            ['price' => 1000000, 'quantity' => 2]
        ];
        $discountPercent = 0;
        $shippingFee = 30000;
        
        // Act
        $total = $this->calculateTotal($items, $discountPercent, $shippingFee);
        
        // Assert
        // 2,000,000 + 30,000 = 2,030,000
        $this->assertEquals(2030000, $total);
    }

    /**
     * Test calculate total with discount and shipping
     */
    public function test_calculate_total_with_discount_and_shipping(): void
    {
        // Arrange
        $items = [
            ['price' => 1000000, 'quantity' => 2]
        ];
        $discountPercent = 10;
        $shippingFee = 30000;
        
        // Act
        $total = $this->calculateTotal($items, $discountPercent, $shippingFee);
        
        // Assert
        // (2,000,000 - 200,000) + 30,000 = 1,830,000
        $this->assertEquals(1830000, $total);
    }

    /**
     * Test calculate subtotal for single item
     */
    public function test_calculate_item_subtotal(): void
    {
        // Arrange
        $item = ['price' => 1000000, 'quantity' => 3];
        
        // Act
        $subtotal = $item['price'] * $item['quantity'];
        
        // Assert
        $this->assertEquals(3000000, $subtotal);
    }

    /**
     * Test zero quantity returns zero
     */
    public function test_calculate_with_zero_quantity_returns_zero(): void
    {
        // Arrange
        $items = [
            ['price' => 1000000, 'quantity' => 0]
        ];
        
        // Act
        $total = $this->calculateTotal($items);
        
        // Assert
        $this->assertEquals(0, $total);
    }

    // ==========================================
    // HELPER METHOD (Business Logic)
    // ==========================================
    
    /**
     * Calculate cart total
     * 
     * @param array $items Cart items
     * @param float $discountPercent Discount percentage (0-100)
     * @param float $shippingFee Shipping fee
     * @return float Total amount
     */
    private function calculateTotal(
        array $items, 
        float $discountPercent = 0, 
        float $shippingFee = 0
    ): float {
        // Calculate subtotal
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Apply discount
        $discount = $subtotal * ($discountPercent / 100);
        $totalAfterDiscount = $subtotal - $discount;
        
        // Add shipping fee
        $grandTotal = $totalAfterDiscount + $shippingFee;
        
        return $grandTotal;
    }
}
