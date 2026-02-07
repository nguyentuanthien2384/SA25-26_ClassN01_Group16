# LAB 08: PRESENTATION GUIDE
## Deployment View & ATAM Analysis - Electronics E-Commerce Website

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
| 1 | Gioi thieu Lab 08 & muc tieu | 1.5 phut | Thien |
| 2 | Deployment View - UML Deployment Diagram | 4 phut | Thien |
| 3 | Node Descriptions & Communication Paths | 3 phut | Thien |
| 4 | ATAM - Dinh nghia Scenarios (SS1, AS1) | 3 phut | Viet Anh |
| 5 | ATAM - Danh gia kien truc & Trade-offs | 3 phut | Viet Anh |
| 6 | So sanh Monolithic vs Microservices | 2 phut | Viet Anh |
| 7 | Demo docker-compose & thuc te | 2 phut | Thien |
| 8 | Ket luan | 1 phut | Ca hai |

---

## 2. NOI DUNG CHI TIET TUNG PHAN

---

### PHAN 1: GIOI THIEU LAB 08 (1.5 phut)

**Cach noi:**

> "Xin chao thay va cac ban. Nhom 16 trinh bay Lab 08 - **Deployment View va ATAM Analysis**.
>
> Cac Lab truoc chung em da lam:
> - Lab 01: Xac dinh **ASRs** (Scalability, Security, Modifiability)
> - Lab 02: Thiet ke **Layered Architecture** (va nhan ra yeu Scalability)
> - Lab 04: **Tach microservices** theo Business Capability, dinh nghia Service Contracts
>
> Lab 08 la buoc cuoi cung: **TRIEN KHAI va DANH GIA**.
> 1. **Deployment View**: He thong duoc trien khai VAt LY nhu the nao? Phan mem nam o may nao? Giao tiep bang giao thuc gi?
> 2. **ATAM Analysis**: Kien truc da chon co THUC SU thoa man ASRs khong? Co trade-off gi?
>
> Diem dac biet: Toan bo kien truc microservices da duoc cau hinh trong `docker-compose.microservices.yml` voi **17+ containers** - co the chay duoc ngay."

---

### PHAN 2: DEPLOYMENT VIEW - UML DEPLOYMENT DIAGRAM (4 phut) - TRONG TAM

**Cach noi:**

> "UML Deployment Diagram tra loi cau hoi: **'Phan mem nam o DAU trong moi truong vat ly?'** No cho thay cac **node** (may chu, container), cac **artifact** (phan mem duoc deploy), va cac **duong giao tiep** (protocol) giua chung."

#### So do Deployment (VE LEN BANG):

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          DEPLOYMENT VIEW                                 │
└─────────────────────────────────────────────────────────────────────────┘

 ┌──────────────┐
 │ CLIENT DEVICE│    Web Browser / Mobile App
 │              │
 └──────┬───────┘
        │ HTTPS
        ▼
 ┌──────────────────┐
 │ LOAD BALANCER    │    Nginx / Cloud LB (production)
 │ (Logical Node)   │    Phan phoi tai giua cac instance
 └──────┬───────────┘
        │ HTTP/HTTPS
        ▼
 ┌──────────────────────────────────────────────────────────────────┐
 │ API GATEWAY NODE                                                 │
 │                                                                  │
 │  ┌─────────────────────┐    ┌──────────────────┐               │
 │  │ Kong Proxy          │    │ Kong Admin API   │               │
 │  │ :9000 (HTTP)        │    │ :9001            │               │
 │  │ :9443 (HTTPS)       │    │ :9444 (HTTPS)    │               │
 │  └─────────────────────┘    └──────────────────┘               │
 │                                                                  │
 │  ┌─────────────────────┐                                        │
 │  │ PostgreSQL (Kong DB)│  Luu cau hinh routing cua Kong        │
 │  └─────────────────────┘                                        │
 └──────┬───────────────────────────────────────────────────────────┘
        │ HTTP/REST (routing theo path)
        ▼
 ┌──────────────────────────────────────────────────────────────────┐
 │ APPLICATION CLUSTER (Microservices)                              │
 │                                                                  │
 │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
 │  │ Catalog Svc  │  │ Order Svc    │  │ User Svc     │          │
 │  │ :9005        │  │ :9002        │  │ :9003        │          │
 │  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘          │
 │         │                 │                  │                   │
 │  ┌──────────────┐                                               │
 │  │ Notification │  (Chay doc lap, doc tu RabbitMQ)              │
 │  │ Service :9004│                                               │
 │  └──────────────┘                                               │
 └──────┬──────────────┬────────────────┬──────────────────────────┘
        │              │                │
        ▼              ▼                ▼
 ┌─────────────┐ ┌────────────┐ ┌─────────────────────────────────┐
 │ MESSAGE     │ │ CACHE      │ │ DATABASES (DB-per-Service)      │
 │ BROKER      │ │            │ │                                  │
 │ RabbitMQ    │ │ Redis      │ │ ┌─────────┐┌─────────┐┌───────┐│
 │ :5672 AMQP  │ │ :6381      │ │ │MySQL    ││MySQL    ││MySQL  ││
 │ :15672 UI   │ │            │ │ │Catalog  ││Order    ││User   ││
 └─────────────┘ └────────────┘ │ │:3310    ││:3311    ││:3312  ││
                                │ └─────────┘└─────────┘└───────┘│
                                └─────────────────────────────────┘

 ┌──────────────────────────────────────────────────────────────────┐
 │ INFRASTRUCTURE (Monitoring & Observability)                      │
 │                                                                  │
 │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
 │  │ Consul   │  │ Jaeger   │  │Prometheus│  │ Grafana  │       │
 │  │ :8500    │  │ :16686   │  │ :9090    │  │ :3000    │       │
 │  │ Discovery│  │ Tracing  │  │ Metrics  │  │Dashboard │       │
 │  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
 └──────────────────────────────────────────────────────────────────┘
