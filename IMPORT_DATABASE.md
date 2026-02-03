# 📦 Hướng Dẫn Import Database từ File SQL

## 📁 File Database

**File:** `duan.sql` (đã có sẵn trong project)  
**Database name:** `duan`  
**Size:** ~924 dòng  
**Includes:** Tables + Data + Admin accounts

---

## ⚡ CÁCH 1: Import qua Command Line (NHANH NHẤT)

### Bước 1: Nhập Password MySQL vào .env

Mở file `.env` và sửa:

```env
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=your_mysql_password_here
```

### Bước 2: Tạo Database

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS duan;"
```

**Hoặc trong MySQL console:**

```sql
mysql -u root -p
CREATE DATABASE IF NOT EXISTS duan;
exit;
```

### Bước 3: Import File SQL

```bash
cd d:\Web_Ban_Do_Dien_Tu
mysql -u root -p duan < duan.sql
```

**Nhập password khi được hỏi.**

### Bước 4: Verify & Run

```bash
php artisan config:clear
php artisan serve
```

→ Mở: http://localhost:8000

---

## 👍 CÁCH 2: Import qua phpMyAdmin (DỄ NHẤT)

### Bước 1: Mở phpMyAdmin

- URL: http://localhost/phpmyadmin
- Hoặc: http://127.0.0.1/phpmyadmin

**Login:**
- Username: `root`
- Password: [your MySQL password]

### Bước 2: Tạo Database

1. Click **"New"** ở sidebar trái
2. Database name: `duan`
3. Collation: `utf8mb4_unicode_ci`
4. Click **"Create"**

### Bước 3: Import File

1. Click vào database **"duan"** vừa tạo
2. Click tab **"Import"** ở menu trên
3. Section "File to import":
   - Click **"Choose File"**
   - Browse đến: `d:\Web_Ban_Do_Dien_Tu\duan.sql`
4. Scroll xuống cuối
5. Click **"Go"** (Import)

**Đợi import xong (có thể 10-30 giây)**

✅ Thành công khi thấy: "Import has been successfully finished"

### Bước 4: Cập nhật .env

Mở file `.env`:

```env
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

### Bước 5: Run Application

```bash
php artisan config:clear
php artisan serve
```

→ Mở: http://localhost:8000

---

## 🔧 CÁCH 3: Import qua MySQL Workbench

### Bước 1: Mở MySQL Workbench

1. Launch MySQL Workbench
2. Click vào connection của bạn (thường là Local instance)
3. Nhập password

### Bước 2: Import Data

1. Menu: **Server → Data Import**
2. Chọn **"Import from Self-Contained File"**
3. Click **"..."** button
4. Browse đến: `d:\Web_Ban_Do_Dien_Tu\duan.sql`
5. Section "Default Target Schema":
   - Option 1: Chọn existing schema "duan" (nếu có)
   - Option 2: Chọn "New..." để tạo mới
6. Click **"Start Import"** (bottom right)

### Bước 3: Verify

Sau khi import xong:
- Left sidebar: Refresh schemas
- Click vào `duan` → Tables
- Xem các tables đã được import

### Bước 4: Cập nhật .env & Run

```env
DB_DATABASE=duan
DB_PASSWORD=your_mysql_password
```

```bash
php artisan config:clear
php artisan serve
```

---

## 🧪 VERIFY DATABASE

### Check 1: Xem Tables

```bash
mysql -u root -p duan -e "SHOW TABLES;"
```

**Kết quả mong đợi:**
```
+--------------------+
| Tables_in_duan     |
+--------------------+
| admin              |
| article            |
| banner             |
| carts              |
| category           |
| contact            |
| failed_jobs        |
| import_goods       |
| migrations         |
| oders              |
| password_resets    |
| ...                |
+--------------------+
```

### Check 2: Đếm Records

```bash
mysql -u root -p duan -e "SELECT COUNT(*) FROM admin;"
```

