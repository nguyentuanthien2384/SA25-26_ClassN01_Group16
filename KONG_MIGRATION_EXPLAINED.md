# 📘 Kong Migration - Giải Thích Chi Tiết

## 1. Kong Migration Là Gì?

**Kong Migration** là một container đặc biệt có nhiệm vụ:

- Khởi tạo database schema cho Kong API Gateway
- Tạo các tables cần thiết trong PostgreSQL
- Chạy một lần rồi tự động tắt

---

## 2. Quy Trình Chi Tiết

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      KONG MIGRATION WORKFLOW                                 │
└─────────────────────────────────────────────────────────────────────────────┘

 Bước 1: KHỞI ĐỘNG
 ─────────────────
    ┌─────────────────────┐
    │  docker-compose up  │
    │  kong-migration     │
    └──────────┬──────────┘
               │
               ▼
    ┌─────────────────────┐
    │  Container starts   │
    │  Image: kong:3.4    │
    └──────────┬──────────┘
               │
               ▼

 Bước 2: KẾT NỐI POSTGRESQL
 ───────────────────────────
    ┌─────────────────────┐         ┌─────────────────────┐
    │   kong_migration    │────────►│   kong_database     │
    │                     │         │   (PostgreSQL)      │
    └─────────────────────┘         └─────────────────────┘
               │
               │  Thông tin kết nối:
               │  - Host: kong-database
               │  - User: kong
               │  - Password: kongpass
               │  - Database: kong
               │
               ▼

 Bước 3: CHẠY MIGRATIONS
 ────────────────────────
    ┌─────────────────────────────────────────────────────────┐
    │  Lệnh: kong migrations bootstrap                        │
    │                                                         │
    │  Công việc:                                             │
    │  ├── Kiểm tra database có tồn tại không                │
    │  ├── Tạo schema 'kong'                                  │
    │  ├── Tạo các tables (services, routes, plugins...)      │
    │  ├── Tạo indexes                                        │
    │  └── Insert default data                                │
    └──────────┬──────────────────────────────────────────────┘
               │
               ▼

 Bước 4: TẠO CÁC TABLES
 ──────────────────────
    ┌─────────────────────────────────────────────────────────┐
    │  PostgreSQL Database: kong                               │
    ├─────────────────────────────────────────────────────────┤
    │                                                          │
    │  Tables được tạo:                                        │
    │  ┌─────────────────┐  ┌─────────────────┐               │
    │  │    services     │  │     routes      │               │
    │  │ ─────────────── │  │ ─────────────── │               │
    │  │ id              │  │ id              │               │
    │  │ name            │  │ name            │               │
    │  │ host            │  │ paths           │               │
    │  │ port            │  │ methods         │               │
    │  │ protocol        │  │ service_id      │               │
    │  └─────────────────┘  └─────────────────┘               │
    │                                                          │
    │  ┌─────────────────┐  ┌─────────────────┐               │
    │  │    plugins      │  │   consumers     │               │
    │  │ ─────────────── │  │ ─────────────── │               │
    │  │ id              │  │ id              │               │
    │  │ name            │  │ username        │               │
    │  │ config          │  │ custom_id       │               │
    │  │ service_id      │  │ tags            │               │
    │  │ route_id        │  │                 │               │
    │  └─────────────────┘  └─────────────────┘               │
    │                                                          │
    │  ┌─────────────────┐  ┌─────────────────┐               │
    │  │   upstreams     │  │    targets      │               │
    │  │ ─────────────── │  │ ─────────────── │               │
    │  │ id              │  │ id              │               │
    │  │ name            │  │ upstream_id     │               │
    │  │ algorithm       │  │ target          │               │
    │  │ slots           │  │ weight          │               │
    │  └─────────────────┘  └─────────────────┘               │
    │                                                          │
    │  + certificates, snis, ca_certificates,                  │
    │    clustering_data_planes, parameters, ...               │
    │                                                          │
    └─────────────────────────────────────────────────────────┘
               │
               ▼

 Bước 5: HOÀN THÀNH → EXIT
 ─────────────────────────
    ┌─────────────────────────────────────────────────────────┐
    │  ✅ Migrations completed successfully                    │
    │                                                          │
    │  Container exits with code 0                             │
    │  (0 = Success, không có lỗi)                            │
    │                                                          │
    │  Status: EXITED (0)                                      │
    └─────────────────────────────────────────────────────────┘
```

---

## 3. Cấu Hình Trong Docker Compose

```yaml
# Trích từ docker-compose.microservices.yml

# 1. Database cho Kong (chạy liên tục)
kong-database:
  image: postgres:13-alpine
  container_name: kong_database
  environment:
    POSTGRES_USER: kong
    POSTGRES_DB: kong
    POSTGRES_PASSWORD: kongpass
  healthcheck:
    test: ["CMD-SHELL", "pg_isready -U kong"]
    interval: 10s

