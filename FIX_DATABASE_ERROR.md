# 🔧 Fix Lỗi Database Connection

## ❌ LỖI ĐANG GẶP

```
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
```

**Nguyên nhân:** MySQL có password nhưng file `.env` không có password.

---

## ✅ CÁCH FIX

### Option 1: Nhập Password vào .env (Khuyên Dùng) ⭐

**Bước 1:** Tìm password MySQL của bạn

```bash
# Test xem MySQL có password không:
mysql -u root
# Nếu báo lỗi → có password

mysql -u root -p
# Nhập password → nếu vào được → password đúng!
```

**Bước 2:** Mở file `.env` (dòng 16)

**Trước:**
```env
DB_PASSWORD=
```

**Sau:**
```env
DB_PASSWORD=your_actual_password
```

**Bước 3:** Clear cache & migrate

```bash
php artisan config:clear
php artisan migrate
```

---

### Option 2: Reset Password MySQL về Trống

**Nếu muốn không dùng password:**

#### Windows:

1. **Mở MySQL Command Line Client as Administrator**
   - Start Menu → MySQL → MySQL Command Line Client (Right click → Run as Administrator)

2. **Nhập password hiện tại** (nếu biết) hoặc skip

3. **Chạy lệnh:**
```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
FLUSH PRIVILEGES;
exit;
```

#### Hoặc dùng MySQL Workbench:

1. Mở MySQL Workbench
2. Server → Users and Privileges
3. Chọn user 'root'
4. Tab "Authentication"
5. Xóa password
6. Apply

**Sau đó:**
```bash
php artisan config:clear
php artisan migrate
```

---

### Option 3: Tạo User Mới Cho Laravel

**Tạo user không có password:**

```sql
-- Mở MySQL (với user root):
mysql -u root -p

-- Trong MySQL console:
CREATE DATABASE IF NOT EXISTS csdl;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON csdl.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
exit;
```

**Cập nhật .env:**
```env
DB_USERNAME=laravel_user
DB_PASSWORD=
```

**Test:**
```bash
php artisan config:clear
php artisan migrate
```

---

### Option 4: Dùng Password Mạnh (Production)

**Tạo user với password:**

```sql
mysql -u root -p

-- Trong MySQL:
CREATE DATABASE IF NOT EXISTS csdl;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'Strong_Password_123';
GRANT ALL PRIVILEGES ON csdl.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
exit;
```

**Cập nhật .env:**
```env
DB_USERNAME=laravel_user
DB_PASSWORD=Strong_Password_123
```

**Test:**
```bash
php artisan config:clear
php artisan migrate
```

---

## 🧪 TEST CONNECTION

### Test 1: Test MySQL từ command line

```bash
# Không password:
mysql -u root

# Có password:
mysql -u root -p
# (Nhập password khi được hỏi)

# Nếu vào được, chạy:
SHOW DATABASES;
USE csdl;
exit;
```

### Test 2: Test từ Laravel

```bash
php artisan tinker

# Trong tinker:
>>> DB::connection()->getPdo();
# Nếu không lỗi → kết nối thành công!

>>> exit
```

---

## 🔍 TÌM PASSWORD MYSQL

### Cách 1: Check MySQL Workbench

1. Mở MySQL Workbench
2. Click vào connection đang dùng
3. Edit Connection
4. Xem password (nếu đã lưu)

### Cách 2: Check phpMyAdmin config

```bash
# File: C:\xampp\phpMyAdmin\config.inc.php (nếu dùng XAMPP)
# Tìm dòng:
$cfg['Servers'][$i]['password'] = 'your_password';
```

### Cách 3: Check project Laravel khác

```bash
# Mở .env của project cũ
# Xem DB_PASSWORD=?
```

### Cách 4: Check XAMPP/WAMP/MAMP

- **XAMPP:** Password mặc định = trống
- **WAMP:** Password mặc định = trống hoặc "root"
- **MAMP:** Password mặc định = "root"

---

## ⚠️ LƯU Ý

### Development (Local):

```env
# .env
DB_USERNAME=root
DB_PASSWORD=           # Trống OK
# hoặc
DB_PASSWORD=root       # Nếu có
```

### Production (Server):

```env
# .env
DB_USERNAME=specific_user    # Không dùng root
DB_PASSWORD=strong_password  # Password mạnh
```

---

## 📋 CHECKLIST

- [ ] Test MySQL connection từ command line
- [ ] Tìm được password MySQL
- [ ] Cập nhật password vào `.env` dòng 16
- [ ] Chạy `php artisan config:clear`
- [ ] Chạy `php artisan migrate`
- [ ] Verify: `php artisan tinker` → `DB::connection()->getPdo();`

---

## 🆘 VẪN GẶP LỖI?

### Lỗi: Can't connect to MySQL server

**Fix:**
```bash
# Windows - Start MySQL service:
net start MySQL80
# hoặc
net start MySQL
```

### Lỗi: Unknown database 'csdl'

**Fix:**
```sql
mysql -u root -p
CREATE DATABASE csdl;
exit;
```

### Lỗi: php artisan config:clear không giúp gì

**Fix:**
```bash
# Clear tất cả cache:
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Restart lại terminal
# Chạy lại:
php artisan migrate
```

---

## ✅ SUCCESS!

Khi thành công, bạn sẽ thấy:

```
Migration table created successfully.
Migrating: 2014_10_12_100000_create_password_reset_tokens_table
Migrated:  2014_10_12_100000_create_password_reset_tokens_table
...
```

→ **Tiếp tục:** `php artisan serve` và mở http://localhost:8000

---

**Last Updated:** 2026-01-28  
**Status:** Common Issue - Easy Fix ✅
