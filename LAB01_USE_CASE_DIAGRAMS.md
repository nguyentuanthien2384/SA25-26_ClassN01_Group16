# LAB 01: USE CASE DIAGRAMS

## Hướng dẫn vẽ sơ đồ Use Case trên Draw.io

Các sơ đồ dưới đây được thiết kế để vẽ lại trên **draw.io** (diagrams.net).

---

## 1. SYSTEM CONTEXT USE CASE DIAGRAM

### Mô tả
Sơ đồ tổng quan hệ thống ElectroShop với tất cả actors và use cases chính.

### Các thành phần cần vẽ:

#### System Boundary (Hình chữ nhật lớn)
```
Tên: ElectroShop E-Commerce Platform
```

#### Actors (Stick Figures - Ngoài system boundary)

**Primary Actors (Bên trái):**
1. **Web Customer**
   - Vị trí: Bên trái, giữa
   - Kết nối đến: Browse Products, Search Products, Manage Cart, Make Purchase, Manage Profile, Rate Product, Manage Wishlist

2. **Guest User**
   - Vị trí: Bên trái, dưới
   - Kết nối đến: Browse Products, Search Products

**Administrator (Bên phải):**
3. **Administrator**
   - Vị trí: Bên phải, giữa
   - Kết nối đến: Manage Catalog, Process Orders, Manage Users, View Analytics, Handle Support

**External Actors (Dưới cùng):**
4. **Payment Gateway**
   - Vị trí: Dưới bên trái
   - Kết nối đến: Process Payment

5. **Notification Service**
   - Vị trí: Dưới bên phải
   - Kết nối đến: Send Notification

#### Use Cases (Ovals - Bên trong system boundary)

**Customer Use Cases (Cột bên trái):**
```
1. Browse Products
2. Search Products  
3. Manage Cart
4. Make Purchase
5. Manage Profile
6. Rate Product
7. Manage Wishlist
```

**Admin Use Cases (Cột bên phải):**
```
8. Manage Catalog
9. Process Orders
10. Manage Users
11. View Analytics
12. Handle Support
```

**System Use Cases (Giữa):**
```
13. Process Payment (kết nối với Payment Gateway)
14. Send Notification (kết nối với Notification Service)
```

### Relationships (Solid Lines)
- Web Customer → Browse Products
- Web Customer → Search Products
- Web Customer → Manage Cart
- Web Customer → Make Purchase
- Web Customer → Manage Profile
- Web Customer → Rate Product
- Web Customer → Manage Wishlist
- Guest User → Browse Products
- Guest User → Search Products
- Administrator → Manage Catalog
- Administrator → Process Orders
- Administrator → Manage Users
- Administrator → View Analytics
- Administrator → Handle Support
- Payment Gateway → Process Payment
- Notification Service → Send Notification

---

## 2. DETAILED CHECKOUT PROCESS USE CASE DIAGRAM

### Mô tả
Sơ đồ chi tiết use case "Make Purchase" với các relationship **<<include>>** và **<<extend>>**.

### Các thành phần cần vẽ:

#### System Boundary (Hình chữ nhật)
```
Tên: ElectroShop E-Commerce Platform
     (Detailed Checkout Process)
```

#### Actors (Stick Figures)

1. **Web Customer**
   - Vị trí: Bên trái
   - Kết nối đến: Make Purchase

2. **Payment Gateway**
   - Vị trí: Bên phải
   - Kết nối đến: Process Credit Card

#### Use Cases (Ovals)

**Main Use Case (Trung tâm):**
```
Make Purchase
```

**Included Use Cases (Mandatory - theo flow từ trên xuống):**
```
1. Verify Cart Items
2. Calculate Total
3. Secure Payment
4. Process Credit Card (external)
5. Create Order
6. Send Notification
```

**Extended Use Cases (Optional - bên trái):**
```
7. Apply Discount Code
8. Add Gift Message
```

### Relationships

#### <<include>> Relationships (Dashed arrows with <<include>>)
**Vẽ mũi tên nét đứt, có label <<include>>:**

1. **Make Purchase** --<<include>>--> **Verify Cart Items**
   ```
   Direction: From Make Purchase to Verify Cart Items
   Label: <<include>>
   Note: "mandatory"
   ```

2. **Verify Cart Items** --<<include>>--> **Calculate Total**
   ```
   Direction: From Verify Cart Items to Calculate Total
   Label: <<include>>
   Note: "mandatory"
   ```

