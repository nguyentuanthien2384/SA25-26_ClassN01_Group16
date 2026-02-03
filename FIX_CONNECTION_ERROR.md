# 🔧 Fix Lỗi MySQL Connection Refused

## ❌ LỖI ĐANG GẶP

```
SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it
```

**Location:** `FrontendController.php` line 12  
**Query:** `Category::all()`

---

## 🎯 NGUYÊN NHÂN

File `.env` của bạn có **PORT SAI**:

```env
DB_PORT=3307  ← SAI!
```

Nhưng MySQL đang chạy trên port **3306**

---

## ✅ CÁCH FIX (1 PHÚT)

### Bước 1: Mở file .env

File: `d:\Web_Ban_Do_Dien_Tu\.env`

### Bước 2: Tìm dòng 13

**Trước (SAI):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307          ← Dòng 13 - SAI!
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=
```

**Sau (ĐÚNG):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306          ← Sửa thành 3306
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=your_password  ← Nhớ điền password nếu có
```

### Bước 3: Save file .env

### Bước 4: Clear cache & Test

```bash
php artisan config:clear
php artisan serve
```

Mở: http://localhost:8000

✅ **XONG!**

---

## 🔍 KIỂM TRA PORT MYSQL

### Cách 1: Command Line

```bash
netstat -ano | findstr :3306
```

**Kết quả mong đợi:**
```
TCP    0.0.0.0:3306    0.0.0.0:0    LISTENING
```

### Cách 2: Test Connection

```bash
mysql -u root -p -h 127.0.0.1 --port=3306
```

Nếu vào được → port 3306 đúng!

---

## 📋 CHECKLIST

- [ ] Mở file `.env`
- [ ] Tìm dòng `DB_PORT=3307`
- [ ] Sửa thành `DB_PORT=3306`
- [ ] Kiểm tra `DB_PASSWORD=` có đúng không
- [ ] Save file
- [ ] Chạy `php artisan config:clear`
- [ ] Chạy `php artisan serve`
- [ ] Test: http://localhost:8000

---

## 🆘 VẪN LỖI?

### Lỗi: Access denied

**Nguyên nhân:** Chưa có password trong `.env`

**Fix:** Đọc file `FIX_NOW.txt`

### Lỗi: Unknown database 'duan'

**Nguyên nhân:** Chưa import file `duan.sql`

**Fix:** Đọc file `IMPORT_NOW.txt`

### MySQL không chạy

**Fix:**
```bash
# Start MySQL service:
net start MySQL80

# Hoặc:
net start MySQL
```

---

## 💡 TẠI SAO CÓ LỖI NÀY?

- MySQL mặc định chạy trên port **3306**
- File `.env` của bạn có `DB_PORT=3307` (có thể copy từ project khác dùng MariaDB hoặc multiple MySQL instances)
- Laravel cố kết nối port 3307 → không có MySQL ở port đó → bị từ chối

---

## ✅ SAU KHI FIX

Khi fix đúng, trang chủ sẽ hiển thị:
- Danh sách categories
- Danh sách sản phẩm
- Banner
- Không còn lỗi connection

**Tiếp theo:**
- Test đăng ký/đăng nhập
- Test thêm vào giỏ hàng
- Test admin panel: http://localhost:8000/admin

---

**Last Updated:** 2026-01-28  
**Status:** Easy Fix - Just Change Port ✅
