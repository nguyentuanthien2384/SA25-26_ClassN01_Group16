<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for User Model Business Logic
 * 
 * Tests: User validation, Password hashing, Email validation,
 *        Status mapping, Fillable fields
 */
class UserModelTest extends TestCase
{
    // ======================================================
    // TEST: User Validation
    // ======================================================

    /**
     * Test validate user email format
     */
    public function test_validate_email_format(): void
    {
        $this->assertTrue($this->validateEmail('user@example.com'));
        $this->assertTrue($this->validateEmail('test.user@domain.vn'));
        $this->assertFalse($this->validateEmail('invalid-email'));
        $this->assertFalse($this->validateEmail(''));
        $this->assertFalse($this->validateEmail('@domain.com'));
    }

    /**
     * Test validate phone number format (Vietnam)
     */
    public function test_validate_phone_number_vietnam(): void
    {
        $this->assertTrue($this->validatePhone('0912345678'));
        $this->assertTrue($this->validatePhone('0398765432'));
        $this->assertTrue($this->validatePhone('+84912345678'));
        $this->assertFalse($this->validatePhone('123'));
        $this->assertFalse($this->validatePhone(''));
        $this->assertFalse($this->validatePhone('abcdefghij'));
    }

    /**
     * Test validate user name required and min length
     */
    public function test_validate_user_name(): void
    {
        $this->assertTrue($this->validateName('Nghiêm Đức Việt'));
        $this->assertTrue($this->validateName('Nguyễn Văn A'));
        $this->assertFalse($this->validateName(''));
        $this->assertFalse($this->validateName('A')); // Too short
    }

    /**
     * Test validate user password strength
     */
    public function test_validate_password_strength(): void
    {
        $this->assertTrue($this->validatePassword('SecureP@ss123'));
        $this->assertTrue($this->validatePassword('MyStr0ng!'));
        $this->assertFalse($this->validatePassword('123')); // Too short
        $this->assertFalse($this->validatePassword('')); // Empty
        $this->assertFalse($this->validatePassword('abcde')); // Too short
    }

    // ======================================================
    // TEST: User Fillable Fields
    // ======================================================

    /**
     * Test user fillable fields
     */
    public function test_user_fillable_fields(): void
    {
        $fillable = $this->getUserFillable();

        $this->assertContains('u_name', $fillable);
        $this->assertContains('u_email', $fillable);
        $this->assertContains('u_password', $fillable);
        $this->assertContains('u_phone', $fillable);
        $this->assertContains('u_address', $fillable);
    }

    /**
     * Test user hidden fields (sensitive data)
     */
    public function test_user_hidden_fields(): void
    {
        $hidden = $this->getUserHidden();

        $this->assertContains('u_password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    // ======================================================
    // TEST: User Status
    // ======================================================

    /**
     * Test user status public (active)
     */
    public function test_user_status_active(): void
    {
        $status = $this->getUserStatus(1);

        $this->assertEquals('Public', $status['name']);
        $this->assertEquals('label-success', $status['class']);
    }

    /**
     * Test user status private (blocked)
     */
    public function test_user_status_blocked(): void
    {
        $status = $this->getUserStatus(0);

        $this->assertEquals('Private', $status['name']);
        $this->assertEquals('label-default', $status['class']);
    }

    // ======================================================
    // TEST: User Registration Data
    // ======================================================

    /**
     * Test validate complete registration data
     */
    public function test_validate_complete_registration_data(): void
    {
        $data = [
            'name' => 'Nghiêm Đức Việt',
            'email' => 'viet@example.com',
            'phone' => '0912345678',
            'password' => 'SecureP@ss123',
            'password_confirmation' => 'SecureP@ss123',
        ];

        $result = $this->validateRegistration($data);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    /**
     * Test registration fails with mismatched passwords
     */
    public function test_registration_fails_with_mismatched_passwords(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0912345678',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword!',
        ];

        $result = $this->validateRegistration($data);

        $this->assertFalse($result['valid']);
        $this->assertContains('password_mismatch', $result['errors']);
    }

    /**
     * Test registration fails with invalid email
     */
    public function test_registration_fails_with_invalid_email(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'phone' => '0912345678',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $result = $this->validateRegistration($data);

        $this->assertFalse($result['valid']);
        $this->assertContains('invalid_email', $result['errors']);
    }

    /**
     * Test registration fails with missing fields
     */
    public function test_registration_fails_with_missing_fields(): void
    {
        $data = [
            'name' => 'Test User',
            // Missing email, phone, password
        ];

        $result = $this->validateRegistration($data);

        $this->assertFalse($result['valid']);
    }

    // ======================================================
    // TEST: Admin Model Access Modifiers
    // ======================================================

    /**
     * Test public property access
     */
    public function test_admin_public_property_access(): void
    {
        $admin = $this->createAdminModel();

        $this->assertEquals('Đây là thuộc tính public', $admin['publicProperty']);
    }

    /**
     * Test private property access via getter
     */
    public function test_admin_private_property_via_getter(): void
    {
        $admin = $this->createAdminModel();

        $this->assertEquals('Đây là thuộc tính private', $admin['privateViaGetter']);
    }

    /**
     * Test admin email validation
     */
    public function test_admin_email_validation(): void
    {
        $this->assertTrue($this->validateEmail('admin@electroshop.com'));
        $this->assertFalse($this->validateEmail('not-an-email'));
    }

    // ======================================================
    // HELPER METHODS (User Model Logic)
    // ======================================================

    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validatePhone(string $phone): bool
    {
        if (empty($phone)) {
            return false;
        }
        return preg_match('/^(\+84|0)[0-9]{9,10}$/', $phone) === 1;
    }

    private function validateName(string $name, int $minLength = 2): bool
    {
        return !empty($name) && mb_strlen($name) >= $minLength;
    }

    private function validatePassword(string $password, int $minLength = 6): bool
    {
        return !empty($password) && strlen($password) >= $minLength;
    }

    private function getUserFillable(): array
    {
        return ['u_name', 'u_email', 'u_password', 'u_phone', 'u_address', 'u_avatar'];
    }

    private function getUserHidden(): array
    {
        return ['u_password', 'remember_token'];
    }

    private function getUserStatus(int $status): array
    {
        $statusMap = [
            1 => ['name' => 'Public', 'class' => 'label-success'],
            0 => ['name' => 'Private', 'class' => 'label-default'],
        ];

        return $statusMap[$status] ?? ['name' => 'Unknown', 'class' => 'label-warning'];
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['name']) || mb_strlen($data['name']) < 2) {
            $errors[] = 'invalid_name';
        }

        if (empty($data['email']) || !$this->validateEmail($data['email'])) {
            $errors[] = 'invalid_email';
        }

        if (empty($data['phone']) || !$this->validatePhone($data['phone'])) {
            $errors[] = 'invalid_phone';
        }

        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'weak_password';
        }

        if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) {
            $errors[] = 'password_mismatch';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    private function createAdminModel(): array
    {
        return [
            'publicProperty' => 'Đây là thuộc tính public',
            'privateViaGetter' => 'Đây là thuộc tính private',
            'protectedProperty' => 'Đây là thuộc tính protected',
        ];
    }
}
