# 🗄️ DATABASE SEPARATION - COMPLETE SETUP GUIDE

**Pattern:** Database Per Service  
**Goal:** +13 điểm → Từ 68/100 lên 81/100  
**Time Required:** 2-3 giờ

---

## 📋 OVERVIEW

Tách database monolithic thành 4 databases riêng biệt theo domain:

```
┌─────────────┐
│   csdl      │  ← Main database (legacy)
│  (monolith) │
└─────────────┘
       ↓
       ↓ MIGRATE TO
       ↓
┌─────────────┬─────────────┬─────────────┬─────────────┐
│ catalog_db  │customer_db  │  order_db   │ content_db  │
├─────────────┼─────────────┼─────────────┼─────────────┤
│ products    │ users       │transactions │ articles    │
│ categories  │ wishlists   │trans_detail │ banners     │
│ pro_image   │             │ ratings     │ contacts    │
│ suppliers   │             │             │             │
│ warehouses  │             │             │             │
└─────────────┴─────────────┴─────────────┴─────────────┘
```

---

## 🚀 STEP-BY-STEP GUIDE

### **STEP 1: Backup Data (QUAN TRỌNG!)**

```bash
# Backup toàn bộ database
mysqldump -u root -p csdl > backup_csdl_$(date +%Y%m%d_%H%M%S).sql

# Hoặc backup từng table quan trọng
mysqldump -u root -p csdl products categories users transactions > backup_critical.sql
```

---

### **STEP 2: Tạo Databases**

```bash
# Chạy migration tạo databases
php artisan migrate --path=database/migrations/2026_01_28_120000_create_service_databases.php

# Output mong đợi:
# ✅ Created databases: catalog_db, customer_db, order_db, content_db
```

**Kiểm tra:**

```bash
mysql -u root -p -e "SHOW DATABASES;"

# Phải thấy:
# catalog_db
# customer_db
# order_db
# content_db
```

---

### **STEP 3: Tạo Database Users**

```bash
# Windows
mysql -u root -p < database\migrations\create_database_users.sql

# Linux/Mac
mysql -u root -p < database/migrations/create_database_users.sql
```

**Verify:**

```bash
mysql -u root -p -e "SELECT User, Host FROM mysql.user WHERE User LIKE '%_user';"

# Output:
# +----------------+-----------+
# | User           | Host      |
# +----------------+-----------+
# | catalog_user   | localhost |
# | catalog_user   | %         |
# | customer_user  | localhost |
# | customer_user  | %         |
# | order_user     | localhost |
# | order_user     | %         |
# | content_user   | localhost |
# | content_user   | %         |
# +----------------+-----------+
```

**Test connections:**

```bash
# Test catalog user
mysql -u catalog_user -pcatalog_pass_2026 -e "USE catalog_db; SHOW TABLES;"

# Test customer user
mysql -u customer_user -pcustomer_pass_2026 -e "USE customer_db; SHOW TABLES;"
```

---

### **STEP 4: Update .env**

```bash
# Copy .env.example nếu chưa có .env
cp .env.example .env

# Edit .env
nano .env
```

**Add/Update these lines:**

```env
# Main database (legacy - for migrations only)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csdl
DB_USERNAME=root
DB_PASSWORD=your_root_password

# Catalog Service
CATALOG_DB_HOST=127.0.0.1
CATALOG_DB_PORT=3306
CATALOG_DB_DATABASE=catalog_db
CATALOG_DB_USERNAME=catalog_user
CATALOG_DB_PASSWORD=catalog_pass_2026

# Customer Service
CUSTOMER_DB_HOST=127.0.0.1
CUSTOMER_DB_PORT=3306
CUSTOMER_DB_DATABASE=customer_db
CUSTOMER_DB_USERNAME=customer_user
CUSTOMER_DB_PASSWORD=customer_pass_2026

# Order Service
ORDER_DB_HOST=127.0.0.1
ORDER_DB_PORT=3306
ORDER_DB_DATABASE=order_db
ORDER_DB_USERNAME=order_user
ORDER_DB_PASSWORD=order_pass_2026

# Content Service
CONTENT_DB_HOST=127.0.0.1
CONTENT_DB_PORT=3306
CONTENT_DB_DATABASE=content_db
CONTENT_DB_USERNAME=content_user
CONTENT_DB_PASSWORD=content_pass_2026
```

**Clear cache:**

```bash
php artisan config:clear
php artisan config:cache
```

---

### **STEP 5: Migrate Tables to Service Databases**

