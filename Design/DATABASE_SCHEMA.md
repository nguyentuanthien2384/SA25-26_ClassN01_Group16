# 🗄️ DATABASE SCHEMA DOCUMENTATION - ElectroShop

## 📋 Overview

**Database Management System:** MySQL 8.0  
**Character Set:** utf8mb4  
**Collation:** utf8mb4_unicode_ci  
**Engine:** InnoDB (with foreign keys support)

**Total Tables:** 14 main tables + 1 event table

---

## 📊 ER Diagram

See `Database_ER_Diagram.puml` for complete Entity-Relationship diagram.

---

## 📚 TABLE DEFINITIONS

### 1️⃣ **users** - User Accounts

**Purpose:** Store customer accounts and authentication data

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `u_name` | VARCHAR(255) | NOT NULL | Full name |
| `u_email` | VARCHAR(255) | UNIQUE, NOT NULL | Email (login) |
| `u_password` | VARCHAR(255) | NOT NULL | Hashed password |
| `u_phone` | VARCHAR(20) | NULL | Phone number |
| `u_address` | TEXT | NULL | Shipping address |
| `u_avatar` | VARCHAR(255) | NULL | Avatar image path |
| `email_verified_at` | TIMESTAMP | NULL | Email verification time |
| `remember_token` | VARCHAR(100) | NULL | Remember me token |
| `created_at` | TIMESTAMP | NULL | Account creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE KEY (`u_email`)

**Relationships:**
- Has many: `carts`, `transactions`, `ratings`, `articles`, `wishlists`

---

### 2️⃣ **category** - Product Categories

**Purpose:** Hierarchical product categorization

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `c_name` | VARCHAR(255) | NOT NULL | Category name |
| `c_slug` | VARCHAR(255) | UNIQUE, NOT NULL | URL-friendly slug |
| `c_parent_id` | BIGINT | NULL | Parent category (for sub-categories) |
| `c_active` | TINYINT(1) | DEFAULT 1 | Active status (1=Active, 0=Inactive) |
| `c_home` | TINYINT(1) | DEFAULT 0 | Show on homepage (1=Yes, 0=No) |
| `created_at` | TIMESTAMP | NULL | Creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE KEY (`c_slug`)
- INDEX (`c_parent_id`)
- INDEX (`c_active`)

**Relationships:**
- Has many: `products`, `articles`
- Belongs to: `category` (parent) - self-referencing for hierarchy

**Business Rules:**
- `c_active = 1`: Category visible on frontend
- `c_home = 1`: Featured in homepage menu
- `c_parent_id = NULL`: Top-level category
- `c_parent_id != NULL`: Sub-category

---

### 3️⃣ **products** - Products Catalog

**Purpose:** Main product information

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `pro_category_id` | BIGINT | FK, NOT NULL | Category reference |
| `pro_name` | VARCHAR(255) | NOT NULL | Product name |
| `pro_slug` | VARCHAR(255) | NOT NULL | URL slug |
| `pro_price` | INT | NOT NULL | Original price (VND) |
| `pro_sale` | INT | DEFAULT 0 | Discount % (0-100) |
| `pro_total` | INT | NULL | Final price after discount |
| `pro_content` | TEXT | NULL | Long description (HTML) |
| `pro_description` | TEXT | NULL | Short description |
| `pro_image` | VARCHAR(255) | NULL | Main product image |
| `quantity` | INT | DEFAULT 0 | Stock quantity |
| `pro_active` | TINYINT(1) | DEFAULT 1 | Published (1=Yes, 0=No) |
| `pro_hot` | TINYINT(1) | DEFAULT 0 | Featured (1=Yes, 0=No) |
| `pro_pay` | INT | DEFAULT 0 | Number of purchases |
| `pro_total_number` | INT | DEFAULT 0 | Total sold count |
| `created_at` | TIMESTAMP | NULL | Creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`pro_category_id`) REFERENCES `category(id)`
- INDEX (`pro_active`, `pro_hot`)
- INDEX (`pro_category_id`)
- INDEX (`pro_slug`)

**Relationships:**
- Belongs to: `category`
- Has many: `product_images`, `carts`, `orders`, `ratings`, `wishlists`

