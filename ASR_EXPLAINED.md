# ASR - ARCHITECTURALLY SIGNIFICANT REQUIREMENTS
## Giai thich chi tiet cho du an ElectroShop

---

## 1. ASR LA GI?

**ASR = Architecturally Significant Requirements** (Yeu cau co y nghia kien truc)

La nhung yeu cau **phi chuc nang (NFR)** ma neu **KHONG DAT** thi he thong coi nhu **THAT BAI** - bat ke chuc nang co chay dung hay khong.

### So sanh de hieu:

| | Functional Requirement (FR) | ASR |
|---|---|---|
| **Cau hoi** | He thong lam **gi**? | He thong lam **tot nhu the nao**? |
| **Vi du** | Khach hang dat duoc don hang | He thong chiu duoc 5,000 nguoi dat cung luc |
| **Neu khong dat?** | Mat 1 tinh nang | **MAT TOAN BO** he thong |
| **So luong** | 10-20 cai | Chi **3-5 cai** quan trong nhat |

### ASR khac gi NFR thuong?

| | NFR thuong | ASR |
|---|---|---|
| **Muc do** | Quan trong nhung khong phai tat ca anh huong kien truc | **QUYET DINH** kien truc he thong |
| **Hau qua neu khong dat** | Giam trai nghiem | He thong **THAT BAI** |
| **Vi du** | "Giao dien dep, responsive" | "Chiu 5,000 users dong thoi trong Flash Sale" |

**Noi ngan gon:** ASR la **nhung NFR quan trong nhat** ma truc tiep **quyet dinh ban chon kien truc gi**.

---

## 2. BA ASR CUA DU AN ELECTROSHOP

---

### ASR-1: SCALABILITY (Kha nang mo rong)

**Phat bieu:**
> "He thong phai ho tro tang dot bien tu 500 den 5,000 nguoi dung dong thoi trong cac dot sale theo mua (Flash Sale, Black Friday)."

**Neu khong dat:**
- Website sap khi nhieu nguoi truy cap
- Mat khach hang va mat doanh thu
- Uy tin thuong hieu bi anh huong

**Tac dong len kien truc:**

| Quyet dinh kien truc | Ly do |
|----------------------|-------|
| Dung **Microservices** | Scale rieng tung service (chi scale Catalog khi nhieu nguoi duyet SP) |
| Dung **API Gateway (Kong)** | Phan phoi tai giua cac instance, rate limiting |
| Dung **Database per Service** | 3 MySQL rieng (catalog, order, user) → tranh bottleneck |
| Dung **Redis Cache** | Giam tai cho database, response nhanh hon |
| Dung **Load Balancer** | Phan phoi request giua nhieu instance |

**Chung minh bang code:**
- `docker-compose.microservices.yml`: 17+ services, 3 database rieng
- `mysql-catalog` (:3310), `mysql-order` (:3311), `mysql-user` (:3312)
- `kong` API Gateway (:9000) lam load balancer
- `redis` (:6381) lam cache

---

### ASR-2: SECURITY (Bao mat & Kiem soat truy cap)

**Phat bieu:**
> "Moi truy cap vao chuc nang quan tri phai duoc xac thuc va phan quyen qua token bao mat. Mat khau va thong tin thanh toan phai tuan thu chuan cong nghiep."

**Neu khong dat:**
- Lo thong tin khach hang → vi pham phap luat
- Bi tan cong → mat du lieu, mat tien
- Mat uy tin, co the bi kien

**Tac dong len kien truc:**

| Quyet dinh kien truc | Ly do |
|----------------------|-------|
| Dung **Gateway Pattern (Kong)** | 1 diem kiem tra bao mat duy nhat truoc khi request vao he thong |
| Dung **Middleware Pattern** | Auth check o Business Logic Layer, moi request phai qua middleware |
| **Khong luu thong tin the** | Delegate cho MoMo, VNPay, PayPal xu ly thanh toan |
| Dung **Bcrypt** cho mat khau | Ma hoa 1 chieu, cost factor 12 |
| Dung **CSRF Protection** | Chong tan cong Cross-Site Request Forgery |
| Dung **HTTPS/TLS** | Ma hoa du lieu truyen tai |

