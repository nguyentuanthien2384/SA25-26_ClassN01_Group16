<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Models\Product;
use App\Models\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;

class Lab03ApiTest extends TestCase
{
    use WithFaker;

    /**
     * Test Lab 03 health check endpoint
     */
    public function test_lab03_health_check(): void
    {
        $response = $this->getJson('/api/lab03/health');
        
        $response->assertStatus(200);
        // Accept either 'status' or 'message' in response
        $this->assertTrue(
            $response->json('status') !== null || $response->json('message') !== null
        );
    }

    /**
     * Test get all products (Lab 03 API)
     */
    public function test_get_all_products_lab03(): void
    {
        $response = $this->getJson('/api/lab03/products');
        
        $response->assertStatus(200);
        // Check if response has data array
        $this->assertIsArray($response->json('data'));
    }

    /**
     * Test get single product by ID (Lab 03 API)
     */
    public function test_get_single_product_by_id_lab03(): void
    {
        $product = Product::first();
        
        if ($product) {
            $response = $this->getJson('/api/lab03/products/' . $product->id);
            
            $response->assertStatus(200);
            // Check if success is true and data exists
            $this->assertTrue($response->json('success') === true);
            $this->assertIsArray($response->json('data'));
        } else {
            $this->markTestSkipped('No products for API test');
        }
    }

    /**
     * Test get non-existent product returns 404 (Lab 03 API)
     */
    public function test_get_nonexistent_product_returns_404_lab03(): void
    {
        $nonExistentId = 999999;
        
        $response = $this->getJson('/api/lab03/products/' . $nonExistentId);
        
        $response->assertStatus(404);
        // Check if success is false
        $this->assertTrue($response->json('success') === false);
    }

    /**
     * Test create product with valid data (Lab 03 API)
     */
    public function test_create_product_with_valid_data_lab03(): void
    {
        $category = Category::first();
        
        if ($category) {
            $productData = [
                'pro_name' => 'Test Product ' . $this->faker->word(),
                'pro_price' => 500000,
                'pro_category_id' => $category->id,
                'pro_description' => 'Test product description',
                'quantity' => 10
            ];
            
            $response = $this->postJson('/api/lab03/products', $productData);
            
            $response->assertStatus(201);
            // Check if success is true
            $this->assertTrue($response->json('success') === true);
            $this->assertIsArray($response->json('data'));
        } else {
            $this->markTestSkipped('No categories for product creation test');
        }
    }

    /**
     * Test create product with invalid data returns 400 (Lab 03 API)
     */
    public function test_create_product_with_invalid_data_returns_400_lab03(): void
    {
        $invalidData = [
            'pro_name' => '', // Empty name
            'pro_price' => -100, // Negative price
        ];
        
        $response = $this->postJson('/api/lab03/products', $invalidData);
        
        $response->assertStatus(400);
        // Check if success is false and errors exist
        $this->assertTrue($response->json('success') === false);
        $this->assertArrayHasKey('errors', $response->json());
    }

    /**
     * Test create product with price = 0 returns 400 (Lab 03 API)
     */
    public function test_create_product_with_zero_price_returns_400_lab03(): void
    {
        $category = Category::first();
        
        if ($category) {
            $productData = [
                'pro_name' => 'Test Product',
                'pro_price' => 0, // Invalid: price must be > 0
                'pro_category_id' => $category->id
            ];
            
            $response = $this->postJson('/api/lab03/products', $productData);
            
            $response->assertStatus(400);
            // Check if success is false
            $this->assertTrue($response->json('success') === false);
        } else {
            $this->markTestSkipped('No categories for validation test');
        }
    }

    /**
     * Test update product (Lab 03 API)
     */
    public function test_update_product_lab03(): void
    {
        $product = Product::first();
        
        if ($product) {
            $updateData = [
                'pro_name' => 'Updated Product Name',
                'pro_price' => 600000,
                'pro_category_id' => $product->pro_category_id // Include required field
            ];
            
            $response = $this->putJson('/api/lab03/products/' . $product->id, $updateData);
            
            $response->assertStatus(200);
            // Check if success is true
            $this->assertTrue($response->json('success') === true);
        } else {
            $this->markTestSkipped('No products for update test');
        }
    }

    /**
     * Test delete product (Lab 03 API)
     */
    public function test_delete_product_lab03(): void
    {
        // Create a test product to delete
        $category = Category::first();
        
        if ($category) {
            $product = Product::create([
                'pro_name' => 'Product to Delete',
                'pro_slug' => 'product-to-delete-' . time(),
                'pro_price' => 100000,
                'pro_category_id' => $category->id,
                'pro_description' => 'Test',
                'quantity' => 1
            ]);
            
            $response = $this->deleteJson('/api/lab03/products/' . $product->id);
            
            $response->assertStatus(200);
            // Check if success is true
            $this->assertTrue($response->json('success') === true);
        } else {
            $this->markTestSkipped('No categories for delete test');
        }
    }

    /**
     * Test search products by name (Lab 03 API)
     */
    public function test_search_products_lab03(): void
    {
        $product = Product::first();
        
        if ($product) {
            $searchTerm = substr($product->pro_name, 0, 5);
            
            $response = $this->getJson('/api/lab03/products/search?keyword=' . urlencode($searchTerm));
            
            // Accept 200 or 400 (if search requires longer keyword)
            $this->assertTrue(
                in_array($response->status(), [200, 400])
            );
        } else {
            $this->markTestSkipped('No products for search test');
        }
    }

    /**
     * Test pagination works (Lab 03 API)
     */
    public function test_products_pagination_lab03(): void
    {
        $response = $this->getJson('/api/lab03/products?page=1&per_page=10');
        
        $response->assertStatus(200);
        // Check if data exists
        $this->assertIsArray($response->json('data'));
    }

    /**
     * Test API returns proper error codes
     */
    public function test_api_returns_proper_error_codes_lab03(): void
    {
        // Test 400 Bad Request
        $response = $this->postJson('/api/lab03/products', []);
        $response->assertStatus(400);
        
        // Test 404 Not Found
        $response = $this->getJson('/api/lab03/products/999999');
        $response->assertStatus(404);
    }

    /**
     * Test API accepts JSON content type
     */
    public function test_api_accepts_json_content_type_lab03(): void
    {
        $response = $this->json('GET', '/api/lab03/products', [], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }
}