**Business Rules:**
- `pro_active = 1`: Product visible on storefront
- `pro_hot = 1`: Featured/hot product (homepage)
- `pro_sale > 0`: Discount active
- `pro_total = pro_price - (pro_price * pro_sale / 100)`
- `quantity = 0`: Out of stock

---

### 4️⃣ **product_images** - Product Gallery

**Purpose:** Multiple images per product

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `pi_product_id` | BIGINT | FK, NOT NULL | Product reference |
| `pi_name` | VARCHAR(255) | NULL | Image filename |
| `pi_slug` | VARCHAR(255) | NULL | Image slug |
| `created_at` | TIMESTAMP | NULL | Upload time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`pi_product_id`) REFERENCES `products(id)` ON DELETE CASCADE

**Relationships:**
- Belongs to: `products`

---

### 5️⃣ **carts** - Shopping Cart Items

**Purpose:** Temporary cart storage (session-based or user-based)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `c_user_id` | BIGINT | FK, NULL | User reference (NULL for guest) |
| `c_product_id` | BIGINT | FK, NOT NULL | Product reference |
| `c_quantity` | INT | DEFAULT 1 | Quantity in cart |
| `created_at` | TIMESTAMP | NULL | Added to cart time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`c_user_id`) REFERENCES `users(id)` ON DELETE CASCADE
- FOREIGN KEY (`c_product_id`) REFERENCES `products(id)` ON DELETE CASCADE
- UNIQUE KEY (`c_user_id`, `c_product_id`)

**Relationships:**
- Belongs to: `users`, `products`

---

### 6️⃣ **transactions** - Orders/Transactions

**Purpose:** Customer orders and payment information

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `tr_user_id` | BIGINT | FK, NOT NULL | Customer reference |
| `tr_total` | INT | NOT NULL | Total amount (VND) |
| `tr_status` | TINYINT(1) | DEFAULT 0 | Order status (0-3) |
| `tr_payment_method` | VARCHAR(50) | NULL | Payment method |
| `tr_payment_status` | VARCHAR(50) | NULL | Payment status |
| `tr_transaction_id` | VARCHAR(255) | NULL | Payment gateway transaction ID |
| `tr_note` | TEXT | NULL | Customer note |
| `tr_name` | VARCHAR(255) | NOT NULL | Shipping name |
| `tr_email` | VARCHAR(255) | NOT NULL | Shipping email |
| `tr_phone` | VARCHAR(20) | NOT NULL | Shipping phone |
| `tr_address` | TEXT | NOT NULL | Shipping address |
| `created_at` | TIMESTAMP | NULL | Order time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`tr_user_id`) REFERENCES `users(id)`
- INDEX (`tr_status`)
- INDEX (`tr_payment_method`)

**Relationships:**
- Belongs to: `users`
- Has many: `orders`

**Status Values:**
- `0` = Pending (waiting confirmation)
- `1` = Processing (preparing shipment)
- `2` = Completed (delivered)
- `3` = Cancelled

**Payment Methods:**
- `COD` = Cash on Delivery
- `momo` = Momo E-wallet
- `vnpay` = VNPay
- `paypal` = PayPal

---

### 7️⃣ **orders** - Order Line Items

**Purpose:** Individual products in each transaction

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `o_transaction_id` | BIGINT | FK, NOT NULL | Transaction reference |
| `o_product_id` | BIGINT | FK, NOT NULL | Product reference |
| `o_qty` | INT | NOT NULL | Quantity ordered |
| `o_price` | INT | NOT NULL | Unit price at purchase time |
| `o_sale` | INT | DEFAULT 0 | Discount % applied |
| `created_at` | TIMESTAMP | NULL | Order item time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`o_transaction_id`) REFERENCES `transactions(id)` ON DELETE CASCADE
- FOREIGN KEY (`o_product_id`) REFERENCES `products(id)`

**Relationships:**
- Belongs to: `transactions`, `products`

**Business Rules:**
- `o_price`: Snapshot of product price at time of purchase
- `o_sale`: Discount applied (for historical record)

---

### 8️⃣ **ratings** - Product Reviews

