# 🐳 HƯỚNG DẪN TRIỂN KHAI DỰ ÁN BẰNG DOCKER DESKTOP

## 📋 Mục Lục

1. [Yêu Cầu Hệ Thống](#-yêu-cầu-hệ-thống)
2. [Cài Đặt Docker Desktop](#-cài-đặt-docker-desktop)
3. [Chuẩn Bị Dự Án](#-chuẩn-bị-dự-án)
4. [Khởi Chạy Dự Án](#-khởi-chạy-dự-án)
5. [Truy Cập Ứng Dụng](#-truy-cập-ứng-dụng)
6. [Các Lệnh Thường Dùng](#-các-lệnh-thường-dùng)
7. [Khắc Phục Lỗi](#-khắc-phục-lỗi)

---

## 💻 Yêu Cầu Hệ Thống

### Phần Cứng Tối Thiểu
- **RAM:** 8GB (khuyến nghị 16GB)
- **CPU:** 4 cores
- **Disk:** 20GB free space

### Phần Mềm Cần Có
- Windows 10/11 Pro/Enterprise (với WSL2)
- Docker Desktop phiên bản mới nhất

---

## 🔧 Cài Đặt Docker Desktop

### Bước 1: Tải Docker Desktop

```
https://www.docker.com/products/docker-desktop/
```

### Bước 2: Cài Đặt WSL2 (Nếu Chưa Có)

Mở **PowerShell as Administrator** và chạy:

```powershell
wsl --install
```

Khởi động lại máy tính sau khi cài đặt.

### Bước 3: Cài Đặt Docker Desktop

1. Chạy file cài đặt Docker Desktop
2. Chọn **"Use WSL 2 instead of Hyper-V"**
3. Hoàn tất cài đặt và khởi động lại

### Bước 4: Kiểm Tra Docker

Mở **PowerShell** hoặc **Command Prompt**:

```bash
docker --version
docker-compose --version
```

**Kết quả mong đợi:**
```
Docker version 24.x.x, build xxxxxxx
Docker Compose version v2.x.x
```

---

## 📁 Chuẩn Bị Dự Án

### Bước 1: Mở Terminal Trong Thư Mục Dự Án

```powershell
cd D:\Web_Ban_Do_Dien_Tu
```

### Bước 2: Tạo File .env

```powershell
copy .env.example .env
```

### Bước 3: Cập Nhật File .env Cho Docker

Mở file `.env` và **thay đổi** các giá trị sau:

```env
# Database Configuration (Docker)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=csdl
DB_USERNAME=root
DB_PASSWORD=root_password

# Redis Configuration (Docker)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Elasticsearch
ELASTICSEARCH_HOST=http://elasticsearch:9200

# Consul
CONSUL_HOST=consul
CONSUL_PORT=8500
CONSUL_SERVICE_HOST=laravel-app
CONSUL_SERVICE_PORT=8000
```

### Bước 4: Tạo Thư Mục Cần Thiết (Nếu Chưa Có)

```powershell
# Tạo thư mục storage
mkdir -p storage\app\public
mkdir -p storage\framework\cache
mkdir -p storage\framework\sessions
mkdir -p storage\framework\views
mkdir -p storage\logs

# Tạo thư mục bootstrap/cache
mkdir -p bootstrap\cache
```

---

## 🚀 Khởi Chạy Dự Án

### Option A: Khởi Chạy Đầy Đủ (Tất Cả Services) - Khuyến Nghị

```powershell
# Bước 1: Build và khởi chạy tất cả containers
docker-compose up -d --build

# Bước 2: Xem logs để theo dõi tiến trình
docker-compose logs -f laravel-app
```

**Thời gian chờ:** ~3-5 phút lần đầu tiên

### Option B: Khởi Chạy Từng Phần

```powershell
# 1. Khởi chạy Database trước
docker-compose up -d mysql redis

# 2. Chờ 30 giây cho database sẵn sàng
timeout 30

# 3. Khởi chạy Laravel App
docker-compose up -d laravel-app

# 4. Khởi chạy Infrastructure (tùy chọn)
docker-compose up -d elasticsearch logstash kibana
docker-compose up -d kong kong-database kong-migration konga
docker-compose up -d consul jaeger prometheus grafana
```

### Option C: Chỉ Laravel + MySQL + Redis (Nhẹ Nhất)

```powershell
docker-compose up -d mysql redis laravel-app redis-commander
```

---

## 🌐 Truy Cập Ứng Dụng

### Ứng Dụng Chính

| Service | URL | Mô Tả |
|---------|-----|-------|
| **Laravel App** | http://localhost:8000 | Website chính |
| **Laravel Admin** | http://localhost:8000/admin | Trang quản trị |

### Công Cụ Database

| Service | URL | Credentials |
|---------|-----|-------------|
| **Redis Commander** | http://localhost:8081 | Không cần đăng nhập |
| **MySQL** | localhost:3306 | root / root_password |

### Monitoring & Observability

| Service | URL | Credentials |
|---------|-----|-------------|
| **Kibana** (Logs) | http://localhost:5601 | Không cần đăng nhập |
| **Grafana** (Metrics) | http://localhost:3000 | admin / admin |
| **Prometheus** | http://localhost:9090 | Không cần đăng nhập |
| **Jaeger** (Tracing) | http://localhost:16686 | Không cần đăng nhập |
| **Consul** (Service Discovery) | http://localhost:8500 | Không cần đăng nhập |

### API Gateway

| Service | URL | Mô Tả |
|---------|-----|-------|
| **Kong Proxy** | http://localhost:8002 | API Gateway |
| **Kong Admin** | http://localhost:8001 | Kong Admin API |
| **Konga** (Kong GUI) | http://localhost:1337 | Kong Dashboard |

---

## 📊 Kiểm Tra Trạng Thái

### Xem Tất Cả Containers

```powershell
docker-compose ps
```

**Kết quả mong đợi:**
```
NAME                IMAGE                       STATUS              PORTS
laravel-app         web_ban_do_dien_tu-...     Up (healthy)        0.0.0.0:8000->8000/tcp
mysql               mysql:8.0                   Up (healthy)        0.0.0.0:3306->3306/tcp
redis               redis:7-alpine              Up (healthy)        0.0.0.0:6379->6379/tcp
elasticsearch       elasticsearch:8.11.0        Up (healthy)        0.0.0.0:9200->9200/tcp
kibana              kibana:8.11.0               Up                  0.0.0.0:5601->5601/tcp
kong                kong:3.4                    Up (healthy)        0.0.0.0:8001-8002->8000-8001/tcp
consul              consul:1.17                 Up (healthy)        0.0.0.0:8500->8500/tcp
...
```

### Xem Logs Của Laravel

```powershell
docker-compose logs -f laravel-app
```

### Xem Logs Của Database

```powershell
docker-compose logs -f mysql
```

---

## 🔧 Các Lệnh Thường Dùng

### Quản Lý Containers

```powershell
# Khởi chạy tất cả
docker-compose up -d

# Dừng tất cả
docker-compose down

# Khởi động lại
docker-compose restart

# Dừng và xóa volumes (reset database)
docker-compose down -v

# Rebuild containers
docker-compose up -d --build
```

### Chạy Lệnh Laravel

```powershell
# Vào container Laravel
docker-compose exec laravel-app bash

# Chạy artisan commands
docker-compose exec laravel-app php artisan migrate
docker-compose exec laravel-app php artisan config:cache
docker-compose exec laravel-app php artisan cache:clear
docker-compose exec laravel-app php artisan queue:work

# Tạo APP_KEY
docker-compose exec laravel-app php artisan key:generate
```

### Database Commands

```powershell
# Vào MySQL CLI
docker-compose exec mysql mysql -u root -proot_password csdl

# Import database từ file SQL
docker-compose exec -T mysql mysql -u root -proot_password csdl < duan.sql

# Export database
docker-compose exec mysql mysqldump -u root -proot_password csdl > backup.sql
```

### Xem Logs

```powershell
# Xem logs tất cả services
docker-compose logs

# Xem logs realtime
docker-compose logs -f

# Xem logs của service cụ thể
docker-compose logs -f laravel-app
docker-compose logs -f mysql
docker-compose logs -f elasticsearch
```

---

## 🐛 Khắc Phục Lỗi

### Lỗi 1: Port Already In Use

**Triệu chứng:**
```
Error: bind: address already in use
```

**Giải pháp:**

```powershell
# Tìm process đang dùng port
netstat -ano | findstr :8000

# Kill process (thay PID bằng số từ lệnh trên)
taskkill /PID <PID> /F

# Hoặc đổi port trong docker-compose.yml
# ports:
#   - "8080:8000"  # Đổi 8000 thành 8080
```

### Lỗi 2: MySQL Connection Refused

**Triệu chứng:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Giải pháp:**

```powershell
# 1. Kiểm tra MySQL đã chạy chưa
docker-compose ps mysql

# 2. Chờ MySQL khởi động hoàn tất
docker-compose logs -f mysql

# 3. Khởi động lại Laravel sau khi MySQL ready
docker-compose restart laravel-app
```

### Lỗi 3: Permission Denied (Storage)

**Triệu chứng:**
```
The stream or file could not be opened: failed to open stream: Permission denied
```

**Giải pháp:**

```powershell
# Vào container và fix permissions
docker-compose exec laravel-app bash -c "chmod -R 777 storage bootstrap/cache"
```

### Lỗi 4: Elasticsearch Out of Memory

**Triệu chứng:**
```
elasticsearch exited with code 137
```

**Giải pháp:**

Mở Docker Desktop → Settings → Resources → Memory → Tăng lên 4GB+

Hoặc giảm memory của Elasticsearch trong `docker-compose.yml`:
```yaml
environment:
  - "ES_JAVA_OPTS=-Xms256m -Xmx256m"  # Giảm từ 512m
```

### Lỗi 5: Build Failed

**Giải pháp:**

```powershell
# Xóa cache và rebuild
docker-compose down
docker system prune -a -f
docker-compose up -d --build
```

### Lỗi 6: Container Keeps Restarting

```powershell
# Xem logs để tìm lỗi
docker-compose logs laravel-app

# Thường do APP_KEY chưa có
docker-compose exec laravel-app php artisan key:generate --force
docker-compose restart laravel-app
```

---

## 📊 Kiến Trúc Docker

```
┌─────────────────────────────────────────────────────────────────┐
│                     DOCKER DESKTOP                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                   NETWORK: microservices                 │    │
│  │                                                          │    │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │    │
│  │  │  laravel-app │  │    mysql     │  │    redis     │   │    │
│  │  │    :8000     │  │    :3306     │  │    :6379     │   │    │
│  │  └──────────────┘  └──────────────┘  └──────────────┘   │    │
│  │                                                          │    │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │    │
│  │  │elasticsearch │  │   logstash   │  │    kibana    │   │    │
│  │  │    :9200     │  │    :5044     │  │    :5601     │   │    │
│  │  └──────────────┘  └──────────────┘  └──────────────┘   │    │
│  │                                                          │    │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │    │
│  │  │     kong     │  │    consul    │  │    jaeger    │   │    │
│  │  │    :8002     │  │    :8500     │  │   :16686     │   │    │
│  │  └──────────────┘  └──────────────┘  └──────────────┘   │    │
│  │                                                          │    │
│  │  ┌──────────────┐  ┌──────────────┐                     │    │
│  │  │  prometheus  │  │   grafana    │                     │    │
│  │  │    :9090     │  │    :3000     │                     │    │
│  │  └──────────────┘  └──────────────┘                     │    │
│  │                                                          │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                  │
│  Volumes:                                                        │
│  ├── mysql_data                                                  │
│  ├── redis_data                                                  │
│  ├── elasticsearch_data                                          │
│  ├── prometheus_data                                             │
│  ├── grafana_data                                                │
│  └── consul_data                                                 │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## ✅ Checklist Triển Khai

### Trước Khi Chạy
- [ ] Docker Desktop đã cài đặt và đang chạy
- [ ] File `.env` đã được tạo và cấu hình
- [ ] Thư mục storage có quyền ghi
- [ ] Port 8000, 3306, 6379 chưa bị sử dụng

### Sau Khi Chạy
- [ ] Container `mysql` status: Up (healthy)
- [ ] Container `redis` status: Up (healthy)
- [ ] Container `laravel-app` status: Up
- [ ] Truy cập http://localhost:8000 thành công
- [ ] Database đã có dữ liệu

---

## 🎯 Quick Start (Lệnh Nhanh)

```powershell
# 1. Vào thư mục dự án
cd D:\Web_Ban_Do_Dien_Tu

# 2. Tạo file .env
copy .env.example .env

# 3. Sửa .env: đổi DB_HOST=mysql, REDIS_HOST=redis

# 4. Khởi chạy Docker
docker-compose up -d --build

# 5. Chờ 2-3 phút, sau đó mở trình duyệt
# http://localhost:8000
```

---

## 📞 Hỗ Trợ

Nếu gặp lỗi:

1. Xem logs: `docker-compose logs -f`
2. Kiểm tra status: `docker-compose ps`
3. Restart: `docker-compose restart`
4. Reset hoàn toàn: `docker-compose down -v && docker-compose up -d --build`

---

**Tạo ngày:** 2026-01-28  
**Phiên bản Docker:** 24.x  
**Phiên bản Compose:** 2.x
