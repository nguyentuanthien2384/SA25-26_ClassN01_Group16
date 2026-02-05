# So Sánh Containers Cũ (Monolith) vs Containers Mới (Microservices)

## 📊 Tổng Quan

| Đặc điểm | Containers Cũ (Monolith) | Containers Mới (Microservices) |
|----------|--------------------------|-------------------------------|
| **Tổng số containers** | **5 containers** | **20 containers** |
| **Kiến trúc** | Monolithic | Microservices |
| **Database** | 1 MySQL duy nhất | 3 MySQL riêng biệt + 1 PostgreSQL |
| **Application** | 1 Laravel app | 4 microservices độc lập |
| **Ports** | 4 ports | 15+ ports |

---

## 📦 Chi Tiết Containers

### 🔴 **CONTAINERS CŨ (5 containers) - Kiến Trúc Monolith**

#### 1️⃣ **Database Layer (1 container)**
- `electroshop_mysql` (MySQL 8.0)
  - Port: `3308:3306`
  - **1 database duy nhất** chứa TẤT CẢ dữ liệu
  - Database name: `duan`

#### 2️⃣ **Cache Layer (1 container)**
- `electroshop_redis` (Redis 7-alpine)
  - Port: `6380:6379`
  - Cache, session, queue

#### 3️⃣ **Application Layer (1 container)**
- `electroshop_app` (Laravel)
  - Port: `8000:8000`
  - **TOÀN BỘ ứng dụng trong 1 container**
  - Xử lý tất cả: Users, Products, Orders, Admin, etc.

#### 4️⃣ **Management Tools (2 containers)**
- `electroshop_phpmyadmin` (phpMyAdmin)
  - Port: `8083:80`
- `electroshop_redis_commander` (Redis Commander)
  - Port: `8082:8081`

---

### 🟢 **CONTAINERS MỚI (20 containers) - Kiến Trúc Microservices**

#### 1️⃣ **API Gateway Layer (4 containers)**
- `kong_database` (PostgreSQL 13-alpine)
  - Kong yêu cầu PostgreSQL (không dùng MySQL)
  - Internal port: `5432`
  
- `kong_migration` (Kong 3.4)
  - **One-time job** - chạy xong tự tắt
  - Setup Kong database schema
  
- `kong_gateway` (Kong 3.4)
  - Proxy Port: `9000:8000`
  - Admin API: `9001:8001`
  - SSL Proxy: `9443:8443`
  - SSL Admin: `9444:8444`
  - **Routing, Rate Limiting, Authentication**
  
- `konga_gui` (Konga)
  - Port: `1337:1337`
  - Kong Admin GUI

#### 2️⃣ **Message Broker (1 container)**
- `rabbitmq_broker` (RabbitMQ 3-management-alpine)
  - AMQP Port: `5672:5672`
  - Management UI: `15672:15672`
  - **Asynchronous communication** giữa các services

#### 3️⃣ **Service Discovery (1 container)**
- `consul_discovery` (Consul 1.15)
  - HTTP API: `8500:8500`
  - DNS: `8600:8600`
  - **Service registration & health checks**

#### 4️⃣ **Distributed Tracing (1 container)**
- `jaeger_tracing` (Jaeger All-in-One 1.50)
  - UI: `16686:16686`
  - Collector: `14268:14268`, `14250:14250`
  - Agent: `6831-6832:6831-6832/udp`, `5778:5778`
  - Zipkin: `9411:9411`
  - **Trace requests across microservices**

#### 5️⃣ **Monitoring & Metrics (2 containers)**
- `prometheus` (Prometheus v2.47.0)
  - Port: `9090:9090`
  - **Metrics collection**
  
- `grafana` (Grafana 10.1.0)
  - Port: `3000:3000`
  - **Metrics visualization & dashboards**

#### 6️⃣ **Database Layer (4 containers - Database per Service pattern)**
- `mysql_catalog` (MySQL 8.0)
  - Port: `3310:3306`
  - Database: `catalog_db`
  - **CHỈ cho Catalog Service**
  - User: root / catalog_root_pass
  