**Chung minh bang code:**
- `app/Http/Kernel.php`: Middleware stack (CSRF, session encryption, auth check)
- `Modules/Admin/`: Bao ve boi `AdminAuthMiddleware`
- `Modules/Payment/routes/web.php`: MoMo, VNPay, PayPal callbacks (khong luu the)
- Kong API Gateway: Rate limiting chong DDoS

---

### ASR-3: MODIFIABILITY (Kha nang thay doi)

**Phat bieu:**
> "Tich hop mot Payment Gateway moi (vi du: Stripe) KHONG duoc yeu cau thay doi module Product Inventory hay User Management."

**Neu khong dat:**
- Moi lan them tinh nang phai sua nhieu cho → de loi
- Thoi gian phat trien lau, chi phi cao
- He thong nhanh chong tro nen kho bao tri

**Tac dong len kien truc:**

| Quyet dinh kien truc | Ly do |
|----------------------|-------|
| Dung **Layered Architecture** | Tach tang: thay doi 1 tang khong anh huong tang khac |
| Dung **Interface** | Phu thuoc vao abstraction, khong phu thuoc implementation cu the |
| Dung **Module hoa** | 8 modules doc lap, moi module co routes/controllers/views rieng |
| Dung **Dependency Injection** | ServiceProvider bind interface → implementation, doi 1 dong de switch |
| Dung **Separation of Concerns** | Moi class lam 1 viec duy nhat |

**Chung minh bang code:**
- 8 modules doc lap: `Modules/Admin, Catalog, Cart, Customer, Payment, Review, Content, Support`
- `app/Lab03/Repositories/ProductRepositoryInterface.php`: Contract giua cac tang
- `app/Lab03/Providers/Lab03ServiceProvider.php`: Bind interface → implementation
- Them Payment Gateway moi: chi sua `Modules/Payment/`, KHONG sua `Modules/Catalog/`

---

## 3. ASR CARDS (THE MO TA CHI TIET)

ASR Card la format chuan de trinh bay ASR. Moi card gom 4 phan:

### ASR Card 1: Scalability

```
┌───────────────────────────────────────────────────────────────┐
│                      ASR CARD 1                                │
├───────────────────────────────────────────────────────────────┤
│ Quality Attribute:  SCALABILITY                                │
│                                                                │
│ Statement:                                                     │
│ He thong phai xu ly 5,000 nguoi dung dong thoi trong          │
│ cac dot Flash Sale voi response time p95 < 2 giay.            │
│                                                                │
│ Architectural Impact:                                          │
│ → Microservices architecture (scale tung service doc lap)     │
│ → Kong API Gateway (load balancing + rate limiting)           │
│ → Database per Service (3 MySQL rieng)                         │
│ → Redis caching (giam tai database)                            │
│ → Stateless design (de scale ngang)                            │
│                                                                │
│ Code Evidence:                                                 │
│ → docker-compose.microservices.yml: 17+ services              │
│ → mysql-catalog (:3310), mysql-order (:3311),                 │
│   mysql-user (:3312)                                           │
│ → kong (:9000), redis (:6381)                                 │
│                                                                │
│ Trade-off:                                                     │
│ Duoc: Scale doc lap, chiu tai cao                              │
│ Mat: Phuc tap hon monolith (17+ containers)                   │
└───────────────────────────────────────────────────────────────┘
```

### ASR Card 2: Security

```
┌───────────────────────────────────────────────────────────────┐
│                      ASR CARD 2                                │
├───────────────────────────────────────────────────────────────┤
│ Quality Attribute:  SECURITY & ACCESS CONTROL                  │
│                                                                │
│ Statement:                                                     │
│ Moi truy cap quan tri phai xac thuc va phan quyen.            │
│ Thong tin thanh toan tuan thu chuan PCI DSS.                  │
│ Mat khau phai duoc ma hoa Bcrypt.                              │
│                                                                │
│ Architectural Impact:                                          │
│ → Gateway Pattern (Kong): Diem kiem tra bao mat tap trung    │
│ → Middleware stack: CSRF, session encryption, auth check      │
│ → Payment delegation: Khong luu the, delegate MoMo/VNPay     │
│ → Bcrypt password hashing (cost 12)                            │
│ → HTTPS/TLS cho toan bo giao tiep                              │
│                                                                │
│ Code Evidence:                                                 │
│ → app/Http/Kernel.php: Middleware stack                        │
│ → Modules/Admin/: AdminAuthMiddleware bao ve                   │
│ → Modules/Payment/: MoMo, VNPay, PayPal callbacks             │
│                                                                │
│ Trade-off:                                                     │
│ Duoc: Bao mat tap trung, tuan thu PCI DSS                     │
│ Mat: Them do tre vi moi request qua nhieu lop middleware      │
└───────────────────────────────────────────────────────────────┘
```

