# Lab 03 - Hướng Dẫn Chạy Trên CMD (Windows)

## 📌 YÊU CẦU

- ✅ XAMPP đã cài đặt (Apache + MySQL)
- ✅ PHP version ≥ 8.0
- ✅ Composer đã cài đặt
- ✅ Database `duan` đã tồn tại

---

## 🚀 BƯỚC 1: MỞ CMD VÀ CHUYỂN VÀO THỦ MỤC DỰ ÁN

### 1. Mở Command Prompt (CMD)

**Cách 1:** Nhấn `Win + R` → gõ `cmd` → Enter

**Cách 2:** Search "Command Prompt" trong Start Menu

### 2. Chuyển vào thư mục dự án

```cmd
cd /d D:\Web_Ban_Do_Dien_Tu
```

**Kiểm tra đã vào đúng thư mục:**

```cmd
dir
```

Bạn sẽ thấy: `artisan`, `composer.json`, `app`, `config`, etc.

---

## 🧹 BƯỚC 2: CLEAR CACHE

Chạy lần lượt các lệnh sau:

```cmd
php artisan config:clear
```

```cmd
php artisan route:clear
```

```cmd
php artisan cache:clear
```

**Kết quả mong đợi:**

```
Configuration cache cleared successfully.
Route cache cleared successfully.
Application cache cleared successfully.
```

---

## 🔍 BƯỚC 3: KIỂM TRA ROUTES

```cmd
php artisan route:list | findstr "lab03"
```

**Kết quả mong đợi:** (nếu setup đúng)

```
GET|HEAD  api/lab03/health ........................... lab03.health
GET|HEAD  api/lab03/products .................. lab03.products.index
POST      api/lab03/products .................. lab03.products.store
GET|HEAD  api/lab03/products/search ........... lab03.products.search
GET|HEAD  api/lab03/products/{id} ............. lab03.products.show
PUT       api/lab03/products/{id} ............. lab03.products.update
DELETE    api/lab03/products/{id} ............. lab03.products.destroy
```

**❌ Nếu KHÔNG thấy routes:**

```cmd
composer dump-autoload
php artisan config:clear
php artisan route:list | findstr "lab03"
```

---

## 🌐 BƯỚC 4: START LARAVEL SERVER

### Option A: Dùng Laravel Built-in Server (Đơn giản)

```cmd
php artisan serve
```

**Kết quả:**

```
Starting Laravel development server: http://127.0.0.1:8000
[Press Ctrl+C to quit]
```

Server đang chạy tại: **http://localhost:8000**

---

### Option B: Dùng XAMPP Apache (Nếu bạn đã config vhost)

1. **Start XAMPP Apache và MySQL**

2. **Truy cập:**
   - Nếu có virtual host: `http://electroshop.local`
   - Nếu không: `http://localhost/Web_Ban_Do_Dien_Tu/public`

---

## ✅ BƯỚC 5: TEST API - HEALTH CHECK

**Mở CMD MỚI (giữ server chạy ở CMD cũ)**

### 1. Mở CMD thứ 2

Nhấn `Win + R` → gõ `cmd` → Enter

### 2. Test health check

```cmd
curl http://localhost:8000/api/lab03/health
```

**Kết quả mong đợi:**

```json
{"status":"OK","message":"Lab 03 API is running","timestamp":"2026-01-28T10:30:00+00:00"}
```

---

## 📝 BƯỚC 6: TEST API - CREATE PRODUCT (201 Created)

### Test 1: Create Product Thành Công

**Lệnh CMD:**

```cmd
curl -X POST http://localhost:8000/api/lab03/products -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"pro_name\":\"Samsung Galaxy S24\",\"pro_price\":25000000,\"pro_category_id\":1,\"quantity\":10}"
```

**Kết quả mong đợi:** `201 Created`

```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 1,
    "name": "Samsung Galaxy S24",
    "slug": "samsung-galaxy-s24-1738051200",
    "price": 25000000,
    "sale": 0,
    "final_price": 25000000,
    "category_id": 1,
    "stock": 10,
    "is_active": true,
    "is_hot": true,
    "created_at": "2026-01-28 10:30:00"
  }
}
```

**📸 Chụp màn hình này để nộp bài!**

---

## ❌ BƯỚC 7: TEST API - CREATE PRODUCT LỖI (400 Bad Request)

### Test 2: Create Product Với Validation Error

**Lệnh CMD (price âm, name rỗng):**

```cmd
curl -X POST http://localhost:8000/api/lab03/products -H "Content-Type: application/json" -d "{\"pro_name\":\"\",\"pro_price\":-100}"
```

