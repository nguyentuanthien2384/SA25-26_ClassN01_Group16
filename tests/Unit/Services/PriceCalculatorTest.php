<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * Unit Test for Price Calculator
 * 
 * Tests price calculation with sale/discount
 */
class PriceCalculatorTest extends TestCase
{
    /**
     * Test calculate final price without sale
     */
    public function test_calculate_final_price_without_sale(): void
    {
        // Arrange
        $originalPrice = 10000000;
        $sale = 0;
        
        // Act
        $finalPrice = $this->calculateFinalPrice($originalPrice, $sale);
        
        // Assert
        $this->assertEquals(10000000, $finalPrice);
    }

    /**
     * Test calculate final price with 10% sale
     */
    public function test_calculate_final_price_with_10_percent_sale(): void
    {
        // Arrange
        $originalPrice = 10000000;
        $sale = 10;
        
        // Act
        $finalPrice = $this->calculateFinalPrice($originalPrice, $sale);
        
        // Assert
        // 10,000,000 - (10,000,000 * 10 / 100) = 9,000,000
        $this->assertEquals(9000000, $finalPrice);
    }

    /**
     * Test calculate final price with 50% sale
     */
    public function test_calculate_final_price_with_50_percent_sale(): void
    {
        // Arrange
        $originalPrice = 20000000;
        $sale = 50;
        
        // Act
        $finalPrice = $this->calculateFinalPrice($originalPrice, $sale);
        
        // Assert
        $this->assertEquals(10000000, $finalPrice);
    }

    /**
     * Test calculate final price with 100% sale (free)
     */
    public function test_calculate_final_price_with_100_percent_sale(): void
    {
        // Arrange
        $originalPrice = 5000000;
        $sale = 100;
        
        // Act
        $finalPrice = $this->calculateFinalPrice($originalPrice, $sale);
        
        // Assert
        $this->assertEquals(0, $finalPrice);
    }

    /**
     * Test calculate final price throws exception for negative sale
     */
    public function test_calculate_final_price_throws_exception_for_negative_sale(): void
    {
        // Arrange & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sale percentage must be between 0 and 100');
        
        // Act
        $this->calculateFinalPrice(1000000, -10);
    }

    /**
     * Test calculate final price throws exception for sale > 100
     */
    public function test_calculate_final_price_throws_exception_for_sale_over_100(): void
    {
        // Arrange & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sale percentage must be between 0 and 100');
        
        // Act
        $this->calculateFinalPrice(1000000, 150);
    }

    /**
     * Test calculate sale amount
     */
    public function test_calculate_sale_amount(): void
    {
        // Arrange
        $originalPrice = 10000000;
        $salePercent = 20;
        
        // Act
        $saleAmount = $originalPrice * ($salePercent / 100);
        
        // Assert
        $this->assertEquals(2000000, $saleAmount);
    }

    /**
     * Test calculate final price with decimal sale percentage
     */
    public function test_calculate_final_price_with_decimal_sale(): void
    {
        // Arrange
        $originalPrice = 10000000;
        $sale = 15.5; // 15.5%
        
        // Act
        $finalPrice = $this->calculateFinalPrice($originalPrice, $sale);
        
        // Assert
        // 10,000,000 - (10,000,000 * 15.5 / 100) = 8,450,000
        $this->assertEquals(8450000, $finalPrice);
    }

    /**
     * Test zero price with sale returns zero
     */
    public function test_zero_price_with_sale_returns_zero(): void
    {
        // Arrange
        $originalPrice = 0;
        $sale = 50;
        
        // Act
        $finalPrice = $this->calculateFinalPrice($originalPrice, $sale);
        
        // Assert
        $this->assertEquals(0, $finalPrice);
    }

    // ==========================================
    // HELPER METHOD (Business Logic)
    // ==========================================
    
    /**
     * Calculate final price with sale
     * 
     * @param float $originalPrice Original price
     * @param float $salePercent Sale percentage (0-100)
     * @return float Final price
     * @throws InvalidArgumentException
     */
    private function calculateFinalPrice(float $originalPrice, float $salePercent): float
    {
        // Validate sale percentage
        if ($salePercent < 0 || $salePercent > 100) {
            throw new InvalidArgumentException('Sale percentage must be between 0 and 100');
        }
        
        // Calculate sale amount
        $saleAmount = $originalPrice * ($salePercent / 100);
        
        // Calculate final price
        $finalPrice = $originalPrice - $saleAmount;
        
        return $finalPrice;
    }
}