### ASR Card 3: Modifiability

```
┌───────────────────────────────────────────────────────────────┐
│                      ASR CARD 3                                │
├───────────────────────────────────────────────────────────────┤
│ Quality Attribute:  MODIFIABILITY & LOOSE COUPLING             │
│                                                                │
│ Statement:                                                     │
│ Tich hop Payment Gateway moi KHONG duoc anh huong             │
│ module Product hay User. Thay doi 1 module khong              │
│ lam hong module khac.                                          │
│                                                                │
│ Architectural Impact:                                          │
│ → Layered Architecture: 4 tang voi Strict Downward Dependency │
│ → Interface-based design: ProductRepositoryInterface          │
│ → Module hoa: 8 modules doc lap                                │
│ → Dependency Injection: ServiceProvider bind interface        │
│ → Separation of Concerns: moi class 1 trach nhiem             │
│                                                                │
│ Code Evidence:                                                 │
│ → app/Lab03/: Controllers/ → Services/ → Repositories/        │
│ → ProductRepositoryInterface.php: Contract giua tang          │
│ → Lab03ServiceProvider.php: Bind interface → implementation   │
│ → 8 Modules/ doc lap voi routes, controllers, views rieng     │
│                                                                │
│ Trade-off:                                                     │
│ Duoc: Thay doi doc lap, de bao tri, de test                   │
│ Mat: Them layers → them code boilerplate                       │
└───────────────────────────────────────────────────────────────┘
```

---

## 4. ASR ANH HUONG DEN TOAN BO HANH TRINH CAC LAB

```
Lab 01: Xac dinh 3 ASRs (Scalability, Security, Modifiability)
  │
  │  ASR-3 (Modifiability) → Can tach tang, interface
  ▼
Lab 02: Chon Layered Architecture (4 tang, Strict Downward Dependency)
  │     → Danh gia: ASR-2 TOT, ASR-3 TOT, ASR-1 YEU
  │
  │  ASR-1 (Scalability) → Layered khong du, can tach service
  ▼
Lab 04: Chuyen sang Microservices Decomposition
  │     → 9 services, Database per Service, Sync/Async
  │     → Outbox Pattern cho Fault Isolation (lien quan ASR-2)
  │
  │  Can danh gia toan dien
  ▼
Lab 08: ATAM Analysis + Deployment View
        → SS1 (Scalability): Microservices THANG
        → AS1 (Availability): Microservices THANG
        → Trade-off: Phuc tap hon nhung XUNG DANG cho e-commerce
```

**Moi Lab xay dung tren Lab truoc, va ASR la "kim chi nam" xuyen suot.**

---

## 5. ATAM - DANH GIA ASR BANG SCENARIOS

ATAM (Architecture Trade-off Analysis Method) la cach danh gia kien truc co **KHACH QUAN** hay khong thoa man ASRs.

### Scenario Template (6 thanh phan):

| Thanh phan | Y nghia | Giai thich |
|------------|---------|------------|
| **Source** | Ai/cai gi tao ra su kien | Nguoi dung, he thong ben ngoai, loi deploy... |
| **Stimulus** | Su kien xay ra | 10K users truy cap, service bi sap... |
| **Artifact** | Thanh phan bi anh huong | API Gateway, Catalog Service, Database... |
| **Environment** | Dieu kien khi xay ra | Flash Sale, gio cao diem, loi 1 phan... |
| **Response** | He thong phan ung the nao | Van hoat dong, chuyen sang backup... |
| **Measure** | Cach do luong | p95 < 2 giay, 0 don hang mat, 99.9% uptime... |

### Scenario SS1: Scalability (Flash Sale)

