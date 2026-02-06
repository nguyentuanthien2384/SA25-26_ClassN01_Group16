# ⚡ LAB 05 - QUICK TEST GUIDE

**5-Minute Test Guide for Lab 05 Compliance**

---

## 🚀 QUICK START

### 1. Start Server
```bash
cd d:\Web_Ban_Do_Dien_Tu
php artisan serve
```

### 2. Run Quick Test Script (Windows)
```bash
.\test-lab05.bat
```

**Or follow manual tests below:**

---

## 🧪 4 REQUIRED TESTS (LAB 05)

### ✅ TEST 1: List Products

```bash
curl http://127.0.0.1:8000/api/products
```

**Expected:** 200 OK + JSON array of products

**Quick Check:**
- Status code: 200 ✅
- Contains products ✅

---

### ✅ TEST 2: Search Products

```bash
curl "http://127.0.0.1:8000/api/products?search=iPhone"
```

**Expected:** 200 OK + Filtered results

**Quick Check:**
- Status code: 200 ✅
- Only iPhone products ✅

---

### ✅ TEST 3: Product Details

```bash
curl http://127.0.0.1:8000/api/products/1
```

**Expected:** 200 OK + Single product object

**Quick Check:**
- Status code: 200 ✅
- Single product data ✅

---

### ✅ TEST 4: Not Found

```bash
curl http://127.0.0.1:8000/api/products/999
```

**Expected:** 404 Not Found

**Quick Check:**
- Status code: 404 ✅
- Error message ✅

---

## 🎁 BONUS: Automated Tests

```bash
php artisan test tests/Feature/Lab03ApiTest.php
```

**Expected:** 13/13 PASSED ✅

---

## 📊 RESULT

**Manual Tests:** __/4 passed  
**Status:** 
- 4/4 = ✅ **PASS LAB 05**
- 0-3/4 = ❌ **FAIL**

---

## 🎯 ONE-LINER TESTS

```bash
# All 4 tests in one go
curl http://127.0.0.1:8000/api/products && curl "http://127.0.0.1:8000/api/products?search=iPhone" && curl http://127.0.0.1:8000/api/products/1 && curl http://127.0.0.1:8000/api/products/999
```

---

**⏱️ Total Time: < 5 minutes**  
**✅ Result: LAB 05 COMPLIANCE VERIFIED!**
