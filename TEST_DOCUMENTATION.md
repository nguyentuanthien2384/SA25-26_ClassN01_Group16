# TEST DOCUMENTATION - Web Bán Đồ Điện Tử (ElectroShop)

**Project:** Laravel 10 - E-Commerce Platform  
**PHP Version:** ^8.1  
**Test Framework:** PHPUnit ^10.1  
**Total Test Files:** 27  
**Test Suites:** Unit (20 files) | Feature (7 files)

---

## Mục Lục

- [1. Cách Chạy Test](#1-cách-chạy-test)
- [2. Tổng Quan Test](#2-tổng-quan-test)
- [3. Unit Tests](#3-unit-tests)
  - [3.1. Architecture Tests](#31-architecture-tests)
  - [3.2. Model Tests](#32-model-tests)
  - [3.3. Service Tests](#33-service-tests)
  - [3.4. Security Tests](#34-security-tests)
  - [3.5. Middleware Tests](#35-middleware-tests)
  - [3.6. Event Tests](#36-event-tests)
  - [3.7. Helper Tests](#37-helper-tests)
  - [3.8. Validator Tests](#38-validator-tests)
- [4. Feature Tests](#4-feature-tests)
- [5. Test Configuration](#5-test-configuration)

---

## 1. Cách Chạy Test

```bash
# Chạy toàn bộ test
php artisan test

# Chạy riêng Unit tests
php artisan test --testsuite=Unit

# Chạy riêng Feature tests
php artisan test --testsuite=Feature

# Chạy file test cụ thể
php artisan test tests/Unit/Models/ProductModelTest.php

# Chạy method test cụ thể
php artisan test --filter=test_method_name

# Chạy test với output chi tiết
php artisan test -v

# Dừng ngay khi gặp lỗi đầu tiên
php artisan test --stop-on-failure

# Chạy test song song
php artisan test --parallel
```

---

## 2. Tổng Quan Test

| STT | File | Loại | Số Test | Mô tả |
|-----|------|------|---------|--------|
| 1 | `ExampleTest.php` (Unit) | Unit | 1 | Basic PHPUnit example |
| 2 | `MicroservicesPatternTest.php` | Unit | 18 | Microservices architecture patterns |
| 3 | `NFRComplianceTest.php` | Unit | 24 | Non-Functional Requirements compliance |
| 4 | `ComponentArchitectureTest.php` | Unit | 24 | Component existence verification |
| 5 | `ProductModelTest.php` | Unit | 12 | Product model business logic |
| 6 | `CategoryModelTest.php` | Unit | 9 | Category model business logic |
| 7 | `UserModelTest.php` | Unit | 12 | User model validation & registration |
| 8 | `TransactionModelTest.php` | Unit | 16 | Transaction status & state machine |
| 9 | `PaymentSignatureTest.php` | Unit | 8 | HMAC-SHA256 payment signatures |
| 10 | `CircuitBreakerServiceTest.php` | Unit | 15 | Circuit breaker state transitions |
| 11 | `ServiceDiscoveryTest.php` | Unit | 14 | Consul service discovery |
| 12 | `PriceCalculatorTest.php` | Unit | 8 | Price calculation with discounts |
| 13 | `CartCalculatorTest.php` | Unit | 7 | Cart total calculation |
| 14 | `CQRSPatternTest.php` | Unit | 16 | CQRS pattern (command/query) |
| 15 | `OrderSagaTest.php` | Unit | 16 | Saga orchestration & compensation |
| 16 | `GatewaySecurityTest.php` | Unit | 14 | Gateway token & RBAC |
| 17 | `CircuitBreakerMiddlewareTest.php` | Unit | 11 | Circuit breaker middleware |
| 18 | `EventDrivenArchitectureTest.php` | Unit | 14 | Event-driven & outbox pattern |
| 19 | `StringHelperTest.php` | Unit | 11 | String manipulation helpers |
| 20 | `ProductValidatorTest.php` | Unit | 12 | Product validation rules |
| 21 | `ExampleTest.php` (Feature) | Feature | 1 | Application response check |
| 22 | `UserAuthenticationTest.php` | Feature | 10 | User auth flow (login/register) |
| 23 | `ApiGatewayRoutingTest.php` | Feature | 14 | API Gateway routing & endpoints |
| 24 | `ProductTest.php` | Feature | 10 | Product listing & detail pages |
| 25 | `Lab03ApiTest.php` | Feature | 12 | Lab03 RESTful API (CRUD) |
| 26 | `GatewayAuthTest.php` | Feature | 11 | Gateway 401/403/200 responses |
| 27 | `CartTest.php` | Feature | 10 | Cart CRUD & checkout |

---

## 3. Unit Tests

### 3.1. Architecture Tests

#### 3.1.1. MicroservicesPatternTest

**File:** `tests/Unit/Architecture/MicroservicesPatternTest.php`  
**Mô tả:** Kiểm tra các pattern kiến trúc microservices: Database per Service, API Gateway routing, Module separation, Health checks, Metrics.

| Test Method | Mô tả |
|-------------|--------|
| `test_database_per_service_connections` | Mỗi service có database connection riêng (catalog, order, customer, content) |
| `test_catalog_database_tables` | Catalog DB chứa tables: products, category, supplier, product_image |
| `test_order_database_tables` | Order DB chứa tables: transactions, oders, rating |
| `test_customer_database_tables` | Customer DB chứa tables: users, wishlists |
| `test_content_database_tables` | Content DB chứa tables: article, contacts |
| `test_api_gateway_routes` | Kong API Gateway route đúng tới các services |
| `test_api_gateway_port_configuration` | Kong proxy port 9000, admin port 9001 |
| `test_api_gateway_plugins` | Kong plugins: cors, rate-limiting, prometheus |
| `test_required_modules_exist` | 8 modules tồn tại: Admin, Catalog, Cart, Customer, Payment, Review, Content, Support |
| `test_module_structure` | Mỗi module có cấu trúc: App, config, Database, resources, routes |
| `test_module_isolation` | Không có cross-module coupling trực tiếp |
| `test_health_check_response_format` | Health check trả về: status, timestamp, service |
| `test_health_check_includes_service_name` | Health check chứa tên service |
| `test_metrics_response_includes_request_count` | Metrics có requests_total, response_time_avg |
| `test_metrics_response_includes_memory` | Metrics có memory_usage (numeric) |
| `test_services_have_unique_ports` | Không có port collision giữa các services |
| `test_service_port_assignments` | Đúng port: app=8000, kong=9000, order=9002, user=9003, notification=9004, catalog=9005 |
| `test_required_docker_services` | Docker compose có: app, mysql, redis |

---

#### 3.1.2. NFRComplianceTest

**File:** `tests/Unit/Architecture/NFRComplianceTest.php`  
**Mô tả:** Kiểm tra tuân thủ các yêu cầu phi chức năng (NFR): Performance, Scalability, Availability, Security, Monitoring.

| Test Method | NFR | Mô tả |
|-------------|-----|--------|
| `test_product_api_uses_cache_remember` | Performance | ProductApiController sử dụng `Cache::remember` |
| `test_product_api_uses_select_for_query_optimization` | Performance | Sử dụng `::select()` để tối ưu query |
| `test_product_api_uses_eager_loading` | Performance | Sử dụng `->with()` eager loading |
| `test_product_api_uses_pagination` | Performance | Sử dụng pagination |
| `test_api_routes_use_cache_headers` | Performance | API routes có `X-Cache-Status` và `Cache-Control` |
| `test_per_page_has_maximum_limit` | Performance | Giới hạn per_page bằng `min(max())` |
| `test_microservices_docker_compose_has_separate_databases` | Scalability | Databases riêng: catalog_db, order_db, user_db |
| `test_microservices_docker_compose_has_separate_mysql_containers` | Scalability | MySQL containers riêng |
| `test_services_use_environment_variables_for_config` | Scalability | Services cấu hình qua ENV |
| `test_eight_domain_modules_exist` | Scalability | 8 domain modules tồn tại |
| `test_circuit_breaker_has_three_states` | Availability | Circuit breaker: closed, open, half_open |
| `test_circuit_breaker_has_failure_threshold` | Availability | Có failureThreshold |
| `test_external_api_service_has_retry_logic` | Availability | ExternalApiService có retry logic |
| `test_health_check_endpoint_exists_in_routes` | Availability | Route `/health` tồn tại |
| `test_health_check_verifies_database_connection` | Availability | Health check kiểm tra DB connection |
| `test_health_check_verifies_redis_connection` | Availability | Health check kiểm tra Redis |
| `test_cqrs_query_service_has_fallback` | Availability | ProductQueryService có fallback search |
| `test_gateway_middleware_checks_bearer_token` | Security | Middleware kiểm tra Bearer token, trả 401 |
| `test_gateway_controller_enforces_admin_for_writes` | Security | Admin mới được POST/PUT/DELETE, trả 403 |
| `test_csrf_middleware_is_registered` | Security | CSRF middleware tồn tại |
| `test_kernel_registers_gateway_middleware` | Security | Kernel đăng ký gateway.token middleware |
| `test_log_requests_middleware_generates_uuid` | Monitoring | LogRequests tạo UUID, thêm X-Request-ID |
| `test_log_requests_middleware_tracks_duration` | Monitoring | LogRequests theo dõi thời gian (microtime) |
| `test_metrics_endpoint_exists` | Monitoring | Route `/metrics` tồn tại, có `laravel_app_up` |

---

#### 3.1.3. ComponentArchitectureTest

**File:** `tests/Unit/Architecture/ComponentArchitectureTest.php`  
**Mô tả:** Kiểm tra sự tồn tại của tất cả các component kiến trúc: Controllers, Middleware, Services, Models, Events, Docker, Design docs.

| Test Method | Layer | Mô tả |
|-------------|-------|--------|
| `test_gateway_controller_exists` | Presentation | GatewayController tồn tại |
| `test_product_api_controller_exists` | Presentation | ProductApiController tồn tại |
| `test_lab03_product_controller_exists` | Presentation | Lab03 ProductController tồn tại |
| `test_gateway_token_middleware_exists` | Middleware | GatewayTokenMiddleware tồn tại |
| `test_circuit_breaker_middleware_exists` | Middleware | CircuitBreaker middleware tồn tại |
| `test_log_requests_middleware_exists` | Middleware | LogRequests middleware tồn tại |
| `test_product_service_exists` | Business | Lab03 ProductService tồn tại |
| `test_cqrs_command_service_exists` | Business | ProductCommandService tồn tại |
| `test_cqrs_query_service_exists` | Business | ProductQueryService tồn tại |
| `test_saga_orchestrator_exists` | Business | OrderSaga tồn tại |
| `test_saga_step_interface_exists` | Business | SagaStepInterface tồn tại |
| `test_all_saga_steps_exist` | Business | 4 saga steps: ReserveStock, ProcessPayment, CreateShipment, SendNotification |
| `test_external_api_service_exists` | Business | ExternalApiService tồn tại |
| `test_service_discovery_exists` | Business | ServiceDiscovery tồn tại |
| `test_product_repository_interface_exists` | Persistence | ProductRepositoryInterface tồn tại |
| `test_product_repository_implementation_exists` | Persistence | ProductRepository tồn tại |
| `test_repository_interface_defines_required_methods` | Persistence | Interface có: getAllPaginated, findById, create, update, delete, searchByName |
| `test_lab03_service_provider_binds_interface` | Persistence | ServiceProvider bind interface |
| `test_all_required_models_exist` | Data | Models: Product, Category, User, Transaction, Order, Cart, OutboxMessage |
| `test_order_placed_event_exists` | Event | OrderPlaced event tồn tại |
| `test_product_events_exist` | Event | ProductCreated, ProductUpdated, ProductDeleted events tồn tại |
| `test_outbox_listener_exists` | Event | SaveOrderPlacedToOutbox listener tồn tại |
| `test_publish_outbox_job_exists` | Event | PublishOutboxMessages job tồn tại |
| `test_all_eight_modules_exist` | Modules | 8 modules tồn tại |

---

### 3.2. Model Tests

#### 3.2.1. ProductModelTest

**File:** `tests/Unit/Models/ProductModelTest.php`  
**Mô tả:** Kiểm tra business logic của Product Model: Status mapping, Hot flag, Price calculations, Slug generation.

| Test Method | Mô tả |
|-------------|--------|
| `test_product_status_public` | Status 1 = "Public" với class "label-success" |
| `test_product_status_private` | Status 0 = "Private" với class "label-default" |
| `test_product_status_unknown_returns_na` | Status không xác định trả về "[N\A]" |
| `test_product_hot_flag_on` | Hot 1 = "Nổi bật" với class "label-danger" |
| `test_product_hot_flag_off` | Hot 0 = "Không" với class "label-default" |
| `test_product_status_constants` | STATUS_PUBLIC = 1, STATUS_PRIVATE = 0 |
| `test_product_hot_constants` | HOT_ON = 1, HOT_OFF = 0 |
| `test_product_fillable_fields` | Fillable chứa: pro_name, pro_slug, pro_price, pro_sale, pro_category_id, ... |
| `test_product_table_name` | Table name = "products" |
| `test_sale_price_calculation_10_percent` | 29,990,000 giảm 10% = 26,991,000 |
| `test_sale_price_calculation_50_percent` | 10,000,000 giảm 50% = 5,000,000 |
| `test_slug_generation_from_name` | "iPhone 15 Pro Max 256GB" → "iphone-15-pro-max-256gb" |

---

#### 3.2.2. CategoryModelTest

**File:** `tests/Unit/Models/CategoryModelTest.php`  
**Mô tả:** Kiểm tra Category Model: Status mapping, Home flag, Category tree, Filters.

| Test Method | Mô tả |
|-------------|--------|
| `test_category_status_public` | Status 1 = "Public" |
| `test_category_status_private` | Status 0 = "Private" |
| `test_category_status_unknown` | Status không xác định trả về "[N\A]" |
| `test_category_home_flag_enabled` | Home 1 = "Public" với class "label-primary" |
| `test_category_home_flag_disabled` | Home 0 = "Private" |
| `test_category_status_constants` | STATUS_PUBLIC = 1, STATUS_PRIVATE = 0 |
| `test_category_home_constant` | HOME = 1 |
| `test_category_table_name` | Table name = "category" |
| `test_build_category_tree` | Build cây danh mục từ parent-child relationship |

---

#### 3.2.3. UserModelTest

**File:** `tests/Unit/Models/UserModelTest.php`  
**Mô tả:** Kiểm tra User Model: Validation (email, phone, name, password), Registration, Fillable/Hidden fields.

| Test Method | Mô tả |
|-------------|--------|
| `test_validate_email_format` | Validate email (user@example.com = true, invalid-email = false) |
| `test_validate_phone_number_vietnam` | Validate SĐT Việt Nam (0912345678 hoặc +84912345678) |
| `test_validate_user_name` | Validate tên (tối thiểu 2 ký tự) |
| `test_validate_password_strength` | Validate mật khẩu (tối thiểu 6 ký tự) |
| `test_user_fillable_fields` | Fillable: u_name, u_email, u_password, u_phone, u_address |
| `test_user_hidden_fields` | Hidden: u_password, remember_token |
| `test_user_status_active` | Status 1 = "Public" (active) |
| `test_user_status_blocked` | Status 0 = "Private" (blocked) |
| `test_validate_complete_registration_data` | Validate full registration data thành công |
| `test_registration_fails_with_mismatched_passwords` | Đăng ký thất bại khi mật khẩu không khớp |
| `test_registration_fails_with_invalid_email` | Đăng ký thất bại khi email không hợp lệ |
| `test_admin_public_property_access` | Truy cập thuộc tính public của Admin model |

---

#### 3.2.4. TransactionModelTest

**File:** `tests/Unit/Models/TransactionModelTest.php`  
**Mô tả:** Kiểm tra Transaction Model: Status constants, Order total, Payment methods, State machine.

| Test Method | Mô tả |
|-------------|--------|
| `test_transaction_status_constants` | STATUS_DEFAULT=0, STATUS_DONE=1, STATUS_WAIT=2, STATUS_FAILURE=3 |
| `test_transaction_status_labels` | 0="Mới đặt", 1="Hoàn thành", 2="Đang xử lý", 3="Thất bại" |
| `test_calculate_order_total` | Tính tổng đơn hàng từ items |
| `test_order_total_with_sale_prices` | Tính tổng với giá giảm |
| `test_order_total_with_empty_items` | Giỏ hàng rỗng = 0 |
| `test_valid_payment_methods` | Phương thức hợp lệ: cod, momo, vnpay, paypal, qrcode |
| `test_invalid_payment_methods` | Phương thức không hợp lệ: bitcoin, cash, stripe |
| `test_new_order_starts_with_default_status` | Đơn mới bắt đầu với status 0 |
| `test_transition_default_to_wait` | Chuyển trạng thái 0 → 2 (Đang xử lý) |
| `test_transition_wait_to_done` | Chuyển trạng thái 2 → 1 (Hoàn thành) |
| `test_transition_wait_to_failure` | Chuyển trạng thái 2 → 3 (Thất bại) |
| `test_invalid_transition_done_to_default` | Không được chuyển 1 → 0 |
| `test_invalid_transition_failure_to_done` | Không được chuyển 3 → 1 |
| `test_payment_status_pending` | Payment status 0 = "Chưa thanh toán" |
| `test_payment_status_completed` | Payment status 1 = "Đã thanh toán" |
| `test_payment_status_refunded` | Payment status 2 = "Đã hoàn tiền" |

---

### 3.3. Service Tests

#### 3.3.1. PaymentSignatureTest

**File:** `tests/Unit/Services/PaymentSignatureTest.php`  
**Mô tả:** Kiểm tra HMAC-SHA256 signature generation cho MoMo/VNPay. Critical cho PCI DSS compliance.

| Test Method | Mô tả |
|-------------|--------|
| `test_generate_momo_signature` | Signature MoMo không rỗng, 64 ký tự hex |
| `test_verify_momo_signature_valid` | Verify signature hợp lệ → true |
| `test_verify_momo_signature_invalid` | Verify signature giả → false |
| `test_signature_changes_when_data_changes` | Data thay đổi → signature thay đổi |
| `test_signature_same_for_same_data` | Data giống → signature giống |
| `test_generate_vnpay_signature` | Tạo signature VNPay (64 ký tự) |
| `test_verify_signature_with_tampered_amount_fails` | Sửa amount → verify thất bại |
| `test_verify_signature_with_wrong_secret_key_fails` | Sai secret key → verify thất bại |

---

#### 3.3.2. CircuitBreakerServiceTest

**File:** `tests/Unit/Services/CircuitBreakerServiceTest.php`  
**Mô tả:** Kiểm tra Circuit Breaker pattern: CLOSED → OPEN → HALF_OPEN → CLOSED, failure counting, timeout, exponential backoff.

| Test Method | Mô tả |
|-------------|--------|
| `test_initial_state_is_closed` | Trạng thái ban đầu là CLOSED |
| `test_circuit_stays_closed_below_threshold` | < threshold failures → vẫn CLOSED |
| `test_circuit_opens_when_threshold_reached` | = threshold failures → OPEN |
| `test_open_circuit_rejects_calls` | OPEN → reject calls (CIRCUIT_OPEN) |
| `test_circuit_transitions_to_half_open_after_timeout` | OPEN + timeout → HALF_OPEN |
| `test_circuit_stays_open_before_timeout` | OPEN + chưa timeout → vẫn OPEN |
| `test_half_open_circuit_closes_on_success` | HALF_OPEN + success → CLOSED |
| `test_half_open_circuit_reopens_on_failure` | HALF_OPEN + failure → OPEN |
| `test_reset_circuit_breaker` | Reset circuit → CLOSED, failures = 0 |
| `test_separate_circuit_breakers_per_service` | Mỗi service có circuit breaker riêng |
| `test_exponential_backoff_calculation` | Backoff: 2^1=2s, 2^2=4s, 2^3=8s |
| `test_retry_succeeds_on_second_attempt` | Retry lần 2 thành công |
| `test_retry_fails_after_max_retries` | Thất bại sau max retries |
| `test_service_specific_configuration` | Config riêng cho momo, vnpay (threshold, timeout, fallback) |
| `test_fallback_payment_method_when_circuit_open` | MoMo fail → QR Code, VNPay fail → COD |

---

#### 3.3.3. ServiceDiscoveryTest

**File:** `tests/Unit/Services/ServiceDiscoveryTest.php`  
**Mô tả:** Kiểm tra Service Discovery (Consul): Registration, Discovery, Load Balancing, Caching, Health Check.

| Test Method | Mô tả |
|-------------|--------|
| `test_register_service_successfully` | Đăng ký service thành công |
| `test_register_generates_service_id` | Service ID chứa tên service |
| `test_register_multiple_instances` | Đăng ký nhiều instances cùng service |
| `test_register_includes_health_check` | Đăng ký kèm health check config |
| `test_discover_registered_service` | Tìm service đã đăng ký |
| `test_discover_unregistered_service_returns_null` | Service chưa đăng ký → null |
| `test_discover_only_returns_healthy_instances` | Chỉ trả về instances healthy |
| `test_get_service_url_single_instance` | Lấy URL service (http://host:port) |
| `test_round_robin_load_balancing` | Load balancing round-robin qua 3 instances |
| `test_discovery_results_are_cached` | Kết quả discovery được cache |
| `test_cache_expiration` | Cache hết hạn → null |
| `test_deregister_service` | Hủy đăng ký service |
| `test_health_check_url_format` | URL format: http://host:port/api/health |
| `test_consul_health_check` | Mô phỏng Consul health check |

---

#### 3.3.4. PriceCalculatorTest

**File:** `tests/Unit/Services/PriceCalculatorTest.php`  
**Mô tả:** Kiểm tra tính giá sản phẩm với khuyến mãi.

| Test Method | Mô tả |
|-------------|--------|
| `test_calculate_final_price_without_sale` | Không giảm giá → giá gốc |
| `test_calculate_final_price_with_10_percent_sale` | Giảm 10%: 10M → 9M |
| `test_calculate_final_price_with_50_percent_sale` | Giảm 50%: 20M → 10M |
| `test_calculate_final_price_with_100_percent_sale` | Giảm 100%: 5M → 0 |
| `test_calculate_final_price_throws_exception_for_negative_sale` | Sale < 0 → InvalidArgumentException |
| `test_calculate_final_price_throws_exception_for_sale_over_100` | Sale > 100 → InvalidArgumentException |
| `test_calculate_sale_amount` | Tính số tiền giảm: 10M * 20% = 2M |
| `test_calculate_final_price_with_decimal_sale` | Giảm 15.5%: 10M → 8.45M |

---

#### 3.3.5. CartCalculatorTest

**File:** `tests/Unit/Services/CartCalculatorTest.php`  
**Mô tả:** Kiểm tra logic tính tổng giỏ hàng (không database, không HTTP).

| Test Method | Mô tả |
|-------------|--------|
| `test_calculate_total_with_empty_cart` | Giỏ rỗng = 0 |
| `test_calculate_total_with_single_item` | 1 item: 1M × 2 = 2M |
| `test_calculate_total_with_multiple_items` | 3 items = 5.5M |
| `test_calculate_total_with_discount_percentage` | Giảm 10%: 2M → 1.8M |
| `test_calculate_total_with_shipping_fee` | Phí ship: 2M + 30K = 2.03M |
| `test_calculate_total_with_discount_and_shipping` | Giảm 10% + ship: 1.83M |
| `test_calculate_with_zero_quantity_returns_zero` | Quantity = 0 → total = 0 |

---

#### 3.3.6. CQRSPatternTest

**File:** `tests/Unit/Services/CQRS/CQRSPatternTest.php`  
**Mô tả:** Kiểm tra CQRS (Command Query Responsibility Segregation): Write model (MySQL), Read model (Elasticsearch), Event sync.

| Test Method | Mô tả |
|-------------|--------|
| `test_create_product_command` | Tạo sản phẩm → write model |
| `test_create_command_dispatches_event` | Create → dispatch ProductCreated event |
| `test_update_product_command` | Cập nhật giá, giữ nguyên tên |
| `test_update_command_dispatches_event` | Update → dispatch ProductUpdated event |
| `test_delete_product_command` | Xóa sản phẩm khỏi write model |
| `test_delete_command_dispatches_event` | Delete → dispatch ProductDeleted event |
| `test_update_stock_command` | Cập nhật tồn kho: 10 + 5 = 15 |
| `test_update_stock_reduces_quantity` | Giảm tồn kho: 10 - 3 = 7 |
| `test_search_products_by_keyword` | Tìm kiếm từ read model theo keyword |
| `test_search_with_no_results` | Không tìm thấy → products rỗng |
| `test_search_with_pagination` | Phân trang: limit 3, total 10 |
| `test_find_product_by_id` | Tìm theo ID từ read model |
| `test_find_products_by_category` | Tìm theo category |
| `test_product_created_syncs_to_read_model` | Event sync create → read model |
| `test_product_deleted_removes_from_read_model` | Event sync delete → xóa khỏi read model |
| `test_fallback_to_database_search` | Elasticsearch unavailable → fallback DB |

---

#### 3.3.7. OrderSagaTest

**File:** `tests/Unit/Services/Saga/OrderSagaTest.php`  
**Mô tả:** Kiểm tra Saga pattern: Execute steps tuần tự, compensate ngược khi failure.  
**Steps:** ReserveStock → ProcessPayment → CreateShipment → SendNotification

| Test Method | Mô tả |
|-------------|--------|
| `test_saga_executes_all_steps_in_order` | Happy path: 4 steps thực thi đúng thứ tự |
| `test_saga_with_single_step_succeeds` | 1 step → success |
| `test_saga_with_empty_steps_succeeds` | 0 steps → success |
| `test_saga_compensates_on_failure_in_reverse_order` | Fail step 3 → compensate: Payment → Stock (reverse) |
| `test_saga_compensates_when_first_step_fails` | Fail step 1 → không có gì để compensate |
| `test_saga_compensates_when_last_step_fails` | Fail step 4 → compensate: Shipment → Payment → Stock |
| `test_saga_continues_compensation_on_compensation_failure` | Compensation fail → vẫn tiếp tục compensate các step khác |
| `test_saga_status_after_successful_execution` | Status sau khi thành công |
| `test_saga_status_after_failure` | Status sau khi thất bại |
| `test_reserve_stock_step_creates_reservation` | ReserveStock tạo reservation (RES-xxx) |
| `test_process_payment_step_with_cod` | COD payment → status pending, order wait |
| `test_process_payment_step_with_online_payment` | Online payment → status success |
| `test_create_shipment_step_generates_tracking` | CreateShipment → SHIP-xxx, TRACK-xxx |
| `test_reserve_stock_compensation_releases_stock` | Compensate stock → release stock |
| `test_process_payment_compensation_creates_refund` | Compensate payment → refund, cancelled |

---

### 3.4. Security Tests

#### 3.4.1. GatewaySecurityTest

**File:** `tests/Unit/Security/GatewaySecurityTest.php`  
**Mô tả:** Kiểm tra Gateway Security: Token parsing, RBAC authorization.

| Test Method | Mô tả |
|-------------|--------|
| `test_valid_admin_token_is_recognized` | "valid-admin-token" → role "admin" |
| `test_valid_user_token_is_recognized` | "valid-user-token" → role "user" |
| `test_invalid_token_returns_null` | Token không hợp lệ → null |
| `test_bearer_prefix_is_stripped_correctly` | Strip "Bearer " prefix |
| `test_authorization_header_must_start_with_bearer` | "Bearer " phân biệt hoa thường |
| `test_admin_can_perform_get` | Admin: GET ✓ |
| `test_admin_can_perform_post` | Admin: POST ✓ |
| `test_admin_can_perform_put` | Admin: PUT ✓ |
| `test_admin_can_perform_delete` | Admin: DELETE ✓ |
| `test_user_can_perform_get` | User: GET ✓ |
| `test_user_cannot_perform_post` | User: POST ✗ |
| `test_user_cannot_perform_put` | User: PUT ✗ |
| `test_user_cannot_perform_delete` | User: DELETE ✗ |
| `test_full_rbac_matrix` | Kiểm tra toàn bộ RBAC matrix |

---

### 3.5. Middleware Tests

#### 3.5.1. CircuitBreakerMiddlewareTest

**File:** `tests/Unit/Middleware/CircuitBreakerMiddlewareTest.php`  
**Mô tả:** Kiểm tra Circuit Breaker Middleware: Request handling, Failure tracking, State transitions, Fallback response.

| Test Method | Mô tả |
|-------------|--------|
| `test_request_passes_when_circuit_closed` | CLOSED → request pass (200) |
| `test_request_blocked_when_circuit_open` | OPEN → request blocked (503) |
| `test_fallback_response_format` | Fallback: error="Service Unavailable", code="CIRCUIT_OPEN" |
| `test_request_allowed_in_half_open` | HALF_OPEN → cho phép 1 test request |
| `test_failure_incremented_on_request_error` | Request fail → failure count +1 |
| `test_circuit_opens_after_threshold_failures` | 5 failures → OPEN |
| `test_success_resets_failures_in_half_open` | HALF_OPEN + success → CLOSED, failures = 0 |
| `test_failure_in_half_open_reopens_circuit` | HALF_OPEN + failure → OPEN |
| `test_open_circuit_allows_request_after_timeout` | OPEN + timeout → cho phép request |
| `test_open_circuit_blocks_request_before_timeout` | OPEN + chưa timeout → block (503) |
| `test_independent_circuits_per_service` | MoMo OPEN, VNPay vẫn CLOSED |

---

### 3.6. Event Tests

#### 3.6.1. EventDrivenArchitectureTest

**File:** `tests/Unit/Events/EventDrivenArchitectureTest.php`  
**Mô tả:** Kiểm tra Event-Driven Architecture: Events, Outbox pattern, Publishing flow.

| Test Method | Mô tả |
|-------------|--------|
| `test_order_placed_event_contains_correct_data` | OrderPlaced event chứa transaction + order details |
| `test_product_created_event_contains_product_data` | ProductCreated event chứa product data |
| `test_product_updated_event_contains_updated_data` | ProductUpdated event chứa data mới |
| `test_product_deleted_event_contains_product_id` | ProductDeleted event chứa product_id |
| `test_dashboard_updated_event_for_broadcasting` | DashboardUpdated event broadcast qua channel "dashboard" |
| `test_outbox_message_created_from_order_event` | Outbox message: aggregate_type, event_type, published=false |
| `test_outbox_message_payload_contains_details` | Payload chứa: transaction_id, user_id, total, payment_method |
| `test_mark_outbox_message_as_published` | Mark published → published=true, published_at set |
| `test_filter_unpublished_outbox_messages` | Lọc messages chưa publish |
| `test_outbox_messages_ordered_by_occurred_at` | Sắp xếp theo thời gian (FIFO) |
| `test_publish_job_processes_batch` | Job xử lý batch 5 messages |
| `test_publish_job_respects_batch_size` | Job giới hạn batch size |
| `test_publish_job_creates_correct_queue_format` | Queue format: uuid, displayName, job, data |
| `test_complete_event_flow` | Full flow: Order → Event → Outbox → Queue → Mark Published |

---

### 3.7. Helper Tests

#### 3.7.1. StringHelperTest

**File:** `tests/Unit/Helpers/StringHelperTest.php`  
**Mô tả:** Kiểm tra các hàm xử lý chuỗi.

| Test Method | Mô tả |
|-------------|--------|
| `test_slugify_converts_text_to_slug` | "iPhone 15 Pro Max" → "iphone-15-pro-max" |
| `test_slugify_handles_special_characters` | "Product @#$% 2024!!!" → "product-2024" |
| `test_slugify_converts_uppercase_to_lowercase` | "SAMSUNG GALAXY S24 ULTRA" → lowercase |
| `test_slugify_handles_multiple_spaces` | Nhiều khoảng trắng → 1 dấu gạch |
| `test_slugify_trims_dashes` | Bỏ dấu gạch đầu/cuối |
| `test_format_currency_vnd` | 10,000,000 → "10.000.000 ₫" |
| `test_format_currency_with_zero` | 0 → "0 ₫" |
| `test_format_currency_with_large_number` | 123,456,789 → "123.456.789 ₫" |
| `test_truncate_text_to_length` | Cắt text + "..." |
| `test_truncate_short_text_returns_original` | Text ngắn → giữ nguyên |
| `test_strip_html_tags` | Bỏ HTML tags |

---

### 3.8. Validator Tests

#### 3.8.1. ProductValidatorTest

**File:** `tests/Unit/Validators/ProductValidatorTest.php`  
**Mô tả:** Kiểm tra validation rules cho Product.

| Test Method | Mô tả |
|-------------|--------|
| `test_validate_product_price_positive_passes` | Giá > 0 → valid |
| `test_validate_product_price_zero_fails` | Giá = 0 → invalid |
| `test_validate_product_price_negative_fails` | Giá < 0 → invalid |
| `test_validate_product_name_required_passes` | Tên sản phẩm có giá trị → valid |
| `test_validate_empty_product_name_fails` | Tên rỗng → invalid |
| `test_validate_product_name_minimum_length` | Tên < 3 ký tự → invalid |
| `test_validate_stock_quantity_non_negative_passes` | Tồn kho >= 0 → valid |
| `test_validate_stock_quantity_zero_passes` | Tồn kho = 0 (hết hàng) → valid |
| `test_validate_stock_quantity_negative_fails` | Tồn kho < 0 → invalid |
| `test_validate_sale_percentage_in_range_passes` | Sale 0-100 → valid |
| `test_validate_sale_percentage_negative_fails` | Sale < 0 → invalid |
| `test_validate_sale_percentage_over_100_fails` | Sale > 100 → invalid |
| `test_validate_complete_product_data_passes` | Full data hợp lệ → valid |
| `test_validate_product_with_missing_fields_fails` | Thiếu fields → invalid |

---

## 4. Feature Tests

### 4.1. ExampleTest

**File:** `tests/Feature/ExampleTest.php`

| Test Method | Mô tả |
|-------------|--------|
| `test_the_application_returns_a_successful_response` | GET `/` trả về 200 |

---

### 4.2. UserAuthenticationTest

**File:** `tests/Feature/UserAuthenticationTest.php`  
**Mô tả:** Kiểm tra luồng xác thực người dùng: Login, Register, Logout, Password Reset.

| Test Method | Mô tả |
|-------------|--------|
| `test_login_page_loads_successfully` | GET `/login` → 200, view `auth.login` |
| `test_register_page_loads_successfully` | GET `/register` → 200, view `auth.register` |
| `test_user_can_login_with_valid_credentials` | Login với credentials hợp lệ (skipped) |
| `test_user_cannot_login_with_invalid_credentials` | Login sai → redirect/422 |
| `test_user_profile_requires_authentication` | GET `/user/user` → cần đăng nhập |
| `test_authenticated_user_can_access_profile` | User đã login → truy cập profile |
| `test_user_can_logout` | POST `/logout` → redirect |
| `test_user_registration_requires_valid_data` | Register với data thiếu → validation error |
| `test_user_can_register_with_valid_data` | Register thành công → redirect/201 |
| `test_duplicate_email_registration_fails` | Email trùng → validation error |

---

### 4.3. ApiGatewayRoutingTest

**File:** `tests/Feature/ApiGatewayRoutingTest.php`  
**Mô tả:** Kiểm tra API Gateway routing: Health, Ping, Products, Cache, Metrics.

| Test Method | Mô tả |
|-------------|--------|
| `test_health_endpoint_returns_json` | GET `/api/health` → JSON (status, timestamp, checks) |
| `test_health_endpoint_reports_healthy_status` | Health status = "healthy" hoặc "unhealthy" |
| `test_ping_endpoint_returns_pong` | GET `/api/ping` → {"message": "pong"} |
| `test_products_endpoint_returns_paginated_list` | GET `/api/products` → paginated (current_page, data, per_page, total) |
| `test_products_endpoint_respects_per_page_parameter` | `?per_page=5` → max 5 items |
| `test_products_endpoint_max_per_page_is_60` | `?per_page=100` → bị limit thành 60 |
| `test_products_endpoint_supports_search` | `?search=Laptop` → tìm kiếm |
| `test_products_endpoint_supports_category_filter` | `?category=1` → lọc theo danh mục |
| `test_products_endpoint_supports_sorting` | Sort: newest, price_asc, price_desc, name_asc |
| `test_single_product_endpoint_returns_product` | GET `/api/products/{id}` → product detail |
| `test_single_product_404_for_nonexistent_id` | GET `/api/products/999999` → 404 |
| `test_products_response_has_cache_headers` | Response có X-Cache-Status hoặc Cache-Control |
| `test_response_contains_request_id_header` | Response có X-Request-ID (UUID 36 chars) |
| `test_metrics_endpoint_returns_prometheus_format` | GET `/api/metrics` → text/plain, laravel_app_up |

---

### 4.4. ProductTest

**File:** `tests/Feature/ProductTest.php`  
**Mô tả:** Kiểm tra trang sản phẩm frontend: Listing, Detail, Filter, Pagination.

| Test Method | Mô tả |
|-------------|--------|
| `test_product_listing_page_loads_successfully` | GET `/san-pham` → 200/302 |
| `test_product_detail_page_loads_successfully` | GET `/san-pham/{slug}-{id}` → 200 |
| `test_product_belongs_to_category` | Product có relationship với Category |
| `test_hot_products_are_displayed` | Sản phẩm nổi bật có pro_hot=1 |
| `test_product_price_calculation_with_sale` | Giá giảm đúng theo pro_sale |
| `test_product_search_returns_results` | Tìm kiếm sản phẩm |
| `test_products_filtered_by_category` | GET `/danh-muc/{slug}-{id}` → view product.index |
| `test_product_pagination_works` | Phân trang hoạt động |
| `test_only_active_products_are_shown_on_frontend` | Frontend chỉ hiển thị products active |
| `test_product_has_required_fields` | Product có: pro_name, pro_slug, pro_price, pro_active |

---

### 4.5. Lab03ApiTest

**File:** `tests/Feature/Lab03ApiTest.php`  
**Mô tả:** Kiểm tra Lab03 RESTful API: Full CRUD operations với proper HTTP status codes.

| Test Method | Mô tả |
|-------------|--------|
| `test_lab03_health_check` | GET `/api/lab03/health` → 200 |
| `test_get_all_products_lab03` | GET `/api/lab03/products` → data array |
| `test_get_single_product_by_id_lab03` | GET `/api/lab03/products/{id}` → success=true |
| `test_get_nonexistent_product_returns_404_lab03` | GET `/api/lab03/products/999999` → 404, success=false |
| `test_create_product_with_valid_data_lab03` | POST `/api/lab03/products` → 201, success=true |
| `test_create_product_with_invalid_data_returns_400_lab03` | POST invalid data → 400, errors |
| `test_create_product_with_zero_price_returns_400_lab03` | Price = 0 → 400 |
| `test_update_product_lab03` | PUT `/api/lab03/products/{id}` → 200, success=true |
| `test_delete_product_lab03` | DELETE `/api/lab03/products/{id}` → 200, success=true |
| `test_search_products_lab03` | GET `/api/lab03/products/search?keyword=xxx` → 200 |
| `test_products_pagination_lab03` | `?page=1&per_page=10` → paginated |
| `test_api_accepts_json_content_type_lab03` | Content-Type: application/json |

---

### 4.6. GatewayAuthTest

**File:** `tests/Feature/GatewayAuthTest.php`  
**Mô tả:** Kiểm tra API Gateway Authentication & Authorization: 401 (Unauthorized), 403 (Forbidden), 200 (OK).

| Test Method | Mô tả |
|-------------|--------|
| `test_401_when_no_authorization_header` | Không có header → 401 "Unauthorized" |
| `test_401_when_authorization_header_has_no_bearer_prefix` | Không có "Bearer" prefix → 401 |
| `test_401_when_bearer_token_is_invalid` | Token không hợp lệ → 401 "Invalid or expired token" |
| `test_401_when_bearer_token_is_empty` | Token rỗng → 401 |
| `test_403_when_user_token_sends_post` | User POST → 403 "Admin token required" |
| `test_403_when_user_token_sends_put` | User PUT → 403 |
| `test_403_when_user_token_sends_delete` | User DELETE → 403 |
| `test_200_when_admin_token_sends_get` | Admin GET → 200 |
| `test_200_when_user_token_sends_get` | User GET → 200 |
| `test_admin_token_allows_post` | Admin POST → không phải 403 |
| `test_response_contains_x_gateway_header_on_success` | Header X-Gateway: "ElectroShop-Gateway" |

---

### 4.7. CartTest

**File:** `tests/Feature/CartTest.php`  
**Mô tả:** Kiểm tra chức năng giỏ hàng: Add, Update, Remove, Total, Checkout.

| Test Method | Mô tả |
|-------------|--------|
| `test_cart_page_loads_successfully` | GET `/cart` → 200/302 |
| `test_add_product_to_cart` | GET `/cart/add/{id}` → thêm sản phẩm |
| `test_cart_displays_added_items` | Giỏ hàng hiển thị items đã thêm |
| `test_update_cart_quantity` | GET `/cart/update/{id}` → cập nhật số lượng |
| `test_remove_item_from_cart` | GET `/cart/delete/{id}` → xóa sản phẩm |
| `test_cart_calculates_total_correctly` | Tổng = (price1×qty1) + (price2×qty2) |
| `test_empty_cart_shows_message` | Giỏ rỗng hiển thị thông báo |
| `test_cannot_add_out_of_stock_product` | Không thêm được sản phẩm hết hàng |
| `test_cannot_add_quantity_exceeding_stock` | Không thêm quá số lượng tồn kho |
| `test_checkout_page_requires_authentication` | GET `/oder/pay` → cần đăng nhập |

---

## 5. Test Configuration

### phpunit.xml

```xml
<phpunit bootstrap="vendor/autoload.php" colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>
```

### Test Base Classes

- **`tests/TestCase.php`** — Extends `Illuminate\Foundation\Testing\TestCase`, uses `CreatesApplication` trait.
- **`tests/CreatesApplication.php`** — Trait bootstraps Laravel application for testing.

### Dependencies (require-dev)

| Package | Version |
|---------|---------|
| phpunit/phpunit | ^10.1 |
| fakerphp/faker | ^1.9.1 |
| mockery/mockery | ^1.4.4 |
| nunomaduro/collision | ^7.0 |
| laravel/pint | ^1.0 |
| laravel/sail | ^1.18 |
| spatie/laravel-ignition | ^2.0 |
| barryvdh/laravel-debugbar | ^3.12 |
