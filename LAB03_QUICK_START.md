# Lab 03 - Quick Start Guide

## 🚀 Cách Chạy Lab 03

### Bước 1: Clear Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Bước 2: Start Server

```bash
php artisan serve
```

Server sẽ chạy tại: `http://localhost:8000`

---

## 📝 Test API Endpoints

### 1. Health Check

```bash
curl http://localhost:8000/api/lab03/health
```

**Expected:**
```json
{
  "status": "OK",
  "message": "Lab 03 API is running"
}
```

---

### 2. Create Product (201 Created)

**Windows PowerShell:**
```powershell
curl -X POST http://localhost:8000/api/lab03/products `
  -H "Content-Type: application/json" `
  -H "Accept: application/json" `
  -d '{\"pro_name\":\"Samsung Galaxy S24\",\"pro_price\":25000000,\"pro_category_id\":1,\"quantity\":10}'
```

**Linux/Mac:**
```bash
curl -X POST http://localhost:8000/api/lab03/products \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"pro_name":"Samsung Galaxy S24","pro_price":25000000,"pro_category_id":1,"quantity":10}'
```

**Expected Response:** `201 Created`

---

### 3. Create Product with Error (400 Bad Request)

**Windows PowerShell:**
```powershell
curl -X POST http://localhost:8000/api/lab03/products `
  -H "Content-Type: application/json" `
  -d '{\"pro_name\":\"\",\"pro_price\":-100}'
```

**Expected Response:** `400 Bad Request` with validation errors

---

### 4. Get Product by ID

```bash
# Replace {id} with actual product ID
curl http://localhost:8000/api/lab03/products/1
```

**Expected Response:** `200 OK` or `404 Not Found`

---

### 5. Get All Products

```bash
curl http://localhost:8000/api/lab03/products
```

**Expected Response:** `200 OK` with paginated data

---

### 6. Search Products

```bash
curl "http://localhost:8000/api/lab03/products/search?q=samsung"
```

---

### 7. Update Product

**Windows PowerShell:**
```powershell
curl -X PUT http://localhost:8000/api/lab03/products/1 `
  -H "Content-Type: application/json" `
  -d '{\"pro_name\":\"Updated Name\",\"pro_price\":30000000}'
```

---

### 8. Delete Product

```bash
curl -X DELETE http://localhost:8000/api/lab03/products/1
```

---

## 📊 View Diagrams

### Sequence Diagram
```bash
# Open in PlantUML viewer
Design/Lab03_Sequence_CRUD.puml
```

### Component Diagram
```bash
# Open in PlantUML viewer
Design/Lab03_Component_Diagram.puml
```

**Online PlantUML Viewer:**
- http://www.plantuml.com/plantuml/uml/
- Copy nội dung file .puml và paste vào để render

---

## 📂 File Structure

```
app/Lab03/
├── Controllers/
│   └── ProductController.php          # API endpoints
├── Services/
│   └── ProductService.php             # Business logic
├── Repositories/
│   ├── ProductRepositoryInterface.php # Contract
│   └── ProductRepository.php          # Implementation
├── Providers/
│   └── Lab03ServiceProvider.php       # DI bindings
└── routes.php                         # API routes

Design/
├── Lab03_Sequence_CRUD.puml          # Sequence diagram
└── Lab03_Component_Diagram.puml      # Component diagram

LAB03_REPORT.md                        # Full report
LAB03_QUICK_START.md                  # This file
```

---

## ✅ Checklist

- [x] Service Provider registered in `config/app.php`
- [x] Routes loaded via Service Provider
- [x] Repository interface bound to implementation
- [x] All CRUD endpoints working
- [x] Validation rules applied
- [x] Business rules implemented
- [x] Diagrams created
- [x] Report completed

---

## 🎯 Learning Objectives Achieved

1. ✅ **3-Layer Architecture**: Controller → Service → Repository
2. ✅ **Dependency Injection**: Interface-based programming
3. ✅ **CRUD Operations**: Create, Read, Update, Delete
4. ✅ **Business Logic**: Validation, rules, transformation
5. ✅ **RESTful API**: Proper HTTP methods and status codes
6. ✅ **Documentation**: Sequence + Component diagrams

---

## 🐛 Troubleshooting

### Error: "Route [lab03.products.index] not defined"

**Solution:**
```bash
php artisan config:clear
php artisan route:clear
php artisan route:list | grep lab03
```

### Error: "Class ProductRepositoryInterface not found"

**Solution:**
```bash
composer dump-autoload
php artisan config:clear
```

### Error: "SQLSTATE[42S02]: Base table or directory not found: 1146 Table 'duan.products' doesn't exist"

**Solution:**
```bash
# Make sure you have products table in your database
# Or update ProductRepository to use correct table name
```

---

## 📞 Support

Nếu gặp vấn đề, check:
1. Service Provider đã register chưa (`config/app.php`)
2. Routes có load đúng không (`php artisan route:list`)
3. Database có data không (check via phpMyAdmin)
4. Server đã start chưa (`php artisan serve`)

---

**End of Quick Start Guide**
