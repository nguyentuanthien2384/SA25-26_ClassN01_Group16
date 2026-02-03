# 🚀 KONG API GATEWAY - SETUP GUIDE

## 📋 OVERVIEW

Kong API Gateway đã được setup để proxy requests tới Laravel application với:
- ✅ **7 Routes** configured
- ✅ **3 Plugins** enabled (CORS, Rate Limiting, Prometheus)
- ✅ **Health Checks** implemented
- ✅ **Monitoring** ready

---

## 🏗️ ARCHITECTURE

### Before Kong (Direct Access):

```
[Browser/Client]
    ↓
    http://localhost:8000/san-pham
    ↓
[Laravel App :80]
    ↓
[MySQL Database]
```

### After Kong (API Gateway):

```
[Browser/Client]
    ↓
    http://localhost:8000/san-pham
    ↓
[Kong API Gateway :8000]
    ├─ CORS Check ✅
    ├─ Rate Limiting (100 req/min) ✅
    ├─ Logging & Metrics ✅
    └─ Route to → [Laravel App :80]
                      ↓
                  [MySQL Database]
```

---

## 🎯 QUICK START

### Step 1: Start Infrastructure

```bash
# Start Kong and all services
docker-compose -f docker-compose.microservices.yml up -d

# Check Kong is running
curl http://localhost:8001
```

### Step 2: Setup Routes (Windows)

```cmd
# Run setup script
kong\kong-routes-setup.bat
```

### Step 2: Setup Routes (Linux/Mac)

```bash
# Run setup script
bash kong/kong-routes-setup.sh
```

### Step 3: Verify Setup

```bash
# Test homepage through Kong
curl http://localhost:8000/

# Test API endpoint
curl http://localhost:8000/api/products/hot

# Test health check
curl http://localhost:8000/health
```

---

## 📊 ROUTES CONFIGURED

| Route | Path | Purpose | Strip Path | Preserve Host |
|-------|------|---------|-----------|---------------|
| **homepage** | `/` | Homepage & Web | No | Yes |
| **api-products** | `/api/products` | Product API | No | Yes |
| **search-category** | `/san-pham`, `/danh-muc` | Search & Categories | No | Yes |
| **cart** | `/gio-hang`, `/thanh-toan` | Cart & Checkout | No | Yes |
| **user-account** | `/tai-khoan`, `/don-hang` | User Account | No | Yes |
| **admin-panel** | `/admin` | Admin Panel | No | Yes |
| **health-check** | `/health`, `/api/health` | Health Check | No | Yes |

---

## 🔌 PLUGINS ENABLED

### 1. CORS Plugin

**Purpose:** Allow cross-origin requests

**Configuration:**
- Origins: `*` (All origins)
- Methods: `GET, POST, PUT, DELETE, OPTIONS`
- Headers: `Accept, Authorization, Content-Type, X-CSRF-TOKEN`
- Credentials: `true`
- Max Age: `3600s`

**Test:**
```bash
curl -X OPTIONS http://localhost:8000/api/products/hot \
  -H "Origin: http://example.com" \
  -H "Access-Control-Request-Method: GET"
```

---

### 2. Rate Limiting Plugin

**Purpose:** Limit requests to prevent abuse

**Configuration:**
- Limit: `100 requests per minute`
- Policy: `local` (in-memory)

**Test:**
```bash
# Make multiple requests
for i in {1..10}; do
  curl -i http://localhost:8000/api/products/hot
done

# Check headers:
# X-RateLimit-Limit-Minute: 100
# X-RateLimit-Remaining-Minute: 90
```

**Response when rate limit exceeded:**
```json
{
  "message": "API rate limit exceeded"
}
```

---

### 3. Prometheus Metrics Plugin

**Purpose:** Expose metrics for monitoring

**Metrics Endpoint:**
```bash
curl http://localhost:8000/metrics
```

**Sample Metrics:**
```
kong_http_status{service="laravel-app",code="200"} 150
kong_request_count{service="laravel-app"} 200
kong_request_latency_ms{service="laravel-app"} 45.2
```

---

## 🏥 HEALTH CHECKS

### Laravel Health Endpoint

**URL:** `http://localhost:8000/health`

**Response (Healthy):**
```json
{
  "status": "healthy",
  "timestamp": "2026-01-28T10:30:00Z",
  "service": "laravel-app",
  "version": "1.0.0",
  "checks": {
    "database": "ok",
    "cache": "ok"
  },
  "uptime": "123.45s"
}
```

**Response (Unhealthy):**
```json
{
  "status": "unhealthy",
  "timestamp": "2026-01-28T10:30:00Z",
  "service": "laravel-app",
  "version": "1.0.0",
  "checks": {
    "database": "error: Connection refused",
    "cache": "ok"
  },
  "uptime": "123.45s"
}
```

