# Kiến Trúc Microservices - Web Bán Đồ Điện Tử

## 📐 Tổng Quan Kiến Trúc

Hệ thống được xây dựng theo **Strangler Pattern**, tách dần từ Monolith lên Microservices.

```
┌──────────────────────────────────────────────────────────────┐
│                    CLIENT (Web Browser)                       │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────┐
│                  WEB APPLICATION (Laravel)                    │
│                                                               │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │   Catalog   │  │  Customer   │  │    Cart     │         │
│  │   Module    │  │   Module    │  │   Module    │         │
│  └─────────────┘  └─────────────┘  └─────────────┘         │
│                                                               │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │   Payment   │  │   Review    │  │   Support   │         │
│  │   Module    │  │   Module    │  │   Module    │         │
│  └─────────────┘  └─────────────┘  └─────────────┘         │
│                                                               │
│  ┌──────────────────────────────────────────────┐           │
│  │         Event-Driven Layer                   │           │
│  │  - Domain Events (OrderPlaced, etc.)         │           │
│  │  - Outbox Pattern (outbox_messages table)    │           │
│  │  - Publisher Job (PublishOutboxMessages)     │           │
│  └──────────────────────────────────────────────┘           │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────┐
│                     REDIS QUEUE                               │
│                  (Message Broker)                             │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────┐
│             NOTIFICATION SERVICE (Microservice)               │
│                                                               │
│  ┌────────────────┐       ┌──────────────────┐              │
│  │ Redis Consumer │──────▶│   EmailSender    │              │
│  │  (PHP Daemon)  │       │  (Symfony Mail)  │              │
│  └────────────────┘       └──────────────────┘              │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────┐
│                   SMTP SERVER (Gmail)                         │
│                    → Customer Email                           │
└──────────────────────────────────────────────────────────────┘
```

---

## 🏗️ Cấu Trúc Modules (Modular Monolith)

### Web Chính - Laravel

```
Web_Ban_Do_Dien_Tu/
├── Modules/
│   ├── Admin/          # Quản trị
│   ├── Catalog/        # Sản phẩm, Danh mục, Trang chủ
│   ├── Content/        # Bài viết
│   ├── Customer/       # Auth, User, Wishlist
│   ├── Cart/           # Giỏ hàng, Checkout
│   ├── Payment/        # Thanh toán (Momo, VNPay, PayPal, QR)
│   ├── Review/         # Đánh giá sản phẩm
│   └── Support/        # Liên hệ
│
├── app/
│   ├── Events/
│   │   └── OrderPlaced.php         # Domain event
│   ├── Listeners/
│   │   └── SaveOrderPlacedToOutbox.php  # Event listener
│   ├── Jobs/
│   │   └── PublishOutboxMessages.php    # Outbox publisher
│   ├── Models/Models/
│   │   └── OutboxMessage.php       # Outbox pattern model
│   └── Console/Commands/
│       └── PublishOutboxCommand.php
│
└── database/
    └── migrations/
        └── *_create_outbox_messages_table.php
```

### Notification Service - Microservice

```
notification-service/
├── src/
│   ├── RedisConsumer.php    # Consumer từ Redis queue
│   └── EmailSender.php      # Logic gửi email
├── config/
│   └── config.php           # Configuration
├── logs/
│   └── app.log              # Service logs
├── consumer.php             # Main entry point
├── bootstrap.php            # Bootstrap app
├── composer.json
└── .env                     # Service config
```

---

## 🔄 Luồng Xử Lý Chi Tiết

### 1. Đặt Hàng (Order Placement)

```
User Action: Đặt hàng
        ↓
CartController::saveCart()
    │
    ├─→ Tạo Transaction (tr_status = PENDING)
    ├─→ Tạo Orders (order_items)
    └─→ Dispatch: event(new OrderPlaced($transaction, $orderDetails))
        ↓
SaveOrderPlacedToOutbox Listener
    │
    └─→ INSERT INTO outbox_messages (
            aggregate_type: 'Transaction',
            aggregate_id: 123,
            event_type: 'OrderPlaced',
            payload: {...},
            published: false
        )
```

### 2. Event Publishing (Async)

```
Laravel Queue Worker
    ↓
PublishOutboxMessages Job (every 1 minute or triggered)
    │
    ├─→ SELECT * FROM outbox_messages WHERE published = false
    │
    ├─→ Foreach message:
    │       ├─→ Format as Laravel job
    │       ├─→ Redis LPUSH('notifications', json_encode($job))
    │       └─→ UPDATE outbox_messages SET published = true
    │
    └─→ Log: "Published {count} messages"
```

### 3. Notification Processing

```
Notification Service Consumer
    ↓
Redis BRPOP('notifications', timeout=5)
    │
    ├─→ Receive message
    │
    ├─→ Parse event_type
    │       │
    │       ├─ OrderPlaced → sendOrderConfirmation()
    │       ├─ UserRegistered → sendWelcomeEmail()
    │       └─ PaymentSucceeded → sendPaymentConfirmation()
    │
    └─→ EmailSender::handleEvent()
            │
            ├─→ Build HTML template
            ├─→ Symfony Mailer send()
            └─→ Log: "Email sent to user@example.com"
```

---

## 🎯 Design Patterns Đã Áp Dụng

### 1. **Modular Monolith**
- Tách theo domain/bounded context
- Mỗi module độc lập về logic
- Chuẩn bị cho việc extract thành microservice

