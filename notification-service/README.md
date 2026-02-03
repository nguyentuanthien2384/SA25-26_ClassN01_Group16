# Notification Service - Microservice

Notification Service là một microservice độc lập để xử lý việc gửi email thông báo cho hệ thống Web Bán Hàng.

## Kiến Trúc

```
Web Bán Hàng (Laravel)
    ↓ (publish events)
Redis Queue
    ↓ (consume)
Notification Service (PHP)
    ↓ (send emails)
SMTP Server (Gmail, etc.)
```

## Cài Đặt

### 1. Install Dependencies

```bash
cd notification-service
composer install
```

### 2. Cấu Hình

Copy `.env.example` sang `.env` và điền thông tin:

```bash
cp .env.example .env
```

**Config Redis:**
```env
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_QUEUE=notifications
```

**Config SMTP (Gmail example):**
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=your-email@gmail.com
```

> **Lưu ý Gmail:** Bạn cần tạo **App Password** tại: https://myaccount.google.com/apppasswords

### 3. Chạy Service

```bash
php consumer.php
```

Service sẽ lắng nghe queue `notifications` và xử lý các events:
- `OrderPlaced` → Gửi email xác nhận đơn hàng
- `UserRegistered` → Gửi email chào mừng
- `PaymentSucceeded` → Gửi email xác nhận thanh toán

## Cấu Trúc Thư Mục

```
notification-service/
├── composer.json          # Dependencies
├── .env                   # Configuration (không commit)
├── .env.example          # Configuration template
├── bootstrap.php         # Application bootstrap
├── consumer.php          # Main consumer script
├── config/
│   └── config.php        # Configuration loader
├── src/
│   ├── RedisConsumer.php # Redis queue consumer
│   └── EmailSender.php   # Email sending logic
└── logs/
    └── app.log           # Application logs
```

## Event Format

Events từ web chính có format:

```json
{
    "event_type": "OrderPlaced",
    "aggregate_type": "Transaction",
    "aggregate_id": 123,
    "payload": {
        "transaction_id": 123,
        "user_id": 1,
        "user_email": "customer@example.com",
        "total": 500000,
        "payment_method": "momo",
        "order_details": [...]
    },
    "occurred_at": "2026-01-28 07:00:00"
}
```

## Monitoring

### Xem Logs

```bash
# Real-time logs
tail -f logs/app.log

# Hoặc xem trên console (stdout)
php consumer.php
```

### Kiểm Tra Redis Queue

```bash
redis-cli
> LLEN notifications  # Xem số message trong queue
> LRANGE notifications 0 -1  # Xem tất cả messages
```

## Testing

### Gửi Test Message

Từ Laravel app:

```php
use App\Events\OrderPlaced;

$transaction = Transaction::find(1);
event(new OrderPlaced($transaction, []));

// Run outbox publisher
php artisan outbox:publish
```

## Production Deployment

### 1. Sử Dụng Supervisor (Linux)

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

### 2. Chạy Nền (Windows)

```bash
# PowerShell
Start-Process -NoNewWindow -FilePath "php" -ArgumentList "consumer.php"
```

### 3. Docker (Optional)

```dockerfile
FROM php:8.2-cli
WORKDIR /app
COPY . /app
RUN composer install --no-dev --optimize-autoloader
CMD ["php", "consumer.php"]
```

## Troubleshooting

### Lỗi kết nối Redis

```
Error: Connection refused [tcp://127.0.0.1:6379]
```

**Giải pháp:** Kiểm tra Redis đã chạy chưa:
```bash
redis-cli ping
# Phải trả về: PONG
```

### Lỗi gửi email

```
Failed to authenticate on SMTP server
```

**Giải pháp:** 
- Gmail: Bật 2FA và tạo App Password
- Kiểm tra SMTP credentials trong `.env`

### Consumer không nhận messages

**Giải pháp:**
1. Kiểm tra queue name trong `.env` khớp với Laravel
2. Kiểm tra Outbox publisher đã chạy: `php artisan outbox:publish`

## Scaling

Để scale service, chạy nhiều consumer instances:

```bash
# Terminal 1
php consumer.php

# Terminal 2  
php consumer.php

# Terminal 3
php consumer.php
```

Mỗi consumer sẽ xử lý messages độc lập, tăng throughput.

---

**Maintainer:** AI Assistant  
**Version:** 1.0.0  
**Last Updated:** 2026-01-28
