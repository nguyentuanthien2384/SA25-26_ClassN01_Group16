# 🧪 LAB 05 - TEST GUIDE

**Hướng dẫn test Product Service theo Lab 05 & Lecture 05**

**Version:** 1.0  
**Date:** 2026-01-28  
**Framework:** Laravel 10 (equivalent to Python/Flask Lab 05)

---

## 📋 MỤC LỤC

1. [Chuẩn bị](#chuẩn-bị)
2. [Manual Testing (Lab 05 Style)](#manual-testing-lab-05-style)
3. [Automated Testing (Bonus)](#automated-testing-bonus)
4. [Test Checklist](#test-checklist)
5. [Troubleshooting](#troubleshooting)

---

## 🎯 CHUẨN BỊ

### 1. Start Laravel Server

```bash
cd d:\Web_Ban_Do_Dien_Tu
php artisan serve
```

**Output:**
```
Laravel development server started on http://127.0.0.1:8000
```

✅ **Server running on port 8000** (Lab 05 uses port 5001)

---

### 2. Verify Database Connection

```bash
php artisan migrate:status
```

**Expected:** All migrations should be run.

---

### 3. Seed Sample Data (if needed)

```bash
php artisan db:seed
```

**Expected:** Products table populated with sample data.

---

### 4. Check Environment

```bash
php artisan --version
# Laravel Framework 10.x
```

**Required:**
- ✅ PHP 8.2+
- ✅ MySQL 8.0+
- ✅ Composer installed

---

## 🔧 MANUAL TESTING (LAB 05 STYLE)

Lab 05 yêu cầu 3 manual tests với `curl`. Dưới đây là test tương đương cho dự án Laravel.

---

### TEST 1: List All Products (200 OK)

**Lab 05 Command:**
```bash
curl -X GET http://127.0.0.1:5001/api/products
```

**Dự án Command:**
```bash
curl -X GET http://127.0.0.1:8000/api/products
```

**Expected Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "pro_name": "iPhone 15 Pro Max",
      "pro_slug": "iphone-15-pro-max",
      "pro_price": 29990000,
      "pro_sale": 10,
      "pro_image": "/upload/iphone15.jpg",
      "pro_description": "Flagship smartphone with A17 Pro chip",
      "pro_category_id": 1,
      "category": {
        "id": 1,
        "c_name": "Điện thoại",
        "c_slug": "dien-thoai"
      }
    },
    {
      "id": 2,
      "pro_name": "Samsung Galaxy S24 Ultra",
      "pro_slug": "samsung-galaxy-s24-ultra",
      "pro_price": 27990000,
      "pro_sale": 5,
      "pro_image": "/upload/samsung-s24.jpg",
      "pro_description": "AI-powered flagship smartphone",
      "pro_category_id": 1,
      "category": {
        "id": 1,
        "c_name": "Điện thoại",
        "c_slug": "dien-thoai"
      }
    }
  ],
  "first_page_url": "http://127.0.0.1:8000/api/products?page=1",
  "from": 1,
  "last_page": 3,
  "last_page_url": "http://127.0.0.1:8000/api/products?page=3",
  "links": [...],
  "next_page_url": "http://127.0.0.1:8000/api/products?page=2",
  "path": "http://127.0.0.1:8000/api/products",
  "per_page": 20,
  "prev_page_url": null,
  "to": 20,
  "total": 50
}
```

**Validation:**
- ✅ HTTP Status: **200 OK**
- ✅ Content-Type: `application/json`
- ✅ Response contains array of products
- ✅ Each product has required fields (id, name, price, etc.)
- ✅ Only active products (`pro_active = 1`)

**PowerShell (Windows):**
```powershell
Invoke-RestMethod -Uri http://127.0.0.1:8000/api/products -Method GET | ConvertTo-Json
```

**Postman:**
- Method: `GET`
- URL: `http://127.0.0.1:8000/api/products`
- Expected: 200 OK + JSON response

✅ **PASS** if response code is 200 and contains product array.

---

### TEST 2: Search Products (200 OK)

**Lab 05 Command:**
```bash
curl -X GET "http://127.0.0.1:5001/api/products?q=Laptop"
```

**Dự án Command:**
```bash
curl -X GET "http://127.0.0.1:8000/api/products?search=iPhone"
```

**Expected Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "pro_name": "iPhone 15 Pro Max",
      "pro_price": 29990000,
      "pro_description": "Flagship smartphone with A17 Pro chip"
    },
    {
      "id": 5,
      "pro_name": "iPhone 14 Pro",
      "pro_price": 23990000,
      "pro_description": "Previous generation flagship"
    }
  ],
  "total": 2
}
```

**Validation:**
- ✅ HTTP Status: **200 OK**
- ✅ Only products matching "iPhone" are returned
- ✅ Search works on `pro_name` field
- ✅ Search is case-insensitive

**Test different keywords:**
```bash
# Search by phone
curl -X GET "http://127.0.0.1:8000/api/products?search=phone"