- `mysql_order` (MySQL 8.0)
  - Port: `3311:3306`
  - Database: `order_db`
  - **CHỈ cho Order Service**
  - User: root / order_root_pass
  
- `mysql_user` (MySQL 8.0)
  - Port: `3312:3306`
  - Database: `user_db`
  - **CHỈ cho User Service**
  - User: root / user_root_pass

- `ms_redis_cache` (Redis 7-alpine)
  - Port: `6381:6379`
  - **Shared cache** cho tất cả microservices

#### 7️⃣ **Application Services (4 microservices)**
- `catalog_service`
  - Port: `9005:9005`
  - **Chức năng:**
    - Quản lý sản phẩm (products)
    - Quản lý danh mục (categories)
    - Quản lý nhà cung cấp (suppliers)
    - Inventory management
  - Kết nối: `mysql_catalog`, `redis`, `rabbitmq`, `consul`, `jaeger`
  
- `order_service`
  - Port: `9002:9002`
  - **Chức năng:**
    - Quản lý đơn hàng (orders/transactions)
    - Quản lý giỏ hàng (carts)
    - Payment processing
    - Order history
  - Kết nối: `mysql_order`, `redis`, `rabbitmq`, `consul`, `jaeger`
  - **Gọi API `catalog_service`** để lấy thông tin sản phẩm
  
- `user_service`
  - Port: `9003:9003`
  - **Chức năng:**
    - Quản lý người dùng (users)
    - Quản lý admin
    - Authentication & Authorization
    - User profiles, wishlist, ratings
  - Kết nối: `mysql_user`, `redis`, `rabbitmq`, `consul`, `jaeger`
  
- `notification_service`
  - Port: `9004:9004`
  - **Chức năng:**
    - Gửi email thông báo
    - Lắng nghe RabbitMQ queue
    - Event-driven notifications (order created, payment success, etc.)
  - Kết nối: `redis`, `rabbitmq`, `mailhog`

#### 8️⃣ **Email Testing (1 container)**
- `mailhog` (MailHog)
  - SMTP: `1025:1025`
  - Web UI: `8025:8025`
  - **Bắt và hiển thị email test**

#### 9️⃣ **Management Tools (2 containers)**
- `ms_phpmyadmin` (phpMyAdmin)
  - Port: `9083:80`
  - Quản lý TẤT CẢ MySQL databases
  
- `ms_redis_commander` (Redis Commander)
  - Port: `9082:8081`
  - Quản lý Redis cache

---

## 🔄 So Sánh Kiến Trúc

### **Containers Cũ (Monolith)**
```
┌─────────────────────────────────────────┐
│         LARAVEL APP (Port 8000)         │
│                                         │
│  ┌──────────┐ ┌──────────┐ ┌─────────┐│
│  │  Users   │ │ Products │ │ Orders  ││
│  │  Module  │ │  Module  │ │ Module  ││
│  └──────────┘ └──────────┘ └─────────┘│
│                                         │
│  All modules in ONE container          │
└─────────────────┬───────────────────────┘
                  │
                  ↓
        ┌─────────────────┐
        │  MySQL (3308)   │
        │ ONE DATABASE    │
        │  "duan"         │
        └─────────────────┘
```

**Vấn đề:**
- ❌ Không scale được từng phần riêng
- ❌ Lỗi 1 module → toàn bộ app down
- ❌ Deploy phải deploy toàn bộ
- ❌ Khó bảo trì khi app lớn

---

### **Containers Mới (Microservices)**
```
                    ┌──────────────────┐
                    │  Kong Gateway    │
                    │   (Port 9000)    │
                    └────────┬─────────┘
                             │
            ┌────────────────┼────────────────┐
            ↓                ↓                ↓
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │   Catalog    │ │    Order     │ │     User     │
    │   Service    │ │   Service    │ │   Service    │
    │  (Port 9005) │ │  (Port 9002) │ │  (Port 9003) │
    └──────┬───────┘ └──────┬───────┘ └──────┬───────┘
           │                │                │
           ↓                ↓                ↓
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │mysql_catalog │ │ mysql_order  │ │  mysql_user  │
    │  (3310)      │ │   (3311)     │ │   (3312)     │
    │ catalog_db   │ │  order_db    │ │  user_db     │
    └──────────────┘ └──────────────┘ └──────────────┘

            ┌─────────────────────────────┐
            │      RabbitMQ (5672)        │
            │  Async Communication        │
            └─────────────┬───────────────┘
                          ↓
                  ┌───────────────┐
                  │ Notification  │
                  │   Service     │
                  │ (Port 9004)   │
                  └───────────────┘
```

