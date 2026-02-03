# 🧪 KONG TEST MANUAL - Hướng dẫn test từng bước

## ❌ PHÁT HIỆN VẤN ĐỀ

Tôi đã check và phát hiện:
- ❌ Kong không chạy (port 8001 không phản hồi)
- ❌ Docker có thể chưa start

---

## ✅ GIẢI PHÁP - 3 OPTIONS

### Option A: Start Docker Desktop (Recommended)

**Bước 1: Mở Docker Desktop**
1. Click vào biểu tượng Docker Desktop
2. Đợi Docker start (30-60 giây)
3. Xem Docker Desktop UI → "Docker is running"

**Bước 2: Start Kong**
```cmd
cd d:\Web_Ban_Do_Dien_Tu

REM Start all services
docker-compose -f docker-compose.microservices.yml up -d

REM Wait 30 seconds
timeout /t 30

REM Check Kong status
docker ps | findstr kong
```

**Bước 3: Setup routes**
```cmd
kong\kong-routes-setup.bat
```

**Bước 4: Test**
```cmd
REM Test Kong Admin
curl http://localhost:8001

REM Test Kong Gateway
curl http://localhost:8000/health

REM Test API
curl http://localhost:8000/api/products/hot
```

---

### Option B: Test Laravel trực tiếp (Không cần Docker)

**Nếu bạn không muốn dùng Docker:**

**Bước 1: Start Laravel**
```cmd
cd d:\Web_Ban_Do_Dien_Tu

REM Start Laravel dev server
php artisan serve

REM Mở browser
http://localhost:8000
```

**Bước 2: Test endpoints**
```cmd
REM Test homepage
curl http://localhost:8000/

REM Test API
curl http://localhost:8000/api/products/hot

REM Test health
curl http://localhost:8000/health

REM Test search
curl "http://localhost:8000/san-pham?k=điều+hòa"
```

**Lưu ý:**
- ✅ Website vẫn chạy BÌNH THƯỜNG
- ✅ Tất cả features vẫn hoạt động
- ❌ Không có Kong Gateway (không sao!)
- ❌ Không có rate limiting (OK cho dev)

---

### Option C: Check services từng cái

**Check 1: Docker có chạy không?**
```cmd
docker version
```

**Kết quả mong đợi:**
```
Client: Docker Engine...
Server: Docker Engine...
```

**Nếu lỗi:**
- Mở Docker Desktop
- Đợi Docker start

**Check 2: Kong có trong Docker không?**
```cmd
docker ps -a | findstr kong
```

**Kết quả mong đợi:**
```
kong                 Up 5 minutes   0.0.0.0:8000->8000/tcp
kong-database        Up 5 minutes   5432/tcp
```

**Nếu không có:**
```cmd
REM Start Kong
docker-compose -f docker-compose.microservices.yml up -d
```

**Check 3: Laravel có chạy không?**

Option 3a: Check PHP dev server
```cmd
netstat -an | findstr :8000
```

Option 3b: Start Laravel nếu chưa chạy
```cmd
php artisan serve
```

---

## 🎯 TESTING SCENARIOS

### Scenario 1: Kong đang chạy (Ideal)

```cmd
REM 1. Test Kong Admin
curl http://localhost:8001
REM Expected: JSON với "version": "3.4..."

REM 2. Setup routes
kong\kong-routes-setup.bat
REM Expected: "✅ Configuration Complete!"

REM 3. Test Kong Gateway
curl http://localhost:8000/health
REM Expected: {"status":"healthy",...}

REM 4. Test through Kong
curl http://localhost:8000/
REM Expected: HTML homepage

REM 5. Test API through Kong
curl http://localhost:8000/api/products/hot
REM Expected: JSON với products
```

---

### Scenario 2: Kong không chạy (OK, dùng Laravel trực tiếp)

```cmd
REM 1. Start Laravel
php artisan serve
REM Expected: "Laravel development server started: http://127.0.0.1:8000"

REM 2. Test Laravel directly
curl http://localhost:8000/
REM Expected: HTML homepage

REM 3. Test API directly
curl http://localhost:8000/api/products/hot
REM Expected: JSON với products

REM 4. Test health
curl http://localhost:8000/health
REM Expected: {"status":"healthy",...}
```

---

### Scenario 3: Cả hai đều chạy (Best!)

```cmd
REM Test Laravel direct
curl http://localhost:80/
REM OR
curl http://localhost:8080/

REM Test through Kong
curl http://localhost:8000/
```

**So sánh:**
- Laravel direct: ~50-200ms
- Through Kong: ~55-220ms (thêm 5-20ms overhead)

---

## 🧪 DETAILED TEST COMMANDS

### Test 1: Homepage

**Direct Laravel:**
```cmd
curl http://localhost:8000/ -o homepage.html
notepad homepage.html
```

**Through Kong:**
```cmd
curl http://localhost:8000/ -o homepage-kong.html
notepad homepage-kong.html
```

**Compare:** Should be identical HTML

---

### Test 2: API Hot Products

**Request:**
```cmd
curl http://localhost:8000/api/products/hot
```

**Expected response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "pro_name": "Điều hòa Daikin...",
      "pro_price": 8999000,
      "pro_sale": 7999000,
      "pro_image": "...",
      "category": {
        "id": 1,
        "c_name": "Điều hòa",
        "c_slug": "dieu-hoa"
      }
    },
    ...
  ],
  "per_page": 20,
  "total": 50
}
```

**Check caching:**
```cmd
REM First request (MISS)
curl -i http://localhost:8000/api/products/hot | findstr "X-Cache-Status"
REM Output: X-Cache-Status: MISS