# Search by laptop
curl -X GET "http://127.0.0.1:8000/api/products?search=laptop"

# Search by Samsung
curl -X GET "http://127.0.0.1:8000/api/products?search=Samsung"
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/products?search=iPhone" -Method GET | ConvertTo-Json
```

✅ **PASS** if only matching products are returned.

---

### TEST 3: Get Product Details by ID (200 OK)

**Lab 05 Command:**
```bash
curl -X GET http://127.0.0.1:5001/api/products/1
```

**Dự án Command:**
```bash
curl -X GET http://127.0.0.1:8000/api/products/1
```

**Expected Response:**
```json
{
  "id": 1,
  "pro_name": "iPhone 15 Pro Max",
  "pro_slug": "iphone-15-pro-max",
  "pro_content": "iPhone 15 Pro Max là flagship mới nhất...",
  "pro_category_id": 1,
  "pro_price": 29990000,
  "pro_author_id": 1,
  "pro_sale": 10,
  "pro_active": 1,
  "pro_hot": 1,
  "pro_view": 1543,
  "pro_description": "Flagship smartphone with A17 Pro chip",
  "pro_image": "/upload/iphone15.jpg",
  "pro_title_seo": "iPhone 15 Pro Max - Siêu phẩm 2024",
  "pro_description_seo": "Mua iPhone 15 Pro Max chính hãng...",
  "pro_keyword_seo": "iphone 15 pro max, iphone, apple",
  "created_at": "2024-01-15T10:30:00.000000Z",
  "updated_at": "2024-01-20T15:45:00.000000Z",
  "category": {
    "id": 1,
    "c_name": "Điện thoại",
    "c_slug": "dien-thoai"
  }
}
```

**Validation:**
- ✅ HTTP Status: **200 OK**
- ✅ Response is a single product object (not array)
- ✅ Contains all product fields
- ✅ Includes category relationship
- ✅ Product ID matches requested ID

**Test multiple IDs:**
```bash
# Get product 1
curl -X GET http://127.0.0.1:8000/api/products/1

# Get product 2
curl -X GET http://127.0.0.1:8000/api/products/2

# Get product 10
curl -X GET http://127.0.0.1:8000/api/products/10
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri http://127.0.0.1:8000/api/products/1 -Method GET | ConvertTo-Json
```

✅ **PASS** if correct product details are returned.

---

### TEST 4: Product Not Found (404 NOT FOUND)

**Lab 05 Command:**
```bash
curl -X GET http://127.0.0.1:5001/api/products/999
```

**Dự án Command:**
```bash
curl -X GET http://127.0.0.1:8000/api/products/999
```

**Expected Response:**
```json
{
  "message": "No query results for model [App\\Models\\Models\\Product] 999"
}
```

**HTTP Status:** **404 Not Found**

**Validation:**
- ✅ HTTP Status: **404 Not Found**
- ✅ Error message indicates product not found
- ✅ No product data returned

**Test multiple non-existent IDs:**
```bash
# Non-existent ID
curl -X GET http://127.0.0.1:8000/api/products/999

