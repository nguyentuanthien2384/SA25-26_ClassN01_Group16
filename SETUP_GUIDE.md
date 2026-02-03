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
DB_PORT=3306
DB_DATABASE=web_ban_hang
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

## 🔧 Troubleshooting

### Lỗi: Connection refused [tcp://127.0.0.1:6379]

**Nguyên nhân:** Redis chưa chạy

**Giải pháp:**

```bash
# Windows (với Redis MSI installer)
redis-server

# Hoặc dùng WSL
wsl
sudo service redis-server start

# Kiểm tra
redis-cli ping
# Output: PONG
```

### Lỗi: Failed to authenticate on SMTP server

**Nguyên nhân:** SMTP credentials sai hoặc Gmail chặn

**Giải pháp:**
1. Kiểm tra username/password trong `.env`
2. Với Gmail: Bật 2FA và tạo App Password
3. Thử SMTP provider khác: SendGrid, Mailgun

### Queue Worker không xử lý jobs

**Giải pháp:**

```bash
# Restart queue worker
Ctrl+C (để stop)
php artisan queue:work
```

### Notification Service không nhận messages

**Kiểm tra:**

```bash
# Xem messages trong Redis
redis-cli
> LLEN notifications
> LRANGE notifications 0 -1

# Nếu có messages, restart consumer
Ctrl+C
php consumer.php
```

---

## 📈 Production Deployment

### Web Chính (Laravel)

```bash
# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue worker (Supervisor)
sudo apt install supervisor
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
```

### Notification Service (Supervisor)

```ini
[program:notification-service]
command=php /path/to/notification-service/consumer.php
directory=/path/to/notification-service
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/notification-service.log
```

---

## 📚 Các Command Hữu Ích

### Laravel

```bash
# Xem outbox messages chưa publish
php artisan tinker
>>> OutboxMessage::unpublished()->count()

# Publish outbox thủ công
php artisan outbox:publish

# Xem queue jobs
php artisan queue:monitor

# Clear queue (nếu cần)
php artisan queue:clear redis
```

### Redis

```bash
redis-cli

# Xem số messages trong queue
> LLEN notifications

# Xem tất cả messages
> LRANGE notifications 0 -1

# Xóa queue (cẩn thận!)
> DEL notifications

# Monitor real-time
> MONITOR
```

### Notification Service

```bash
# Xem logs real-time
tail -f logs/app.log

# Kiểm tra process đang chạy (Linux)
ps aux | grep consumer.php

# Kill process
kill <PID>
```

---

## 🎯 Next Steps - Mở Rộng

### 1. Thêm Events Khác

Tạo events cho:
- `UserRegistered` → Welcome email
- `PaymentSucceeded` → Payment confirmation
- `ProductCreated` → Admin notification

### 2. Scale Notification Service

Chạy nhiều consumers:

```bash
# Terminal 1
php consumer.php

# Terminal 2
php consumer.php

# Terminal 3
php consumer.php
```

### 3. Monitoring & Alerting

- **ELK Stack** - Aggregate logs
- **Prometheus + Grafana** - Metrics
- **Sentry** - Error tracking

### 4. Tách Services Khác

- **Product Service** (Catalog module)
- **User Service** (Customer module)
- **Payment Service** (Payment module)

---

## 📞 Support

Nếu gặp vấn đề, kiểm tra:
1. **Logs:** `storage/logs/laravel.log` (web chính)
2. **Logs:** `notification-service/logs/app.log` (service)
3. **Redis:** `redis-cli MONITOR`

---

**Version:** 1.0.0  
**Last Updated:** 2026-01-28  
**Status:** ✅ HOÀN TẤT - PRODUCTION READY