3. **Calculate Total** --<<include>>--> **Secure Payment**
   ```
   Direction: From Calculate Total to Secure Payment
   Label: <<include>>
   Note: "mandatory"
   ```

4. **Secure Payment** --<<include>>--> **Process Credit Card**
   ```
   Direction: From Secure Payment to Process Credit Card
   Label: <<include>>
   Note: "external - Payment Gateway"
   ```

5. **Process Credit Card** --<<include>>--> **Create Order**
   ```
   Direction: From Process Credit Card to Create Order
   Label: <<include>>
   Note: "mandatory"
   ```

6. **Create Order** --<<include>>--> **Send Notification**
   ```
   Direction: From Create Order to Send Notification
   Label: <<include>>
   Note: "mandatory"
   ```

#### <<extend>> Relationships (Dashed arrows with <<extend>>)
**Vẽ mũi tên nét đứt, có label <<extend>>:**

1. **Apply Discount Code** --<<extend>>--> **Make Purchase**
   ```
   Direction: From Apply Discount Code to Make Purchase
   Label: <<extend>>
   Note: "optional"
   Extension Point: "After Calculate Total"
   ```

2. **Add Gift Message** --<<extend>>--> **Make Purchase**
   ```
   Direction: From Add Gift Message to Make Purchase
   Label: <<extend>>
   Note: "optional"
   Extension Point: "Before Create Order"
   ```

### Layout Recommendations

**Vertical Flow (Top to Bottom):**
```
Make Purchase
    ↓ <<include>>
Verify Cart Items
    ↓ <<include>>
Calculate Total
    ↓ <<include>>
Secure Payment
    ↓ <<include>>
Process Credit Card (external to Payment Gateway)
    ↓ <<include>>
Create Order
    ↓ <<include>>
Send Notification
```

**Extended Use Cases (Left side):**
```
Apply Discount Code ----<<extend>>---→ Make Purchase
                                       (at Calculate Total)

Add Gift Message ----<<extend>>---→ Make Purchase
                                   (before Create Order)
```

---

## 3. HIGH-LEVEL ARCHITECTURE DIAGRAM

### Mô tả
Sơ đồ kiến trúc Microservices tổng quan của hệ thống.

### Layers (Từ trên xuống dưới)

#### Layer 1: CLIENT LAYER
```
┌─────────────────────────────────────┐
│        CLIENT LAYER                  │
│                                      │
│  [Web Browser] [Mobile App] [Admin] │
└──────────────┬──────────────────────┘
               │
```

#### Layer 2: API GATEWAY LAYER
```
┌──────────────┴──────────────────────┐
│     API GATEWAY LAYER                │
│                                      │
│      ┌────────────────────┐         │
│      │  Kong API Gateway  │         │
│      │  - Rate Limiting   │         │
│      │  - Authentication  │         │
│      │  - Load Balancing  │         │
│      └──────────┬─────────┘         │
└─────────────────┴──────────────────┘
                  │
```

#### Layer 3: MICROSERVICES LAYER
```
┌─────────────────┴──────────────────────────────────┐
│           MICROSERVICES LAYER                       │
│                                                     │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │ Catalog  │  │ Customer │  │   Cart   │        │
│  │ Service  │  │ Service  │  │ Service  │        │
│  └──────────┘  └──────────┘  └──────────┘        │
│                                                     │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │ Payment  │  │  Review  │  │ Content  │        │
│  │ Service  │  │ Service  │  │ Service  │        │
│  └──────────┘  └──────────┘  └──────────┘        │
│                                                     │
│  ┌──────────┐  ┌──────────────┐                   │
│  │ Support  │  │ Notification │                   │
│  │ Service  │  │ Service      │                   │
│  └──────────┘  └──────────────┘                   │
└─────────────────┬──────────────────────────────────┘
                  │
```

#### Layer 4: EVENT-DRIVEN LAYER
```
┌─────────────────┴──────────────────────┐
│      EVENT-DRIVEN LAYER                 │
│                                         │
│     ┌────────────────────┐             │
│     │   Redis Queue      │             │
│     │   (Event Bus)      │             │
│     │                    │             │
│     │ - OrderPlaced      │             │
│     │ - ProductCreated   │             │
│     │ - UserRegistered   │             │
│     └──────────┬─────────┘             │
│                │                        │
│     ┌──────────┴─────────┐             │
│     │  Outbox Pattern    │             │
│     └────────────────────┘             │
└─────────────────┬──────────────────────┘
                  │
```

