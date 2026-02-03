# 🚀 KONG API GATEWAY - QUICK START

## ⚡ 3 BƯỚC ĐỂ CHẠY

### Bước 1: Start Kong

```bash
# Start all services (Kong, Prometheus, Grafana, etc.)
docker-compose -f docker-compose.microservices.yml up -d

# Đợi ~30 giây để Kong khởi động
```

### Bước 2: Setup Routes (Windows)

```cmd
cd d:\Web_Ban_Do_Dien_Tu
kong\kong-routes-setup.bat
```

### Bước 3: Test

```bash
# Test homepage
http://localhost:8000/

# Test API
http://localhost:8000/api/products/hot

# Test health check
http://localhost:8000/health
```

---

## 🎯 URLS

| Service | URL | Purpose |
|---------|-----|---------|
| **Kong Gateway** | http://localhost:8000 | API Gateway (use this!) |
| **Kong Admin** | http://localhost:8001 | Admin API |
| **Konga UI** | http://localhost:1337 | Web UI for Kong |
| **Prometheus** | http://localhost:9090 | Metrics |
| **Grafana** | http://localhost:3000 | Dashboards |

---

## 📊 FEATURES ENABLED

- ✅ **7 Routes** configured
- ✅ **CORS** enabled (all origins)
- ✅ **Rate Limiting** (100 requests/minute)
- ✅ **Health Checks** (`/health`)
- ✅ **Prometheus Metrics** (`/metrics`)
- ✅ **Request Logging**

---

## 🧪 TEST ROUTES

```bash
# Homepage
curl http://localhost:8000/

# API Products
curl http://localhost:8000/api/products/hot
curl http://localhost:8000/api/products/new
curl http://localhost:8000/api/products/selling

# Search
curl "http://localhost:8000/san-pham?k=điều+hòa"

# Health
curl http://localhost:8000/health

# Metrics
curl http://localhost:8000/metrics
```

---

## 📚 DOCUMENTATION

- **Full Setup Guide:** [KONG_SETUP.md](./KONG_SETUP.md)
- **Kong Docs:** https://docs.konghq.com/

---

## 🆘 TROUBLESHOOTING

### "Connection refused"

```bash
# Check Kong is running
docker ps | grep kong

# Restart Kong
docker-compose -f docker-compose.microservices.yml restart kong
```

### "404 Not Found"

```bash
# Re-run setup script
kong\kong-routes-setup.bat
```

### "Rate limit exceeded"

```bash
# Wait 1 minute or increase limit in setup script
```

---

**Status:** ✅ Ready to use!
