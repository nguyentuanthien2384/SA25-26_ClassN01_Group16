# 🔌 Tổng Hợp Ports - Docker Containers

---

## 📋 MỤC LỤC

- [Setup CŨ (Monolith) - 5 Containers](#setup-cũ-monolith---5-containers)
- [Setup MỚI (Microservices) - 20 Containers](#setup-mới-microservices---20-containers)
- [Bảng So Sánh Ports](#bảng-so-sánh-ports)

---

## 🔴 SETUP CŨ (Monolith) - 5 Containers

**File:** `docker-compose.yml`
**Network:** `bridge` (default)
**Tổng ports:** **4 ports**

### 📊 Danh Sách Ports

| Port Host | Container Port | Service         | Container Name                | URL/Access            |
| --------- | -------------- | --------------- | ----------------------------- | --------------------- |
| **8000**  | 8000           | Laravel App     | `electroshop_app`             | http://localhost:8000 |
| **3308**  | 3306           | MySQL           | `electroshop_mysql`           | localhost:3308        |
| **6380**  | 6379           | Redis           | `electroshop_redis`           | localhost:6380        |
| **8083**  | 80             | phpMyAdmin      | `electroshop_phpmyadmin`      | http://localhost:8083 |
| **8082**  | 8081           | Redis Commander | `electroshop_redis_commander` | http://localhost:8082 |

### 🌐 Cách Truy Cập

```bash
# Laravel Application (Monolith)
http://localhost:8000

# phpMyAdmin
http://localhost:8083
  Username: root
  Password: root
  Server: electroshop_mysql

# Redis Commander
http://localhost:8082

# MySQL Connection (từ host)
mysql -h 127.0.0.1 -P 3308 -u root -p
  Password: root
  Database: duan

# Redis Connection (từ host)
redis-cli -h 127.0.0.1 -p 6380
```

---

## 🟢 SETUP MỚI (Microservices) - 20 Containers

**File:** `docker-compose.microservices.yml`
**Network:** `ms_network` (custom bridge)
**Tổng ports:** **27+ ports**

---

### 🎯 1. APPLICATION SERVICES (4 Microservices)

| Port     | Service              | Container              | URL                   | Chức năng                                |
| -------- | -------------------- | ---------------------- | --------------------- | ---------------------------------------- |
| **9005** | Catalog Service      | `catalog_service`      | http://localhost:9005 | Quản lý sản phẩm, danh mục, nhà cung cấp |
| **9002** | Order Service        | `order_service`        | http://localhost:9002 | Quản lý đơn hàng, giỏ hàng, thanh toán   |
| **9003** | User Service         | `user_service`         | http://localhost:9003 | Quản lý user, admin, authentication      |
| **9004** | Notification Service | `notification_service` | http://localhost:9004 | Gửi email, thông báo                     |

#### 📝 Chi Tiết API Endpoints

```bash
# Catalog Service (9005)
http://localhost:9005/api/health          # Health check
http://localhost:9005/api/products        # Danh sách sản phẩm
http://localhost:9005/api/categories      # Danh sách danh mục
http://localhost:9005/api/suppliers       # Danh sách nhà cung cấp

# Order Service (9002)
http://localhost:9002/api/health          # Health check
http://localhost:9002/api/orders          # Quản lý đơn hàng
http://localhost:9002/api/cart            # Giỏ hàng
http://localhost:9002/api/transactions    # Giao dịch

# User Service (9003)
http://localhost:9003/api/health          # Health check
http://localhost:9003/api/users           # Quản lý users
http://localhost:9003/api/admin           # Admin panel
http://localhost:9003/api/auth            # Authentication

# Notification Service (9004)
http://localhost:9004/health              # Health check
```

---

### 🚪 2. API GATEWAY (Kong)

| Port     | Protocol | Service    | Container       | URL                    | Chức năng            |
| -------- | -------- | ---------- | --------------- | ---------------------- | -------------------- |
| **9000** | HTTP     | Proxy      | `kong_gateway`  | http://localhost:9000  | Public API endpoint  |
| **9001** | HTTP     | Admin API  | `kong_gateway`  | http://localhost:9001  | Kong configuration   |
| **9443** | HTTPS    | Proxy SSL  | `kong_gateway`  | https://localhost:9443 | Secure proxy         |
| **9444** | HTTPS    | Admin SSL  | `kong_gateway`  | https://localhost:9444 | Secure admin         |
| **1337** | HTTP     | Konga GUI  | `konga_gui`     | http://localhost:1337  | Kong admin interface |
| -        | Internal | PostgreSQL | `kong_database` | (internal)             | Kong database        |

#### 🔧 Kong Admin API Examples

```bash
# List all services
curl http://localhost:9001/services

# List all routes
curl http://localhost:9001/routes

# Health check
curl http://localhost:9001/status

# Access through Kong Gateway
http://localhost:9000/catalog/api/products
http://localhost:9000/order/api/orders
http://localhost:9000/user/api/users
```

---

### 🗄️ 3. DATABASES (4 Containers)

| Port     | Database      | Container        | Database Name | User | Password            | Dùng cho        |
| -------- | ------------- | ---------------- | ------------- | ---- | ------------------- | --------------- |
| **3310** | MySQL 8.0     | `mysql_catalog`  | `catalog_db`  | root | `catalog_root_pass` | Catalog Service |
| **3311** | MySQL 8.0     | `mysql_order`    | `order_db`    | root | `order_root_pass`   | Order Service   |
| **3312** | MySQL 8.0     | `mysql_user`     | `user_db`     | root | `user_root_pass`    | User Service    |
| **6381** | Redis 7       | `ms_redis_cache` | -             | -    | -                   | Shared cache    |
| -        | PostgreSQL 13 | `kong_database`  | `kong`        | kong | `kong`              | Kong Gateway    |

#### 🔌 Database Connections

```bash
# MySQL Catalog (từ host)
mysql -h 127.0.0.1 -P 3310 -u root -pcatalog_root_pass catalog_db

# MySQL Order (từ host)
mysql -h 127.0.0.1 -P 3311 -u root -porder_root_pass order_db

# MySQL User (từ host)
mysql -h 127.0.0.1 -P 3312 -u root -puser_root_pass user_db

# Redis (từ host)
redis-cli -h 127.0.0.1 -p 6381

# PostgreSQL Kong (từ host)
psql -h 127.0.0.1 -U kong -d kong
  Password: kong
```

---

### 📨 4. MESSAGE BROKER (RabbitMQ)

| Port      | Protocol | Container         | URL                    | Chức năng     |
| --------- | -------- | ----------------- | ---------------------- | ------------- |
| **5672**  | AMQP     | `rabbitmq_broker` | amqp://localhost:5672  | Message queue |
| **15672** | HTTP     | `rabbitmq_broker` | http://localhost:15672 | Management UI |

#### 🐰 RabbitMQ Access

```bash
# Management UI
http://localhost:15672
  Username: admin
  Password: admin123

# AMQP Connection
amqp://admin:admin123@localhost:5672/electroshop

# Queues
- order.created
- order.updated
- payment.processed
- email.notification
```

---

### 🔍 5. SERVICE DISCOVERY (Consul)

| Port      | Protocol | Container          | URL                   | Chức năng     |
| --------- | -------- | ------------------ | --------------------- | ------------- |
| **8500**  | HTTP     | `consul_discovery` | http://localhost:8500 | UI & HTTP API |
| **8600**  | DNS      | `consul_discovery` | localhost:8600        | DNS interface |
| 8301-8302 | TCP/UDP  | `consul_discovery` | -                     | Serf LAN      |
| 8300      | TCP      | `consul_discovery` | -                     | Server RPC    |

#### 🔎 Consul Access

```bash
# Web UI
http://localhost:8500

# List services
curl http://localhost:8500/v1/catalog/services

# Health checks
curl http://localhost:8500/v1/health/service/catalog-service

# DNS lookup
dig @127.0.0.1 -p 8600 catalog-service.service.consul
```

---

### 📊 6. DISTRIBUTED TRACING (Jaeger)

| Port      | Protocol | Container        | Chức năng            |
| --------- | -------- | ---------------- | -------------------- |
| **16686** | HTTP     | `jaeger_tracing` | Jaeger UI            |
| **14268** | HTTP     | `jaeger_tracing` | Collector HTTP       |
| **14250** | gRPC     | `jaeger_tracing` | Collector gRPC       |
| **9411**  | HTTP     | `jaeger_tracing` | Zipkin compatible    |
| **6831**  | UDP      | `jaeger_tracing` | Agent compact thrift |
| **6832**  | UDP      | `jaeger_tracing` | Agent binary thrift  |
| **5778**  | HTTP     | `jaeger_tracing` | Agent config         |
| **5775**  | UDP      | `jaeger_tracing` | Agent zipkin thrift  |

#### 🔬 Jaeger Access

```bash
# Web UI
http://localhost:16686

# Search traces by service
http://localhost:16686/search?service=catalog-service

# Dependencies graph
http://localhost:16686/dependencies
```

---

### 📈 7. MONITORING (Prometheus + Grafana)

| Port     | Service    | Container    | URL                   | Chức năng          |
| -------- | ---------- | ------------ | --------------------- | ------------------ |
| **9090** | Prometheus | `prometheus` | http://localhost:9090 | Metrics collection |
| **3000** | Grafana    | `grafana`    | http://localhost:3000 | Visualization      |

#### 📊 Monitoring Access

```bash
# Prometheus UI
http://localhost:9090
  - Targets: http://localhost:9090/targets
  - Graph: http://localhost:9090/graph
  - Alerts: http://localhost:9090/alerts

# Grafana Dashboards
http://localhost:3000
  Username: admin
  Password: admin

# Prometheus metrics endpoints
http://localhost:9005/metrics  # Catalog
http://localhost:9002/metrics  # Order
http://localhost:9003/metrics  # User
```

---

### 📧 8. EMAIL TESTING (MailHog)

| Port     | Protocol | Container | URL                   | Chức năng   |
| -------- | -------- | --------- | --------------------- | ----------- |
| **1025** | SMTP     | `mailhog` | localhost:1025        | SMTP server |
| **8025** | HTTP     | `mailhog` | http://localhost:8025 | Web UI      |

#### 📮 MailHog Access

```bash
# Web UI (xem emails)
http://localhost:8025

# SMTP Configuration (Laravel .env)
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

---

### 🛠️ 9. MANAGEMENT TOOLS

| Port     | Service         | Container            | URL                   | Chức năng     |
| -------- | --------------- | -------------------- | --------------------- | ------------- |
| **9083** | phpMyAdmin      | `ms_phpmyadmin`      | http://localhost:9083 | Quản lý MySQL |
| **9082** | Redis Commander | `ms_redis_commander` | http://localhost:9082 | Quản lý Redis |

#### 🔧 Tools Access

```bash
# phpMyAdmin
http://localhost:9083
  Servers:
  - mysql_catalog (3310)
  - mysql_order (3311)
  - mysql_user (3312)

# Redis Commander
http://localhost:9082
  Connection: ms_redis_cache:6379
```

---

## 📊 BẢNG SO SÁNH PORTS

### 🔴 Setup CŨ vs 🟢 Setup MỚI

| Chức năng             | Setup CŨ (Port) | Setup MỚI (Port) | Conflict? |
| --------------------- | --------------- | ---------------- | --------- |
| **Web Application**   | 8000            | 9002, 9003, 9005 | ❌ No     |
| **MySQL**             | 3308            | 3310, 3311, 3312 | ❌ No     |
| **Redis**             | 6380            | 6381             | ❌ No     |
| **phpMyAdmin**        | 8083            | 9083             | ❌ No     |
| **Redis Commander**   | 8082            | 9082             | ❌ No     |
| **API Gateway**       | -               | 9000, 9001       | ✅ Mới    |
| **Message Broker**    | -               | 5672, 15672      | ✅ Mới    |
| **Service Discovery** | -               | 8500, 8600       | ✅ Mới    |
| **Tracing**           | -               | 16686            | ✅ Mới    |
| **Monitoring**        | -               | 9090, 3000       | ✅ Mới    |
| **Email Testing**     | -               | 1025, 8025       | ✅ Mới    |

---
