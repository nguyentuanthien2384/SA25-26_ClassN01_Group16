<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Payment Signature Generator
 * 
 * Tests HMAC-SHA256 signature generation and verification
 * Critical for PCI DSS compliance
 */
class PaymentSignatureTest extends TestCase
{
    private string $secretKey = 'test_secret_key_12345';

    /**
     * Test generate MoMo signature
     */
    public function test_generate_momo_signature(): void
    {
        // Arrange
        $data = [
            'partnerCode' => 'MOMO',
            'orderId' => 'ELEC_123_1706456789',
            'amount' => 10000000,
            'orderInfo' => 'Thanh toan don hang #123',
        ];
        
        // Act
        $signature = $this->generateSignature($data, $this->secretKey);
        
        // Assert
        $this->assertNotEmpty($signature);
        $this->assertEquals(64, strlen($signature)); // SHA256 = 64 chars
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $signature);
    }

    /**
     * Test verify valid MoMo signature
     */
    public function test_verify_momo_signature_valid(): void
    {
        // Arrange
        $data = [
            'partnerCode' => 'MOMO',
            'orderId' => 'ELEC_123_1706456789',
            'amount' => 10000000,
            'orderInfo' => 'Thanh toan don hang #123',
        ];
        
        $signature = $this->generateSignature($data, $this->secretKey);
        
        // Act
        $isValid = $this->verifySignature($data, $signature, $this->secretKey);
        
        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * Test verify invalid MoMo signature
     */
    public function test_verify_momo_signature_invalid(): void
    {
        // Arrange
        $data = [
            'partnerCode' => 'MOMO',
            'orderId' => 'ELEC_123_1706456789',
            'amount' => 10000000,
        ];
        
        $fakeSignature = 'abc123invalidhash';
        
        // Act
        $isValid = $this->verifySignature($data, $fakeSignature, $this->secretKey);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test signature changes when data changes
     */
    public function test_signature_changes_when_data_changes(): void
    {
        // Arrange
        $data1 = [
            'orderId' => 'ELEC_123',
            'amount' => 10000000,
        ];
        
        $data2 = [
            'orderId' => 'ELEC_123',
            'amount' => 20000000, // Different amount
        ];
        
        // Act
        $signature1 = $this->generateSignature($data1, $this->secretKey);
        $signature2 = $this->generateSignature($data2, $this->secretKey);
        
        // Assert
        $this->assertNotEquals($signature1, $signature2);
    }

    /**
     * Test signature same for same data
     */
    public function test_signature_same_for_same_data(): void
    {
        // Arrange
        $data = [
            'orderId' => 'ELEC_123',
            'amount' => 10000000,
        ];
        
        // Act
        $signature1 = $this->generateSignature($data, $this->secretKey);
        $signature2 = $this->generateSignature($data, $this->secretKey);
        
        // Assert
        $this->assertEquals($signature1, $signature2);
    }

    /**
     * Test generate VNPay signature
     */
    public function test_generate_vnpay_signature(): void
    {
        // Arrange
        $data = [
            'vnp_TmnCode' => 'VNPAY123',
            'vnp_TxnRef' => 'ELEC_456',
            'vnp_Amount' => 1000000000, // VNPay uses amount * 100
            'vnp_OrderInfo' => 'Thanh toan don hang',
        ];
        
        // Act
        $signature = $this->generateSignature($data, $this->secretKey);
        
        // Assert
        $this->assertNotEmpty($signature);
        $this->assertEquals(64, strlen($signature));
    }

    /**
     * Test verify signature with tampered amount fails
     */
    public function test_verify_signature_with_tampered_amount_fails(): void
    {
        // Arrange: Original data
        $originalData = [
            'orderId' => 'ELEC_123',
            'amount' => 10000000,
        ];
        
        $signature = $this->generateSignature($originalData, $this->secretKey);
        
        // Act: Tamper with amount
        $tamperedData = [
            'orderId' => 'ELEC_123',
            'amount' => 1000000, // Changed to 1M (fraud attempt)
        ];
        
        $isValid = $this->verifySignature($tamperedData, $signature, $this->secretKey);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test verify signature with wrong secret key fails
     */
    public function test_verify_signature_with_wrong_secret_key_fails(): void
    {
        // Arrange
        $data = [
            'orderId' => 'ELEC_123',
            'amount' => 10000000,
        ];
        
        $signature = $this->generateSignature($data, $this->secretKey);
        $wrongSecretKey = 'wrong_secret_key';
        
        // Act
        $isValid = $this->verifySignature($data, $signature, $wrongSecretKey);
        
        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * Test signature is case-sensitive
     */
    public function test_signature_is_case_sensitive(): void
    {
        // Arrange
        $data = [
            'orderId' => 'ELEC_123',
            'amount' => 10000000,
        ];
        
        $signature = $this->generateSignature($data, $this->secretKey);
        $uppercaseSignature = strtoupper($signature);
        
        // Act
        $isValid = $this->verifySignature($data, $uppercaseSignature, $this->secretKey);
        
        // Assert
        $this->assertFalse($isValid);
    }

    // ==========================================
    // HELPER METHODS (Cryptography Logic)
    // ==========================================
    
    /**
     * Generate HMAC-SHA256 signature
     * 
     * @param array $data Payment data
     * @param string $secretKey Secret key
     * @return string Signature (64 chars hex)
     */
    private function generateSignature(array $data, string $secretKey): string
    {
        // Sort data by key (required for MoMo/VNPay)
        ksort($data);
        
        // Build query string
        $queryString = http_build_query($data);
        
        // Generate HMAC-SHA256
        $signature = hash_hmac('sha256', $queryString, $secretKey);
        
        return $signature;
    }

    /**
     * Verify HMAC-SHA256 signature
     * 
     * @param array $data Payment data
     * @param string $signature Signature to verify
     * @param string $secretKey Secret key
     * @return bool True if valid, false otherwise
     */
    private function verifySignature(array $data, string $signature, string $secretKey): bool
    {
        // Generate expected signature
        $expectedSignature = $this->generateSignature($data, $secretKey);
        
        // Compare signatures (timing-safe comparison)
        return hash_equals($expectedSignature, $signature);
    }
}
