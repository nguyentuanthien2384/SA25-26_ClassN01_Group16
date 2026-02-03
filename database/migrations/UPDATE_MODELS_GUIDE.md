# 📚 HƯỚNG DẪN UPDATE MODELS SỬ DỤNG SERVICE DATABASES

## 🎯 MỤC TIÊU

Update các Model để sử dụng database connection riêng cho từng service theo pattern "Database Per Service".

---

## 📋 DANH SÁCH MODELS CẦN UPDATE

### 1. **Catalog Service Models** → Use `CatalogModel`

```
app/Models/Models/Category.php
app/Models/Models/Product.php
app/Models/Models/ProImage.php
app/Models/Models/Supplier.php (if exists)
app/Models/Models/Warehouse.php (if exists)
app/Models/Models/ImportGoods.php (if exists)
```

**Cách update:**

```php
<?php

namespace App\Models\Models;

use App\Models\Base\CatalogModel;  // ← Thay đổi

class Product extends CatalogModel  // ← Thay đổi từ Model
{
    protected $table = 'products';
    // ... rest of code
}
```

---

### 2. **Customer Service Models** → Use `CustomerModel`

```
app/Models/User.php
app/Models/Models/Wishlist.php
```

**Cách update:**

```php
<?php

namespace App\Models;

use App\Models\Base\CustomerModel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // Thêm property này
    protected $connection = 'customer';
    
    // ... rest of code
}
```

**Lưu ý:** User extends Authenticatable nên chỉ cần thêm `protected $connection = 'customer';`

```php
<?php

namespace App\Models\Models;

use App\Models\Base\CustomerModel;

class Wishlist extends CustomerModel
{
    protected $table = 'wishlists';
    // ... rest of code
}
```

---

### 3. **Order Service Models** → Use `OrderModel`

```
app/Models/Models/Transaction.php
app/Models/Models/TransactionDetail.php
app/Models/Models/Rating.php
```

**Cách update:**

```php
<?php

namespace App\Models\Models;

use App\Models\Base\OrderModel;

class Transaction extends OrderModel
{
    protected $table = 'transactions';
    // ... rest of code
}
```

---

### 4. **Content Service Models** → Use `ContentModel`

```
app/Models/Models/Article.php
app/Models/Models/Banner.php
app/Models/Models/Contact.php
```

**Cách update:**

```php
<?php

namespace App\Models\Models;

use App\Models\Base\ContentModel;

class Article extends ContentModel
{
    protected $table = 'articles';
    // ... rest of code
}
```

---

## 🔧 AUTOMATED UPDATE SCRIPT

Tạo file `update_models.php` trong thư mục root:

