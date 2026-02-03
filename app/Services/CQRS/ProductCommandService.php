<?php

namespace App\Services\CQRS;

use App\Models\Models\Product;
use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Events\ProductDeleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CQRS - Command Side
 * Handles all write operations for products
 */
class ProductCommandService
{
    /**
     * Create a new product
     */
    public function create(array $data): Product
    {
        DB::beginTransaction();
        
        try {
            $product = Product::create($data);
            
            // Dispatch event to update read model (Elasticsearch)
            event(new ProductCreated($product));
            
            DB::commit();
            
            Log::info('Product created via CQRS Command', [
                'product_id' => $product->id,
                'name' => $product->pro_name,
            ]);
            
            return $product;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create product', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing product
     */
    public function update(int $productId, array $data): Product
    {
        DB::beginTransaction();
        
        try {
            $product = Product::findOrFail($productId);
            $product->update($data);
            
            // Dispatch event to update read model
            event(new ProductUpdated($product));
            
            DB::commit();
            
            Log::info('Product updated via CQRS Command', [
                'product_id' => $product->id,
            ]);
            
            return $product->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a product
     */
    public function delete(int $productId): bool
    {
        DB::beginTransaction();
        
        try {
            $product = Product::findOrFail($productId);
            $product->delete();
            
            // Dispatch event to update read model
            event(new ProductDeleted($productId));
            
            DB::commit();
            
            Log::info('Product deleted via CQRS Command', [
                'product_id' => $productId,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete product', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update stock quantity
     */
    public function updateStock(int $productId, int $quantity): Product
    {
        DB::beginTransaction();
        
        try {
            $product = Product::findOrFail($productId);
            $product->increment('pro_number', $quantity);
            
            event(new ProductUpdated($product->fresh()));
            
            DB::commit();
            
            Log::info('Product stock updated', [
                'product_id' => $productId,
                'quantity_change' => $quantity,
            ]);
            
            return $product->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
