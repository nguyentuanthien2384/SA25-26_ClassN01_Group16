# 🚀 Hướng Dẫn Triển Khai Kiến Trúc Microservices

## 📋 Tổng Quan Kiến Trúc

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           MICROSERVICES ARCHITECTURE                                 │
└─────────────────────────────────────────────────────────────────────────────────────┘

                              ┌─────────────────┐
                              │     Client      │
                              │   (Browser)     │
                              └────────┬────────┘
                                       │
                                       ▼
                    ┌──────────────────────────────────────┐
                    │         API GATEWAY (Kong)           │
                    │            Port: 8000                │
                    │  • Routing    • Rate Limiting        │
                    │  • Auth       • Load Balancing       │
                    └──────────────────┬───────────────────┘
                                       │
         ┌─────────────────────────────┼─────────────────────────────┐
         │                             │                             │
         ▼                             ▼                             ▼
┌─────────────────┐          ┌─────────────────┐          ┌─────────────────┐
│ Catalog Service │          │  Order Service  │          │  User Service   │
│   Port: 9001    │          │   Port: 9002    │          │   Port: 9003    │
├─────────────────┤          ├─────────────────┤          ├─────────────────┤
│ • Products      │          │ • Orders        │          │ • Auth          │
│ • Categories    │          │ • Cart          │          │ • Users         │
│ • Search        │          │ • Transactions  │          │ • Profiles      │
└────────┬────────┘          └────────┬────────┘          └────────┬────────┘
         │                             │                             │
         ▼                             ▼                             ▼
┌─────────────────┐          ┌─────────────────┐          ┌─────────────────┐
│  MySQL Catalog  │          │   MySQL Order   │          │   MySQL User    │
│   Port: 3310    │          │   Port: 3311    │          │   Port: 3312    │
└─────────────────┘          └─────────────────┘          └─────────────────┘

                    ┌──────────────────────────────────────┐
                    │      MESSAGE BROKER (RabbitMQ)       │
                    │         Port: 5672 / 15672           │
                    │  • Event Publishing                  │
                    │  • Async Communication               │
                    └──────────────────┬───────────────────┘
                                       │
                                       ▼
                    ┌──────────────────────────────────────┐
                    │    Notification Service              │
                    │         Port: 9004                   │
                    │  • Email    • SMS    • Push          │
                    └──────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────────┐
│                            INFRASTRUCTURE SERVICES                                   │
├─────────────────┬─────────────────┬─────────────────┬─────────────────┬─────────────┤
│  Consul         │   Jaeger        │   Prometheus    │   Grafana       │   Redis     │
│  Port: 8500     │   Port: 16686   │   Port: 9090    │   Port: 3000    │   Port: 6379│
│  Discovery      │   Tracing       │   Metrics       │   Dashboard     │   Cache     │
└─────────────────┴─────────────────┴─────────────────┴─────────────────┴─────────────┘
```

---

---

## 🚀 Hướng Dẫn Triển Khai

### Bước 1: Khởi động Microservices

```powershell
# Di chuyển đến thư mục project
cd D:\Web_Ban_Do_Dien_Tu

# Khởi động tất cả services
docker-compose -f docker-compose.microservices.yml up -d
```

### Bước 2: Đợi services khởi động (khoảng 2-3 phút)

```powershell
# Kiểm tra trạng thái
docker-compose -f docker-compose.microservices.yml ps
```

### Bước 3: Cấu hình Kong Routes

```powershell
# Chạy script cấu hình
.\setup-kong-routes.ps1
```

### Bước 4: Truy cập các services

| Service             | URL                    | Mô tả                                 |
| ------------------- | ---------------------- | ------------------------------------- |
| **API Gateway**     | http://localhost:8000  | Entry point cho clients               |
| **Kong Admin**      | http://localhost:8001  | Kong Admin API                        |
| **Konga GUI**       | http://localhost:1337  | Kong Admin Dashboard                  |
| **RabbitMQ**        | http://localhost:15672 | Message Broker UI (admin/admin123)    |
| **Consul**          | http://localhost:8500  | Service Discovery UI                  |
| **Jaeger**          | http://localhost:16686 | Distributed Tracing UI                |
| **Prometheus**      | http://localhost:9090  | Metrics UI                            |
| **Grafana**         | http://localhost:3000  | Monitoring Dashboard (admin/admin123) |
| **phpMyAdmin**      | http://localhost:8083  | Database Admin                        |
| **Redis Commander** | http://localhost:8082  | Redis Admin                           |
| **MailHog**         | http://localhost:8025  | Email Testing UI                      |

---

## 📡 API Endpoints (qua Kong Gateway)

### Products API

```bash
# Lấy danh sách sản phẩm
GET http://localhost:8000/api/products

