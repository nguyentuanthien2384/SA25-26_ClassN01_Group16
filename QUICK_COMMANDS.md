# ⚡ QUICK COMMANDS - LỆNH NHANH

## 🚀 CHẠY DỰ ÁN

### Chạy Local (Development)

```powershell
# Chạy Laravel server
php artisan serve

# Hoặc chỉ định port khác
php artisan serve --port=8080

# Hoặc chỉ định host
php artisan serve --host=0.0.0.0 --port=8000
```

**Truy cập:** http://localhost:8000

---

## 🔧 SETUP BAN ĐẦU

```powershell
# 1. Cài đặt dependencies
composer install

# 2. Copy .env
copy .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Import database
mysql -u root -p duan < duan.sql

# 5. Chạy
php artisan serve
```

---

## 🗄️ DATABASE

### Import Database

```powershell
# Tạo database
mysql -u root -p -e "CREATE DATABASE duan"

# Import
mysql -u root -p duan < duan.sql
```

### Migration (Nếu dùng migrations)

```powershell
# Chạy migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Reset và chạy lại
php artisan migrate:fresh

# Reset và seed
php artisan migrate:fresh --seed
```

---

## 🧹 CLEAR CACHE

```powershell
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Hoặc clear tất cả một lần
php artisan optimize:clear
```

---

## ⚙️ OPTIMIZE (Production)

```powershell
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload -o

# Optimize tất cả
php artisan optimize
```

---

## 🔄 QUEUE (Background Jobs)

```powershell
# Chạy queue worker
php artisan queue:work

# Với retry
php artisan queue:work --tries=3

# Chỉ định connection
php artisan queue:work redis

# Restart queue
php artisan queue:restart

# Xem failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry all
```

---

## 🔔 NOTIFICATION SERVICE

```powershell
# Chạy notification service
cd notification-service
php consumer.php
```

---

## 🐳 DOCKER

### Docker Compose

```powershell
# Start all services
docker-compose -f docker-compose.microservices.yml up -d

# Stop all services
docker-compose -f docker-compose.microservices.yml down

# View logs
docker-compose -f docker-compose.microservices.yml logs -f

# Restart một service
docker-compose -f docker-compose.microservices.yml restart nginx
```

### Docker Commands

```powershell
# List containers
docker ps

# Stop container
docker stop container_name

# Remove container
docker rm container_name

# View logs
docker logs container_name -f

# Execute command in container
docker exec -it container_name bash
```

---

## 📊 MONITORING & HEALTH

```powershell
# Health check
curl http://localhost:8000/api/health

# Metrics
curl http://localhost:8000/api/metrics

# Circuit breaker status
php artisan circuit-breaker:status

# Reset circuit breaker
php artisan circuit-breaker:reset momo
```

---

## 🔐 PERMISSIONS (Linux/Mac)

```bash
# Set storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Set ownership
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

---

## 📝 ARTISAN COMMANDS

### List all commands

```powershell
php artisan list
```

### Useful commands

```powershell
# Tinker (REPL)
php artisan tinker

# Make controller
php artisan make:controller ProductController

# Make model
php artisan make:model Product

# Make migration
php artisan make:migration create_products_table

# Make seeder
php artisan make:seeder ProductSeeder

# Make middleware
php artisan make:middleware CheckAdmin

# Make request
php artisan make:request ProductRequest

# Make job
php artisan make:job SendEmail

# Make event
php artisan make:event OrderPlaced

# Make listener
php artisan make:listener SendOrderNotification
```

---

## 🔍 DEBUG & LOGS

```powershell
# View Laravel logs (Windows)
type storage\logs\laravel.log

# View last 50 lines
Get-Content storage\logs\laravel.log -Tail 50

# Watch logs (require tail command)
tail -f storage/logs/laravel.log
```

---

## 🌐 GIT COMMANDS

```powershell
# Status
git status

# Add all
git add .

# Commit
git commit -m "Your message"

# Push
git push origin master

# Pull
git pull origin master

# Clone
git clone https://github.com/your-username/repo.git
```

---

## 📦 COMPOSER

```powershell
# Install
composer install

# Update all
composer update

# Add package
composer require package/name

# Remove package
composer remove package/name

# Autoload optimize
composer dump-autoload -o
```

---

## 🧪 TESTING

```powershell
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=ProductTest

# PHPUnit
./vendor/bin/phpunit
```

---

## 🔐 SUPERVISOR (Linux - Queue Worker)

```bash
# Status
sudo supervisorctl status

