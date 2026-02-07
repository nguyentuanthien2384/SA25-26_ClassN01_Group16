# Hướng Dẫn Chạy Hệ Thống Microservices

Hướng dẫn chi tiết để chạy **Web Bán Hàng + Notification Service** (Microservices Architecture)

---

## 📋 Yêu Cầu Hệ Thống

- **PHP** >= 8.0
- **Composer**
- **MySQL** hoặc MariaDB
- **Redis** server
- **SMTP** (Gmail, SendGrid, hoặc tương tự)

---

## 🚀 Bước 1: Setup Web Chính (Laravel)

### 1.1. Install Dependencies

```bash
cd d:\Web_Ban_Do_Dien_Tu
composer install
```

### 1.2. Cấu Hình .env

Copy `.env.example` sang `.env` và cấu hình:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

# Queue
QUEUE_CONNECTION=redis
```

### 1.3. Run Migrations

```bash
php artisan key:generate
php artisan migrate
```

Kiểm tra bảng `outbox_messages` đã được tạo.

### 1.4. Chạy Web Server

```bash
php artisan serve
# Web chạy tại: http://localhost:8000
```

### 1.5. Chạy Queue Worker (Terminal riêng)

```bash
php artisan queue:work
```

### 1.6. Setup Scheduler (Optional - Auto publish outbox)

Thêm vào `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('outbox:publish')->everyMinute();
}
```

Chạy scheduler:

```bash
php artisan schedule:work
```

---

## 📧 Bước 2: Setup Notification Service

### 2.1. Install Dependencies

```bash
cd d:\Web_Ban_Do_Dien_Tu\notification-service
composer install
```

### 2.2. Cấu Hình .env

Copy `.env.example` sang `.env`:

```bash
cp .env.example .env
```

**Config Redis** (giống web chính):

```env
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_QUEUE=notifications
```

**Config SMTP (Gmail example):**

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_ENCRYPTION=tls
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME="Web Ban Hang"
```

> **Tạo Gmail App Password:**
>
> 1. Vào https://myaccount.google.com/apppasswords
> 2. Bật 2-Factor Authentication
> 3. Tạo App Password và copy vào `SMTP_PASSWORD`

### 2.3. Chạy Service (Terminal riêng)

```bash
php consumer.php
```

Output khi chạy thành công:

```
[INFO] === Notification Service Starting ===
[INFO] Redis: 127.0.0.1:6379
[INFO] Queue: notifications
[INFO] ✓ Connected to Redis successfully
[INFO] ✓ Email sender initialized
[INFO] ✓ Consumer initialized
[INFO] 🚀 Starting to consume messages...
```

---

## 🧪 Bước 3: Test Hệ Thống

### 3.1. Test Đặt Hàng → Gửi Email

1. **Đăng ký tài khoản** tại web: `http://localhost:8000/register`

2. **Thêm sản phẩm vào giỏ** và **đặt hàng**

3. **Kiểm tra outbox_messages:**

```bash
# Trong Laravel Tinker
php artisan tinker
>>> DB::table('outbox_messages')->where('published', false)->get();
```

4. **Publish outbox messages:**

```bash
# Thủ công
php artisan outbox:publish

# Hoặc đợi queue worker tự động xử lý
```

5. **Xem log Notification Service:**

Console của `notification-service` sẽ hiển thị:

```
[INFO] Received message from queue
[INFO] Processing event [OrderPlaced]
[INFO] Order confirmation email sent [transaction_id: 1, to: user@example.com]
[INFO] ✓ Event processed successfully
```

6. **Kiểm tra email** trong inbox của người dùng

---

## 📊 Kiến Trúc Luồng Xử Lý

```
┌─────────────────────┐
│   User Đặt Hàng     │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────────────┐
│  CartController::saveCart   │
│  - Tạo Transaction          │
│  - Dispatch OrderPlaced     │
└──────────┬──────────────────┘
           │
           ▼
┌───────────────────────────────────┐
│  SaveOrderPlacedToOutbox Listener │
│  - Lưu vào outbox_messages        │
└──────────┬────────────────────────┘
           │
           ▼
┌─────────────────────────────┐
│  Queue Worker (Laravel)     │
│  - Lấy từ queue             │
│  - Chạy PublishOutbox job   │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│  Redis Queue                │
│  - Chứa event messages      │
└──────────┬──────────────────┘
           │
           ▼
┌──────────────────────────────┐
│  Notification Service        │
│  - Consumer lấy messages     │
│  - EmailSender gửi email     │
└──────────────────────────────┘
```

---