# Lấy sản phẩm theo ID
GET http://localhost:8000/api/products/{id}

# Sản phẩm hot
GET http://localhost:8000/api/products/hot

# Tìm kiếm
GET http://localhost:8000/api/products?search=keyword
```

### Orders API

```bash
# Tạo đơn hàng
POST http://localhost:8000/api/orders

# Lấy đơn hàng
GET http://localhost:8000/api/orders/{id}

# Giỏ hàng
GET http://localhost:8000/api/cart
POST http://localhost:8000/api/cart/add
```

### Users API

```bash
# Đăng ký
POST http://localhost:8000/api/auth/register

# Đăng nhập
POST http://localhost:8000/api/auth/login

# Profile
GET http://localhost:8000/api/users/profile
```

---

## 📊 Monitoring & Observability

### 1. Service Discovery (Consul)

- URL: http://localhost:8500
- Xem danh sách services đã đăng ký
- Kiểm tra health status của từng service

### 2. Distributed Tracing (Jaeger)

- URL: http://localhost:16686
- Trace request qua nhiều services
- Debug performance issues

### 3. Metrics (Prometheus + Grafana)

- Prometheus: http://localhost:9090
- Grafana: http://localhost:3000
- Xem metrics của tất cả services

### 4. Message Broker (RabbitMQ)

- URL: http://localhost:15672
- Username: admin
- Password: admin123
- Xem queues, exchanges, messages

---

## 🔄 Event-Driven Communication

### Events Flow Example: Order Created

```
┌─────────────────┐     1. Create Order     ┌─────────────────┐
│   Order Service │ ──────────────────────▶ │    MySQL DB     │
└────────┬────────┘                         └─────────────────┘
         │
         │ 2. Publish Event "order.created"
         ▼
┌─────────────────────────────────────────────────────────────┐
│                    RabbitMQ (Message Broker)                 │
│                                                              │
│   Exchange: order.events                                     │
│   Queue: notification.order                                  │
│   Queue: inventory.order                                     │
│   Queue: analytics.order                                     │
└────────┬────────────────────┬───────────────────┬───────────┘
         │                    │                   │
         ▼                    ▼                   ▼
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│  Notification   │ │   Inventory     │ │   Analytics     │
│    Service      │ │    Service      │ │    Service      │
│                 │ │                 │ │                 │
│ 3. Send Email   │ │ Update Stock    │ │ Track Order     │
└─────────────────┘ └─────────────────┘ └─────────────────┘
```

---

## 🛠️ Các Lệnh Hữu Ích

### Quản lý Containers

```powershell
# Khởi động
docker-compose -f docker-compose.microservices.yml up -d

# Dừng
docker-compose -f docker-compose.microservices.yml down

# Xem logs
docker-compose -f docker-compose.microservices.yml logs -f

# Logs của 1 service
docker logs catalog_service -f

# Restart service
docker restart catalog_service
```

### Kiểm tra Health

```powershell
# Health check tất cả services
docker-compose -f docker-compose.microservices.yml ps

# Health check Kong
curl http://localhost:8001/status

# Health check Catalog Service
curl http://localhost:9001/api/health

# Health check Order Service
curl http://localhost:9002/api/health
```

### Debug

```powershell
# Vào container
docker exec -it catalog_service bash

# Xem network
docker network inspect microservices

# Xem logs RabbitMQ
docker logs rabbitmq_broker
```

---

## 📋 So Sánh: Trước và Sau

| Tiêu chí               | Trước (Monolith) | Sau (Microservices)     |
| ---------------------- | ---------------- | ----------------------- |
| **Containers**         | 1                | 15+                     |
| **Databases**          | 1 (shared)       | 3 (per service)         |
| **API Gateway**        | ❌ Không         | ✅ Kong                 |
| **Message Broker**     | ❌ Không         | ✅ RabbitMQ             |
| **Service Discovery**  | ❌ Không         | ✅ Consul               |
| **Tracing**            | ❌ Không         | ✅ Jaeger               |
| **Monitoring**         | ❌ Cơ bản        | ✅ Prometheus + Grafana |
| **Điểm Microservices** | 23%              | 90%+                    |

---

## ⚠️ Troubleshooting

### Kong không khởi động

```powershell
# Kiểm tra Kong database
docker logs kong_database
docker logs kong_migration

# Restart Kong
docker restart kong_gateway
```

### Service không kết nối được database

```powershell
# Kiểm tra database đã ready chưa
docker exec mysql_catalog mysqladmin -uroot -p ping

# Xem logs
docker logs mysql_catalog
```

### RabbitMQ connection refused

```powershell
# Kiểm tra RabbitMQ đã ready
docker logs rabbitmq_broker


```
