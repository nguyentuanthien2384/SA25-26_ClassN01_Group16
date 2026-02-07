# 🐳 Hướng Dẫn Các Service Docker - ElectroShop

## 📋 Tổng Quan Kiến Trúc

```
                                    ┌─────────────────┐
                                    │   Client/User   │
                                    │   (Browser)     │
                                    └────────┬────────┘
                                             │
                                             ▼ HTTP :8000
┌────────────────────────────────────────────────────────────────────────────┐
│                        Docker Network: microservices                        │
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │                     electroshop_app (Laravel)                        │   │
│  │                         Port: 8000                                   │   │
│  │  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐                │   │
│  │  │  Nginx  │  │ PHP-FPM │  │  Queue  │  │Supervisor│                │   │
│  │  │ :8000   │─▶│  :9000  │  │ Worker  │  │         │                │   │
│  │  └─────────┘  └─────────┘  └─────────┘  └─────────┘                │   │
│  └──────────────────────┬───────────────────────┬──────────────────────┘   │
│                         │                       │                           │
│              ┌──────────▼──────────┐  ┌────────▼────────┐                  │
│              │  electroshop_mysql  │  │ electroshop_redis│                  │
│              │     Port: 3308      │  │    Port: 6380    │                  │
│              │    (MySQL 8.0)      │  │  (Redis 7)       │                  │
│              └──────────┬──────────┘  └────────┬────────┘                  │
│                         │                       │                           │
│              ┌──────────▼──────────┐  ┌────────▼────────┐                  │
│              │electroshop_phpmyadmin│ │electroshop_redis │                  │
│              │     Port: 8083      │  │   _commander     │                  │
│              │   (Web GUI MySQL)   │  │   Port: 8082     │                  │
│              └─────────────────────┘  └─────────────────┘                  │
└────────────────────────────────────────────────────────────────────────────┘
```

---

## 🔧 Chi Tiết Từng Service

### 1. 🌐 electroshop_app (Laravel Application)

**Mô tả:** Ứng dụng web chính - Website bán đồ điện tử

| Thuộc tính | Giá trị                |
| ---------- | ---------------------- |
| **Image**  | Tự build từ Dockerfile |
| **Port**   | 8000                   |
| **URL**    | http://localhost:8000  |

#### Thành phần bên trong:

- **Nginx**: Web server, nhận request từ browser
- **PHP-FPM**: Xử lý code PHP Laravel
- **Queue Worker**: Xử lý background jobs (gửi email, etc.)
- **Supervisor**: Quản lý các process

#### Luồng hoạt động:

```
Browser Request → Nginx(:8000) → PHP-FPM(:9000) → Laravel Code
                                                      │
                                    ┌─────────────────┼─────────────────┐
                                    ▼                 ▼                 ▼
                                 MySQL            Redis             Storage
                              (Database)        (Cache)            (Files)
```

#### Các lệnh hữu ích:

```powershell
# Xem logs
docker logs electroshop_app

# Vào container
docker exec -it electroshop_app bash

# Chạy artisan commands
docker exec electroshop_app php artisan cache:clear
docker exec electroshop_app php artisan migrate
docker exec electroshop_app php artisan tinker
```

---

### 2. 🗄️ electroshop_mysql (MySQL Database)

**Mô tả:** Cơ sở dữ liệu lưu trữ tất cả thông tin

| Thuộc tính   | Giá trị                           |
| ------------ | --------------------------------- |
| **Image**    | mysql:8.0                         |
| **Port**     | 3308 (external) → 3306 (internal) |
| **Database** | duan                              |
| **Username** | root                              |
| **Password** | root_password                     |

#### Dữ liệu lưu trữ:

- 👤 Users (Người dùng)
- 📦 Products (Sản phẩm)
- 📁 Categories (Danh mục)
- 🛒 Orders (Đơn hàng)
- ⭐ Ratings (Đánh giá)
- 📞 Contacts (Liên hệ)

#### Luồng hoạt động:

```
Laravel App ──SQL Query──▶ MySQL Container
                               │
                               ▼
                        ┌─────────────┐
                        │   Tables    │
                        │  - users    │
                        │  - products │
                        │  - orders   │
                        │  - ...      │
                        └─────────────┘
```