### 2. **Outbox Pattern**
- Đảm bảo message không bị mất
- Atomic transaction: DB write + Event publish
- Eventual consistency

### 3. **Event-Driven Architecture**
- Loose coupling giữa modules
- Async processing qua events
- Scalability & resilience

### 4. **Strangler Pattern**
- Tách dần từ monolith
- Không "big bang" rewrite
- Coexist old & new systems

### 5. **Saga Pattern** (Chuẩn bị)
- Orchestrate distributed transactions
- Compensation logic cho failures

---

## 📊 Database Design

### Outbox Messages Table

```sql
CREATE TABLE outbox_messages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    aggregate_type VARCHAR(255),      -- 'Transaction', 'Product', 'User'
    aggregate_id BIGINT,              -- Entity ID
    event_type VARCHAR(255),          -- 'OrderPlaced', 'UserRegistered'
    payload JSON,                     -- Event data
    occurred_at TIMESTAMP,            -- Event timestamp
    published BOOLEAN DEFAULT FALSE,  -- Published to queue?
    published_at TIMESTAMP NULL,      -- When published?
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_published_occurred (published, occurred_at),
    INDEX idx_aggregate (aggregate_type, aggregate_id)
);
```

### Event Payload Example

```json
{
    "transaction_id": 123,
    "user_id": 1,
    "user_email": "customer@example.com",
    "total": 500000,
    "payment_method": "momo",
    "order_details": [
        {
            "product_id": 10,
            "product_name": "iPhone 15 Pro",
            "quantity": 1,
            "price": 500000
        }
    ]
}
```

---

## 🔐 Security Considerations

### Web Chính
- ✅ CSRF protection (Laravel default)
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Authentication (Sanctum)
- ✅ Input validation

### Notification Service
- ✅ Redis authentication (if enabled)
- ✅ Email rate limiting (to prevent spam)
- ✅ Template sanitization
- 🔄 **TODO:** API authentication (khi expose REST API)

---

## 📈 Scalability Strategy

### Current (Monolith + 1 Microservice)

```
Load: 100 RPS
└─→ 1x Laravel instance (web)
└─→ 1x Queue worker
└─→ 1x Notification service
```

### Future (Scale Out)

```
Load: 1000+ RPS
├─→ 3x Laravel instances (behind load balancer)
├─→ 5x Queue workers (horizontal scaling)
└─→ 3x Notification service instances
    └─→ Redis acts as load balancer (BRPOP is atomic)
```

### Service Extraction Roadmap

**Phase 4:** Tách **Cart Service**
- Độc lập về database
- API Gateway routing

**Phase 5:** Tách **Payment Service**
- PCI DSS compliance
- Isolated payment processing

**Phase 6:** Tách **Catalog Service**
- Read-heavy optimization
- CDN integration
- Elasticsearch for search

---

## 🛠️ Technology Stack

### Web Chính
- **Framework:** Laravel 10
- **Language:** PHP 8.2
- **Database:** MySQL 8.0
- **Cache/Queue:** Redis 7.x
- **Modules:** nwidart/laravel-modules

### Notification Service
- **Language:** PHP 8.2
- **Queue:** Redis (Predis client)
- **Mailer:** Symfony Mailer
- **Logger:** Monolog
- **Config:** vlucas/phpdotenv

### Infrastructure
- **Message Broker:** Redis (future: RabbitMQ)
- **Email:** Gmail SMTP (future: SendGrid)
- **Monitoring:** Monolog → ELK Stack (planned)

---

## 🚀 Migration Path to Full Microservices

### Current State: **Modular Monolith + 1 Microservice**

```
[Web + All Modules] → [Redis] → [Notification Service]
     (Single DB)                      (No DB)
```

### Target State: **True Microservices**

```
[API Gateway (Kong/Nginx)]
    │
    ├─→ [Catalog Service]    (Postgres DB)
    ├─→ [Customer Service]   (Postgres DB)
    ├─→ [Cart Service]       (Redis)
    ├─→ [Order Service]      (MySQL DB)
    ├─→ [Payment Service]    (MySQL DB)
    └─→ [Notification]       (No DB)
            ↓
    [RabbitMQ/Kafka]
```

### Database per Service

- **Catalog DB:** Products, Categories
- **Customer DB:** Users, Profiles, Wishlist
- **Order DB:** Transactions, Orders
- **Payment DB:** Payment logs, transactions
- **Notification:** Stateless (no DB)

---

## 📝 Best Practices Đã Áp Dụng

✅ **Separation of Concerns** - Mỗi module có trách nhiệm rõ ràng  
✅ **Event Sourcing (Light)** - Outbox lưu lại event history  
✅ **Idempotency** - Email sender có thể retry an toàn  
✅ **Observability** - Structured logging (Monolog)  
✅ **Configuration Management** - .env files  
✅ **Documentation** - README cho từng service  

---

## 🔮 Future Enhancements

1. **API Gateway** (Kong, Tyk)
2. **Service Mesh** (Istio - nếu K8s)
3. **Distributed Tracing** (Jaeger)
4. **Circuit Breaker** (Resilience4j pattern)
5. **CQRS** cho Catalog (read replicas)
6. **Event Store** (EventStoreDB)
7. **GraphQL** API Gateway
8. **Kubernetes** deployment

---

**Architecture Version:** 1.0  
**Last Updated:** 2026-01-28  
**Status:** ✅ PRODUCTION READY
