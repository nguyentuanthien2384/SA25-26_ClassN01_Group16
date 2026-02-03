#!/bin/bash

# ============================================================================
# Kong API Gateway - Complete Setup Script
# ============================================================================
# This script configures Kong with services, routes, and plugins
# Run: chmod +x kong-routes-setup-complete.sh && ./kong-routes-setup-complete.sh
# ============================================================================

set -e  # Exit on error

KONG_ADMIN_URL="http://localhost:8001"
LARAVEL_HOST="host.docker.internal:8000"  # For Docker
# LARAVEL_HOST="localhost:8000"  # For local

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║           Kong API Gateway - Complete Setup                 ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ============================================================================
# STEP 1: Check Kong Admin API
# ============================================================================
echo "🔍 Checking Kong Admin API..."
if curl -s "${KONG_ADMIN_URL}" > /dev/null; then
    echo "✅ Kong Admin API is accessible"
else
    echo "❌ Kong Admin API is not accessible at ${KONG_ADMIN_URL}"
    echo "   Please start Kong first: docker-compose up kong"
    exit 1
fi

# ============================================================================
# STEP 2: Create Laravel Service
# ============================================================================
echo ""
echo "📦 Creating Laravel Service..."

SERVICE_RESPONSE=$(curl -s -X POST "${KONG_ADMIN_URL}/services" \
  --data "name=laravel-app" \
  --data "url=http://${LARAVEL_HOST}")

if echo "$SERVICE_RESPONSE" | grep -q '"id"'; then
    SERVICE_ID=$(echo "$SERVICE_RESPONSE" | grep -o '"id":"[^"]*' | cut -d'"' -f4)
    echo "✅ Service created: laravel-app (ID: ${SERVICE_ID:0:8}...)"
else
    echo "⚠️  Service already exists or error occurred"
fi

# ============================================================================
# STEP 3: Create Routes
# ============================================================================
echo ""
echo "🛣️  Creating Routes..."

# Main API route
echo "  Creating /api route..."
curl -s -X POST "${KONG_ADMIN_URL}/services/laravel-app/routes" \
  --data "name=api-route" \
  --data "paths[]=/api" \
  --data "strip_path=false" > /dev/null
echo "  ✅ /api → laravel-app"

# Web routes
echo "  Creating / route..."
curl -s -X POST "${KONG_ADMIN_URL}/services/laravel-app/routes" \
  --data "name=web-route" \
  --data "paths[]=/"\
  --data "strip_path=false" > /dev/null
echo "  ✅ / → laravel-app"

# ============================================================================
# STEP 4: Add Rate Limiting Plugin
# ============================================================================
echo ""
echo "⏱️  Configuring Rate Limiting..."

curl -s -X POST "${KONG_ADMIN_URL}/services/laravel-app/plugins" \
  --data "name=rate-limiting" \
  --data "config.minute=100" \
  --data "config.hour=10000" \
  --data "config.policy=local" > /dev/null

echo "✅ Rate Limiting: 100 req/min, 10000 req/hour"

# ============================================================================
# STEP 5: Add Request/Response Logging
# ============================================================================
echo ""
echo "📝 Configuring Logging..."

curl -s -X POST "${KONG_ADMIN_URL}/services/laravel-app/plugins" \
  --data "name=file-log" \
  --data "config.path=/tmp/kong-laravel.log" > /dev/null

echo "✅ File logging enabled: /tmp/kong-laravel.log"

# ============================================================================
# STEP 6: Add CORS Plugin
# ============================================================================
echo ""
echo "🌐 Configuring CORS..."

curl -s -X POST "${KONG_ADMIN_URL}/services/laravel-app/plugins" \
  --data "name=cors" \
  --data "config.origins=*" \
  --data "config.methods=GET,POST,PUT,PATCH,DELETE,OPTIONS" \
  --data "config.headers=Accept,Authorization,Content-Type,X-Request-ID" \
  --data "config.exposed_headers=X-Request-ID" \
  --data "config.credentials=true" \
  --data "config.max_age=3600" > /dev/null

echo "✅ CORS enabled for all origins"

# ============================================================================
# STEP 7: Add Request Transformer
# ============================================================================
echo ""
echo "🔄 Configuring Request Transformer..."

curl -s -X POST "${KONG_ADMIN_URL}/services/laravel-app/plugins" \
  --data "name=request-transformer" \
  --data "config.add.headers=X-Kong-Gateway:true" > /dev/null

echo "✅ Request transformer: Added X-Kong-Gateway header"

# ============================================================================
# STEP 8: Add Response Transformer
# ============================================================================
echo ""
echo "📤 Configuring Response Transformer..."

curl -s -X POST "${KONG_ADMIN_URL}/services/laravel-app/plugins" \
  --data "name=response-transformer" \
  --data "config.add.headers=X-Kong-Proxy:kong-gateway" > /dev/null

echo "✅ Response transformer: Added X-Kong-Proxy header"

# ============================================================================
# SUMMARY
# ============================================================================
echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                     Setup Complete! 🎉                       ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "📊 Configuration Summary:"
echo "  • Service: laravel-app → ${LARAVEL_HOST}"
echo "  • Routes: /api, /"
echo "  • Rate Limiting: 100/min, 10000/hour"
echo "  • CORS: Enabled"
echo "  • Logging: Enabled"
echo "  • Headers: X-Kong-Gateway, X-Kong-Proxy"
echo ""
echo "🧪 Test Commands:"
echo "  curl http://localhost:8000/api/health"
echo "  curl -I http://localhost:8000/"
echo ""
echo "📊 View Configuration:"
echo "  Kong Admin: http://localhost:8001"
echo "  Konga UI: http://localhost:1337"
echo ""
echo "✅ Kong Gateway is ready!"
echo ""