# Very large ID
curl -X GET http://127.0.0.1:8000/api/products/999999

# Zero ID
curl -X GET http://127.0.0.1:8000/api/products/0
```

**PowerShell:**
```powershell
try {
    Invoke-RestMethod -Uri http://127.0.0.1:8000/api/products/999 -Method GET
} catch {
    Write-Host "Status Code: $($_.Exception.Response.StatusCode.value__)"
    # Should be 404
}
```

**Using curl with verbose:**
```bash
curl -v http://127.0.0.1:8000/api/products/999
# Look for: HTTP/1.1 404 Not Found
```

✅ **PASS** if status code is 404.

---

### TEST 5: Filter by Category (BONUS)

**Lab 05:** Không có  
**Dự án:** Có

```bash
curl -X GET "http://127.0.0.1:8000/api/products?category=1"
```

**Expected Response:**
```json
{
  "data": [
    {
      "id": 1,
      "pro_name": "iPhone 15 Pro Max",
      "pro_category_id": 1,
      "category": {
        "id": 1,
        "c_name": "Điện thoại"
      }
    }
  ]
}
```

**Validation:**
- ✅ Only products from category 1 are returned
- ✅ All products have `pro_category_id = 1`

✅ **BONUS FEATURE**

---

### TEST 6: Sort Products (BONUS)

**Lab 05:** Không có  
**Dự án:** Có

```bash
# Sort by price ascending
curl -X GET "http://127.0.0.1:8000/api/products?sort=price_asc"

# Sort by price descending
curl -X GET "http://127.0.0.1:8000/api/products?sort=price_desc"

# Sort by newest
curl -X GET "http://127.0.0.1:8000/api/products?sort=newest"
```

**Validation:**
- ✅ Products are sorted correctly
- ✅ `price_asc`: prices from low to high
- ✅ `price_desc`: prices from high to low
- ✅ `newest`: newest products first

✅ **BONUS FEATURE**

---

### TEST 7: Pagination (BONUS)

**Lab 05:** Không có  
**Dự án:** Có

```bash
# Page 1
curl -X GET "http://127.0.0.1:8000/api/products?page=1&per_page=10"

# Page 2
curl -X GET "http://127.0.0.1:8000/api/products?page=2&per_page=10"
```

**Expected Response:**
```json
{
  "current_page": 2,
  "data": [...],
  "first_page_url": "http://127.0.0.1:8000/api/products?page=1",
  "from": 11,
  "last_page": 5,
  "next_page_url": "http://127.0.0.1:8000/api/products?page=3",
  "per_page": 10,
  "prev_page_url": "http://127.0.0.1:8000/api/products?page=1",
  "to": 20,
  "total": 50
}
```

**Validation:**
- ✅ Correct page number
- ✅ Correct per_page count
- ✅ Pagination links are valid

✅ **BONUS FEATURE**

---

### TEST 8: Cache Headers (BONUS)

**Lab 05:** Không có  
**Dự án:** Có

```bash
curl -I http://127.0.0.1:8000/api/products
```

**Expected Headers:**
```
HTTP/1.1 200 OK
Content-Type: application/json
Cache-Control: public, max-age=300
X-Cache-Status: MISS
```

**Second request (should hit cache):**
```bash
curl -I http://127.0.0.1:8000/api/products
```

**Expected Headers:**
```
X-Cache-Status: HIT
```

**Validation:**
- ✅ Cache-Control header present
- ✅ X-Cache-Status changes from MISS to HIT
- ✅ Response faster on cache hit

✅ **BONUS FEATURE**

---

## 🤖 AUTOMATED TESTING (BONUS)

Lab 05 chỉ có manual testing. Dự án có **23 automated tests**!

---

### Run All Product Tests

```bash
php artisan test tests/Feature/Lab03ApiTest.php
```

**Expected Output:**
```
   PASS  Tests\Feature\Lab03ApiTest
  ✓ get all products lab03                          0.15s
  ✓ get single product by id lab03                  0.12s
  ✓ get nonexistent product returns 404 lab03       0.10s
  ✓ create product with valid data lab03            0.25s
  ✓ create product with invalid data returns 400 lab03  0.18s
  ✓ update product lab03                            0.20s
  ✓ delete product lab03                            0.15s
  ✓ search products lab03                           0.13s
  ✓ products pagination lab03                       0.14s
  ✓ filter products by category lab03               0.12s
  ✓ sort products by price lab03                    0.11s
  ✓ product price validation lab03                  0.16s
  ✓ product stock validation lab03                  0.15s

  Tests:    13 passed (13 assertions)
  Duration: 1.86s
