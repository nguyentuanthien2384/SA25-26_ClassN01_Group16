# Hướng Dẫn Cải Thiện Theo Lý Thuyết Microservices

Dựa trên đánh giá trong `ARCHITECTURE_REVIEW.md`, đây là các bước cải thiện cụ thể.

---

## 🔴 PRIORITY 1: CRITICAL (Làm ngay - tuần này)

### ✅ 1. Health Check Endpoints (ĐÃ HOÀN THÀNH)

**File đã tạo:** `routes/api.php`

**Endpoints:**
- `GET /api/health` - Kiểm tra trạng thái service
- `GET /api/ready` - Readiness probe (K8s)
- `GET /api/metrics` - Metrics cho monitoring

**Test:**
```bash
curl http://localhost:8000/api/health
curl http://localhost:8000/api/ready
curl http://localhost:8000/api/metrics
```

**Expected response:**
```json
{
  "status": "healthy",
  "timestamp": "2026-01-28T07:00:00Z",
  "services": {
    "database": "up",
    "redis": "up",
    "queue": {
      "status": "ok",
      "size": 5
    }
  }
}
```

---

### ✅ 2. Circuit Breaker Implementation (ĐÃ HOÀN THÀNH)

**File đã tạo:**
- `app/Http/Middleware/CircuitBreaker.php`
- `app/Services/ExternalApiService.php`

**Cách dùng trong PaymentController:**

```php
use App\Services\ExternalApiService;

class PaymentController extends Controller
{
    private ExternalApiService $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    private function initMomo(Transaction $transaction)
    {
        $config = config('services.momo');
        // ... prepare payload ...

        try {
            // Thay vì: Http::post($config['endpoint'], $payload)
            $response = $this->apiService->callWithRetry(
                'momo',
                $config['endpoint'],
                ['data' => $payload]
            );

            if (!$response->ok()) {
                throw new \Exception('MoMo API error: ' . $response->body());
            }

            $data = $response->json();
            // ... process response ...

        } catch (\Exception $e) {
            Log::error('MoMo payment failed', ['error' => $e->getMessage()]);
            
            // Fallback: Chuyển sang QR Code payment
            return redirect()->route('payment.show', [
                'method' => 'qrcode',
                'transaction' => $transaction->id,
            ])->with('warning', 'MoMo tạm thời không khả dụng, vui lòng dùng QR Code.');
        }
    }
}
```

**Benefits:**
- ✅ Auto retry với exponential backoff (2s, 4s, 8s)
- ✅ Circuit breaker tự động mở khi API fail nhiều
- ✅ Fallback sang phương thức thanh toán khác
- ✅ Logging đầy đủ

**Test circuit breaker:**
```bash
# Xem trạng thái
php artisan tinker
>>> app(\App\Services\ExternalApiService::class)->getStatus('momo');

# Reset circuit nếu cần
>>> app(\App\Services\ExternalApiService::class)->reset('momo');
```

---

### 🟡 3. Notification Service Health Check (CẦN LÀM)

**Thêm vào `notification-service/consumer.php`:**

```php
// Trước khi start consuming, expose HTTP health endpoint
$healthServer = new \React\Http\HttpServer(function ($request) use ($redis) {
    if ($request->getUri()->getPath() === '/health') {
        try {
            $redis->ping();
            return new \React\Http\Message\Response(200, 
                ['Content-Type' => 'application/json'],
                json_encode(['status' => 'healthy', 'service' => 'notification'])
            );
        } catch (\Exception $e) {
            return new \React\Http\Message\Response(503,
                ['Content-Type' => 'application/json'],
                json_encode(['status' => 'unhealthy', 'error' => $e->getMessage()])
            );
        }
    }
    return new \React\Http\Message\Response(404);
});

$socket = new \React\Socket\SocketServer('0.0.0.0:9001');
$healthServer->listen($socket);

echo "Health check available at http://localhost:9001/health\n";
```

---

## 🟡 PRIORITY 2: HIGH (Làm trong 1-2 tuần)

### 🟡 4. Private Tables Per Service

**Theo PDF DB Patterns (Slide 4):** "Private-tables-per-service"

**Migration Plan:**