# 2. Migration container (chạy một lần)
kong-migration:
  image: kong:3.4
  container_name: kong_migration
  command: kong migrations bootstrap    # ← Lệnh quan trọng
  environment:
    KONG_DATABASE: postgres
    KONG_PG_HOST: kong-database
    KONG_PG_USER: kong
    KONG_PG_PASSWORD: kongpass
  depends_on:
    kong-database:
      condition: service_healthy        # ← Chờ DB sẵn sàng
  restart: "no"                          # ← Không restart sau khi exit

# 3. Kong Gateway (chạy liên tục sau khi migration xong)
kong:
  image: kong:3.4
  container_name: kong_gateway
  environment:
    KONG_DATABASE: postgres
    KONG_PG_HOST: kong-database
    ...
  depends_on:
    kong-migration:
      condition: service_completed_successfully  # ← Chờ migration xong
```

---

## 4. Thứ Tự Khởi Động

```
Timeline: ─────────────────────────────────────────────────────────────────►

    T=0s         T=5s         T=15s        T=20s        T=25s
     │            │            │            │            │
     ▼            ▼            ▼            ▼            ▼
┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐
│ kong_   │  │ kong_   │  │ kong_   │  │ kong_   │  │ kong_   │
│database │  │database │  │migration│  │migration│  │gateway  │
│ STARTS  │  │ HEALTHY │  │ STARTS  │  │ EXITS   │  │ STARTS  │
└─────────┘  └─────────┘  └─────────┘  └─────────┘  └─────────┘
     │            │            │            │            │
     │            │            │            │            │
   Start      Ready for     Running      Done!       API Gateway
   PostgreSQL connections   migrations   Exit(0)     is running
```

---

## 5. Các Exit Codes

| Exit Code | Ý Nghĩa                        | Hành động                              |
| --------- | ------------------------------ | -------------------------------------- |
| **0**     | ✅ Thành công                  | Không cần làm gì                       |
| **1**     | ❌ Lỗi chung                   | Xem logs: `docker logs kong_migration` |
| **2**     | ❌ Database không kết nối được | Kiểm tra kong_database                 |

---

## 6. Kiểm Tra Migration Đã Chạy Thành Công

### Cách 1: Xem Exit Code

```powershell
docker inspect kong_migration --format='{{.State.ExitCode}}'
# Kết quả: 0 = OK
```

### Cách 2: Xem Logs

```powershell
docker logs kong_migration
```

Output mẫu khi thành công:

```
Bootstrapping database...
migrating core on database 'kong'...
core migrated up to: 000_base (executed)
core migrated up to: 003_100_to_110 (executed)
core migrated up to: 004_110_to_120 (executed)
...
Database is up to date
```

### Cách 3: Kiểm Tra Tables Trong PostgreSQL

```powershell
docker exec -it kong_database psql -U kong -d kong -c "\dt"
```

Output:

```
              List of relations
 Schema |         Name          | Type  | Owner
--------+-----------------------+-------+-------
 public | services              | table | kong
 public | routes                | table | kong
 public | plugins               | table | kong
 public | consumers             | table | kong
 public | upstreams             | table | kong
 public | targets               | table | kong
 ...
```

---

## 7. Khi Nào Cần Chạy Lại Migration?

### Tự động chạy lại khi:

- `docker-compose up` lần đầu
- Sau khi `docker-compose down -v` (xóa volumes)

### Thủ công chạy lại:

```powershell
# Nếu cần reset Kong database
docker-compose -f docker-compose.microservices.yml run --rm kong-migration kong migrations reset
docker-compose -f docker-compose.microservices.yml run --rm kong-migration kong migrations bootstrap
```

---

## 8. Troubleshooting

### Lỗi: Migration failed

```powershell
# Xem chi tiết lỗi
docker logs kong_migration

# Kiểm tra database có chạy không
docker ps | Select-String kong_database

# Thử kết nối database
docker exec -it kong_database psql -U kong -d kong -c "SELECT 1"
```

### Lỗi: Database connection refused

```powershell
# Restart database
docker restart kong_database

# Chờ database healthy
Start-Sleep -Seconds 10

# Chạy lại migration
docker-compose -f docker-compose.microservices.yml up -d kong-migration
```

---

## 9. Tóm Tắt

```
┌─────────────────────────────────────────────────────────────┐
│                    KONG MIGRATION                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Mục đích:    Khởi tạo database schema cho Kong             │
│                                                              │
│  Chạy khi:    docker-compose up lần đầu                     │
│                                                              │
│  Thời gian:   5-15 giây                                     │
│                                                              │
│  Sau khi:     Container tự động tắt (EXITED 0)              │
│               → Đây là BÌNH THƯỜNG                          │
│                                                              │
│  Quan hệ:     kong_database → kong_migration → kong_gateway │
│               (phải chạy      (chạy một lần)  (chạy liên    │
│                trước)                          tục)          │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---