```bash
# Chạy migration copy tables
php artisan migrate --path=database/migrations/2026_01_28_130000_migrate_tables_to_service_databases.php

# Output:
# 🚀 Starting table migration to service databases...
# 
# 📦 Migrating to catalog_db:
#    ✅ categories (15 rows)
#    ✅ products (120 rows)
#    ✅ pro_image (45 rows)
#    ...
# 
# 📦 Migrating to customer_db:
#    ✅ users (50 rows)
#    ✅ wishlists (30 rows)
#    ...
```

**Verify:**

```bash
# Check catalog_db
mysql -u catalog_user -pcatalog_pass_2026 catalog_db -e "SHOW TABLES;"
mysql -u catalog_user -pcatalog_pass_2026 catalog_db -e "SELECT COUNT(*) FROM products;"

# Check customer_db
mysql -u customer_user -pcustomer_pass_2026 customer_db -e "SHOW TABLES;"
mysql -u customer_user -pcustomer_pass_2026 customer_db -e "SELECT COUNT(*) FROM users;"

# Check order_db
mysql -u order_user -porder_pass_2026 order_db -e "SHOW TABLES;"
mysql -u order_user -porder_pass_2026 order_db -e "SELECT COUNT(*) FROM transactions;"

# Check content_db
mysql -u content_user -pcontent_pass_2026 content_db -e "SHOW TABLES;"
mysql -u content_user -pcontent_pass_2026 content_db -e "SELECT COUNT(*) FROM articles;"
```

---

### **STEP 6: Update Models**

#### Option A: Automated (Recommended)

Tạo file `update_models.php` trong root và copy nội dung từ `UPDATE_MODELS_GUIDE.md`, sau đó:

```bash
php update_models.php
```

#### Option B: Manual

Update từng model theo hướng dẫn trong `database/migrations/UPDATE_MODELS_GUIDE.md`

**Example updates:**

```php
// app/Models/Models/Product.php
use App\Models\Base\CatalogModel;
class Product extends CatalogModel { ... }

// app/Models/Models/Wishlist.php
use App\Models\Base\CustomerModel;
class Wishlist extends CustomerModel { ... }

// app/Models/Models/Transaction.php
use App\Models\Base\OrderModel;
class Transaction extends OrderModel { ... }

// app/Models/Models/Article.php
use App\Models\Base\ContentModel;
class Article extends ContentModel { ... }

// app/Models/User.php (special case)
protected $connection = 'customer';
```

---

### **STEP 7: Test Connections**

```bash
php artisan tinker
```

```php
// Test Catalog
>>> $product = App\Models\Models\Product::first();
>>> $product->getConnectionName();
=> "catalog"
>>> $product->pro_name;
=> "iPhone 15 Pro"

// Test Customer
>>> $user = App\Models\User::first();
>>> $user->getConnectionName();
=> "customer"
>>> $user->email;

// Test Order
>>> $transaction = App\Models\Models\Transaction::first();
>>> $transaction->getConnectionName();
=> "order"

// Test Content
>>> $article = App\Models\Models\Article::first();
>>> $article->getConnectionName();
=> "content"

// Test insert
>>> $cat = new App\Models\Models\Category();
>>> $cat->c_name = 'Test Category';
>>> $cat->c_slug = 'test-category';
>>> $cat->save();
>>> $cat->getConnectionName();
=> "catalog"
```

---

### **STEP 8: Test Application**

```bash
# Start server
php artisan serve

# Test các endpoints:
# http://localhost:8000/                    ← Products (catalog_db)
# http://localhost:8000/danh-muc/slug-1     ← Categories (catalog_db)
# http://localhost:8000/san-pham/slug-1     ← Product detail (catalog_db)
# http://localhost:8000/bai-viet            ← Articles (content_db)
# http://localhost:8000/dang-nhap           ← Login (customer_db)
# http://localhost:8000/gio-hang            ← Cart
# http://localhost:8000/thanh-toan          ← Checkout (order_db)
```

**Test CRUD operations:**

1. ✅ Tạo category mới
2. ✅ Tạo product mới
3. ✅ Đăng ký user mới
4. ✅ Đặt hàng mới
5. ✅ Tạo article mới

---

### **STEP 9: Update Seeders (If needed)**

Nếu bạn dùng seeders, update chúng để seed vào đúng databases:

```php
// database/seeders/CatalogSeeder.php
use Illuminate\Support\Facades\DB;

DB::connection('catalog')->table('products')->insert([...]);
DB::connection('catalog')->table('categories')->insert([...]);
```

---

### **STEP 10: (Optional) Clean Up Main Database**

**⚠️ CHỈ LÀM SAU KHI TEST KỸ CÀNG!**

