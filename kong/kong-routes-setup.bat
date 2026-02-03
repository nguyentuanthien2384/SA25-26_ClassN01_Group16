@echo off
REM ============================================================================
REM Kong API Gateway - Routes Configuration Script (Windows)
REM ============================================================================
REM This script configures Kong routes to proxy requests to Laravel app
REM Run: kong\kong-routes-setup.bat
REM ============================================================================

setlocal enabledelayedexpansion

set KONG_ADMIN_URL=http://localhost:8001
set LARAVEL_APP_URL=http://host.docker.internal:80

echo.
echo ============================================================================
echo   Kong API Gateway - Routes Configuration
echo ============================================================================
echo.

REM ============================================================================
REM 1. Create Laravel App Service
REM ============================================================================
echo [1/4] Creating Laravel App Service...
echo.

curl -s -X POST %KONG_ADMIN_URL%/services ^
  --data "name=laravel-app" ^
  --data "url=%LARAVEL_APP_URL%" ^
  --data "retries=5" ^
  --data "connect_timeout=60000" ^
  --data "write_timeout=60000" ^
  --data "read_timeout=60000" > nul

if errorlevel 1 (
    echo [ERROR] Failed to create service
    exit /b 1
)

echo [SUCCESS] Service created: laravel-app
echo.

REM ============================================================================
REM 2. Create Routes
REM ============================================================================
echo [2/4] Creating Routes...
echo.

echo   - Creating route: / (Homepage)
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/routes ^
  --data "name=homepage" ^
  --data "paths[]=/" ^
  --data "strip_path=false" ^
  --data "preserve_host=true" > nul
echo   [OK] Homepage route created
echo.

echo   - Creating route: /api/products
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/routes ^
  --data "name=api-products" ^
  --data "paths[]=/api/products" ^
  --data "strip_path=false" ^
  --data "preserve_host=true" > nul
echo   [OK] API products route created
echo.

echo   - Creating route: /san-pham (Search)
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/routes ^
  --data "name=search-category" ^
  --data "paths[]=/san-pham" ^
  --data "paths[]=/danh-muc" ^
  --data "strip_path=false" ^
  --data "preserve_host=true" > nul
echo   [OK] Search routes created
echo.

echo   - Creating route: /gio-hang (Cart)
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/routes ^
  --data "name=cart" ^
  --data "paths[]=/gio-hang" ^
  --data "paths[]=/thanh-toan" ^
  --data "strip_path=false" ^
  --data "preserve_host=true" > nul
echo   [OK] Cart routes created
echo.

echo   - Creating route: /tai-khoan (Account)
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/routes ^
  --data "name=user-account" ^
  --data "paths[]=/tai-khoan" ^
  --data "paths[]=/don-hang" ^
  --data "strip_path=false" ^
  --data "preserve_host=true" > nul
echo   [OK] Account routes created
echo.

echo   - Creating route: /admin
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/routes ^
  --data "name=admin-panel" ^
  --data "paths[]=/admin" ^
  --data "strip_path=false" ^
  --data "preserve_host=true" > nul
echo   [OK] Admin route created
echo.

echo   - Creating route: /health
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/routes ^
  --data "name=health-check" ^
  --data "paths[]=/health" ^
  --data "paths[]=/api/health" ^
  --data "strip_path=false" ^
  --data "preserve_host=true" > nul
echo   [OK] Health check route created
echo.

REM ============================================================================
REM 3. Configure Plugins
REM ============================================================================
echo [3/4] Configuring Plugins...
echo.

echo   - Enabling CORS plugin...
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/plugins ^
  --data "name=cors" ^
  --data "config.origins=*" ^
  --data "config.methods=GET,POST,PUT,DELETE,OPTIONS" ^
  --data "config.headers=Accept,Authorization,Content-Type,X-CSRF-TOKEN" ^
  --data "config.credentials=true" > nul
echo   [OK] CORS enabled
echo.

echo   - Enabling Rate Limiting...
curl -s -X POST %KONG_ADMIN_URL%/services/laravel-app/plugins ^
  --data "name=rate-limiting" ^
  --data "config.minute=100" ^
  --data "config.policy=local" > nul
echo   [OK] Rate Limiting enabled (100 req/min)
echo.

echo   - Enabling Prometheus Metrics...
curl -s -X POST %KONG_ADMIN_URL%/plugins ^
  --data "name=prometheus" > nul
echo   [OK] Prometheus enabled
echo.

REM ============================================================================
REM 4. Summary
REM ============================================================================
echo [4/4] Configuration Complete!
echo.
echo ============================================================================
echo   KONG GATEWAY - CONFIGURATION SUMMARY
echo ============================================================================
echo.
echo Service: laravel-app
echo   URL: %LARAVEL_APP_URL%
echo.
echo Routes Created:
echo   1. / (Homepage)
echo   2. /api/products (API)
echo   3. /san-pham (Search)
echo   4. /gio-hang (Cart)
echo   5. /tai-khoan (Account)
echo   6. /admin (Admin Panel)
echo   7. /health (Health Check)
echo.
echo Plugins Enabled:
echo   - CORS
echo   - Rate Limiting (100 req/min)
echo   - Prometheus Metrics
echo.
echo ============================================================================
echo   TEST YOUR SETUP
echo ============================================================================
echo.
echo 1. Test Homepage:
echo    curl http://localhost:8000/
echo.
echo 2. Test API:
echo    curl http://localhost:8000/api/products/hot
echo.
echo 3. View Admin:
echo    http://localhost:8001
echo.
echo 4. View Konga UI:
echo    http://localhost:1337
echo.
echo 5. View Metrics:
echo    http://localhost:8000/metrics
echo.
echo ============================================================================
echo   Documentation: kong\KONG_SETUP.md
echo ============================================================================
echo.

endlocal