```php
<?php

/**
 * Script to update all models to use service-specific connections
 * Run: php update_models.php
 */

$updates = [
    // Catalog Models
    'app/Models/Models/Category.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\CatalogModel;',
        'class' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    'app/Models/Models/Product.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\CatalogModel;',
        'class' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    'app/Models/Models/ProImage.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\CatalogModel;',
        'class' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    
    // Customer Models
    'app/Models/Models/Wishlist.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\CustomerModel;',
        'class' => 'extends Model',
        'class_to' => 'extends CustomerModel',
    ],
    
    // Order Models
    'app/Models/Models/Transaction.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\OrderModel;',
        'class' => 'extends Model',
        'class_to' => 'extends OrderModel',
    ],
    'app/Models/Models/TransactionDetail.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\OrderModel;',
        'class' => 'extends Model',
        'class_to' => 'extends OrderModel',
    ],
    'app/Models/Models/Rating.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\OrderModel;',
        'class' => 'extends Model',
        'class_to' => 'extends OrderModel',
    ],
    
    // Content Models
    'app/Models/Models/Article.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\ContentModel;',
        'class' => 'extends Model',
        'class_to' => 'extends ContentModel',
    ],
    'app/Models/Models/Banner.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\ContentModel;',
        'class' => 'extends Model',
        'class_to' => 'extends ContentModel',
    ],
    'app/Models/Models/Contact.php' => [
        'from' => 'use Illuminate\Database\Eloquent\Model;',
        'to' => 'use App\Models\Base\ContentModel;',
        'class' => 'extends Model',
        'class_to' => 'extends ContentModel',
    ],
];

foreach ($updates as $file => $replacements) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: {$file}\n";
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Replace use statement
    $content = str_replace($replacements['from'], $replacements['to'], $content);
    
    // Replace class extends
    $content = str_replace($replacements['class'], $replacements['class_to'], $content);
    
    file_put_contents($file, $content);
    
    echo "✅ Updated: {$file}\n";
}

// Special case: User model
$userFile = 'app/Models/User.php';
if (file_exists($userFile)) {
    $content = file_get_contents($userFile);
    
    // Add connection property after class declaration
    if (strpos($content, "protected \$connection") === false) {
        $content = preg_replace(
            '/(class User extends Authenticatable\s*\{)/',
            "$1\n\n    /**\n     * The database connection to use\n     *\n     * @var string\n     */\n    protected \$connection = 'customer';",
            $content
        );
        
        file_put_contents($userFile, $content);
        echo "✅ Updated: {$userFile}\n";
    } else {
        echo "⚠️  User model already has connection property\n";
    }
}

echo "\n✅ Model updates completed!\n";
```

---

## ⚡ QUICK UPDATE (Manual)

Nếu không muốn dùng script, update thủ công:

### Example: Product Model

**Before:**
```php
<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    // ...
}
```

**After:**
```php
<?php

namespace App\Models\Models;

use App\Models\Base\CatalogModel;  // ← Changed

class Product extends CatalogModel  // ← Changed
{
    protected $table = 'products';
    // ... (rest unchanged)
}
```

---

## 🧪 TESTING

After updating models, test connections:

```php
php artisan tinker

// Test Catalog connection
>>> $product = App\Models\Models\Product::first();
>>> $product->getConnectionName();
=> "catalog"

// Test Customer connection
>>> $user = App\Models\User::first();
>>> $user->getConnectionName();
=> "customer"

// Test Order connection
>>> $transaction = App\Models\Models\Transaction::first();
>>> $transaction->getConnectionName();
=> "order"

// Test Content connection
>>> $article = App\Models\Models\Article::first();
>>> $article->getConnectionName();
=> "content"
```

---

## ⚠️ CROSS-SERVICE RELATIONSHIPS

### Problem:
Models in different databases cannot use Eloquent relationships directly.

### Solution:
Use Events and CQRS pattern for cross-service data.

**Example:**

```php
// ❌ BAD: Direct relationship across databases
class Product extends CatalogModel
{
    public function ratings()  // ratings in order_db
    {
        return $this->hasMany(Rating::class);  // Won't work!
    }
}

// ✅ GOOD: Use events or API calls
class Product extends CatalogModel
{
    public function getRatings()
    {
        // Option 1: HTTP call to Rating service API
        // Option 2: Subscribe to RatingCreated events
        // Option 3: CQRS read model in catalog_db
    }
}
```

---

## 📝 MIGRATION CHECKLIST

- [ ] Tạo databases: `catalog_db`, `customer_db`, `order_db`, `content_db`
- [ ] Tạo database users với passwords
- [ ] Copy tables vào databases tương ứng
- [ ] Update .env với credentials
- [ ] Update models extend BaseModels
- [ ] Test connections với tinker
- [ ] Test CRUD operations
- [ ] Update relationships (if any cross-database)
- [ ] Test full application flow
- [ ] Update documentation

---

## 🚀 ROLLBACK (If needed)

Nếu có vấn đề, rollback:

```bash
# Rollback migrations
php artisan migrate:rollback --step=2

# Models will fallback to 'mysql' connection (main database)
```

---

**Next Steps:** [DATABASE_SEPARATION_SETUP.md](DATABASE_SEPARATION_SETUP.md)