#### Layer 5: DATA PERSISTENCE LAYER
```
┌─────────────────┴───────────────────────────┐
│     DATA PERSISTENCE LAYER                   │
│                                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐ │
│  │  MySQL   │  │  MySQL   │  │  MySQL   │ │
│  │(Catalog) │  │(Customer)│  │ (Order)  │ │
│  └──────────┘  └──────────┘  └──────────┘ │
│                                              │
│  ┌──────────┐  ┌────────────────┐          │
│  │  Redis   │  │ Elasticsearch  │          │
│  │  Cache   │  │   (Search)     │          │
│  └──────────┘  └────────────────┘          │
└─────────────────┬───────────────────────────┘
                  │
```

#### Layer 6: OBSERVABILITY LAYER
```
┌─────────────────┴────────────────────────────┐
│      OBSERVABILITY LAYER                      │
│                                               │
│  ┌──────────┐  ┌──────────┐  ┌───────────┐ │
│  │   ELK    │  │Prometheus│  │  Jaeger   │ │
│  │  Stack   │  │ +Grafana │  │ (Tracing) │ │
│  └──────────┘  └──────────┘  └───────────┘ │
│                                               │
│  ┌──────────┐  ┌──────────┐                 │
│  │  Consul  │  │  Health  │                 │
│  │(Discovery)│  │  Checks  │                 │
│  └──────────┘  └──────────┘                 │
└───────────────────────────────────────────────┘
```

---

## 4. CIRCUIT BREAKER PATTERN DIAGRAM

### Mô tả
Sơ đồ minh họa Circuit Breaker pattern bảo vệ hệ thống.

### States

```
┌─────────────────────────────────────────────────────────┐
│           Circuit Breaker States                         │
└─────────────────────────────────────────────────────────┘

        ┌──────────┐
        │  CLOSED  │ ←─────────────┐
        │ (Normal) │                │
        └────┬─────┘                │
             │                      │
             │ Failure threshold    │ Success
             │ exceeded             │
             ▼                      │
        ┌──────────┐                │
        │   OPEN   │                │
        │(Blocked) │                │
        └────┬─────┘                │
             │                      │
             │ Timeout              │
             │ expired              │
             ▼                      │
        ┌──────────┐                │
        │HALF_OPEN │                │
        │ (Testing)│ ───────────────┘
        └──────────┘
```

### Flow Example: Payment Service

```
┌─────────────┐         ┌─────────────────┐         ┌─────────────┐
│   Order     │         │ Circuit Breaker │         │  Payment    │
│   Service   │         │                 │         │  Gateway    │
└──────┬──────┘         └────────┬────────┘         └──────┬──────┘
       │                         │                         │
       │ 1. Process Payment      │                         │
       ├────────────────────────>│                         │
       │                         │                         │
       │                         │ 2. Check State          │
       │                         │    (CLOSED?)            │
       │                         │                         │
       │                         │ 3. Forward Request      │
       │                         ├────────────────────────>│
       │                         │                         │
       │                         │ 4. Response/Error       │
       │                         │<────────────────────────┤
       │                         │                         │
       │                         │ 5. Record Result        │
       │                         │    (Success/Failure)    │
       │                         │                         │
       │ 6. Return Response      │                         │
       │<────────────────────────┤                         │
       │                         │                         │
```

### Failure Scenario

```
┌─────────────┐         ┌─────────────────┐         ┌─────────────┐
│   Order     │         │ Circuit Breaker │         │  Payment    │
│   Service   │         │   (OPEN State)  │         │  Gateway    │
└──────┬──────┘         └────────┬────────┘         └──────┬──────┘
       │                         │                         │
       │ 1. Process Payment      │                         │
       ├────────────────────────>│                         │
       │                         │                         │
       │                         │ 2. Check State          │
       │                         │    (OPEN!)              │
       │                         │                         │
       │                         │ 3. ❌ BLOCKED           │
       │                         │    No request sent      │
       │                         │                         │
       │ 4. Return Fallback      │                         │
       │    (Cached/Default)     │                         │
       │<────────────────────────┤                         │
       │                         │                         │
```

---

## 5. EVENT-DRIVEN ARCHITECTURE WITH OUTBOX PATTERN

