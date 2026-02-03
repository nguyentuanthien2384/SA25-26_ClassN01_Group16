# ============================================================================
# KONG API GATEWAY - Routes Configuration
# ============================================================================
# Run this script AFTER docker-compose is up and Kong is healthy
# ============================================================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Setting up Kong API Gateway Routes   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

$KONG_ADMIN = "http://localhost:8001"

# Wait for Kong to be ready
Write-Host "`nWaiting for Kong to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# ============================================================================
# 1. CATALOG SERVICE
# ============================================================================
Write-Host "`n[1/4] Setting up Catalog Service..." -ForegroundColor Green

# Create Service
Invoke-RestMethod -Uri "$KONG_ADMIN/services" -Method Post -Body @{
    name = "catalog-service"
    url = "http://catalog-service:9001"
} -ErrorAction SilentlyContinue

# Create Route for Products
Invoke-RestMethod -Uri "$KONG_ADMIN/services/catalog-service/routes" -Method Post -Body @{
    name = "catalog-products-route"
    paths = "/api/products"
    strip_path = "false"
} -ErrorAction SilentlyContinue

# Create Route for Categories
Invoke-RestMethod -Uri "$KONG_ADMIN/services/catalog-service/routes" -Method Post -Body @{
    name = "catalog-categories-route"
    paths = "/api/categories"
    strip_path = "false"
} -ErrorAction SilentlyContinue

Write-Host "  Catalog Service configured!" -ForegroundColor Green

# ============================================================================
# 2. ORDER SERVICE
# ============================================================================
Write-Host "`n[2/4] Setting up Order Service..." -ForegroundColor Green

# Create Service
Invoke-RestMethod -Uri "$KONG_ADMIN/services" -Method Post -Body @{
    name = "order-service"
    url = "http://order-service:9002"
} -ErrorAction SilentlyContinue

# Create Route for Orders
Invoke-RestMethod -Uri "$KONG_ADMIN/services/order-service/routes" -Method Post -Body @{
    name = "order-orders-route"
    paths = "/api/orders"
    strip_path = "false"
} -ErrorAction SilentlyContinue

# Create Route for Cart
Invoke-RestMethod -Uri "$KONG_ADMIN/services/order-service/routes" -Method Post -Body @{
    name = "order-cart-route"
    paths = "/api/cart"
    strip_path = "false"
} -ErrorAction SilentlyContinue

Write-Host "  Order Service configured!" -ForegroundColor Green

# ============================================================================
# 3. USER SERVICE
# ============================================================================
Write-Host "`n[3/4] Setting up User Service..." -ForegroundColor Green

# Create Service
Invoke-RestMethod -Uri "$KONG_ADMIN/services" -Method Post -Body @{
    name = "user-service"
    url = "http://user-service:9003"
} -ErrorAction SilentlyContinue

# Create Route for Users
Invoke-RestMethod -Uri "$KONG_ADMIN/services/user-service/routes" -Method Post -Body @{
    name = "user-users-route"
    paths = "/api/users"
    strip_path = "false"
} -ErrorAction SilentlyContinue

# Create Route for Auth
Invoke-RestMethod -Uri "$KONG_ADMIN/services/user-service/routes" -Method Post -Body @{
    name = "user-auth-route"
    paths = "/api/auth"
    strip_path = "false"
} -ErrorAction SilentlyContinue

Write-Host "  User Service configured!" -ForegroundColor Green

# ============================================================================
# 4. ENABLE PLUGINS
# ============================================================================
Write-Host "`n[4/4] Enabling Kong Plugins..." -ForegroundColor Green

# Rate Limiting Plugin
Invoke-RestMethod -Uri "$KONG_ADMIN/plugins" -Method Post -Body @{
    name = "rate-limiting"
    config = @{
        minute = 100
        policy = "local"
    } | ConvertTo-Json
} -ContentType "application/json" -ErrorAction SilentlyContinue

# CORS Plugin
Invoke-RestMethod -Uri "$KONG_ADMIN/plugins" -Method Post -Body @{
    name = "cors"
    config = @{
        origins = "*"
        methods = "GET,POST,PUT,DELETE,OPTIONS"
        headers = "Content-Type,Authorization"
    } | ConvertTo-Json
} -ContentType "application/json" -ErrorAction SilentlyContinue

Write-Host "  Plugins enabled!" -ForegroundColor Green

# ============================================================================
# SUMMARY
# ============================================================================
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Kong Setup Complete!                 " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "`nAPI Gateway Endpoints:" -ForegroundColor Yellow
Write-Host "  Products:   http://localhost:8000/api/products"
Write-Host "  Categories: http://localhost:8000/api/categories"
Write-Host "  Orders:     http://localhost:8000/api/orders"
Write-Host "  Cart:       http://localhost:8000/api/cart"
Write-Host "  Users:      http://localhost:8000/api/users"
Write-Host "  Auth:       http://localhost:8000/api/auth"
Write-Host "`nAdmin UIs:" -ForegroundColor Yellow
Write-Host "  Kong Admin:    http://localhost:8001"
Write-Host "  Konga GUI:     http://localhost:1337"