**HTTP Status Codes:**
- `200 OK` - Service is healthy
- `503 Service Unavailable` - Service is unhealthy

---

## 🧪 TESTING GUIDE

### Test 1: Basic Connectivity

```bash
# Test Kong Admin API
curl http://localhost:8001

# Test Kong Proxy
curl http://localhost:8000/health

# Expected: {"status":"healthy",...}
```

---

### Test 2: Homepage Access

```bash
# Direct access (bypass Kong)
curl http://localhost/

# Through Kong Gateway
curl http://localhost:8000/

# Both should return same HTML
```

---

### Test 3: API Endpoints

```bash
# Hot products
curl http://localhost:8000/api/products/hot

# New products
curl http://localhost:8000/api/products/new

# Best selling
curl http://localhost:8000/api/products/selling

# All products with filters
curl "http://localhost:8000/api/products?category=1&sort=price_asc"

# Single product
curl http://localhost:8000/api/products/1
```

---

### Test 4: Rate Limiting

```bash
# Bash script to test rate limit
for i in {1..105}; do
  echo "Request $i"
  curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8000/api/products/hot
done

# Expected:
# Requests 1-100: 200
# Requests 101+: 429 (Rate limit exceeded)
```

---

### Test 5: CORS

```bash
# Test CORS preflight
curl -X OPTIONS http://localhost:8000/api/products/hot \
  -H "Origin: http://example.com" \
  -H "Access-Control-Request-Method: GET" \
  -v

# Expected headers:
# Access-Control-Allow-Origin: *
# Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS
```

---

### Test 6: Performance

```bash
# Test with cache
time curl -s http://localhost:8000/api/products/hot > /dev/null

# First request: ~500-800ms (cache MISS)
# Second request: ~5-20ms (cache HIT)

# Check cache status in headers
curl -i http://localhost:8000/api/products/hot | grep X-Cache-Status
# X-Cache-Status: HIT
```

---

## 📈 MONITORING

### Kong Admin UI (Konga)

**URL:** http://localhost:1337

**Setup:**
1. Visit http://localhost:1337
2. Create admin account
3. Add connection:
   - Name: `Kong Admin`
   - Kong Admin URL: `http://kong:8001`

**Features:**
- ✅ View all routes & services
- ✅ Manage plugins
- ✅ Monitor traffic
- ✅ View logs

---

### Prometheus Metrics

**URL:** http://localhost:8000/metrics

**Sample Metrics:**
```
# Kong metrics
kong_http_status{service="laravel-app",code="200"} 150
kong_request_count{service="laravel-app"} 200
kong_request_latency_ms{service="laravel-app"} 45.2
kong_bandwidth_bytes{type="egress",service="laravel-app"} 1048576

# Laravel custom metrics
laravel_app_up 1
laravel_products_total 100
laravel_cache_hits 1
```

**Grafana Dashboard:**
- URL: http://localhost:3000
- Username: `admin`
- Password: `admin`

---

### View Kong Logs

```bash
# View Kong access logs
docker logs kong -f

# View Kong error logs
docker logs kong --tail 100

# View request logs
docker exec kong cat /tmp/kong-requests.log
```

---

## 🔧 ADVANCED CONFIGURATION

### Add New Route

```bash
# Example: Add /api/cart route
curl -X POST http://localhost:8001/services/laravel-app/routes \
  --data "name=api-cart" \
  --data "paths[]=/api/cart" \
  --data "strip_path=false" \
  --data "preserve_host=true"
```

---

### Add JWT Authentication

```bash
# Enable JWT plugin
curl -X POST http://localhost:8001/services/laravel-app/plugins \
  --data "name=jwt" \
  --data "config.secret_is_base64=false"

# Create consumer
curl -X POST http://localhost:8001/consumers \
  --data "username=john_doe"

# Create JWT credential
curl -X POST http://localhost:8001/consumers/john_doe/jwt \
  --data "key=mykey" \
  --data "secret=mysecret"
```

---

### Update Rate Limit

```bash
# Get plugin ID
curl http://localhost:8001/plugins | jq '.data[] | select(.name=="rate-limiting")'

# Update rate limit to 200 req/min
curl -X PATCH http://localhost:8001/plugins/{PLUGIN_ID} \
  --data "config.minute=200"
```

---

### Add IP Whitelist

```bash
# Only allow specific IPs
curl -X POST http://localhost:8001/services/laravel-app/plugins \
  --data "name=ip-restriction" \
  --data "config.whitelist=192.168.1.100" \
  --data "config.whitelist=10.0.0.0/8"
```

