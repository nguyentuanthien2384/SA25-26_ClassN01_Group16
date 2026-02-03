# ⚡ CHẠY NHANH - 3 PHÚT

## 1️⃣ Setup (1 phút)

```bash
cd d:\Web_Ban_Do_Dien_Tu

# Copy .env
copy .env.example .env

# Generate key
php artisan key:generate
```

**Chỉnh file `.env` (chỉ cần 3 dòng):**

```env
DB_DATABASE=duan                 # ⚠️ Dùng "duan" vì file SQL tạo database tên này
DB_USERNAME=root
DB_PASSWORD=your_mysql_password  # ⚠️ QUAN TRỌNG: Nhập password MySQL của bạn!
```

**⚠️ LƯU Ý:** 
- Nếu MySQL của bạn có password → nhập vào `DB_PASSWORD=`
- Nếu không có password → để trống `DB_PASSWORD=`
- Test password: `mysql -u root -p` (nhập password khi được hỏi)

## 2️⃣ Database (1 phút)

**⚠️ BẠN ĐÃ CÓ FILE SQL SẴN → Import thay vì migrate!**

```bash
# Option A: Import qua Command Line (NHANH)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS duan;"
mysql -u root -p duan < duan.sql

# Option B: Import qua phpMyAdmin (DỄ)
# 1. Mở http://localhost/phpmyadmin
# 2. Tạo database "duan"
# 3. Import file "duan.sql"
```

**Đọc chi tiết:** `IMPORT_DATABASE.md`

## 3️⃣ Chạy (30 giây)

```bash
php artisan serve
```

## 4️⃣ Mở Browser

```
http://localhost:8000
```

---

## ✅ XONG!

**Admin:** http://localhost:8000/admin  
**API Health:** http://localhost:8000/api/health  

---

## 🔧 Nếu Gặp Lỗi

### Lỗi: "Access denied for user 'root'@'localhost'" ⚠️
```bash
# NGUYÊN NHÂN: MySQL có password nhưng .env không có
# FIX: Mở file .env, sửa dòng DB_PASSWORD=your_password

# Hoặc đọc file này:
# FIX_DATABASE_ERROR.md
```

### Lỗi Database Connection:
```bash
# Check MySQL đang chạy
net start MySQL80

# Test password
mysql -u root -p
```

### Lỗi Port 8000:
```bash
php artisan serve --port=8080
```

### Lỗi Cache:
```bash
php artisan config:clear
php artisan cache:clear
```

---

**Đọc thêm:** `GETTING_STARTED.md` để biết đầy đủ features