```

**Cach noi khi trinh bay diagram:**

> "Deployment Diagram cua chung em gom **6 tang vat ly** tu tren xuong duoi:
>
> **Tang 1 - Client Device:** Browser cua khach hang, gui request HTTPS.
>
> **Tang 2 - Load Balancer:** Trong production dung Nginx hoac Cloud LB de phan phoi tai. Trong local development, Kong xu ly truc tiep.
>
> **Tang 3 - API Gateway (Kong):** Diem vao DUY NHAT. Moi request phai di qua day. Kong xu ly routing (`/api/products/*` → Catalog Service), rate limiting, authentication.
>
> **Tang 4 - Application Cluster:** 4 microservices chay doc lap, moi service co port rieng. **Notification Service** dac biet vi no KHONG nhan request HTTP tu Gateway - no doc event tu RabbitMQ.
>
> **Tang 5 - Data Tier:** Gom 3 thanh phan:
> - **RabbitMQ** (Message Broker): Async messaging giua services
> - **Redis** (Cache): Giam tai cho database
> - **3 MySQL** (Database per Service): Catalog DB, Order DB, User DB - hoan toan tach biet
>
> **Tang 6 - Infrastructure:** Consul (Service Discovery), Jaeger (Distributed Tracing), Prometheus + Grafana (Monitoring).
>
> **Diem quan trong:** Tat ca deu la Docker containers, co the deploy tren bat ky moi truong nao (local, AWS, GCP, Azure)."

---

### PHAN 3: NODE DESCRIPTIONS & COMMUNICATION PATHS (3 phut)

#### Bang Node Descriptions:

| Node | Artifact | Chuc nang |
|------|----------|-----------|
| **Client Device** | Web Browser / Mobile App | Gui HTTP/HTTPS request |
| **Load Balancer** | Nginx / Cloud LB | Phan phoi tai, dam bao Availability |
| **API Gateway** | Kong Proxy (:9000), Kong Admin (:9001) | Routing, rate limiting, auth |
| **Kong DB** | PostgreSQL | Luu cau hinh Kong |
| **Application** | Catalog (:9005), Order (:9002), User (:9003), Notification (:9004) | 4 microservices doc lap |
| **Message Broker** | RabbitMQ (:5672, :15672) | Async event-driven messaging |
| **Cache** | Redis (:6381) | Caching, giam tai DB |
| **Databases** | MySQL Catalog (:3310), Order (:3311), User (:3312) | Database per Service |

#### Bang Communication Paths:

| Tu → Den | Protocol | Muc dich |
|----------|----------|----------|
| Client → Load Balancer | **HTTPS** | Request cua user (duyet SP, checkout) |
| Load Balancer → API Gateway | **HTTP/HTTPS** | Routing va ap dung gateway policies |
| API Gateway → Services | **HTTP/REST** | Sync: doc san pham, xac thuc, dat hang |
| Order Service → RabbitMQ | **AMQP** | Publish event `OrderPlaced` |
| RabbitMQ → Notification Svc | **AMQP** | Async: gui email ma khong chan checkout |
| Services → Redis | **TCP (Redis)** | Caching, giam tai DB |
| Services → MySQL | **MySQL protocol** | Doc/ghi du lieu cua rieng service |

**Cach noi:**

> "**Communication Paths** cho thay he thong dung **2 loai giao tiep**:
>
> **Synchronous (HTTP/REST):** Client → Kong → Services. Dung cho cac thao tac can phan hoi ngay: duyet san pham, dang nhap, dat hang.
>
> **Asynchronous (AMQP):** Order Service → RabbitMQ → Notification Service. Dung cho gui email - KHONG CHAN viec dat don hang. Day la **Fault Isolation** thuc te.
>
> **Diem dang chu y:** Moi service chi giao tiep voi database cua RIENG NO. Catalog Service chi truy van `mysql-catalog` (:3310), KHONG BAO GIO truy van `mysql-order` (:3311). Day la **Database per Service pattern**."

---

### PHAN 4: ATAM - DINH NGHIA SCENARIOS (3 phut)

**Cach noi:**

> "ATAM - Architecture Trade-off Analysis Method - la phuong phap danh gia kien truc dua tren **scenarios cu the**. Chung em tap trung vao 2 quality attributes quan trong nhat: **Scalability** va **Availability**."

#### Scenario SS1: Scalability (Flash Sale)

```
┌──────────────────────────────────────────────────────────┐
│              SCENARIO SS1: SCALABILITY                    │
├──────────────┬───────────────────────────────────────────┤
│ Source       │ 10,000 nguoi dung dong thoi               │
│ Stimulus     │ Tim kiem san pham + Dat don hang cung luc │
│ Artifact     │ API Gateway, Catalog Svc, Order Svc,     │
│              │ Redis Cache, Service Databases             │
│ Environment  │ Flash Sale / Black Friday                  │
│ Response     │ Do tre p95 < 2 giay                       │
│ Measure      │ He thong van hoat dong binh thuong        │
└──────────────┴───────────────────────────────────────────┘
```

**Cach noi:**

> "Scenario SS1: Trong dot **Flash Sale**, 10,000 nguoi dung DONG THOI tim kiem san pham va dat hang. He thong phai giu **p95 response time duoi 2 giay**.
>
> **Voi Monolith:** Phai scale TOAN BO ung dung du chi Catalog va Order bi tai cao. Database dung chung tro thanh bottleneck.
>
> **Voi Microservices:** Scale RIENG Catalog Service (them instance cho doc san pham) va Order Service (them instance cho dat hang). Redis cache giam tai cho database. Kong Gateway va Load Balancer phan phoi tai giua cac instance."

#### Scenario AS1: Availability (Service Failure)

```
┌──────────────────────────────────────────────────────────┐
│              SCENARIO AS1: AVAILABILITY                   │
├──────────────┬───────────────────────────────────────────┤
│ Source       │ Loi deploy / Notification Service down     │
│ Stimulus     │ Khach hang dat don hang khi Notif Svc loi │
│ Artifact     │ Order Service, RabbitMQ, Notification Svc │
│ Environment  │ Hoat dong binh thuong, 1 service bi loi   │
│              │ trong 1 gio                                │
│ Response     │ Don hang VAN thanh cong                    │
│              │ Email gui lai sau khi service phuc hoi    │
│ Measure      │ 0 don hang bi mat, email gui 100% sau    │
│              │ recovery                                   │
└──────────────┴───────────────────────────────────────────┘
```

**Cach noi:**

> "Scenario AS1: **Notification Service bi sap** hoan toan trong 1 gio. Khach hang van dat don hang duoc khong?
>
> **Voi Monolith:** Neu gui email dong bo ben trong cung process, loi notification co the lam GIAM hieu nang hoac CHAN viec checkout.
>
> **Voi Microservices:** Order Service dat hang thanh cong → publish event vao **RabbitMQ** → tra ve 200 OK cho khach hang NGAY. RabbitMQ **LUU GIU** message (buffer). Khi Notification Service phuc hoi → no doc tat ca message tu queue va gui email. **KHONG MAT BAT KY event nao.**
>
> Day la Fault Isolation thuc te: 1 service loi KHONG keo sap toan bo he thong."

---

### PHAN 5: ATAM - DANH GIA & TRADE-OFFS (3 phut)

#### Bang danh gia kien truc:

| Scenario | Monolithic (Layered) | Microservices | Ket luan |
|----------|---------------------|---------------|----------|
| **SS1 Scalability** | Scale toan bo app. DB chung la bottleneck. Vertical scaling co gioi han. | Scale tung service doc lap. Cache giam tai DB. Load Balancer + Gateway phan phoi tai. | **Microservices THANG** |
| **AS1 Availability** | Loi notification co the chan checkout. Khong co fault isolation. | Order tach khoi Notification qua RabbitMQ. Message duoc buffer, gui lai sau. | **Microservices THANG** |

#### Trade-off Statement:

**Cach noi:**

> "**Trade-off chinh:** Microservices cai thien Scalability va Availability, nhung **DANH DOI** bang **do phuc tap van hanh cao hon**.
>
> So voi Monolith, ElectroShop can them:
> - **API Gateway** (Kong + PostgreSQL) - them 2 containers
> - **3 database rieng** thay vi 1 - them complexity cho data consistency
> - **Message Broker** (RabbitMQ) - them infrastructure de quan ly
> - **Monitoring stack** (Prometheus, Grafana, Jaeger, Consul) - can thiet de theo doi 4+ services
>
> **Tong cong:** Tu 3-5 containers (monolith) len **17+ containers** (microservices).
>
> **Nhung tai sao van chon Microservices?** Vi doi voi **e-commerce**:
> - Flash Sale co the tang traffic **10 lan** → can scale doc lap
> - Gui email cham 5 giay → KHONG DUOC chan checkout → can Fault Isolation
> - Team lon co the phat trien song song → moi team 1 service
>
> Do phuc tap van hanh duoc giai quyet bang **Docker Compose** (trien khai 1 lenh) va **Monitoring** (Prometheus + Grafana theo doi tu dong)."

---

### PHAN 6: SO SANH MONOLITHIC VS MICROSERVICES (2 phut)

**Cach noi:**

> "Tong hop so sanh 2 kien truc:"

| Tieu chi | Monolithic (Layered) | Microservices |
|----------|---------------------|---------------|
| **Deploy** | 1 unit duy nhat | Nhieu unit doc lap |
| **Database** | 1 database chung | Database per Service (3 MySQL) |
| **Scale** | Scale toan bo | Scale tung service |
| **Fault Isolation** | Loi 1 module → anh huong all | Loi 1 service → service khac van chay |
| **Complexity** | **Don gian** | Phuc tap hon (gateway, broker, monitoring) |
| **Team** | Phu hop team nho | Phu hop team lon, phat trien song song |
| **Phu hop khi** | Prototype, MVP, du an nho | E-commerce, du an can scale, team lon |

**Noi:**

> "Khong co kien truc nao **tot hon** tuyet doi. Monolith don gian hon va phu hop cho du an nho. Microservices phuc tap hon nhung can thiet khi he thong can **scale** va **fault isolation**.
>
> Doi voi ElectroShop - mot **e-commerce platform** se co Flash Sale, nhieu phuong thuc thanh toan, va can gui thong bao khong chan checkout - **Microservices la lua chon phu hop**. Va do phuc tap them duoc giai quyet bang Docker va Monitoring."

---

### PHAN 7: DEMO DOCKER-COMPOSE (2 phut)

**Cach noi:**

> "Tat ca kien truc vua trinh bay khong chi la ly thuyet. Chung em da cau hinh san toan bo trong `docker-compose.microservices.yml`."

#### Mo file va chi ra cac service:

```
docker-compose.microservices.yml - 17+ services:

API GATEWAY:
  kong              → :9000 (proxy), :9001 (admin)
  kong-database     → PostgreSQL cho Kong
  konga             → :1337 (Kong GUI)

MESSAGE BROKER:
  rabbitmq          → :5672 (AMQP), :15672 (Management UI)

SERVICE DISCOVERY:
  consul            → :8500 (UI + API)

TRACING:
  jaeger            → :16686 (UI)

DATABASES (per service):
  mysql-catalog     → :3310
  mysql-order       → :3311
  mysql-user        → :3312

CACHE:
  redis             → :6381

MONITORING:
  prometheus        → :9090
  grafana           → :3000 (dashboards)

MICROSERVICES:
  catalog-service   → :9005
  order-service     → :9002
  user-service      → :9003
  notification-service → :9004

TOOLS:
  mailhog           → :8025 (email testing UI)
  phpmyadmin        → :9083
  redis-commander   → :9082
```

**Noi:**

> "Chi voi **1 lenh** `docker-compose -f docker-compose.microservices.yml up -d`, toan bo 17+ services khoi dong. Moi service co health check rieng, restart policy, va network rieng.
>
> **Traceability tu bao cao den code:**
> - Diagram noi 'API Gateway Kong' → `docker-compose` co service `kong` port 9000
> - Diagram noi 'Database per Service' → co `mysql-catalog`, `mysql-order`, `mysql-user`
> - Diagram noi 'RabbitMQ' → co service `rabbitmq` port 5672
> - Diagram noi 'Monitoring' → co `prometheus` + `grafana`
>
> Moi thu trong Deployment Diagram deu **TRUY VET DUOC** den 1 container thuc te."

#### Demo PlantUML Diagram:

> "Deployment Diagram cung da duoc code bang PlantUML tai `Design/deployment-diagram.puml` - co the render tu dong va version control bang Git."

---

### PHAN 8: KET LUAN (1 phut)

**Cach noi:**

> "Lab 08 da hoan thanh 4 muc tieu:
>
> 1. **UML Deployment Diagram**: Mo hinh hoa 6 tang vat ly tu Client den Infrastructure, 17+ containers
> 2. **ATAM Analysis**: 2 scenarios (SS1 Scalability, AS1 Availability) danh gia ca Monolith va Microservices
> 3. **Trade-offs**: Microservices tot hon cho Scalability va Availability, nhung danh doi bang do phuc tap van hanh
> 4. **So sanh**: Monolith don gian, phu hop du an nho. Microservices phu hop e-commerce can scale.
>
> **Bai hoc quan trong nhat tu Lab 08:**
> - **Deployment View** la cau noi giua *thiet ke* va *thuc te* - no cho thay phan mem chay O DAU
> - **ATAM** giup danh gia kien truc MOT CACH KHACH QUAN bang scenarios cu the, khong chi cam tinh
> - **Trade-off la khong the tranh khoi** - quan trong la biet trade-off cua minh va giai thich TAI SAO chap nhan no
>
> Cam on thay va cac ban."

---

## 3. DEMO CODE THUC TE

### Demo 1: docker-compose.microservices.yml

Mo file va chi ra:
- **17+ services** voi port mapping cu the
- **depends_on** voi `condition: service_healthy` → dam bao thu tu khoi dong
- **healthcheck** cho moi service → dam bao chi chay khi san sang
- **networks: ms_network** → tat ca service cung 1 mang noi bo

**Noi:** "Day la Deployment Diagram duoi dang CODE - moi node trong diagram tuong ung voi 1 service trong file nay."

### Demo 2: Database per Service (3 MySQL containers)

```yaml
# Catalog Service Database
mysql-catalog:
  container_name: mysql_catalog
  environment:
    MYSQL_DATABASE: catalog_db      # Database rieng cho Catalog
  ports:
    - "3310:3306"                    # Port rieng

# Order Service Database  
mysql-order:
  container_name: mysql_order
  environment:
    MYSQL_DATABASE: order_db         # Database rieng cho Order
  ports:
    - "3311:3306"                    # Port khac

# User Service Database
mysql-user:
  container_name: mysql_user
  environment:
    MYSQL_DATABASE: user_db          # Database rieng cho User
  ports:
    - "3312:3306"                    # Port khac nua
```

**Noi:** "3 MySQL container hoan toan doc lap. Catalog Service chi biet `mysql-catalog`, KHONG BIET `mysql-order` ton tai. Day la **Data Isolation** thuc te."

### Demo 3: Notification Service - Microservice doc lap

```
notification-service/
├── consumer.php          ← Doc Redis queue, gui email
├── Dockerfile            ← Build image rieng
├── bootstrap.php         ← Config rieng
├── composer.json         ← Dependencies rieng
└── src/
    ├── RedisConsumer.php ← Logic doc queue
    └── EmailSender.php   ← Logic gui email
```

**Noi:** "Notification Service co Dockerfile rieng, codebase rieng, deploy RIENG. Day la microservice thuc su - khong phai chi la 1 module trong monolith."

### Demo 4: Monitoring Stack

- **Prometheus** (`docker/prometheus/prometheus.yml`): Scrape metrics tu catalog-service, order-service
- **Grafana** (`docker/grafana/`): Dashboard va datasource config

**Noi:** "Monitoring la BAt BUOC cho microservices. Prometheus thu thap metrics, Grafana hien thi dashboard. Khi 1 service cham, chung em thay NGAY tren dashboard."

### Demo 5: PlantUML Deployment Diagram

Mo file `Design/deployment-diagram.puml` va render (Alt+D).

**Noi:** "Diagram duoc code bang PlantUML - co the version control, tu dong render, va luon dong bo voi thuc te."

---

## 4. CAU HOI THUONG GAP VA CACH TRA LOI

### Q1: "Deployment Diagram khac gi voi Component Diagram?"

**Tra loi:**
> "**Component Diagram** (Lab 02, 04) cho thay **cau truc logic** - cac class, service, va moi quan he giua chung. No tra loi 'Phan mem duoc TO CHUC nhu the nao?'
>
> **Deployment Diagram** (Lab 08) cho thay **cau truc vat ly** - phan mem chay o **may nao**, giao tiep bang **giao thuc gi**, qua **port nao**. No tra loi 'Phan mem duoc TRIEN KHAI nhu the nao?'
>
> Vi du: Component Diagram chi 'ProductService goi ProductRepository'. Deployment Diagram chi 'Catalog Service chay tren container port 9005, giao tiep voi MySQL Catalog qua port 3310 bang MySQL protocol.'"

### Q2: "ATAM la gi? Tai sao can?"

**Tra loi:**
> "ATAM la **Architecture Trade-off Analysis Method** - phuong phap danh gia kien truc bang cach:
> 1. **Dinh nghia Scenarios** cu the (khong mo ho)
> 2. **Danh gia** kien truc doi voi tung scenario
> 3. **Xac dinh Trade-offs** - duoc gi, mat gi
>
> **Tai sao can?** Vi khong co kien truc nao hoan hao. ATAM giup ban:
> - Biet **diem manh** (Microservices tot cho Scalability)
> - Biet **diem yeu** (Microservices phuc tap hon)
> - Ra quyet dinh **CO CO SO** thay vi cam tinh
>
> Khong co ATAM, ban co the chon kien truc khong phu hop ma khong biet."

### Q3: "Tai sao can Load Balancer truoc API Gateway?"

**Tra loi:**
> "**API Gateway (Kong)** xu ly routing, rate limiting, auth. Nhung neu chi co **1 instance Kong** va no bi qua tai hoac loi → toan bo he thong khong truy cap duoc.
>
> **Load Balancer** dat truoc Gateway de:
> 1. Phan phoi tai giua **nhieu instance Kong** (horizontal scaling)
> 2. Neu 1 Kong instance loi → LB tu dong chuyen traffic sang instance khac
> 3. Dam bao **High Availability** cho diem vao he thong
>
> Trong local dev, chua can LB vi chi co 1 instance. Trong production, day la **bat buoc**."

### Q4: "RabbitMQ khac gi Redis Queue?"

**Tra loi:**
> "Trong du an chung em co ca 2:
> - **Redis Queue**: Dung trong monolith (docker-compose.yml), don gian, phu hop cho job queue
> - **RabbitMQ**: Dung trong microservices (docker-compose.microservices.yml), co nhieu tinh nang hon
>
> RabbitMQ uu viet hon vi:
> 1. **Message Acknowledgment**: Consumer xac nhan da xu ly xong, neu chua → message duoc gui lai
> 2. **Routing**: Co the gui message den dung queue dua tren routing key
> 3. **Persistence**: Message luu tren disk, khong mat khi restart
> 4. **Management UI**: Theo doi queue, message rate qua port :15672
>
> Cho microservices, RabbitMQ la lua chon tot hon Redis Queue."

### Q5: "Database per Service co van de gi voi data consistency?"

**Tra loi:**
> "Day la trade-off lon nhat cua Microservices. Khi 3 service co 3 database rieng:
> - **KHONG THE** dung database transaction giua 2 service
> - Neu Order Service tao don hang nhung Payment Service that bai → can co che **compensating transaction**
>
> Chung em giai quyet bang:
> 1. **Saga Pattern**: Chuoi cac buoc voi compensating action (da implement trong `app/Services/Saga/OrderSaga.php`)
> 2. **Outbox Pattern**: Dam bao event khong bi mat (da implement trong `app/Models/Models/OutboxMessage.php`)
> 3. **Eventual Consistency**: Chap nhan du lieu co the khong dong bo 100% tai moi thoi diem, nhung se CUOI CUNG dong bo
>
> Day la trade-off: Mat **Strong Consistency**, duoc **Scalability** va **Availability**."

### Q6: "Scenario Template co nhung thanh phan gi?"

**Tra loi:**
> "Scenario Template gom 6 thanh phan (theo Lecture 08):
>
> | Thanh phan | Y nghia | Vi du SS1 |
> |---|---|---|
> | **Source** | Ai/cai gi tao ra stimulus | 10,000 nguoi dung dong thoi |
> | **Stimulus** | Su kien xay ra | Tim kiem san pham + dat don hang |
> | **Artifact** | Thanh phan bi anh huong | API Gateway, Catalog Svc, Order Svc |
> | **Environment** | Dieu kien khi xay ra | Flash Sale / Black Friday |
> | **Response** | He thong phan ung the nao | Do tre p95 < 2 giay |
> | **Measure** | Cach do luong | He thong van hoat dong binh thuong |
>
> Template nay giup dinh nghia scenario **KHONG MO HO** - co so cu the de danh gia."

### Q7: "Microservices co 17+ containers. Quan ly the nao?"

**Tra loi:**
> "Chung em su dung:
> 1. **Docker Compose**: 1 file YAML cau hinh tat ca 17+ services. Khoi dong bang 1 lenh: `docker-compose up -d`
> 2. **Health Checks**: Moi service co healthcheck rieng. Docker tu dong restart neu service loi.
> 3. **Prometheus + Grafana**: Thu thap metrics va hien thi dashboard. Biet ngay service nao cham.
> 4. **Jaeger**: Distributed Tracing - theo doi 1 request di qua nhung service nao, mat bao lau.
> 5. **Consul**: Service Discovery - services tu dong dang ky va tim nhau.
>
> Trong production se dung **Kubernetes** thay Docker Compose de auto-scaling va self-healing."

---

## 5. TIPS GHI DIEM

### DO - Nen lam:

1. **Ve Deployment Diagram len bang** truoc khi noi - 6 tang tu Client den Infrastructure
2. **Viet Scenario Template ra bang** - SS1 va AS1 voi 6 thanh phan day du
3. **Chi vao docker-compose** khi noi ve tung node - chung minh TRACEABILITY
4. **Nhan manh Trade-off** - "Microservices tot hon nhung PHUC TAP hon" → cho thay hieu biet sau
5. **Dung tu "Fault Isolation"** khi noi ve AS1 - day la keyword thay muon nghe
6. **So sanh 2 kien truc** mot cach CONG BANG - khong noi monolith xau, chi noi khong phu hop cho e-commerce

### DON'T - Khong nen lam:

1. **Khong noi "Microservices tot hon Monolith"** tuyet doi - phai noi **"phu hop hon cho use case nay"**
2. **Khong bo qua Communication Paths** - giao thuc giua cac node la phan QUAN TRONG cua Deployment Diagram
3. **Khong quen lien ket voi Lab truoc** - ASR-1 Scalability (Lab 01) → Microservices (Lab 04) → Deploy (Lab 08)
4. **Khong trinh bay Deployment nhu Component** - Deployment la VAt LY (may chu, port, protocol), khong phai LOGIC (class, method)

### Cau mo dau gay an tuong:

> "Lab 01 xac dinh yeu cau. Lab 02 thiet ke logic. Lab 04 tach microservices. Lab 08 la buoc cuoi: LAM CHO NO CHAY THUC TE. Deployment Diagram cho thay 17+ containers giao tiep voi nhau nhu the nao, va ATAM chung minh kien truc nay DUNG cho e-commerce."

### Cau ket thuc gay an tuong:

> "Tu 3 ASRs trong Lab 01 den 17+ containers trong Lab 08 - moi quyet dinh kien truc deu co ly do va chung minh duoc. Microservices khong hoan hao - no phuc tap hon monolith. Nhung doi voi ElectroShop, trade-off do la XUng DANG vi chung em duoc Scalability, Availability, va Fault Isolation ma e-commerce KHONG THE THIEU."

---

## 6. PHIEU GHI NHO

```
+------------------------------------------------+
|           PHIEU GHI NHO - LAB 08               |
+------------------------------------------------+
|                                                |
| DEPLOYMENT VIEW:                               |
| 6 tang: Client → LB → Kong Gateway →          |
| Services → Data Tier → Infrastructure          |
|                                                |
| 17+ containers trong docker-compose            |
| 3 MySQL rieng (Catalog, Order, User)           |
| RabbitMQ cho async messaging                   |
|                                                |
| ATAM - 2 SCENARIOS:                            |
| SS1 Scalability: 10K users, Flash Sale         |
|   → Microservices: scale rieng tung service    |
|   → Monolith: scale toan bo (YEU)             |
|                                                |
| AS1 Availability: Notif Service down 1h        |
|   → Microservices: RabbitMQ buffer, 0 mat      |
|   → Monolith: co the chan checkout (YEU)       |
|                                                |
| TRADE-OFF:                                     |
| Duoc: Scalability + Availability               |
| Mat: Do phuc tap van hanh (17+ containers)     |
| Giai phap: Docker Compose + Monitoring         |
|                                                |
| KEY MESSAGE:                                   |
| "Trade-offs are inevitable -                   |
|  know yours and justify them"                  |
+------------------------------------------------+
```

---

## 7. CHIA PHAN TRINH BAY

### Nguyen Tuan Thien (23010571):

| Phan | Noi dung | Thoi gian |
|------|----------|-----------|
| Mo dau | Gioi thieu Lab 08, lien ket cac Lab truoc | 1.5 phut |
| Deployment Diagram | Ve va giai thich 6 tang, cac node | 4 phut |
| Communication Paths | Bang giao thuc giua cac node | 3 phut |
| Demo | Mo docker-compose, chi services + ports | 2 phut |

### Dang Viet Anh (23010689):

| Phan | Noi dung | Thoi gian |
|------|----------|-----------|
| ATAM Scenarios | SS1 (Scalability) + AS1 (Availability) voi template | 3 phut |
| Danh gia & Trade-offs | Bang so sanh Mono vs Micro, trade-off statement | 3 phut |
| So sanh kien truc | Bang tong hop 7 tieu chi | 2 phut |
| Ket luan | Tong ket 4 muc tieu, bai hoc | 1 phut |

---

## 8. SO SANH BAO CAO VS YEU CAU LAB

| Yeu cau Lab 08 | Trong bao cao .docx | Danh gia |
|----------------|---------------------|----------|
| UML Deployment Diagram | Figure 1: Day du nodes, artifacts, paths | DAT |
| Node Descriptions | Table: 8 nodes voi artifacts va rationale | DAT |
| Communication Paths | Table: 7 paths voi protocol va purpose | DAT |
| ATAM - Define Scenarios | SS1 (Scalability) + AS1 (Availability) | DAT |
| ATAM - Evaluate Architectures | Table so sanh Mono vs Micro cho 2 scenarios | DAT |
| ATAM - Identify Trade-offs | Trade-off statement ro rang | DAT |
| Comparison Mono vs Micro | Section 4: So sanh day du | DAT |
| Scenario Template (Appendix) | Table 6 thanh phan cho SS1 va AS1 | DAT (BONUS) |
| Code-to-Deployment Mapping | Table 10 services voi ports | DAT (BONUS) |

**Ket luan: Bao cao day du 100% yeu cau. Phan Code-to-Deployment Mapping va Scenario Template la DIEM CONG.**

---

## 9. TONG KET HANH TRINH 4 LAB

| Lab | Noi dung | Ket qua chinh |
|-----|----------|---------------|
| **Lab 01** | Requirements & ASRs | 3 ASRs: Scalability, Security, Modifiability |
| **Lab 02** | Layered Architecture | 4 tang, Strict Downward Dependency. ASR-1 **YEU** |
| **Lab 04** | Microservices Decomposition | 9 services, Service Contracts, Sync/Async, C4 Model |
| **Lab 08** | Deployment & ATAM | 17+ containers, SS1+AS1 → Microservices THANG |

**Noi trong phan ket:**

> "4 Lab tao thanh 1 hanh trinh hoan chinh:
> - Lab 01 hoi **'Can gi?'** → ASRs
> - Lab 02 hoi **'To chuc the nao?'** → Layered Architecture (chua du)
> - Lab 04 hoi **'Tach the nao?'** → Microservices
> - Lab 08 hoi **'Chay the nao? Co tot khong?'** → Deployment + ATAM
>
> Moi Lab xay dung tren Lab truoc, va tat ca deu co **SOURCE CODE THUC TE** de chung minh."

---

*File nay duoc tao de ho tro thuyet trinh Lab 08. Chuc nhom 16 thuyet trinh thanh cong!*
