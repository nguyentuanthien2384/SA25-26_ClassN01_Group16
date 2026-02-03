<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration to create separate databases for microservices
 * 
 * Pattern: Database Per Service
 * 
 * Creates:
 * - catalog_db (Products, Categories, ProImage)
 * - customer_db (Users, Wishlists)
 * - order_db (Transactions, Orders, TransactionDetail)
 * - content_db (Articles, Banners)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create databases
        DB::statement('CREATE DATABASE IF NOT EXISTS catalog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('CREATE DATABASE IF NOT EXISTS customer_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('CREATE DATABASE IF NOT EXISTS order_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('CREATE DATABASE IF NOT EXISTS content_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        echo "✅ Created databases: catalog_db, customer_db, order_db, content_db\n";

        // Note: Database users should be created manually for security
        // See: database/migrations/create_database_users.sql
        
        echo "\n";
        echo "⚠️  IMPORTANT: Create database users manually:\n";
        echo "   mysql -u root -p < database/migrations/create_database_users.sql\n";
        echo "\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Warning: This will delete all data!
        // Only use in development
        if (app()->environment('production')) {
            throw new \Exception('Cannot drop databases in production!');
        }

        DB::statement('DROP DATABASE IF EXISTS catalog_db');
        DB::statement('DROP DATABASE IF EXISTS customer_db');
        DB::statement('DROP DATABASE IF EXISTS order_db');
        DB::statement('DROP DATABASE IF EXISTS content_db');

        echo "✅ Dropped databases\n";
    }
};