```php
// database/migrations/2026_01_29_000000_add_service_ownership_comments.php

public function up()
{
    // Đánh dấu ownership cho từng bảng
    DB::statement("ALTER TABLE products COMMENT 'OWNED_BY: Catalog Service'");
    DB::statement("ALTER TABLE categories COMMENT 'OWNED_BY: Catalog Service'");
    DB::statement("ALTER TABLE users COMMENT 'OWNED_BY: Customer Service'");
    DB::statement("ALTER TABLE wishlists COMMENT 'OWNED_BY: Customer Service'");
    DB::statement("ALTER TABLE carts COMMENT 'OWNED_BY: Cart Service'");
    DB::statement("ALTER TABLE transactions COMMENT 'OWNED_BY: Order Service'");
    DB::statement("ALTER TABLE oders COMMENT 'OWNED_BY: Order Service'");
}
```

**Access Control Rules:**

```php
// Modules/Catalog/Database/Connection.php
class CatalogConnection
{
    // Chỉ được access:
    private const ALLOWED_TABLES = [
        'products',
        'categories',
        'pro_images',
    ];
    
    public function query($table)
    {
        if (!in_array($table, self::ALLOWED_TABLES)) {
            throw new \Exception("Catalog Service không được access table: {$table}");
        }
        return DB::table($table);
    }
}
```

**Benefits:**
- ✅ Chuẩn bị cho database separation sau này
- ✅ Clear ownership boundaries
- ✅ Prevent cross-service data access

---

### 🟡 5. Centralized Logging với ELK Stack

**Docker Compose Setup:**

```yaml
# docker-compose.logging.yml
version: '3.8'

services:
  elasticsearch:
    image: elasticsearch:8.11.0
    environment:
      - discovery.type=single-node
      - xpack.security.enabled=false
    ports:
      - "9200:9200"

  logstash:
    image: logstash:8.11.0
    volumes:
      - ./logstash/pipeline:/usr/share/logstash/pipeline
    ports:
      - "5044:5044"
    depends_on:
      - elasticsearch

  kibana:
    image: kibana:8.11.0
    ports:
      - "5601:5601"
    depends_on:
      - elasticsearch
```

**Logstash Config:**

```ruby
# logstash/pipeline/laravel.conf
input {
  file {
    path => "/var/log/laravel/*.log"
    type => "laravel"
  }
  tcp {
    port => 5044
    codec => json
  }
}

filter {
  if [type] == "laravel" {
    grok {
      match => { "message" => "\[%{TIMESTAMP_ISO8601:timestamp}\] %{WORD:env}\.%{WORD:level}: %{GREEDYDATA:message}" }
    }
  }
}

output {
  elasticsearch {
    hosts => ["elasticsearch:9200"]
    index => "laravel-%{+YYYY.MM.dd}"
  }
}
```

**Laravel Integration:**

```bash
composer require cviebrock/laravel-elasticsearch
```

```php
// config/logging.php
'channels' => [
    'elk' => [
        'driver' => 'monolog',
        'handler' => Monolog\Handler\SocketHandler::class,
        'handler_with' => [
            'connectionString' => 'tcp://logstash:5044',
        ],
    ],
];
```

---

### 🟡 6. Distributed Tracing với Jaeger

**Install:**

```bash
composer require jcchavezs/zipkin-opentracing
```

**Middleware:**

```php
// app/Http/Middleware/DistributedTracing.php
class DistributedTracing
{
    public function handle($request, Closure $next)
    {
        $tracer = app('tracer');
        
        $span = $tracer->startSpan('http_request', [
            'tags' => [
                'http.method' => $request->method(),
                'http.url' => $request->fullUrl(),
                'component' => 'laravel',
            ],
        ]);

        $request->attributes->set('trace_id', $span->getContext()->getTraceId());

        $response = $next($request);

        $span->setTag('http.status_code', $response->status());
        $span->finish();

        return $response;
    }
}
```

**Jaeger Docker:**

```bash
docker run -d --name jaeger \
  -p 6831:6831/udp \
  -p 16686:16686 \
  jaegertracing/all-in-one:latest

# UI: http://localhost:16686
```

---