| Thanh phan | Noi dung |
|------------|---------|
| Source | 10,000 nguoi dung dong thoi |
| Stimulus | Tim kiem san pham + Dat don hang cung luc |
| Artifact | API Gateway, Catalog Service, Order Service, Redis, Databases |
| Environment | Flash Sale / Black Friday |
| Response | He thong van hoat dong, khong sap |
| Measure | Response time p95 < 2 giay |

**Danh gia:**

| Kien truc | Ket qua | Ly do |
|-----------|---------|-------|
| **Monolithic** | **YEU** | Phai scale toan bo app. DB chung la bottleneck. |
| **Microservices** | **TOT** | Scale rieng Catalog (doc nhieu) va Order (ghi nhieu). Cache giam tai DB. |

### Scenario AS1: Availability (Service Failure)

| Thanh phan | Noi dung |
|------------|---------|
| Source | Loi deploy / Notification Service down |
| Stimulus | Khach dat don hang khi Notification Service loi |
| Artifact | Order Service, RabbitMQ, Notification Service |
| Environment | Hoat dong binh thuong, 1 service loi 1 gio |
| Response | Don hang VAN thanh cong. Email gui lai sau khi phuc hoi. |
| Measure | 0 don hang mat, email gui 100% sau recovery |

**Danh gia:**

| Kien truc | Ket qua | Ly do |
|-----------|---------|-------|
| **Monolithic** | **YEU** | Loi notification co the chan checkout. Khong co fault isolation. |
| **Microservices** | **TOT** | Order tach khoi Notification qua RabbitMQ. Message buffer, gui lai sau. |

---

## 6. TRADE-OFF TONG THE

### Trade-off Statement:

> "Microservices cai thien Scalability va Availability bang cach co lap loi va scale doc lap, nhung DANH DOI bang **do phuc tap van hanh cao hon**."

### Bang Trade-off chi tiet:

| Tieu chi | Monolithic | Microservices | Ket luan |
|----------|-----------|---------------|----------|
| **Scalability** | Scale toan bo (lang phi) | Scale tung service (hieu qua) | MS thang |
| **Availability** | 1 loi → anh huong all | 1 loi → service khac van chay | MS thang |
| **Security** | Tap trung (de quan ly) | Can Gateway + nhieu diem kiem tra | Ngang nhau |
| **Modifiability** | Tot neu dung Layer | Tot hon nho module doc lap | MS tot hon |
| **Complexity** | Don gian (3-5 containers) | Phuc tap (17+ containers) | Mono thang |
| **Deploy** | 1 unit | Nhieu unit | Mono don gian hon |
| **Team** | Team nho | Team lon, song song | Tuy context |

### Tai sao van chon Microservices?

Doi voi **e-commerce** nhu ElectroShop:
1. Flash Sale tang traffic **10 lan** → **CAN** scale doc lap (ASR-1)
2. Gui email cham → **KHONG DUOC** chan checkout → **CAN** Fault Isolation (ASR-2)
3. Them payment gateway moi → **CAN** thay doi doc lap (ASR-3)
4. Do phuc tap duoc giai quyet bang **Docker Compose** (1 lenh deploy) va **Monitoring** (Prometheus + Grafana)

---

## 7. TOM TAT

### ASR la gi?
- La nhung **NFR quan trong nhat** quyet dinh kien truc he thong
- Neu khong dat → he thong THAT BAI

### 3 ASR cua ElectroShop:
1. **Scalability** → Buoc dung Microservices, API Gateway, Database per Service
2. **Security** → Buoc dung Gateway Pattern, Middleware, Payment delegation
3. **Modifiability** → Buoc dung Layered Architecture, Interface, Module hoa

### ASR Cards:
- Format chuan trinh bay ASR: Quality Attribute → Statement → Impact → Evidence → Trade-off

### ATAM:
- Danh gia kien truc bang Scenarios cu the (SS1 Scalability, AS1 Availability)
- Microservices THOA MAN ca 2 scenarios nhung DANH DOI bang complexity

### Lien ket cac Lab:
- Lab 01: Xac dinh ASRs
- Lab 02: ASR-3 → Layered Architecture
- Lab 04: ASR-1 → Microservices
- Lab 08: ATAM danh gia → Microservices XUNG DANG cho e-commerce

---

*ASR la "kim chi nam" cua toan bo kien truc. Moi quyet dinh thiet ke deu bat nguon tu ASR.*