---

## 🆘 TROUBLESHOOTING

### Issue 1: "Connection refused" when accessing Kong

**Cause:** Kong not running

**Fix:**
```bash
# Check Kong status
docker ps | grep kong

# Start Kong
docker-compose -f docker-compose.microservices.yml up -d kong

# Check logs
docker logs kong
```

---

### Issue 2: "404 Not Found" for routes

**Cause:** Routes not configured

**Fix:**
```bash
# Re-run setup script
kong\kong-routes-setup.bat

# Or manually check routes
curl http://localhost:8001/routes
```

---

### Issue 3: Rate limit not working

**Cause:** Plugin not enabled

**Fix:**
```bash
# Check plugins
curl http://localhost:8001/plugins

# Re-enable rate limiting
curl -X POST http://localhost:8001/services/laravel-app/plugins \
  --data "name=rate-limiting" \
  --data "config.minute=100"
```

---

### Issue 4: CORS errors in browser

**Cause:** CORS plugin not configured

**Fix:**
```bash
# Check CORS plugin
curl http://localhost:8001/plugins | grep cors

# Re-enable CORS
curl -X POST http://localhost:8001/services/laravel-app/plugins \
  --data "name=cors" \
  --data "config.origins=*"
```

---

### Issue 5: Slow response times

**Cause:** Kong adding latency

**Check latency:**
```bash
# Measure time through Kong
time curl -s http://localhost:8000/api/products/hot > /dev/null

# Measure time direct to Laravel
time curl -s http://localhost/api/products/hot > /dev/null

# Difference = Kong overhead (usually 5-20ms)
```

**Fix:**
- Kong overhead is normal (~5-20ms)
- Enable caching in Laravel (already done)
- Use HTTP/2 for better performance

---

## 📚 USEFUL COMMANDS

### Service Management

```bash
# List all services
curl http://localhost:8001/services

# Get service details
curl http://localhost:8001/services/laravel-app

# Update service
curl -X PATCH http://localhost:8001/services/laravel-app \
  --data "retries=10"

# Delete service (careful!)
curl -X DELETE http://localhost:8001/services/laravel-app
```

---

### Route Management

```bash
# List all routes
curl http://localhost:8001/routes

# Get route details
curl http://localhost:8001/routes/{route_id}

# Delete route
curl -X DELETE http://localhost:8001/routes/{route_id}
```

---

### Plugin Management

```bash
# List all plugins
curl http://localhost:8001/plugins

# Disable plugin
curl -X PATCH http://localhost:8001/plugins/{plugin_id} \
  --data "enabled=false"

# Delete plugin
curl -X DELETE http://localhost:8001/plugins/{plugin_id}
```

---

### Health & Status

```bash
# Kong health
curl http://localhost:8001/status

# Kong configuration
curl http://localhost:8001/

# Laravel health
curl http://localhost:8000/health
```

---

## 🎯 BEST PRACTICES

### 1. Always Use Health Checks

```bash
# Add health checks to all services
# Already implemented at /health
```

### 2. Enable Rate Limiting

```bash
# Protect your API from abuse
# Already enabled: 100 req/min
```

### 3. Use CORS Properly

```bash
# In production, use specific origins
curl -X PATCH http://localhost:8001/plugins/{cors_plugin_id} \
  --data "config.origins=https://yourdomain.com"
```

### 4. Monitor Everything

```bash
# Use Prometheus + Grafana
# Already setup and ready
```

### 5. Cache Aggressively

```bash
# Laravel caching already implemented
# Cache duration: 5 minutes
```

---

## 📖 DOCUMENTATION LINKS

- **Kong Official Docs:** https://docs.konghq.com/
- **Kong Admin API:** https://docs.konghq.com/gateway/latest/admin-api/
- **Kong Plugins:** https://docs.konghq.com/hub/
- **Konga UI:** https://github.com/pantsel/konga

---

## ✅ SUMMARY

**Infrastructure:**
- ✅ Kong Gateway running on `:8000`
- ✅ Kong Admin API on `:8001`
- ✅ Konga UI on `:1337`

**Routes:**
- ✅ 7 routes configured
- ✅ All Laravel endpoints proxied

**Plugins:**
- ✅ CORS enabled
- ✅ Rate Limiting (100 req/min)
- ✅ Prometheus metrics

**Monitoring:**
- ✅ Health checks at `/health`
- ✅ Metrics at `/metrics`
- ✅ Grafana dashboards ready

**Status:** ✅ **PRODUCTION READY**

---

**Setup Date:** 2026-01-28  
**Version:** 1.0.0  
**Status:** ✅ COMPLETE
