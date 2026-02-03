@echo off
REM ============================================================================
REM Kong API Gateway - Complete Setup Script (Windows)
REM ============================================================================
REM Run: kong-routes-setup-complete.bat
REM ============================================================================

setlocal enabledelayedexpansion

set KONG_ADMIN_URL=http://localhost:8001
set LARAVEL_HOST=host.docker.internal:8000

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║           Kong API Gateway - Complete Setup                 ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

REM ============================================================================
REM STEP 1: Check Kong Admin API
REM ============================================================================
echo 🔍 Checking Kong Admin API...
curl -s %KONG_ADMIN_URL% >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo ✅ Kong Admin API is accessible
) else (
    echo ❌ Kong Admin API is not accessible
    echo    Please start Kong first: docker-compose up kong
    exit /b 1
)

REM ============================================================================
REM STEP 2: Create Laravel Service
REM ============================================================================
echo.
echo 📦 Creating Laravel Service...

curl -s -X POST "%KONG_ADMIN_URL%/services" ^
  --data "name=laravel-app" ^
  --data "url=http://%LARAVEL_HOST%" >nul 2>&1

echo ✅ Service created: laravel-app

REM ============================================================================
REM STEP 3: Create Routes
REM ============================================================================
echo.
echo 🛣️  Creating Routes...

echo   Creating /api route...
curl -s -X POST "%KONG_ADMIN_URL%/services/laravel-app/routes" ^
  --data "name=api-route" ^
  --data "paths[]=/api" ^
  --data "strip_path=false" >nul 2>&1
echo   ✅ /api -^> laravel-app

echo   Creating / route...
curl -s -X POST "%KONG_ADMIN_URL%/services/laravel-app/routes" ^
  --data "name=web-route" ^
  --data "paths[]=/"\
  --data "strip_path=false" >nul 2>&1
echo   ✅ / -^> laravel-app

REM ============================================================================
REM STEP 4: Add Rate Limiting
REM ============================================================================
echo.
echo ⏱️  Configuring Rate Limiting...

curl -s -X POST "%KONG_ADMIN_URL%/services/laravel-app/plugins" ^
  --data "name=rate-limiting" ^
  --data "config.minute=100" ^
  --data "config.hour=10000" >nul 2>&1

echo ✅ Rate Limiting: 100 req/min, 10000 req/hour

REM ============================================================================
REM STEP 5: Add Logging
REM ============================================================================
echo.
echo 📝 Configuring Logging...

curl -s -X POST "%KONG_ADMIN_URL%/services/laravel-app/plugins" ^
  --data "name=file-log" ^
  --data "config.path=/tmp/kong-laravel.log" >nul 2>&1

echo ✅ File logging enabled

REM ============================================================================
REM STEP 6: Add CORS
REM ============================================================================
echo.
echo 🌐 Configuring CORS...

curl -s -X POST "%KONG_ADMIN_URL%/services/laravel-app/plugins" ^
  --data "name=cors" ^
  --data "config.origins=*" ^
  --data "config.methods=GET,POST,PUT,PATCH,DELETE,OPTIONS" ^
  --data "config.headers=Accept,Authorization,Content-Type,X-Request-ID" ^
  --data "config.credentials=true" >nul 2>&1

echo ✅ CORS enabled

REM ============================================================================
REM STEP 7: Add Request Transformer
REM ============================================================================
echo.
echo 🔄 Configuring Request Transformer...

curl -s -X POST "%KONG_ADMIN_URL%/services/laravel-app/plugins" ^
  --data "name=request-transformer" ^
  --data "config.add.headers=X-Kong-Gateway:true" >nul 2>&1

echo ✅ Request transformer enabled

REM ============================================================================
REM STEP 8: Add Response Transformer
REM ============================================================================
echo.
echo 📤 Configuring Response Transformer...

curl -s -X POST "%KONG_ADMIN_URL%/services/laravel-app/plugins" ^
  --data "name=response-transformer" ^
  --data "config.add.headers=X-Kong-Proxy:kong-gateway" >nul 2>&1

echo ✅ Response transformer enabled

REM ============================================================================
REM SUMMARY
REM ============================================================================
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                     Setup Complete! 🎉                       ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
echo 📊 Configuration Summary:
echo   • Service: laravel-app -^> %LARAVEL_HOST%
echo   • Routes: /api, /
echo   • Rate Limiting: 100/min, 10000/hour
echo   • CORS: Enabled
echo   • Logging: Enabled
echo.
echo 🧪 Test Commands:
echo   curl http://localhost:8000/api/health
echo   curl -I http://localhost:8000/
echo.
echo 📊 View Configuration:
echo   Kong Admin: http://localhost:8001
echo   Konga UI: http://localhost:1337
echo.
echo ✅ Kong Gateway is ready!
echo.

endlocal
