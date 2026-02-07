# 📋 Hướng Dẫn Lệnh Docker - Microservices

> **Tài liệu tham khảo nhanh** cho việc quản lý Docker containers trong dự án ElectroShop Microservices.

---

## 📑 Mục Lục

1. [Khởi Động Services](#1-khởi-động-services)
2. [Xem Trạng Thái](#2-xem-trạng-thái)
3. [Xem Logs](#3-xem-logs)
4. [Dừng Services](#4-dừng-services)
5. [Khởi Động Lại](#5-khởi-động-lại)
6. [Truy Cập Container](#6-truy-cập-container)
7. [Database Commands](#7-database-commands)
8. [Xem Resources](#8-xem-resources)
9. [Network Commands](#9-network-commands)
10. [Troubleshooting](#10-troubleshooting)
11. [URLs Truy Cập](#11-urls-truy-cập)
12. [Quick Reference](#12-quick-reference)

---

## 1. Khởi Động Services

### Di chuyển đến thư mục dự án

```powershell
cd d:\Web_Ban_Do_Dien_Tu
```

### Khởi động tất cả containers (chạy nền)

```powershell
docker-compose -f docker-compose.microservices.yml up -d
```

### Khởi động và build lại images

```powershell
docker-compose -f docker-compose.microservices.yml up -d --build
```

### Khởi động một service cụ thể

```powershell
docker-compose -f docker-compose.microservices.yml up -d catalog-service
```

### Khởi động với logs hiển thị (foreground)

```powershell
docker-compose -f docker-compose.microservices.yml up
```

---

## 2. Xem Trạng Thái

### Xem tất cả containers đang chạy

```powershell
docker ps
```

### Xem với format đẹp

```powershell
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
```

### Xem tất cả containers (kể cả đã dừng)

```powershell
docker ps -a
```

### Xem chỉ tên containers

```powershell
docker ps --format "{{.Names}}"
```

### Xem chi tiết một container

```powershell
docker inspect catalog_service
```

### Xem health status

```powershell
docker inspect --format='{{.Name}}: {{.State.Health.Status}}' mysql_catalog
```

### Xem health của tất cả databases

```powershell
docker inspect --format='{{.Name}}: {{.State.Health.Status}}' mysql_catalog mysql_order mysql_user
```

---

## 3. Xem Logs

### Xem logs của một service

```powershell
docker logs catalog_service
```

### Xem logs realtime (theo dõi liên tục)

```powershell
docker logs -f catalog_service
```

### Xem 50 dòng cuối

```powershell
docker logs --tail 50 catalog_service
```

### Xem 100 dòng cuối với timestamps

```powershell
docker logs --tail 100 -t catalog_service
```

### Xem logs từ thời điểm cụ thể

```powershell
docker logs --since 30m catalog_service
```

### Xem logs của tất cả services

```powershell
docker-compose -f docker-compose.microservices.yml logs
```

### Xem logs realtime tất cả services

```powershell
docker-compose -f docker-compose.microservices.yml logs -f
```

### Xem logs của nhiều services

```powershell
docker-compose -f docker-compose.microservices.yml logs catalog-service order-service
```

---

## 4. Dừng Services

### Dừng một container

```powershell
docker stop catalog_service
```

### Dừng nhiều containers

```powershell
docker stop catalog_service order_service user_service
```

### Dừng tất cả containers của project

```powershell
docker-compose -f docker-compose.microservices.yml stop
```

### Dừng và xóa containers (giữ data)

```powershell
docker-compose -f docker-compose.microservices.yml down
```

### ⚠️ Dừng, xóa containers VÀ xóa volumes (MẤT DATA!)

```powershell
docker-compose -f docker-compose.microservices.yml down -v
```

### Dừng và xóa kèm orphan containers

```powershell
docker-compose -f docker-compose.microservices.yml down --remove-orphans
```

---

## 5. Khởi Động Lại

### Restart một container

```powershell
docker restart catalog_service
```

### Restart nhiều containers

```powershell
docker restart catalog_service order_service user_service
```

### Restart tất cả

```powershell
docker-compose -f docker-compose.microservices.yml restart
```

### Start containers đã dừng

```powershell
docker-compose -f docker-compose.microservices.yml start
```

### Start một service cụ thể

```powershell
docker-compose -f docker-compose.microservices.yml start catalog-service
```

---

## 6. Truy Cập Container

### Truy cập bash của container

```powershell
docker exec -it catalog_service bash
```

### Truy cập sh (nếu không có bash)

```powershell
docker exec -it catalog_service sh
```

### Chạy lệnh trong container

```powershell
docker exec catalog_service php artisan cache:clear
```

### Chạy nhiều lệnh

```powershell
docker exec catalog_service php artisan config:clear
docker exec catalog_service php artisan route:clear
docker exec catalog_service php artisan view:clear
```

### Clear tất cả cache

```powershell
docker exec catalog_service php artisan optimize:clear
```

---

## 7. Database Commands

### Truy cập MySQL Catalog

```powershell
docker exec -it mysql_catalog mysql -u root -pcatalog_root_pass
```

### Truy cập MySQL Order

```powershell
docker exec -it mysql_order mysql -u root -porder_root_pass
```

### Truy cập MySQL User

```powershell
docker exec -it mysql_user mysql -u root -puser_root_pass
```

### Chạy query trực tiếp

```powershell
docker exec mysql_catalog mysql -u root -pcatalog_root_pass -e "SHOW DATABASES;"
```

### Xem tables trong database

```powershell
docker exec mysql_catalog mysql -u root -pcatalog_root_pass -e "USE catalog_db; SHOW TABLES;"
```

### Export database

```powershell
docker exec mysql_catalog mysqldump -u root -pcatalog_root_pass catalog_db > catalog_backup.sql
```

### Import database

```powershell
docker exec -i mysql_catalog mysql -u root -pcatalog_root_pass catalog_db < catalog_backup.sql
```

### Chạy migrations (trong Laravel service)

```powershell
docker exec catalog_service php artisan migrate
```

### Rollback migrations

```powershell
docker exec catalog_service php artisan migrate:rollback
```

---

## 8. Xem Resources

### Xem CPU, Memory của tất cả containers

```powershell
docker stats
```

### Xem một lần (không refresh)

```powershell
docker stats --no-stream
```

### Xem một container cụ thể

```powershell
docker stats catalog_service
```

### Xem với format tùy chỉnh

```powershell
docker stats --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}"
```

### Xem disk usage

```powershell
docker system df
```

### Xem chi tiết disk usage

```powershell
docker system df -v
```

---

## 9. Network Commands

### Xem tất cả networks

```powershell
docker network ls
```

### Xem chi tiết network

```powershell
docker network inspect ms_network
```

### Xem containers trong network

```powershell
docker network inspect ms_network --format='{{range .Containers}}{{.Name}} {{end}}'
```

### Ping giữa các containers

```powershell
docker exec catalog_service ping mysql-catalog -c 3
```

### Test kết nối database

```powershell
docker exec catalog_service php artisan db:monitor
```

---

## 10. Troubleshooting

### Container không khởi động được

```powershell
# Xem logs để tìm lỗi
docker logs --tail 100 catalog_service

# Xem events
docker events --filter 'container=catalog_service'
```

### Container restart liên tục

```powershell
# Xem exit code
docker inspect catalog_service --format='{{.State.ExitCode}}'

# Xem lý do dừng
docker inspect catalog_service --format='{{.State.Error}}'
```

### Kiểm tra health check

```powershell
# Xem health status
docker inspect --format='{{json .State.Health}}' mysql_catalog
```

### Xóa containers có vấn đề và tạo lại

```powershell
docker rm -f catalog_service
docker-compose -f docker-compose.microservices.yml up -d catalog-service
```

### Rebuild một service

```powershell
docker-compose -f docker-compose.microservices.yml up -d --build --force-recreate catalog-service
```

### Xóa tất cả và bắt đầu lại

```powershell
docker-compose -f docker-compose.microservices.yml down -v
docker-compose -f docker-compose.microservices.yml up -d --build
```

### Xóa images không dùng

```powershell
docker image prune -a
```

### Xóa tất cả resources không dùng

```powershell
docker system prune -a
```

---

## 11. URLs Truy Cập

### Business Services

| Service              | URL                   |
| -------------------- | --------------------- |
| Kong Gateway (API)   | http://localhost:9000 |
| Catalog Service      | http://localhost:9005 |
| Order Service        | http://localhost:9002 |
| User Service         | http://localhost:9003 |
| Notification Service | http://localhost:9004 |

### Admin & Management

| Service         | URL                    | Credentials              |
| --------------- | ---------------------- | ------------------------ |
| Konga (Kong UI) | http://localhost:1337  | Tạo account mới          |
| phpMyAdmin      | http://localhost:9083  | root / catalog_root_pass |
| Redis Commander | http://localhost:9082  | -                        |
| RabbitMQ        | http://localhost:15672 | admin / admin123         |

### Monitoring

| Service    | URL                    | Credentials      |
| ---------- | ---------------------- | ---------------- |
| Grafana    | http://localhost:3000  | admin / admin123 |
| Prometheus | http://localhost:9090  | -                |
| Jaeger     | http://localhost:16686 | -                |
| Consul     | http://localhost:8500  | -                |

### Email Testing

| Service | URL                   |
| ------- | --------------------- |
| MailHog | http://localhost:8025 |

---

## 12. Quick Reference

### 🟢 Các Lệnh Thường Dùng Nhất

```powershell
# Xem trạng thái
docker ps

# Xem logs
docker logs -f catalog_service

# Restart service
docker restart catalog_service

# Dừng tất cả
docker-compose -f docker-compose.microservices.yml stop

# Khởi động tất cả
docker-compose -f docker-compose.microservices.yml start

# Rebuild và restart
docker-compose -f docker-compose.microservices.yml up -d --build
```

### 📊 Bảng Tóm Tắt

| Hành động        | Lệnh                                                               |
| ---------------- | ------------------------------------------------------------------ |
| Xem containers   | `docker ps`                                                        |
| Xem logs         | `docker logs -f <name>`                                            |
| Dừng 1 container | `docker stop <name>`                                               |
| Dừng tất cả      | `docker-compose -f docker-compose.microservices.yml stop`          |
| Start tất cả     | `docker-compose -f docker-compose.microservices.yml start`         |
| Restart          | `docker restart <name>`                                            |
| Vào container    | `docker exec -it <name> bash`                                      |
| Xem resources    | `docker stats`                                                     |
| Rebuild          | `docker-compose -f docker-compose.microservices.yml up -d --build` |
| Xóa tất cả       | `docker-compose -f docker-compose.microservices.yml down -v`       |

### 📝 Danh Sách Container Names

```
catalog_service
order_service
user_service
notification_service
kong_gateway
konga_gui
kong_database
mysql_catalog
mysql_order
mysql_user
ms_redis_cache
ms_redis_commander
ms_phpmyadmin
rabbitmq_broker
consul_discovery
prometheus
grafana
jaeger_tracing
mailhog
```

---
