# 🧪 UNIT TESTS - GIẢI THÍCH CHI TIẾT

**Ngày:** 2026-01-28  
**Dự án:** ElectroShop E-Commerce Platform

---

## 📊 TỔNG QUAN

### Hiện trạng Unit Tests trong dự án:

| Loại Test | Số lượng | Thư mục | Pass Rate |
|-----------|----------|---------|-----------|
| **Unit Tests** | 1 test | `tests/Unit/` | 100% (1/1) |
| **Feature Tests** | 43 tests | `tests/Feature/` | 95% (41/43) |
| **Tổng cộng** | 44 tests | `tests/` | 95.45% (42/44) |

---

## 🎯 UNIT TEST LÀ GÌ?

### Định nghĩa:
> **Unit Test** là loại test kiểm tra **1 đơn vị nhỏ nhất** của code (thường là 1 method/function) một cách **độc lập**, **không phụ thuộc** vào database, network, file system, hay external services.

### Đặc điểm:
- ✅ **Isolated (Độc lập):** Không kết nối database, không gọi API
- ✅ **Fast (Nhanh):** Chạy trong vài milliseconds
- ✅ **Focused (Tập trung):** Test 1 function/method duy nhất
- ✅ **Mocking:** Sử dụng mocks/stubs cho dependencies

---

## 📁 UNIT TESTS HIỆN CÓ TRONG DỰ ÁN

### File: `tests/Unit/ExampleTest.php`

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
```

**Phân tích:**
- ✅ Đây là unit test cơ bản nhất
- ✅ Extend từ `PHPUnit\Framework\TestCase` (không phải `Tests\TestCase` của Laravel)
- ✅ Không sử dụng database, không boot Laravel application
- ✅ Chỉ test logic thuần túy

**Kết quả:**
```
✓ that true is true (0.01s)
Tests: 1 passed
```

---

## 🔄 SO SÁNH: UNIT TEST vs FEATURE TEST

### UNIT TEST

```php
// tests/Unit/CartCalculatorTest.php

use PHPUnit\Framework\TestCase;

class CartCalculatorTest extends TestCase
{
    public function test_calculate_total_with_single_item()
    {
        // Arrange
        $calculator = new CartCalculator();
        $items = [
            ['price' => 1000000, 'quantity' => 2]
        ];
        
        // Act
        $total = $calculator->calculateTotal($items);
        
        // Assert
        $this->assertEquals(2000000, $total);
    }
    
    public function test_calculate_total_with_discount()
    {
        $calculator = new CartCalculator();
        $items = [
            ['price' => 1000000, 'quantity' => 2]
        ];
        $discount = 10; // 10%
        
        $total = $calculator->calculateTotal($items, $discount);
        
        $this->assertEquals(1800000, $total);
    }
}
```

**Đặc điểm:**
- ❌ Không connect database
- ❌ Không gọi HTTP request
- ✅ Test logic tính toán thuần túy
- ✅ Chạy rất nhanh (< 10ms)

---

### FEATURE TEST (Integration Test)

```php
// tests/Feature/CartTest.php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_add_product_to_cart()
    {
        // Arrange: Create product in database
        $product = Product::factory()->create([
            'pro_name' => 'iPhone 15',
            'pro_price' => 20000000,
            'quantity' => 10
        ]);
        
        // Act: Send HTTP request
        $response = $this->post("/cart/add/{$product->id}", [
            'quantity' => 2
        ]);
        
        // Assert: Check HTTP response and session
        $response->assertStatus(200);
        $this->assertTrue(session()->has('cart'));
        $cart = session('cart');
        $this->assertEquals(2, $cart[$product->id]['quantity']);
    }
}
```

**Đặc điểm:**
- ✅ Connect database (RefreshDatabase)
- ✅ Gọi HTTP request (POST /cart/add)
- ✅ Test toàn bộ flow: Controller → Service → Repository → Database
- ⚠️ Chậm hơn unit test (300-500ms)

---

## 🎓 UNIT TEST NÊN TEST GÌ?

### 1. **Business Logic (Logic nghiệp vụ)**

```php
// app/Services/PriceCalculator.php
class PriceCalculator
{
    public function calculateFinalPrice(float $price, float $sale): float
    {
        if ($sale < 0 || $sale > 100) {
            throw new InvalidArgumentException('Sale must be between 0 and 100');
        }
        
        return $price - ($price * $sale / 100);
    }
}

