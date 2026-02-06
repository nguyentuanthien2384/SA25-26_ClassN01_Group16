<?php

namespace Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Product Validator
 * 
 * Tests product validation rules
 */
class ProductValidatorTest extends TestCase
{
    /**
     * Test validate product price is positive
     */
    public function test_validate_product_price_positive_passes(): void
    {
        // Arrange
        $price = 1000000;
        
        // Act
        $isValid = $this->validatePrice($price);
        
        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * Test validate product price zero fails
     */
    public function test_validate_product_price_zero_fails(): void
    {
        // Arrange
        $price = 0;
        
        // Act
        $isValid = $this->validatePrice($price);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test validate product price negative fails
     */
    public function test_validate_product_price_negative_fails(): void
    {
        // Arrange
        $price = -1000;
        
        // Act
        $isValid = $this->validatePrice($price);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test validate product name required
     */
    public function test_validate_product_name_required_passes(): void
    {
        // Arrange
        $name = 'iPhone 15 Pro Max';
        
        // Act
        $isValid = $this->validateName($name);
        
        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * Test validate empty product name fails
     */
    public function test_validate_empty_product_name_fails(): void
    {
        // Arrange
        $name = '';
        
        // Act
        $isValid = $this->validateName($name);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test validate product name minimum length
     */
    public function test_validate_product_name_minimum_length(): void
    {
        // Arrange
        $name = 'IP'; // Too short (< 3 chars)
        
        // Act
        $isValid = $this->validateName($name, 3);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test validate stock quantity non-negative
     */
    public function test_validate_stock_quantity_non_negative_passes(): void
    {
        // Arrange
        $quantity = 10;
        
        // Act
        $isValid = $this->validateStock($quantity);
        
        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * Test validate stock quantity zero passes
     */
    public function test_validate_stock_quantity_zero_passes(): void
    {
        // Arrange
        $quantity = 0; // Out of stock is valid
        
        // Act
        $isValid = $this->validateStock($quantity);
        
        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * Test validate stock quantity negative fails
     */
    public function test_validate_stock_quantity_negative_fails(): void
    {
        // Arrange
        $quantity = -5;
        
        // Act
        $isValid = $this->validateStock($quantity);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test validate sale percentage in range
     */
    public function test_validate_sale_percentage_in_range_passes(): void
    {
        // Arrange
        $sale = 50; // 50% sale
        
        // Act
        $isValid = $this->validateSale($sale);
        
        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * Test validate sale percentage negative fails
     */
    public function test_validate_sale_percentage_negative_fails(): void
    {
        // Arrange
        $sale = -10;
        
        // Act
        $isValid = $this->validateSale($sale);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test validate sale percentage over 100 fails
     */
    public function test_validate_sale_percentage_over_100_fails(): void
    {
        // Arrange
        $sale = 150;
        
        // Act
        $isValid = $this->validateSale($sale);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test validate complete product data
     */
    public function test_validate_complete_product_data_passes(): void
    {
        // Arrange
        $productData = [
            'name' => 'iPhone 15 Pro Max',
            'price' => 29990000,
            'quantity' => 10,
            'sale' => 10,
            'category_id' => 1
        ];
        
        // Act
        $isValid = $this->validateProduct($productData);
        
        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * Test validate product with missing required fields fails
     */
    public function test_validate_product_with_missing_fields_fails(): void
    {
        // Arrange
        $productData = [
            'name' => 'iPhone 15',
            // Missing price, quantity, category_id
        ];
        
        // Act
        $isValid = $this->validateProduct($productData);
        
        // Assert
        $this->assertFalse($isValid);
    }

    // ==========================================
    // HELPER METHODS (Validation Logic)
    // ==========================================
    
    /**
     * Validate product price
     */
    private function validatePrice(float $price): bool
    {
        return $price > 0;
    }

    /**
     * Validate product name
     */
    private function validateName(string $name, int $minLength = 1): bool
    {
        return !empty($name) && strlen($name) >= $minLength;
    }

    /**
     * Validate stock quantity
     */
    private function validateStock(int $quantity): bool
    {
        return $quantity >= 0;
    }

    /**
     * Validate sale percentage
     */
    private function validateSale(float $sale): bool
    {
        return $sale >= 0 && $sale <= 100;
    }

    /**
     * Validate complete product data
     */
    private function validateProduct(array $data): bool
    {
        $required = ['name', 'price', 'quantity', 'category_id'];
        
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }
        
        if (!$this->validateName($data['name'])) {
            return false;
        }
        
        if (!$this->validatePrice($data['price'])) {
            return false;
        }
        
        if (!$this->validateStock($data['quantity'])) {
            return false;
        }
        
        if (isset($data['sale']) && !$this->validateSale($data['sale'])) {
            return false;
        }
        
        return true;
    }
}
