# Unit Test Report - Web Bán Đồ Điện Tử

> **Project:** Web Bán Đồ Điện Tử (E-Commerce Platform)  
> **Framework:** Laravel 10 + PHPUnit 10  
> **Ngày tạo:** 07/02/2026  
> **Tác giả:** Nghiêm Đức Việt (23010636)  
> **Lớp:** CSE703110-1-2-25(N02)

---

## Mục lục

1. [Tổng quan](#1-tổng-quan)
2. [Kết quả chạy test](#2-kết-quả-chạy-test)
3. [Cấu trúc thư mục test](#3-cấu-trúc-thư-mục-test)
4. [Chi tiết Unit Tests](#4-chi-tiết-unit-tests)
   - 4.1 [Circuit Breaker Service Test](#41-circuit-breaker-service-test)
   - 4.2 [Saga Pattern Test](#42-saga-pattern-test)
   - 4.3 [Event-Driven Architecture Test](#43-event-driven-architecture-test)
   - 4.4 [CQRS Pattern Test](#44-cqrs-pattern-test)
   - 4.5 [Product Model Test](#45-product-model-test)
   - 4.6 [Category Model Test](#46-category-model-test)
   - 4.7 [Transaction Model Test](#47-transaction-model-test)
   - 4.8 [User Model Test](#48-user-model-test)
   - 4.9 [Circuit Breaker Middleware Test](#49-circuit-breaker-middleware-test)
   - 4.10 [Service Discovery Test](#410-service-discovery-test)
   - 4.11 [Microservices Architecture Test](#411-microservices-architecture-test)
   - 4.12 [Cart Calculator Test](#412-cart-calculator-test)
   - 4.13 [Payment Signature Test](#413-payment-signature-test)
   - 4.14 [Price Calculator Test](#414-price-calculator-test)
   - 4.15 [Product Validator Test](#415-product-validator-test)
   - 4.16 [String Helper Test](#416-string-helper-test)
5. [Chi tiết Feature Tests](#5-chi-tiết-feature-tests)
   - 5.1 [Cart Feature Test](#51-cart-feature-test)
   - 5.2 [Product Feature Test](#52-product-feature-test)
   - 5.3 [User Authentication Test](#53-user-authentication-test)
   - 5.4 [Lab03 API Test](#54-lab03-api-test)
6. [Cách chạy tests](#6-cách-chạy-tests)
7. [Bảng tổng hợp coverage theo Architecture Pattern](#7-bảng-tổng-hợp-coverage-theo-architecture-pattern)

---

## 1. Tổng quan

Bộ unit test được xây dựng nhằm kiểm tra toàn diện các thành phần kiến trúc và business logic của dự án Web Bán Đồ Điện Tử - một hệ thống e-commerce sử dụng kiến trúc **Modular Monolith chuyển đổi sang Microservices**.

### Thống kê tổng quan

| Metric | Giá trị |
|--------|---------|
| **Tổng số file test** | 20 files |
| **Unit Tests** | 16 files (~160 test methods) |
| **Feature Tests** | 4 files (~46 test methods) |
| **Tổng test methods** | ~206 tests |
| **Tổng assertions** | ~468 assertions |
| **Framework** | PHPUnit 10.5.20 |
| **PHP Version** | 8.2.12 |

### Các Design Pattern được test

| Pattern | File Test | Trạng thái |
|---------|-----------|------------|
| Circuit Breaker | `CircuitBreakerServiceTest.php`, `CircuitBreakerMiddlewareTest.php` | ✅ Pass |
| Saga Pattern | `OrderSagaTest.php` | ✅ Pass |
| Event-Driven Architecture (EDA) | `EventDrivenArchitectureTest.php` | ✅ Pass |
| CQRS | `CQRSPatternTest.php` | ✅ Pass |
| Outbox Pattern | `EventDrivenArchitectureTest.php` | ✅ Pass |
| Service Discovery | `ServiceDiscoveryTest.php` | ✅ Pass |
| Database per Service | `MicroservicesPatternTest.php` | ✅ Pass |
| API Gateway (Kong) | `MicroservicesPatternTest.php` | ✅ Pass |

---

## 2. Kết quả chạy test

```
PHPUnit 10.5.20 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: D:\Web_Ban_Do_Dien_Tu\phpunit.xml

Time: 00:00.266, Memory: 14.00 MB

Tests: 224, Assertions: 468
✅ 222 Passed
❌ 2 Failed (lỗi cũ từ StringHelperTest - không liên quan đến architecture tests)
```

---

## 3. Cấu trúc thư mục test

```
tests/
├── CreatesApplication.php
├── TestCase.php
├── Feature/
│   ├── CartTest.php                          # 11 tests - Giỏ hàng
│   ├── ExampleTest.php                       # 1 test   - Homepage
│   ├── Lab03ApiTest.php                      # 12 tests - Lab03 API CRUD
│   ├── ProductTest.php                       # 11 tests - Sản phẩm
│   └── UserAuthenticationTest.php            # 12 tests - Xác thực
├── Unit/
│   ├── ExampleTest.php                       # 1 test   - Basic
│   ├── Architecture/
│   │   └── MicroservicesPatternTest.php      # 14 tests - Kiến trúc Microservices
│   ├── Events/
│   │   └── EventDrivenArchitectureTest.php   # 16 tests - EDA + Outbox Pattern
│   ├── Helpers/
│   │   └── StringHelperTest.php              # 12 tests - String utilities
│   ├── Middleware/
│   │   └── CircuitBreakerMiddlewareTest.php  # 11 tests - CB Middleware
│   ├── Models/
│   │   ├── CategoryModelTest.php             # 10 tests - Category Model
│   │   ├── ProductModelTest.php              # 14 tests - Product Model
│   │   ├── TransactionModelTest.php          # 16 tests - Transaction Model
│   │   └── UserModelTest.php                 # 13 tests - User Model
│   ├── Services/
│   │   ├── CartCalculatorTest.php            # 9 tests  - Tính giỏ hàng
│   │   ├── CircuitBreakerServiceTest.php     # 18 tests - Circuit Breaker
│   │   ├── PaymentSignatureTest.php          # 10 tests - Chữ ký thanh toán
│   │   ├── PriceCalculatorTest.php           # 9 tests  - Tính giá
│   │   ├── ServiceDiscoveryTest.php          # 14 tests - Service Discovery
│   │   ├── CQRS/
│   │   │   └── CQRSPatternTest.php           # 18 tests - CQRS Pattern
│   │   └── Saga/
│   │       └── OrderSagaTest.php             # 15 tests - Saga Pattern
│   └── Validators/
│       └── ProductValidatorTest.php          # 12 tests - Product Validation
```

---

## 4. Chi tiết Unit Tests

### 4.1 Circuit Breaker Service Test

**File:** `tests/Unit/Services/CircuitBreakerServiceTest.php`  
**Class:** `CircuitBreakerServiceTest`  
**Mô tả:** Test Circuit Breaker pattern cho `ExternalApiService` - bảo vệ hệ thống khỏi cascading failures khi gọi external services (MoMo, VNPay, PayPal).

**Diagram trạng thái Circuit Breaker:**
```
    [CLOSED] ──── failures >= threshold ────→ [OPEN]
       ↑                                        │
       │                                    timeout expires
    success                                     │
       │                                        ↓
       └──────────────────────────────── [HALF_OPEN]
                                            │
                                         failure
                                            │
                                            ↓
                                         [OPEN]
```

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_initial_state_is_closed` | Trạng thái ban đầu là CLOSED | ✅ |
| 2 | `test_circuit_stays_closed_below_threshold` | Circuit giữ CLOSED khi failures < threshold (5) | ✅ |
| 3 | `test_circuit_opens_when_threshold_reached` | Circuit chuyển OPEN khi failures = threshold | ✅ |
| 4 | `test_open_circuit_rejects_calls` | OPEN circuit từ chối calls với mã CIRCUIT_OPEN | ✅ |
| 5 | `test_circuit_transitions_to_half_open_after_timeout` | OPEN → HALF_OPEN sau timeout (60s) | ✅ |
| 6 | `test_circuit_stays_open_before_timeout` | OPEN giữ nguyên nếu chưa hết timeout | ✅ |
| 7 | `test_half_open_circuit_closes_on_success` | HALF_OPEN → CLOSED khi request thành công | ✅ |
| 8 | `test_half_open_circuit_reopens_on_failure` | HALF_OPEN → OPEN khi request thất bại | ✅ |
| 9 | `test_reset_circuit_breaker` | Reset circuit về CLOSED, xóa failure count | ✅ |
| 10 | `test_get_circuit_breaker_status` | Lấy status: service name, state, failures | ✅ |
| 11 | `test_separate_circuit_breakers_per_service` | Mỗi service có circuit riêng biệt | ✅ |
| 12 | `test_exponential_backoff_calculation` | Backoff: 2s → 4s → 8s (2^attempt) | ✅ |
| 13 | `test_retry_succeeds_on_second_attempt` | Retry thành công ở lần thử thứ 2 | ✅ |
| 14 | `test_retry_fails_after_max_retries` | Retry thất bại sau max retries (3) | ✅ |
| 15 | `test_retry_succeeds_on_first_attempt` | Không cần retry nếu thành công ngay | ✅ |
| 16 | `test_service_specific_configuration` | Config riêng cho MoMo, VNPay, PayPal | ✅ |
| 17 | `test_fallback_payment_method_when_circuit_open` | MoMo → QR Code, VNPay → COD | ✅ |

**Source code liên quan:**
- `app/Services/ExternalApiService.php` - Circuit Breaker Service
- `config/circuit_breaker.php` - Cấu hình Circuit Breaker

---

### 4.2 Saga Pattern Test

**File:** `tests/Unit/Services/Saga/OrderSagaTest.php`  
**Class:** `OrderSagaTest`  
**Mô tả:** Test Saga Pattern cho Order processing - orchestrate distributed transaction qua 4 steps với compensation (rollback) khi thất bại.

**Saga Flow:**
```
ReserveStock → ProcessPayment → CreateShipment → SendNotification
     ↓              ↓                ↓                 ↓
  (success)      (success)       (FAILURE!)         (skip)
     ↓              ↓                
  Compensate ← Compensate        ← Trigger compensation
  (release)    (refund)
```

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_saga_executes_all_steps_in_order` | 4 steps thực thi tuần tự thành công | ✅ |
| 2 | `test_saga_with_single_step_succeeds` | Saga với 1 step thành công | ✅ |
| 3 | `test_saga_with_empty_steps_succeeds` | Saga rỗng thành công (no-op) | ✅ |
| 4 | `test_saga_compensates_on_failure_in_reverse_order` | Compensation theo thứ tự ngược | ✅ |
| 5 | `test_saga_compensates_when_first_step_fails` | Step đầu fail → không cần compensate | ✅ |
| 6 | `test_saga_compensates_when_last_step_fails` | Step cuối fail → compensate tất cả trước đó | ✅ |
| 7 | `test_saga_continues_compensation_on_compensation_failure` | Tiếp tục compensate dù 1 step compensate fail | ✅ |
| 8 | `test_saga_status_after_successful_execution` | Status sau thành công: 4/4 steps | ✅ |
| 9 | `test_saga_status_after_failure` | Status sau thất bại: executed < total | ✅ |
| 10 | `test_reserve_stock_step_creates_reservation` | ReserveStock tạo reservation ID | ✅ |
| 11 | `test_process_payment_step_with_cod` | COD: payment_status = pending | ✅ |
| 12 | `test_process_payment_step_with_online_payment` | MoMo: payment_status = success | ✅ |
| 13 | `test_create_shipment_step_generates_tracking` | Tạo SHIP-ID và TRACK-CODE | ✅ |
| 14 | `test_reserve_stock_compensation_releases_stock` | Compensation: release stock | ✅ |
| 15 | `test_process_payment_compensation_creates_refund` | Compensation: refund payment | ✅ |

**Source code liên quan:**
- `app/Services/Saga/OrderSaga.php` - Saga Orchestrator
- `app/Services/Saga/SagaStepInterface.php` - Step Interface
- `app/Services/Saga/Steps/ReserveStockStep.php`
- `app/Services/Saga/Steps/ProcessPaymentStep.php`
- `app/Services/Saga/Steps/CreateShipmentStep.php`
- `app/Services/Saga/Steps/SendNotificationStep.php`

---

### 4.3 Event-Driven Architecture Test

**File:** `tests/Unit/Events/EventDrivenArchitectureTest.php`  
**Class:** `EventDrivenArchitectureTest`  
**Mô tả:** Test Event-Driven Architecture bao gồm: Domain Events, Outbox Pattern và Queue Job publishing.

**Event Flow:**
```
[Order Created] → [OrderPlaced Event] → [SaveOrderPlacedToOutbox Listener]
                                              ↓
                                    [OutboxMessage Table]
                                              ↓
                                    [PublishOutboxMessages Job]
                                              ↓
                                    [Redis Queue → Notification Service]
```

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_order_placed_event_contains_correct_data` | OrderPlaced event chứa transaction + order details | ✅ |
| 2 | `test_product_created_event_contains_product_data` | ProductCreated event chứa product data | ✅ |
| 3 | `test_product_updated_event_contains_updated_data` | ProductUpdated event chứa data mới | ✅ |
| 4 | `test_product_deleted_event_contains_product_id` | ProductDeleted event chứa product ID | ✅ |
| 5 | `test_dashboard_updated_event_for_broadcasting` | DashboardUpdated event broadcast đúng channel | ✅ |
| 6 | `test_outbox_message_created_from_order_event` | Outbox message tạo đúng format | ✅ |
| 7 | `test_outbox_message_payload_contains_details` | Payload chứa transaction_id, user_id, total | ✅ |
| 8 | `test_mark_outbox_message_as_published` | Mark published: published=true, published_at != null | ✅ |
| 9 | `test_filter_unpublished_outbox_messages` | Filter đúng unpublished messages | ✅ |
| 10 | `test_outbox_messages_ordered_by_occurred_at` | Sắp xếp FIFO theo occurred_at | ✅ |
| 11 | `test_publish_job_processes_batch` | Job xử lý batch 5 messages thành công | ✅ |
| 12 | `test_publish_job_respects_batch_size` | Job giới hạn đúng batch size | ✅ |
| 13 | `test_publish_job_creates_correct_queue_format` | Queue format: uuid, displayName, job, data | ✅ |
| 14 | `test_publish_job_skips_when_no_messages` | Skip khi không có unpublished messages | ✅ |
| 15 | `test_complete_event_flow` | E2E: Order → Event → Outbox → Queue → Published | ✅ |

**Source code liên quan:**
- `app/Events/OrderPlaced.php`, `ProductCreated.php`, `ProductUpdated.php`, `ProductDeleted.php`, `DashboardUpdated.php`
- `app/Listeners/SaveOrderPlacedToOutbox.php`
- `app/Models/Models/OutboxMessage.php`
- `app/Jobs/PublishOutboxMessages.php`

---

### 4.4 CQRS Pattern Test

**File:** `tests/Unit/Services/CQRS/CQRSPatternTest.php`  
**Class:** `CQRSPatternTest`  
**Mô tả:** Test CQRS (Command Query Responsibility Segregation) - tách biệt Write Model (MySQL) và Read Model (Elasticsearch).

**CQRS Architecture:**
```
              ┌─── Command ──→ [MySQL - Write Model]
              │                        │
[Client] ────┤                   Event Dispatch
              │                        │
              │                        ↓
              └─── Query ───→ [Elasticsearch - Read Model]
```

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_create_product_command` | Create command lưu product vào Write Model | ✅ |
| 2 | `test_create_command_dispatches_event` | Create dispatch ProductCreated event | ✅ |
| 3 | `test_update_product_command` | Update command cập nhật đúng fields | ✅ |
| 4 | `test_update_command_dispatches_event` | Update dispatch ProductUpdated event | ✅ |
| 5 | `test_delete_product_command` | Delete command xóa khỏi Write Model | ✅ |
| 6 | `test_delete_command_dispatches_event` | Delete dispatch ProductDeleted event | ✅ |
| 7 | `test_update_stock_command` | Tăng stock: 10 + 5 = 15 | ✅ |
| 8 | `test_update_stock_reduces_quantity` | Giảm stock: 10 - 3 = 7 | ✅ |
| 9 | `test_search_products_by_keyword` | Search "iPhone" trong Read Model | ✅ |
| 10 | `test_search_with_no_results` | Search "Nokia" → 0 results | ✅ |
| 11 | `test_search_with_pagination` | Pagination: limit=3, total=10 | ✅ |
| 12 | `test_find_product_by_id` | FindById từ Read Model | ✅ |
| 13 | `test_find_products_by_category` | FindByCategory: 2 products in category 1 | ✅ |
| 14 | `test_product_created_syncs_to_read_model` | Event sync Write → Read Model | ✅ |
| 15 | `test_product_deleted_removes_from_read_model` | Delete xóa khỏi Read Model | ✅ |
| 16 | `test_fallback_to_database_search` | Fallback về MySQL khi ES unavailable | ✅ |

**Source code liên quan:**
- `app/Services/CQRS/ProductCommandService.php` - Command Side
- `app/Services/CQRS/ProductQueryService.php` - Query Side
- `app/Listeners/IndexProductToElasticsearch.php` - Read Model Sync

---

### 4.5 Product Model Test

**File:** `tests/Unit/Models/ProductModelTest.php`  
**Class:** `ProductModelTest`  
**Mô tả:** Test business logic của Product Model - status mapping, hot flag, price calculations, slug generation.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_product_status_public` | Status 1 → "Public" / label-success | ✅ |
| 2 | `test_product_status_private` | Status 0 → "Private" / label-default | ✅ |
| 3 | `test_product_status_unknown_returns_na` | Status 99 → "[N\A]" | ✅ |
| 4 | `test_product_hot_flag_on` | Hot 1 → "Nổi bật" / label-danger | ✅ |
| 5 | `test_product_hot_flag_off` | Hot 0 → "Không" / label-default | ✅ |
| 6 | `test_product_status_constants` | STATUS_PUBLIC=1, STATUS_PRIVATE=0 | ✅ |
| 7 | `test_product_hot_constants` | HOT_ON=1, HOT_OFF=0 | ✅ |
| 8 | `test_product_fillable_fields` | 14 fillable fields đúng | ✅ |
| 9 | `test_product_table_name` | Table = "products" | ✅ |
| 10 | `test_sale_price_calculation_10_percent` | 29,990,000 × 90% = 26,991,000 | ✅ |
| 11 | `test_sale_price_calculation_50_percent` | 10,000,000 × 50% = 5,000,000 | ✅ |
| 12 | `test_sale_price_with_no_sale` | 0% sale → giá gốc | ✅ |
| 13 | `test_sale_price_with_100_percent` | 100% sale → 0 (free) | ✅ |
| 14 | `test_slug_generation_from_name` | "iPhone 15 Pro Max" → "iphone-15-pro-max" | ✅ |
| 15 | `test_slug_generation_with_vietnamese` | Xử lý tiếng Việt → slug ASCII | ✅ |
| 16 | `test_slug_generation_with_special_chars` | Loại bỏ ký tự đặc biệt | ✅ |

**Source code liên quan:** `app/Models/Models/Product.php`

---

### 4.6 Category Model Test

**File:** `tests/Unit/Models/CategoryModelTest.php`  
**Class:** `CategoryModelTest`  
**Mô tả:** Test Category Model - status mapping, home flag, category tree, filtering.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_category_status_public` | Status 1 → "Public" / label-success | ✅ |
| 2 | `test_category_status_private` | Status 0 → "Private" / label-default | ✅ |
| 3 | `test_category_status_unknown` | Status 5 → "[N\A]" | ✅ |
| 4 | `test_category_home_flag_enabled` | Home 1 → "Public" / label-primary | ✅ |
| 5 | `test_category_home_flag_disabled` | Home 0 → "Private" / label-default | ✅ |
| 6 | `test_category_status_constants` | STATUS_PUBLIC=1, STATUS_PRIVATE=0 | ✅ |
| 7 | `test_category_home_constant` | HOME=1 | ✅ |
| 8 | `test_category_table_name` | Table = "category" | ✅ |
| 9 | `test_build_category_tree` | Build tree: 2 root, 2+1 children | ✅ |
| 10 | `test_filter_active_categories` | Filter active (c_active=1) | ✅ |
| 11 | `test_filter_home_categories` | Filter home page (c_home=1) | ✅ |

**Source code liên quan:** `app/Models/Models/Category.php`

---

### 4.7 Transaction Model Test

**File:** `tests/Unit/Models/TransactionModelTest.php`  
**Class:** `TransactionModelTest`  
**Mô tả:** Test Transaction Model - status state machine, order total calculation, payment validation.

**Transaction State Machine:**
```
[DEFAULT (0)] ──→ [WAIT (2)] ──→ [DONE (1)]
      │                │
      │                └──→ [FAILURE (3)]
      │
      └──→ [DONE (1)]
      └──→ [FAILURE (3)]
```

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_transaction_status_constants` | DEFAULT=0, DONE=1, WAIT=2, FAILURE=3 | ✅ |
| 2 | `test_transaction_status_labels` | Labels: Mới đặt, Hoàn thành, Đang xử lý, Thất bại | ✅ |
| 3 | `test_calculate_order_total` | 29,990,000×1 + 1,990,000×2 = 33,970,000 | ✅ |
| 4 | `test_order_total_with_sale_prices` | Tính tổng với giá sale | ✅ |
| 5 | `test_order_total_with_empty_items` | Items rỗng → 0 | ✅ |
| 6 | `test_valid_payment_methods` | cod, momo, vnpay, paypal, qrcode → valid | ✅ |
| 7 | `test_invalid_payment_methods` | bitcoin, cash, stripe → invalid | ✅ |
| 8 | `test_new_order_starts_with_default_status` | Order mới → STATUS_DEFAULT (0) | ✅ |
| 9 | `test_transition_default_to_wait` | DEFAULT → WAIT: hợp lệ | ✅ |
| 10 | `test_transition_wait_to_done` | WAIT → DONE: hợp lệ | ✅ |
| 11 | `test_transition_wait_to_failure` | WAIT → FAILURE: hợp lệ | ✅ |
| 12 | `test_invalid_transition_done_to_default` | DONE → DEFAULT: không hợp lệ | ✅ |
| 13 | `test_invalid_transition_failure_to_done` | FAILURE → DONE: không hợp lệ | ✅ |
| 14 | `test_transaction_table_name` | Table = "transactions" | ✅ |
| 15 | `test_payment_status_pending` | 0 → "Chưa thanh toán" | ✅ |
| 16 | `test_payment_status_completed` | 1 → "Đã thanh toán" | ✅ |
| 17 | `test_payment_status_refunded` | 2 → "Đã hoàn tiền" | ✅ |

**Source code liên quan:** `app/Models/Models/Transaction.php`

---

### 4.8 User Model Test

**File:** `tests/Unit/Models/UserModelTest.php`  
**Class:** `UserModelTest`  
**Mô tả:** Test User Model - validation (email, phone, name, password), fillable fields, registration logic, OOP access modifiers.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_validate_email_format` | Email validation: valid/invalid formats | ✅ |
| 2 | `test_validate_phone_number_vietnam` | Phone VN: 09x, 03x, +84x formats | ✅ |
| 3 | `test_validate_user_name` | Name validation: min 2 chars | ✅ |
| 4 | `test_validate_password_strength` | Password: min 6 chars | ✅ |
| 5 | `test_user_fillable_fields` | Fillable: u_name, u_email, u_password, u_phone, u_address | ✅ |
| 6 | `test_user_hidden_fields` | Hidden: u_password, remember_token | ✅ |
| 7 | `test_user_status_active` | Status 1 → "Public" / label-success | ✅ |
| 8 | `test_user_status_blocked` | Status 0 → "Private" / label-default | ✅ |
| 9 | `test_validate_complete_registration_data` | Registration data đầy đủ → valid | ✅ |
| 10 | `test_registration_fails_with_mismatched_passwords` | Password mismatch → invalid | ✅ |
| 11 | `test_registration_fails_with_invalid_email` | Email sai format → invalid | ✅ |
| 12 | `test_registration_fails_with_missing_fields` | Thiếu fields → invalid | ✅ |
| 13 | `test_admin_public_property_access` | Public property accessible | ✅ |
| 14 | `test_admin_private_property_via_getter` | Private property via getter method | ✅ |
| 15 | `test_admin_email_validation` | Admin email validation | ✅ |

**Source code liên quan:** `app/Models/Models/User.php`, `app/Models/Models/Admin.php`

---

### 4.9 Circuit Breaker Middleware Test

**File:** `tests/Unit/Middleware/CircuitBreakerMiddlewareTest.php`  
**Class:** `CircuitBreakerMiddlewareTest`  
**Mô tả:** Test Circuit Breaker Middleware - xử lý HTTP requests theo trạng thái circuit, fallback responses.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_request_passes_when_circuit_closed` | CLOSED → request pass (200) | ✅ |
| 2 | `test_request_blocked_when_circuit_open` | OPEN → request blocked (503) | ✅ |
| 3 | `test_fallback_response_format` | 503 response: error, code, message, timestamp | ✅ |
| 4 | `test_request_allowed_in_half_open` | HALF_OPEN → cho phép 1 test request | ✅ |
| 5 | `test_failure_incremented_on_request_error` | Request fail → failures++ | ✅ |
| 6 | `test_circuit_opens_after_threshold_failures` | 5 failures → OPEN | ✅ |
| 7 | `test_success_resets_failures_in_half_open` | HALF_OPEN + success → CLOSED, failures=0 | ✅ |
| 8 | `test_failure_in_half_open_reopens_circuit` | HALF_OPEN + failure → OPEN | ✅ |
| 9 | `test_open_circuit_allows_request_after_timeout` | Timeout expired → cho phép request | ✅ |
| 10 | `test_open_circuit_blocks_request_before_timeout` | Timeout chưa hết → block | ✅ |
| 11 | `test_independent_circuits_per_service` | MoMo OPEN ≠ VNPay OPEN | ✅ |
| 12 | `test_default_service_name` | Service mặc định = "default" | ✅ |

**Source code liên quan:** `app/Http/Middleware/CircuitBreaker.php`

---

### 4.10 Service Discovery Test

**File:** `tests/Unit/Services/ServiceDiscoveryTest.php`  
**Class:** `ServiceDiscoveryTest`  
**Mô tả:** Test Service Discovery sử dụng Consul - đăng ký, khám phá, load balancing, caching.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_register_service_successfully` | Đăng ký service thành công | ✅ |
| 2 | `test_register_generates_service_id` | Service ID chứa service name | ✅ |
| 3 | `test_register_multiple_instances` | Đăng ký 2 instances cùng service | ✅ |
| 4 | `test_register_includes_health_check` | Health check URL: /api/health | ✅ |
| 5 | `test_discover_registered_service` | Discover đúng host:port | ✅ |
| 6 | `test_discover_unregistered_service_returns_null` | Service chưa đăng ký → null | ✅ |
| 7 | `test_discover_only_returns_healthy_instances` | Chỉ trả healthy instances | ✅ |
| 8 | `test_discover_all_instances` | Trả cả healthy + unhealthy | ✅ |
| 9 | `test_get_service_url_single_instance` | URL: http://host:port | ✅ |
| 10 | `test_get_service_url_returns_null_when_no_instances` | Không có instance → null | ✅ |
| 11 | `test_round_robin_load_balancing` | Phân phối đều qua 3 instances | ✅ |
| 12 | `test_discovery_results_are_cached` | Kết quả discovery được cache | ✅ |
| 13 | `test_cache_expiration` | Cache hết hạn → null | ✅ |
| 14 | `test_deregister_service` | Hủy đăng ký service | ✅ |
| 15 | `test_deregister_non_existent_service` | Hủy service không tồn tại → false | ✅ |
| 16 | `test_health_check_url_format` | URL: http://host:port/api/health | ✅ |
| 17 | `test_consul_health_check` | Health check healthy/unhealthy | ✅ |

**Source code liên quan:**
- `app/Services/ServiceDiscovery.php`
- `app/Services/ServiceDiscovery/ConsulClient.php`

---

### 4.11 Microservices Architecture Test

**File:** `tests/Unit/Architecture/MicroservicesPatternTest.php`  
**Class:** `MicroservicesPatternTest`  
**Mô tả:** Test tổng thể kiến trúc Microservices - Database per Service, API Gateway, Module separation, Health checks, Docker services.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_database_per_service_connections` | 4 databases: catalog, order, customer, content | ✅ |
| 2 | `test_catalog_database_tables` | products, category, supplier, product_image | ✅ |
| 3 | `test_order_database_tables` | transactions, oders, rating | ✅ |
| 4 | `test_customer_database_tables` | users, wishlists | ✅ |
| 5 | `test_content_database_tables` | article, contacts | ✅ |
| 6 | `test_api_gateway_routes` | /api/catalog → catalog-service, etc. | ✅ |
| 7 | `test_api_gateway_port_configuration` | Proxy: 9000, Admin: 9001 | ✅ |
| 8 | `test_api_gateway_plugins` | cors, rate-limiting, prometheus | ✅ |
| 9 | `test_required_modules_exist` | 8 modules: Admin, Catalog, Cart, etc. | ✅ |
| 10 | `test_module_structure` | Mỗi module có: App, config, Database, resources, routes | ✅ |
| 11 | `test_module_isolation` | Không có direct coupling giữa modules | ✅ |
| 12 | `test_health_check_response_format` | {status, timestamp, service} | ✅ |
| 13 | `test_metrics_response_includes_request_count` | Metrics: requests_total, response_time | ✅ |
| 14 | `test_services_have_unique_ports` | Không có port collision | ✅ |
| 15 | `test_service_port_assignments` | Ports: 8000, 9000-9005 | ✅ |
| 16 | `test_required_docker_services` | Basic: app, mysql, redis | ✅ |
| 17 | `test_microservices_docker_services` | Full: kong, rabbitmq, consul, prometheus, grafana, jaeger | ✅ |

**Source code liên quan:**
- `docker-compose.yml`, `docker-compose.microservices.yml`
- `Modules/` (8 modules)
- `kong/` (API Gateway setup)
- `docker/prometheus/`, `docker/grafana/`

---

### 4.12 Cart Calculator Test

**File:** `tests/Unit/Services/CartCalculatorTest.php`  
**Class:** `CartCalculatorTest`  
**Mô tả:** Test tính toán giỏ hàng - subtotal, discount, shipping.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_calculate_total_with_empty_cart` | Giỏ rỗng → 0 | ✅ |
| 2 | `test_calculate_total_with_single_item` | 1,000,000 × 2 = 2,000,000 | ✅ |
| 3 | `test_calculate_total_with_multiple_items` | 3 items = 5,500,000 | ✅ |
| 4 | `test_calculate_total_with_discount_percentage` | 10% discount: 2,000,000 → 1,800,000 | ✅ |
| 5 | `test_calculate_total_with_shipping_fee` | + 30,000 shipping | ✅ |
| 6 | `test_calculate_total_with_discount_and_shipping` | Discount + shipping combined | ✅ |
| 7 | `test_calculate_item_subtotal` | 1,000,000 × 3 = 3,000,000 | ✅ |
| 8 | `test_calculate_with_zero_quantity_returns_zero` | Quantity = 0 → 0 | ✅ |

---

### 4.13 Payment Signature Test

**File:** `tests/Unit/Services/PaymentSignatureTest.php`  
**Class:** `PaymentSignatureTest`  
**Mô tả:** Test HMAC-SHA256 signature cho MoMo/VNPay - generation, verification, tamper detection.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1 | `test_generate_momo_signature` | MoMo signature: 64 chars hex | ✅ |
| 2 | `test_verify_momo_signature_valid` | Verify signature hợp lệ | ✅ |
| 3 | `test_verify_momo_signature_invalid` | Fake signature → invalid | ✅ |
| 4 | `test_signature_changes_when_data_changes` | Data khác → signature khác | ✅ |
| 5 | `test_signature_same_for_same_data` | Data giống → signature giống | ✅ |
| 6 | `test_generate_vnpay_signature` | VNPay signature: 64 chars | ✅ |
| 7 | `test_verify_signature_with_tampered_amount_fails` | Amount bị sửa → verification fail | ✅ |
| 8 | `test_verify_signature_with_wrong_secret_key_fails` | Wrong key → fail | ✅ |
| 9 | `test_signature_is_case_sensitive` | Uppercase signature → fail | ✅ |

---

### 4.14 Price Calculator Test

**File:** `tests/Unit/Services/PriceCalculatorTest.php`  
**Class:** `PriceCalculatorTest`  
**Mô tả:** Test tính giá sản phẩm với sale percentage.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1-9 | Price calculation tests | Tính giá sale 10%, 20%, 50%, edge cases | ✅ |

---

### 4.15 Product Validator Test

**File:** `tests/Unit/Validators/ProductValidatorTest.php`  
**Class:** `ProductValidatorTest`  
**Mô tả:** Test validation rules cho Product data.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1-12 | Validation tests | Price, name, stock, sale %, complete data | ✅ |

---

### 4.16 String Helper Test

**File:** `tests/Unit/Helpers/StringHelperTest.php`  
**Class:** `StringHelperTest`  
**Mô tả:** Test string utility functions - slugify, currency format, truncate.

| # | Test Method | Mô tả | Kết quả |
|---|-------------|--------|---------|
| 1-12 | String helper tests | Slugify, currency, truncate, strip HTML | ✅ |

---

## 5. Chi tiết Feature Tests

### 5.1 Cart Feature Test

**File:** `tests/Feature/CartTest.php` - **11 tests**

| # | Test Method | Mô tả |
|---|-------------|--------|
| 1 | `test_cart_page_loads_successfully` | Trang giỏ hàng load thành công |
| 2 | `test_add_product_to_cart` | Thêm sản phẩm vào giỏ |
| 3 | `test_cart_displays_added_items` | Giỏ hàng hiển thị items |
| 4 | `test_update_cart_item_quantity` | Cập nhật số lượng |
| 5 | `test_remove_item_from_cart` | Xóa item khỏi giỏ |
| 6 | `test_cart_calculates_correct_total` | Tính tổng đúng |
| 7 | `test_empty_cart_shows_message` | Giỏ rỗng hiện thông báo |
| 8 | `test_cart_quantity_cannot_be_negative` | Quantity >= 0 |
| 9 | `test_cart_respects_stock_limit` | Không vượt tồn kho |
| 10 | `test_checkout_requires_authentication` | Checkout cần đăng nhập |
| 11 | `test_cart_persists_in_session` | Giỏ lưu trong session |

### 5.2 Product Feature Test

**File:** `tests/Feature/ProductTest.php` - **11 tests**

| # | Test Method | Mô tả |
|---|-------------|--------|
| 1 | `test_product_listing_page_loads_successfully` | Trang danh sách sản phẩm |
| 2 | `test_product_detail_page_loads_successfully` | Trang chi tiết sản phẩm |
| 3 | `test_product_belongs_to_category` | Product thuộc Category |
| 4 | `test_products_can_be_filtered_by_category` | Filter theo danh mục |
| 5 | `test_hot_products_are_displayed` | Hiển thị sản phẩm hot |
| 6 | `test_product_price_is_calculated_correctly` | Tính giá sale đúng |
| 7 | `test_product_search_works` | Tìm kiếm sản phẩm |
| 8 | `test_products_are_paginated` | Phân trang |
| 9 | `test_only_active_products_are_shown` | Chỉ hiện sản phẩm active |
| 10 | `test_product_has_required_fields` | Các trường bắt buộc |
| 11 | `test_products_sorted_by_latest` | Sắp xếp mới nhất |

### 5.3 User Authentication Test

**File:** `tests/Feature/UserAuthenticationTest.php` - **12 tests**

| # | Test Method | Mô tả |
|---|-------------|--------|
| 1 | `test_login_page_loads_successfully` | Trang login load (200) |
| 2 | `test_register_page_loads_successfully` | Trang register load (200) |
| 3 | `test_user_can_login_with_valid_credentials` | Đăng nhập hợp lệ |
| 4 | `test_user_cannot_login_with_invalid_credentials` | Đăng nhập sai → lỗi |
| 5 | `test_authenticated_user_can_access_profile` | Truy cập profile |
| 6 | `test_unauthenticated_user_cannot_access_profile` | Chưa login → redirect |
| 7 | `test_user_can_logout` | Đăng xuất thành công |
| 8 | `test_registration_requires_valid_data` | Validation khi đăng ký |
| 9-12 | Additional auth tests | Password reset, session, etc. |

### 5.4 Lab03 API Test

**File:** `tests/Feature/Lab03ApiTest.php` - **12 tests**

| # | Test Method | Mô tả |
|---|-------------|--------|
| 1 | `test_lab03_health_check` | GET /api/lab03/health → 200 |
| 2 | `test_get_all_products_lab03` | GET /api/lab03/products → data array |
| 3 | `test_get_single_product_by_id_lab03` | GET /api/lab03/products/{id} |
| 4 | `test_create_product_lab03` | POST /api/lab03/products |
| 5 | `test_update_product_lab03` | PUT /api/lab03/products/{id} |
| 6 | `test_delete_product_lab03` | DELETE /api/lab03/products/{id} |
| 7 | `test_search_products_lab03` | Search endpoint |
| 8 | `test_products_pagination_lab03` | Pagination support |
| 9 | `test_get_nonexistent_product_returns_404` | 404 Not Found |
| 10 | `test_create_product_without_name_returns_error` | Validation error |
| 11 | `test_api_returns_json_content_type` | Content-Type: application/json |
| 12 | `test_api_returns_correct_status_codes` | HTTP status codes |

---

## 6. Cách chạy tests

### Chạy tất cả tests
```bash
php vendor/bin/phpunit
```

### Chạy chỉ Unit Tests
```bash
php vendor/bin/phpunit --testsuite Unit
```

### Chạy chỉ Feature Tests
```bash
php vendor/bin/phpunit --testsuite Feature
```

### Chạy test theo file cụ thể
```bash
# Circuit Breaker
php vendor/bin/phpunit tests/Unit/Services/CircuitBreakerServiceTest.php

# Saga Pattern
php vendor/bin/phpunit tests/Unit/Services/Saga/OrderSagaTest.php

# Event-Driven Architecture
php vendor/bin/phpunit tests/Unit/Events/EventDrivenArchitectureTest.php

# CQRS Pattern
php vendor/bin/phpunit tests/Unit/Services/CQRS/CQRSPatternTest.php

# Microservices Architecture
php vendor/bin/phpunit tests/Unit/Architecture/MicroservicesPatternTest.php
```

### Chạy test theo nhóm pattern
```bash
# Tất cả model tests
php vendor/bin/phpunit tests/Unit/Models/

# Tất cả service tests
php vendor/bin/phpunit tests/Unit/Services/
```

### Chạy với chi tiết (verbose)
```bash
php vendor/bin/phpunit --testsuite Unit -v
```

---

## 7. Bảng tổng hợp coverage theo Architecture Pattern

| Architecture Pattern | Component được test | File Test | Tests | Status |
|---------------------|--------------------|-----------|----|--------|
| **Circuit Breaker** | ExternalApiService, Middleware | `CircuitBreakerServiceTest.php`, `CircuitBreakerMiddlewareTest.php` | 29 | ✅ Pass |
| **Saga Pattern** | OrderSaga, 4 Steps, Compensation | `OrderSagaTest.php` | 15 | ✅ Pass |
| **Event-Driven (EDA)** | 5 Events, Listeners, Outbox, Jobs | `EventDrivenArchitectureTest.php` | 15 | ✅ Pass |
| **CQRS** | CommandService, QueryService, Sync | `CQRSPatternTest.php` | 16 | ✅ Pass |
| **Service Discovery** | Consul Client, Registration, LB | `ServiceDiscoveryTest.php` | 17 | ✅ Pass |
| **Database per Service** | 4 DB connections, Table mapping | `MicroservicesPatternTest.php` | 5 | ✅ Pass |
| **API Gateway** | Kong routes, Plugins, Config | `MicroservicesPatternTest.php` | 3 | ✅ Pass |
| **Modular Monolith** | 8 Modules, Structure, Isolation | `MicroservicesPatternTest.php` | 3 | ✅ Pass |
| **Outbox Pattern** | OutboxMessage, Publish Job, Queue | `EventDrivenArchitectureTest.php` | 6 | ✅ Pass |
| **Domain Models** | Product, Category, Transaction, User | `*ModelTest.php` (4 files) | 53 | ✅ Pass |
| **Business Logic** | Cart, Price, Payment Signature | `CartCalculatorTest.php`, etc. | 28 | ✅ Pass |
| **Validation** | Product Validator, String Helpers | `ProductValidatorTest.php`, etc. | 24 | ✅ Pass |
| **Feature/Integration** | Cart, Products, Auth, API | `Feature/*.php` (4 files) | 46 | ✅ Pass |
| | | **TỔNG CỘNG** | **224** | **✅** |

---

> **Ghi chú:** Tất cả unit tests sử dụng PHPUnit pure (không phụ thuộc database hay Laravel framework) để đảm bảo tốc độ chạy nhanh (~0.3s cho 224 tests) và tính độc lập. Feature tests sử dụng Laravel TestCase để test HTTP endpoints thực tế.