**Kết quả:** Nên có ít nhất 2 admin accounts

### Check 3: Test từ Laravel

```bash
php artisan tinker
```

```php
>>> DB::connection()->getPdo();
# Không lỗi = Success!

>>> DB::table('admin')->count();
# Nên trả về số lượng admin

>>> exit
```

---

## 🔐 ADMIN ACCOUNTS (từ duan.sql)

Theo file SQL, có 2 admin accounts:

### Admin 1:
- **Email:** admin@gmail.com
- **Password:** [cần check hoặc reset]

### Admin 2:
- **Email:** admin123@gmail.com
- **Password:** [cần check hoặc reset]

**Login URL:** http://localhost:8000/admin/login

---

## ⚠️ LƯU Ý QUAN TRỌNG

### Database Name

File SQL tạo database tên **`duan`**, không phải `csdl`.

**Chọn 1 trong 2:**

**Option A: Dùng tên `duan` (Khuyên dùng)**
```env
# .env
DB_DATABASE=duan
```

**Option B: Đổi tên database thành `csdl`**
```sql
mysql -u root -p
CREATE DATABASE csdl;
USE csdl;
SOURCE d:\Web_Ban_Do_Dien_Tu\duan.sql;
```

### Password trong .env

**PHẢI CẬP NHẬT** password MySQL:

```env
DB_PASSWORD=your_actual_mysql_password
```

**Test password:**
```bash
mysql -u root -p
# Nhập password → nếu vào được = đúng
```

---

## 🆘 TROUBLESHOOTING

### Lỗi: "MySQL server has gone away"

**Nguyên nhân:** File SQL quá lớn

**Fix 1: Tăng max_allowed_packet**

Trong `my.ini` (hoặc `my.cnf`):
```ini
[mysqld]
max_allowed_packet=64M
```

Restart MySQL.

**Fix 2: Import từng phần**

Dùng phpMyAdmin (tự động handle large files)

### Lỗi: "Table already exists"

**Nguyên nhân:** Database đã có tables

**Fix:**
```sql
mysql -u root -p
DROP DATABASE duan;
CREATE DATABASE duan;
exit;

mysql -u root -p duan < duan.sql
```

### Lỗi: "Access denied"

**Fix:** Đọc file `FIX_DATABASE_ERROR.md` hoặc `FIX_NOW.txt`

### Import chậm

- Dùng command line (nhanh nhất)
- Hoặc đợi phpMyAdmin import xong (có progress bar)

---

## ✅ SUCCESS CHECKLIST

- [ ] Database `duan` đã được tạo
- [ ] File `duan.sql` đã import thành công
- [ ] File `.env` đã cập nhật `DB_DATABASE=duan`
- [ ] File `.env` đã cập nhật `DB_PASSWORD=...`
- [ ] Chạy `php artisan config:clear` không lỗi
- [ ] Chạy `php artisan serve` không lỗi
- [ ] Mở http://localhost:8000 hiển thị trang chủ
- [ ] Có thể login admin: http://localhost:8000/admin

---

## 🎯 SAU KHI IMPORT XONG

### Không cần chạy migrate

```bash
# KHÔNG CẦN:
# php artisan migrate  ← Bỏ qua bước này!

# CHỈ CẦN:
php artisan config:clear
php artisan serve
```

### Test Application

1. Trang chủ: http://localhost:8000
2. Admin: http://localhost:8000/admin
3. API Health: http://localhost:8000/api/health

---

## 📚 NEXT STEPS

Sau khi import database thành công:

1. **Chạy app:** `php artisan serve`
2. **Test features:** Xem sản phẩm, đăng ký user, đặt hàng
3. **Login admin:** http://localhost:8000/admin
4. **Commit code:** Đọc `GIT_COMMIT_GUIDE.md`

---

**Last Updated:** 2026-01-28  
**Status:** Easy Import ✅
