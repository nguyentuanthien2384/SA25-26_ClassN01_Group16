<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migrate tables from main database to service-specific databases
 * 
 * Pattern: Database Per Service
 * Strategy: Copy tables with data to new databases
 */
return new class extends Migration
{
    /**
     * Table ownership mapping
     */
    private array $tableMapping = [
        'catalog_db' => [
            'categories',
            'products',
            'pro_image',
            'suppliers',
            'warehouses',
            'import_goods',
            'import_goods_detail',
        ],
        'customer_db' => [
            'users',
            'wishlists',
        ],
        'order_db' => [
            'transactions',
            'transaction_detail',
            'ratings',
        ],
        'content_db' => [
            'articles',
            'banners',
            'contacts',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "\n🚀 Starting table migration to service databases...\n\n";

        foreach ($this->tableMapping as $database => $tables) {
            echo "📦 Migrating to {$database}:\n";
            
            foreach ($tables as $table) {
                if (!Schema::hasTable($table)) {
                    echo "   ⚠️  Table '{$table}' does not exist, skipping...\n";
                    continue;
                }

                try {
                    // Copy table structure and data
                    DB::statement("CREATE TABLE IF NOT EXISTS {$database}.{$table} LIKE csdl.{$table}");
                    
                    // Copy data
                    $count = DB::table($table)->count();
                    if ($count > 0) {
                        DB::statement("INSERT INTO {$database}.{$table} SELECT * FROM csdl.{$table}");
                        echo "   ✅ {$table} ({$count} rows)\n";
                    } else {
                        echo "   ✅ {$table} (empty)\n";
                    }
                    
                } catch (\Exception $e) {
                    echo "   ❌ Error migrating {$table}: {$e->getMessage()}\n";
                }
            }
            
            echo "\n";
        }

        echo "✅ Table migration completed!\n\n";
        
        echo "⚠️  NEXT STEPS:\n";
        echo "1. Update .env with database credentials\n";
        echo "2. Update models to use correct connections\n";
        echo "3. Test all features\n";
        echo "4. (Optional) Drop tables from main database if all works\n";
        echo "\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('production')) {
            throw new \Exception('Cannot reverse table migration in production!');
        }

        echo "\n⚠️  This will drop all tables from service databases!\n";
        echo "Main database (csdl) will NOT be affected.\n\n";

        foreach ($this->tableMapping as $database => $tables) {
            echo "Dropping tables from {$database}:\n";
            
            foreach ($tables as $table) {
                try {
                    DB::statement("DROP TABLE IF EXISTS {$database}.{$table}");
                    echo "   ✅ Dropped {$table}\n";
                } catch (\Exception $e) {
                    echo "   ❌ Error: {$e->getMessage()}\n";
                }
            }
            
            echo "\n";
        }

        echo "✅ Rollback completed!\n";
    }
};