### Mô tả
Sơ đồ minh họa Event-Driven Architecture với Outbox Pattern.

### Flow Diagram

```
┌───────────────────────────────────────────────────────────────────┐
│                  Order Placement Flow                              │
└───────────────────────────────────────────────────────────────────┘

Step 1: Order Created
┌─────────────┐
│   User      │
│  (Browser)  │
└──────┬──────┘
       │ POST /checkout
       ▼
┌─────────────┐
│  Payment    │
│  Controller │
└──────┬──────┘
       │ 1. Create Order
       ▼
┌─────────────┐
│   Orders    │
│   Table     │
└─────────────┘

Step 2: Event to Outbox (Same Transaction)
┌─────────────┐
│   Payment   │
│  Controller │
└──────┬──────┘
       │ 2. Fire Event: OrderPlaced
       ▼
┌─────────────┐
│  Listener:  │
│ SaveToOutbox│
└──────┬──────┘
       │ 3. Save to Outbox Table (ACID)
       ▼
┌─────────────┐
│   Outbox    │
│  Messages   │
│   Table     │
└─────────────┘

Step 3: Publish to Queue (Async)
┌─────────────┐
│   Queue     │
│   Worker    │
└──────┬──────┘
       │ 4. Publish Outbox Messages
       ▼
┌─────────────┐
│   Redis     │
│   Queue     │
└──────┬──────┘
       │
       ├───────> Notification Service
       │
       ├───────> Inventory Service
       │
       └───────> Analytics Service

Step 4: Notification Service Consumes
┌──────────────────┐
│  Notification    │
│  Service         │
│  (Standalone)    │
└────────┬─────────┘
         │ 5. Send Email
         ▼
┌──────────────────┐
│   Customer       │
│   (Email)        │
└──────────────────┘

✅ KEY BENEFIT: If notification fails, order is already saved!
```

---

## 6. SAGA PATTERN DIAGRAM

### Mô tả
Sơ đồ minh họa Saga Pattern cho distributed transactions.

### Order Saga Flow

```
┌───────────────────────────────────────────────────────────────────┐
│                  Order Saga Pattern                                │
│              (Distributed Transaction)                             │
└───────────────────────────────────────────────────────────────────┘

┌──────────────┐
│  Order Saga  │
│ Orchestrator │
└──────┬───────┘
       │
       │ 1. Start Saga: Create Order
       │
       ├──> Step 1: Reserve Stock ──────────────┐
       │              (Inventory Service)        │
       │                                         │
       │    ✅ Success: Stock Reserved           │
       │<────────────────────────────────────────┘
       │
       ├──> Step 2: Process Payment ────────────┐
       │              (Payment Service)          │
       │                                         │
       │    ✅ Success: Payment Charged          │
       │<────────────────────────────────────────┘
       │
       ├──> Step 3: Create Shipment ────────────┐
       │              (Shipping Service)         │
       │                                         │
       │    ✅ Success: Shipment Created         │
       │<────────────────────────────────────────┘
       │
       ├──> Step 4: Send Notification ──────────┐
       │              (Notification Service)     │
       │                                         │
       │    ✅ Success: Email Sent               │
       │<────────────────────────────────────────┘
       │
       └──> ✅ Saga Completed Successfully


Failure Scenario with Compensation:

┌──────────────┐
│  Order Saga  │
│ Orchestrator │
└──────┬───────┘
       │
       │ 1. Start Saga
       │
       ├──> Step 1: Reserve Stock ──────────────┐
       │              (Inventory Service)        │
       │    ✅ Success: Stock Reserved           │
       │<────────────────────────────────────────┘
       │
       ├──> Step 2: Process Payment ────────────┐
       │              (Payment Service)          │
       │    ❌ FAILED: Payment Declined          │
       │<────────────────────────────────────────┘
       │
       │ 2. Start Compensation
       │
       ├──> Compensate Step 1: Release Stock ───┐
       │              (Inventory Service)        │
       │    ✅ Compensation Success              │
       │<────────────────────────────────────────┘
       │
       └──> ❌ Saga Failed (Order Cancelled)
```

---

## 7. CQRS PATTERN DIAGRAM

### Mô tả
Sơ đồ minh họa CQRS (Command Query Responsibility Segregation) pattern.

### Architecture

