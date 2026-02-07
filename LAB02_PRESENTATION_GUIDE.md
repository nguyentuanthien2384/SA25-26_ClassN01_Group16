# LAB 02: PRESENTATION GUIDE
## Layered Architecture Design - Electronics E-Commerce Website

**Course:** Software Architecture-1-2-25 (N01)  
**Group:** 16  
**Members:** Nguyen Tuan Thien - 23010571 | Dang Viet Anh - 23010689  
**Instructor:** M.Sc. Vu Quang Dung

---

## MUC LUC

1. [Kich ban thuyet trinh (Script)](#1-kich-ban-thuyet-trinh)
2. [Noi dung chi tiet tung phan](#2-noi-dung-chi-tiet)
3. [Demo code thuc te - DIEM MANH NHAT](#3-demo-code-thuc-te)
4. [Cau hoi thuong gap va cach tra loi](#4-cau-hoi-thuong-gap)
5. [Tips ghi diem](#5-tips-ghi-diem)

---

## 1. KICH BAN THUYET TRINH (15-20 phut)

### Phan bo thoi gian khuyen nghi

| Phan | Noi dung | Thoi gian | Nguoi trinh bay |
|------|----------|-----------|-----------------|
| 1 | Gioi thieu Lab 02 & muc tieu | 1.5 phut | Thien |
| 2 | Dinh nghia 4 Layer + Trach nhiem | 3 phut | Thien |
| 3 | Data Flow - Request/Response | 2 phut | Thien |
| 4 | Component Identification (Product Catalog) | 3 phut | Viet Anh |
| 5 | Interface Definitions (Lollipop/Socket) | 2.5 phut | Viet Anh |
| 6 | UML Component Diagram | 3 phut | Viet Anh |
| 7 | **DEMO CODE THUC TE** | 2 phut | Thien |
| 8 | Trade-offs & ASR Evaluation | 2 phut | Viet Anh |
| 9 | Ket luan & Lien ket Lab 3 | 1 phut | Ca hai |

---

## 2. NOI DUNG CHI TIET TUNG PHAN

---

### PHAN 1: GIOI THIEU LAB 02 (1.5 phut)

**Mo dau (noi gi):**

> "Xin chao thay va cac ban. Nhom 16 tiep tuc trinh bay Lab 02 - **Layered Architecture Design**.
>
> Trong Lab 01, chung em da xac dinh 3 ASR: Scalability, Security va Modifiability. Dac biet **ASR-3 Modifiability** yeu cau cac component phai thay doi doc lap voi nhau.
>
> Lab 02 nay tra loi cau hoi: **Kien truc nao thoa man yeu cau do?** Va cau tra loi la **Layered Architecture Pattern** - chia he thong thanh 4 tang voi quy tac phu thuoc nghiem ngat.
>
> Muc tieu cua Lab 02:
> 1. Dinh nghia 4 tang va trach nhiem cua moi tang
> 2. Xac dinh cac component trong tung tang cho tinh nang **Product Catalog**
> 3. Mo hinh hoa bang **UML Component Diagram** voi ky hieu Lollipop/Socket
> 4. Danh gia trade-offs cua Layered Architecture doi voi ASRs"

---

### PHAN 2: DINH NGHIA 4 LAYER (3 phut) - TRONG TAM

**Day la co so cua toan bo Lab 02 - phai trinh bay that ro**

**Cach noi:**

> "Layered Architecture Pattern co mot **quy tac vang**: Moi tang CHI DUOC goi tang ngay ben duoi no. Khong duoc nhay tang, khong duoc goi nguoc len. Day goi la **Strict Downward Dependency**.
>
> He thong cua chung em duoc chia thanh 4 tang:"

#### Bang 4 Tang (ve len bang hoac slide)

```
┌─────────────────────────────────────────────────────────┐
│  LAYER 1: PRESENTATION (UI/Web)                         │
│                                                         │
│  Trach nhiem: Xu ly HTTP request, authentication,       │
│  session, render giao dien                              │
│                                                         │
│  Component: ProductController, CategoryController,      │
│             OrderController, CartController              │
│                                                         │
│  Trong code: app/Lab03/Controllers/                     │
└──────────────────────┬──────────────────────────────────┘
                       │ CHI GOI XUONG (khong goi nguoc)
                       ▼
┌─────────────────────────────────────────────────────────┐
│  LAYER 2: BUSINESS LOGIC (Service/Domain)               │
│                                                         │
│  Trach nhiem: Business rules, validation, dieu phoi     │
│  truy cap du lieu                                       │
│                                                         │
│  Component: ProductService, CategoryService,            │
│             OrderService, CartService                    │
│                                                         │
│  Trong code: app/Lab03/Services/                        │
└──────────────────────┬──────────────────────────────────┘
                       │ CHI GOI XUONG
                       ▼
┌─────────────────────────────────────────────────────────┐
│  LAYER 3: PERSISTENCE (Data Access)                     │
│                                                         │
│  Trach nhiem: Map business object -> database entity,   │
│  thuc hien CRUD                                         │
│                                                         │
│  Component: ProductRepository, CategoryRepository,      │
│             OrderRepository                              │
│                                                         │
│  Trong code: app/Lab03/Repositories/                    │
└──────────────────────┬──────────────────────────────────┘
                       │ CHI GOI XUONG
                       ▼
┌─────────────────────────────────────────────────────────┐
│  LAYER 4: DATA (Database)                               │
│                                                         │
│  Trach nhiem: Luu tru du lieu vat ly (MySQL)            │
│                                                         │
│  Artifact: products, categories, orders, users,         │
│            carts, reviews tables                         │
│                                                         │
│  Trong code: database/migrations/                       │
└─────────────────────────────────────────────────────────┘
```

**Cach noi tiep:**

> "**Diem mau chot:** Moi tang chi biet tang ngay duoi no. Vi du:
> - **ProductController** (Layer 1) goi `ProductService` (Layer 2) - DUNG
> - **ProductController** (Layer 1) goi `ProductRepository` (Layer 3) - **SAI** - vi nhay tang
> - **ProductService** (Layer 2) goi `ProductController` (Layer 1) - **SAI** - vi goi nguoc len
>
> Quy tac nay dam bao **ASR-3 Modifiability**: khi thay doi Layer 3 (doi database), Layer 1 va Layer 2 KHONG bi anh huong."

---

### PHAN 3: DATA FLOW - REQUEST/RESPONSE (2 phut)

**Vi du: Khach hang xem chi tiet san pham**

```
Client Request → Layer 1 → Layer 2 → Layer 3 → Layer 4
                                                  ↓
Client Response ← Layer 1 ← Layer 2 ← Layer 3 ←──┘
```

#### Bang chi tiet 7 buoc:

| Buoc | Tu | Den | Hanh dong |
|------|-----|-----|-----------|
| 1 | Client (Browser) | ProductController | HTTP GET `/products/{id}` |
| 2 | ProductController | ProductService | Goi `productService->getProductDetails(id)` |
| 3 | ProductService | ProductRepository | Goi `productRepository->findById(id)` |
| 4 | ProductRepository | Database (MySQL) | Chay SQL: `SELECT * FROM products WHERE id = ?` |
| 5 | Database | ProductRepository | Tra ve row du lieu Product |
| 6 | ProductRepository → ProductService | ProductController | Tra ve Product entity → Product DTO |
| 7 | ProductController | Client (Browser) | Tra ve HTTP 200 + JSON/HTML san pham |

**Cach noi:**

> "Khi khach hang truy cap trang chi tiet san pham, request di qua **4 tang** theo thu tu:
>
> **Buoc 1-2:** Browser gui request `GET /products/5` den **ProductController**. Controller KHONG xu ly logic, chi goi `ProductService`.
>
> **Buoc 3-4:** Service kiem tra business rules (san pham con active khong?) roi goi **ProductRepository**. Repository chuyen thanh SQL query va truy van MySQL.
>
> **Buoc 5-7:** Du lieu tra ve theo chieu nguoc lai: Database → Repository → Service (xu ly them logic) → Controller (format JSON) → Browser.
>
> **Diem quan trong:** Moi tang chi lam nhiem vu cua minh. Controller KHONG BIET database la MySQL hay PostgreSQL. Service KHONG BIET request den tu web hay API. Day chinh la **Separation of Concerns**."

---

### PHAN 4: COMPONENT IDENTIFICATION - PRODUCT CATALOG (3 phut)

**Cach noi:**

> "Bay gio chung em di vao cu the: xac dinh cac component cho tinh nang **Product Catalog** - mot trong nhung FR quan trong nhat."

#### Bang Component:

| Tang | Component | Trach nhiem cu the |
|------|-----------|-------------------|
| **Layer 1** (Presentation) | **ProductController** | - Nhan HTTP request (GET, POST, PUT, DELETE) |
| | | - Validate tham so dau vao (id co phai so khong) |
| | | - Goi Business Logic Layer |
| | | - Tra ve HTTP response (JSON/HTML) |
| **Layer 2** (Business Logic) | **ProductService** | - Nhan product ID tu Controller |
| | | - Kiem tra business rules (san pham active? con hang?) |
| | | - Goi Persistence Layer |
| | | - Tra ve Product object da duoc validate |
| **Layer 3** (Persistence) | **ProductRepository** | - Chuyen request thanh database query |
| | | - Thuc hien CRUD (Create, Read, Update, Delete) |
| | | - Tra ve ProductEntity data tho |

**Cach noi:**

> "Moi component co **trach nhiem rieng biet**:
> - **ProductController** chi biet HTTP - no khong biet business rule nao ca
> - **ProductService** chi biet business logic - no khong biet database query
> - **ProductRepository** chi biet data access - no khong biet ai dang goi no
>
> Day la nguyen tac **Single Responsibility** - moi class lam mot viec duy nhat va lam tot viec do."

---

### PHAN 5: INTERFACE DEFINITIONS - LOLLIPOP/SOCKET (2.5 phut)

**Day la phan thay se hoi ky - phai giai thich that ro**

**Cach noi:**

> "Giua cac tang, chung em dinh nghia **Interface** - la hop dong (contract) giua 2 tang.
>
> Trong UML, interface duoc the hien bang 2 ky hieu:
> - **Lollipop** (hinh tron tren duong thang) ○─ = **Provided Interface** - tang cung cap dich vu
> - **Socket** (hinh ban nguyet) ◠ = **Required Interface** - tang can su dung dich vu
>
> Khi Lollipop gap Socket, ta co mot **connection** giua 2 component."

#### Interface Definitions cu the:

**IProductService** (Layer 2 cung cap cho Layer 1):

```
Interface: IProductService
├── getProductDetails(productId): Product
│   → Tra ve san pham da validate voi tat ca business rules
│
├── getAllProducts(): List<Product>
│   → Tra ve tat ca san pham active trong catalog
│
└── searchProducts(keyword): List<Product>
    → Tim kiem san pham theo tu khoa trong ten/mo ta
```

**IProductRepository** (Layer 3 cung cap cho Layer 2):

```
Interface: IProductRepository
├── findById(productId): ProductEntity
│   → Tra ve entity tho tu database
│
├── findAll(): List<ProductEntity>
│   → Tra ve tat ca product entities
│
├── save(entity): void
│   → Luu entity vao database
│
├── delete(id): bool
│   → Xoa san pham theo id
│
└── searchByName(keyword): List<ProductEntity>
    → Tim kiem trong database theo ten
```

**Cach noi:**

> "**Tai sao dung Interface?** Vi no cho phep thay doi implementation ma KHONG anh huong layer phia tren.
>
> Vi du: Hien tai `ProductRepository` dung **MySQL**. Neu mai sau doi sang **MongoDB**, chung em chi can tao `MongoProductRepository` implement cung `IProductRepository`. **ProductService** KHONG CAN SUA gi vi no chi biet Interface, khong biet implementation cu the.
>
> Trong du an thuc te, chung em da lam dung nhu vay:"

**Chi vao code thuc te:**

> "File `ProductRepositoryInterface.php` dinh nghia contract. File `ProductRepository.php` implement contract do. Va trong `Lab03ServiceProvider.php`, chung em dung **Dependency Injection** de bind interface voi implementation:
>
> ```php
> $this->app->bind(
>     ProductRepositoryInterface::class,
>     ProductRepository::class
> );
> ```
>
> Khi can doi sang MongoDB, chi can doi dong bind nay thanh `MongoProductRepository` - KHONG SUA bat ky file nao khac."

---

### PHAN 6: UML COMPONENT DIAGRAM (3 phut)

**Cach noi:**

> "Day la UML Component Diagram cho tinh nang Product Catalog. Chung em giai thich cac thanh phan:"

#### Giai thich Diagram:

```
┌─────────────────────────── PRESENTATION LAYER ──────────────────────────┐
│                                                                          │
│   ┌──────────────────────┐                                               │
│   │ <<component>>        │                                               │
│   │ ProductController    │──◠ (Socket: can IProductService)              │
│   │                      │                                               │
│   │ + index(): JSON      │                                               │
│   │ + show(id): JSON     │                                               │
│   │ + store(): JSON      │                                               │
│   │ + update(id): JSON   │                                               │
│   │ + destroy(id): JSON  │                                               │
│   └──────────────────────┘                                               │
│                                                                          │
└──────────────────────────────────┬───────────────────────────────────────┘
                                   │
                          ○── IProductService (Lollipop: cung cap)
                                   │
┌──────────────────────── BUSINESS LOGIC LAYER ───────────────────────────┐
│                                                                          │
│   ┌──────────────────────────┐                                           │
│   │ <<component>>            │                                           │
│   │ ProductService           │──◠ (Socket: can IProductRepository)       │
│   │                          │                                           │
│   │ + getAllProducts(): List  │                                           │
│   │ + getProductDetails(id)  │                                           │
│   │ + createProduct(data)    │                                           │
│   │ + searchProducts(kw)     │                                           │
│   └──────────────────────────┘                                           │
│                                                                          │
└──────────────────────────────────┬───────────────────────────────────────┘
                                   │
                          ○── IProductRepository (Lollipop: cung cap)
                                   │
┌──────────────────────── PERSISTENCE LAYER ──────────────────────────────┐
│                                                                          │
│   ┌──────────────────────────┐                                           │
│   │ <<component>>            │                                           │
│   │ ProductRepository        │                                           │
│   │ (implements Interface)   │ ───→ [MySQL Database]                     │
│   │                          │                                           │
│   │ + findById(id)           │                                           │
│   │ + findAll()              │                                           │
│   │ + save(entity)           │                                           │
│   │ + delete(id)             │                                           │
│   └──────────────────────────┘                                           │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

**Cach noi:**

> "Trong diagram nay:
> - **3 hinh chu nhat lon** dai dien cho 3 tang: Presentation, Business Logic, Persistence
> - **Hinh component** (hinh chu nhat voi 2 hinh chu nhat nho) dai dien cho tung class
> - **Lollipop** (vong tron) ○── tren ProductService la **Provided Interface** - no CUNG CAP `IProductService`
> - **Socket** (ban nguyet) ◠ tren ProductController la **Required Interface** - no CAN `IProductService`
> - Tat ca mui ten chi **huong xuong duoi** - khong co mui ten nao chi nguoc len. Day la **Strict Downward Dependency**.
>
> Diagram day du cua toan he thong (Figure 2.5 trong bao cao) mo rong them cho cac tinh nang Order, User, Cart voi cung mo hinh tuong tu."

---

### PHAN 7: DEMO CODE THUC TE (2 phut) - DIEM KHAC BIET

**Day la phan lam ban noi bat hon cac nhom khac - vi ban co CODE THUC TE**

**Cach noi:**

> "Diem dac biet cua nhom chung em: Layered Architecture KHONG CHI la ly thuyet tren giay ma da duoc **implement thanh code thuc te** trong folder `app/Lab03/`."

**Mo file va chi:**

> "Day la cau truc folder Lab03 - tuong ung chinh xac voi 4 tang:
>
> ```
> app/Lab03/
> ├── Controllers/
> │   └── ProductController.php       ← Layer 1: Presentation
> ├── Services/
> │   └── ProductService.php          ← Layer 2: Business Logic
> ├── Repositories/
> │   ├── ProductRepositoryInterface.php  ← Contract (Interface)
> │   └── ProductRepository.php       ← Layer 3: Persistence
> ├── Providers/
> │   └── Lab03ServiceProvider.php    ← Dependency Injection config
> └── routes.php                      ← API Routes
> ```
>
> Va ban co the test API ngay bay gio:"

**Demo API (neu co the chay Docker):**

```
GET  http://localhost:8000/api/lab03/products         → List san pham
GET  http://localhost:8000/api/lab03/products/1        → Chi tiet san pham
GET  http://localhost:8000/api/lab03/products/search?keyword=laptop  → Tim kiem
POST http://localhost:8000/api/lab03/products          → Tao san pham
```

---

### PHAN 8: TRADE-OFFS & ASR EVALUATION (2 phut)

**Cach noi:**

> "Moi kien truc deu co uu va nhuoc diem. Layered Architecture cung vay:"

#### Bang Trade-offs:

| Uu diem (Pros) | Nhuoc diem (Cons) |
|----------------|-------------------|
| **Don gian:** De hieu, de phat trien nhanh cho team nho | **Scale kem:** Neu chi Catalog can nhieu tai, van phai scale toan bo ung dung |
| **De test:** Moi tang test doc lap bang cach mock tang duoi | **Deploy dinh kem:** Thay doi 1 tang phai redeploy toan bo he thong |
| **De bao tri:** Thay doi 1 tang khong anh huong tang khac (neu giu interface) | **Performance overhead:** Moi tang them do tre vi chuyen doi du lieu |

#### Danh gia ASR:

| ASR | Layered Architecture ho tro? | Xep hang | Hanh dong tuong lai |
|-----|------------------------------|----------|---------------------|
| **ASR-1: Scalability** | **YEU** - Phai scale toan bo monolith du chi 1 feature can | YEU | Can chuyen sang **Microservices** |
| **ASR-2: Security** | **TOT** - Logic bao mat tap trung tai Business Logic Layer | TOT | Giu nguyen |
| **ASR-3: Modifiability** | **TOT** - Phu thuoc tang nghiem ngat, thay doi doc lap | TOT | Giu nguyen |

**Cach noi:**

> "Danh gia voi 3 ASR tu Lab 01:
>
> **ASR-1 Scalability: YEU.** Day la diem yeu lon nhat cua Layered Architecture. Trong dot sale Black Friday, chi Catalog Service can xu ly nhieu request, nhung voi monolith chung em phai scale TOAN BO ung dung - lang phi tai nguyen.
>
> **ASR-2 Security: TOT.** Logic bao mat duoc tap trung tai **Business Logic Layer**. Bat ke request den tu web UI hay API, deu phai qua Service layer de kiem tra quyen han.
>
> **ASR-3 Modifiability: TOT.** Day la the manh cua Layered Architecture. Nho interface, chung em co the doi database (MySQL → PostgreSQL) hay them Payment Gateway moi ma KHONG anh huong cac tang khac.
>
> **Ket luan:** Layered Architecture la **diem khoi dau tot** de phat trien nhanh, nhung **se can tien hoa thanh Microservices** de giai quyet van de Scalability. Va do chinh la noi dung cua cac Lab tiep theo."

---

### PHAN 9: KET LUAN (1 phut)

**Cach noi:**

> "Tong ket Lab 02, chung em da hoan thanh:
>
> 1. **Dinh nghia 4 tang** voi trach nhiem ro rang va output artifact cu the
> 2. **Xac dinh component** cho Product Catalog: Controller → Service → Repository
> 3. **Dinh nghia Interface** voi Lollipop (provided) va Socket (required) notation
> 4. **UML Component Diagram** the hien cau truc toan he thong
> 5. **Code thuc te** trong folder `app/Lab03/` voi API chay duoc
> 6. **Danh gia trade-offs** - Layered Architecture tot cho Modifiability va Security nhung yeu cho Scalability
>
> **Bai hoc quan trong nhat:**
> - Kien truc khong co **dung** hay **sai** - chi co **phu hop** hay **khong phu hop** voi ASRs cua he thong
> - Layered Architecture phu hop khi bat dau, nhung can **tien hoa** khi yeu cau tang len
>
> **Lien ket Lab 3:** Se implement Layered Architecture nay thanh code hoan chinh voi RESTful API va Unit Tests.
>
> Cam on thay va cac ban da lang nghe."

---

## 3. DEMO CODE THUC TE - DIEM MANH NHAT

### Demo 1: Folder Structure = 4 Layers

```
app/Lab03/
├── Controllers/                         ← LAYER 1: PRESENTATION
│   └── ProductController.php
├── Services/                            ← LAYER 2: BUSINESS LOGIC
│   └── ProductService.php
├── Repositories/                        ← LAYER 3: PERSISTENCE
│   ├── ProductRepositoryInterface.php   ← Interface (Contract)
│   └── ProductRepository.php            ← Implementation
├── Providers/
│   └── Lab03ServiceProvider.php         ← Dependency Injection
└── routes.php                           ← API Route config
```

**Noi:** "Moi folder tuong ung chinh xac voi 1 layer trong kien truc."

### Demo 2: Strict Downward Dependency trong code

**ProductController.php - Layer 1 CHI GOI Layer 2:**
```php
class ProductController extends Controller
{
    protected $productService;  // Chi biet Service, KHONG biet Repository

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function show($id): JsonResponse
    {
        $product = $this->productService->getProductDetails($id);  // Goi Layer 2
        return response()->json($product);
    }
}
```

**ProductService.php - Layer 2 CHI GOI Layer 3 (qua Interface):**
```php
class ProductService
{
    protected $productRepository;  // Chi biet Interface, KHONG biet implementation cu the

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProductDetails(int $id)
    {
        $product = $this->productRepository->findById($id);  // Goi Layer 3
        // + business logic validation...
        return $product;
    }
}
```

**ProductRepository.php - Layer 3 GOI Layer 4 (Database):**
```php
class ProductRepository implements ProductRepositoryInterface
{
    protected $model;  // Eloquent Model → Database

    public function findById(int $id): ?Product
    {
        return $this->model->find($id);  // Goi Layer 4 (MySQL)
    }
}
```

**Noi:** "Nhin vao constructor cua moi class: Controller inject Service, Service inject Repository Interface. KHONG BAO GIO Controller inject truc tiep Repository. Day la Strict Downward Dependency trong code thuc te."

### Demo 3: Interface Pattern (doi database ma khong sua code)

**ProductRepositoryInterface.php:**
```php
interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function getAll(): Collection;
    public function create(array $data): Product;
    public function update(int $id, array $data): ?Product;
    public function delete(int $id): bool;
    public function searchByName(string $keyword): Collection;
}
```

**Lab03ServiceProvider.php (Dependency Injection):**
```php
public function register(): void
{
    $this->app->bind(
        ProductRepositoryInterface::class,
        ProductRepository::class         // ← Doi dong nay de switch implementation
    );
}
```

**Noi:** "Neu ngay mai can doi sang MongoDB, chi can tao `MongoProductRepository` implement cung Interface, roi doi 1 dong trong ServiceProvider. **KHONG SUA** ProductService hay ProductController."

### Demo 4: API Endpoints chay duoc

```
RESTful API da implement:

GET    /api/lab03/products           → Danh sach san pham (co pagination)
GET    /api/lab03/products/{id}      → Chi tiet 1 san pham
GET    /api/lab03/products/search    → Tim kiem theo keyword
POST   /api/lab03/products           → Tao san pham moi
PUT    /api/lab03/products/{id}      → Cap nhat san pham
DELETE /api/lab03/products/{id}      → Xoa san pham
```

**Noi:** "Day KHONG chi la diagram tren giay. Cac API nay thuc su chay duoc va tra ve JSON response."

---

## 4. CAU HOI THUONG GAP VA CACH TRA LOI

### Q1: "Quy tac cua Layered Architecture la gi?"

**Tra loi:**
> "Quy tac chinh la **Strict Downward Dependency** - moi tang CHI DUOC phu thuoc vao tang NGAY BEN DUOI no.
>
> - Layer 1 → Layer 2: DUOC (Controller goi Service)
> - Layer 1 → Layer 3: **KHONG DUOC** (Controller goi truc tiep Repository = nhay tang)
> - Layer 2 → Layer 1: **KHONG DUOC** (Service goi Controller = goi nguoc)
>
> Quy tac nay dam bao thay doi o tang nay khong anh huong tang khac, tuc la **Modifiability**."

### Q2: "Lollipop va Socket la gi?"

**Tra loi:**
> "Day la 2 ky hieu trong UML Component Diagram de the hien Interface:
>
> - **Lollipop** ○── (hinh tron tren duong thang): **Provided Interface** - component nay CUNG CAP dich vu. Vi du: ProductService cung cap `IProductService`.
>
> - **Socket** ◠ (hinh ban nguyet): **Required Interface** - component nay CAN su dung dich vu. Vi du: ProductController can `IProductService` de hoat dong.
>
> Khi Lollipop cua component A khop voi Socket cua component B, ta co mot **dependency** hop le."

### Q3: "Tai sao dung Interface ma khong goi truc tiep class?"

**Tra loi:**
> "Dung Interface de dat **Loose Coupling** - giam su phu thuoc truc tiep giua cac tang.
>
> Vi du cu the: `ProductService` phu thuoc vao `ProductRepositoryInterface`, KHONG phai `ProductRepository`. Nghia la:
> - Hien tai: dung MySQL (ProductRepository)
> - Ngay mai: doi sang MongoDB (MongoProductRepository)
> - Chi can doi 1 dong trong ServiceProvider
> - ProductService KHONG biet va KHONG can biet su thay doi nay
>
> Day chinh la **Dependency Inversion Principle** (chu D trong SOLID) - phu thuoc vao abstraction, khong phu thuoc vao implementation cu the."

### Q4: "Layered Architecture khac gi voi Microservices?"

**Tra loi:**
> "Day la 2 pattern o 2 muc do khac nhau:
>
> | | Layered Architecture | Microservices |
> |---|---|---|
> | Deploy | 1 unit (monolith) | Nhieu unit doc lap |
> | Database | 1 database chung | Database rieng cho moi service |
> | Scale | Scale toan bo | Scale tung service |
> | Complexity | Don gian | Phuc tap hon nhieu |
> | Phu hop | Du an nho-vua, team nho | Du an lon, team lon, can scale |
>
> Layered Architecture la **kien truc BEN TRONG** 1 ung dung. Microservices la **kien truc giua NHIEU** ung dung. Thuc te, moi microservice van co the dung Layered Architecture ben trong no."

### Q5: "Tai sao ASR-1 Scalability bi danh gia YEU?"

**Tra loi:**
> "Vi Layered Architecture la monolith - tat ca tang nam trong 1 ung dung duy nhat.
>
> Vi du: Trong dot sale Black Friday, chi **Product Catalog** can xu ly gap 10 lan request. Nhung voi monolith, chung em phai **scale toan bo ung dung** - bao gom ca Order, User, Review - du chung khong can.
>
> Voi Microservices, chi can them 5 instance cua **Catalog Service** ma KHONG can scale cac service khac. Tiet kiem tai nguyen va hieu qua hon.
>
> Day la ly do Lab sau se tien hoa tu Layered Architecture sang Microservices - de giai quyet chinh ASR-1 nay."

### Q6: "Data Flow di qua 4 tang nhu the nao?"

**Tra loi:**
> "Lay vi du khach hang xem san pham ID = 5:
>
> **Chieu di (Request):**
> 1. Browser gui `GET /products/5`
> 2. **ProductController** nhan request, goi `ProductService`
> 3. **ProductService** kiem tra logic (san pham active khong?), goi `ProductRepository`
> 4. **ProductRepository** chay `SELECT * FROM products WHERE id = 5`
>
> **Chieu ve (Response):**
> 5. **MySQL** tra ve row du lieu
> 6. **ProductRepository** tra ve Product entity cho Service
> 7. **ProductService** validate, transform du lieu cho Controller
> 8. **ProductController** tra ve HTTP 200 + JSON cho Browser
>
> Moi buoc, du lieu duoc **transform** phu hop voi tang do: Controller dung HTTP, Service dung Business Object, Repository dung Database Entity."

### Q7: "Code nay co chay duoc khong?"

**Tra loi:**
> "Co. Chung em da implement day du trong folder `app/Lab03/`. API co the test bang Postman hoac curl:
>
> ```
> curl http://localhost:8000/api/lab03/products
> ```
>
> Tat ca 6 endpoint (list, detail, search, create, update, delete) deu chay duoc va tra ve JSON response."

---

## 5. TIPS GHI DIEM

### DO - Nen lam:

1. **Ve so do 4 tang tren bang** truoc khi trinh bay - giup thay va cac ban hinh dung truc quan
2. **Nhan manh quy tac Strict Downward Dependency** - day la diem cot loi
3. **Chi vao code thuc te** khi noi ve moi tang - chung minh khong chi la ly thuyet
4. **Giai thich Lollipop/Socket** bang tay (ve tren bang) - day la kien thuc thay se hoi
5. **Lien ket voi Lab 01 ASRs** - cho thay su lien tuc giua cac lab
6. **Nhac den Trade-offs** - cho thay ban hieu su danh doi, khong phai kien truc nao cung hoan hao
7. **Su dung tu khoa chuyen nganh**: "Separation of Concerns", "Dependency Inversion", "Loose Coupling"

### DON'T - Khong nen lam:

1. **Khong noi Layered Architecture la hoan hao** - phai nhac den nhuoc diem (Scale kem)
2. **Khong bo qua Interface** - day la diem khac biet giua thiet ke tot va thiet ke te
3. **Khong nham Layered voi MVC** - MVC chi la pattern cua Layer 1 (Presentation), KHONG phai toan bo kien truc
4. **Khong quen lien ket ASR** - moi quyet dinh kien truc phai co ly do tu ASR

### Cau mo dau gay an tuong:

> "Lab 01 chung em da xac dinh 3 ASR quan trong nhat. Lab 02 nay tra loi cau hoi: kien truc nao thoa man chung? Layered Architecture voi 4 tang va quy tac phu thuoc nghiem ngat chinh la cau tra loi - va chung em da implement no thanh CODE THUC TE chay duoc."

### Cau ket thuc gay an tuong:

> "Layered Architecture khong phai la dich cuoi - no la DIEM KHOI DAU. Tot cho Modifiability va Security, nhung yeu cho Scalability. Day la ly do kien truc can TIEN HOA - va hanh trinh tu Layered Monolith den Microservices chinh la chuyen di cua du an chung em qua cac Lab tiep theo."

---

## 6. PHIEU GHI NHO - IN RA CAM TAY

```
+----------------------------------------------+
|          PHIEU GHI NHO - LAB 02              |
+----------------------------------------------+
|                                              |
| LAYERED ARCHITECTURE = 4 TANG               |
|                                              |
| 1. PRESENTATION  → Controller               |
|    (xu ly HTTP, format response)             |
|          ↓ goi xuong (KHONG goi nguoc)       |
| 2. BUSINESS LOGIC → Service                  |
|    (business rules, validation)              |
|          ↓ goi xuong (qua INTERFACE)         |
| 3. PERSISTENCE   → Repository               |
|    (data access, CRUD, SQL query)            |
|          ↓ goi xuong                         |
| 4. DATA          → MySQL Database            |
|    (luu tru vat ly)                          |
|                                              |
| QUY TAC VANG:                                |
| Strict Downward Dependency                   |
| Tang N chi goi Tang N+1 (khong nhay, ko nguoc)|
|                                              |
| UML NOTATION:                                |
| ○── Lollipop = Provided Interface            |
| ◠   Socket   = Required Interface            |
|                                              |
| ASR EVALUATION:                              |
| ASR-1 Scalability  → YEU (phai scale all)   |
| ASR-2 Security     → TOT (tap trung Layer 2)|
| ASR-3 Modifiability→ TOT (interface isolate) |
|                                              |
| CODE: app/Lab03/                             |
| Controllers/ Services/ Repositories/         |
|                                              |
| KEY MESSAGE:                                 |
| "Architecture is about TRADE-OFFS"           |
| "Layered = good start, evolve to MS"         |
+----------------------------------------------+
```

---

## 7. CHIA PHAN TRINH BAY CHO 2 NGUOI

### Nguyen Tuan Thien (23010571):

| Stt | Phan | Noi dung | Thoi gian |
|-----|------|----------|-----------|
| 1 | Mo dau | Gioi thieu Lab 02, lien ket voi ASRs Lab 01 | 1.5 phut |
| 2 | 4 Layers | Dinh nghia 4 tang, trach nhiem, output | 3 phut |
| 3 | Data Flow | 7 buoc request/response qua 4 tang | 2 phut |
| 4 | **Demo Code** | Mo code, chi cau truc folder, demo API | 2 phut |
| 5 | Ket luan | Tong ket va lien ket Lab 3 | 0.5 phut |

### Dang Viet Anh (23010689):

| Stt | Phan | Noi dung | Thoi gian |
|-----|------|----------|-----------|
| 1 | Components | Xac dinh 3 component cho Product Catalog | 3 phut |
| 2 | Interfaces | Dinh nghia IProductService, IProductRepository | 2.5 phut |
| 3 | UML Diagram | Giai thich Component Diagram voi Lollipop/Socket | 3 phut |
| 4 | Trade-offs | Uu/nhuoc diem + ASR Evaluation | 2 phut |
| 5 | Ket luan | Phan ket luan chung | 0.5 phut |

**Luu y:** Ca hai nen hieu TOAN BO noi dung. Dac biet phai nho:
- **4 tang** va trach nhiem moi tang
- **Lollipop/Socket** la gi
- **Tai sao ASR-1 Scalability bi danh gia YEU**

---

## 8. SO SANH BAO CAO VS YEU CAU LAB

| Yeu cau Lab 02 | Trong bao cao .docx | Danh gia |
|----------------|---------------------|----------|
| Dinh nghia 4 Layers voi trach nhiem | Table 2.1: 4 tang day du | DAT |
| Data Flow Definition | Figure 2.1 + Table 2.2: 7 buoc chi tiet | DAT |
| Component Identification | Table 2.3: 3 components cho Product Catalog | DAT |
| Interface Definitions (method signatures) | Table 2.4: IProductService + IProductRepository | DAT |
| UML Component Diagram voi Lollipop/Socket | Figure 2.4 + Figure 2.5 | DAT |
| Folder-to-Layer Mapping | Table 2.5 + Figure 2.3: app/Lab03/ mapping | DAT (BONUS) |
| Trade-offs Analysis | Table 3.1: 3 pros + 3 cons | DAT |
| ASR Evaluation | Table 3.2: 3 ASRs danh gia | DAT |
| Conclusion & Reflection | Section 4: Key deliverables + learnings | DAT |

**Ket luan: Bao cao day du 100% yeu cau. Phan code thuc te (app/Lab03/) va API chay duoc la DIEM CONG LON.**

---

*File nay duoc tao de ho tro thuyet trinh Lab 02. Chuc nhom 16 thuyet trinh thanh cong!*
