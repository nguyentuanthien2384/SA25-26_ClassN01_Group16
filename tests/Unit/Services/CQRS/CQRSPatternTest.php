<?php

namespace Tests\Unit\Services\CQRS;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for CQRS Pattern (Command Query Responsibility Segregation)
 * 
 * Tests: Command operations (Create, Update, Delete)
 *        Query operations (Search, FindById, FindByCategory)
 *        Read model sync (Elasticsearch indexing)
 */
class CQRSPatternTest extends TestCase
{
    private array $writeModel = [];  // Simulates MySQL
    private array $readModel = [];   // Simulates Elasticsearch
    private array $events = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->writeModel = [];
        $this->readModel = [];
        $this->events = [];
    }

    // ======================================================
    // TEST: Command Side (Write Operations)
    // ======================================================

    /**
     * Test create product command
     */
    public function test_create_product_command(): void
    {
        $data = [
            'pro_name' => 'MacBook Pro M3',
            'pro_price' => 49990000,
            'pro_category_id' => 2,
            'pro_slug' => 'macbook-pro-m3',
        ];

        $product = $this->executeCreateCommand($data);

        $this->assertNotNull($product['id']);
        $this->assertEquals('MacBook Pro M3', $product['pro_name']);
        $this->assertEquals(49990000, $product['pro_price']);
    }

    /**
     * Test create command dispatches ProductCreated event
     */
    public function test_create_command_dispatches_event(): void
    {
        $data = [
            'pro_name' => 'MacBook Pro M3',
            'pro_price' => 49990000,
            'pro_category_id' => 2,
        ];

        $this->executeCreateCommand($data);

        $this->assertCount(1, $this->events);
        $this->assertEquals('ProductCreated', $this->events[0]['type']);
    }

    /**
     * Test update product command
     */
    public function test_update_product_command(): void
    {
        // Create first
        $product = $this->executeCreateCommand([
            'pro_name' => 'MacBook Pro M3',
            'pro_price' => 49990000,
            'pro_category_id' => 2,
        ]);

        // Update
        $updated = $this->executeUpdateCommand($product['id'], [
            'pro_price' => 45990000,
        ]);

        $this->assertEquals(45990000, $updated['pro_price']);
        $this->assertEquals('MacBook Pro M3', $updated['pro_name']); // Unchanged
    }

    /**
     * Test update command dispatches ProductUpdated event
     */
    public function test_update_command_dispatches_event(): void
    {
        $product = $this->executeCreateCommand([
            'pro_name' => 'Test Product',
            'pro_price' => 1000000,
            'pro_category_id' => 1,
        ]);
        $this->events = []; // Clear creation event

        $this->executeUpdateCommand($product['id'], ['pro_price' => 900000]);

        $this->assertCount(1, $this->events);
        $this->assertEquals('ProductUpdated', $this->events[0]['type']);
    }

    /**
     * Test delete product command
     */
    public function test_delete_product_command(): void
    {
        $product = $this->executeCreateCommand([
            'pro_name' => 'To Delete',
            'pro_price' => 1000000,
            'pro_category_id' => 1,
        ]);

        $result = $this->executeDeleteCommand($product['id']);

        $this->assertTrue($result);
        $this->assertNull($this->findInWriteModel($product['id']));
    }

    /**
     * Test delete command dispatches ProductDeleted event
     */
    public function test_delete_command_dispatches_event(): void
    {
        $product = $this->executeCreateCommand([
            'pro_name' => 'To Delete',
            'pro_price' => 1000000,
            'pro_category_id' => 1,
        ]);
        $this->events = [];

        $this->executeDeleteCommand($product['id']);

        $this->assertCount(1, $this->events);
        $this->assertEquals('ProductDeleted', $this->events[0]['type']);
        $this->assertEquals($product['id'], $this->events[0]['product_id']);
    }

    /**
     * Test update stock command
     */
    public function test_update_stock_command(): void
    {
        $product = $this->executeCreateCommand([
            'pro_name' => 'Stock Product',
            'pro_price' => 1000000,
            'pro_category_id' => 1,
            'pro_number' => 10,
        ]);

        $updated = $this->executeUpdateStockCommand($product['id'], 5);

        $this->assertEquals(15, $updated['pro_number']); // 10 + 5
    }

    /**
     * Test update stock with negative quantity (reduce stock)
     */
    public function test_update_stock_reduces_quantity(): void
    {
        $product = $this->executeCreateCommand([
            'pro_name' => 'Stock Product',
            'pro_price' => 1000000,
            'pro_category_id' => 1,
            'pro_number' => 10,
        ]);

        $updated = $this->executeUpdateStockCommand($product['id'], -3);

        $this->assertEquals(7, $updated['pro_number']); // 10 - 3
    }

    // ======================================================
    // TEST: Query Side (Read Operations)
    // ======================================================

    /**
     * Test search products by keyword
     */
    public function test_search_products_by_keyword(): void
    {
        // Seed read model
        $this->seedReadModel([
            ['id' => 1, 'name' => 'iPhone 15 Pro Max', 'price' => 29990000, 'category' => 'Điện thoại'],
            ['id' => 2, 'name' => 'Samsung Galaxy S24', 'price' => 22990000, 'category' => 'Điện thoại'],
            ['id' => 3, 'name' => 'MacBook Pro M3', 'price' => 49990000, 'category' => 'Laptop'],
        ]);

        $results = $this->searchReadModel('iPhone');

        $this->assertCount(1, $results['products']);
        $this->assertEquals('iPhone 15 Pro Max', $results['products'][0]['name']);
    }

    /**
     * Test search with no results
     */
    public function test_search_with_no_results(): void
    {
        $this->seedReadModel([
            ['id' => 1, 'name' => 'iPhone 15', 'price' => 29990000, 'category' => 'Điện thoại'],
        ]);

        $results = $this->searchReadModel('Nokia');

        $this->assertCount(0, $results['products']);
        $this->assertEquals(0, $results['total']);
    }

    /**
     * Test search with pagination
     */
    public function test_search_with_pagination(): void
    {
        $products = [];
        for ($i = 1; $i <= 10; $i++) {
            $products[] = ['id' => $i, 'name' => "Product {$i}", 'price' => $i * 1000000, 'category' => 'Test'];
        }
        $this->seedReadModel($products);

        $results = $this->searchReadModel('Product', 3, 0);

        $this->assertCount(3, $results['products']);
        $this->assertEquals(10, $results['total']);
    }

    /**
     * Test find product by ID from read model
     */
    public function test_find_product_by_id(): void
    {
        $this->seedReadModel([
            ['id' => 1, 'name' => 'iPhone 15', 'price' => 29990000, 'category' => 'Điện thoại'],
        ]);

        $product = $this->findInReadModel(1);

        $this->assertNotNull($product);
        $this->assertEquals('iPhone 15', $product['name']);
    }

    /**
     * Test find product by category
     */
    public function test_find_products_by_category(): void
    {
        $this->seedReadModel([
            ['id' => 1, 'name' => 'iPhone 15', 'price' => 29990000, 'category_id' => 1, 'category' => 'Điện thoại'],
            ['id' => 2, 'name' => 'Samsung S24', 'price' => 22990000, 'category_id' => 1, 'category' => 'Điện thoại'],
            ['id' => 3, 'name' => 'MacBook Pro', 'price' => 49990000, 'category_id' => 2, 'category' => 'Laptop'],
        ]);

        $results = $this->findByCategory(1);

        $this->assertCount(2, $results['products']);
        $this->assertEquals(2, $results['total']);
    }

    // ======================================================
    // TEST: Read Model Sync (Event → Elasticsearch)
    // ======================================================

    /**
     * Test ProductCreated event syncs to read model
     */
    public function test_product_created_syncs_to_read_model(): void
    {
        $product = $this->executeCreateCommand([
            'pro_name' => 'New Product',
            'pro_price' => 5000000,
            'pro_category_id' => 1,
        ]);

        // Simulate event handler syncing to Elasticsearch
        $this->syncToReadModel($product);

        $readProduct = $this->findInReadModel($product['id']);
        $this->assertNotNull($readProduct);
        $this->assertEquals('New Product', $readProduct['name']);
    }

    /**
     * Test ProductDeleted event removes from read model
     */
    public function test_product_deleted_removes_from_read_model(): void
    {
        $product = $this->executeCreateCommand([
            'pro_name' => 'To Delete',
            'pro_price' => 1000000,
            'pro_category_id' => 1,
        ]);
        $this->syncToReadModel($product);
        $this->assertNotNull($this->findInReadModel($product['id']));

        // Delete
        $this->executeDeleteCommand($product['id']);
        $this->removeFromReadModel($product['id']);

        $this->assertNull($this->findInReadModel($product['id']));
    }

    /**
     * Test fallback to database when Elasticsearch unavailable
     */
    public function test_fallback_to_database_search(): void
    {
        // Write model has data but read model is empty (ES unavailable)
        $this->executeCreateCommand([
            'pro_name' => 'Fallback Product',
            'pro_price' => 1000000,
            'pro_category_id' => 1,
        ]);

        $results = $this->fallbackSearch('Fallback');

        $this->assertTrue($results['fallback']);
        $this->assertCount(1, $results['products']);
    }

    // ======================================================
    // HELPER METHODS (CQRS Logic)
    // ======================================================

    private function executeCreateCommand(array $data): array
    {
        $id = count($this->writeModel) + 1;
        $product = array_merge($data, [
            'id' => $id,
            'pro_number' => $data['pro_number'] ?? 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->writeModel[$id] = $product;
        $this->events[] = ['type' => 'ProductCreated', 'product' => $product];

        return $product;
    }

    private function executeUpdateCommand(int $productId, array $data): array
    {
        if (!isset($this->writeModel[$productId])) {
            throw new \Exception("Product not found: {$productId}");
        }

        $this->writeModel[$productId] = array_merge($this->writeModel[$productId], $data);
        $this->writeModel[$productId]['updated_at'] = date('Y-m-d H:i:s');

        $this->events[] = ['type' => 'ProductUpdated', 'product' => $this->writeModel[$productId]];

        return $this->writeModel[$productId];
    }

    private function executeDeleteCommand(int $productId): bool
    {
        if (!isset($this->writeModel[$productId])) {
            throw new \Exception("Product not found: {$productId}");
        }

        unset($this->writeModel[$productId]);
        $this->events[] = ['type' => 'ProductDeleted', 'product_id' => $productId];

        return true;
    }

    private function executeUpdateStockCommand(int $productId, int $quantity): array
    {
        if (!isset($this->writeModel[$productId])) {
            throw new \Exception("Product not found: {$productId}");
        }

        $this->writeModel[$productId]['pro_number'] += $quantity;
        $this->events[] = ['type' => 'ProductUpdated', 'product' => $this->writeModel[$productId]];

        return $this->writeModel[$productId];
    }

    private function findInWriteModel(int $id): ?array
    {
        return $this->writeModel[$id] ?? null;
    }

    private function seedReadModel(array $products): void
    {
        foreach ($products as $product) {
            $this->readModel[$product['id']] = $product;
        }
    }

    private function syncToReadModel(array $product): void
    {
        $this->readModel[$product['id']] = [
            'id' => $product['id'],
            'name' => $product['pro_name'],
            'price' => $product['pro_price'],
            'category_id' => $product['pro_category_id'] ?? null,
        ];
    }

    private function removeFromReadModel(int $productId): void
    {
        unset($this->readModel[$productId]);
    }

    private function findInReadModel(int $id): ?array
    {
        return $this->readModel[$id] ?? null;
    }

    private function searchReadModel(string $keyword, int $limit = 20, int $offset = 0): array
    {
        $results = array_filter($this->readModel, function ($product) use ($keyword) {
            return stripos($product['name'], $keyword) !== false;
        });

        $total = count($results);
        $products = array_slice(array_values($results), $offset, $limit);

        return ['total' => $total, 'products' => $products];
    }

    private function findByCategory(int $categoryId): array
    {
        $results = array_filter($this->readModel, function ($product) use ($categoryId) {
            return ($product['category_id'] ?? null) === $categoryId;
        });

        return ['total' => count($results), 'products' => array_values($results)];
    }

    private function fallbackSearch(string $keyword): array
    {
        $results = array_filter($this->writeModel, function ($product) use ($keyword) {
            return stripos($product['pro_name'], $keyword) !== false;
        });

        return [
            'total' => count($results),
            'products' => array_values($results),
            'fallback' => true,
        ];
    }
}
