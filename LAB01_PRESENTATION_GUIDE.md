# LAB 01: PRESENTATION GUIDE
## Requirements Elicitation & Modeling - Electronics E-Commerce Website

**Course:** Software Architecture-1-2-25 (N01)  
**Group:** 16  
**Members:** Nguyen Tuan Thien - 23010571 | Dang Viet Anh - 23010689  
**Instructor:** M.Sc. Vu Quang Dung

---

## MUC LUC

1. [Kich ban thuyet trinh (Script)](#1-kich-ban-thuyet-trinh)
2. [Noi dung chi tiet tung phan](#2-noi-dung-chi-tiet)
3. [Demo code thuc te](#3-demo-code-thuc-te)
4. [Cau hoi thuong gap va cach tra loi](#4-cau-hoi-thuong-gap)
5. [Tips ghi diem](#5-tips-ghi-diem)

---

## 1. KICH BAN THUYET TRINH (15-20 phut)

### Phan bo thoi gian khuyen nghi

| Phan | Noi dung | Thoi gian | Nguoi trinh bay |
|------|----------|-----------|-----------------|
| 1 | Gioi thieu du an | 2 phut | Thien |
| 2 | Actors & Functional Requirements | 3 phut | Thien |
| 3 | Non-Functional Requirements | 2 phut | Viet Anh |
| 4 | ASR Cards (trong tam) | 4 phut | Viet Anh |
| 5 | Use Case Diagram | 3 phut | Thien |
| 6 | Critical Path: Checkout | 3 phut | Viet Anh |
| 7 | Ket luan & Lien ket Lab 2 | 1 phut | Ca hai |
| 8 | Q&A | 2-3 phut | Ca hai |

---

## 2. NOI DUNG CHI TIET TUNG PHAN

---

### PHAN 1: GIOI THIEU DU AN (2 phut)

**Mo dau (noi gi):**

> "Xin chao thay va cac ban. Nhom 16 chung em se trinh bay Lab 01 - Requirements Elicitation & Modeling cho du an **ElectroShop - Website Ban Do Dien Tu**.
>
> Du an nay la mot nen tang thuong mai dien tu chuyen ban cac san pham dien tu nhu laptop, dien thoai, phu kien... Du an duoc xay dung bang **Laravel Framework** voi kien truc **Modular Monolith** huong toi **Microservices**.
>
> Trong Lab 01, chung em tap trung vao 2 hoat dong chinh:
> 1. **Requirements Elicitation** - Thu thap va phan loai yeu cau
> 2. **Use Case Modeling** - Mo hinh hoa hanh vi he thong bang UML Use Case Diagram"

**Diem nhan:**
- Nhac du an la **THUC TE**, co source code chay duoc (khong phai chi la ly thuyet)
- Du an co **8 module thuc te**: Admin, Catalog, Customer, Payment, Cart, Review, Content, Support
- Da deploy duoc bang **Docker** voi day du ha tang microservices

---

### PHAN 2: ACTORS & FUNCTIONAL REQUIREMENTS (3 phut)

**Noi dung trinh bay:**

#### 2.1 Actors (3 actors chinh)

| Actor | Mo ta | Tuong tac |
|-------|-------|-----------|
| **Web Customer** | Nguoi dung cuoi - Khach hang | Duyet san pham, dang ky/dang nhap, them vao gio hang, thanh toan, danh gia |
| **Administrator** | Nguoi quan ly cua hang | Quan ly san pham, danh muc, don hang, bai viet, tai khoan |
| **Payment Gateway** | He thong thanh toan ben ngoai (VNPay, MoMo) | Xu ly giao dich, xac minh thong tin, tra ve trang thai |

**Cach noi:**

> "He thong cua chung em xac dinh 3 actor chinh:
> - **Web Customer** la khach hang truy cap website de mua hang
> - **Administrator** la nguoi quan ly cua hang, co quyen quan tri toan bo he thong
> - **Payment Gateway** la he thong ben ngoai nhu VNPay, MoMo de xu ly thanh toan
>
> Diem dac biet la chung em co tich hop **3 cong thanh toan thuc te**: MoMo, VNPay va PayPal - khong chi la ly thuyet ma da co code xu ly callback thuc te."

#### 2.2 Functional Requirements (10 FRs)

| ID | Mo ta | Do uu tien | Module tuong ung |
|----|-------|------------|------------------|
| FR-01 | Duyet va tim kiem san pham theo tu khoa, danh muc | High | Modules/Catalog |
| FR-02 | Dang ky tai khoan va dang nhap bao mat | High | Modules/Customer |
| FR-03 | Them san pham vao gio hang, quan ly so luong | High | Modules/Cart |
| FR-04 | Checkout: nhap dia chi, thanh toan (Make Purchase) | **Critical** | Modules/Cart + Payment |
| FR-05 | Gui danh gia, xep hang san pham | Medium | Modules/Review |
| FR-06 | Admin CRUD danh muc san pham | High | Modules/Admin |
| FR-07 | Admin quan ly kho hang (them, sua, xoa san pham) | High | Modules/Admin |
| FR-08 | Admin xem va cap nhat trang thai don hang | High | Modules/Admin |
| FR-09 | Gui xac nhan don hang khi mua thanh cong | Medium | Event System |
| FR-10 | Admin quan ly bai viet/tin tuc ve dien tu | Low | Modules/Content |

**Cach noi:**

> "Chung em da xac dinh 10 Functional Requirements, phan loai theo do uu tien.
> - **Critical** nhat la FR-04 - quy trinh Checkout, vi day la chuc nang cot loi cua mot website thuong mai dien tu
> - Moi FR deu duoc **map truc tiep** vao mot module cu the trong source code. Vi du FR-01 Browse Products tuong ung voi **Modules/Catalog**, FR-04 Checkout lien quan den ca **Modules/Cart** va **Modules/Payment**
> - Dieu nay dam bao **Requirements-to-Code Traceability** - moi yeu cau deu co the truy vet den code thuc te"

---

### PHAN 3: NON-FUNCTIONAL REQUIREMENTS (2 phut)

| ID | Thuoc tinh | Mo ta | Muc do |
|----|-----------|-------|--------|
| NFR-01 | **Performance** | 90% truy van tim kiem phan hoi trong **2 giay** duoi tai binh thuong | High |
| NFR-02 | **Security** | Du lieu khach hang va thanh toan phai duoc **ma hoa** (HTTPS, AES-256) | Critical |
| NFR-03 | **Availability** | He thong duy tri toi thieu **99.9% uptime** | Critical |
| NFR-04 | **Scalability** | Ho tro it nhat **5,000 nguoi dung dong thoi** trong gio cao diem | High |
| NFR-05 | **Usability** | Giao dien **responsive** cho ca desktop va mobile | Medium |

**Cach noi:**

> "Ve Non-Functional Requirements, chung em xac dinh 5 yeu cau phi chuc nang quan trong nhat:
>
> **NFR-01 Performance**: He thong phai phan hoi trong 2 giay - chung em dat duoc nho **Redis caching** va **database indexing**
>
> **NFR-02 Security**: Day la yeu cau **Critical** - chung em su dung HTTPS, bcrypt cho mat khau, CSRF protection, va **khong luu thong tin the** ma delegate cho Payment Gateway
>
> **NFR-04 Scalability**: Ho tro 5000 nguoi dung dong thoi - day chinh la ly do chung em chon kien truc **Microservices** voi **Kong API Gateway** va **Database per Service**
>
> Cac NFR nay chinh la co so de chung em xac dinh cac **Architecturally Significant Requirements** o phan tiep theo"

---

### PHAN 4: ASR CARDS - TRONG TAM (4 phut)

**Day la phan quan trong nhat - thay se danh gia ky nhat**

#### ASR-1: Scalability (Kha nang mo rong)

```
+----------------------------------------------------------+
|                    ASR CARD 1                             |
+----------------------------------------------------------+
| Quality Attribute: SCALABILITY                           |
|                                                          |
| Statement:                                               |
| He thong phai ho tro tang dot bien tu 500 den 5,000     |
| nguoi dung dong thoi trong cac dot sale theo mua.       |
|                                                          |
| Architectural Impact:                                    |
| -> Phan tach component (Module separation)              |
| -> Can bang tai (Load balancing - Kong Gateway)          |
| -> Chien luoc trien khai ngang (Horizontal scaling)     |
| -> Database per Service                                  |
| -> Redis caching                                         |
|                                                          |
| Code Evidence:                                           |
| - docker-compose.microservices.yml: 17+ services        |
| - 3 database rieng: mysql-catalog, mysql-order,         |
|   mysql-user                                             |
| - Kong API Gateway lam load balancer                    |
+----------------------------------------------------------+
```

**Cach noi:**

> "ASR-1 la **Scalability**. Yeu cau nay phat bieu rang he thong phai xu ly duoc 5,000 nguoi dung dong thoi trong cac dot sale lon nhu Black Friday.
>
> **Tai sao day la ASR?** Vi neu khong dat, he thong se sap khi co nhieu nguoi truy cap - va do la that bai toan bo.
>
> **Tac dong len kien truc:** Yeu cau nay buoc chung em:
> - **Phan tach thanh cac service doc lap** de co the scale rieng tung phan (Catalog, Order, User la 3 service rieng)
> - Su dung **Kong API Gateway** de can bang tai va dinh tuyen
> - Moi service co **database rieng** (mysql-catalog, mysql-order, mysql-user) de tranh bottleneck
> - Su dung **Redis** lam cache de giam tai cho database
>
> **Chung minh thuc te:** Trong file `docker-compose.microservices.yml`, chung em da cau hinh 17+ services bao gom ca monitoring voi Prometheus va Grafana"

#### ASR-2: Security & Access Control (Bao mat)

```
+----------------------------------------------------------+
|                    ASR CARD 2                             |
+----------------------------------------------------------+
| Quality Attribute: SECURITY                              |
|                                                          |
| Statement:                                               |
| Moi truy cap vao chuc nang quan tri phai duoc xac thuc |
| va phan quyen qua token bao mat. Mat khau va thong tin  |
| thanh toan phai tuan thu chuan cong nghiep.             |
|                                                          |
| Architectural Impact:                                    |
| -> API Gateway Pattern (Kong) cho authentication        |
| -> Security Middleware layer                              |
| -> Khong luu du lieu the (delegate Payment Gateway)     |
| -> Bcrypt password hashing                               |
| -> CSRF + XSS protection                                |
|                                                          |
| Code Evidence:                                           |
| - app/Http/Kernel.php: Middleware stack                  |
| - Modules/Admin: Auth middleware bao ve                  |
| - Modules/Payment: MoMo, VNPay, PayPal delegation       |
+----------------------------------------------------------+
```

**Cach noi:**

> "ASR-2 la **Security**. Yeu cau nay bao ve ca 2 mat:
> - **Authentication**: Xac thuc nguoi dung qua session/token
> - **Authorization**: Chi Admin moi truy cap duoc trang quan tri
>
> **Tac dong len kien truc:**
> - Chung em dung **Kong API Gateway** lam diem kiem tra duy nhat truoc khi request vao he thong
> - **Middleware pattern** trong Laravel: moi request phai qua auth check truoc khi den Controller
> - Dac biet voi thanh toan: chung em **KHONG luu thong tin the** - ma delegate cho cac Payment Gateway nhu MoMo, VNPay, PayPal. Khach hang nhap thong tin tren trang cua ho, khong phai tren trang cua chung em. Day la cach tuan thu PCI DSS.
>
> Vi du cu the: khi Admin truy cap `/admin/products`, request phai qua `AdminAuthMiddleware` truoc - neu chua dang nhap se bi redirect ve trang login"

#### ASR-3: Modifiability & Loose Coupling (Kha nang thay doi)

```
+----------------------------------------------------------+
|                    ASR CARD 3                             |
+----------------------------------------------------------+
| Quality Attribute: MODIFIABILITY                         |
|                                                          |
| Statement:                                               |
| Tich hop mot Payment Gateway moi KHONG duoc anh huong  |
| den module Product Inventory hay User Management.        |
|                                                          |
| Architectural Impact:                                    |
| -> Separation of Concerns (tach biet moi quan tam)      |
| -> Layered Architecture (trinh bay, logic, du lieu)     |
| -> Interface giua cac tang ro rang                      |
| -> Module hoa theo domain                                |
|                                                          |
| Code Evidence:                                           |
| - 8 modules doc lap: Admin, Catalog, Customer, Payment, |
|   Cart, Review, Content, Support                         |
| - Moi module co routes, controllers, views rieng        |
| - Them Payment Gateway moi chi can sua Modules/Payment  |
+----------------------------------------------------------+
```

**Cach noi:**

> "ASR-3 la **Modifiability**. Day la yeu cau ma nhieu he thong that bai vi thiet ke khong tot.
>
> **Phat bieu**: Khi chung em can tich hop them mot cong thanh toan moi, vi du Stripe, thi viec do **KHONG DUOC anh huong** den module Product hay User.
>
> **Cach chung em dat duoc dieu nay:**
> - Du an duoc to chuc thanh **8 module doc lap**, moi module co folder rieng voi routes, controllers, models, views cua rieng no
> - Module **Payment** hoan toan tach biet voi module **Catalog** - chung chi giao tiep qua interface
> - Khi can them Stripe, chung em chi can tao them file trong `Modules/Payment/` ma KHONG can sUA bat ky dong code nao trong `Modules/Catalog/` hay `Modules/Customer/`
>
> Day chinh la nguyen tac **Separation of Concerns** - va cung la ly do Layered Architecture duoc chon cho Lab 2"

#### Bang tom tat ASR -> Kien truc

| ASR | Quality Attribute | Quyet dinh kien truc | Pattern ap dung |
|-----|-------------------|---------------------|-----------------|
| ASR-1 | Scalability | Phan tach service, scale ngang | Microservices, API Gateway, Database per Service |
| ASR-2 | Security | Diem kiem tra tap trung | Gateway Pattern, Middleware Pattern |
| ASR-3 | Modifiability | Module doc lap, interface ro rang | Layered Architecture, Separation of Concerns |

---

### PHAN 5: USE CASE DIAGRAM (3 phut)

#### 5.1 System Context Diagram

**Cach noi:**

> "Diagram dau tien la **System Context Diagram** - cho thay ranh gioi he thong va cac Actor tuong tac nhu the nao.
>
> O giua la **ElectroShop Platform** - day la system boundary.
> - Ben trai la **Web Customer** - tuong tac qua trinh duyet web (HTTPS)
> - Ben trai tren la **Administrator** - tuong tac qua Admin Panel
> - Ben phai la **Payment Gateway** (VNPay, MoMo) - tuong tac qua REST API
>
> Diagram nay cho thay he thong cua chung em khong ton tai doc lap ma phu thuoc vao **he thong thanh toan ben ngoai** de xu ly giao dich"

#### 5.2 Main Use Case Diagram

**Noi dung:**

**Customer Use Cases:**
- Browse Products - Duyet san pham theo danh muc
- Register/Login - Tao tai khoan va xac thuc
- Manage Shopping Cart - Them, xoa, sua so luong
- **Make Purchase** - Hoan tat checkout (**Critical Path**)
- Submit Review - Danh gia san pham

**Administrator Use Cases:**
- Manage Categories - CRUD danh muc
- Manage Products - Them, sua, xoa san pham
- Process Orders - Xem va cap nhat trang thai don hang
- Manage Articles - Tao va xuat ban bai viet

**Cach noi:**

> "Diagram chinh cho thay tat ca use case cua he thong. Diem dang chu y:
> - Customer co 5 use case chinh, trong do **Make Purchase** la critical nhat
> - Admin co 4 use case chinh de quan ly he thong
> - **Payment Gateway** la actor ngoai he thong, chi tham gia vao quy trinh thanh toan
> - Chung em se di chi tiet vao **Make Purchase** o phan tiep theo vi day la **Critical Path** cua he thong"

---

### PHAN 6: CRITICAL PATH - MAKE PURCHASE / CHECKOUT (3 phut)

**Day la phan thay muon thay include/extend - trinh bay that ro**

#### Use Case Relationships:

```
                    +-------------------+
                    |  Make Purchase    |
                    | (Checkout Process)|
                    +-------------------+
                           |
              +------------+------------+
              |            |            |
        <<include>>  <<include>>  <<include>>
              |            |            |
    +---------+  +---------+  +--------+--------+
    | Validate |  | Enter   |  | Select Payment  |
    | Cart     |  | Shipping|  | Method          |
    +----------+  | Info    |  +---------+-------+
                  +---------+            |
                                    <<include>>
                                         |
                                  +------+------+
                                  | Secure      |
                                  | Payment     |
                                  | (Gateway)   |
                                  +-------------+

        <<extend>>                    <<extend>>
    +----------------+           +------------------+
    | Apply Coupon   |---------->| Make Purchase    |
    | Code           |           |                  |
    +----------------+           |                  |
                                 |                  |
    +----------------+           |                  |
    | Use Loyalty    |---------->|                  |
    | Points         |           +------------------+
    +----------------+
```

**Cach noi:**

> "Day la phan quan trong nhat cua Use Case Modeling - chi tiet hoa **Make Purchase**.
>
> **4 moi quan he <<include>> (bat buoc):**
> 1. **Validate Cart** - He thong PHAI kiem tra gio hang truoc: con hang khong, gia co thay doi khong
> 2. **Enter Shipping Info** - Khach hang PHAI nhap dia chi giao hang
> 3. **Select Payment Method** - PHAI chon phuong thuc thanh toan (MoMo, VNPay, PayPal, COD)
> 4. **Secure Payment** - Khi da chon phuong thuc, he thong PHAI goi Payment Gateway de xu ly
>
> **2 moi quan he <<extend>> (tuy chon):**
> 1. **Apply Coupon Code** - Khach hang CO THE nhap ma giam gia (khong bat buoc)
> 2. **Use Loyalty Points** - Khach hang CO THE dung diem tich luy (khong bat buoc)
>
> **Diem khac biet quan trong:**
> - **<<include>>** = PHAI CO, khong co thi use case khong hoan thanh
> - **<<extend>>** = TUY CHON, co hay khong use case van hoan thanh
>
> Trong code thuc te, khi khach hang an nut 'Thanh Toan', he thong se:
> 1. Goi `CartController@checkout` - validate gio hang
> 2. Hien form nhap thong tin giao hang
> 3. Khach chon phuong thuc thanh toan
> 4. Redirect sang trang cua MoMo/VNPay hoac xu ly COD
> 5. Nhan callback va tao don hang"

---

### PHAN 7: KET LUAN & LIEN KET LAB 2 (1 phut)

**Cach noi:**

> "Tong ket Lab 01, chung em da hoan thanh:
> 1. **Requirements Elicitation**: 3 Actors, 10 FRs, 5 NFRs, 3 ASRs voi phan tich chi tiet
> 2. **Use Case Modeling**: System Context Diagram, Main Use Case Diagram, va Detailed Checkout Process voi include/extend
>
> **Bai hoc rut ra:**
> - **NFRs quan trong hon FRs** khi quyet dinh kien truc - 'he thong lam gi' de hon 'he thong lam tot nhu the nao'
> - **ASRs la dong luc chinh** cho viec chon architectural pattern
>
> **Lien ket sang Lab 2:**
> - ASR-3 (Modifiability) truc tiep dan den viec chon **Layered Architecture**
> - Moi module se duoc to chuc theo 4 tang: Presentation -> Business Logic -> Persistence -> Data
> - Controller -> Service -> Repository -> Database
>
> Cam on thay va cac ban da lang nghe. Nhom chung em san sang tra loi cau hoi."

---

## 3. DEMO CODE THUC TE

Khi thay hoi "Code o dau?", ban co the mo truc tiep cac file sau:

### Demo 1: Module Structure (chung minh Separation of Concerns)

```
Modules/
├── Admin/          <- FR-06, FR-07, FR-08, FR-10
│   ├── routes/web.php
│   ├── Http/Controllers/
│   └── Resources/views/
├── Catalog/        <- FR-01
│   ├── routes/web.php
│   ├── Http/Controllers/
│   └── Resources/views/
├── Customer/       <- FR-02
│   ├── routes/web.php
│   ├── Http/Controllers/
│   └── Resources/views/
├── Payment/        <- FR-04 (Checkout)
│   ├── routes/web.php     <- MoMo, VNPay, PayPal routes
│   ├── Http/Controllers/
│   └── Resources/views/
├── Cart/           <- FR-03, FR-04
│   ├── routes/web.php
│   ├── Http/Controllers/
│   └── Resources/views/
├── Review/         <- FR-05
├── Content/        <- FR-10
└── Support/        <- Contact support
```

**Cach demo:**
> "Day la cau truc 8 module trong du an. Moi module hoan toan doc lap, co routes, controllers, views rieng. Khi toi can them Payment Gateway moi, toi chi lam viec trong folder `Modules/Payment/` ma KHONG can sua code o `Modules/Catalog/` - day la **Separation of Concerns** thuc te."

### Demo 2: Payment Routes (chung minh tich hop nhieu Payment Gateway)

**File:** `Modules/Payment/routes/web.php`

> "Day la routes cua module Payment, ho tro 4 phuong thuc thanh toan: MoMo, VNPay, PayPal va QR Code. Moi phuong thuc co route rieng de xu ly callback tu Gateway."

### Demo 3: Docker Microservices (chung minh ASR-1 Scalability)

**File:** `docker-compose.microservices.yml`

> "Day la file Docker Compose voi 17+ services. Chu y:
> - 3 database rieng: `mysql-catalog`, `mysql-order`, `mysql-user` -> **Database per Service**
> - `kong` -> **API Gateway** lam load balancer
> - `rabbitmq` -> **Message Broker** cho async communication
> - `prometheus` + `grafana` -> **Monitoring** de theo doi hieu nang"

### Demo 4: Middleware Security (chung minh ASR-2 Security)

**File:** `app/Http/Kernel.php`

> "Day la Middleware stack. Moi request HTTP phai di qua cac lop bao mat nhu CSRF verification, session encryption, authentication check TRUOC khi den Controller. Day la **Gateway Pattern** ap dung cho bao mat."

---

## 4. CAU HOI THUONG GAP VA CACH TRA LOI

### Q1: "Tai sao chon 3 ASR nay ma khong phai cai khac?"

**Tra loi:**
> "Chung em chon 3 ASR nay vi chung la nhung yeu cau ma **neu khong dat thi he thong coi nhu that bai**:
> - **Scalability**: Website e-commerce ma khong chiu duoc tai cao diem thi mat khach hang va doanh thu
> - **Security**: Lo thong tin thanh toan cua khach hang thi vi pham phap luat va mat uy tin
> - **Modifiability**: Nganh thanh toan thay doi lien tuc, neu khong them duoc Payment Gateway moi thi he thong nhanh chong loi thoi
>
> 3 ASR nay cung dai dien cho 3 nhom chat luong khac nhau: Performance, Security, Maintainability - bao phu cac khia canh quan trong nhat"

### Q2: "Su khac nhau giua <<include>> va <<extend>> la gi?"

**Tra loi:**
> "**<<include>>** la moi quan he BAT BUOC - use case chinh KHONG THE hoan thanh neu thieu no.
> Vi du: Make Purchase PHAI co Validate Cart - khong the thanh toan neu gio hang khong hop le.
>
> **<<extend>>** la moi quan he TUY CHON - use case chinh VAN hoan thanh duoc khi khong co no.
> Vi du: Apply Coupon la tuy chon - khach hang van mua hang duoc du khong nhap ma giam gia.
>
> Noi don gian: include = 'bao gom', extend = 'mo rong them'"

### Q3: "ASR tac dong len Layered Architecture nhu the nao?"

**Tra loi:**
> "ASR-3 (Modifiability) truc tiep quyet dinh viec dung Layered Architecture vi:
> - **Presentation Layer**: UI co the thay doi khong anh huong Business Logic
> - **Business Logic Layer**: Them tinh nang moi khong anh huong Database
> - **Persistence Layer**: Doi database (MySQL -> PostgreSQL) khong anh huong Logic
> - **Data Layer**: Schema thay doi duoc quan ly doc lap
>
> Cac tang chi phu thuoc **mot chieu tu tren xuong**. Controller goi Service, Service goi Repository - KHONG BAO GIO nguoc lai.
>
> ASR-2 (Security) cung duoc thuc thi o **Business Logic Layer** - dam bao moi request deu duoc kiem tra quyen han, du no den tu web UI hay API"

### Q4: "Functional Requirements da duoc implement het chua?"

**Tra loi:**
> "Da, tat ca 10 FRs deu da duoc implement va co the demo:
> - FR-01: Vao trang chu, bam vao danh muc bat ky de duyet san pham
> - FR-02: Bam Dang ky, nhap thong tin, dang nhap thanh cong
> - FR-03: Bam 'Them vao gio hang' tren bat ky san pham nao
> - FR-04: Vao gio hang, bam 'Thanh toan', chon MoMo/VNPay/PayPal
> - ...va cac FR khac deu tuong tu.
>
> Chung em co **Requirements-to-Code Traceability** - moi FR map truc tiep den module cu the trong code"

### Q5: "Tai sao dung Microservices ma khong dung Monolithic?"

**Tra loi:**
> "Thuc ra du an hien tai la **Modular Monolith** - chua phai Microservices hoan toan. Nhung chung em da thiet ke theo huong microservices vi:
>
> **ASR-1 (Scalability)** yeu cau he thong chiu duoc 5000 nguoi dung dong thoi. Voi Monolithic, khi can scale phai scale toan bo ung dung. Voi Microservices, chi can scale nhung service bi tai cao (vi du Catalog Service trong dot sale).
>
> Hien tai chung em dang o giai doan chuyen tiep:
> - **Code**: Modular Monolith (8 modules doc lap trong 1 codebase)
> - **Infrastructure**: Da san sang Microservices (Docker Compose voi 17+ services)
> - **Chuyen doi**: Dung **Strangler Pattern** - dan dan tach tung module ra thanh service doc lap"

### Q6: "Lam sao dam bao NFR-01 Performance (phan hoi trong 2 giay)?"

**Tra loi:**
> "Chung em su dung nhieu ky thuat:
> 1. **Redis Caching**: Cache ket qua truy van thuong xuyen (danh muc, san pham hot)
> 2. **Database Indexing**: Index tren cac truong thuong tim kiem (ten, danh muc, gia)
> 3. **Eager Loading**: Tranh N+1 query problem khi load san pham voi danh muc
> 4. **Pagination**: Khong load tat ca san pham cung luc, chi load 20 san pham/trang
> 5. **CDN-ready**: Static files (CSS, JS, images) co the dat len CDN"

### Q7: "Diagram duoc ve bang gi?"

**Tra loi:**
> "Chung em su dung 2 cong cu:
> - **PlantUML** cho cac diagram trong source code (co 12 file .puml trong thu muc Design/)
> - **Draw.io** cho cac diagram trong bao cao Word
>
> Cac diagram PlantUML co the render tu dong tu code, dam bao luon dong bo voi kien truc thuc te"

---

## 5. TIPS GHI DIEM

### DO - Nen lam:

1. **Luon lien ket voi code thuc te**: Khi noi ve ASR, chi vao file cu the trong source code
2. **Dung con so cu the**: "5,000 nguoi dung", "2 giay response time", "8 modules" - khong noi chung chung
3. **Giai thich TAI SAO**: "Chung em chon Layered Architecture **VI** ASR-3 yeu cau Modifiability" - khong chi noi "chung em chon Layered Architecture"
4. **Demo live**: Neu co the, mo project va chi vao cau truc folder, docker-compose file
5. **Tu tin voi phan Q&A**: Doc ky cac cau tra loi o tren
6. **Nhan manh diem khac biet**: Du an cua nhom KHONG CHI la ly thuyet ma da co code chay duoc

### DON'T - Khong nen lam:

1. **Khong doc nguyen van slide**: Noi tu nhien, chi nhin slide de nho y chinh
2. **Khong noi qua nhanh**: De thay va cac nhom khac kip hieu
3. **Khong noi "em khong biet"**: Neu khong chac, noi "theo hieu biet cua em thi..." va giai thich
4. **Khong bo qua ASR Cards**: Day la phan thay danh gia ky nhat
5. **Khong quen include/extend**: Day la kien thuc co ban nhat cua UML Use Case - phai giai thich duoc

### Cau mo dau gay an tuong:

> "Khac voi nhieu du an chi tren giay, ElectroShop la mot he thong THUC TE voi hon 800 files code, 8 module doc lap, da deploy duoc bang Docker voi 17+ microservices. Lab 01 nay cho thay requirements da dan den nhung quyet dinh kien truc nhu the nao."

### Cau ket thuc gay an tuong:

> "Tu 10 Functional Requirements va 3 ASRs trong Lab 01, chung em da xay dung duoc mot kien truc module hoa voi 8 modules doc lap, san sang chuyen doi sang Microservices. Day khong chi la bai tap - ma la nen tang cho mot he thong co the chay trong moi truong production thuc te."

---

## 6. TOM TAT NHANH - PHIEU GHI NHO

In phieu nay de cam tay khi thuyet trinh:

```
+-------------------------------------------+
|          PHIEU GHI NHO - LAB 01           |
+-------------------------------------------+
|                                           |
| 1. DU AN: ElectroShop - Ban Do Dien Tu   |
|    Laravel + Docker + 8 Modules           |
|                                           |
| 2. ACTORS: 3 (Customer, Admin, Gateway)   |
|                                           |
| 3. FRs: 10 (Critical: FR-04 Checkout)    |
|                                           |
| 4. NFRs: 5 (Critical: Security, Avail.)  |
|                                           |
| 5. ASRs: 3                               |
|    ASR-1: Scalability -> Microservices    |
|    ASR-2: Security    -> Gateway Pattern  |
|    ASR-3: Modifiability -> Layered Arch   |
|                                           |
| 6. USE CASE:                              |
|    - System Context (3 actors)            |
|    - Main Diagram (9 use cases)           |
|    - Checkout Detail:                     |
|      4 <<include>> (bat buoc)             |
|      2 <<extend>>  (tuy chon)            |
|                                           |
| 7. KEY MESSAGE:                           |
|    "ASRs drive Architecture decisions"    |
|    "NFRs > FRs for architecture"          |
|                                           |
+-------------------------------------------+
```

---

## 7. CHIA PHAN TRINH BAY CHO 2 NGUOI

### Nguyen Tuan Thien (23010571):

| Stt | Phan | Noi dung |
|-----|------|----------|
| 1 | Mo dau | Gioi thieu du an, muc tieu Lab 01 |
| 2 | Actors | 3 actors va vai tro |
| 3 | FRs | 10 Functional Requirements voi priority |
| 4 | Use Case Diagram | System Context + Main Diagram |
| 5 | Ket luan | Tong ket va lien ket Lab 2 |

### Dang Viet Anh (23010689):

| Stt | Phan | Noi dung |
|-----|------|----------|
| 1 | NFRs | 5 Non-Functional Requirements |
| 2 | ASR Card 1 | Scalability -> Microservices |
| 3 | ASR Card 2 | Security -> Gateway Pattern |
| 4 | ASR Card 3 | Modifiability -> Layered Architecture |
| 5 | Critical Path | Checkout process voi include/extend |

**Luu y:** Ca hai nen hieu TOAN BO noi dung de co the ho tro nhau trong phan Q&A.

---

## 8. SO SANH: BAO CAO HIEN TAI vs YEU CAU LAB

| Yeu cau Lab 01 | Trong bao cao .docx | Danh gia |
|----------------|---------------------|----------|
| Identify 3+ Actors | 3 Actors (Customer, Admin, Payment Gateway) | DAT |
| Document FRs voi priority | 10 FRs voi priority ranking | DAT |
| Document NFRs | 5 NFRs voi impact level | DAT |
| Define 3 ASRs | 3 ASR Cards chi tiet | DAT |
| ASR Impact Analysis | Figure 2.1 + bang phan tich | DAT |
| System Context Diagram | Co (Figure 2.2) | DAT |
| Main Use Case Diagram | Co (Figure 2.3) | DAT |
| Critical Path voi include/extend | Co (Figure 2.4) - 4 include, 2 extend | DAT |
| Requirements-to-Code Traceability | Bang FR -> Module mapping | DAT (BONUS) |
| Problem Analysis for Lab 2 | ASR -> Layered Architecture | DAT |

**Ket luan: Bao cao da day du tat ca yeu cau. Phan Requirements-to-Code Traceability la diem cong them.**

---

*File nay duoc tao de ho tro thuyet trinh Lab 01. Chuc nhom 16 thuyet trinh thanh cong!*