**Kết quả mong đợi:** `400 Bad Request`

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "pro_name": ["Product name is required"],
    "pro_price": ["Product price must be greater than or equal to 0"],
    "pro_category_id": ["Category is required"]
  }
}
```

**📸 Chụp màn hình này để nộp bài!**

---

## 📖 BƯỚC 8: TEST API - GET PRODUCT BY ID

### Test 3: Get Product Tồn Tại (200 OK)

**Lệnh CMD:**

```cmd
curl http://localhost:8000/api/lab03/products/1
```

**Kết quả mong đợi:** `200 OK` với thông tin product

---

### Test 4: Get Product KHÔNG Tồn Tại (404 Not Found)

**Lệnh CMD:**

```cmd
curl http://localhost:8000/api/lab03/products/99999
```

**Kết quả mong đợi:** `404 Not Found`

```json
{
  "success": false,
  "message": "Product with ID 99999 not found",
  "error": {
    "code": 404,
    "description": "Not Found - Resource does not exist"
  }
}
```

**📸 Chụp màn hình này để nộp bài!**

---

## 📋 BƯỚC 9: TEST API - LIST ALL PRODUCTS

**Lệnh CMD:**

```cmd
curl http://localhost:8000/api/lab03/products
```

**Kết quả:** `200 OK` với danh sách products (có pagination)

---

## 🔍 BƯỚC 10: TEST API - SEARCH PRODUCTS

**Lệnh CMD:**

```cmd
curl "http://localhost:8000/api/lab03/products/search?q=samsung"
```

**Kết quả:** `200 OK` với kết quả tìm kiếm

---

## ✏️ BƯỚC 11: TEST API - UPDATE PRODUCT

**Lệnh CMD:**

```cmd
curl -X PUT http://localhost:8000/api/lab03/products/1 -H "Content-Type: application/json" -d "{\"pro_name\":\"Samsung Galaxy S24 Ultra\",\"pro_price\":29990000}"
```

**Kết quả mong đợi:** `200 OK` với data đã update

---

## 🗑️ BƯỚC 12: TEST API - DELETE PRODUCT

**Lệnh CMD:**

```cmd
curl -X DELETE http://localhost:8000/api/lab03/products/1
```

**Kết quả mong đợi:** `200 OK`

```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

---

## 📊 TỔNG HỢP CÁC LỆNH CURL QUAN TRỌNG

### ✅ 1. Health Check

```cmd
curl http://localhost:8000/api/lab03/health
```

---

### ✅ 2. Create Product (201)

```cmd
curl -X POST http://localhost:8000/api/lab03/products -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"pro_name\":\"Samsung Galaxy S24\",\"pro_price\":25000000,\"pro_category_id\":1,\"quantity\":10}"
```

---

### ✅ 3. Create Product Error (400)

```cmd
curl -X POST http://localhost:8000/api/lab03/products -H "Content-Type: application/json" -d "{\"pro_name\":\"\",\"pro_price\":-100}"
```

---

### ✅ 4. Get Product Not Found (404)

```cmd
curl http://localhost:8000/api/lab03/products/99999
```

---

### ✅ 5. List All Products (200)

```cmd
curl http://localhost:8000/api/lab03/products
```

---

### ✅ 6. Search Products (200)

```cmd
curl "http://localhost:8000/api/lab03/products/search?q=samsung"
```

---

## 🐛 XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi 1: "php is not recognized"

**Nguyên nhân:** PHP chưa được add vào PATH

**Giải pháp:**

```cmd
set PATH=%PATH%;C:\xampp\php
```

Hoặc dùng đường dẫn đầy đủ:

```cmd
C:\xampp\php\php.exe artisan serve
```

---

### Lỗi 2: "curl is not recognized"

**Nguyên nhân:** Windows 10/11 cũ chưa có curl

**Giải pháp 1:** Update Windows

**Giải pháp 2:** Dùng Postman hoặc browser

**Giải pháp 3:** Download curl từ https://curl.se/windows/

---

### Lỗi 3: "Route [lab03.products.index] not defined"

**Nguyên nhân:** Routes chưa load

**Giải pháp:**

```cmd
composer dump-autoload
php artisan config:clear
php artisan route:clear
```

Kiểm tra lại:

```cmd
php artisan route:list | findstr "lab03"
```

---

### Lỗi 4: "SQLSTATE[HY000] [2002] No connection could be made"

**Nguyên nhân:** MySQL chưa chạy hoặc config sai

**Giải pháp:**