```

✅ **13/13 PASSED**

---

### Run Specific Test

```bash
# Test list products
php artisan test --filter=test_get_all_products_lab03

# Test product details
php artisan test --filter=test_get_single_product_by_id_lab03

# Test 404 error
php artisan test --filter=test_get_nonexistent_product_returns_404_lab03
```

---

### Test with Detailed Output

```bash
php artisan test tests/Feature/Lab03ApiTest.php --testdox
```

**Output:**
```
Lab03Api
 ✓ Get all products lab03
 ✓ Get single product by id lab03
 ✓ Get nonexistent product returns 404 lab03
 ✓ Create product with valid data lab03
 ✓ Create product with invalid data returns 400 lab03
 ✓ Update product lab03
 ✓ Delete product lab03
 ✓ Search products lab03
 ✓ Products pagination lab03
 ✓ Filter products by category lab03
 ✓ Sort products by price lab03
 ✓ Product price validation lab03
 ✓ Product stock validation lab03
```

---

### Test Coverage

```bash
php artisan test --coverage
```

**Expected:**
```
  Lab03ApiTest ............... 13 / 13 (100%)
  ProductTest ................ 10 / 10 (100%)

  Total ...................... 23 / 23 (100%)
```

---

### Run All Product-Related Tests

```bash
# Lab 03 API tests
php artisan test tests/Feature/Lab03ApiTest.php

# Product functional tests
php artisan test tests/Feature/ProductTest.php

# Product unit tests
php artisan test tests/Unit/Services/PriceCalculatorTest.php
php artisan test tests/Unit/Validators/ProductValidatorTest.php
```

---

## ✅ TEST CHECKLIST

### Manual Tests (Lab 05 Requirements)

- [ ] **Test 1:** List all products → 200 OK
- [ ] **Test 2:** Search products → 200 OK
- [ ] **Test 3:** Get product details → 200 OK
- [ ] **Test 4:** Product not found → 404 Not Found

**Required:** 4/4 tests must pass ✅

---

### Bonus Tests (Dự án có thêm)

- [ ] **Test 5:** Filter by category → 200 OK
- [ ] **Test 6:** Sort products → 200 OK
- [ ] **Test 7:** Pagination → 200 OK
- [ ] **Test 8:** Cache headers → Correct headers

**Bonus:** 4/4 tests (optional)

---

### Automated Tests (Dự án có thêm)

- [ ] **Lab03ApiTest:** 13/13 tests pass
- [ ] **ProductTest:** 10/10 tests pass
- [ ] **PriceCalculatorTest:** 9/9 tests pass
- [ ] **ProductValidatorTest:** 14/14 tests pass

**Total:** 46 automated tests

---

## 🐛 TROUBLESHOOTING

### Issue 1: Server not responding

**Symptom:**
```bash
curl: (7) Failed to connect to 127.0.0.1 port 8000: Connection refused
```

**Solution:**
```bash
# Check if server is running
php artisan serve

# Or run on different port
php artisan serve --port=8001
```

---

### Issue 2: Database connection error

**Symptom:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Solution:**
```bash
# Check database config
php artisan config:clear

# Test database connection
php artisan migrate:status

