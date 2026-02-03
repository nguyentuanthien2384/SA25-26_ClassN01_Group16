@echo off
chcp 65001 >nul
title Docker Deployment - ElectroShop

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║       🐳 DOCKER DEPLOYMENT - ELECTROSHOP                     ║
echo ║       Web Ban Do Dien Tu - Microservices                     ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

:: Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker Desktop chua chay!
    echo.
    echo Hay mo Docker Desktop va cho no khoi dong xong.
    echo Sau do chay lai script nay.
    echo.
    pause
    exit /b 1
)

echo [OK] Docker Desktop dang chay
echo.

:: Menu
echo Chon che do khoi chay:
echo.
echo   [1] FULL    - Tat ca services (Laravel + MySQL + Redis + ELK + Kong + Monitoring)
echo                 RAM can thiet: 8GB+
echo.
echo   [2] MEDIUM  - Laravel + MySQL + Redis + Elasticsearch + Kibana
echo                 RAM can thiet: 4GB+
echo.
echo   [3] LITE    - Chi Laravel + MySQL + Redis (nhe nhat)
echo                 RAM can thiet: 2GB+
echo.
echo   [4] STOP    - Dung tat ca containers
echo.
echo   [5] RESET   - Xoa tat ca va khoi dong lai (mat du lieu)
echo.
echo   [6] LOGS    - Xem logs cua Laravel
echo.
echo   [7] STATUS  - Xem trang thai containers
echo.
echo   [0] EXIT    - Thoat
echo.

set /p choice="Nhap lua chon (0-7): "

if "%choice%"=="1" goto full
if "%choice%"=="2" goto medium
if "%choice%"=="3" goto lite
if "%choice%"=="4" goto stop
if "%choice%"=="5" goto reset
if "%choice%"=="6" goto logs
if "%choice%"=="7" goto status
if "%choice%"=="0" goto end

echo Lua chon khong hop le!
pause
goto end

:full
echo.
echo ════════════════════════════════════════════════════════════════
echo  Khoi chay FULL (tat ca services)...
echo ════════════════════════════════════════════════════════════════
echo.

:: Check if .env exists
if not exist .env (
    echo [INFO] Tao file .env tu .env.example...
    copy .env.example .env
    
    echo [INFO] Cap nhat cau hinh Docker trong .env...
    powershell -Command "(Get-Content .env) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=mysql' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'DB_HOST=localhost', 'DB_HOST=mysql' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'REDIS_HOST=127.0.0.1', 'REDIS_HOST=redis' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'REDIS_HOST=localhost', 'REDIS_HOST=redis' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=', 'DB_PASSWORD=root_password' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'ELASTICSEARCH_HOST=http://localhost:9200', 'ELASTICSEARCH_HOST=http://elasticsearch:9200' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'CONSUL_HOST=localhost', 'CONSUL_HOST=consul' | Set-Content .env"
    
    echo [OK] File .env da duoc tao va cap nhat
    echo.
)

echo [1/3] Build va khoi chay containers...
docker-compose up -d --build

echo.
echo [2/3] Cho database khoi dong (30 giay)...
timeout /t 30 /nobreak

echo.
echo [3/3] Kiem tra trang thai...
docker-compose ps

echo.
echo ════════════════════════════════════════════════════════════════
echo  HOAN THANH! Truy cap cac URL sau:
echo ════════════════════════════════════════════════════════════════
echo.
echo   Laravel App:    http://localhost:8000
echo   Admin Panel:    http://localhost:8000/admin
echo   Redis GUI:      http://localhost:8081
echo   Kibana:         http://localhost:5601
echo   Grafana:        http://localhost:3000 (admin/admin)
echo   Consul:         http://localhost:8500
echo   Jaeger:         http://localhost:16686
echo   Kong Admin:     http://localhost:8001
echo.
pause
goto end

:medium
echo.
echo ════════════════════════════════════════════════════════════════
echo  Khoi chay MEDIUM (Laravel + DB + ELK)...
echo ════════════════════════════════════════════════════════════════
echo.

if not exist .env (
    copy .env.example .env
    powershell -Command "(Get-Content .env) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=mysql' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'REDIS_HOST=127.0.0.1', 'REDIS_HOST=redis' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=', 'DB_PASSWORD=root_password' | Set-Content .env"
)

docker-compose up -d mysql redis laravel-app redis-commander elasticsearch kibana

echo.
echo Cho 30 giay de services khoi dong...
timeout /t 30 /nobreak

docker-compose ps

echo.
echo ════════════════════════════════════════════════════════════════
echo  HOAN THANH! Truy cap:
echo ════════════════════════════════════════════════════════════════
echo   Laravel App:    http://localhost:8000
echo   Redis GUI:      http://localhost:8081
echo   Kibana:         http://localhost:5601
echo.
pause
goto end

:lite
echo.
echo ════════════════════════════════════════════════════════════════
echo  Khoi chay LITE (chi Laravel + MySQL + Redis)...
echo ════════════════════════════════════════════════════════════════
echo.

if not exist .env (
    copy .env.example .env
    powershell -Command "(Get-Content .env) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=mysql' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'REDIS_HOST=127.0.0.1', 'REDIS_HOST=redis' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=', 'DB_PASSWORD=root_password' | Set-Content .env"
)

docker-compose up -d mysql redis laravel-app redis-commander

echo.
echo Cho 30 giay de services khoi dong...
timeout /t 30 /nobreak

docker-compose ps

echo.
echo ════════════════════════════════════════════════════════════════
echo  HOAN THANH! Truy cap:
echo ════════════════════════════════════════════════════════════════
echo   Laravel App:    http://localhost:8000
echo   Redis GUI:      http://localhost:8081
echo.
pause
goto end

:stop
echo.
echo Dung tat ca containers...
docker-compose down
echo.
echo [OK] Da dung tat ca containers
pause
goto end

:reset
echo.
echo CANH BAO: Thao tac nay se xoa tat ca du lieu!
set /p confirm="Ban co chac khong? (y/n): "
if /i "%confirm%"=="y" (
    echo.
    echo Xoa containers va volumes...
    docker-compose down -v
    docker system prune -f
    
    echo.
    echo Khoi dong lai...
    docker-compose up -d --build
    
    echo.
    echo Cho 30 giay...
    timeout /t 30 /nobreak
    
    docker-compose ps
    echo.
    echo [OK] Da reset xong
)
pause
goto end

:logs
echo.
echo Xem logs Laravel (Nhan Ctrl+C de thoat)...
echo.
docker-compose logs -f laravel-app
goto end

:status
echo.
echo ════════════════════════════════════════════════════════════════
echo  TRANG THAI CONTAINERS
echo ════════════════════════════════════════════════════════════════
echo.
docker-compose ps
echo.
echo ════════════════════════════════════════════════════════════════
echo  SU DUNG TAI NGUYEN
echo ════════════════════════════════════════════════════════════════
echo.
docker stats --no-stream
echo.
pause
goto end

:end
echo.
echo Tam biet!