## 🟢 PRIORITY 3: MEDIUM (Làm khi scale - 1-2 tháng)

### 🟢 7. Database Per Service - Full Separation

**Tạo databases riêng:**

```sql
-- Catalog Database
CREATE DATABASE catalog_db;
CREATE USER 'catalog_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON catalog_db.* TO 'catalog_user'@'localhost';

-- Customer Database
CREATE DATABASE customer_db;
CREATE USER 'customer_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON customer_db.* TO 'customer_user'@'localhost';

-- Order Database
CREATE DATABASE order_db;
CREATE USER 'order_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON order_db.* TO 'order_user'@'localhost';
```

**Config connections:**

```php
// config/database.php
'connections' => [
    'catalog' => [
        'driver' => 'mysql',
        'host' => env('CATALOG_DB_HOST', '127.0.0.1'),
        'database' => env('CATALOG_DB_DATABASE', 'catalog_db'),
        'username' => env('CATALOG_DB_USERNAME', 'catalog_user'),
        'password' => env('CATALOG_DB_PASSWORD', ''),
    ],
    'customer' => [
        'driver' => 'mysql',
        'host' => env('CUSTOMER_DB_HOST', '127.0.0.1'),
        'database' => env('CUSTOMER_DB_DATABASE', 'customer_db'),
        'username' => env('CUSTOMER_DB_USERNAME', 'customer_user'),
        'password' => env('CUSTOMER_DB_PASSWORD', ''),
    ],
];
```

**Models specify connection:**

```php
// Modules/Catalog/App/Models/Product.php
class Product extends Model
{
    protected $connection = 'catalog';
}

// Modules/Customer/App/Models/User.php
class User extends Authenticatable
{
    protected $connection = 'customer';
}
```

**Challenges (theo PDF DB Patterns slide 5):**
- ❌ Queries cần join cross-database
- ❌ Transactions across databases

**Solutions:**
- ✅ CQRS - Read replica
- ✅ Event-driven sync
- ✅ API composition

---

### 🟢 8. CQRS cho Catalog Service

**Theo PDF DB Patterns (Slide 10-16):**

```
Write Side (Command):
    └─ ProductWriteModel → Master DB

Read Side (Query):
    └─ ProductReadModel → Elasticsearch

Events:
    ProductCreated → Index to Elasticsearch
```

**Implementation:**

```bash
composer require elasticsearch/elasticsearch
```

```php
// Modules/Catalog/App/Services/ProductCommandService.php
class ProductCommandService
{
    public function createProduct(array $data)
    {
        $product = Product::create($data);
        
        // Dispatch event to update read store
        event(new ProductCreated($product));
        
        return $product;
    }
}

// Modules/Catalog/App/Services/ProductQueryService.php
class ProductQueryService
{
    public function search(string $query)
    {
        // Search from Elasticsearch (fast!)
        return $this->elasticsearch->search([
            'index' => 'products',
            'body' => [
                'query' => [
                    'match' => ['name' => $query]
                ]
            ]
        ]);
    }
}

// Listener
class IndexProductToElasticsearch
{
    public function handle(ProductCreated $event)
    {
        $this->elasticsearch->index([
            'index' => 'products',
            'id' => $event->product->id,
            'body' => [
                'name' => $event->product->pro_name,
                'price' => $event->product->pro_price,
                'category' => $event->product->category->c_name,
            ],
        ]);
    }
}
```

**Benefits:**
- ✅ Search cực nhanh
- ✅ Tách read/write, giảm load DB
- ✅ Scale read independently

---

### 🟢 9. Saga Pattern cho Order Workflow

**Theo PDF DB Patterns (Slide 39-48):**