#### Các lệnh hữu ích:

```powershell
# Kết nối MySQL
docker exec -it electroshop_mysql mysql -uroot -proot_password duan

# Xem danh sách bảng
docker exec electroshop_mysql mysql -uroot -proot_password -e "USE duan; SHOW TABLES;"

# Đếm sản phẩm
docker exec electroshop_mysql mysql -uroot -proot_password -e "SELECT COUNT(*) FROM duan.products;"

# Backup database
docker exec electroshop_mysql mysqldump -uroot -proot_password duan > backup.sql

# Restore database
Get-Content backup.sql | docker exec -i electroshop_mysql mysql -uroot -proot_password duan
```

---

### 3. ⚡ electroshop_redis (Redis Cache)

**Mô tả:** Bộ nhớ đệm tốc độ cao, lưu trữ session và cache

| Thuộc tính | Giá trị                           |
| ---------- | --------------------------------- |
| **Image**  | redis:7-alpine                    |
| **Port**   | 6380 (external) → 6379 (internal) |

#### Chức năng:

- 🚀 **Cache**: Lưu kết quả query để tăng tốc
- 🔐 **Session**: Lưu phiên đăng nhập người dùng
- 📬 **Queue**: Hàng đợi xử lý background jobs

#### Luồng hoạt động:

```
┌─────────────┐     Cache Miss      ┌─────────────┐
│   Laravel   │ ──────────────────▶ │    MySQL    │
│     App     │                     │  (Database) │
└──────┬──────┘                     └─────────────┘
       │                                   │
       │ Cache Hit (Fast!)                 │ Query Result
       │                                   │
       ▼                                   ▼
┌─────────────┐                     ┌─────────────┐
│    Redis    │ ◀────────────────── │   Store in  │
│   (Cache)   │    Cache Result     │    Cache    │
└─────────────┘                     └─────────────┘
```

#### Các lệnh hữu ích:

```powershell
# Kết nối Redis CLI
docker exec -it electroshop_redis redis-cli

# Xem tất cả keys
docker exec electroshop_redis redis-cli KEYS "*"

# Xóa cache
docker exec electroshop_redis redis-cli FLUSHALL

# Kiểm tra Redis hoạt động
docker exec electroshop_redis redis-cli PING
```

---

### 4. 🖥️ electroshop_phpmyadmin (MySQL GUI)

**Mô tả:** Giao diện web để quản lý MySQL database

| Thuộc tính | Giá trị               |
| ---------- | --------------------- |
| **Image**  | phpmyadmin:latest     |
| **Port**   | 8083                  |
| **URL**    | http://localhost:8083 |

#### Thông tin đăng nhập:

| Field    | Value         |
| -------- | ------------- |
| Server   | mysql         |
| Username | root          |
| Password | root_password |

#### Chức năng:

- 📊 Xem/Sửa/Xóa dữ liệu trong bảng
- 📝 Chạy SQL queries
- 📥 Import/Export database
- 🔧 Quản lý cấu trúc bảng

#### Luồng hoạt động:

```
Browser ──:8083──▶ phpMyAdmin Container ──SQL──▶ MySQL Container
   │                      │                           │
   │                      ▼                           ▼
   │              ┌──────────────┐           ┌──────────────┐
   └─────────────▶│  Web GUI     │──────────▶│   Database   │
                  │  (Tables,    │           │   (duan)     │
                  │   Queries)   │           │              │
                  └──────────────┘           └──────────────┘
```

---

### 5. 📊 electroshop_redis_commander (Redis GUI)

**Mô tả:** Giao diện web để xem dữ liệu trong Redis

| Thuộc tính | Giá trị                               |
| ---------- | ------------------------------------- |
| **Image**  | rediscommander/redis-commander:latest |
| **Port**   | 8082                                  |
| **URL**    | http://localhost:8082                 |

#### Chức năng:

- 🔍 Xem tất cả keys trong Redis
- 📝 Xem/Sửa giá trị
- 🗑️ Xóa keys
- 📈 Theo dõi Redis stats

#### Luồng hoạt động:

