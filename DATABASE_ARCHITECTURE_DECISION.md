# Quyết Định Kiến Trúc: Tách Riêng vs Gộp Chung Database

## 📊 So Sánh: Gộp Chung vs Tách Riêng

### ❌ **GỘP CHUNG 1 DATABASE** (Monolith Database)

```
┌─────────────────────────────────────────┐
│          MYSQL CHUNG (1 DB)             │
│  ┌──────────┐ ┌──────────┐ ┌─────────┐│
│  │ products │ │  orders  │ │  users  ││
│  │categories│ │   carts  │ │  admin  ││
│  │suppliers │ │transaction│ │ ratings ││
│  └──────────┘ └──────────┘ └─────────┘│
└─────────────────┬───────────────────────┘
                  │
    ┌─────────────┼─────────────┐
    ↓             ↓             ↓
┌─────────┐ ┌─────────┐ ┌─────────┐
│ Catalog │ │  Order  │ │  User   │
│ Service │ │ Service │ │ Service │
└─────────┘ └─────────┘ └─────────┘
```

**Vấn đề:**
- ❌ **Vi phạm nguyên tắc Microservices** - Database per Service
- ❌ **Coupling cao:** Services phụ thuộc lẫn nhau qua DB
- ❌ **Không scale độc lập:** 1 service query nhiều → ảnh hưởng tất cả
- ❌ **Không thể dùng DB khác nhau:** Tất cả phải dùng MySQL
- ❌ **Schema conflicts:** 2 services cùng sửa 1 bảng → xung đột
- ❌ **Mất điểm trong bài tập:** Không đạt chuẩn Microservices
- ❌ **Không failover được:** DB down → tất cả services down

**Khi nào dùng:**
- Chỉ dùng cho **Monolith** (setup cũ)
- KHÔNG dùng cho Microservices

---

### ✅ **TÁCH RIÊNG DATABASE** (Database per Service)

```
┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│ Catalog DB  │  │  Order DB   │  │   User DB   │
│ (MySQL:3310)│  │(MySQL:3311) │  │(MySQL:3312) │
│             │  │             │  │             │
│ ┌─────────┐ │  │ ┌─────────┐ │  │ ┌─────────┐ │
│ │products │ │  │ │ orders  │ │  │ │  users  │ │
│ │category │ │  │ │  carts  │ │  │ │  admin  │ │
│ │supplier │ │  │ │transact │ │  │ │ ratings │ │
│ └─────────┘ │  │ └─────────┘ │  │ └─────────┘ │
└──────┬──────┘  └──────┬──────┘  └──────┬──────┘
       │                │                │
       ↓                ↓                ↓
┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│  Catalog    │  │    Order    │  │    User     │
│  Service    │  │   Service   │  │   Service   │
│ (Port 9005) │  │ (Port 9002) │  │ (Port 9003) │
└─────────────┘  └─────────────┘  └─────────────┘
       ↑                ↓                ↑
       └────────────────┴────────────────┘
           Communication qua API/RabbitMQ
```

**Ưu điểm:**
- ✅ **Đúng nguyên tắc Microservices** - Mỗi service có DB riêng
- ✅ **Loose coupling:** Services độc lập hoàn toàn
- ✅ **Scale độc lập:** Catalog DB nhiều data → scale riêng
- ✅ **Technology diversity:** Catalog dùng MySQL, Order dùng PostgreSQL (nếu cần)
- ✅ **Fault isolation:** Order DB down → Catalog & User vẫn chạy
- ✅ **Team ownership:** Mỗi team quản lý DB của service mình
- ✅ **Deployment độc lập:** Update schema không ảnh hưởng services khác
- ✅ **Đạt điểm cao:** Đúng chuẩn Microservices trong bài tập

**Nhược điểm:**
- ⚠️ Phức tạp hơn: Phải quản lý nhiều DB
- ⚠️ Joins across services: Không thể JOIN giữa `products` và `orders`
- ⚠️ Data consistency: Phải dùng Distributed Transactions hoặc Saga Pattern
- ⚠️ Tốn tài nguyên: 3 MySQL containers thay vì 1

---

## 🎓 Theo Tiêu Chí Đánh Giá (100 điểm)

### Yêu cầu từ PDF "Software architecture - Microservices":

#### ✅ **Database per Service Pattern (BẮT BUỘC)**

Từ file `Software architecture - Microservices - 3 DB Patterns.pdf`:

```
"Each microservice has its own database"
"Services can't access each other's databases directly"
"Must communicate through APIs or message brokers"
```

**Điểm đánh giá:**
- ❌ Gộp chung 1 DB: **0 điểm** - Vi phạm nguyên tắc cơ bản
- ✅ Tách riêng DB: **Full điểm** - Đúng pattern

---

