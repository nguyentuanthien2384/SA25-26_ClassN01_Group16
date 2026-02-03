<?php

/**
 * Automated Model Update Script
 * 
 * Updates all models to use service-specific database connections
 * 
 * Usage: php update_models.php
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       DATABASE PER SERVICE - MODEL UPDATE SCRIPT             ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$updates = [
    // ========================================================================
    // CATALOG SERVICE MODELS
    // ========================================================================
    'app/Models/Models/Category.php' => [
        'service' => 'Catalog',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\CatalogModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    'app/Models/Models/Product.php' => [
        'service' => 'Catalog',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\CatalogModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    'app/Models/Models/ProImage.php' => [
        'service' => 'Catalog',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\CatalogModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    'app/Models/Models/Supplier.php' => [
        'service' => 'Catalog',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\CatalogModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    'app/Models/Models/Warehouse.php' => [
        'service' => 'Catalog',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\CatalogModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    'app/Models/Models/ImportGoods.php' => [
        'service' => 'Catalog',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\CatalogModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends CatalogModel',
    ],
    
    // ========================================================================
    // CUSTOMER SERVICE MODELS
    // ========================================================================
    'app/Models/Models/Wishlist.php' => [
        'service' => 'Customer',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\CustomerModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends CustomerModel',
    ],
    
    // ========================================================================
    // ORDER SERVICE MODELS
    // ========================================================================
    'app/Models/Models/Transaction.php' => [
        'service' => 'Order',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\OrderModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends OrderModel',
    ],
    'app/Models/Models/TransactionDetail.php' => [
        'service' => 'Order',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\OrderModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends OrderModel',
    ],
    'app/Models/Models/Rating.php' => [
        'service' => 'Order',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\OrderModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends OrderModel',
    ],
    
    // ========================================================================
    // CONTENT SERVICE MODELS
    // ========================================================================
    'app/Models/Models/Article.php' => [
        'service' => 'Content',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\ContentModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends ContentModel',
    ],
    'app/Models/Models/Banner.php' => [
        'service' => 'Content',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\ContentModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends ContentModel',
    ],
    'app/Models/Models/Contact.php' => [
        'service' => 'Content',
        'use_from' => 'use Illuminate\Database\Eloquent\Model;',
        'use_to' => 'use App\Models\Base\ContentModel;',
        'class_from' => 'extends Model',
        'class_to' => 'extends ContentModel',
    ],
];

$stats = [
    'updated' => 0,
    'skipped' => 0,
    'errors' => 0,
];

echo "📦 Updating models...\n\n";

foreach ($updates as $file => $config) {
    // Convert forward slashes to backslashes for Windows
    $filePath = str_replace('/', DIRECTORY_SEPARATOR, $file);
    
    if (!file_exists($filePath)) {
        echo "⚠️  [{$config['service']}] File not found: {$file}\n";
        $stats['skipped']++;
        continue;
    }
    
    try {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Replace use statement
        $content = str_replace($config['use_from'], $config['use_to'], $content);
        
        // Replace class extends
        $content = str_replace($config['class_from'], $config['class_to'], $content);
        
        if ($content === $originalContent) {
            echo "⏭️  [{$config['service']}] Already updated: " . basename($file) . "\n";
            $stats['skipped']++;
        } else {
            file_put_contents($filePath, $content);
            echo "✅ [{$config['service']}] Updated: " . basename($file) . "\n";
            $stats['updated']++;
        }
        
    } catch (Exception $e) {
        echo "❌ [{$config['service']}] Error updating {$file}: {$e->getMessage()}\n";
        $stats['errors']++;
    }
}

echo "\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "Special case: User model\n";
echo "─────────────────────────────────────────────────────────────\n\n";

// ============================================================================
// SPECIAL CASE: USER MODEL
// ============================================================================
$userFile = 'app' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'User.php';

if (file_exists($userFile)) {
    $content = file_get_contents($userFile);
    
    // Check if connection property already exists
    if (strpos($content, "protected \$connection") !== false) {
        echo "⏭️  [Customer] User model already has connection property\n";
        $stats['skipped']++;
    } else {
        // Add connection property after class declaration
        $pattern = '/(class User extends Authenticatable\s*\{)/';
        $replacement = "$1\n\n    /**\n     * The database connection to use for Customer service\n     *\n     * @var string\n     */\n    protected \$connection = 'customer';\n";
        
        $content = preg_replace($pattern, $replacement, $content);
        
        if ($content) {
            file_put_contents($userFile, $content);
            echo "✅ [Customer] Updated: User.php (added connection property)\n";
            $stats['updated']++;
        } else {
            echo "❌ [Customer] Failed to update User.php\n";
            $stats['errors']++;
        }
    }
} else {
    echo "⚠️  [Customer] User.php not found\n";
    $stats['skipped']++;
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                      UPDATE SUMMARY                          ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Updated:  {$stats['updated']} files\n";
echo "⏭️  Skipped:  {$stats['skipped']} files\n";
echo "❌ Errors:   {$stats['errors']} files\n";
echo "\n";

if ($stats['errors'] === 0) {
    echo "🎉 SUCCESS! All models updated successfully!\n";
    echo "\n";
    echo "📋 NEXT STEPS:\n";
    echo "1. Clear config cache:  php artisan config:clear\n";
    echo "2. Clear cache:         php artisan cache:clear\n";
    echo "3. Dump autoload:       composer dump-autoload\n";
    echo "4. Test connections:    php artisan tinker\n";
    echo "\n";
    echo "Test in tinker:\n";
    echo "  >>> \$product = App\\Models\\Models\\Product::first();\n";
    echo "  >>> \$product->getConnectionName();\n";
    echo "  => \"catalog\"\n";
    echo "\n";
} else {
    echo "⚠️  Some errors occurred. Please check the output above.\n";
    echo "\n";
}

echo "Done!\n";
echo "\n";