```
Browser ──:8082──▶ Redis Commander ──Redis Protocol──▶ Redis Container
   │                     │                                  │
   │                     ▼                                  ▼
   │              ┌──────────────┐                  ┌──────────────┐
   └─────────────▶│  Web GUI     │─────────────────▶│  Cache Data  │
                  │  (Keys,      │                  │  (Sessions,  │
                  │   Values)    │                  │   Queries)   │
                  └──────────────┘                  └──────────────┘
```

---

## 🔄 Luồng Request Hoàn Chỉnh

Khi người dùng truy cập website:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           LUỒNG XỬ LÝ REQUEST                                │
└─────────────────────────────────────────────────────────────────────────────┘

1️⃣ User mở browser, truy cập http://localhost:8000
                              │
                              ▼
2️⃣ Request đến Nginx (trong electroshop_app container)
                              │
                              ▼
3️⃣ Nginx chuyển request đến PHP-FPM
                              │
                              ▼
4️⃣ Laravel xử lý request:
   ┌──────────────────────────┼──────────────────────────┐
   │                          │                          │
   ▼                          ▼                          ▼

5️⃣ Kiểm tra Cache         5️⃣ Query Database        5️⃣ Đọc Session
   (Redis)                    (MySQL)                   (Redis)
   │                          │                          │
   └──────────────────────────┼──────────────────────────┘
                              │
                              ▼
6️⃣ Laravel render View (Blade template)
                              │
                              ▼
7️⃣ Nginx trả response HTML về Browser
                              │
                              ▼
8️⃣ Browser hiển thị trang web cho User

```

---

## 🚀 Các Lệnh Quản Lý Docker

### Khởi động Services

```powershell
# Khởi động tất cả
docker-compose up -d

# Khởi động và rebuild
docker-compose up -d --build
```

### Dừng Services

```powershell
# Dừng tất cả
docker-compose down

# Dừng và xóa volumes (XÓA DỮ LIỆU!)
docker-compose down -v
```

### Xem Trạng Thái

```powershell
# Xem containers đang chạy
docker ps

# Xem logs realtime
docker-compose logs -f

# Xem logs của 1 service
docker logs -f electroshop_app
```

### Restart Services

```powershell
# Restart tất cả
docker-compose restart

# Restart 1 service
docker restart electroshop_app
```

---

## 🌐 Bảng Tổng Hợp URLs

| Service             | URL                   | Mô tả         |
| ------------------- | --------------------- | ------------- |
| **Web App**         | http://localhost:8000 | Website chính |
| **phpMyAdmin**      | http://localhost:8083 | Quản lý MySQL |
| **Redis Commander** | http://localhost:8082 | Quản lý Redis |

---

## 📁 Cấu Trúc Files Docker

```
D:\Web_Ban_Do_Dien_Tu\
├── docker-compose.yml      # Định nghĩa các services
├── Dockerfile              # Build image Laravel
├── docker/
│   ├── nginx/
│   │   └── default.conf    # Cấu hình Nginx
│   └── supervisor/
│       └── supervisord.conf # Cấu hình Supervisor
└── .env                    # Biến môi trường
```

---

## ❓ Troubleshooting

### Service không khởi động?

```powershell
# Xem logs lỗi
docker-compose logs electroshop_app

# Rebuild container
docker-compose up -d --build --force-recreate
```

### Database connection refused?

```powershell
# Kiểm tra MySQL đã ready chưa
docker exec electroshop_mysql mysqladmin -uroot -proot_password ping

# Đợi MySQL khởi động (30 giây)
Start-Sleep -Seconds 30
```

### Cache không hoạt động?

```powershell
# Xóa cache
docker exec electroshop_app php artisan cache:clear
docker exec electroshop_redis redis-cli FLUSHALL
```

---

## 📝 Ghi Chú

- **Port 3308**: MySQL Docker (tránh conflict với XAMPP port 3306/3307)
- **Port 6380**: Redis Docker (tránh conflict nếu có Redis local)
- Tất cả services nằm trong cùng **Docker network** nên có thể giao tiếp với nhau bằng tên container
- Dữ liệu MySQL được lưu trong **Docker volume** `mysql_data`, không bị mất khi restart

---
