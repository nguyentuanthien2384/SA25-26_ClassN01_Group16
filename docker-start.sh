#!/bin/bash

# ============================================================================
# Docker Deployment Script - ElectroShop
# For Git Bash / Linux / Mac
# ============================================================================

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║       🐳 DOCKER DEPLOYMENT - ELECTROSHOP                     ║"
echo "║       Web Ban Do Dien Tu - Microservices                     ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "[ERROR] Docker Desktop chua chay!"
    echo ""
    echo "Hay mo Docker Desktop va cho no khoi dong xong."
    echo "Sau do chay lai script nay."
    exit 1
fi

echo "[OK] Docker Desktop dang chay"
echo ""

# Menu
echo "Chon che do khoi chay:"
echo ""
echo "  [1] FULL    - Tat ca services (Laravel + MySQL + Redis + ELK + Kong + Monitoring)"
echo "                RAM can thiet: 8GB+"
echo ""
echo "  [2] MEDIUM  - Laravel + MySQL + Redis + Elasticsearch + Kibana"
echo "                RAM can thiet: 4GB+"
echo ""
echo "  [3] LITE    - Chi Laravel + MySQL + Redis (nhe nhat)"
echo "                RAM can thiet: 2GB+"
echo ""
echo "  [4] STOP    - Dung tat ca containers"
echo ""
echo "  [5] LOGS    - Xem logs cua Laravel"
echo ""
echo "  [6] STATUS  - Xem trang thai containers"
echo ""
echo "  [0] EXIT    - Thoat"
echo ""

read -p "Nhap lua chon (0-6): " choice

setup_env() {
    if [ ! -f .env ]; then
        echo "[INFO] Tao file .env tu .env.example..."
        cp .env.example .env
        
        echo "[INFO] Cap nhat cau hinh Docker trong .env..."
        sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mysql/g' .env
        sed -i 's/DB_HOST=localhost/DB_HOST=mysql/g' .env
        sed -i 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/g' .env
        sed -i 's/REDIS_HOST=localhost/REDIS_HOST=redis/g' .env
        sed -i 's/DB_PASSWORD=$/DB_PASSWORD=root_password/g' .env
        sed -i 's|ELASTICSEARCH_HOST=http://localhost:9200|ELASTICSEARCH_HOST=http://elasticsearch:9200|g' .env
        sed -i 's/CONSUL_HOST=localhost/CONSUL_HOST=consul/g' .env
        
        echo "[OK] File .env da duoc tao va cap nhat"
        echo ""
    fi
}

case $choice in
    1)
        echo ""
        echo "════════════════════════════════════════════════════════════════"
        echo " Khoi chay FULL (tat ca services)..."
        echo "════════════════════════════════════════════════════════════════"
        echo ""
        
        setup_env
        
        echo "[1/3] Build va khoi chay containers..."
        docker-compose up -d --build
        
        echo ""
        echo "[2/3] Cho database khoi dong (30 giay)..."
        sleep 30
        
        echo ""
        echo "[3/3] Kiem tra trang thai..."
        docker-compose ps
        
        echo ""
        echo "════════════════════════════════════════════════════════════════"
        echo " HOAN THANH! Truy cap cac URL sau:"
        echo "════════════════════════════════════════════════════════════════"
        echo ""
        echo "  Laravel App:    http://localhost:8000"
        echo "  Admin Panel:    http://localhost:8000/admin"
        echo "  Redis GUI:      http://localhost:8081"
        echo "  Kibana:         http://localhost:5601"
        echo "  Grafana:        http://localhost:3000 (admin/admin)"
        echo "  Consul:         http://localhost:8500"
        echo "  Jaeger:         http://localhost:16686"
        echo "  Kong Admin:     http://localhost:8001"
        echo ""
        ;;
        
    2)
        echo ""
        echo "════════════════════════════════════════════════════════════════"
        echo " Khoi chay MEDIUM (Laravel + DB + ELK)..."
        echo "════════════════════════════════════════════════════════════════"
        echo ""
        
        setup_env
        
        docker-compose up -d mysql redis laravel-app redis-commander elasticsearch kibana
        
        echo ""
        echo "Cho 30 giay de services khoi dong..."
        sleep 30
        
        docker-compose ps
        
        echo ""
        echo "════════════════════════════════════════════════════════════════"
        echo " HOAN THANH! Truy cap:"
        echo "════════════════════════════════════════════════════════════════"
        echo "  Laravel App:    http://localhost:8000"
        echo "  Redis GUI:      http://localhost:8081"
        echo "  Kibana:         http://localhost:5601"
        echo ""
        ;;
        
    3)
        echo ""
        echo "════════════════════════════════════════════════════════════════"
        echo " Khoi chay LITE (chi Laravel + MySQL + Redis)..."
        echo "════════════════════════════════════════════════════════════════"
        echo ""
        
        setup_env
        
        docker-compose up -d mysql redis laravel-app redis-commander
        
        echo ""
        echo "Cho 30 giay de services khoi dong..."
        sleep 30
        
        docker-compose ps
        
        echo ""
        echo "════════════════════════════════════════════════════════════════"
        echo " HOAN THANH! Truy cap:"
        echo "════════════════════════════════════════════════════════════════"
        echo "  Laravel App:    http://localhost:8000"
        echo "  Redis GUI:      http://localhost:8081"
        echo ""
        ;;
        
    4)
        echo ""
        echo "Dung tat ca containers..."
        docker-compose down
        echo ""
        echo "[OK] Da dung tat ca containers"
        ;;
        
    5)
        echo ""
        echo "Xem logs Laravel (Nhan Ctrl+C de thoat)..."
        echo ""
        docker-compose logs -f laravel-app
        ;;
        
    6)
        echo ""
        echo "════════════════════════════════════════════════════════════════"
        echo " TRANG THAI CONTAINERS"
        echo "════════════════════════════════════════════════════════════════"
        echo ""
        docker-compose ps
        echo ""
        ;;
        
    0)
        echo ""
        echo "Tam biet!"
        ;;
        
    *)
        echo "Lua chon khong hop le!"
        ;;
esac