**Purpose:** Customer product reviews and ratings

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `r_product_id` | BIGINT | FK, NOT NULL | Product reference |
| `r_user_id` | BIGINT | FK, NOT NULL | User reference |
| `r_number` | INT | NOT NULL | Rating (1-5 stars) |
| `r_content` | TEXT | NULL | Review text |
| `r_status` | TINYINT(1) | DEFAULT 0 | Approved (1=Yes, 0=Pending) |
| `created_at` | TIMESTAMP | NULL | Review time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`r_product_id`) REFERENCES `products(id)` ON DELETE CASCADE
- FOREIGN KEY (`r_user_id`) REFERENCES `users(id)` ON DELETE CASCADE
- INDEX (`r_status`)
- INDEX (`r_number`)

**Relationships:**
- Belongs to: `products`, `users`

**Business Rules:**
- `r_number`: 1-5 (star rating)
- `r_status = 1`: Approved and visible
- `r_status = 0`: Pending moderation

---

### 9️⃣ **articles** - Blog/News Articles

**Purpose:** Content management (news, blogs, pages)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `a_user_id` | BIGINT | FK, NULL | Author reference |
| `a_category_id` | BIGINT | FK, NULL | Category reference |
| `a_name` | VARCHAR(255) | NOT NULL | Article title |
| `a_slug` | VARCHAR(255) | NOT NULL | URL slug |
| `a_description` | TEXT | NULL | Short description |
| `a_content` | TEXT | NULL | Full content (HTML) |
| `a_image` | VARCHAR(255) | NULL | Featured image |
| `a_status` | TINYINT(1) | DEFAULT 1 | Published (1=Yes, 0=Draft) |
| `a_hot` | TINYINT(1) | DEFAULT 0 | Featured (1=Yes, 0=No) |
| `created_at` | TIMESTAMP | NULL | Creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`a_user_id`) REFERENCES `users(id)`
- FOREIGN KEY (`a_category_id`) REFERENCES `category(id)`
- INDEX (`a_status`, `a_hot`)
- INDEX (`a_slug`)

**Relationships:**
- Belongs to: `users`, `category`

---

### 🔟 **contacts** - Contact Form Submissions

**Purpose:** Customer inquiries and contact messages

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `c_name` | VARCHAR(255) | NOT NULL | Sender name |
| `c_email` | VARCHAR(255) | NOT NULL | Sender email |
| `c_phone` | VARCHAR(20) | NULL | Sender phone |
| `c_title` | VARCHAR(255) | NOT NULL | Subject |
| `c_content` | TEXT | NOT NULL | Message content |
| `c_status` | TINYINT(1) | DEFAULT 0 | Processed (1=Yes, 0=Pending) |
| `created_at` | TIMESTAMP | NULL | Submission time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`c_status`)

---

### 1️⃣1️⃣ **wishlists** - User Wishlist

**Purpose:** Save products for later (favorites)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `w_user_id` | BIGINT | FK, NOT NULL | User reference |
| `w_product_id` | BIGINT | FK, NOT NULL | Product reference |
| `created_at` | TIMESTAMP | NULL | Added time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`w_user_id`) REFERENCES `users(id)` ON DELETE CASCADE
- FOREIGN KEY (`w_product_id`) REFERENCES `products(id)` ON DELETE CASCADE
- UNIQUE KEY (`w_user_id`, `w_product_id`)

**Relationships:**
- Belongs to: `users`, `products`

---

### 1️⃣2️⃣ **admins** - Admin Accounts

**Purpose:** Administrator/staff authentication

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `ad_name` | VARCHAR(255) | NOT NULL | Admin name |
| `ad_email` | VARCHAR(255) | UNIQUE, NOT NULL | Admin email (login) |
| `ad_password` | VARCHAR(255) | NOT NULL | Hashed password |
| `ad_phone` | VARCHAR(20) | NULL | Phone number |
| `ad_avatar` | VARCHAR(255) | NULL | Avatar image |
| `created_at` | TIMESTAMP | NULL | Creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE KEY (`ad_email`)

---

### 1️⃣3️⃣ **banners** - Homepage Banners

