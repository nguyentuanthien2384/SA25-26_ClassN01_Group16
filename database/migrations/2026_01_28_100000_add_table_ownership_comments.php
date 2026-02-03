<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Mark table ownership for each service (Database Per Service pattern)
     */
    public function up(): void
    {
        // Catalog Service owns these tables
        DB::statement("ALTER TABLE products COMMENT 'SERVICE_OWNER: Catalog'");
        DB::statement("ALTER TABLE categories COMMENT 'SERVICE_OWNER: Catalog'");
        DB::statement("ALTER TABLE pro_images COMMENT 'SERVICE_OWNER: Catalog'");

        // Customer Service owns these tables
        DB::statement("ALTER TABLE users COMMENT 'SERVICE_OWNER: Customer'");
        DB::statement("ALTER TABLE wishlists COMMENT 'SERVICE_OWNER: Customer'");
        DB::statement("ALTER TABLE admins COMMENT 'SERVICE_OWNER: Customer'");

        // Cart Service owns these tables
        DB::statement("ALTER TABLE carts COMMENT 'SERVICE_OWNER: Cart'");

        // Order Service owns these tables
        DB::statement("ALTER TABLE transactions COMMENT 'SERVICE_OWNER: Order'");
        DB::statement("ALTER TABLE oders COMMENT 'SERVICE_OWNER: Order'");

        // Review Service owns these tables
        DB::statement("ALTER TABLE ratings COMMENT 'SERVICE_OWNER: Review'");

        // Content Service owns these tables
        DB::statement("ALTER TABLE articles COMMENT 'SERVICE_OWNER: Content'");
        DB::statement("ALTER TABLE banners COMMENT 'SERVICE_OWNER: Content'");

        // Support Service owns these tables
        DB::statement("ALTER TABLE contacts COMMENT 'SERVICE_OWNER: Support'");

        // Shared/Infrastructure tables
        DB::statement("ALTER TABLE outbox_messages COMMENT 'SERVICE_OWNER: Shared-Infrastructure'");
        DB::statement("ALTER TABLE failed_jobs COMMENT 'SERVICE_OWNER: Shared-Infrastructure'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove comments
        $tables = [
            'products', 'categories', 'pro_images',
            'users', 'wishlists', 'admins',
            'carts',
            'transactions', 'oders',
            'ratings',
            'articles', 'banners',
            'contacts',
            'outbox_messages', 'failed_jobs'
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} COMMENT ''");
        }
    }
};
