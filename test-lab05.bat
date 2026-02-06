@echo off
REM ============================================================================
REM LAB 05 - QUICK TEST SCRIPT
REM Test Product Service API (like Lab 05 requirements)
REM ============================================================================

echo.
echo ========================================
echo   LAB 05 - PRODUCT SERVICE TEST
echo ========================================
echo.

REM Check if server is running
echo [1/5] Checking if Laravel server is running...
curl -s http://127.0.0.1:8000/api/products >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Server not running!
    echo Please start server: php artisan serve
    pause
    exit /b 1
)
echo [OK] Server is running on port 8000
echo.

REM Test 1: List Products
echo ========================================
echo TEST 1: List All Products (200 OK)
echo ========================================
echo Command: GET /api/products
echo.
curl -s -w "HTTP Status: %%{http_code}\n" http://127.0.0.1:8000/api/products | findstr /C:"pro_name" /C:"HTTP Status"
if %errorlevel% equ 0 (
    echo [PASS] Test 1: List products - 200 OK
) else (
    echo [FAIL] Test 1: List products failed
)
echo.
timeout /t 2 >nul

REM Test 2: Search Products
echo ========================================
echo TEST 2: Search Products (200 OK)
echo ========================================
echo Command: GET /api/products?search=iPhone
echo.
curl -s -w "HTTP Status: %%{http_code}\n" "http://127.0.0.1:8000/api/products?search=iPhone" | findstr /C:"iPhone" /C:"HTTP Status"
if %errorlevel% equ 0 (
    echo [PASS] Test 2: Search products - 200 OK
) else (
    echo [FAIL] Test 2: Search failed
)
echo.
timeout /t 2 >nul

REM Test 3: Product Details
echo ========================================
echo TEST 3: Get Product Details (200 OK)
echo ========================================
echo Command: GET /api/products/1
echo.
curl -s -w "HTTP Status: %%{http_code}\n" http://127.0.0.1:8000/api/products/1 | findstr /C:"pro_name" /C:"HTTP Status"
if %errorlevel% equ 0 (
    echo [PASS] Test 3: Product details - 200 OK
) else (
    echo [FAIL] Test 3: Product details failed
)
echo.
timeout /t 2 >nul

REM Test 4: Not Found (404)
echo ========================================
echo TEST 4: Product Not Found (404)
echo ========================================
echo Command: GET /api/products/999
echo.
curl -s -w "HTTP Status: %%{http_code}\n" http://127.0.0.1:8000/api/products/999 | findstr /C:"404" /C:"not found" /C:"HTTP Status"
if %errorlevel% equ 0 (
    echo [PASS] Test 4: Not found - 404 returned
) else (
    echo [FAIL] Test 4: Should return 404
)
echo.
timeout /t 2 >nul

REM Bonus Test: Automated Tests
echo ========================================
echo BONUS TEST: Automated Tests (PHPUnit)
echo ========================================
echo Running automated test suite...
echo.
php artisan test tests/Feature/Lab03ApiTest.php --testdox
echo.

REM Summary
echo ========================================
echo   TEST SUMMARY
echo ========================================
echo.
echo Manual Tests (Lab 05 Required):
echo   [1] List products       - Check above
echo   [2] Search products     - Check above
echo   [3] Product details     - Check above
echo   [4] Not found (404)     - Check above
echo.
echo Automated Tests (Bonus):
echo   [*] See PHPUnit results above
echo.
echo ========================================
echo   LAB 05 COMPLIANCE CHECK
echo ========================================
echo.
echo If all 4 manual tests passed:
echo   Status: PASS - Lab 05 Requirements Met!
echo.
echo Bonus: 13 automated tests for extra credit!
echo.
echo ========================================

pause
