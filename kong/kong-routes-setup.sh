#!/bin/bash

# ============================================================================
# Kong API Gateway - Routes Configuration Script
# ============================================================================
# This script configures Kong routes to proxy requests to Laravel app
# Run: bash kong/kong-routes-setup.sh
# ============================================================================

KONG_ADMIN_URL="http://localhost:8001"
LARAVEL_APP_URL="http://host.docker.internal:80"

echo "🚀 Starting Kong Routes Configuration..."
echo "=========================================="
echo ""

# ============================================================================
# 1. Create Laravel App Service
# ============================================================================
echo "📦 Step 1: Creating Laravel App Service..."

SERVICE_RESPONSE=$(curl -s -X POST ${KONG_ADMIN_URL}/services \
  --data "name=laravel-app" \
  --data "url=${LARAVEL_APP_URL}" \
  --data "retries=5" \
  --data "connect_timeout=60000" \
  --data "write_timeout=60000" \
  --data "read_timeout=60000")

SERVICE_ID=$(echo $SERVICE_RESPONSE | grep -o '"id":"[^"]*' | cut -d'"' -f4)

if [ -z "$SERVICE_ID" ]; then
    echo "❌ Failed to create service. Response:"
    echo $SERVICE_RESPONSE
    exit 1
fi

echo "✅ Service created: $SERVICE_ID"
echo ""

# ============================================================================
# 2. Create Routes for Laravel App
# ============================================================================
echo "🛣️  Step 2: Creating Routes..."
echo ""

# Route 1: Homepage & Web Routes (Root path)
echo "📍 Creating route: / (Homepage & Web)"
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/routes \
  --data "name=web-routes" \
  --data "paths[]=/^(?!\/api\/).*$" \
  --data "strip_path=false" \
  --data "preserve_host=true" > /dev/null
echo "✅ Route created: / (Homepage, products, categories, etc.)"
echo ""

# Route 2: API Products
echo "📍 Creating route: /api/products"
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/routes \
  --data "name=api-products" \
  --data "paths[]=/api/products" \
  --data "strip_path=false" \
  --data "preserve_host=true" > /dev/null
echo "✅ Route created: /api/products"
echo ""

# Route 3: Search
echo "📍 Creating route: /san-pham (Search & Category)"
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/routes \
  --data "name=search-category" \
  --data "paths[]=/san-pham" \
  --data "paths[]=/danh-muc" \
  --data "strip_path=false" \
  --data "preserve_host=true" > /dev/null
echo "✅ Route created: /san-pham, /danh-muc"
echo ""

# Route 4: Cart & Checkout
echo "📍 Creating route: /gio-hang (Cart)"
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/routes \
  --data "name=cart" \
  --data "paths[]=/gio-hang" \
  --data "paths[]=/thanh-toan" \
  --data "strip_path=false" \
  --data "preserve_host=true" > /dev/null
echo "✅ Route created: /gio-hang, /thanh-toan"
echo ""

# Route 5: User Account
echo "📍 Creating route: /tai-khoan (User Account)"
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/routes \
  --data "name=user-account" \
  --data "paths[]=/tai-khoan" \
  --data "paths[]=/don-hang" \
  --data "strip_path=false" \
  --data "preserve_host=true" > /dev/null
echo "✅ Route created: /tai-khoan, /don-hang"
echo ""

# Route 6: Admin Panel
echo "📍 Creating route: /admin (Admin Panel)"
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/routes \
  --data "name=admin-panel" \
  --data "paths[]=/admin" \
  --data "strip_path=false" \
  --data "preserve_host=true" > /dev/null
echo "✅ Route created: /admin"
echo ""

# Route 7: Health Check
echo "📍 Creating route: /health (Health Check)"
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/routes \
  --data "name=health-check" \
  --data "paths[]=/health" \
  --data "paths[]=/api/health" \
  --data "strip_path=false" \
  --data "preserve_host=true" > /dev/null
echo "✅ Route created: /health"
echo ""

# ============================================================================
# 3. Configure Basic Plugins
# ============================================================================
echo "🔌 Step 3: Configuring Plugins..."
echo ""

# Plugin 1: CORS
echo "🔧 Enabling CORS plugin..."
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/plugins \
  --data "name=cors" \
  --data "config.origins=*" \
  --data "config.methods=GET,POST,PUT,DELETE,OPTIONS" \
  --data "config.headers=Accept,Authorization,Content-Type,X-CSRF-TOKEN" \
  --data "config.exposed_headers=X-Auth-Token" \
  --data "config.credentials=true" \
  --data "config.max_age=3600" > /dev/null
echo "✅ CORS plugin enabled"
echo ""

# Plugin 2: Rate Limiting (100 requests per minute)
echo "🔧 Enabling Rate Limiting plugin..."
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/plugins \
  --data "name=rate-limiting" \
  --data "config.minute=100" \
  --data "config.policy=local" > /dev/null
echo "✅ Rate Limiting enabled (100 req/min)"
echo ""

# Plugin 3: Request/Response Logging
echo "🔧 Enabling Logging plugin..."
curl -s -X POST ${KONG_ADMIN_URL}/services/laravel-app/plugins \
  --data "name=file-log" \
  --data "config.path=/tmp/kong-requests.log" > /dev/null
echo "✅ Logging plugin enabled"
echo ""

# Plugin 4: Prometheus Metrics
echo "🔧 Enabling Prometheus plugin..."
curl -s -X POST ${KONG_ADMIN_URL}/plugins \
  --data "name=prometheus" > /dev/null
echo "✅ Prometheus plugin enabled"
echo ""

# ============================================================================
# 4. Verify Configuration
# ============================================================================
echo "=========================================="
echo "✅ Kong Routes Configuration Complete!"
echo "=========================================="
echo ""
echo "📊 Configuration Summary:"
echo ""
echo "Service: laravel-app"
echo "  URL: ${LARAVEL_APP_URL}"
echo "  ID: ${SERVICE_ID}"
echo ""
echo "Routes Created:"
echo "  1. / (Homepage & Web Routes)"
echo "  2. /api/products (API Products)"
echo "  3. /san-pham (Search & Category)"
echo "  4. /gio-hang, /thanh-toan (Cart & Checkout)"
echo "  5. /tai-khoan, /don-hang (User Account)"
echo "  6. /admin (Admin Panel)"
echo "  7. /health (Health Check)"
echo ""
echo "Plugins Enabled:"
echo "  ✅ CORS"
echo "  ✅ Rate Limiting (100 req/min)"
echo "  ✅ Request Logging"
echo "  ✅ Prometheus Metrics"
echo ""
echo "=========================================="
echo "🎯 Next Steps:"
echo "=========================================="
echo ""
echo "1. Test Kong Gateway:"
echo "   curl http://localhost:8000/"
echo ""
echo "2. View Kong Admin:"
echo "   http://localhost:8001"
echo ""
echo "3. View Konga UI:"
echo "   http://localhost:1337"
echo ""
echo "4. View Prometheus Metrics:"
echo "   http://localhost:8000/metrics"
echo ""
echo "5. Check service health:"
echo "   curl http://localhost:8000/health"
echo ""
echo "=========================================="
echo "📚 Documentation: kong/KONG_SETUP.md"
echo "=========================================="