```php
// app/Services/OrderSaga.php
class OrderSaga
{
    private array $steps = [];
    private array $executedSteps = [];

    public function addStep(SagaStep $step)
    {
        $this->steps[] = $step;
        return $this;
    }

    public function execute(Transaction $transaction)
    {
        try {
            foreach ($this->steps as $step) {
                $step->execute($transaction);
                $this->executedSteps[] = $step;
            }
            
            return true;

        } catch (\Exception $e) {
            Log::error('Saga execution failed, compensating...', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            // Compensate in reverse order
            foreach (array_reverse($this->executedSteps) as $step) {
                try {
                    $step->compensate($transaction);
                } catch (\Exception $compensateError) {
                    Log::critical('Saga compensation failed', [
                        'step' => get_class($step),
                        'error' => $compensateError->getMessage(),
                    ]);
                }
            }

            throw $e;
        }
    }
}

// Saga Steps
class ReserveStockStep implements SagaStep
{
    public function execute(Transaction $transaction)
    {
        // Call Inventory Service API
        Http::post('http://inventory-service/reserve', [
            'order_id' => $transaction->id,
            'items' => $transaction->items,
        ]);
    }

    public function compensate(Transaction $transaction)
    {
        // Release stock
        Http::post('http://inventory-service/release', [
            'order_id' => $transaction->id,
        ]);
    }
}

class ProcessPaymentStep implements SagaStep
{
    public function execute(Transaction $transaction)
    {
        // Process payment
        $result = Http::post('http://payment-service/process', [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->tr_total,
        ]);

        if (!$result->successful()) {
            throw new PaymentFailedException();
        }
    }

    public function compensate(Transaction $transaction)
    {
        // Refund payment
        Http::post('http://payment-service/refund', [
            'transaction_id' => $transaction->id,
        ]);
    }
}

// Usage in CartController
public function saveCart(Request $request)
{
    $transaction = Transaction::create([...]);

    $saga = new OrderSaga();
    $saga->addStep(new ReserveStockStep())
         ->addStep(new ProcessPaymentStep())
         ->addStep(new CreateShipmentStep())
         ->addStep(new SendNotificationStep());

    try {
        $saga->execute($transaction);
        return redirect()->route('home')->with('success', 'Đặt hàng thành công!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
    }
}
```

**Benefits:**
- ✅ Distributed transaction handling
- ✅ Auto compensation on failure
- ✅ Consistency across services

---

## 🟢 PRIORITY 4: API GATEWAY (Khi có nhiều services)

### 🟢 10. Implement Kong API Gateway

**Docker setup:**

```yaml
# docker-compose.gateway.yml
version: '3.8'

services:
  kong-database:
    image: postgres:13
    environment:
      POSTGRES_USER: kong
      POSTGRES_DB: kong
      POSTGRES_PASSWORD: kong

  kong-migration:
    image: kong:3.4
    command: kong migrations bootstrap
    depends_on:
      - kong-database
    environment:
      KONG_DATABASE: postgres
      KONG_PG_HOST: kong-database

  kong:
    image: kong:3.4
    environment:
      KONG_DATABASE: postgres
      KONG_PG_HOST: kong-database
      KONG_PROXY_ACCESS_LOG: /dev/stdout
      KONG_ADMIN_ACCESS_LOG: /dev/stdout
      KONG_PROXY_ERROR_LOG: /dev/stderr
      KONG_ADMIN_ERROR_LOG: /dev/stderr
      KONG_ADMIN_LISTEN: 0.0.0.0:8001
    ports:
      - "8000:8000"   # Proxy
      - "8001:8001"   # Admin API
    depends_on:
      - kong-database

  konga:
    image: pantsel/konga
    environment:
      NODE_ENV: production
    ports:
      - "1337:1337"   # Konga UI
```

**Configure services:**

```bash
# Add Catalog Service
curl -i -X POST http://localhost:8001/services \
  --data name=catalog-service \
  --data url=http://host.docker.internal:8000

# Add route
curl -i -X POST http://localhost:8001/services/catalog-service/routes \
  --data paths[]=/api/v1/products \
  --data methods[]=GET

# Add plugins
curl -i -X POST http://localhost:8001/services/catalog-service/plugins \
  --data name=rate-limiting \
  --data config.minute=100

curl -i -X POST http://localhost:8001/services/catalog-service/plugins \
  --data name=prometheus
```

**Benefits (theo PDF API Gateway slide 6):**
- ✅ Single entry point
- ✅ Rate limiting
- ✅ Authentication centralized
- ✅ Caching layer
- ✅ Circuit breaker
- ✅ Load balancing
- ✅ Request transformation
- ✅ Monitoring (Prometheus metrics)

