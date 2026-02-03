<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Events\ProductDeleted;
use Illuminate\Support\Facades\Log;

/**
 * Sync product changes to Elasticsearch (Read Model)
 * NOTE: Elasticsearch is optional. Install: composer require elasticsearch/elasticsearch
 */
class IndexProductToElasticsearch
{
    private $elasticsearch;
    private $enabled = false;

    public function __construct()
    {
        // Only initialize if Elasticsearch package is installed
        if (class_exists(\Elasticsearch\ClientBuilder::class)) {
            try {
                $hosts = [config('services.elasticsearch.host', 'http://localhost:9200')];
                $this->elasticsearch = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();
                $this->enabled = true;
            } catch (\Exception $e) {
                Log::warning('Elasticsearch not available: ' . $e->getMessage());
                $this->enabled = false;
            }
        } else {
            Log::info('Elasticsearch package not installed. CQRS read model disabled. Install: composer require elasticsearch/elasticsearch');
        }
    }

    /**
     * Handle ProductCreated event
     */
    public function handleProductCreated(ProductCreated $event): void
    {
        if (!$this->enabled) {
            return;
        }

        try {
            $product = $event->product;

            $this->elasticsearch->index([
                'index' => 'products',
                'id' => $product->id,
                'body' => [
                    'id' => $product->id,
                    'name' => $product->pro_name,
                    'slug' => $product->pro_slug,
                    'price' => $product->pro_price,
                    'sale_price' => $product->pro_sale,
                    'description' => $product->pro_description_seo ?? '',
                    'category_id' => $product->pro_category_id,
                    'category' => $product->category->c_name ?? '',
                    'avatar' => $product->pro_avatar,
                    'view_count' => $product->pro_view ?? 0,
                    'sold_count' => 0,
                    'in_stock' => $product->pro_number > 0,
                    'active' => $product->pro_active == 1,
                    'created_at' => $product->created_at?->toIso8601String(),
                    'updated_at' => $product->updated_at?->toIso8601String(),
                ],
            ]);

            Log::info('Product indexed to Elasticsearch', ['product_id' => $product->id]);

        } catch (\Exception $e) {
            Log::error('Failed to index product to Elasticsearch', [
                'product_id' => $event->product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle ProductUpdated event
     */
    public function handleProductUpdated(ProductUpdated $event): void
    {
        if (!$this->enabled) {
            return;
        }

        try {
            $product = $event->product;

            $this->elasticsearch->update([
                'index' => 'products',
                'id' => $product->id,
                'body' => [
                    'doc' => [
                        'name' => $product->pro_name,
                        'slug' => $product->pro_slug,
                        'price' => $product->pro_price,
                        'sale_price' => $product->pro_sale,
                        'description' => $product->pro_description_seo ?? '',
                        'category_id' => $product->pro_category_id,
                        'category' => $product->category->c_name ?? '',
                        'avatar' => $product->pro_avatar,
                        'view_count' => $product->pro_view ?? 0,
                        'in_stock' => $product->pro_number > 0,
                        'active' => $product->pro_active == 1,
                        'updated_at' => $product->updated_at?->toIso8601String(),
                    ],
                ],
            ]);

            Log::info('Product updated in Elasticsearch', ['product_id' => $product->id]);

        } catch (\Exception $e) {
            Log::error('Failed to update product in Elasticsearch', [
                'product_id' => $event->product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle ProductDeleted event
     */
    public function handleProductDeleted(ProductDeleted $event): void
    {
        if (!$this->enabled) {
            return;
        }

        try {
            $this->elasticsearch->delete([
                'index' => 'products',
                'id' => $event->productId,
            ]);

            Log::info('Product deleted from Elasticsearch', ['product_id' => $event->productId]);

        } catch (\Exception $e) {
            Log::error('Failed to delete product from Elasticsearch', [
                'product_id' => $event->productId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Register event listeners
     */
    public function subscribe($events): array
    {
        return [
            ProductCreated::class => 'handleProductCreated',
            ProductUpdated::class => 'handleProductUpdated',
            ProductDeleted::class => 'handleProductDeleted',
        ];
    }
}