**Ưu điểm:**
- ✅ Scale từng service riêng
- ✅ Lỗi 1 service → các service khác vẫn chạy
- ✅ Deploy độc lập từng service
- ✅ Dễ bảo trì, mỗi team quản lý 1 service
- ✅ Monitoring, tracing, service discovery

---

## 🌐 Ports So Sánh

### Containers Cũ (4 ports)
```
http://localhost:8000  → Laravel App
http://localhost:3308  → MySQL
http://localhost:8083  → phpMyAdmin
http://localhost:8082  → Redis Commander
```

### Containers Mới (15+ ports)
```
# Application Services
http://localhost:9005  → Catalog Service (Products, Categories)
http://localhost:9002  → Order Service (Orders, Cart)
http://localhost:9003  → User Service (Users, Admin)
http://localhost:9004  → Notification Service

# API Gateway
http://localhost:9000  → Kong Gateway (Proxy)
http://localhost:9001  → Kong Admin API
http://localhost:1337  → Konga Admin GUI

# Databases
localhost:3310         → MySQL Catalog
localhost:3311         → MySQL Order
localhost:3312         → MySQL User
localhost:6381         → Redis

# Infrastructure
http://localhost:15672 → RabbitMQ Management
http://localhost:8500  → Consul UI
http://localhost:16686 → Jaeger UI
http://localhost:9090  → Prometheus
http://localhost:3000  → Grafana

# Tools
http://localhost:9083  → phpMyAdmin
http://localhost:9082  → Redis Commander
http://localhost:8025  → MailHog UI
```

---

## 📋 Kết Luận

### ❓ Có giống nhau không?
**KHÔNG! Hoàn toàn khác nhau:**

| Khía cạnh | Cũ | Mới |
|-----------|-----|-----|
| Số lượng containers | 5 | 20 |
| Kiến trúc | Monolith | Microservices |
| Databases | 1 MySQL | 3 MySQL + 1 PostgreSQL |
| Application | 1 app | 4 services |
| API Gateway | Không | Kong |
| Message Broker | Không | RabbitMQ |
| Service Discovery | Không | Consul |
| Distributed Tracing | Không | Jaeger |
| Monitoring | Không | Prometheus + Grafana |
| Complexity | Đơn giản | Phức tạp nhưng mạnh mẽ |

### 🎯 Khi nào dùng cái nào?

**Dùng Containers Cũ (Monolith) khi:**
- Dự án nhỏ, đơn giản
- Team nhỏ (1-3 người)
- Cần deploy nhanh
- Không cần scale cao

**Dùng Containers Mới (Microservices) khi:**
- Dự án lớn, phức tạp
- Team lớn, nhiều người
- Cần scale từng phần riêng
- Cần high availability
- Dự án production, enterprise-level
- **Yêu cầu đạt 100 điểm với kiến trúc microservices hoàn chỉnh**

---

## 💡 Lưu Ý

1. **Không chạy đồng thời cả 2 setup** (port conflicts, resource usage)
2. **Chuyển đổi giữa 2 setup:**
   ```bash
   # Stop setup cũ
   docker-compose -f docker-compose.yml down
   
   # Start setup mới
   docker-compose -f docker-compose.microservices.yml up -d
   
   # Hoặc ngược lại
   docker-compose -f docker-compose.microservices.yml down
   docker-compose -f docker-compose.yml up -d
   ```

3. **Dữ liệu riêng biệt:** Mỗi setup có volumes riêng, dữ liệu KHÔNG tự động sync
