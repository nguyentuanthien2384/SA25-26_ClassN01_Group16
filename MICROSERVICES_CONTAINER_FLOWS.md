# Hướng Dẫn Chi Tiết Luồng Chạy Containers Microservices

## Mục Lục
1. [Tổng Quan Kiến Trúc](#1-tổng-quan-kiến-trúc)
2. [Luồng Khởi Động](#2-luồng-khởi-động)
3. [Chi Tiết Từng Container](#3-chi-tiết-từng-container)
4. [Luồng Giao Tiếp Giữa Các Service](#4-luồng-giao-tiếp-giữa-các-service)
5. [Khi Dừng Container](#5-khi-dừng-container)
6. [Khi Có Lỗi](#6-khi-có-lỗi)
7. [Lệnh Quản Lý](#7-lệnh-quản-lý)

---

## 1. Tổng Quan Kiến Trúc

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              NGƯỜI DÙNG (Browser)                            │
└─────────────────────────────────────────────────────────────────────────────┘
                                       │
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         KONG API GATEWAY (:9000)                             │
│                    (Điểm vào duy nhất cho tất cả API)                        │
└─────────────────────────────────────────────────────────────────────────────┘
                                       │
         ┌─────────────────────────────┼─────────────────────────────┐
         ▼                             ▼                             ▼
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│ Catalog Service │         │  Order Service  │         │  User Service   │
│    (:9005)      │         │    (:9002)      │         │    (:9003)      │
└────────┬────────┘         └────────┬────────┘         └────────┬────────┘
         │                           │                           │
         ▼                           ▼                           ▼
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│  MySQL Catalog  │         │   MySQL Order   │         │   MySQL User    │
│    (:3310)      │         │    (:3311)      │         │    (:3312)      │
└─────────────────┘         └─────────────────┘         └─────────────────┘
         │                           │                           │
         └───────────────────────────┼───────────────────────────┘
                                     ▼
                    ┌─────────────────────────────────┐
                    │      RabbitMQ (Message Broker)  │
                    │           (:15672)              │
                    └─────────────────────────────────┘
                                     │
                                     ▼
                    ┌─────────────────────────────────┐
                    │    Notification Service (:9004) │
                    │    (Gửi Email qua MailHog)      │
                    └─────────────────────────────────┘
```

---

## 2. Luồng Khởi Động

### Thứ Tự Khởi Động (Quan Trọng!)

Các containers khởi động theo thứ tự phụ thuộc:

```
PHASE 1: Infrastructure (Hạ tầng)
├── mysql_catalog     ──┐
├── mysql_order       ──┼── Databases khởi động đầu tiên
├── mysql_user        ──┘
├── kong_database     ──── PostgreSQL cho Kong
├── ms_redis_cache    ──── Cache
├── consul_discovery  ──── Service Discovery
└── rabbitmq_broker   ──── Message Queue

PHASE 2: Gateway & Monitoring
├── kong_migration    ──── Chạy migrations cho Kong (tạm thời)
├── kong_gateway      ──── API Gateway
├── konga_gui         ──── Kong Admin UI
├── prometheus        ──── Thu thập metrics
├── grafana           ──── Dashboard
├── jaeger_tracing    ──── Distributed Tracing
└── mailhog           ──── Email Testing

PHASE 3: Business Services (Sau khi DB sẵn sàng)
├── catalog_service   ──── Quản lý sản phẩm
├── order_service     ──── Quản lý đơn hàng
├── user_service      ──── Quản lý người dùng
└── notification_service ── Gửi thông báo
```

### Health Check Flow

```
┌─────────────────┐    health check    ┌─────────────────┐
│   Docker        │ ───────────────►   │    Container    │
│   Engine        │ ◄───────────────   │                 │
└─────────────────┘    OK / FAIL       └─────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │ Nếu FAIL 3 lần  │
                    │ → Restart       │
                    │ container       │
                    └─────────────────┘
```

---

## 3. Chi Tiết Từng Container

### 3.1 Database Layer

#### MySQL Catalog (mysql_catalog)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: mysql_catalog                                     │
├─────────────────────────────────────────────────────────────┤
│ Port: 3310                                                   │
│ Database: catalog_db                                         │
│ User: root / Password: catalog_root_pass                     │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│ - Lưu trữ thông tin sản phẩm (products)                     │
│ - Lưu trữ danh mục (categories)                             │
│ - Lưu trữ thông tin kho (inventory)                         │
├─────────────────────────────────────────────────────────────┤
│ Health Check:                                                │
│ mysqladmin ping -h localhost -u root -p***                   │
│ Interval: 10s | Timeout: 5s | Retries: 5                    │
└─────────────────────────────────────────────────────────────┘
```

**Khi dừng mysql_catalog:**
```
❌ catalog_service KHÔNG thể:
   - Lấy danh sách sản phẩm
   - Tìm kiếm sản phẩm
   - Cập nhật thông tin sản phẩm
   
⚠️ Ảnh hưởng:
   - Trang chủ không hiển thị sản phẩm
   - Trang chi tiết sản phẩm lỗi 500
   - Giỏ hàng không thể thêm sản phẩm mới
```

#### MySQL Order (mysql_order)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: mysql_order                                       │
├─────────────────────────────────────────────────────────────┤
│ Port: 3311                                                   │
│ Database: order_db                                           │
│ User: root / Password: order_root_pass                       │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│ - Lưu trữ đơn hàng (orders)                                 │
│ - Lưu trữ chi tiết đơn hàng (order_items)                   │
│ - Lưu trữ giỏ hàng (carts)                                  │
│ - Lưu trữ giao dịch (transactions)                          │
└─────────────────────────────────────────────────────────────┘
```

**Khi dừng mysql_order:**
```
❌ order_service KHÔNG thể:
   - Tạo đơn hàng mới
   - Xem lịch sử đơn hàng
   - Cập nhật trạng thái đơn hàng
   
⚠️ Ảnh hưởng:
   - Checkout thất bại
   - Trang "Đơn hàng của tôi" lỗi
   - Admin không thể quản lý đơn hàng
```

#### MySQL User (mysql_user)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: mysql_user                                        │
├─────────────────────────────────────────────────────────────┤
│ Port: 3312                                                   │
│ Database: user_db                                            │
│ User: root / Password: user_root_pass                        │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│ - Lưu trữ thông tin người dùng                              │
│ - Lưu trữ địa chỉ giao hàng                                 │
│ - Quản lý authentication tokens                              │
└─────────────────────────────────────────────────────────────┘
```

**Khi dừng mysql_user:**
```
❌ user_service KHÔNG thể:
   - Đăng nhập / Đăng ký
   - Lấy thông tin profile
   - Cập nhật địa chỉ
   
⚠️ Ảnh hưởng:
   - Người dùng không thể đăng nhập
   - Session có thể bị mất
   - Tất cả chức năng yêu cầu auth bị lỗi
```

---

### 3.2 API Gateway Layer

#### Kong Gateway (kong_gateway)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: kong_gateway                                      │
├─────────────────────────────────────────────────────────────┤
│ Ports:                                                       │
│   - 9000: Proxy (Client gọi API qua đây)                    │
│   - 9001: Admin API (Cấu hình Kong)                         │
│   - 9443: Proxy SSL                                         │
│   - 9444: Admin API SSL                                     │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│ - Routing: Điều hướng request đến đúng service              │
│ - Load Balancing: Cân bằng tải                              │
│ - Rate Limiting: Giới hạn số request                        │
│ - Authentication: Xác thực JWT/API Key                      │
│ - CORS: Xử lý Cross-Origin requests                         │
│ - Logging: Ghi log tất cả requests                          │
└─────────────────────────────────────────────────────────────┘
```

**Luồng Request qua Kong:**
```
Browser                 Kong Gateway              Services
   │                         │                        │
   │  GET /api/products      │                        │
   │ ───────────────────────►│                        │
   │                         │                        │
   │                         │  1. Check Route        │
   │                         │  2. Check Auth         │
   │                         │  3. Check Rate Limit   │
   │                         │                        │
   │                         │  GET /api/products     │
   │                         │───────────────────────►│ Catalog
   │                         │                        │ Service
   │                         │◄───────────────────────│
   │                         │   {products: [...]}    │
   │                         │                        │
   │◄───────────────────────│                        │
   │   {products: [...]}     │                        │
```

**Khi dừng kong_gateway:**
```
❌ NGHIÊM TRỌNG - Toàn bộ API không thể truy cập:
   - Tất cả /api/* endpoints bị lỗi
   - Frontend không thể gọi backend
   
⚠️ Ảnh hưởng:
   - Website hoàn toàn không hoạt động
   - Đây là SINGLE POINT OF FAILURE
   
✅ Giải pháp Production:
   - Chạy multiple Kong instances
   - Đặt Load Balancer phía trước
```

---

### 3.3 Business Services Layer

#### Catalog Service (catalog_service)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: catalog_service                                   │
├─────────────────────────────────────────────────────────────┤
│ Port: 9005                                                   │
│ Framework: Laravel 10                                        │
├─────────────────────────────────────────────────────────────┤
│ API Endpoints:                                               │
│   GET  /api/products           - Danh sách sản phẩm         │
│   GET  /api/products/{id}      - Chi tiết sản phẩm          │
│   GET  /api/products/hot       - Sản phẩm hot               │
│   GET  /api/products/new       - Sản phẩm mới               │
│   GET  /api/categories         - Danh mục                    │
│   POST /api/products           - Tạo sản phẩm (Admin)       │
│   PUT  /api/products/{id}      - Cập nhật sản phẩm          │
├─────────────────────────────────────────────────────────────┤
│ Dependencies:                                                │
│   - mysql_catalog (Database)                                 │
│   - ms_redis_cache (Caching)                                │
│   - rabbitmq_broker (Events)                                │
└─────────────────────────────────────────────────────────────┘
```

**Luồng xử lý Request:**
```
Request: GET /api/products?category=1&page=2

┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│    Redis     │     │   Catalog    │     │    MySQL     │
│    Cache     │     │   Service    │     │   Catalog    │
└──────┬───────┘     └──────┬───────┘     └──────┬───────┘
       │                    │                    │
       │  1. Check Cache    │                    │
       │◄───────────────────│                    │
       │                    │                    │
       │  Cache MISS        │                    │
       │───────────────────►│                    │
       │                    │                    │
       │                    │  2. Query DB       │
       │                    │───────────────────►│
       │                    │                    │
       │                    │  3. Return Data    │
       │                    │◄───────────────────│
       │                    │                    │
       │  4. Store Cache    │                    │
       │◄───────────────────│                    │
       │                    │                    │
       │                    │  5. Return JSON    │
       │                    │─────────────────────────► Client
```

**Khi dừng catalog_service:**
```
❌ Không thể:
   - Hiển thị sản phẩm trên trang chủ
   - Tìm kiếm sản phẩm
   - Xem chi tiết sản phẩm
   
⚠️ Ảnh hưởng:
   - order_service không thể lấy thông tin sản phẩm
   - Giỏ hàng không hiển thị đúng thông tin
   
✅ Fallback:
   - order_service có thể dùng cached data
   - Frontend hiển thị "Đang tải..."
```

#### Order Service (order_service)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: order_service                                     │
├─────────────────────────────────────────────────────────────┤
│ Port: 9002                                                   │
│ Framework: Laravel 10                                        │
├─────────────────────────────────────────────────────────────┤
│ API Endpoints:                                               │
│   POST /api/cart/add           - Thêm vào giỏ hàng          │
│   GET  /api/cart               - Xem giỏ hàng               │
│   POST /api/orders             - Tạo đơn hàng               │
│   GET  /api/orders             - Danh sách đơn hàng         │
│   GET  /api/orders/{id}        - Chi tiết đơn hàng          │
│   PUT  /api/orders/{id}/status - Cập nhật trạng thái        │
├─────────────────────────────────────────────────────────────┤
│ Dependencies:                                                │
│   - mysql_order (Database)                                   │
│   - catalog_service (Lấy thông tin sản phẩm)                │
│   - user_service (Lấy thông tin user)                       │
│   - rabbitmq_broker (Publish events)                        │
└─────────────────────────────────────────────────────────────┘
```

**Luồng Tạo Đơn Hàng:**
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         LUỒNG TẠO ĐƠN HÀNG                                   │
└─────────────────────────────────────────────────────────────────────────────┘

Client          Order          Catalog        User           RabbitMQ      Notification
  │             Service        Service        Service           │            Service
  │                │              │              │               │               │
  │ POST /orders   │              │              │               │               │
  │───────────────►│              │              │               │               │
  │                │              │              │               │               │
  │                │ Get Products │              │               │               │
  │                │─────────────►│              │               │               │
  │                │◄─────────────│              │               │               │
  │                │              │              │               │               │
  │                │ Get User Info│              │               │               │
  │                │─────────────────────────────►│               │               │
  │                │◄─────────────────────────────│               │               │
  │                │              │              │               │               │
  │                │ Validate & Calculate        │               │               │
  │                │ ─────────────────────       │               │               │
  │                │              │              │               │               │
  │                │ Save to DB   │              │               │               │
  │                │ ──────────── │              │               │               │
  │                │              │              │               │               │
  │                │ Publish: order.created      │               │               │
  │                │──────────────────────────────────────────────►│               │
  │                │              │              │               │               │
  │                │              │              │               │ Consume Event │
  │                │              │              │               │◄──────────────│
  │                │              │              │               │               │
  │                │              │              │               │ Send Email    │
  │                │              │              │               │──────────────►│
  │                │              │              │               │               │
  │◄───────────────│              │              │               │               │
  │ Order Created  │              │              │               │               │
```

**Khi dừng order_service:**
```
❌ Không thể:
   - Thêm sản phẩm vào giỏ
   - Đặt hàng
   - Xem lịch sử đơn hàng
   
⚠️ Ảnh hưởng NGHIÊM TRỌNG:
   - Mất doanh thu
   - Khách hàng không thể mua hàng
   
✅ Mitigation:
   - Hiển thị thông báo "Hệ thống bảo trì"
   - Lưu giỏ hàng vào localStorage
```

#### User Service (user_service)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: user_service                                      │
├─────────────────────────────────────────────────────────────┤
│ Port: 9003                                                   │
│ Framework: Laravel 10                                        │
├─────────────────────────────────────────────────────────────┤
│ API Endpoints:                                               │
│   POST /api/auth/register      - Đăng ký                    │
│   POST /api/auth/login         - Đăng nhập                  │
│   POST /api/auth/logout        - Đăng xuất                  │
│   GET  /api/user/profile       - Thông tin user             │
│   PUT  /api/user/profile       - Cập nhật profile           │
│   GET  /api/user/addresses     - Danh sách địa chỉ          │
├─────────────────────────────────────────────────────────────┤
│ Security:                                                    │
│   - JWT Token Authentication                                 │
│   - Password Hashing (bcrypt)                               │
│   - Rate Limiting (login attempts)                          │
└─────────────────────────────────────────────────────────────┘
```

**Luồng Authentication:**
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         LUỒNG ĐĂNG NHẬP                                      │
└─────────────────────────────────────────────────────────────────────────────┘

Client              Kong               User Service           MySQL User
  │                  │                      │                      │
  │ POST /api/login  │                      │                      │
  │ {email, pass}    │                      │                      │
  │─────────────────►│                      │                      │
  │                  │                      │                      │
  │                  │ Forward to User Svc  │                      │
  │                  │─────────────────────►│                      │
  │                  │                      │                      │
  │                  │                      │ SELECT * FROM users  │
  │                  │                      │ WHERE email = ?      │
  │                  │                      │─────────────────────►│
  │                  │                      │◄─────────────────────│
  │                  │                      │                      │
  │                  │                      │ Verify Password      │
  │                  │                      │ (bcrypt compare)     │
  │                  │                      │                      │
  │                  │                      │ Generate JWT Token   │
  │                  │                      │                      │
  │                  │◄─────────────────────│                      │
  │                  │ {token: "eyJ..."}    │                      │
  │                  │                      │                      │
  │◄─────────────────│                      │                      │
  │ {token: "eyJ..."}│                      │                      │
  │                  │                      │                      │
  │                  │                      │                      │
  │ [Subsequent Requests with Token]        │                      │
  │                  │                      │                      │
  │ GET /api/profile │                      │                      │
  │ Header: Bearer eyJ...                   │                      │
  │─────────────────►│                      │                      │
  │                  │ Validate JWT         │                      │
  │                  │─────────────────────►│                      │
  │                  │                      │                      │
```

**Khi dừng user_service:**
```
❌ Không thể:
   - Đăng nhập / Đăng ký
   - Xem/Sửa profile
   - Xác thực token
   
⚠️ Ảnh hưởng:
   - Người dùng mới không thể tạo tài khoản
   - Người dùng đã login có thể vẫn dùng được (nếu token valid)
   - order_service không thể verify user
```

#### Notification Service (notification_service)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: notification_service                              │
├─────────────────────────────────────────────────────────────┤
│ Port: 9004                                                   │
│ Type: Background Worker (không phải HTTP server)            │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│   - Lắng nghe events từ RabbitMQ                            │
│   - Gửi email xác nhận đơn hàng                             │
│   - Gửi email khuyến mãi                                    │
│   - Gửi thông báo push (nếu có)                             │
├─────────────────────────────────────────────────────────────┤
│ Events được xử lý:                                           │
│   - order.created → Gửi email xác nhận                      │
│   - order.shipped → Gửi email tracking                      │
│   - user.registered → Gửi email chào mừng                   │
│   - payment.failed → Gửi email thông báo                    │
└─────────────────────────────────────────────────────────────┘
```

**Luồng Event-Driven:**
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    LUỒNG XỬ LÝ EVENT (Asynchronous)                          │
└─────────────────────────────────────────────────────────────────────────────┘

Order Service           RabbitMQ              Notification Service      MailHog
     │                      │                         │                    │
     │ Publish Event        │                         │                    │
     │ {                    │                         │                    │
     │   event: "order.created",                      │                    │
     │   order_id: 123,     │                         │                    │
     │   user_email: "..."  │                         │                    │
     │ }                    │                         │                    │
     │─────────────────────►│                         │                    │
     │                      │                         │                    │
     │                      │ Queue: notifications    │                    │
     │                      │ ──────────────────────  │                    │
     │                      │                         │                    │
     │                      │ Deliver to Consumer     │                    │
     │                      │────────────────────────►│                    │
     │                      │                         │                    │
     │                      │                         │ Process Event      │
     │                      │                         │ ─────────────      │
     │                      │                         │                    │
     │                      │                         │ Send SMTP Email    │
     │                      │                         │───────────────────►│
     │                      │                         │                    │
     │                      │                         │ ACK (Acknowledge)  │
     │                      │◄────────────────────────│                    │
     │                      │                         │                    │
```

**Khi dừng notification_service:**
```
❌ Không thể:
   - Gửi email xác nhận đơn hàng
   - Gửi thông báo
   
⚠️ Ảnh hưởng (KHÔNG nghiêm trọng):
   - Messages được lưu trong RabbitMQ queue
   - Khi service restart, messages sẽ được xử lý
   - Đơn hàng vẫn được tạo thành công
   
✅ Đây là ưu điểm của Event-Driven Architecture:
   - Decoupling giữa order và notification
   - Fault tolerance
```

---

### 3.4 Message Broker & Cache Layer

#### RabbitMQ (rabbitmq_broker)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: rabbitmq_broker                                   │
├─────────────────────────────────────────────────────────────┤
│ Ports:                                                       │
│   - 5672: AMQP Protocol (Services connect)                  │
│   - 15672: Management UI                                     │
├─────────────────────────────────────────────────────────────┤
│ Credentials:                                                 │
│   - Username: admin                                          │
│   - Password: admin123                                       │
│   - VHost: electroshop                                       │
├─────────────────────────────────────────────────────────────┤
│ Queues:                                                      │
│   - notifications: Email notifications                       │
│   - order_events: Order-related events                      │
│   - inventory_updates: Stock updates                        │
├─────────────────────────────────────────────────────────────┤
│ Exchanges:                                                   │
│   - orders (topic): Routing order events                    │
│   - users (topic): Routing user events                      │
│   - notifications (fanout): Broadcast notifications         │
└─────────────────────────────────────────────────────────────┘
```

**Message Flow:**
```
Producer                   Exchange                    Queue                 Consumer
(Order Svc)                   │                          │                  (Notify Svc)
    │                         │                          │                       │
    │ Publish Message         │                          │                       │
    │ routing_key:            │                          │                       │
    │ "order.created"         │                          │                       │
    │────────────────────────►│                          │                       │
    │                         │                          │                       │
    │                         │ Route based on           │                       │
    │                         │ binding key              │                       │
    │                         │─────────────────────────►│                       │
    │                         │                          │                       │
    │                         │                          │ Deliver               │
    │                         │                          │──────────────────────►│
    │                         │                          │                       │
    │                         │                          │ ACK                   │
    │                         │                          │◄──────────────────────│
```

**Khi dừng rabbitmq_broker:**
```
❌ NGHIÊM TRỌNG:
   - Services không thể gửi events
   - Notification service không nhận messages
   
⚠️ Ảnh hưởng:
   - order_service: publish fails → có thể retry hoặc log
   - notification_service: không có gì để xử lý
   
✅ Mitigation trong Production:
   - RabbitMQ Cluster (3 nodes)
   - Persistent queues
   - Dead letter queues
```

#### Redis Cache (ms_redis_cache)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: ms_redis_cache                                    │
├─────────────────────────────────────────────────────────────┤
│ Port: 6381                                                   │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│   - Session Storage                                          │
│   - Query Caching                                            │
│   - Rate Limiting Counter                                    │
│   - Real-time Data                                           │
├─────────────────────────────────────────────────────────────┤
│ Cache Keys Pattern:                                          │
│   - products:all → Cached product list                      │
│   - products:{id} → Single product                          │
│   - categories:all → Category list                          │
│   - user:session:{token} → User session                     │
│   - rate_limit:{ip} → Request counter                       │
└─────────────────────────────────────────────────────────────┘
```

**Khi dừng ms_redis_cache:**
```
⚠️ Ảnh hưởng:
   - Cache miss → Query trực tiếp DB (chậm hơn)
   - Sessions có thể bị mất
   - Rate limiting không hoạt động
   
✅ Services vẫn hoạt động nhưng:
   - Response time tăng
   - DB load tăng
   - Có thể bị overload nếu traffic cao
```

---

### 3.5 Service Discovery & Monitoring

#### Consul (consul_discovery)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: consul_discovery                                  │
├─────────────────────────────────────────────────────────────┤
│ Ports:                                                       │
│   - 8500: HTTP API & UI                                     │
│   - 8600: DNS Interface                                     │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│   - Service Registration                                     │
│   - Health Checking                                          │
│   - Service Discovery                                        │
│   - Key/Value Store                                         │
├─────────────────────────────────────────────────────────────┤
│ Registered Services:                                         │
│   - catalog-service (healthy/unhealthy)                     │
│   - order-service (healthy/unhealthy)                       │
│   - user-service (healthy/unhealthy)                        │
└─────────────────────────────────────────────────────────────┘
```

**Service Discovery Flow:**
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      SERVICE DISCOVERY FLOW                                  │
└─────────────────────────────────────────────────────────────────────────────┘

Service A                     Consul                         Service B
    │                           │                               │
    │ 1. Register               │                               │
    │ {name: "catalog-service", │                               │
    │  host: "10.0.0.5",        │                               │
    │  port: 9005}              │                               │
    │──────────────────────────►│                               │
    │                           │                               │
    │                           │ 2. Health Check (every 10s)   │
    │                           │──────────────────────────────►│
    │                           │◄──────────────────────────────│
    │                           │ OK                            │
    │                           │                               │
    │                           │                               │
    │ 3. Discover "order-svc"   │                               │
    │──────────────────────────►│                               │
    │                           │                               │
    │◄──────────────────────────│                               │
    │ [{host: "10.0.0.6",       │                               │
    │   port: 9002,             │                               │
    │   status: "healthy"}]     │                               │
    │                           │                               │
    │ 4. Direct call            │                               │
    │───────────────────────────────────────────────────────────►│
```

**Khi dừng consul_discovery:**
```
⚠️ Ảnh hưởng:
   - Không thể discover services mới
   - Không có health check centralized
   
✅ Trong setup này:
   - Kong đã cấu hình static routes
   - Services có thể hoạt động mà không cần Consul
   - Consul chủ yếu dùng cho monitoring
```

#### Prometheus (prometheus)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: prometheus                                        │
├─────────────────────────────────────────────────────────────┤
│ Port: 9090                                                   │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│   - Thu thập metrics từ tất cả services                     │
│   - Lưu trữ time-series data                                │
│   - Alert rules                                              │
├─────────────────────────────────────────────────────────────┤
│ Scrape Targets:                                              │
│   - catalog-service:9005/metrics                            │
│   - order-service:9002/metrics                              │
│   - user-service:9003/metrics                               │
│   - kong:8001/metrics                                        │
│   - redis:6379 (via exporter)                               │
└─────────────────────────────────────────────────────────────┘
```

**Metrics Flow:**
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         METRICS COLLECTION FLOW                              │
└─────────────────────────────────────────────────────────────────────────────┘

Services                    Prometheus                      Grafana
    │                           │                              │
    │ Expose /metrics           │                              │
    │ ─────────────────         │                              │
    │                           │                              │
    │                           │ Scrape (every 15s)           │
    │◄──────────────────────────│                              │
    │                           │                              │
    │ {                         │                              │
    │   http_requests_total,    │                              │
    │   response_time_seconds,  │                              │
    │   error_rate,             │                              │
    │   ...                     │                              │
    │ }                         │                              │
    │──────────────────────────►│                              │
    │                           │                              │
    │                           │ Store in TSDB               │
    │                           │ ──────────────               │
    │                           │                              │
    │                           │                              │
    │                           │ Query: PromQL                │
    │                           │◄─────────────────────────────│
    │                           │                              │
    │                           │ Data for visualization       │
    │                           │─────────────────────────────►│
```

#### Grafana (grafana)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: grafana                                           │
├─────────────────────────────────────────────────────────────┤
│ Port: 3000                                                   │
│ Credentials: admin / admin123                                │
├─────────────────────────────────────────────────────────────┤
│ Dashboards:                                                  │
│   - System Overview                                          │
│   - API Metrics                                              │
│   - Database Metrics                                         │
│   - Error Rates                                              │
│   - Response Times                                           │
└─────────────────────────────────────────────────────────────┘
```

#### Jaeger (jaeger_tracing)
```
┌─────────────────────────────────────────────────────────────┐
│ Container: jaeger_tracing                                    │
├─────────────────────────────────────────────────────────────┤
│ Ports:                                                       │
│   - 16686: UI                                               │
│   - 6831: UDP (agents send traces)                          │
│   - 14268: HTTP (direct trace submission)                   │
├─────────────────────────────────────────────────────────────┤
│ Chức năng:                                                   │
│   - Distributed Tracing                                      │
│   - Request flow visualization                               │
│   - Latency analysis                                         │
│   - Root cause analysis                                      │
└─────────────────────────────────────────────────────────────┘
```

**Tracing Flow:**
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      DISTRIBUTED TRACING FLOW                                │
└─────────────────────────────────────────────────────────────────────────────┘

                     TraceID: abc123
                     ────────────────────────────────────────────────────────►

Request             Kong              Catalog           Order             Jaeger
   │                  │                  │                 │                  │
   │  SpanID: 001     │                  │                 │                  │
   │─────────────────►│                  │                 │                  │
   │                  │                  │                 │                  │
   │                  │  SpanID: 002     │                 │                  │
   │                  │  ParentID: 001   │                 │                  │
   │                  │─────────────────►│                 │                  │
   │                  │                  │                 │                  │
   │                  │                  │  SpanID: 003    │                  │
   │                  │                  │  ParentID: 002  │                  │
   │                  │                  │────────────────►│                  │
   │                  │                  │                 │                  │
   │                  │                  │                 │                  │
   │                  │                  │  Send spans     │                  │
   │                  │                  │─────────────────────────────────────►│
   │                  │  Send spans      │                 │                  │
   │                  │─────────────────────────────────────────────────────────►│
   │  Send spans      │                  │                 │                  │
   │─────────────────────────────────────────────────────────────────────────────►│
```

---

## 4. Luồng Giao Tiếp Giữa Các Service

### 4.1 Synchronous Communication (HTTP/REST)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SYNCHRONOUS COMMUNICATION                                 │
│                    (Request-Response Pattern)                                │
└─────────────────────────────────────────────────────────────────────────────┘

Service A                                                              Service B
    │                                                                      │
    │  HTTP Request                                                        │
    │  GET /api/products/123                                               │
    │─────────────────────────────────────────────────────────────────────►│
    │                                                                      │
    │  ┌─────────────────────────────────────────────────────────────────┐ │
    │  │ Service A WAITS until Service B responds                        │ │
    │  │ (Blocking)                                                       │ │
    │  └─────────────────────────────────────────────────────────────────┘ │
    │                                                                      │
    │  HTTP Response                                                       │
    │  {id: 123, name: "Product", ...}                                     │
    │◄─────────────────────────────────────────────────────────────────────│
    │                                                                      │

Ưu điểm:
  ✓ Đơn giản, dễ implement
  ✓ Response ngay lập tức
  ✓ Dễ debug

Nhược điểm:
  ✗ Tight coupling
  ✗ Nếu Service B down → Service A cũng bị ảnh hưởng
  ✗ Latency cộng dồn
```

### 4.2 Asynchronous Communication (Message Queue)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    ASYNCHRONOUS COMMUNICATION                                │
│                    (Event-Driven Pattern)                                    │
└─────────────────────────────────────────────────────────────────────────────┘

Service A              RabbitMQ                                     Service B
    │                      │                                            │
    │  Publish Event       │                                            │
    │  {event: "order.created", data: {...}}                            │
    │─────────────────────►│                                            │
    │                      │                                            │
    │  ACK (Message stored)│                                            │
    │◄─────────────────────│                                            │
    │                      │                                            │
    │  ┌───────────────────┴───────────────────────────────────────┐    │
    │  │ Service A continues immediately                           │    │
    │  │ (Non-blocking)                                            │    │
    │  └───────────────────┬───────────────────────────────────────┘    │
    │                      │                                            │
    │                      │  (Later, when Service B is ready)          │
    │                      │                                            │
    │                      │  Deliver Message                           │
    │                      │───────────────────────────────────────────►│
    │                      │                                            │
    │                      │  ACK (Processed)                           │
    │                      │◄───────────────────────────────────────────│
    │                      │                                            │

Ưu điểm:
  ✓ Loose coupling
  ✓ Service B có thể down → Messages được buffer
  ✓ Better scalability
  ✓ Retry mechanism built-in

Nhược điểm:
  ✗ Phức tạp hơn
  ✗ Eventual consistency
  ✗ Khó debug
```

---

## 5. Khi Dừng Container

### 5.1 Tổng Quan Impact Matrix

```
┌─────────────────────┬─────────────────────────────────────────────────────────┐
│ Container Dừng      │ Ảnh Hưởng                                               │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ kong_gateway        │ ❌ CRITICAL: Toàn bộ API không thể truy cập            │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ mysql_catalog       │ ❌ HIGH: Không hiển thị sản phẩm                        │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ mysql_order         │ ❌ HIGH: Không thể đặt hàng                             │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ mysql_user          │ ❌ HIGH: Không thể đăng nhập                            │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ catalog_service     │ ⚠️ MEDIUM: Trang sản phẩm lỗi                          │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ order_service       │ ⚠️ MEDIUM: Checkout lỗi                                │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ user_service        │ ⚠️ MEDIUM: Auth lỗi (cached tokens có thể vẫn work)    │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ notification_service│ ℹ️ LOW: Email không gửi, messages được buffer          │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ rabbitmq_broker     │ ⚠️ MEDIUM: Events không được publish                   │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ ms_redis_cache      │ ⚠️ MEDIUM: Performance giảm, DB load tăng              │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ prometheus          │ ℹ️ LOW: Không có metrics mới                           │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ grafana             │ ℹ️ LOW: Dashboard không xem được                       │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ jaeger_tracing      │ ℹ️ LOW: Không có traces mới                            │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ consul_discovery    │ ℹ️ LOW: Service discovery không hoạt động              │
├─────────────────────┼─────────────────────────────────────────────────────────┤
│ mailhog             │ ℹ️ LOW: Emails không nhận được (dev only)              │
└─────────────────────┴─────────────────────────────────────────────────────────┘
```

### 5.2 Graceful Shutdown Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       GRACEFUL SHUTDOWN PROCESS                              │
└─────────────────────────────────────────────────────────────────────────────┘

docker stop container_name
        │
        ▼
┌───────────────────┐
│ SIGTERM sent      │
│ to main process   │
└─────────┬─────────┘
          │
          ▼
┌───────────────────────────────────────────────────────────────────────────┐
│ Application receives signal and starts graceful shutdown:                  │
│                                                                            │
│ 1. Stop accepting new requests                                             │
│ 2. Finish processing current requests (wait for completion)                │
│ 3. Close database connections properly                                     │
│ 4. Flush any pending writes to disk                                        │
│ 5. Deregister from service discovery (Consul)                              │
│ 6. Send remaining messages to queue                                        │
│ 7. Close queue connections                                                 │
│ 8. Exit with code 0                                                        │
└─────────┬─────────────────────────────────────────────────────────────────┘
          │
          ▼ (after timeout, typically 10-30 seconds)
┌───────────────────┐
│ If still running: │
│ SIGKILL sent      │
│ (Force kill)      │
└───────────────────┘
```

### 5.3 Dependency Chain When Stopping

```
Khi dừng mysql_catalog:
                                    
     ┌──────────────────┐           
     │  mysql_catalog   │  STOP     
     └────────┬─────────┘           
              │                      
              ▼                      
     ┌──────────────────┐           
     │ catalog_service  │  ERROR    
     │ "Connection      │           
     │  refused"        │           
     └────────┬─────────┘           
              │                      
              ▼                      
     ┌──────────────────┐           
     │  order_service   │  DEGRADED 
     │ "Cannot get      │           
     │  product info"   │           
     └────────┬─────────┘           
              │                      
              ▼                      
     ┌──────────────────┐           
     │   Frontend       │  PARTIAL  
     │ "Products not    │           
     │  loading"        │           
     └──────────────────┘           
```

---

## 6. Khi Có Lỗi

### 6.1 Các Loại Lỗi Thường Gặp

#### A. Connection Errors
```
┌─────────────────────────────────────────────────────────────────────────────┐
│ ERROR: SQLSTATE[HY000] [2002] Connection refused                            │
├─────────────────────────────────────────────────────────────────────────────┤
│ Nguyên nhân:                                                                 │
│   - Database container chưa khởi động                                       │
│   - Database container đã dừng                                              │
│   - Network issue giữa containers                                           │
│   - Sai hostname/port                                                        │
├─────────────────────────────────────────────────────────────────────────────┤
│ Giải pháp:                                                                   │
│   1. Kiểm tra container: docker ps | grep mysql                             │
│   2. Kiểm tra logs: docker logs mysql_catalog                               │
│   3. Kiểm tra network: docker network inspect ms_network                    │
│   4. Restart container: docker restart mysql_catalog                        │
└─────────────────────────────────────────────────────────────────────────────┘
```

#### B. Timeout Errors
```
┌─────────────────────────────────────────────────────────────────────────────┐
│ ERROR: cURL error 28: Operation timed out after 30000 milliseconds          │
├─────────────────────────────────────────────────────────────────────────────┤
│ Nguyên nhân:                                                                 │
│   - Service bị overload                                                     │
│   - Database query quá chậm                                                 │
│   - Network congestion                                                       │
│   - Deadlock                                                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│ Giải pháp:                                                                   │
│   1. Check service health: docker logs <service>                            │
│   2. Check resource usage: docker stats                                     │
│   3. Check database: SHOW PROCESSLIST                                       │
│   4. Scale service nếu cần                                                  │
└─────────────────────────────────────────────────────────────────────────────┘
```

#### C. Memory/Resource Errors
```
┌─────────────────────────────────────────────────────────────────────────────┐
│ ERROR: Container killed - OOMKilled                                          │
├─────────────────────────────────────────────────────────────────────────────┤
│ Nguyên nhân:                                                                 │
│   - Container sử dụng quá nhiều memory                                      │
│   - Memory leak trong application                                           │
│   - Không đủ RAM trên host                                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│ Giải pháp:                                                                   │
│   1. Tăng memory limit trong docker-compose                                 │
│   2. Tối ưu application code                                                │
│   3. Thêm RAM cho host machine                                              │
│   4. Sử dụng swap                                                           │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 6.2 Error Propagation Pattern

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        ERROR PROPAGATION                                     │
└─────────────────────────────────────────────────────────────────────────────┘

                    Database Error
                         │
                         ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│ mysql_catalog: ERROR 1040 - Too many connections                             │
└──────────────────────────────────────────────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│ catalog_service: PDOException - Connection failed                            │
│                                                                              │
│ Error Handling:                                                              │
│ try {                                                                        │
│     $products = Product::all();                                              │
│ } catch (PDOException $e) {                                                  │
│     Log::error('Database error: ' . $e->getMessage());                       │
│     return response()->json(['error' => 'Service unavailable'], 503);        │
│ }                                                                            │
└──────────────────────────────────────────────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│ Kong Gateway: Receives 503 from upstream                                     │
│                                                                              │
│ Circuit Breaker (if configured):                                             │
│ - After 5 consecutive failures → Circuit OPEN                               │
│ - Return cached response or error immediately                                │
│ - After 30s → Try again (Circuit HALF-OPEN)                                 │
└──────────────────────────────────────────────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│ Frontend: Receives error response                                            │
│                                                                              │
│ User sees:                                                                   │
│ ┌────────────────────────────────────────────┐                              │
│ │  ⚠️ Không thể tải sản phẩm                 │                              │
│ │     Vui lòng thử lại sau                   │                              │
│ │     [Thử lại]                              │                              │
│ └────────────────────────────────────────────┘                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

### 6.3 Recovery Patterns

#### Automatic Recovery (Docker Restart Policy)
```yaml
# docker-compose.yml
services:
  catalog-service:
    restart: unless-stopped
    # Restart policies:
    # - "no": Never restart (default)
    # - "always": Always restart
    # - "on-failure": Restart only on failure
    # - "unless-stopped": Restart unless manually stopped
```

#### Health Check Based Recovery
```yaml
services:
  mysql-catalog:
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s    # Check every 10 seconds
      timeout: 5s      # Wait 5 seconds for response
      retries: 5       # Fail after 5 consecutive failures
      start_period: 30s # Wait 30s before first check
```

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        HEALTH CHECK FLOW                                     │
└─────────────────────────────────────────────────────────────────────────────┘

     Time: 0s        10s       20s       30s       40s       50s
           │          │         │         │         │         │
           ▼          ▼         ▼         ▼         ▼         ▼
        ┌─────┐   ┌─────┐   ┌─────┐   ┌─────┐   ┌─────┐   ┌─────┐
        │ ✓   │   │ ✓   │   │ ✗   │   │ ✗   │   │ ✗   │   │ ✗   │
        │Check│   │Check│   │Check│   │Check│   │Check│   │Check│
        └─────┘   └─────┘   └─────┘   └─────┘   └─────┘   └─────┘
           │                   │         │         │         │
           │                   └─────────┴─────────┴─────────┘
           │                             │
           ▼                             ▼
      Container                    5 consecutive
       HEALTHY                     failures
                                        │
                                        ▼
                              ┌─────────────────────┐
                              │ Container UNHEALTHY │
                              │ Docker may restart  │
                              │ or alert            │
                              └─────────────────────┘
```

---

## 7. Lệnh Quản Lý

### 7.1 Xem Trạng Thái

```powershell
# Xem tất cả containers đang chạy
docker ps

# Xem tất cả containers (kể cả đã dừng)
docker ps -a

# Xem chi tiết một container
docker inspect <container_name>

# Xem resource usage
docker stats

# Xem logs của một service
docker logs <container_name>

# Xem logs realtime
docker logs -f <container_name>

# Xem logs 100 dòng cuối
docker logs --tail 100 <container_name>
```

### 7.2 Quản Lý Containers

```powershell
# Dừng một container
docker stop <container_name>

# Dừng tất cả containers của project
docker-compose -f docker-compose.microservices.yml stop

# Khởi động lại một container
docker restart <container_name>

# Khởi động tất cả
docker-compose -f docker-compose.microservices.yml start

# Dừng và xóa tất cả
docker-compose -f docker-compose.microservices.yml down

# Dừng, xóa và xóa cả volumes (DATA SẼ MẤT!)
docker-compose -f docker-compose.microservices.yml down -v
```

### 7.3 Debug Commands

```powershell
# Truy cập vào container
docker exec -it <container_name> bash

# Chạy lệnh trong container
docker exec <container_name> php artisan cache:clear

# Kiểm tra network
docker network ls
docker network inspect ms_network

# Kiểm tra volumes
docker volume ls

# Xem events
docker events

# Kiểm tra health status
docker inspect --format='{{.State.Health.Status}}' <container_name>
```

### 7.4 Troubleshooting Workflow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      TROUBLESHOOTING WORKFLOW                                │
└─────────────────────────────────────────────────────────────────────────────┘

1. XÁC ĐỊNH VẤN ĐỀ
   │
   ├── docker ps -a                      # Xem container nào đang chạy/dừng
   ├── docker logs <container>           # Xem logs để tìm error
   └── docker stats                      # Xem resource usage
   
2. PHÂN TÍCH
   │
   ├── Nếu container restart liên tục:
   │   └── docker logs --tail 100 <container>
   │
   ├── Nếu connection refused:
   │   ├── docker network inspect ms_network
   │   └── docker exec <container> ping <target_host>
   │
   └── Nếu timeout:
       └── docker stats   # Check CPU/Memory
   
3. KHẮC PHỤC
   │
   ├── Container lỗi:
   │   └── docker restart <container>
   │
   ├── Cần rebuild:
   │   └── docker-compose -f docker-compose.microservices.yml up -d --build <service>
   │
   └── Cần reset hoàn toàn:
       └── docker-compose -f docker-compose.microservices.yml down -v
           docker-compose -f docker-compose.microservices.yml up -d --build
   
4. XÁC NHẬN
   │
   ├── docker ps                         # Tất cả containers running
   ├── docker logs <container>           # Không có errors mới
   └── curl http://localhost:9000/health # API hoạt động
```

---

## Tóm Tắt

### Các Container Quan Trọng Nhất (Không được dừng)
1. **kong_gateway** - API Gateway (điểm vào duy nhất)
2. **mysql_*** - Databases (lưu trữ dữ liệu)
3. **catalog/order/user_service** - Business logic

### Các Container Có Thể Dừng Tạm Thời
1. **notification_service** - Messages sẽ được buffer
2. **prometheus/grafana/jaeger** - Chỉ monitoring
3. **mailhog** - Development tool

### Recovery Time
| Loại Lỗi | Thời Gian Recovery |
|----------|-------------------|
| Container restart | 10-30 giây |
| Database connection | 30-60 giây |
| Full system restart | 2-5 phút |

---

*Tài liệu được tạo: Ngày 28/01/2026*
