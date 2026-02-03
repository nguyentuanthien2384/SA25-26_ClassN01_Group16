# 🐳 FIX DOCKER ERROR

## ❌ LỖI: "docker-compose is not recognized"

```
docker-compose : The term 'docker-compose' is not recognized...
```

### NGUYÊN NHÂN:

1. **Docker Desktop chưa được cài đặt**
2. **Đang dùng Docker Compose V2** (dùng `docker compose` thay vì `docker-compose`)
3. **Docker chưa được thêm vào PATH**

---

## ✅ GIẢI PHÁP

### 🔧 OPTION 1: Dùng Docker Compose V2 (Recommended)

Nếu bạn đã cài Docker Desktop mới (2022+), hãy dùng lệnh không có dấu gạch ngang:

```powershell
# Thay vì: docker-compose
# Dùng: docker compose (không có dấu -)

docker compose -f docker-compose.microservices.yml up -d
```

**Kiểm tra version:**

```powershell
docker compose version
```

---

### 📥 OPTION 2: Cài Docker Desktop (Nếu chưa có)

#### Bước 1: Download Docker Desktop

- **Link:** https://www.docker.com/products/docker-desktop/
- Chọn version cho Windows
- Download file `.exe`

#### Bước 2: Cài đặt

1. Chạy file cài đặt
2. Tick: "Use WSL 2 instead of Hyper-V" (recommended)
3. Next → Next → Install
4. **Restart máy** sau khi cài xong

#### Bước 3: Khởi động Docker Desktop

1. Mở Docker Desktop từ Start Menu
2. Đợi Docker engine start (icon Docker ở taskbar màu xanh)
3. Kiểm tra:

```powershell
docker --version
docker compose version
```

#### Bước 4: Chạy lại lệnh

```powershell
# Dùng docker compose (V2)
docker compose -f docker-compose.microservices.yml up -d
```

---

### ⚠️ OPTION 3: Skip Docker (Chạy basic mode)

**Nếu không muốn cài Docker**, bạn có thể chạy dự án ở chế độ basic:

```powershell
# Chỉ cần MySQL và PHP
# Không cần Redis, Elasticsearch, Monitoring...

# 1. Setup .env cho basic mode
# Sửa file .env:
```

```env
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

# Comment các dòng Redis, Elasticsearch
# REDIS_HOST=127.0.0.1
# ELASTICSEARCH_ENABLED=false
```

```powershell
# 2. Clear cache
php artisan config:clear
php artisan cache:clear

# 3. Chạy
php artisan serve
```

**✅ Website vẫn chạy bình thường!**

Chỉ mất các tính năng optional:
- ❌ Centralized logging (ELK)
- ❌ Metrics (Prometheus/Grafana)
- ❌ Distributed tracing (Jaeger)
- ❌ Service discovery (Consul)
- ✅ **Website vẫn hoạt động đầy đủ**

---

## 🔍 KIỂM TRA DOCKER ĐÃ CÀI CHƯA

```powershell
# Kiểm tra Docker
docker --version

# Kiểm tra Docker Compose V2
docker compose version

# Kiểm tra Docker Compose V1 (cũ)
docker-compose --version
```

**Kết quả mong đợi:**

```
Docker version 24.0.x, build xxxxx
Docker Compose version v2.x.x
```

---

## 📋 FULL COMMANDS REFERENCE

### Docker Compose V2 (Mới - Recommended)

```powershell
# Start all services
docker compose -f docker-compose.microservices.yml up -d

# Stop all services
docker compose -f docker-compose.microservices.yml down

# View logs
docker compose -f docker-compose.microservices.yml logs -f

# Restart service
docker compose -f docker-compose.microservices.yml restart nginx

# List running containers
docker compose -f docker-compose.microservices.yml ps
```

### Docker Compose V1 (Cũ)

```powershell
# Chỉ dùng nếu bạn cài version cũ
docker-compose -f docker-compose.microservices.yml up -d
```

---