1. **Mở XAMPP Control Panel**
2. **Start MySQL**
3. **Kiểm tra `.env`:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=
```

4. **Clear config:**

```cmd
php artisan config:clear
```

---

### Lỗi 5: "Class 'App\Lab03\Providers\Lab03ServiceProvider' not found"

**Nguyên nhân:** Autoload chưa update

**Giải pháp:**

```cmd
composer dump-autoload
php artisan config:clear
```

---

### Lỗi 6: "Base table or view not found: 1146 Table 'duan.products' doesn't exist"

**Nguyên nhân:** Table products chưa tồn tại

**Giải pháp:**

**Option 1:** Run migration (nếu có)

```cmd
php artisan migrate
```

**Option 2:** Import SQL thủ công qua phpMyAdmin

1. Mở http://localhost/phpmyadmin
2. Chọn database `duan`
3. Import file SQL

---

## 📸 CHỤP MÀN HÌNH ĐỂ NỘP BÀI

Bạn cần chụp **3 ảnh chính** này:

### 1. ✅ Success Case (201 Created)

```cmd
curl -X POST http://localhost:8000/api/lab03/products -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"pro_name\":\"Test Product\",\"pro_price\":15000000,\"pro_category_id\":1,\"quantity\":10}"
```

**Chụp:** Response `201 Created` với JSON có `"success": true`

---

### 2. ❌ Error Case (400 Bad Request)

```cmd
curl -X POST http://localhost:8000/api/lab03/products -H "Content-Type: application/json" -d "{\"pro_name\":\"\",\"pro_price\":-100}"
```

**Chụp:** Response `400 Bad Request` với validation errors

---

### 3. ❌ Not Found Case (404)

```cmd
curl http://localhost:8000/api/lab03/products/99999
```

**Chụp:** Response `404 Not Found` với error message

---

## 📁 CẤU TRÚC FILE LAB 03

```
D:\Web_Ban_Do_Dien_Tu\
├── app\Lab03\
│   ├── Controllers\ProductController.php     ✅ Presentation Layer
│   ├── Services\ProductService.php          ✅ Business Logic Layer
│   ├── Repositories\
│   │   ├── ProductRepositoryInterface.php   ✅ Interface
│   │   └── ProductRepository.php           ✅ Data Access Layer
│   ├── Providers\Lab03ServiceProvider.php   ✅ DI Container
│   └── routes.php                          ✅ API Routes
│
├── Design\
│   ├── Lab03_Sequence_CRUD.puml            ✅ Sequence Diagram
│   └── Lab03_Component_Diagram.puml        ✅ Component Diagram
│
├── LAB03_REPORT.md                         ✅ Full Report
├── LAB03_QUICK_START.md                    ✅ Quick Guide
├── LAB03_SUMMARY.md                        ✅ Summary
└── LAB03_CMD_GUIDE.md                      ✅ This file
```

---

## 🎯 CHECKLIST HOÀN THÀNH LAB 03

- [ ] **Setup:**
  - [ ] Đã clear cache (`php artisan config:clear`)
  - [ ] Đã check routes (`php artisan route:list | findstr "lab03"`)
  - [ ] Server đang chạy (`php artisan serve`)

- [ ] **Testing:**
  - [ ] Test health check (200 OK)
  - [ ] Test create success (201 Created)
  - [ ] Test create error (400 Bad Request)
  - [ ] Test get not found (404 Not Found)
  - [ ] Test list products (200 OK)

- [ ] **Screenshots:**
  - [ ] Chụp màn hình 201 Created
  - [ ] Chụp màn hình 400 Bad Request
  - [ ] Chụp màn hình 404 Not Found

- [ ] **Documentation:**
  - [ ] Đọc `LAB03_REPORT.md`
  - [ ] Xem diagrams (`.puml` files)
  - [ ] Hiểu 3-layer architecture

---

## 🚀 LỆNH NHANH (COPY PASTE)

**1. Start server:**

```cmd
cd /d D:\Web_Ban_Do_Dien_Tu && php artisan config:clear && php artisan route:clear && php artisan serve
```

**2. Test nhanh (mở CMD mới):**

```cmd
curl http://localhost:8000/api/lab03/health
```

**3. Create product:**

```cmd
curl -X POST http://localhost:8000/api/lab03/products -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"pro_name\":\"Test Product\",\"pro_price\":15000000,\"pro_category_id\":1,\"quantity\":10}"
```

---

## 💡 TIPS

1. **Giữ 2 cửa sổ CMD:**
   - CMD 1: Chạy `php artisan serve` (server)
   - CMD 2: Chạy lệnh `curl` (test API)

2. **Nếu curl không có trong CMD:**
   - Dùng Postman
   - Hoặc dùng PowerShell (có curl built-in)

3. **Xem logs real-time:**
   - Mở file `storage/logs/laravel.log`
   - Hoặc chạy: `php artisan tail`

4. **Test với Postman (dễ hơn):**
   - Import collection từ API docs
   - Click "Send" thay vì gõ curl

---

## 📞 HỖ TRỢ

Nếu gặp lỗi, check theo thứ tự:

1. ✅ Server có chạy không? (`php artisan serve`)
2. ✅ MySQL có chạy không? (XAMPP Control Panel)
3. ✅ Routes có đúng không? (`php artisan route:list | findstr "lab03"`)
4. ✅ Cache đã clear chưa? (`php artisan config:clear`)
5. ✅ Autoload đã update chưa? (`composer dump-autoload`)

---

**🎉 CHÚC BẠN THÀNH CÔNG VỚI LAB 03!**

**Nếu cần giúp thêm, hãy cho tôi biết lỗi cụ thể!** 🚀