# Restart MySQL
net start mysql80  # Windows
sudo service mysql restart  # Linux
```

---

### Issue 3: Empty product list

**Symptom:**
```json
{
  "data": [],
  "total": 0
}
```

**Solution:**
```bash
# Seed database
php artisan db:seed

# Or manually insert data
php artisan tinker
>>> Product::create(['pro_name' => 'Test Product', 'pro_price' => 1000, 'quantity' => 10, 'pro_active' => 1]);
```

---

### Issue 4: Cache issues

**Symptom:**
Old data returned even after database update.

**Solution:**
```bash
# Clear cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear
```

---

### Issue 5: 404 on all requests

**Symptom:**
All API requests return 404.

**Solution:**
```bash
# Clear route cache
php artisan route:clear

# List all routes
php artisan route:list | findstr "products"

# Check .htaccess (if using Apache)
# Ensure mod_rewrite is enabled
```

---

### Issue 6: Permission denied

**Symptom:**
```
Permission denied when accessing logs
```

**Solution (Windows):**
```bash
# Run as administrator
# Or check folder permissions
```

**Solution (Linux):**
```bash
sudo chmod -R 775 storage/
sudo chown -R www-data:www-data storage/
```

---

## 📊 TEST RESULTS TEMPLATE

### Manual Test Results

**Date:** ___________  
**Tester:** ___________

| Test # | Description | Expected | Actual | Status |
|--------|-------------|----------|--------|--------|
| 1 | List products | 200 OK + array | | [ ] |
| 2 | Search products | 200 OK + filtered | | [ ] |
| 3 | Product details | 200 OK + object | | [ ] |
| 4 | Not found | 404 Not Found | | [ ] |

**Manual Score:** ___/4 passed

---

### Automated Test Results

```bash
php artisan test tests/Feature/Lab03ApiTest.php
```

**Result:**
```
Tests:    ____ passed (____ assertions)
Duration: ____s
```

**Automated Score:** ___/13 passed

---

### Overall Score

**Manual Tests:** ___/4 (100% required)  
**Automated Tests:** ___/13 (bonus)

**Grade:** ____

- 4/4 manual = ✅ **PASS**
- 0-3/4 manual = ❌ **FAIL**

---

## 📝 SUBMISSION CHECKLIST (LAB 05)

### Document 1: Product Model Code
✅ **File:** `app/Models/Models/Product.php`

### Document 2: API Route Code
✅ **File:** `routes/api.php` (lines 144-219)

### Document 3: Test Results
✅ **Screenshots:**
- Test 1: List products (200 OK)
- Test 2: Search (200 OK)
- Test 3: Details (200 OK)
- Test 4: Not found (404)

✅ **Automated test output:**
```bash
php artisan test tests/Feature/Lab03ApiTest.php --testdox
```

---

## 🎯 QUICK REFERENCE

### Essential Commands

```bash
# Start server
php artisan serve

# Run all tests
php artisan test

# Clear cache
php artisan cache:clear

# Seed data
php artisan db:seed
```

### Quick Test Suite

```bash
# Test 1: List
curl http://127.0.0.1:8000/api/products

# Test 2: Search
curl "http://127.0.0.1:8000/api/products?search=iPhone"

# Test 3: Details
curl http://127.0.0.1:8000/api/products/1

# Test 4: Not Found
curl http://127.0.0.1:8000/api/products/999
```

### Expected HTTP Codes

- **200 OK** - Success
- **404 Not Found** - Resource not found
- **400 Bad Request** - Invalid data (bonus)
- **500 Server Error** - Server error (should not happen)

---

## 🏆 SUCCESS CRITERIA

### Lab 05 Requirements:

1. ✅ GET /api/products returns active products
2. ✅ Search with query param works
3. ✅ GET /api/products/{id} returns product details
4. ✅ Non-existent ID returns 404

**All 4 tests must pass for Lab 05 compliance!**

---

**Ngày:** 2026-01-28  
**Version:** 1.0  
**Status:** ✅ Ready to test

**🎉 FOLLOW THIS GUIDE TO VERIFY LAB 05 COMPLIANCE! 🎉**