## 💡 Khuyến Nghị Cho Dự Án Của Bạn

### 🎯 **MỤC TIÊU: ĐẠT 100 ĐIỂM**

#### ✅ **NÊN LÀM (RECOMMENDED):**

**1. Giữ CẢ 2 SETUP:**
```bash
# File cũ: docker-compose.yml (Monolith)
# → Giữ lại cho backup & development nhanh

# File mới: docker-compose.microservices.yml (Microservices)
# → Dùng cho demo, nộp bài, đạt điểm cao
```

**2. Tách Riêng Database (ĐÃ LÀM ĐÚNG):**
```
✅ mysql_catalog (port 3310) → catalog_db
✅ mysql_order (port 3311) → order_db  
✅ mysql_user (port 3312) → user_db
```

**3. Data Migration Strategy:**
```sql
-- Từ 1 DB "duan" → 3 DBs riêng

-- Catalog DB: Import tables liên quan products
mysql_catalog: products, categories, suppliers, banners

-- Order DB: Import tables liên quan orders
mysql_order: orders, carts, transactions, payments

-- User DB: Import tables liên quan users
mysql_user: users, admin, ratings, contacts, wishlists
```

---

## 📋 Hành Động Cụ Thể

### Option 1: **TÁCH DỮ LIỆU ĐÚNG CÁCH** (Recommended cho 100đ)

Tôi sẽ giúp bạn:
1. ✅ Phân tích file `duan.sql` hiện tại
2. ✅ Tạo 3 file SQL riêng:
   - `catalog_db.sql` - Chỉ tables của Catalog
   - `order_db.sql` - Chỉ tables của Order
   - `user_db.sql` - Chỉ tables của User
3. ✅ Import đúng dữ liệu vào đúng database
4. ✅ Update code để services chỉ truy cập DB của mình

**Ưu điểm:**
- Đúng 100% nguyên tắc Microservices
- Mỗi service độc lập hoàn toàn
- Đạt điểm tối đa

---

### Option 2: **IMPORT CHUNG NHANH** (Dễ hơn nhưng mất điểm)

Import cả `duan.sql` vào cả 3 databases:
```bash
# Import duan.sql vào cả 3
mysql_catalog ← duan.sql (tất cả tables)
mysql_order ← duan.sql (tất cả tables)
mysql_user ← duan.sql (tất cả tables)
```

**Nhược điểm:**
- Mỗi DB có tables không cần thiết
- Services vẫn có thể truy cập tables của service khác
- Không đạt chuẩn Database per Service
- Mất điểm trong đánh giá

**Ưu điểm:**
- Nhanh, đơn giản
- App vẫn chạy được
- Có thể tối ưu sau

---

## 🎬 Quyết Định Cuối Cùng

### ❓ Bạn muốn:

**A. ĐẠT 100 ĐIỂM - TÁCH DỮ LIỆU ĐÚNG** ⭐ Recommended
```
→ Tôi sẽ:
  1. Phân tích duan.sql
  2. Tạo 3 file SQL riêng
  3. Import đúng data vào đúng service
  4. Update code để enforce database boundaries
  
→ Thời gian: ~30 phút
→ Kết quả: Kiến trúc Microservices hoàn hảo
```

**B. CHẠY NHANH - IMPORT CHUNG**
```
→ Import duan.sql vào cả 3 databases
→ Thời gian: 5 phút
→ Kết quả: App chạy được nhưng không đạt chuẩn
```

---

## 📌 Kết Luận

### 🎯 **TRẢ LỜI TRỰC TIẾP:**

1. **Có nên giữ cả 2 setup?**
   - ✅ **NÊN** - Giữ cả 2 để backup và so sánh
   - Chỉ chạy 1 setup tại 1 thời điểm

2. **Có nên tách riêng database?**
   - ✅ **BẮT BUỘC PHẢI TÁCH** - Đây là nguyên tắc cốt lõi của Microservices
   - Gộp chung = vi phạm pattern = mất điểm

3. **Khuyến nghị cuối:**
   ```
   Setup Cũ (Monolith): GIỮ LẠI
   ├─ 1 MySQL (duan)
   └─ Dùng khi cần dev/test nhanh
   
   Setup Mới (Microservices): DÙNG CHO ĐIỂM SỐ
   ├─ 3 MySQL riêng biệt
   ├─ Mỗi service 1 database
   └─ Đúng chuẩn kiến trúc Microservices
   ```

---

## ✨ Bước Tiếp Theo

Bạn muốn:
1. **Tiếp tục import như hiện tại** (import toàn bộ duan.sql vào cả 3 DB)?
2. **Tách dữ liệu đúng cách** (tôi tạo 3 file SQL riêng cho từng service)?

Hãy cho tôi biết bạn chọn cách nào! 🚀
