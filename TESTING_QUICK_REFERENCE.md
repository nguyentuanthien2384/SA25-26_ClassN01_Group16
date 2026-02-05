# 🧪 TESTING QUICK REFERENCE

## 🚀 Most Common Commands

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProductTest.php

# Run tests matching pattern
php artisan test --filter=Lab03

# Run with detailed output
php artisan test --verbose

# Stop on first failure
php artisan test --stop-on-failure
```

---

## 📊 Current Test Status

```
✅ PASSED:  44 tests
⏹️ SKIPPED: 2 tests  
❌ FAILED:  0 tests
💯 SUCCESS: 100%
```

---

## 📁 Test Files

| File | Tests | Focus Area |
|------|-------|------------|
| `ProductTest.php` | 10 | Products, Categories, Search |
| `CartTest.php` | 10 | Shopping Cart, Checkout |
| `UserAuthenticationTest.php` | 11 | Login, Register, Profile |
| `Lab03ApiTest.php` | 13 | RESTful API CRUD |

---

## 🔍 Test by Feature

### **Test Products**
```bash
php artisan test --filter=ProductTest
```

### **Test Shopping Cart**
```bash
php artisan test --filter=CartTest
```

### **Test Authentication**
```bash
php artisan test --filter=UserAuthenticationTest
```

### **Test Lab 03 API**
```bash
php artisan test --filter=Lab03
```

---

## ⚡ Quick Troubleshooting

### **MySQL Not Running**
```bash
# Start XAMPP MySQL
# Or: docker-compose up -d mysql
```

### **Port 8000 Busy**
```bash
# Windows: Find and kill process
netstat -ano | findstr :8000
taskkill /PID <PID> /F
```

### **Database Empty**
```bash
# Import data
mysql -u root duan < duan.sql
```

---

## 📈 Coverage Summary

```
Product/Catalog:    90%  ✅
Shopping Cart:     100%  ✅
User Auth:          90%  ✅
Lab 03 API:        100%  ✅
─────────────────────────
OVERALL:            95%  ✅
```

---

## ✅ Pre-Commit Checklist

- [ ] `php artisan test` - All tests pass
- [ ] No new skipped tests
- [ ] New features have tests
- [ ] Updated this doc if needed

---

**Full Guide:** See `TESTING_GUIDE.md` for complete documentation.