// tests/Unit/PriceCalculatorTest.php
class PriceCalculatorTest extends TestCase
{
    public function test_calculate_final_price_with_valid_sale()
    {
        $calculator = new PriceCalculator();
        $finalPrice = $calculator->calculateFinalPrice(1000000, 10);
        $this->assertEquals(900000, $finalPrice);
    }
    
    public function test_calculate_final_price_throws_exception_for_invalid_sale()
    {
        $this->expectException(InvalidArgumentException::class);
        $calculator = new PriceCalculator();
        $calculator->calculateFinalPrice(1000000, 150);
    }
}
```

---

### 2. **Helper Functions/Utilities**

```php
// app/Helpers/StringHelper.php
class StringHelper
{
    public static function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text;
    }
}

// tests/Unit/StringHelperTest.php
class StringHelperTest extends TestCase
{
    public function test_slugify_converts_text_to_slug()
    {
        $slug = StringHelper::slugify('iPhone 15 Pro Max');
        $this->assertEquals('iphone-15-pro-max', $slug);
    }
    
    public function test_slugify_handles_special_characters()
    {
        $slug = StringHelper::slugify('Điện thoại @#$% 2024');
        $this->assertEquals('di-n-tho-i-2024', $slug);
    }
}
```

---

### 3. **Validation Rules**

```php
// app/Rules/ProductPriceRule.php
class ProductPriceRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return is_numeric($value) && $value > 0;
    }
    
    public function message(): string
    {
        return 'Product price must be greater than 0';
    }
}

// tests/Unit/ProductPriceRuleTest.php
class ProductPriceRuleTest extends TestCase
{
    public function test_passes_for_valid_price()
    {
        $rule = new ProductPriceRule();
        $this->assertTrue($rule->passes('price', 1000000));
    }
    
    public function test_fails_for_zero_price()
    {
        $rule = new ProductPriceRule();
        $this->assertFalse($rule->passes('price', 0));
    }
    
    public function test_fails_for_negative_price()
    {
        $rule = new ProductPriceRule();
        $this->assertFalse($rule->passes('price', -1000));
    }
}
```

---

### 4. **Data Transformation**

```php
// app/Transformers/ProductTransformer.php
class ProductTransformer
{
    public function transform(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->pro_name,
            'price' => $product->pro_price,
            'sale_price' => $this->calculateSalePrice($product),
            'in_stock' => $product->quantity > 0,
        ];
    }
    
    private function calculateSalePrice(Product $product): ?float
    {
        if ($product->pro_sale > 0) {
            return $product->pro_price - ($product->pro_price * $product->pro_sale / 100);
        }
        return null;
    }
}

// tests/Unit/ProductTransformerTest.php
class ProductTransformerTest extends TestCase
{
    public function test_transform_product_with_sale()
    {
        $product = Mockery::mock(Product::class);
        $product->id = 1;
        $product->pro_name = 'iPhone 15';
        $product->pro_price = 20000000;
        $product->pro_sale = 10;
        $product->quantity = 5;
        
        $transformer = new ProductTransformer();
        $result = $transformer->transform($product);
        
        $this->assertEquals([
            'id' => 1,
            'name' => 'iPhone 15',
            'price' => 20000000,
            'sale_price' => 18000000,
            'in_stock' => true,
        ], $result);
    }
}
```

---

## 📦 ĐỀ XUẤT UNIT TESTS CHO DỰ ÁN

### Nên tạo Unit Tests cho:

#### 1. **Cart Calculator** (Quan trọng)
```
tests/Unit/Services/CartCalculatorTest.php
- test_calculate_total_with_empty_cart
- test_calculate_total_with_single_item
- test_calculate_total_with_multiple_items
- test_calculate_total_with_discount
- test_calculate_total_with_shipping_fee
```

#### 2. **Price Calculator**
```
tests/Unit/Services/PriceCalculatorTest.php
- test_calculate_final_price_with_sale
- test_calculate_final_price_without_sale
- test_calculate_final_price_with_invalid_sale
- test_calculate_price_per_quantity
```

#### 3. **Product Validator**
```
tests/Unit/Validators/ProductValidatorTest.php
- test_validate_product_price_positive
- test_validate_product_price_zero_fails
- test_validate_product_name_required
- test_validate_product_category_exists
- test_validate_stock_quantity_non_negative
```

#### 4. **String Helper**
```
tests/Unit/Helpers/StringHelperTest.php
- test_slugify_converts_text
- test_slugify_handles_vietnamese
- test_slugify_handles_special_characters
- test_format_currency
- test_truncate_text
```

#### 5. **Payment Signature Generator**
```
tests/Unit/Services/PaymentSignatureTest.php
- test_generate_momo_signature
- test_verify_momo_signature_valid
- test_verify_momo_signature_invalid
- test_generate_vnpay_signature
```

---

## 🎯 BEST PRACTICES CHO UNIT TESTS

### 1. **Arrange-Act-Assert Pattern**
```php
public function test_example()
{
    // Arrange: Setup test data
    $calculator = new Calculator();
    $input = 10;
    
    // Act: Execute the code
    $result = $calculator->double($input);
    
    // Assert: Verify result
    $this->assertEquals(20, $result);
}
```

### 2. **Test One Thing**
```php
// ❌ BAD: Test multiple things
public function test_product()
{
    $this->assertTrue($product->isValid());
    $this->assertEquals(1000, $product->price);
    $this->assertEquals('iPhone', $product->name);
}

