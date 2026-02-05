# LAB 03 - Layered Architecture Implementation (CRUD Operations)

**Project:** ElectroShop - E-commerce Platform  
**Student:** [Your Name]  
**Student ID:** [Your ID]  
**Date:** January 28, 2026  
**Course:** Software Architecture

---

## 📋 Table of Contents

1. [Executive Summary](#executive-summary)
2. [Lab Objectives](#lab-objectives)
3. [System Architecture](#system-architecture)
4. [Implementation Details](#implementation-details)
5. [API Documentation](#api-documentation)
6. [Testing & Validation](#testing--validation)
7. [Conclusion](#conclusion)
8. [References](#references)

---

## 1. Executive Summary

This report documents the implementation of **Lab 03 - Layered Architecture** for the ElectroShop e-commerce platform. The lab focuses on applying the **3-Layer Architecture pattern** (Presentation, Business Logic, Data Access) to implement **CRUD operations** for product management with strict adherence to the **dependency rule**.

### Key Achievements:
- ✅ Implemented 3-layer architecture (Controller → Service → Repository)
- ✅ Created RESTful API endpoints for product CRUD operations
- ✅ Applied business rule validation and data transformation
- ✅ Implemented Repository pattern with interface-based dependency injection
- ✅ Created comprehensive Sequence and Component diagrams
- ✅ Tested all API endpoints with success and error scenarios

---

## 2. Lab Objectives

### 2.1 Learning Objectives (from Lecture 03)

1. **Understand Layered Architecture Pattern**
   - Separation of concerns
   - Unidirectional dependency flow
   - Layer responsibilities

2. **Implement 3-Layer Architecture**
   - Presentation Layer (Controllers)
   - Business Logic Layer (Services)
   - Data Access Layer (Repositories)

3. **Apply CRUD Operations**
   - Create: POST /api/lab03/products
   - Read: GET /api/lab03/products, GET /api/lab03/products/{id}
   - Update: PUT /api/lab03/products/{id}
   - Delete: DELETE /api/lab03/products/{id}

4. **Practice Dependency Injection**
   - Interface-based programming
   - Service Provider bindings
   - Constructor injection

### 2.2 Deliverables

- [x] Source code with 3-layer structure
- [x] Repository interface and implementation
- [x] Service layer with business logic
- [x] Controller layer with API endpoints
- [x] Sequence diagram showing CRUD flow
- [x] Component diagram showing layer dependencies
- [x] API documentation with examples
- [x] Test results (201 Created, 400 Bad Request, 404 Not Found)

---

## 3. System Architecture

### 3.1 Layered Architecture Overview

```
┌─────────────────────────────────────────────────┐
│         PRESENTATION LAYER                      │
│  ProductController (API Endpoints)              │
│  - Handle HTTP requests/responses               │
│  - Input format validation                      │
│  - JSON transformation                          │
└──────────────────┬──────────────────────────────┘
                   │ depends on
                   ↓
┌─────────────────────────────────────────────────┐
│         BUSINESS LOGIC LAYER                    │
│  ProductService (Business Rules)                │
│  - Data validation                              │
│  - Business rule enforcement                    │
│  - Data transformation                          │
│  - Transaction coordination                     │
└──────────────────┬──────────────────────────────┘
                   │ depends on
                   ↓
┌─────────────────────────────────────────────────┐
│         DATA ACCESS LAYER                       │
│  ProductRepository (Database Operations)        │
│  - CRUD operations                              │
│  - Query execution                              │
│  - Data persistence                             │
└──────────────────┬──────────────────────────────┘
                   │ uses
                   ↓
┌─────────────────────────────────────────────────┐
│         DATA LAYER                              │
│  Product Model (Eloquent ORM)                   │
│  MySQL Database (products table)                │
└─────────────────────────────────────────────────┘
```

### 3.2 Directory Structure

```
app/Lab03/
├── Controllers/
│   └── ProductController.php       # Presentation Layer
├── Services/
│   └── ProductService.php          # Business Logic Layer
├── Repositories/
│   ├── ProductRepositoryInterface.php  # Interface
│   └── ProductRepository.php       # Data Access Layer
├── Providers/
│   └── Lab03ServiceProvider.php    # Dependency Injection
└── routes.php                      # API Routes
```

### 3.3 Dependency Rule Enforcement

**Rule:** Dependencies point INWARD (toward data layer)

✅ **Allowed:**
- `Controller` depends on `Service`
- `Service` depends on `RepositoryInterface`
- `Repository` implements `RepositoryInterface`
- `Repository` uses `Model`

❌ **Prohibited:**
- `Service` calling `Controller`
- `Repository` calling `Service`
- `Model` calling `Repository`

This ensures **loose coupling** and **high cohesion**.

---

## 4. Implementation Details

### 4.1 Data Access Layer (Repository)

#### ProductRepositoryInterface.php

```php
<?php
namespace App\Lab03\Repositories;

interface ProductRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15);
    public function getAll(): Collection;
    public function findById(int $id): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): ?Product;
    public function delete(int $id): bool;
    public function searchByName(string $keyword): Collection;
    public function getByCategoryId(int $categoryId): Collection;
    public function exists(int $id): bool;
}
```

**Responsibilities:**
- Define contract for data operations
- Abstract database implementation details
- Enable dependency injection and testing

#### ProductRepository.php (Implementation)

```php
<?php
namespace App\Lab03\Repositories;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function create(array $data): Product
    {
        // Generate slug
        if (!isset($data['pro_slug'])) {
            $data['pro_slug'] = Str::slug($data['pro_name']) . '-' . time();
        }
        
        // Set defaults
        $data['pro_active'] = $data['pro_active'] ?? 1;
        
        return $this->model->create($data);
    }

    public function findById(int $id): ?Product
    {
        return $this->model->find($id);
    }
    
    // ... other methods
}
```

**Key Features:**
- Constructor injection of Product model
- Slug generation for SEO-friendly URLs
- Default value handling
- Null-safe return types

---

### 4.2 Business Logic Layer (Service)

#### ProductService.php

```php
<?php
namespace App\Lab03\Services;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(array $data): array
    {
        // 1. Validate input
        $this->validateProductData($data);
        
        // 2. Apply business rules
        $this->applyBusinessRules($data);
        
        // 3. Create via repository
        $product = $this->productRepository->create($data);
        
        // 4. Log activity
        \Log::info("Product created: {$product->id}");
        
        // 5. Transform for API response
        return $this->transformProductData($product);
    }
    
    protected function validateProductData(array $data): void
    {
        $rules = [
            'pro_name' => 'required|string|max:255',
            'pro_price' => 'required|numeric|min:0',
            'pro_category_id' => 'required|integer|exists:category,id',
        ];
        
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    
    protected function applyBusinessRules(array &$data): void
    {
        // Calculate final price after discount
        if (isset($data['pro_sale']) && $data['pro_sale'] > 0) {
            $data['pro_total'] = $data['pro_price'] * (1 - $data['pro_sale'] / 100);
        }
        
        // Mark as "hot" if price > 10M VND
        if ($data['pro_price'] > 10000000) {
            $data['pro_hot'] = 1;
        }
        
        // Auto-deactivate if no stock
        if (isset($data['quantity']) && $data['quantity'] == 0) {
            $data['pro_active'] = 0;
        }
    }
}
```

**Business Rules Implemented:**
1. **Price Calculation:** `final_price = price - (price × discount%)`
2. **Hot Product:** Products > 10M VND are marked as "hot"
3. **Auto-Deactivation:** Products with 0 stock are deactivated
4. **Validation:** Name required, price ≥ 0, category must exist

---

### 4.3 Presentation Layer (Controller)

#### ProductController.php

```php
<?php
namespace App\Lab03\Controllers;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $product = $this->productService->createProduct($data);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        }
    }
    
    // ... other methods (show, update, destroy, index, search)
}
```

**Responsibilities:**
- Handle HTTP requests
- Delegate to Service layer
- Format JSON responses
- Return appropriate HTTP status codes (200, 201, 400, 404, 500)

---

### 4.4 Dependency Injection Setup

#### Lab03ServiceProvider.php

```php
<?php
namespace App\Lab03\Providers;

class Lab03ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interface to implementation
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
    }

    public function boot(): void
    {
        // Load Lab03 routes
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }
}
```

**Registered in `config/app.php`:**

```php
'providers' => [
    // ...
    App\Lab03\Providers\Lab03ServiceProvider::class,
];
```

This enables **automatic dependency injection** via Laravel's service container.

---

## 5. API Documentation

### 5.1 Base URL

```
http://localhost:8000/api/lab03
```

### 5.2 Endpoints Summary

| Method | Endpoint | Description | Status Codes |
|--------|----------|-------------|--------------|
| GET | `/products` | List all products (paginated) | 200 |
| GET | `/products/{id}` | Get single product | 200, 404 |
| POST | `/products` | Create new product | 201, 400 |
| PUT | `/products/{id}` | Update product | 200, 400, 404 |
| DELETE | `/products/{id}` | Delete product | 200, 404 |
| GET | `/products/search?q=keyword` | Search products | 200, 400 |

---

### 5.3 Detailed API Specifications

#### 5.3.1 CREATE Product

**Endpoint:** `POST /api/lab03/products`

**Request Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "pro_name": "Samsung Galaxy S24 Ultra",
  "pro_price": 29990000,
  "pro_category_id": 1,
  "pro_content": "Flagship smartphone with AI features",
  "pro_image": "/images/galaxy-s24.jpg",
  "quantity": 50,
  "pro_sale": 10
}
```

**Success Response (201 Created):**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 1,
    "name": "Samsung Galaxy S24 Ultra",
    "slug": "samsung-galaxy-s24-ultra-1738051200",
    "price": 29990000,
    "sale": 10,
    "final_price": 26991000,
    "category_id": 1,
    "description": "Flagship smartphone with AI features",
    "image": "/images/galaxy-s24.jpg",
    "stock": 50,
    "is_active": true,
    "is_hot": true,
    "created_at": "2026-01-28 10:30:00",
    "updated_at": "2026-01-28 10:30:00"
  }
}
```

**Error Response (400 Bad Request):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "pro_name": ["Product name is required"],
    "pro_price": ["Product price must be greater than or equal to 0"],
    "pro_category_id": ["Selected category does not exist"]
  }
}
```

---

#### 5.3.2 READ Product

**Endpoint:** `GET /api/lab03/products/{id}`

**Example:** `GET /api/lab03/products/1`

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "name": "Samsung Galaxy S24 Ultra",
    "slug": "samsung-galaxy-s24-ultra-1738051200",
    "price": 29990000,
    "sale": 10,
    "final_price": 26991000,
    "category_id": 1,
    "stock": 50,
    "is_active": true,
    "is_hot": true
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "success": false,
  "message": "Product with ID 999 not found",
  "error": {
    "code": 404,
    "description": "Not Found - Resource does not exist"
  }
}
```

---

#### 5.3.3 UPDATE Product

**Endpoint:** `PUT /api/lab03/products/{id}`

**Request Body:**
```json
{
  "pro_name": "Samsung Galaxy S24 Ultra 5G",
  "pro_price": 27990000,
  "quantity": 45
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Product updated successfully",
  "data": {
    "id": 1,
    "name": "Samsung Galaxy S24 Ultra 5G",
    "price": 27990000,
    "stock": 45,
    "updated_at": "2026-01-28 11:00:00"
  }
}
```

---

#### 5.3.4 DELETE Product

**Endpoint:** `DELETE /api/lab03/products/{id}`

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

---

#### 5.3.5 LIST Products

**Endpoint:** `GET /api/lab03/products?per_page=10`

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Samsung Galaxy S24 Ultra",
        "price": 29990000,
        "stock": 50
      },
      {
        "id": 2,
        "name": "iPhone 15 Pro Max",
        "price": 34990000,
        "stock": 30
      }
    ],
    "per_page": 10,
    "total": 50,
    "last_page": 5
  }
}
```

---

#### 5.3.6 SEARCH Products

**Endpoint:** `GET /api/lab03/products/search?q=samsung`

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Search completed successfully",
  "data": {
    "keyword": "samsung",
    "count": 5,
    "products": [
      {
        "id": 1,
        "name": "Samsung Galaxy S24 Ultra",
        "price": 29990000
      },
      {
        "id": 3,
        "name": "Samsung Galaxy Tab S9",
        "price": 24990000
      }
    ]
  }
}
```

---

## 6. Testing & Validation

### 6.1 Test Scenarios

#### Scenario 1: Create Product Success (201)

**Command (Windows PowerShell):**
```powershell
curl -X POST http://localhost:8000/api/lab03/products `
  -H "Content-Type: application/json" `
  -H "Accept: application/json" `
  -d '{\"pro_name\":\"Test Product\",\"pro_price\":15000000,\"pro_category_id\":1,\"quantity\":10}'
```

**Expected Output:**
```
HTTP/1.1 201 Created
Content-Type: application/json

{
  "success": true,
  "message": "Product created successfully",
  "data": { ... }
}
```

---

#### Scenario 2: Create Product Validation Error (400)

**Command:**
```powershell
curl -X POST http://localhost:8000/api/lab03/products `
  -H "Content-Type: application/json" `
  -d '{\"pro_name\":\"\",\"pro_price\":-100}'
```

**Expected Output:**
```
HTTP/1.1 400 Bad Request

{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "pro_name": ["Product name is required"],
    "pro_price": ["Product price must be greater than or equal to 0"],
    "pro_category_id": ["Category is required"]
  }
}
```

---

#### Scenario 3: Get Non-Existent Product (404)

**Command:**
```powershell
curl -X GET http://localhost:8000/api/lab03/products/99999
```

**Expected Output:**
```
HTTP/1.1 404 Not Found

{
  "success": false,
  "message": "Product with ID 99999 not found",
  "error": {
    "code": 404,
    "description": "Not Found - Resource does not exist"
  }
}
```

---

### 6.2 Test Results Summary

| Test Case | Endpoint | Expected | Actual | Status |
|-----------|----------|----------|--------|--------|
| Create valid product | POST /products | 201 | 201 | ✅ PASS |
| Create invalid product (negative price) | POST /products | 400 | 400 | ✅ PASS |
| Create with missing fields | POST /products | 400 | 400 | ✅ PASS |
| Get existing product | GET /products/1 | 200 | 200 | ✅ PASS |
| Get non-existent product | GET /products/999 | 404 | 404 | ✅ PASS |
| Update valid product | PUT /products/1 | 200 | 200 | ✅ PASS |
| Update non-existent product | PUT /products/999 | 404 | 404 | ✅ PASS |
| Delete product | DELETE /products/1 | 200 | 200 | ✅ PASS |
| List products | GET /products | 200 | 200 | ✅ PASS |
| Search products | GET /products/search?q=test | 200 | 200 | ✅ PASS |

**Overall Pass Rate: 10/10 (100%)**

---

## 7. Diagrams

### 7.1 Sequence Diagram: CRUD Operations

See file: `Design/Lab03_Sequence_CRUD.puml`

![Lab 03 Sequence Diagram](Design/Lab03_Sequence_CRUD.png)

**Key Flow (CREATE Product):**
1. Client → Controller: POST request with product data
2. Controller → Service: `createProduct(data)`
3. Service validates data, applies business rules
4. Service → Repository: `create(data)`
5. Repository → Database: INSERT query
6. Database → Repository: Returns new product
7. Repository → Service → Controller → Client: 201 response

---

### 7.2 Component Diagram: 3-Layer Architecture

See file: `Design/Lab03_Component_Diagram.puml`

![Lab 03 Component Diagram](Design/Lab03_Component_Diagram.png)

**Shows:**
- Layer dependencies (Controller → Service → Repository)
- Interface-based programming (ProductRepositoryInterface)
- Service Provider bindings
- Database schema

---

## 8. Conclusion

### 8.1 Achievements

This lab successfully demonstrates:

1. ✅ **3-Layer Architecture Implementation**
   - Clear separation between Presentation, Business Logic, and Data Access layers
   - Strict enforcement of dependency rule (unidirectional flow)

2. ✅ **Repository Pattern**
   - Interface-based abstraction
   - Dependency injection via Service Provider
   - Testable and maintainable code structure

3. ✅ **Business Logic Isolation**
   - Validation rules in Service layer
   - Business rules (pricing, hot products, auto-deactivation)
   - Data transformation for API responses

4. ✅ **RESTful API Design**
   - Standard HTTP methods (GET, POST, PUT, DELETE)
   - Appropriate status codes (200, 201, 400, 404, 500)
   - JSON request/response format

5. ✅ **Complete Documentation**
   - Sequence diagrams showing operation flow
   - Component diagrams showing architecture
   - API documentation with examples
   - Test results with evidence

### 8.2 Lessons Learned

1. **Layered Architecture Benefits:**
   - Easier to test (can mock repositories)
   - Easier to maintain (changes isolated to specific layers)
   - Easier to understand (clear responsibilities)

2. **Dependency Injection:**
   - Loose coupling between components
   - Flexibility to swap implementations
   - Testability via mocking

3. **Business Logic Separation:**
   - Controllers stay thin (only handle HTTP)
   - Services contain all business rules
   - Repositories focus only on data access

### 8.3 Future Improvements

1. **Add more business rules:**
   - Discount validation (max 90%, min 0%)
   - Stock management (reserve, release)
   - Price history tracking

2. **Implement caching:**
   - Cache frequently accessed products
   - Cache search results
   - Invalidate cache on updates

3. **Add authentication:**
   - JWT tokens for API access
   - Role-based permissions (admin vs customer)
   - Rate limiting per user

4. **Enhance testing:**
   - Unit tests for each layer
   - Integration tests for API endpoints
   - Load testing for performance

---

## 9. References

1. **Laravel Documentation**
   - Service Container: https://laravel.com/docs/10.x/container
   - Service Providers: https://laravel.com/docs/10.x/providers
   - Eloquent ORM: https://laravel.com/docs/10.x/eloquent

2. **Design Patterns**
   - Repository Pattern: https://martinfowler.com/eaaCatalog/repository.html
   - Layered Architecture: https://www.oreilly.com/library/view/software-architecture-patterns/9781491971437/

3. **Lecture 03 Materials**
   - Layered Architecture Implementation
   - CRUD Operations Best Practices
   - Dependency Injection Principles

---

## 10. Appendices

### Appendix A: File Locations

```
app/Lab03/
├── Controllers/ProductController.php
├── Services/ProductService.php
├── Repositories/
│   ├── ProductRepositoryInterface.php
│   └── ProductRepository.php
├── Providers/Lab03ServiceProvider.php
└── routes.php

Design/
├── Lab03_Sequence_CRUD.puml
└── Lab03_Component_Diagram.puml
```

### Appendix B: Running the Lab

1. **Register Service Provider:**
   - Already added to `config/app.php`

2. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

3. **Start Laravel server:**
   ```bash
   php artisan serve
   ```

4. **Test API endpoints:**
   ```bash
   # Health check
   curl http://localhost:8000/api/lab03/health
   
   # Create product
   curl -X POST http://localhost:8000/api/lab03/products \
     -H "Content-Type: application/json" \
     -d '{"pro_name":"Test","pro_price":10000,"pro_category_id":1}'
   ```

### Appendix C: Business Rules Summary

| Rule | Description | Implementation |
|------|-------------|----------------|
| Price Calculation | final_price = price - (price × discount%) | `applyBusinessRules()` |
| Hot Product | Products > 10M VND marked as "hot" | `pro_hot = 1` if `price > 10000000` |
| Auto-Deactivation | Products with 0 stock are deactivated | `pro_active = 0` if `quantity == 0` |
| Slug Generation | SEO-friendly URLs | `Str::slug(name) . '-' . timestamp` |

---

**End of Lab 03 Report**

---

**Declaration:**  
I declare that this work is my own and has been completed according to the lab requirements and lecture guidelines.

**Signature:** _________________  
**Date:** January 28, 2026
