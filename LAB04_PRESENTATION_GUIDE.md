# LAB 04: PRESENTATION GUIDE
## Microservices Decomposition & C4 Model - Electronics E-Commerce Website

**Course:** Software Architecture-1-2-25 (N01)  
**Group:** 16  
**Members:** Nguyen Tuan Thien - 23010571 | Dang Viet Anh - 23010689  
**Instructor:** M.Sc. Vu Quang Dung

---

## MUC LUC

1. [Kich ban thuyet trinh](#1-kich-ban-thuyet-trinh)
2. [Noi dung chi tiet tung phan](#2-noi-dung-chi-tiet)
3. [Demo code thuc te](#3-demo-code-thuc-te)
4. [Cau hoi thuong gap va cach tra loi](#4-cau-hoi-thuong-gap)
5. [Tips ghi diem](#5-tips-ghi-diem)

---

## 1. KICH BAN THUYET TRINH (15-20 phut)

| Phan | Noi dung | Thoi gian | Nguoi trinh bay |
|------|----------|-----------|-----------------|
| 1 | Gioi thieu Lab 04 & muc tieu | 1.5 phut | Thien |
| 2 | Practice 1: Decomposition by Business Capability | 3.5 phut | Thien |
| 3 | Practice 2: Service Contracts (API Endpoints) | 3 phut | Viet Anh |
| 4 | Practice 3: Communication Strategy (Sync vs Async) | 3 phut | Viet Anh |
| 5 | C4 Model (Level 1-4) | 4 phut | Thien |
| 6 | Mapping to Source Code + Demo | 2 phut | Ca hai |
| 7 | Ket luan | 1 phut | Viet Anh |
| 8 | Q&A | 2-3 phut | Ca hai |

---

## 2. NOI DUNG CHI TIET TUNG PHAN

---

### PHAN 1: GIOI THIEU LAB 04 (1.5 phut)

**Cach noi:**

> "Xin chao thay va cac ban. Nhom 16 trinh bay Lab 04 - **Microservices Decomposition & C4 Model**.
>
> O Lab 02, chung em da thiet ke **Layered Architecture** va nhan ra diem yeu lon: **ASR-1 Scalability** bi danh gia YEU vi phai scale toan bo monolith.
>
> Lab 04 giai quyet van de nay bang cach:
> 1. **Tach monolith thanh microservices** theo Business Capability
> 2. **Dinh nghia Service Contracts** (API endpoints) de cac service giao tiep
> 3. **Chon chien luoc giao tiep** Synchronous vs Asynchronous
> 4. **Mo hinh hoa** he thong bang **C4 Model** (4 cap tu tong quan den code)
>
> Diem dac biet: du an cua chung em da co san **8 modules** tuong ung voi 8 candidate microservices, va da co **Notification Service** chay doc lap nhu mot microservice thuc thu."

---

### PHAN 2: DECOMPOSITION BY BUSINESS CAPABILITY (3.5 phut) - TRONG TAM

**Cach noi:**

> "Nguyen tac cot loi cua microservices decomposition la: **Moi service so huu mot kha nang kinh doanh (business capability) va du lieu cua rieng no**. Khong service nao duoc truy cap truc tiep database cua service khac.
>
> Tu codebase hien tai (Modules/), chung em xac dinh **9 candidate microservices**:"

#### Bang Decomposition (VE LEN BANG hoac slide):

| Microservice | Business Capability | Du lieu so huu | Module tuong ung |
|-------------|---------------------|----------------|------------------|
| **Customer/Identity** | Dang ky, dang nhap, profile, wishlist | User, Wishlist | `Modules/Customer` |
| **Catalog (Product)** | Duyet, tim kiem, danh muc, chi tiet SP | Product, Category, ProImage, Rating | `Modules/Catalog` |
| **Cart** | Gio hang, khoi tao checkout | Cart, CartItem | `Modules/Cart` |
| **Order** | Tao don hang, vong doi don hang | Transaction, Order | Hien nam o Cart/Payment |
| **Payment** | Xu ly thanh toan, callback | Payment records | `Modules/Payment` |
| **Notification** | Gui email xac nhan, thong bao | OutboxMessage, logs | `notification-service/` |
| **Admin** | Quan tri SP, don hang, noi dung | Admin actions | `Modules/Admin` |
| **Content** | Bai viet, banner, tin tuc | Article | `Modules/Content` |
| **Support** | Lien he, ho tro khach hang | Contact | `Modules/Support` |

**Cach noi tiep:**

> "**Diem quan trong so 1: Data Ownership.** Moi service SO HUU du lieu cua rieng no.
> - Catalog Service so huu bang `products`, `categories`
> - Order Service so huu bang `transactions`, `orders`
> - Cart Service **KHONG DUOC** truy van truc tiep bang `products` - phai goi API cua Catalog Service
>
> **Diem quan trong so 2: External Dependencies.** He thong phu thuoc 3 he thong ben ngoai:
> 1. **Payment Gateways**: MoMo, VNPay, PayPal - da tich hop trong `Modules/Payment`
> 2. **Email Provider** (SMTP): Notification Service gui email qua Gmail/SendGrid
> 3. **Shipping Carrier API**: Du dinh tich hop cho theo doi van chuyen
>
> **Tai sao ranh gioi nay?** Vi moi module trong codebase hien tai da co **routes, controllers, models RIENG** - chinh la ranh gioi tu nhien cho microservices."

---

### PHAN 3: SERVICE CONTRACTS - API ENDPOINTS (3 phut)

**Cach noi:**

> "Khi cac service tach rieng, chung can mot cach de giao tiep voi nhau. Do la **Service Contract** - tap hop cac API endpoint ma moi service cung cap."

#### Catalog Service Contract:

| Method | Endpoint | Mo ta | Du lieu tra ve |
|--------|----------|-------|----------------|
| `GET` | `/api/products` | Danh sach san pham (co search: `?q=laptop`) | List product summaries |
| `GET` | `/api/products/{id}` | Chi tiet 1 san pham | Product object day du |
| `GET` | `/api/products/{id}/price` | Tra gia (cho Cart Service goi) | `{ price, sale, currency }` |
| `POST` | `/api/products` | Tao san pham (chi Admin) | Created product |

#### Cart Service Contract:

| Method | Endpoint | Mo ta |
|--------|----------|-------|
| `GET` | `/api/cart` | Xem gio hang cua user dang dang nhap |
| `POST` | `/api/cart/items` | Them san pham vao gio (product_id, quantity) |
| `PUT` | `/api/cart/items/{itemId}` | Cap nhat so luong |
| `DELETE` | `/api/cart/items/{itemId}` | Xoa san pham khoi gio |
| `POST` | `/api/cart/checkout` | Bat dau checkout - validate gia va ton kho |

#### Order & Payment Service Contract:

| Method | Endpoint | Mo ta |
|--------|----------|-------|
| `POST` | `/api/orders` | Tao don hang moi (201 Created) |
| `GET` | `/api/orders/{id}` | Chi tiet don hang |
| `GET` | `/api/orders?user_id=` | Lich su don hang |
| `POST` | `/payment/{method}/{transaction}/init` | Khoi tao thanh toan |
| `GET` | `/payment/momo/return/{tx}` | MoMo callback |
| `GET` | `/payment/vnpay/return/{tx}` | VNPay callback |

**Cach noi:**

> "Diem mau chot cua Service Contract:
>
> **Rule 1: Khong chia se database.** Cart Service muon biet gia san pham? Phai goi `GET /api/products/{id}/price` cua Catalog Service. **KHONG DUOC** query truc tiep bang `products`.
>
> **Rule 2: API la hop dong.** Khi Catalog Service thay doi database schema, API van giu nguyen → Cart Service khong bi anh huong. Day la **Loose Coupling**.
>
> **Mapping voi code hien tai:**
> - Catalog API tuong ung voi routes trong `Modules/Catalog/routes/web.php`
> - Cart API tuong ung voi routes trong `Modules/Cart/routes/web.php`
> - Payment callbacks da co san: MoMo, VNPay, PayPal, QR Code trong `Modules/Payment/routes/web.php`"

---

### PHAN 4: COMMUNICATION STRATEGY - SYNC VS ASYNC (3 phut) - QUAN TRONG

**Cach noi:**

> "Microservices giao tiep voi nhau bang 2 cach: **Synchronous** (dong bo) va **Asynchronous** (bat dong bo). Chon cach nao phu thuoc vao **yeu cau cua tuong tac**."

#### Bang chien luoc giao tiep:

| Tuong tac | Tham gia | Loai | Ly do |
|-----------|----------|------|-------|
| Dang nhap/Profile | Gateway ↔ Customer Service | **Sync (HTTP)** | User can phan hoi ngay lap tuc |
| Duyet/Tim kiem SP | Client ↔ Catalog Service | **Sync (HTTP)** | Browsing can do tre thap |
| Kiem tra gia cho Cart | Cart Service ↔ Catalog Service | **Sync (HTTP)** | Checkout phai dung gia chinh xac |
| Checkout tao don hang | Cart Service ↔ Order Service | **Sync (HTTP)** | Don hang phai thanh cong/that bai ngay |
| Xu ly thanh toan | Payment Service ↔ Gateway | **Sync (HTTP callback)** | Provider tra ve ket qua qua IPN/return URL |
| **Gui email xac nhan** | Order Service → Notification | **ASYNC (Redis Queue)** | **Email KHONG DUOC chan viec tao don hang** |

**Cach noi:**

> "**Tai sao gui email la Asynchronous?** Day la diem quan trong nhat:
>
> Hinh dung: Khach hang vua dat don hang thanh cong. Neu he thong gui email **dong bo** va email server cham (mat 5 giay) hoac loi → khach hang phai **DOI** hoac don hang **THAT BAI** chi vi email.
>
> Giai phap: Dung **Asynchronous messaging**. Order Service tao don hang thanh cong → luu event vao **Outbox** → tra ve '200 OK' cho khach hang NGAY LAP TUC → Notification Service se gui email **SAU DO** doc lap.
>
> Day chinh la **ASR-2 Fault Isolation** tu Lab 01: *'Notification failure must NOT prevent order completion'*.
>
> Trong code thuc te, chung em da implement day du:
> 1. `SaveOrderPlacedToOutbox.php` - Luu event vao bang `outbox_messages`
> 2. `PublishOutboxMessages.php` - Job day event tu outbox len Redis queue
> 3. `notification-service/consumer.php` - Microservice doc lap, doc Redis queue va gui email"

#### So do Outbox Pattern:

```
Don hang thanh cong
       │
       ▼
┌──────────────────┐     ┌──────────────────┐     ┌───────────────────┐
│  Order Service   │────→│  outbox_messages  │────→│  Redis Queue      │
│  (tao don hang)  │ luu │  (database)       │ pub │  (notifications)  │
└──────────────────┘     └──────────────────┘     └────────┬──────────┘
       │                                                     │
       ▼                                                     ▼
  HTTP 200 OK                                    ┌────────────────────┐
  (tra ve ngay)                                  │ Notification Svc   │
                                                 │ (gui email sau)    │
                                                 └────────────────────┘
```

**Noi:**
> "Don hang tra ve thanh cong NGAY cho khach hang. Email duoc gui **SAU DO** boi mot service hoan toan rieng biet. Neu Notification Service bi loi, don hang VAN ton tai va email se duoc gui lai khi service phuc hoi - vi event van con trong Outbox."

---

### PHAN 5: C4 MODEL - 4 CAP (4 phut)

**Cach noi:**

> "C4 Model la cach mo hinh hoa kien truc tu **tong quan den chi tiet** qua 4 cap do. Lab 04 yeu cau Level 1, nhung chung em trinh bay ca 4 cap de day du."

#### Level 1: System Context (zoom xa nhat)

```
                    ┌──────────┐
                    │ Customer │
                    └────┬─────┘
                         │ HTTPS
                         ▼
┌───────┐     ┌──────────────────────┐     ┌──────────────────┐
│ Admin │────→│  ElectroShop         │────→│ Payment Gateways │
└───────┘     │  Platform            │     │ (MoMo, VNPay,    │
              │                      │     │  PayPal)          │
┌───────┐     │                      │────→│ Email Service    │
│ Guest │────→│                      │     │ (SMTP)           │
└───────┘     └──────────────────────┘     └──────────────────┘
```

**Noi:**
> "Level 1 chi co 1 hop lon: **ElectroShop Platform**. Ben ngoai la 3 actors (Customer, Admin, Guest) va 2 he thong ngoai (Payment Gateways, Email Service). Muc nay tra loi cau hoi: **'He thong tuong tac voi AI va voi CAI GI ben ngoai?'**"

#### Level 2: Container (zoom vao he thong)

```
┌─────────────────────────────────────────────────────────────────┐
│                     ElectroShop Platform                         │
│                                                                  │
│  ┌──────────┐  ┌────────────┐                                   │
│  │ Web      │  │ Admin      │                                   │
│  │ Frontend │  │ Panel      │                                   │
│  └────┬─────┘  └──────┬─────┘                                   │
│       │               │                                          │
│       └───────┬───────┘                                          │
│               ▼                                                  │
│      ┌────────────────┐                                          │
│      │ Kong API       │                                          │
│      │ Gateway        │                                          │
│      └───┬────┬───┬───┘                                          │
│          │    │   │                                               │
│   ┌──────┘  ┌┘   └──────┐                                       │
│   ▼         ▼            ▼                                       │
│ ┌────────┐ ┌────────┐ ┌────────┐ ┌──────────────┐              │
│ │Catalog │ │Order   │ │Payment │ │Notification  │              │
│ │Service │ │Service │ │Service │ │Service       │              │
│ └───┬────┘ └───┬────┘ └───┬────┘ └──────────────┘              │
│     │          │          │                                      │
│     ▼          ▼          ▼                                      │
│  ┌─────┐   ┌─────┐   ┌─────┐   ┌─────┐                        │
│  │MySQL│   │MySQL│   │MySQL│   │Redis│                          │
│  │(Cat)│   │(Ord)│   │(Pay)│   │Cache│                          │
│  └─────┘   └─────┘   └─────┘   └─────┘                         │
│                                                                  │
│  ┌────────┐ ┌────────┐ ┌──────────┐ ┌────────┐                 │
│  │Consul  │ │Jaeger  │ │Prometheus│ │Grafana │                  │
│  │(disco.)│ │(trace) │ │(metrics) │ │(dash.) │                  │
│  └────────┘ └────────┘ └──────────┘ └────────┘                 │
└─────────────────────────────────────────────────────────────────┘
```

**Noi:**
> "Level 2 zoom vao ben trong he thong. Chung em thay:
> - **Kong API Gateway** la diem vao duy nhat - tat ca request di qua day
> - **4 microservices chinh**: Catalog, Order, Payment, Notification
> - **Database per Service**: Moi service co MySQL rieng (ASR-1 Scalability)
> - **Ha tang ho tro**: Consul (Service Discovery), Jaeger (Distributed Tracing), Prometheus + Grafana (Monitoring)
>
> Tat ca deu da duoc cau hinh trong `docker-compose.microservices.yml` voi 17+ containers."

#### Level 3: Component (zoom vao Catalog Service)

**Noi:**
> "Level 3 zoom vao ben trong **Catalog Service**. Ben trong no co:
> - **ProductController**: Xu ly API request
> - **CategoryController**: Xu ly danh muc
> - **SearchController**: Tim kiem san pham
> - **ProductService**: Business logic
> - **CacheService**: Redis caching
> 
> Day chinh la **Layered Architecture** tu Lab 02 duoc ap dung BEN TRONG moi microservice."

#### Level 4: Code (zoom vao chi tiet class)

**Noi:**
> "Level 4 chi ra code chi tiet. Vi du luong 'Xem chi tiet san pham':
>
> `HTTP GET /products/{id}` → `ProductController.show()` → `ProductService.getProductDetails()` → `ProductRepository.findById()` → MySQL
>
> Moi class da duoc implement trong `app/Lab03/` nhu da trinh bay o Lab 02.
>
> **C4 Model cho phep ban zoom tu tong quan (Level 1) den dong code (Level 4) - giup ca nguoi ky thuat va khong ky thuat deu hieu duoc he thong.**"

---

### PHAN 6: MAPPING TO SOURCE CODE + DEMO (2 phut)

**Cach noi:**

> "Moi thu chung em trinh bay deu co trong source code thuc te:"

| Kien truc | File/Folder thuc te |
|-----------|---------------------|
| 8 Modules (candidate services) | `Modules/Admin, Catalog, Cart, Customer, Payment, Review, Content, Support` |
| Catalog routes (Service Contract) | `Modules/Catalog/routes/web.php` |
| Cart routes | `Modules/Cart/routes/web.php` |
| Payment callbacks (MoMo, VNPay...) | `Modules/Payment/routes/web.php` |
| Outbox Pattern (Async messaging) | `app/Models/Models/OutboxMessage.php` |
| Event Listener | `app/Listeners/SaveOrderPlacedToOutbox.php` |
| Outbox Publisher Job | `app/Jobs/PublishOutboxMessages.php` |
| Notification Microservice | `notification-service/consumer.php` |
| C4 Level 1 Diagram | `Design/c4-level1-context.puml` |
| C4 Level 2 Diagram | `Design/c4-level2-container.puml` |
| C4 Level 3 Diagram | `Design/c4-level3-catalog-component.puml` |
| C4 Level 4 Diagram | `Design/c4-level4-lab03-class.puml` |
| Docker Microservices | `docker-compose.microservices.yml` (17+ services) |
| Sequence Diagrams | `Design/sequence-checkout-flow.puml`, `sequence-payment-flow.puml` |

**Noi:**
> "Day KHONG chi la ly thuyet. Tat ca 8 modules, Outbox Pattern, Notification Service, va 12 file diagram PlantUML deu co trong source code. Docker Compose da cau hinh san 17+ services de trien khai toan bo kien truc microservices."

---

### PHAN 7: KET LUAN (1 phut)

**Cach noi:**

> "Lab 04 da hoan thanh 4 muc tieu:
>
> 1. **Decomposition**: Tach monolith thanh 9 microservices theo business capability, moi service so huu du lieu rieng
> 2. **Service Contracts**: Dinh nghia API endpoints cho Catalog, Cart, Order, Payment - dam bao khong chia se database
> 3. **Communication Strategy**: Sync cho tuong tac can phan hoi ngay (browsing, checkout), Async cho tac vu khong chan (email) - dung Outbox Pattern
> 4. **C4 Model**: Mo hinh hoa tu System Context (Level 1) den Code (Level 4)
>
> **Bai hoc quan trong:** Microservices KHONG chi la 'tach code ra'. No la ve **data ownership** va **giao tiep** giua cac service. Moi service phai doc lap ve data, deploy, va scale.
>
> Cam on thay va cac ban."

---

## 3. DEMO CODE THUC TE

### Demo 1: 8 Module = 8 Candidate Microservices

```
Modules/
├── Admin/       ← Admin Service
├── Cart/        ← Cart Service
├── Catalog/     ← Catalog (Product) Service
├── Content/     ← Content Service
├── Customer/    ← Customer/Identity Service
├── Payment/     ← Payment Service
├── Review/      ← Review Service
└── Support/     ← Support Service
```

**Noi:** "Moi module co `routes/web.php`, `Controllers/`, `Resources/views/` rieng biet - day la ranh gioi tu nhien de tach thanh microservice."

### Demo 2: Service Contract thuc te - Payment Module

Mo file `Modules/Payment/routes/web.php`:

```php
Route::group(['prefix' => 'payment', 'middleware' => 'CheckLoginUser'], function(){
    // Khoi tao thanh toan
    Route::post('/{method}/{transaction}/init', [PaymentController::class, 'init']);
    
    // MoMo callbacks
    Route::get('/momo/return/{transaction}', [PaymentController::class, 'momoReturn']);
    Route::post('/momo/ipn/{transaction}', [PaymentController::class, 'momoIpn']);
    
    // PayPal callbacks
    Route::get('/paypal/return/{transaction}', [PaymentController::class, 'paypalReturn']);
    
    // VNPay callbacks
    Route::get('/vnpay/return/{transaction}', [PaymentController::class, 'vnpayReturn']);
});
```

**Noi:** "Day la Service Contract thuc te cua Payment Service - ho tro 4 phuong thuc thanh toan voi callback rieng cho tung gateway."

### Demo 3: Async Communication - Outbox Pattern Pipeline

**Buoc 1:** `app/Listeners/SaveOrderPlacedToOutbox.php` - Luu event vao database:
```php
public function handle(OrderPlaced $event): void
{
    OutboxMessage::create([
        'aggregate_type' => 'Transaction',
        'aggregate_id' => $event->transaction->id,
        'event_type' => 'OrderPlaced',
        'payload' => [
            'transaction_id' => $event->transaction->id,
            'user_email' => $userEmail,
            'total' => $event->transaction->tr_total,
            // ...
        ],
        'published' => false,  // Chua gui
    ]);
}
```

**Buoc 2:** `app/Jobs/PublishOutboxMessages.php` - Day event len Redis queue:
```php
public function handle(): void
{
    $messages = OutboxMessage::unpublished()
        ->limit($this->batchSize)
        ->get();
    // Push to Redis queue 'notifications'...
}
```

**Buoc 3:** `notification-service/consumer.php` - Microservice doc lap doc queue:
```php
// Connect to Redis
$redis = new Client([
    'host' => $config['redis']['host'],
    'port' => $config['redis']['port'],
]);
// Loop: doc queue va gui email...
```

**Noi:** "Day la Async communication pipeline hoan chinh: Event → Outbox → Redis Queue → Notification Service. Don hang tra ve ngay, email gui sau."

### Demo 4: C4 Diagrams (PlantUML)

Mo file `Design/c4-level1-context.puml` va render bang PlantUML extension (Alt+D).

**Noi:** "Tat ca diagram deu la code (PlantUML), co the version control bang Git va tu dong render."

---

## 4. CAU HOI THUONG GAP VA CACH TRA LOI

### Q1: "Decomposition by Business Capability nghia la gi?"

**Tra loi:**
> "Nghia la chia he thong theo **kha nang kinh doanh**, khong phai theo ky thuat.
>
> **SAI:** Tach theo ky thuat → 'Database Service', 'Authentication Service', 'API Service'
> **DUNG:** Tach theo business → 'Catalog Service' (quan ly san pham), 'Order Service' (quan ly don hang), 'Payment Service' (xu ly thanh toan)
>
> Moi service dai dien cho mot **nghiep vu kinh doanh** ma cong ty can: ban san pham, xu ly don hang, thanh toan, thong bao khach hang. Neu cong ty co phong 'Product Management' va phong 'Order Processing', thi nen co 'Product Service' va 'Order Service'."

### Q2: "Data Ownership la gi? Tai sao quan trong?"

**Tra loi:**
> "Data Ownership nghia la moi microservice **SO HUU** va **CHI MINH NO** duoc truy cap database cua no.
>
> Vi du:
> - Catalog Service so huu `mysql-catalog` → chi no duoc SELECT/INSERT bang `products`
> - Cart Service muon biet gia san pham → **KHONG DUOC** truy van `mysql-catalog` truc tiep → phai goi `GET /api/products/{id}/price`
>
> **Tai sao?** Vi neu 2 service chia se 1 database:
> 1. Doi schema bang `products` → Cart Service bi loi
> 2. Scale Catalog Service → van bi bottleneck vi cung database
> 3. Khong the deploy doc lap
>
> Trong `docker-compose.microservices.yml`, chung em co 3 database rieng: `mysql-catalog` (port 3310), `mysql-order` (port 3311), `mysql-user` (port 3312)."

### Q3: "Khi nao dung Synchronous, khi nao dung Asynchronous?"

**Tra loi:**
> "**Synchronous (HTTP)** khi:
> - User can phan hoi **NGAY LAP TUC** (duyet san pham, dang nhap, checkout)
> - Ket qua **QUYET DINH** buoc tiep theo (gia san pham ảnh huong tong don hang)
>
> **Asynchronous (Queue/Event)** khi:
> - Tac vu **KHONG CAN** phan hoi ngay (gui email, gui SMS, cap nhat bao cao)
> - Muon **FAULT ISOLATION** - loi o service nay khong anh huong service khac
> - Tac vu **MAT THOI GIAN** (xu ly anh, tao PDF)
>
> Vi du hay nhat: Dat don hang thanh cong → tra ve cho khach NGAY (sync) → gui email SAU (async). Khach khong can doi email de biet don hang thanh cong."

### Q4: "Outbox Pattern la gi? Tai sao khong gui email truc tiep?"

**Tra loi:**
> "Gui email truc tiep co van de: Neu email server loi giua chung → don hang da luu nhung email KHONG gui → khach khong biet.
>
> **Outbox Pattern** giai quyet bang cach:
> 1. Luu event vao bang `outbox_messages` CUNG giao dich voi don hang (trong 1 database transaction)
> 2. Neu giao dich thanh cong → event chac chan duoc luu
> 3. Job rieng se doc outbox va gui len Redis queue
> 4. Notification Service doc queue va gui email
>
> **Uu diem:** Dam bao **AT LEAST ONCE delivery** - event khong bao gio bi mat. Neu Notification Service loi, event van trong outbox, se duoc gui lai khi service phuc hoi."

### Q5: "C4 Model co may cap? Lab 04 yeu cau cap nao?"

**Tra loi:**
> "C4 Model co 4 cap:
> - **Level 1 - System Context:** Tong quan, he thong la 1 hop, chi actor va he thong ngoai
> - **Level 2 - Container:** Zoom vao, thay cac service, database, API gateway
> - **Level 3 - Component:** Zoom vao 1 service, thay cac class/module ben trong
> - **Level 4 - Code:** Zoom vao 1 class, thay method va flow cu the
>
> Lab 04 yeu cau **Level 1** (System Context). Nhung nhom chung em trinh bay ca 4 cap de day du hon, va tat ca da co file PlantUML trong folder `Design/`."

### Q6: "Tai sao dung Kong API Gateway?"

**Tra loi:**
> "API Gateway la **diem vao duy nhat** cho tat ca request tu client. Kong dam nhiem:
> - **Routing:** Chuyen request den dung service (`/api/products/*` → Catalog Service)
> - **Rate Limiting:** Gioi han 100 request/phut de chong DDoS
> - **Authentication:** Kiem tra token truoc khi cho request vao
> - **Load Balancing:** Phan phoi tai giua nhieu instance cua 1 service
> - **Logging:** Ghi log tat ca request de audit
>
> Khong co API Gateway, client phai biet dia chi cua tung service - rat phuc tap va khong bao mat."

### Q7: "Notification Service la microservice thuc su chua?"

**Tra loi:**
> "Da! No la microservice thuc su vi:
> 1. **Chay doc lap**: Co folder rieng `notification-service/` voi `consumer.php`, `Dockerfile`
> 2. **Database rieng**: Doc tu Redis queue, khong truy cap MySQL cua Order Service
> 3. **Deploy rieng**: Co container rieng trong Docker Compose (`notification_service`)
> 4. **Giao tiep qua message**: Khong goi HTTP truc tiep, doc event tu Redis queue
>
> Day la vi du cua **Strangler Pattern** - dan dan tach 1 chuc nang tu monolith ra thanh microservice doc lap."

---

## 5. TIPS GHI DIEM

### DO - Nen lam:

1. **Ve so do Decomposition len bang** - 9 service voi du lieu rieng → truc quan
2. **Nhan manh Data Ownership** - day la nguyen tac so 1 cua microservices
3. **Giai thich Sync vs Async bang vi du cu the** - dat don hang (sync) vs gui email (async)
4. **Demo Outbox Pattern pipeline** - 3 file code thuc te (Listener → Job → Consumer)
5. **Chi vao Docker Compose** - 17+ services, 3 database rieng
6. **Dung tu "Business Capability"** nhieu lan - cho thay ban hieu nguyen tac
7. **So sanh C4 voi zoom camera** - Level 1 zoom xa, Level 4 zoom gan

### DON'T - Khong nen lam:

1. **Khong noi "tach code ra la microservices"** - phai nhac DATA OWNERSHIP
2. **Khong bo qua Async/Outbox** - day la diem khac biet lon
3. **Khong chi trinh bay Level 1** - trinh bay ca 4 cap C4 de an tuong hon
4. **Khong quen lien ket voi Lab truoc** - ASR-1 Scalability yeu → can Microservices

### Cau mo dau gay an tuong:

> "Lab 02 cho thay Layered Architecture **yeu voi Scalability**. Lab 04 la cau tra loi: tach he thong thanh 9 microservices doc lap, moi service so huu du lieu rieng, giao tiep qua API va message queue. Va diem dac biet - tat ca da co trong SOURCE CODE thuc te."

### Cau ket thuc gay an tuong:

> "Tu 1 monolith co 8 modules, chung em da thiet ke 9 microservices voi service contracts ro rang, communication strategy hop ly, va mo hinh hoa bang C4 Model 4 cap. Notification Service da la microservice thuc su - chay doc lap, giao tiep qua Redis queue, deploy rieng bang Docker. Day la kien truc PRODUCTION-READY."

---

## 6. PHIEU GHI NHO

```
+------------------------------------------------+
|           PHIEU GHI NHO - LAB 04               |
+------------------------------------------------+
|                                                |
| PRACTICE 1: DECOMPOSITION                     |
| 9 microservices theo Business Capability       |
| Rule: Moi service SO HUU du lieu rieng        |
| 3 external: Payment GW, Email, Shipping       |
|                                                |
| PRACTICE 2: SERVICE CONTRACTS                  |
| Catalog: GET/POST /api/products                |
| Cart: GET/POST/PUT/DELETE /api/cart            |
| Order: POST /api/orders                        |
| Payment: POST /payment/{method}/init           |
| Rule: KHONG chia se database                   |
|                                                |
| PRACTICE 3: COMMUNICATION                     |
| SYNC: Browsing, checkout, payment (can ngay)   |
| ASYNC: Email, notification (khong chan)         |
| Pattern: Outbox → Redis Queue → Notif Svc     |
|                                                |
| C4 MODEL:                                      |
| L1: System Context (actors + external)         |
| L2: Container (services + DB + infra)          |
| L3: Component (ben trong 1 service)            |
| L4: Code (class + method)                      |
|                                                |
| KEY MESSAGE:                                   |
| "Microservices = Data Ownership + APIs"        |
| "Sync for immediate, Async for non-blocking"   |
+------------------------------------------------+
```

---

## 7. CHIA PHAN TRINH BAY

### Nguyen Tuan Thien (23010571):

| Phan | Noi dung | Thoi gian |
|------|----------|-----------|
| Mo dau | Gioi thieu Lab 04, lien ket ASR-1 tu Lab 02 | 1.5 phut |
| Decomposition | 9 services, data ownership, external deps | 3.5 phut |
| C4 Model | Level 1 → 2 → 3 → 4 (zoom in) | 4 phut |
| Demo | Mo code, chi folder structure | 1 phut |

### Dang Viet Anh (23010689):

| Phan | Noi dung | Thoi gian |
|------|----------|-----------|
| Service Contracts | API endpoints cua Catalog, Cart, Order, Payment | 3 phut |
| Communication | Sync vs Async, bang chien luoc, Outbox Pattern | 3 phut |
| Demo | Mo Outbox code pipeline (3 file) | 1 phut |
| Ket luan | Tong ket 4 muc tieu | 1 phut |

---

## 8. SO SANH BAO CAO VS YEU CAU LAB

| Yeu cau Lab 04 | Trong bao cao .docx | Danh gia |
|----------------|---------------------|----------|
| Decomposition by Business Capability | 9 candidate microservices voi data ownership | DAT |
| Service Contracts (API Endpoints) | Catalog, Cart, Order, Payment contracts | DAT |
| Communication Strategy (Sync/Async) | Bang 6 tuong tac voi ly do cu the | DAT |
| C4 Level 1 - System Context | Figure 6.1 | DAT |
| C4 Level 2 - Container (BONUS) | Figure 6.2 | DAT (BONUS) |
| C4 Level 3 - Component (BONUS) | Figure 6.3 | DAT (BONUS) |
| C4 Level 4 - Code (BONUS) | Figure 6.4 | DAT (BONUS) |
| Sequence Diagram | Figure 5.2: Add to Cart | DAT |
| Mapping to Source Code | Section 7: Module → Routes → Outbox pipeline | DAT (BONUS) |

**Ket luan: Bao cao VUOT yeu cau. Lab chi yeu cau C4 Level 1, nhom da lam ca 4 cap + Outbox Pattern + code thuc te.**

---

*File nay duoc tao de ho tro thuyet trinh Lab 04. Chuc nhom 16 thuyet trinh thanh cong!*