# Start
sudo supervisorctl start laravel-worker:*

# Stop
sudo supervisorctl stop laravel-worker:*

# Restart
sudo supervisorctl restart laravel-worker:*

# Reload config
sudo supervisorctl reread
sudo supervisorctl update
```

---

## 🌍 NGINX (Linux)

```bash
# Test config
sudo nginx -t

# Reload
sudo systemctl reload nginx

# Restart
sudo systemctl restart nginx

# Status
sudo systemctl status nginx

# Enable on boot
sudo systemctl enable nginx
```

---

## 🗂️ FILE OPERATIONS

```powershell
# Windows
# List files
dir

# Copy
copy source.txt destination.txt

# Move
move source.txt destination.txt

# Delete
del file.txt

# Create directory
mkdir folder_name

# Linux/Mac
# List files
ls -la

# Copy
cp source.txt destination.txt

# Move
mv source.txt destination.txt

# Delete
rm file.txt

# Create directory
mkdir folder_name
```

---

## 📊 MYSQL

```powershell
# Login
mysql -u root -p

# Create database
mysql -u root -p -e "CREATE DATABASE duan"

# Drop database
mysql -u root -p -e "DROP DATABASE duan"

# Import
mysql -u root -p duan < duan.sql

# Export
mysqldump -u root -p duan > backup.sql

# Show databases
mysql -u root -p -e "SHOW DATABASES"
```

---

## 🔄 REDIS

```powershell
# CLI
redis-cli

# Ping
redis-cli ping

# Flush all
redis-cli FLUSHALL

# Get key
redis-cli GET key_name

# Set key
redis-cli SET key_name value

# Monitor
redis-cli MONITOR
```

---

## 📈 PERFORMANCE

```powershell
# Clear + Optimize
php artisan optimize:clear && php artisan optimize

# Cache everything
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Production ready
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🆘 EMERGENCY COMMANDS

```powershell
# Reset everything
php artisan optimize:clear
composer dump-autoload
php artisan key:generate
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Permissions (Linux)
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage bootstrap/cache
```

---

## 📱 QUICK ACCESS URLS

| Service | URL | Default Credentials |
|---------|-----|-------------------|
| **Laravel** | http://localhost:8000 | - |
| **Admin** | http://localhost:8000/admin | admin@gmail.com / admin |
| **Kibana** | http://localhost:5601 | - |
| **Grafana** | http://localhost:3000 | admin / admin |
| **Jaeger** | http://localhost:16686 | - |
| **Consul** | http://localhost:8500 | - |
| **Konga** | http://localhost:1337 | - |
| **Prometheus** | http://localhost:9090 | - |
| **phpMyAdmin** | http://localhost/phpmyadmin | root / your_password |

---

## 🎯 DAILY WORKFLOW

### Morning (Bắt đầu làm việc)

```powershell
# 1. Pull latest code
git pull origin master

# 2. Update dependencies (nếu có thay đổi)
composer install

# 3. Clear cache
php artisan optimize:clear

# 4. Run server
php artisan serve
```

### Development (Khi code)

```powershell
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Queue worker (nếu dùng)
php artisan queue:work

# Terminal 3: Notification service (nếu dùng)
cd notification-service && php consumer.php
```

### Evening (Kết thúc)

```powershell
# 1. Add changes
git add .

# 2. Commit
git commit -m "Describe your changes"

# 3. Push
git push origin master

# 4. Stop servers (Ctrl+C in terminals)
```

---

## 💾 BACKUP

```powershell
# Backup database
mysqldump -u root -p duan > backup_2026_01_28.sql

# Backup code (exclude vendor, node_modules)
# Manually zip project folder or use git

# Backup uploads
# Copy public/upload folder
```

---

## 🔑 .ENV QUICK REFERENCE

```env
# App
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=

# Cache & Queue
CACHE_DRIVER=file|redis
QUEUE_CONNECTION=sync|redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

## 📚 MORE INFO

- Full Guide: [RUN_AND_DEPLOY_GUIDE.md](./RUN_AND_DEPLOY_GUIDE.md)
- Quick Start: [QUICK_RUN.md](./QUICK_RUN.md)
- Troubleshooting: [FIX_GUIDE.md](./FIX_GUIDE.md)
- Documentation: [DOCUMENTATION_INDEX.md](./DOCUMENTATION_INDEX.md)

---

<div align="center">

**⚡ SAVE THIS FILE FOR QUICK REFERENCE ⚡**

</div>
