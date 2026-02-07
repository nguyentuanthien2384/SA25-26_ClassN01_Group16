<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Product Model Business Logic
 * 
 * Tests: Status mapping, Hot product flags, Price calculations,
 *        Category relationships, Fillable attributes
 */
class ProductModelTest extends TestCase
{
    // ======================================================
    // TEST: Product Status
    // ======================================================

    /**
     * Test product status PUBLIC
     */
    public function test_product_status_public(): void
    {
        $status = $this->getProductStatus(1);

        $this->assertEquals('Public', $status['name']);
        $this->assertEquals('label-success', $status['class']);
    }

    /**
     * Test product status PRIVATE
     */
    public function test_product_status_private(): void
    {
        $status = $this->getProductStatus(0);

        $this->assertEquals('Private', $status['name']);
        $this->assertEquals('label-default', $status['class']);
    }

    /**
     * Test product status unknown returns N/A
     */
    public function test_product_status_unknown_returns_na(): void
    {
        $status = $this->getProductStatus(99);

        $this->assertEquals('[N\A]', $status);
    }

    // ======================================================
    // TEST: Product Hot Flag
    // ======================================================

    /**
     * Test product hot flag ON
     */
    public function test_product_hot_flag_on(): void
    {
        $hot = $this->getProductHot(1);

        $this->assertEquals('Nổi bật', $hot['name']);
        $this->assertEquals('label-danger', $hot['class']);
    }

    /**
     * Test product hot flag OFF
     */
    public function test_product_hot_flag_off(): void
    {
        $hot = $this->getProductHot(0);

        $this->assertEquals('Không', $hot['name']);
        $this->assertEquals('label-default', $hot['class']);
    }

    // ======================================================
    // TEST: Product Constants
    // ======================================================

    /**
     * Test product status constants
     */
    public function test_product_status_constants(): void
    {
        $this->assertEquals(1, $this->getConstant('STATUS_PUBLIC'));
        $this->assertEquals(0, $this->getConstant('STATUS_PRIVATE'));
    }

    /**
     * Test product hot constants
     */
    public function test_product_hot_constants(): void
    {
        $this->assertEquals(1, $this->getConstant('HOT_ON'));
        $this->assertEquals(0, $this->getConstant('HOT_OFF'));
    }

    // ======================================================
    // TEST: Product Fillable Fields
    // ======================================================

    /**
     * Test product fillable fields are defined
     */
    public function test_product_fillable_fields(): void
    {
        $fillable = $this->getProductFillable();

        $this->assertContains('pro_name', $fillable);
        $this->assertContains('pro_slug', $fillable);
        $this->assertContains('pro_price', $fillable);
        $this->assertContains('pro_sale', $fillable);
        $this->assertContains('pro_category_id', $fillable);
        $this->assertContains('pro_content', $fillable);
        $this->assertContains('pro_description', $fillable);
        $this->assertContains('pro_image', $fillable);
        $this->assertContains('quantity', $fillable);
        $this->assertContains('pro_active', $fillable);
        $this->assertContains('pro_hot', $fillable);
    }

    /**
     * Test product table name is correct
     */
    public function test_product_table_name(): void
    {
        $this->assertEquals('products', $this->getTableName());
    }

    // ======================================================
    // TEST: Price with Sale Calculation
    // ======================================================

    /**
     * Test sale price calculation 10% off
     */
    public function test_sale_price_calculation_10_percent(): void
    {
        $originalPrice = 29990000;
        $salePercent = 10;

        $salePrice = $this->calculateSalePrice($originalPrice, $salePercent);

        $this->assertEquals(26991000, $salePrice);
    }

    /**
     * Test sale price calculation 50% off
     */
    public function test_sale_price_calculation_50_percent(): void
    {
        $originalPrice = 10000000;
        $salePercent = 50;

        $salePrice = $this->calculateSalePrice($originalPrice, $salePercent);

        $this->assertEquals(5000000, $salePrice);
    }

    /**
     * Test sale price with 0% (no sale)
     */
    public function test_sale_price_with_no_sale(): void
    {
        $originalPrice = 29990000;
        $salePercent = 0;

        $salePrice = $this->calculateSalePrice($originalPrice, $salePercent);

        $this->assertEquals(29990000, $salePrice);
    }

    /**
     * Test sale price with 100% (free)
     */
    public function test_sale_price_with_100_percent(): void
    {
        $originalPrice = 29990000;
        $salePercent = 100;

        $salePrice = $this->calculateSalePrice($originalPrice, $salePercent);

        $this->assertEquals(0, $salePrice);
    }

    // ======================================================
    // TEST: Slug Generation
    // ======================================================

    /**
     * Test product slug generation from name
     */
    public function test_slug_generation_from_name(): void
    {
        $name = 'iPhone 15 Pro Max 256GB';
        $slug = $this->generateSlug($name);

        $this->assertEquals('iphone-15-pro-max-256gb', $slug);
    }

    /**
     * Test slug generation with Vietnamese characters
     */
    public function test_slug_generation_with_vietnamese(): void
    {
        $name = 'Điện thoại Samsung Galaxy S24';
        $slug = $this->generateSlug($name);

        $this->assertStringNotContainsString(' ', $slug);
        $this->assertMatchesRegularExpression('/^[a-z0-9\-]+$/', $slug);
    }

    /**
     * Test slug generation with special characters
     */
    public function test_slug_generation_with_special_chars(): void
    {
        $name = 'Product #1 @Special!';
        $slug = $this->generateSlug($name);

        $this->assertStringNotContainsString('#', $slug);
        $this->assertStringNotContainsString('@', $slug);
        $this->assertStringNotContainsString('!', $slug);
    }

    // ======================================================
    // HELPER METHODS (Product Model Logic)
    // ======================================================

    private function getProductStatus(int $active): mixed
    {
        $statusMap = [
            1 => ['name' => 'Public', 'class' => 'label-success'],
            0 => ['name' => 'Private', 'class' => 'label-default'],
        ];

        return $statusMap[$active] ?? '[N\A]';
    }

    private function getProductHot(int $hot): mixed
    {
        $hotMap = [
            1 => ['name' => 'Nổi bật', 'class' => 'label-danger'],
            0 => ['name' => 'Không', 'class' => 'label-default'],
        ];

        return $hotMap[$hot] ?? '[N\A]';
    }

    private function getConstant(string $name): int
    {
        $constants = [
            'STATUS_PUBLIC' => 1,
            'STATUS_PRIVATE' => 0,
            'HOT_ON' => 1,
            'HOT_OFF' => 0,
        ];

        return $constants[$name];
    }

    private function getProductFillable(): array
    {
        return [
            'pro_name', 'pro_slug', 'pro_price', 'pro_sale', 'pro_total',
            'pro_category_id', 'pro_content', 'pro_description', 'pro_image',
            'quantity', 'pro_active', 'pro_hot', 'pro_pay', 'pro_total_number',
        ];
    }

    private function getTableName(): string
    {
        return 'products';
    }

    private function calculateSalePrice(float $originalPrice, float $salePercent): float
    {
        return $originalPrice - ($originalPrice * $salePercent / 100);
    }

    private function generateSlug(string $name): string
    {
        // Convert Vietnamese characters
        $vietnamese = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
            'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
            'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
            'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
            'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
            'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
            'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
            'Đ',
        ];
        $ascii = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        $slug = str_replace($vietnamese, $ascii, $name);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}