## 🎯 KHUYẾN NGHỊ

### Cho người mới:

**➡️ Skip Docker, chạy basic mode**

Lý do:
- Đơn giản hơn
- Không cần cài Docker Desktop (2.8GB)
- Website vẫn chạy đầy đủ
- Chỉ mất monitoring tools (optional)

**Steps:**

```powershell
# 1. Setup .env basic
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# 2. Chạy
composer install
copy .env.example .env
php artisan key:generate
mysql -u root -p duan < duan.sql
php artisan serve
```

**✅ XONG! http://localhost:8000**

---

### Cho người có kinh nghiệm:

**➡️ Cài Docker Desktop**

Lý do:
- Full features
- Monitoring tools
- Production-like environment
- Scalable

**Steps:**

1. Download Docker Desktop
2. Install & restart
3. Run: `docker compose -f docker-compose.microservices.yml up -d`
4. Access monitoring tools

---

## 🆘 VẪN GẶP LỖI?

### Lỗi: "Docker daemon not running"

**Fix:**

```powershell
# 1. Mở Docker Desktop
# 2. Đợi icon Docker ở taskbar màu xanh
# 3. Chạy lại lệnh
```

### Lỗi: "WSL 2 installation is incomplete"

**Fix:**

```powershell
# Cài WSL 2
wsl --install

# Restart máy
# Mở Docker Desktop lại
```

### Lỗi: Port đã được sử dụng

**Fix:**

```powershell
# Kiểm tra port nào đang dùng
netstat -ano | findstr :8000
netstat -ano | findstr :3306

# Stop process đang dùng port hoặc đổi port trong docker-compose.yml
```

---

## 📊 SO SÁNH: CÓ DOCKER vs KHÔNG DOCKER

| Feature | Có Docker | Không Docker |
|---------|-----------|--------------|
| **Website** | ✅ Chạy | ✅ Chạy |
| **Database** | ✅ MySQL | ✅ MySQL |
| **Cache** | ✅ Redis | ⚠️ File cache |
| **Queue** | ✅ Redis | ⚠️ Sync (không async) |
| **Logging** | ✅ ELK Stack | ⚠️ File logs |
| **Monitoring** | ✅ Grafana, Prometheus | ❌ Không |
| **Tracing** | ✅ Jaeger | ❌ Không |
| **Service Discovery** | ✅ Consul | ❌ Không |
| **Setup Time** | 30 min | 5 min |
| **Disk Space** | ~3GB | ~500MB |

**Kết luận:** Không Docker vẫn chạy tốt cho development!

---

## 🎓 RECOMMENDED WORKFLOW

### Development (Local):

```
❌ KHÔNG CẦN Docker
✅ PHP + MySQL + Composer
✅ CACHE_DRIVER=file
✅ QUEUE_CONNECTION=sync
```

### Staging/Production:

```
✅ CẦN Docker
✅ Full infrastructure
✅ Monitoring tools
✅ High availability
```

---

## 💡 QUICK FIX - 3 LỆNH

```powershell
# Nếu có Docker Desktop:
docker compose -f docker-compose.microservices.yml up -d

# Nếu không có Docker:
# Sửa .env: CACHE_DRIVER=file, QUEUE_CONNECTION=sync
php artisan config:clear
php artisan serve
```

**Cả 2 cách đều OK!** ✅

---

## 📚 TÀI LIỆU THAM KHẢO

- **[RUN_AND_DEPLOY_GUIDE.md](./RUN_AND_DEPLOY_GUIDE.md)** - Full guide
- **[GETTING_STARTED.md](./GETTING_STARTED.md)** - 3 levels setup
- **[QUICK_RUN.md](./QUICK_RUN.md)** - Chạy nhanh không Docker

---

<div align="center">

**🎯 BẠN CHỌN CÁCH NÀO?**

**Option 1:** Cài Docker Desktop (30 min)  
**Option 2:** Skip Docker, chạy basic (5 min) ⭐ Recommended

</div>
