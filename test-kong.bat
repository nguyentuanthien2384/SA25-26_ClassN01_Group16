@echo off
REM ============================================================================
REM Kong Test Script - Quick test all endpoints
REM ============================================================================

echo.
echo ============================================================================
echo   KONG API GATEWAY - QUICK TEST
echo ============================================================================
echo.

REM ============================================================================
REM Check Prerequisites
REM ============================================================================
echo [1/6] Checking prerequisites...
echo.

REM Check if curl is available
where curl >nul 2>&1
if errorlevel 1 (
    echo [ERROR] curl not found. Please install curl or use PowerShell.
    exit /b 1
)

echo   [OK] curl is available
echo.

REM ============================================================================
REM Test Kong Admin API
REM ============================================================================
echo [2/6] Testing Kong Admin API (port 8001)...
echo.

curl -s -o nul -w "%%{http_code}" http://localhost:8001 > %TEMP%\kong_test.txt 2>&1
set /p KONG_STATUS=<%TEMP%\kong_test.txt

if "%KONG_STATUS%"=="200" (
    echo   [SUCCESS] Kong Admin API is running!
    set KONG_RUNNING=1
) else (
    echo   [WARNING] Kong Admin API not accessible
    echo   [INFO] Kong may not be running
    set KONG_RUNNING=0
)
echo.

REM ============================================================================
REM Test Laravel App
REM ============================================================================
echo [3/6] Testing Laravel App (port 8000)...
echo.

curl -s -o nul -w "%%{http_code}" http://localhost:8000 > %TEMP%\laravel_test.txt 2>&1
set /p LARAVEL_STATUS=<%TEMP%\laravel_test.txt

if "%LARAVEL_STATUS%"=="200" (
    echo   [SUCCESS] Laravel App is running!
    set LARAVEL_RUNNING=1
) else (
    echo   [WARNING] Laravel App not accessible on port 8000
    echo   [INFO] Try: php artisan serve
    set LARAVEL_RUNNING=0
)
echo.

REM ============================================================================
REM Test Health Endpoint
REM ============================================================================
echo [4/6] Testing Health Endpoint...
echo.

curl -s http://localhost:8000/health > %TEMP%\health_test.txt 2>&1
findstr /C:"healthy" %TEMP%\health_test.txt >nul 2>&1
if errorlevel 1 (
    echo   [WARNING] Health endpoint not responding correctly
) else (
    echo   [SUCCESS] Health endpoint is OK!
    echo   Response:
    type %TEMP%\health_test.txt
    echo.
)
echo.

REM ============================================================================
REM Test API Endpoints
REM ============================================================================
echo [5/6] Testing API Endpoints...
echo.

REM Test Hot Products
echo   Testing /api/products/hot...
curl -s -o nul -w "%%{http_code}" http://localhost:8000/api/products/hot > %TEMP%\api_test.txt 2>&1
set /p API_STATUS=<%TEMP%\api_test.txt
if "%API_STATUS%"=="200" (
    echo   [OK] API Products Hot: Working
) else (
    echo   [WARNING] API Products Hot: Not accessible
)

REM Test New Products
echo   Testing /api/products/new...
curl -s -o nul -w "%%{http_code}" http://localhost:8000/api/products/new > %TEMP%\api_test2.txt 2>&1
set /p API_STATUS2=<%TEMP%\api_test2.txt
if "%API_STATUS2%"=="200" (
    echo   [OK] API Products New: Working
) else (
    echo   [WARNING] API Products New: Not accessible
)

echo.

REM ============================================================================
REM Summary
REM ============================================================================
echo [6/6] Test Summary
echo.
echo ============================================================================
echo   RESULTS
echo ============================================================================
echo.

if "%KONG_RUNNING%"=="1" (
    echo   Kong Admin API:      [RUNNING] Port 8001
) else (
    echo   Kong Admin API:      [NOT RUNNING]
)

if "%LARAVEL_RUNNING%"=="1" (
    echo   Laravel App:         [RUNNING] Port 8000
) else (
    echo   Laravel App:         [NOT RUNNING]
)

if "%API_STATUS%"=="200" (
    echo   API Endpoints:       [WORKING]
) else (
    echo   API Endpoints:       [NOT WORKING]
)

echo.
echo ============================================================================
echo   RECOMMENDATIONS
echo ============================================================================
echo.

if "%KONG_RUNNING%"=="0" (
    echo   To start Kong:
    echo   1. Open Docker Desktop
    echo   2. Run: docker-compose -f docker-compose.microservices.yml up -d
    echo   3. Wait 30 seconds
    echo   4. Run: kong\kong-routes-setup.bat
    echo.
)

if "%LARAVEL_RUNNING%"=="0" (
    echo   To start Laravel:
    echo   1. Run: php artisan serve
    echo   2. Visit: http://localhost:8000
    echo.
)

if "%KONG_RUNNING%"=="1" if "%LARAVEL_RUNNING%"=="1" (
    echo   [SUCCESS] Everything is running!
    echo.
    echo   Access points:
    echo   - Homepage:     http://localhost:8000/
    echo   - API:          http://localhost:8000/api/products/hot
    echo   - Health Check: http://localhost:8000/health
    echo   - Kong Admin:   http://localhost:8001
    echo   - Konga UI:     http://localhost:1337
    echo.
)

echo ============================================================================
echo   For detailed testing: See KONG_TEST_MANUAL.md
echo ============================================================================
echo.

REM Cleanup
del %TEMP%\kong_test.txt 2>nul
del %TEMP%\laravel_test.txt 2>nul
del %TEMP%\health_test.txt 2>nul
del %TEMP%\api_test.txt 2>nul
del %TEMP%\api_test2.txt 2>nul

pause