```
┌───────────────────────────────────────────────────────────────────┐
│                      CQRS Pattern                                  │
└───────────────────────────────────────────────────────────────────┘

┌─────────────┐
│   Client    │
│  (Browser)  │
└──────┬──────┘
       │
       ├──────────────────────────────────────┐
       │                                      │
       │ WRITE (Commands)                    │ READ (Queries)
       │                                      │
       ▼                                      ▼
┌─────────────────┐                   ┌─────────────────┐
│  Command Side   │                   │   Query Side    │
│                 │                   │                 │
│ ┌─────────────┐ │                   │ ┌─────────────┐ │
│ │   Product   │ │                   │ │   Product   │ │
│ │  Command    │ │                   │ │   Query     │ │
│ │  Service    │ │                   │ │  Service    │ │
│ └──────┬──────┘ │                   │ └──────┬──────┘ │
│        │        │                   │        │        │
│        ▼        │                   │        ▼        │
│ ┌─────────────┐ │                   │ ┌─────────────┐ │
│ │   MySQL     │ │                   │ │Elasticsearch│ │
│ │  (Write DB) │ │                   │ │  (Read DB)  │ │
│ │             │ │                   │ │             │ │
│ │ Source of   │ │                   │ │ Optimized   │ │
│ │   Truth     │ │                   │ │ for Search  │ │
│ └──────┬──────┘ │                   │ └─────────────┘ │
└────────┼────────┘                   └─────────────────┘
         │
         │ Event: ProductCreated/Updated
         │
         ▼
┌─────────────────┐
│  Event Bus      │
│  (Redis Queue)  │
└────────┬────────┘
         │
         │ Sync Read Model
         │
         ▼
┌─────────────────┐
│   Listener:     │
│ IndexToES       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Elasticsearch   │
│  (Read Model)   │
└─────────────────┘

Benefits:
✅ Write: Optimized for consistency (MySQL)
✅ Read: Optimized for performance (Elasticsearch)
✅ Scale independently
✅ Fast full-text search
```

---

## 8. HƯỚNG DẪN VẼ TRÊN DRAW.IO

### Bước 1: Truy cập Draw.io
- Mở: https://app.diagrams.net/
- Hoặc download Desktop: https://github.com/jgraph/drawio-desktop/releases

### Bước 2: Chọn UML Shapes
1. Click vào **More Shapes** (phía dưới bên trái)
2. Tick chọn: **UML**
3. Click **Apply**

### Bước 3: Vẽ System Boundary
1. Chọn **Rectangle** từ General shapes
2. Đặt kích thước: 800px x 600px
3. Label: "ElectroShop E-Commerce Platform"
4. Style: 
   - Border: Solid, 2px
   - Fill: None (transparent)

### Bước 4: Vẽ Actors
1. Từ UML shapes, chọn **Actor** (stick figure)
2. Kéo thả ra ngoài system boundary
3. Đổi tên theo danh sách actors
4. Position:
   - Primary actors: Bên trái
   - Admin: Bên phải
   - External: Dưới cùng

### Bước 5: Vẽ Use Cases
1. Từ UML shapes, chọn **Use Case** (oval)
2. Kéo thả vào trong system boundary
3. Đổi tên theo danh sách use cases
4. Arrange vertically cho dễ đọc

### Bước 6: Vẽ Relationships
**Simple Association (Actor to Use Case):**
- Chọn **Connector** tool
- Kéo từ Actor đến Use Case
- Style: Solid line

**<<include>> Relationship:**
- Chọn **Connector** tool
- Kéo từ Use Case A đến Use Case B
- Right-click → Edit Style
- Style: Dashed line
- Add label: "<<include>>"
- Arrow: Open arrow

**<<extend>> Relationship:**
- Tương tự <<include>>
- Label: "<<extend>>"
- Direction: Ngược lại (từ extension đến base)

### Bước 7: Export
1. File → Export as → PNG
2. Resolution: 300 DPI (for print quality)
3. Border width: 10px
4. Include: "Entire diagram"

---

## 📚 THAM KHẢO

- Lab 01.pdf - Requirements & Use Case Modeling
- Lecture 01.pdf - UML Use Case Diagrams
- Draw.io Documentation: https://www.diagrams.net/doc/

---

**Created for:** Lab 01 - Requirements Elicitation & Modeling  
**Project:** ElectroShop E-Commerce Platform  
**Date:** 2026-01-28