**Purpose:** Promotional banners and sliders

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `b_name` | VARCHAR(255) | NOT NULL | Banner name |
| `b_image` | VARCHAR(255) | NOT NULL | Banner image path |
| `b_link` | VARCHAR(255) | NULL | Click-through URL |
| `b_position` | VARCHAR(50) | NULL | Display position |
| `b_active` | TINYINT(1) | DEFAULT 1 | Active (1=Yes, 0=No) |
| `b_order` | INT | DEFAULT 0 | Display order |
| `created_at` | TIMESTAMP | NULL | Creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`b_active`, `b_order`)

**Positions:**
- `homepage_slider` = Main homepage slider
- `sidebar` = Sidebar banner
- `footer` = Footer banner

---

### 1️⃣4️⃣ **outbox_messages** - Event Outbox (Microservices)

**Purpose:** Transactional outbox pattern for event-driven architecture

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PK, AUTO_INCREMENT | Primary key |
| `aggregate_type` | VARCHAR(255) | NOT NULL | Entity type (Transaction, Product, etc.) |
| `aggregate_id` | BIGINT | NOT NULL | Entity ID |
| `event_type` | VARCHAR(255) | NOT NULL | Event name (OrderPlaced, etc.) |
| `payload` | JSON | NOT NULL | Event data (JSON) |
| `occurred_at` | TIMESTAMP | NOT NULL | Event timestamp |
| `published` | BOOLEAN | DEFAULT FALSE | Published to queue? |
| `published_at` | TIMESTAMP | NULL | Publishing time |
| `created_at` | TIMESTAMP | NULL | Record creation |
| `updated_at` | TIMESTAMP | NULL | Last update |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX (`published`, `occurred_at`)
- INDEX (`aggregate_type`, `aggregate_id`)

**Purpose:** Ensures reliable event delivery to microservices message broker.

---

## 📈 DATABASE STATISTICS

**Estimated Row Counts (Production):**

| Table | Estimated Rows | Growth Rate |
|-------|----------------|-------------|
| `users` | 10,000+ | Medium |
| `category` | ~100 | Low |
| `products` | 5,000+ | Medium |
| `product_images` | 20,000+ | High |
| `carts` | 500-1,000 | Variable |
| `transactions` | 50,000+ | High |
| `orders` | 150,000+ | High |
| `ratings` | 25,000+ | Medium |
| `articles` | 200+ | Low |
| `contacts` | 1,000+ | Low |
| `wishlists` | 5,000+ | Medium |
| `outbox_messages` | 100,000+ | Very High |

---

## 🔐 SECURITY NOTES

### Password Hashing
- User passwords: `bcrypt` (Laravel default, cost=10)
- Admin passwords: `bcrypt`

### Sensitive Data
- Payment gateway credentials: Stored in `.env` (not in database)
- Customer PII: `tr_name`, `tr_email`, `tr_phone`, `tr_address`

### SQL Injection Prevention
- All queries use Eloquent ORM (parameterized queries)
- Raw queries use parameter binding

---

## 🚀 PERFORMANCE OPTIMIZATIONS

### Indexes Strategy
- **Primary Keys:** All tables (auto-increment BIGINT)
- **Foreign Keys:** All relationships
- **Composite Indexes:** 
  - `(pro_active, pro_hot)` on products
  - `(c_user_id, c_product_id)` on carts/wishlists
- **Full-Text Index:** None yet (future: product search)

### Caching Layer
- **Redis Cache:** Product listings, categories
- **Cache Keys:** `api:products:hot:{perPage}:{page}`
- **TTL:** 300 seconds (5 minutes)

### Query Optimization
- **Eager Loading:** `with()` to prevent N+1 queries
- **Select Specific Columns:** `select()` instead of `SELECT *`
- **Pagination:** Cursor pagination for large datasets

---

## 📁 RELATED FILES

| File | Purpose |
|------|---------|
| `database/migrations/*.php` | Laravel migrations (schema definitions) |
| `app/Models/Models/*.php` | Eloquent models |
| `Design/Database_ER_Diagram.puml` | ER Diagram (PlantUML) |

---

**Last Updated:** 2026-01-28  
**Database Version:** MySQL 8.0  
**Schema Version:** 1.0
