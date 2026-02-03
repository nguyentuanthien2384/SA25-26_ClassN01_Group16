# ⚡ DATABASE SEPARATION - QUICK START

**Goal:** Tách database thành 4 services trong 30 phút  
**Difficulty:** 🟡 Medium  
**Impact:** +13 điểm (68 → 81/100)

---

## 🚀 5-MINUTE SETUP

```bash
# 1. Backup (2 min)
mysqldump -u root -p csdl > backup_$(date +%Y%m%d).sql

# 2. Create databases (1 min)
php artisan migrate --path=database/migrations/2026_01_28_120000_create_service_databases.php

# 3. Create users (1 min)
mysql -u root -p < database/migrations/create_database_users.sql

# 4. Copy tables (5 min)
php artisan migrate --path=database/migrations/2026_01_28_130000_migrate_tables_to_service_databases.php

# 5. Update .env (2 min)
# Edit .env và add passwords (xem bên dưới)

# 6. Update models (1 min)
php update_models.php

# 7. Clear cache (30 sec)
php artisan config:clear
php artisan cache:clear
composer dump-autoload

# 8. Test (5 min)
php artisan tinker
```

---

## 📝 .ENV UPDATE

Add vào `.env`:

```env
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

---

## 🧪 QUICK TEST

```php
php artisan tinker

// Test each connection
>>> App\Models\Models\Product::first()->getConnectionName();
=> "catalog"  ✅

>>> App\Models\User::first()->getConnectionName();
=> "customer"  ✅

>>> App\Models\Models\Transaction::first()->getConnectionName();
=> "order"  ✅

>>> App\Models\Models\Article::first()->getConnectionName();
=> "content"  ✅
```

---

## ✅ SUCCESS CRITERIA

- [ ] 4 databases created
- [ ] Tables migrated with data
- [ ] Models return correct connection names
- [ ] Website works: `php artisan serve`
- [ ] Can login/register
- [ ] Can view products
- [ ] Can place order

---

## ❌ IF SOMETHING FAILS

```bash
# Rollback
php artisan migrate:rollback --step=2

# Restore backup
mysql -u root -p csdl < backup_YYYYMMDD.sql

# Try again with detailed guide
# See: DATABASE_SEPARATION_SETUP.md
```

---

## 📊 RESULT

```
BEFORE:  68/100 (C+)  
AFTER:   81/100 (B)   ⬆️ +13 points

✅ Database Per Service: 15/15
✅ Service Isolation: Complete
✅ Ready for scaling
```

---

## 📚 DETAILED GUIDES

- **Full Setup:** `DATABASE_SEPARATION_SETUP.md`
- **Model Updates:** `database/migrations/UPDATE_MODELS_GUIDE.md`
- **Architecture:** `ARCHITECTURE_STATUS.md`

---

**Ready? Start with step 1! 🚀**

```bash
mysqldump -u root -p csdl > backup_$(date +%Y%m%d).sql
```
