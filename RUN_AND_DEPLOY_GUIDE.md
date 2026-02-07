# 🚀 HƯỚNG DẪN CHẠY & DEPLOY DỰ ÁN

## 📋 MỤC LỤC

- [I. CHẠY DỰ ÁN LOCAL](#i-chạy-dự-án-local)
- [II. DEPLOY LÊN PRODUCTION](#ii-deploy-lên-production)
- [III. TROUBLESHOOTING](#iii-troubleshooting)

---

## I. CHẠY DỰ ÁN LOCAL

### ⚡ CÁCH 1: CHẠY NHANH (Basic - 3 phút)

#### Bước 1: Kiểm tra yêu cầu hệ thống

```powershell
# Kiểm tra PHP
php -v    # Cần PHP 8.2+

# Kiểm tra MySQL
mysql --version

# Kiểm tra Composer
composer --version
```

#### Bước 2: Cài đặt dependencies

```powershell
# Di chuyển vào thư mục dự án
cd D:\Web_Ban_Do_Dien_Tu

# Cài đặt PHP dependencies
composer install
```

#### Bước 3: Cấu hình môi trường

```powershell
# Copy file .env
copy .env.example .env

# Generate app key
php artisan key:generate
```

#### Bước 4: Sửa file `.env`

Mở file `.env` và cấu hình database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=your_password_here

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

#### Bước 5: Import database

```powershell
# Tạo database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS duan"

# Import database
mysql -u root -p duan < duan.sql
```

Hoặc dùng phpMyAdmin:

1. Mở http://localhost/phpmyadmin
2. Tạo database `duan`
3. Import file `duan.sql`

#### Bước 6: Chạy ứng dụng

```powershell
# Chạy Laravel development server
php artisan serve
```

**✅ Mở browser:** http://localhost:8000

**🎉 XONG! Dự án đã chạy!**

---

### 🚀 CÁCH 2: CHẠY FULL FEATURES (với Docker)

#### Bước 1: Cài đặt Docker Desktop

- Download: https://www.docker.com/products/docker-desktop
- Cài đặt và khởi động Docker Desktop

#### Bước 2: Chạy infrastructure stack

```powershell
# Chạy tất cả services (ELK, Prometheus, Grafana, Jaeger, Consul, Kong)
docker-compose -f docker-compose.microservices.yml up -d
```

#### Bước 3: Chạy Redis (cho Queue & Cache)

```powershell
# Cài đặt Redis trên Windows (dùng Memurai)
# Download: https://www.memurai.com/get-memurai

# Hoặc dùng Docker
docker run -d -p 6379:6379 --name redis redis:alpine
```

#### Bước 4: Cấu hình `.env` cho full features

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=duan
DB_USERNAME=root
DB_PASSWORD=your_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Elasticsearch (optional)
ELASTICSEARCH_ENABLED=true
ELASTICSEARCH_HOSTS=localhost:9200

# Consul (optional)
CONSUL_ENABLED=true
CONSUL_HOST=localhost
CONSUL_PORT=8500
```

#### Bước 5: Chạy Queue Worker

```powershell
# Terminal 1: Laravel app
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work --tries=3
```

#### Bước 6: Chạy Notification Service

```powershell
# Terminal 3: Notification microservice
cd notification-service
php consumer.php
```

#### Bước 7: Truy cập các services

| Service          | URL                    | Mô Tả               |
| ---------------- | ---------------------- | ------------------- |
| **Laravel App**  | http://localhost:8000  | Main application    |
| **Kibana**       | http://localhost:5601  | Log visualization   |
| **Grafana**      | http://localhost:3000  | Metrics dashboard   |
| **Jaeger UI**    | http://localhost:16686 | Distributed tracing |
| **Consul UI**    | http://localhost:8500  | Service discovery   |
| **Konga**        | http://localhost:1337  | Kong admin UI       |
| **Kong Gateway** | http://localhost:8000  | API Gateway         |
| **Prometheus**   | http://localhost:9090  | Metrics collection  |

**🎉 FULL STACK RUNNING!**

---

## II. DEPLOY LÊN PRODUCTION

### 🌐 OPTION 1: Deploy lên VPS/Cloud Server (DigitalOcean, AWS, Linode)

#### Bước 1: Chuẩn bị server

```bash
# SSH vào server
ssh root@your-server-ip

# Update system
sudo apt update && sudo apt upgrade -y

# Cài đặt PHP 8.2
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-gd php8.2-redis -y

# Cài đặt MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Cài đặt Nginx
sudo apt install nginx -y

# Cài đặt Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### Bước 2: Upload code lên server

**Cách 1: Dùng Git (Recommended)**

```bash
# Trên server
cd /var/www
sudo git clone https://github.com/your-username/web-ban-do-dien-tu.git
cd web-ban-do-dien-tu

# Set permissions
sudo chown -R www-data:www-data /var/www/web-ban-do-dien-tu
sudo chmod -R 755 /var/www/web-ban-do-dien-tu/storage
sudo chmod -R 755 /var/www/web-ban-do-dien-tu/bootstrap/cache
```

**Cách 2: Dùng FTP/SFTP**

- Dùng FileZilla/WinSCP
- Upload toàn bộ folder dự án vào `/var/www/web-ban-do-dien-tu`

#### Bước 3: Cài đặt dependencies

```bash
cd /var/www/web-ban-do-dien-tu
composer install --optimize-autoloader --no-dev
```

#### Bước 4: Cấu hình môi trường

```bash
# Copy .env
cp .env.example .env

# Generate key
php artisan key:generate

# Sửa .env
nano .env
```

Cấu hình production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=duan_production
DB_USERNAME=duan_user
DB_PASSWORD=strong_password_here

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
```

#### Bước 5: Setup database

```bash
# Tạo database
sudo mysql -u root -p
```

```sql
CREATE DATABASE duan_production;
CREATE USER 'duan_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON duan_production.* TO 'duan_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Import database
mysql -u duan_user -p duan_production < duan.sql
```

#### Bước 6: Cấu hình Nginx

```bash
sudo nano /etc/nginx/sites-available/web-ban-do-dien-tu
```

Nội dung:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/web-ban-do-dien-tu/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/web-ban-do-dien-tu /etc/nginx/sites-enabled/

# Test config
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

#### Bước 7: Setup SSL (HTTPS) với Let's Encrypt

```bash
# Cài đặt Certbot
sudo apt install certbot python3-certbot-nginx -y

# Tạo SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renew
sudo certbot renew --dry-run
```

#### Bước 8: Setup Queue Worker (Production)

```bash
# Cài đặt Supervisor
sudo apt install supervisor -y

# Tạo config
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Nội dung:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/web-ban-do-dien-tu/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/web-ban-do-dien-tu/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

#### Bước 9: Setup Cron Job

```bash
sudo crontab -e
```

Thêm:

```cron
* * * * * cd /var/www/web-ban-do-dien-tu && php artisan schedule:run >> /dev/null 2>&1
```

#### Bước 10: Optimize Laravel

```bash
cd /var/www/web-ban-do-dien-tu

# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload -o
```

**✅ DEPLOY XONG! Website đã live tại https://your-domain.com**

---

### 🐳 OPTION 2: Deploy với Docker (Recommended for Scaling)

#### Bước 1: Tạo Dockerfile

```dockerfile
# File: Dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . /var/www

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
```

#### Bước 2: Tạo docker-compose.production.yml

```yaml
version: "3.8"

services:
    app:
        build:
            context: .
            dockerfile: Dockerfile
        container_name: laravel-app
        restart: unless-stopped
        working_dir: /var/www
        volumes:
            - ./:/var/www
        networks:
            - app-network

    nginx:
        image: nginx:alpine
        container_name: nginx
        restart: unless-stopped
        ports:
            - "80:80"
            - "443:443"
        volumes:
            - ./:/var/www
            - ./docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf
        networks:
            - app-network

    mysql:
        image: mysql:8.0
        container_name: mysql
        restart: unless-stopped
        environment:
            MYSQL_DATABASE: duan_production
            MYSQL_ROOT_PASSWORD: root_password
            MYSQL_USER: duan_user
            MYSQL_PASSWORD: user_password
        volumes:
            - mysql-data:/var/lib/mysql
        networks:
            - app-network

    redis:
        image: redis:alpine
        container_name: redis
        restart: unless-stopped
        networks:
            - app-network

    queue-worker:
        build:
            context: .
            dockerfile: Dockerfile
        container_name: queue-worker
        restart: unless-stopped
        working_dir: /var/www
        command: php artisan queue:work --tries=3
        volumes:
            - ./:/var/www
        networks:
            - app-network

networks:
    app-network:
        driver: bridge

volumes:
    mysql-data:
```

#### Bước 3: Deploy

```bash
# Build và run
docker-compose -f docker-compose.production.yml up -d --build

# Import database
docker exec -i mysql mysql -u duan_user -puser_password duan_production < duan.sql

# Optimize
docker exec laravel-app php artisan config:cache
docker exec laravel-app php artisan route:cache
docker exec laravel-app php artisan view:cache
```

---

### 🌍 OPTION 3: Deploy lên Shared Hosting (cPanel)

#### Bước 1: Chuẩn bị hosting

- Yêu cầu: PHP 8.2+, MySQL 5.7+, SSH access (recommended)
- Kiểm tra: cPanel > PHP Version > 8.2

#### Bước 2: Upload code

**Cách 1: Dùng Git (nếu hosting support)**

```bash
ssh user@your-hosting.com
cd public_html
git clone https://github.com/your-username/web-ban-do-dien-tu.git .
```

**Cách 2: Dùng File Manager/FTP**

1. Nén dự án thành `project.zip` (trừ `vendor/`, `node_modules/`)
2. Upload qua cPanel File Manager hoặc FTP
3. Extract tại `public_html`

#### Bước 3: Cài đặt Composer dependencies

```bash
# SSH vào hosting
cd public_html
composer install --optimize-autoloader --no-dev
```

Hoặc nếu không có SSH:

- Upload folder `vendor/` đã build sẵn từ local

#### Bước 4: Setup database

1. cPanel > MySQL Databases
2. Tạo database: `username_duan`
3. Tạo user và gán quyền
4. cPanel > phpMyAdmin
5. Import file `duan.sql`

#### Bước 5: Cấu hình .env

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_duan
DB_USERNAME=username_duan_user
DB_PASSWORD=password_here
```

#### Bước 6: Fix Laravel public folder

Tạo file `.htaccess` tại root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Hoặc move nội dung `public/` ra root và update `index.php`:

```php
// Thay đổi paths
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

#### Bước 7: Set permissions

```bash
chmod -R 755 storage bootstrap/cache
```

**✅ XONG! Website đã live!**

---

## III. TROUBLESHOOTING

### ❌ Lỗi: "500 Internal Server Error"

**Giải pháp:**

```bash
# Kiểm tra logs
tail -f storage/logs/laravel.log

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### ❌ Lỗi: "Access denied for user"

**Giải pháp:**

```bash
# Kiểm tra .env
cat .env | grep DB_

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### ❌ Lỗi: "Queue not working"

**Giải pháp:**

```bash
# Restart queue worker
php artisan queue:restart

# Check supervisor (production)
sudo supervisorctl status laravel-worker:*
sudo supervisorctl restart laravel-worker:*
```

### ❌ Lỗi: "CSRF token mismatch"

**Giải pháp:**

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Regenerate key
php artisan key:generate
```

### 📊 Health Check

```bash
# Test application
curl http://localhost:8000/api/health

# Expected response:
# {"status":"healthy","timestamp":"2026-01-28T12:00:00Z"}
```

---

---

## 🎯 CHECKLIST DEPLOY

### Development (Local)

- ✅ PHP 8.2+ installed
- ✅ MySQL running
- ✅ Composer installed
- ✅ `.env` configured
- ✅ Database imported
- ✅ `php artisan serve` running

### Production (VPS)

- ✅ Server setup (PHP, MySQL, Nginx)
- ✅ Code uploaded
- ✅ Dependencies installed
- ✅ Database migrated
- ✅ Nginx configured
- ✅ SSL certificate (HTTPS)
- ✅ Queue worker running
- ✅ Cron job setup
- ✅ Cache optimized

### Production (Docker)

- ✅ Docker installed
- ✅ `docker-compose.production.yml` created
- ✅ Services running
- ✅ Database imported
- ✅ Optimizations applied

---
