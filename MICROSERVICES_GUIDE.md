# Hướng Dẫn Phát Triển Microservices - Web Bán Đồ Điện Tử

## Tổng Quan

---

## ✅ Phase 1: Modular Monolith (ĐÃ HOÀN TẤT)

### 1.1. Các Module Đã Tạo

7 modules mới đã được tạo theo domain:

- **Catalog** - Sản phẩm, danh mục, trang chủ
- **Content** - Bài viết
- **Customer** - Auth, User, Wishlist
- **Cart** - Giỏ hàng, Checkout
- **Payment** - Thanh toán (Momo, VNPay, PayPal, QRCode)
- **Review** - Đánh giá sản phẩm
- **Support** - Liên hệ
- **Admin** - Quản trị (đã có sẵn)

### 1.2. Controllers Đã Di Chuyển

| Module       | Controllers                                                 |
| ------------ | ----------------------------------------------------------- |
| **Catalog**  | HomeController, CategoryController, ProductDetailController |
| **Content**  | ArticleController                                           |
| **Customer** | AuthUserController, UserController, WishlistController      |
| **Cart**     | CartController                                              |
| **Payment**  | PaymentController                                           |
| **Review**   | RatingController                                            |
| **Support**  | ContactController                                           |

### 1.3. Routes Đã Tách

Mỗi module có file `routes/web.php` riêng. File `routes/web.php` chính chỉ giữ:

- Core Laravel routes (`Auth::routes()`)
- Laravel File Manager

### 1.4. Kích Hoạt Modules

File `modules_statuses.json` đã được cập nhật:

```json
{
    "Admin": true,
    "Catalog": true,
    "Content": true,
    "Customer": true,
    "Cart": true,
    "Payment": true,
    "Review": true,
    "Support": true
}
```

---

## ✅ Phase 2: Event-Driven Architecture + Outbox Pattern (ĐÃ HOÀN TẤT)

### 2.1. Queue Driver - Redis

**Đã cài đặt:** `predis/predis` package

**Cách sử dụng:**

1. Cài đặt Redis server
2. Cập nhật `.env`:

    ```env
    QUEUE_CONNECTION=redis
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    ```

3. Chạy queue worker:
    ```bash
    php artisan queue:work
    ```

### 2.2. Outbox Pattern

**Migration:** `2026_01_28_003929_create_outbox_messages_table.php`

**Cấu trúc bảng `outbox_messages`:**

- `id` - Primary key
- `aggregate_type` - Loại entity (Product, Order, User...)
- `aggregate_id` - ID của entity
- `event_type` - Tên event (ProductCreated, OrderPlaced...)
- `payload` - Dữ liệu event (JSON)
- `occurred_at` - Thời điểm xảy ra
- `published` - Đã publish chưa?
- `published_at` - Thời điểm publish

**Chạy migration:**

```bash
php artisan migrate
```

### 2.3. Domain Events

**Event Ví Dụ:** `App\Events\OrderPlaced`
**Listener:** `App\Listeners\SaveOrderPlacedToOutbox`

**Cách sử dụng:**

1. Đăng ký listener trong `app/Providers/EventServiceProvider.php`:

    ```php
    protected $listen = [
        \App\Events\OrderPlaced::class => [
            \App\Listeners\SaveOrderPlacedToOutbox::class,
        ],
    ];
    ```

2. Dispatch event khi đặt hàng:

    ```php
    use App\Events\OrderPlaced;

    // Sau khi tạo transaction
    event(new OrderPlaced($transaction, $orderDetails));
    ```

3. Publish outbox messages:

    ```bash
    # Thủ công
    php artisan outbox:publish

    # Hoặc schedule trong app/Console/Kernel.php
    $schedule->command('outbox:publish')->everyMinute();
    ```

---

## 🔄 Phase 3: Tách Notification Service

#### 3.1. Tạo Notification Service (Project riêng)

1. **Tạo project PHP mới:**

    ```bash
    mkdir notification-service
    cd notification-service
    composer init
    ```

2. **Cài dependencies:**

    ```bash
    composer require php-amqplib/php-amqplib
    composer require symfony/mailer
    ```

3. **Consumer RabbitMQ:**
    - Subscribe topic: `order.placed`, `user.registered`
    - Gửi email thông báo

4. **Config:**
    - SMTP cho email
    - RabbitMQ connection

#### 3.2. Tích Hợp với Web Chính

1. **Chuyển từ Redis sang RabbitMQ:**
    - Bật extension `sockets` trong `php.ini`
    - Cài `vladimir-yuldashev/laravel-queue-rabbitmq`
    - Đổi `QUEUE_CONNECTION=rabbitmq`

2. **Publish events qua RabbitMQ:**
    - Sửa `PublishOutboxMessages` job
    - Publish tới exchange: `events`

3. **Deploy:**
    - Web chính: Port 8000
    - Notification Service: Background process

---

## 📝 Cách Sử Dụng Hiện Tại

### 1. Clone & Setup

```bash
cd d:\Web_Ban_Do_Dien_Tu
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 2. Chạy Server

```bash
php artisan serve
```

### 3. Test Module Routes

- **Trang chủ:** `http://localhost:8000/`
- **Danh mục:** `http://localhost:8000/danh-muc/slug-1`
- **Sản phẩm:** `http://localhost:8000/san-pham/slug-1`
- **Bài viết:** `http://localhost:8000/bai-viet`
- **Liên hệ:** `http://localhost:8000/lien-he`
- **Admin:** `http://localhost:8000/admin`

### 4. Test Outbox Pattern

```php
// Trong controller sau khi đặt hàng
use App\Events\OrderPlaced;

$transaction = Transaction::create([...]);
event(new OrderPlaced($transaction, $orderDetails));

// Check outbox_messages table
DB::table('outbox_messages')->where('published', false)->get();

// Publish manually
php artisan outbox:publish
```

---

## 🎯 Lợi Ích Đạt Được

### Phase 1: Modular Monolith

✅ **Tách biệt domain** - Mỗi module độc lập
✅ **Dễ maintain** - Code rõ ràng, không lộn xộn
✅ **Chuẩn bị microservices** - Sẵn sàng "nhấc" module ra service

### Phase 2: Event-Driven

✅ **Decoupling** - Module không phụ thuộc trực tiếp
✅ **Reliable messaging** - Outbox đảm bảo không mất event
✅ **Async processing** - Xử lý nền qua queue

---

## 🚀 Next Steps

1. **Triển khai sử dụng Events:**
    - `ProductCreated`, `ProductUpdated`
    - `UserRegistered`
    - `PaymentSucceeded`

2. **Setup RabbitMQ:** (nếu muốn thay Redis)
    - Cài RabbitMQ server
    - Bật extension `sockets`
    - Cài package `laravel-queue-rabbitmq`

3. **Tách Notification Service:**
    - Tạo project riêng
    - Consumer RabbitMQ
    - Gửi email/SMS

4. **Monitoring & Observability:**
    - Log aggregation (ELK Stack)
    - Tracing (Jaeger)
    - Metrics (Prometheus)

---
