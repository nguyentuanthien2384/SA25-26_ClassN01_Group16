#!/bin/bash

# ============================================================================
# ELECTROSHOP - DOCKER DEPLOYMENT SCRIPT
# ============================================================================

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║       🐳 ELECTROSHOP - DOCKER DEPLOYMENT                     ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Step 1: Stop all running containers
echo "[1/6] Dừng tất cả containers cũ..."
docker-compose down -v 2>/dev/null
docker stop $(docker ps -aq) 2>/dev/null
docker rm $(docker ps -aq) 2>/dev/null
echo "      ✓ Done"
echo ""

# Step 2: Create .env file
echo "[2/6] Tạo file .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    # Update for Docker
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mysql/g' .env
    sed -i 's/DB_HOST=localhost/DB_HOST=mysql/g' .env
    sed -i 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/g' .env
    sed -i 's/REDIS_HOST=localhost/REDIS_HOST=redis/g' .env
    sed -i 's/DB_PASSWORD=$/DB_PASSWORD=root_password/g' .env
    sed -i 's/DB_PORT=3307/DB_PORT=3306/g' .env
    echo "      ✓ File .env đã được tạo"
else
    echo "      ✓ File .env đã tồn tại"
fi
echo ""

# Step 3: Create required directories
echo "[3/6] Tạo thư mục cần thiết..."
mkdir -p storage/logs
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
chmod -R 777 storage bootstrap/cache 2>/dev/null
echo "      ✓ Done"
echo ""

# Step 4: Build Docker images
echo "[4/6] Build Docker images (có thể mất 3-5 phút)..."
docker-compose build --no-cache
echo "      ✓ Done"
echo ""

# Step 5: Start containers
echo "[5/6] Khởi động containers..."
docker-compose up -d
echo "      ✓ Done"
echo ""

# Step 6: Wait and check
echo "[6/6] Chờ services khởi động (60 giây)..."
sleep 60
echo ""

# Show status
echo "════════════════════════════════════════════════════════════════"
echo "                    TRẠNG THÁI CONTAINERS"
echo "════════════════════════════════════════════════════════════════"
docker-compose ps
echo ""

# Check if services are running
if docker-compose ps | grep -q "Up"; then
    echo "════════════════════════════════════════════════════════════════"
    echo "              ✅ TRIỂN KHAI THÀNH CÔNG!"
    echo "════════════════════════════════════════════════════════════════"
    echo ""
    echo "  🌐 Website:      http://localhost:8000"
    echo "  🔧 Admin:        http://localhost:8000/admin"
    echo "  📊 phpMyAdmin:   http://localhost:8083"
    echo "  🔴 Redis GUI:    http://localhost:8082"
    echo ""
    echo "  📝 Xem logs:     docker-compose logs -f laravel-app"
    echo "  🛑 Dừng:         docker-compose down"
    echo ""
else
    echo "════════════════════════════════════════════════════════════════"
    echo "              ⚠️  CÓ LỖI XẢY RA"
    echo "════════════════════════════════════════════════════════════════"
    echo ""
    echo "  Xem logs để debug:"
    echo "  docker-compose logs laravel-app"
    echo "  docker-compose logs mysql"
    echo ""
fi