---

### 🟢 11. Service Discovery với Consul

**Docker:**

```bash
docker run -d --name=consul \
  -p 8500:8500 \
  consul agent -server -ui -bootstrap-expect=1 -client=0.0.0.0
```

**Laravel Integration:**

```bash
composer require sensiolabs/consul-php-sdk
```

```php
// app/Services/ServiceDiscovery.php
class ServiceDiscovery
{
    private $consul;

    public function __construct()
    {
        $this->consul = new SensioLabs\Consul\ServiceFactory();
    }

    public function register(string $name, string $host, int $port)
    {
        $this->consul->get('agent')->registerService([
            'Name' => $name,
            'Address' => $host,
            'Port' => $port,
            'Check' => [
                'HTTP' => "http://{$host}:{$port}/api/health",
                'Interval' => '10s',
            ],
        ]);
    }

    public function discover(string $serviceName): ?array
    {
        $services = $this->consul->get('health')->service($serviceName)->json();
        
        if (empty($services)) {
            return null;
        }

        // Return first healthy service
        foreach ($services as $service) {
            if ($service['Checks'][0]['Status'] === 'passing') {
                return [
                    'host' => $service['Service']['Address'],
                    'port' => $service['Service']['Port'],
                ];
            }
        }

        return null;
    }
}

// Usage
$discovery = app(ServiceDiscovery::class);
$notificationService = $discovery->discover('notification-service');

if ($notificationService) {
    $url = "http://{$notificationService['host']}:{$notificationService['port']}";
}
```

---

## 📊 KẾT QUẢ SAU KHI CẢI THIỆN

### Trước (Điểm: 55/100)

```
✅ Strangler Pattern: 10/10
✅ Outbox Pattern: 10/10
✅ Event-Driven: 9/10
⚠️ Observable: 6/10
❌ API Gateway: 0/10
❌ Circuit Breaker: 0/10
❌ Service Discovery: 0/10
❌ Database Per Service: 3/10
```

### Sau Improvements (Dự Kiến: 78/100)

```
✅ Strangler Pattern: 10/10
✅ Outbox Pattern: 10/10
✅ Event-Driven: 9/10
✅ Observable: 9/10 (+3) ← ELK + Jaeger
✅ Circuit Breaker: 9/10 (+9) ← ExternalApiService
✅ Health Checks: 10/10 (+8) ← /health, /ready, /metrics
✅ API Gateway: 8/10 (+8) ← Kong
✅ Service Discovery: 8/10 (+8) ← Consul
⚠️ Database Per Service: 7/10 (+4) ← Private tables + separate DBs
⚠️ Saga Pattern: 7/10 (+7) ← Order Saga
```

**New Score: 78/100** (Good → Excellent)

---

## 🎯 TIMELINE

### Week 1 (Đã xong)
- ✅ Health checks
- ✅ Circuit breaker code
- ✅ Metrics endpoint

### Week 2-3
- 🟡 ELK Stack setup
- 🟡 Jaeger tracing
- 🟡 Private tables per service
- 🟡 Update PaymentController to use ExternalApiService

### Month 2
- 🟢 Consul service discovery
- 🟢 Kong API Gateway
- 🟢 Separate databases

### Month 3
- 🟢 Saga Pattern implementation
- 🟢 CQRS for Catalog
- 🟢 Extract more services

---

## 📚 TÀI LIỆU THAM KHẢO

### Tools
- **Kong API Gateway:** https://konghq.com/
- **Consul:** https://www.consul.io/
- **Jaeger:** https://www.jaegertracing.io/
- **ELK Stack:** https://www.elastic.co/elastic-stack

### Patterns
- **Circuit Breaker:** https://martinfowler.com/bliki/CircuitBreaker.html
- **Saga Pattern:** https://microservices.io/patterns/data/saga.html
- **CQRS:** https://martinfowler.com/bliki/CQRS.html
- **Event Sourcing:** https://martinfowler.com/eaaDev/EventSourcing.html

---

**Kết luận:** Sau khi implement các improvements này, dự án sẽ đạt **78/100 điểm** và tuân thủ **gần đầy đủ** các nguyên tắc microservices trong 5 file PDF!