REM Second request (HIT)
curl -i http://localhost:8000/api/products/hot | findstr "X-Cache-Status"
REM Output: X-Cache-Status: HIT
```

---

### Test 3: Search

**Request:**
```cmd
curl "http://localhost:8000/san-pham?k=điều+hòa&page=1"
```

**Expected:** HTML page với search results

---

### Test 4: Health Check

**Request:**
```cmd
curl http://localhost:8000/health
```

**Expected (Healthy):**
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

**Expected (Unhealthy):**
```json
{
  "status": "unhealthy",
  "checks": {
    "database": "error: ..."
  }
}
```

---

### Test 5: Rate Limiting (If Kong running)

**Bash script:**
```cmd
@echo off
for /L %%i in (1,1,105) do (
  echo Request %%i
  curl -s http://localhost:8000/api/products/hot -o nul
)
```

**Expected:**
- Requests 1-100: OK (200)
- Requests 101+: Rate limit exceeded (429)

---

### Test 6: Metrics

**Request:**
```cmd
curl http://localhost:8000/metrics
```

**Expected:**
```
laravel_app_up 1
laravel_products_total 100
laravel_cache_hits 1
```

---

## 📊 EXPECTED PORTS

| Service | Port | Status | Test Command |
|---------|------|--------|--------------|
| **Laravel** | 8000 | ✅ | `curl http://localhost:8000/` |
| **Kong Proxy** | 8000 | ❓ | `curl http://localhost:8000/health` |
| **Kong Admin** | 8001 | ❓ | `curl http://localhost:8001` |
| **Konga UI** | 1337 | ❓ | Open browser |
| **MySQL** | 3306 | ✅ | `mysql -u root -p` |
| **Redis** | 6379 | ✅ | `redis-cli ping` |

---

## 🆘 TROUBLESHOOTING

### Issue 1: "Port 8000 already in use"

**Cause:** Laravel dev server và Kong đều dùng port 8000

**Fix Option 1:** Stop Laravel, dùng Kong
```cmd
REM Stop Laravel (Ctrl+C in terminal)
REM Start Kong
docker-compose -f docker-compose.microservices.yml up -d
```

**Fix Option 2:** Change Laravel port
```cmd
REM Start Laravel on different port
php artisan serve --port=8080

REM Test
curl http://localhost:8080/
```

---

### Issue 2: "Docker daemon not running"

**Cause:** Docker Desktop chưa start

**Fix:**
1. Click Start → Docker Desktop
2. Đợi Docker icon turn green (30-60s)
3. Check: `docker version`
4. Retry: `docker-compose up -d`

---

### Issue 3: "kong-routes-setup.bat not working"

**Cause:** Kong chưa ready

**Fix:**
```cmd
REM Wait for Kong
timeout /t 30

REM Test Kong Admin
curl http://localhost:8001

REM If OK, run setup
kong\kong-routes-setup.bat
```

---

### Issue 4: "Connection refused"

**Possible causes:**
1. Service not running
2. Wrong port
3. Firewall blocking

**Debug:**
```cmd
REM Check what's running on port 8000
netstat -an | findstr :8000

REM Check what's running on port 8001
netstat -an | findstr :8001

REM Check Docker containers
docker ps
```

---

### Issue 5: "Empty response"

**Cause:** Service starting/crashed

**Fix:**
```cmd
REM Check logs
docker logs kong
docker logs kong-database

REM Restart service
docker-compose -f docker-compose.microservices.yml restart kong
```

---

## 💡 QUICK DECISION TREE

```
Do you want to use Kong Gateway?
├─ YES → Go to Option A (Start Docker Desktop)
│        └─ docker-compose up -d
│        └─ kong\kong-routes-setup.bat
│        └─ Test: curl http://localhost:8000/health
│
└─ NO → Go to Option B (Laravel only)
         └─ php artisan serve
         └─ Test: curl http://localhost:8000/
```

---

## ✅ SUCCESS CHECKLIST

**For Kong setup:**
- [ ] Docker Desktop running
- [ ] Kong container running (`docker ps | grep kong`)
- [ ] Kong Admin accessible (`curl http://localhost:8001`)
- [ ] Routes setup completed (`kong\kong-routes-setup.bat`)
- [ ] Health check OK (`curl http://localhost:8000/health`)
- [ ] API works (`curl http://localhost:8000/api/products/hot`)

**For Laravel only:**
- [ ] Laravel dev server running (`php artisan serve`)
- [ ] Homepage loads (`curl http://localhost:8000/`)
- [ ] API works (`curl http://localhost:8000/api/products/hot`)
- [ ] Search works (`curl http://localhost:8000/san-pham?k=test`)

---

## 🎯 RECOMMENDED WORKFLOW

### For Development (Quick):

```cmd
REM Option 1: Just Laravel (Simple)
php artisan serve

REM Test
curl http://localhost:8000/
curl http://localhost:8000/api/products/hot
```

### For Demo/Production (Complete):

```cmd
REM Option 2: Full stack with Kong (Professional)
REM 1. Start Docker Desktop
REM 2. Start services
docker-compose -f docker-compose.microservices.yml up -d

REM 3. Wait
timeout /t 30

REM 4. Setup Kong
kong\kong-routes-setup.bat

REM 5. Test
curl http://localhost:8000/health
curl http://localhost:8000/api/products/hot
```

---

## 📚 REFERENCE

**Created files:**
- `kong/kong-routes-setup.bat` - Setup script
- `kong/KONG_SETUP.md` - Full guide
- `kong/README.md` - Quick start
- `routes/api.php` - API endpoints
- `routes/web.php` - Web routes

**Ports:**
- `8000` - Kong Proxy / Laravel
- `8001` - Kong Admin
- `1337` - Konga UI
- `3306` - MySQL
- `6379` - Redis

---

**Next:** Chọn option A (Kong) hoặc B (Laravel only) và test! 🚀