// ✅ GOOD: Separate tests
public function test_product_is_valid()
{
    $this->assertTrue($product->isValid());
}

public function test_product_has_correct_price()
{
    $this->assertEquals(1000, $product->price);
}
```

### 3. **Use Descriptive Names**
```php
// ❌ BAD
public function test_1()
public function test_product()

// ✅ GOOD
public function test_calculate_total_with_discount()
public function test_product_validation_fails_for_negative_price()
```

### 4. **Use Mocking cho Dependencies**
```php
public function test_order_service_creates_order()
{
    // Mock repository
    $repository = Mockery::mock(OrderRepository::class);
    $repository->shouldReceive('create')
               ->once()
               ->with(['total' => 1000])
               ->andReturn(new Order(['id' => 1]));
    
    // Test service với mocked repository
    $service = new OrderService($repository);
    $order = $service->createOrder(['total' => 1000]);
    
    $this->assertEquals(1, $order->id);
}
```

### 5. **Test Edge Cases**
```php
public function test_calculate_total_with_zero_quantity()
public function test_calculate_total_with_negative_price_throws_exception()
public function test_calculate_total_with_empty_cart_returns_zero()
public function test_calculate_total_with_max_integer_value()
```

---

## 🚀 CÁCH TẠO UNIT TEST MỚI

### Bước 1: Tạo file test
```bash
php artisan make:test Unit/Services/CartCalculatorTest --unit
```

### Bước 2: Viết test
```php
<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\CartCalculator;

class CartCalculatorTest extends TestCase
{
    private CartCalculator $calculator;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CartCalculator();
    }
    
    public function test_calculate_total_with_empty_cart()
    {
        $items = [];
        $total = $this->calculator->calculateTotal($items);
        $this->assertEquals(0, $total);
    }
    
    public function test_calculate_total_with_single_item()
    {
        $items = [
            ['price' => 1000000, 'quantity' => 2]
        ];
        
        $total = $this->calculator->calculateTotal($items);
        
        $this->assertEquals(2000000, $total);
    }
}
```

### Bước 3: Chạy test
```bash
php artisan test tests/Unit/Services/CartCalculatorTest.php
```

---

## 📊 PYRAMIND TESTING

```
       /\
      /  \     E2E Tests (ít)
     /____\    
    /      \   Integration/Feature Tests (vừa)
   /________\  
  /          \ Unit Tests (nhiều)
 /____________\
```

**Recommended ratio:**
- 70% Unit Tests (nhanh, nhiều)
- 20% Feature Tests (vừa)
- 10% E2E Tests (chậm, ít)

**Hiện tại dự án:**
- 2% Unit Tests (1/44)
- 98% Feature Tests (43/44)

**Đề xuất:** Cần bổ sung thêm Unit Tests!

---

## ✅ CHECKLIST TẠO UNIT TESTS

- [ ] CartCalculator (5 tests)
- [ ] PriceCalculator (5 tests)
- [ ] ProductValidator (5 tests)
- [ ] StringHelper (5 tests)
- [ ] PaymentSignature (5 tests)
- [ ] OrderValidator (5 tests)
- [ ] StockChecker (5 tests)
- [ ] DiscountCalculator (5 tests)

**Target:** 40+ Unit Tests (40% của tổng số tests)

---

## 📚 TÀI LIỆU THAM KHẢO

- PHPUnit Documentation: https://phpunit.de/
- Laravel Testing: https://laravel.com/docs/10.x/testing
- Test-Driven Development (TDD)
- SOLID Principles

---

**Ngày cập nhật:** 2026-01-28  
**Trạng thái:** Cần bổ sung thêm Unit Tests
