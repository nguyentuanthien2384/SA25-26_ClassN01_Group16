<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for String Helper
 * 
 * Tests string manipulation functions
 */
class StringHelperTest extends TestCase
{
    /**
     * Test slugify converts text to URL-friendly slug
     */
    public function test_slugify_converts_text_to_slug(): void
    {
        // Arrange
        $text = 'iPhone 15 Pro Max';
        
        // Act
        $slug = $this->slugify($text);
        
        // Assert
        $this->assertEquals('iphone-15-pro-max', $slug);
    }

    /**
     * Test slugify handles special characters
     */
    public function test_slugify_handles_special_characters(): void
    {
        // Arrange
        $text = 'Product @#$% 2024!!!';
        
        // Act
        $slug = $this->slugify($text);
        
        // Assert
        $this->assertEquals('product-2024', $slug);
    }

    /**
     * Test slugify handles uppercase
     */
    public function test_slugify_converts_uppercase_to_lowercase(): void
    {
        // Arrange
        $text = 'SAMSUNG GALAXY S24 ULTRA';
        
        // Act
        $slug = $this->slugify($text);
        
        // Assert
        $this->assertEquals('samsung-galaxy-s24-ultra', $slug);
    }

    /**
     * Test slugify handles multiple spaces
     */
    public function test_slugify_handles_multiple_spaces(): void
    {
        // Arrange
        $text = 'Product    with    spaces';
        
        // Act
        $slug = $this->slugify($text);
        
        // Assert
        $this->assertEquals('product-with-spaces', $slug);
    }

    /**
     * Test slugify trims leading/trailing dashes
     */
    public function test_slugify_trims_dashes(): void
    {
        // Arrange
        $text = '---Product Name---';
        
        // Act
        $slug = $this->slugify($text);
        
        // Assert
        $this->assertEquals('product-name', $slug);
    }

    /**
     * Test format currency VND
     */
    public function test_format_currency_vnd(): void
    {
        // Arrange
        $amount = 10000000;
        
        // Act
        $formatted = $this->formatCurrency($amount);
        
        // Assert
        $this->assertEquals('10.000.000 ₫', $formatted);
    }

    /**
     * Test format currency with zero
     */
    public function test_format_currency_with_zero(): void
    {
        // Arrange
        $amount = 0;
        
        // Act
        $formatted = $this->formatCurrency($amount);
        
        // Assert
        $this->assertEquals('0 ₫', $formatted);
    }

    /**
     * Test format currency with large number
     */
    public function test_format_currency_with_large_number(): void
    {
        // Arrange
        $amount = 123456789;
        
        // Act
        $formatted = $this->formatCurrency($amount);
        
        // Assert
        $this->assertEquals('123.456.789 ₫', $formatted);
    }

    /**
     * Test truncate text to specified length
     */
    public function test_truncate_text_to_length(): void
    {
        // Arrange
        $text = 'This is a very long product description that needs to be truncated';
        $length = 20;
        
        // Act
        $truncated = $this->truncate($text, $length);
        
        // Assert
        $this->assertEquals('This is a very long...', $truncated);
    }

    /**
     * Test truncate text shorter than length returns original
     */
    public function test_truncate_short_text_returns_original(): void
    {
        // Arrange
        $text = 'Short text';
        $length = 50;
        
        // Act
        $truncated = $this->truncate($text, $length);
        
        // Assert
        $this->assertEquals('Short text', $truncated);
    }

    /**
     * Test truncate with custom suffix
     */
    public function test_truncate_with_custom_suffix(): void
    {
        // Arrange
        $text = 'This is a very long product description';
        $length = 20;
        $suffix = ' [...]';
        
        // Act
        $truncated = $this->truncate($text, $length, $suffix);
        
        // Assert
        $this->assertEquals('This is a very long [...]', $truncated);
    }

    /**
     * Test remove HTML tags
     */
    public function test_strip_html_tags(): void
    {
        // Arrange
        $html = '<p>Product <strong>description</strong> with <a href="#">link</a></p>';
        
        // Act
        $plain = $this->stripHtml($html);
        
        // Assert
        $this->assertEquals('Product description with link', $plain);
    }

    /**
     * Test generate random string
     */
    public function test_generate_random_string(): void
    {
        // Arrange
        $length = 10;
        
        // Act
        $random = $this->randomString($length);
        
        // Assert
        $this->assertEquals($length, strlen($random));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $random);
    }

    // ==========================================
    // HELPER METHODS (Business Logic)
    // ==========================================
    
    /**
     * Convert text to URL-friendly slug
     */
    private function slugify(string $text): string
    {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Replace non-alphanumeric with dashes
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        
        // Remove multiple dashes
        $text = preg_replace('/-+/', '-', $text);
        
        // Trim dashes
        $text = trim($text, '-');
        
        return $text;
    }

    /**
     * Format number as VND currency
     */
    private function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Truncate text to specified length
     */
    private function truncate(string $text, int $length, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Strip HTML tags from text
     */
    private function stripHtml(string $html): string
    {
        return strip_tags($html);
    }

    /**
     * Generate random string
     */
    private function randomString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $string;
    }
}