```sql
-- Backup trước khi drop
mysqldump -u root -p csdl > backup_before_cleanup.sql

-- Drop tables đã migrate (giữ migrations và system tables)
USE csdl;

-- Catalog tables
DROP TABLE IF EXISTS products, categories, pro_image, suppliers, warehouses;

-- Customer tables
DROP TABLE IF EXISTS users, wishlists;

-- Order tables
DROP TABLE IF EXISTS transactions, transaction_detail, ratings;

-- Content tables
DROP TABLE IF EXISTS articles, banners, contacts;
```

---

## 🧪 VERIFICATION CHECKLIST

- [ ] ✅ 4 databases được tạo
- [ ] ✅ 4 database users được tạo
- [ ] ✅ Tables được copy với data
- [ ] ✅ .env updated với credentials
- [ ] ✅ Config cache cleared
- [ ] ✅ Models updated to use connections
- [ ] ✅ Tinker test passed
- [ ] ✅ Website chạy bình thường
- [ ] ✅ CRUD operations work
- [ ] ✅ Login/Register work
- [ ] ✅ Checkout work
- [ ] ✅ No errors in logs

---

## 🐛 TROUBLESHOOTING

### Issue 1: "SQLSTATE[HY000] [1049] Unknown database 'catalog_db'"

**Cause:** Databases chưa được tạo

**Fix:**
```bash
php artisan migrate --path=database/migrations/2026_01_28_120000_create_service_databases.php
```

---

### Issue 2: "SQLSTATE[HY000] [1045] Access denied for user 'catalog_user'"

**Cause:** Database users chưa được tạo hoặc password sai

**Fix:**
```bash
mysql -u root -p < database/migrations/create_database_users.sql
# Update .env với correct passwords
php artisan config:clear && php artisan config:cache
```

---

### Issue 3: "Base table or view not found: 1146 Table 'catalog_db.products' doesn't exist"

**Cause:** Tables chưa được migrate

**Fix:**
```bash
php artisan migrate --path=database/migrations/2026_01_28_130000_migrate_tables_to_service_databases.php
```

---

### Issue 4: Models vẫn dùng main database

**Cause:** Models chưa được update hoặc cache chưa clear

**Fix:**
```bash
# Update models
php update_models.php

# Clear cache
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

---

### Issue 5: Cross-database relationships không work

**Expected:** Đây là behavior đúng!

**Solution:** Sử dụng Events/CQRS để sync data cross-service:

```php
// Thay vì relationship
class Product extends CatalogModel
{
    public function ratings()  // ❌ Won't work across databases
    {
        return $this->hasMany(Rating::class);
    }
}

// Dùng event để sync
event(new ProductCreated($product));
// RatingService subscribe và tạo read model local
```

---

## 📊 METRICS - BEFORE & AFTER

### Before (Monolithic Database):
```
┌─────────────┐
│    csdl     │
│ 15 tables   │
│ Single point│
│ of failure  │
└─────────────┘
Score: 2/10
```

### After (Database Per Service):
```
┌────────┬────────┬────────┬────────┐
│catalog │customer│ order  │content │
│4 tables│2 tables│3 tables│3 tables│
│Isolated│Isolated│Isolated│Isolated│
└────────┴────────┴────────┴────────┘
Score: 15/15 ✅
```

---

## 🎯 EXPECTED RESULTS

- ✅ **+13 điểm** → Từ 68/100 lên **81/100**
- ✅ **Grade improvement:** C+ → B
- ✅ **Database isolation:** Each service owns its data
- ✅ **Scalability:** Can scale databases independently
- ✅ **Security:** Limited access per service
- ✅ **Maintainability:** Clear boundaries

---

## 📚 REFERENCES

- [Database Per Service Pattern](https://microservices.io/patterns/data/database-per-service.html)
- [Laravel Multiple Database Connections](https://laravel.com/docs/database#using-multiple-database-connections)
- `UPDATE_MODELS_GUIDE.md` - Chi tiết update models
- `ARCHITECTURE_STATUS.md` - Trạng thái tổng thể

---

## 🚀 NEXT STEPS

After completing database separation:

1. **ELK Stack Integration** (+9 điểm) → 81 → 90
2. **Kong Gateway Setup** (+8 điểm) → 90 → 98
3. **Service Discovery** (+2 điểm) → 98 → 100

---

**⏱️ Estimated time:** 2-3 hours  
**Difficulty:** Medium  
**Risk:** Low (if backup done)  
**Impact:** HIGH (+13 points)

---

**Ready to start?** Follow STEP 1 above! 🚀
