<?php

namespace App\Services\CQRS;

use Illuminate\Support\Facades\Log;

/**
 * CQRS - Query Side
 * Handles all read operations for products via Elasticsearch
 * NOTE: Elasticsearch is optional. Install: composer require elasticsearch/elasticsearch
 */
class ProductQueryService
{
    private $elasticsearch;
    private $enabled = false;

    public function __construct()
    {
        // Only initialize if Elasticsearch package is installed
        if (class_exists(\Elasticsearch\ClientBuilder::class)) {
            try {
                $hosts = [
                    config('services.elasticsearch.host', 'http://localhost:9200')
                ];

                $this->elasticsearch = \Elasticsearch\ClientBuilder::create()
                    ->setHosts($hosts)
                    ->build();
                $this->enabled = true;
            } catch (\Exception $e) {
                Log::warning('Elasticsearch not available: ' . $e->getMessage());
                $this->enabled = false;
            }
        }
    }

    /**
     * Search products by keyword
     */
    public function search(string $keyword, int $limit = 20, int $offset = 0): array
    {
        // If Elasticsearch not available, use database
        if (!$this->enabled) {
            return $this->fallbackSearch($keyword, $limit, $offset);
        }

        try {
            $params = [
                'index' => 'products',
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $keyword,
                            'fields' => ['name^3', 'description^2', 'category'],
                            'fuzziness' => 'AUTO',
                        ],
                    ],
                    'from' => $offset,
                    'size' => $limit,
                    'sort' => [
                        '_score' => ['order' => 'desc'],
                    ],
                ],
            ];

            $response = $this->elasticsearch->search($params);

            return [
                'total' => $response['hits']['total']['value'],
                'products' => array_map(function ($hit) {
                    return array_merge($hit['_source'], ['score' => $hit['_score']]);
                }, $response['hits']['hits']),
            ];

        } catch (\Exception $e) {
            Log::error('Elasticsearch search failed', [
                'keyword' => $keyword,
                'error' => $e->getMessage(),
            ]);

            // Fallback to database
            return $this->fallbackSearch($keyword, $limit, $offset);
        }
    }

    /**
     * Get product by ID from Elasticsearch
     */
    public function findById(int $productId): ?array
    {
        try {
            $params = [
                'index' => 'products',
                'id' => $productId,
            ];

            $response = $this->elasticsearch->get($params);

            return $response['_source'];

        } catch (\Exception $e) {
            Log::warning('Product not found in Elasticsearch', [
                'product_id' => $productId,
            ]);

            return null;
        }
    }

    /**
     * Get products by category
     */
    public function findByCategory(int $categoryId, int $limit = 20, int $offset = 0): array
    {
        try {
            $params = [
                'index' => 'products',
                'body' => [
                    'query' => [
                        'term' => [
                            'category_id' => $categoryId,
                        ],
                    ],
                    'from' => $offset,
                    'size' => $limit,
                    'sort' => [
                        'created_at' => ['order' => 'desc'],
                    ],
                ],
            ];

            $response = $this->elasticsearch->search($params);

            return [
                'total' => $response['hits']['total']['value'],
                'products' => array_map(fn($hit) => $hit['_source'], $response['hits']['hits']),
            ];

        } catch (\Exception $e) {
            Log::error('Elasticsearch category search failed', [
                'category_id' => $categoryId,
                'error' => $e->getMessage(),
            ]);

            return ['total' => 0, 'products' => []];
        }
    }

    /**
     * Get trending/hot products
     */
    public function getTrending(int $limit = 10): array
    {
        try {
            $params = [
                'index' => 'products',
                'body' => [
                    'query' => [
                        'match_all' => (object)[],
                    ],
                    'size' => $limit,
                    'sort' => [
                        'view_count' => ['order' => 'desc'],
                        'sold_count' => ['order' => 'desc'],
                    ],
                ],
            ];

            $response = $this->elasticsearch->search($params);

            return array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);

        } catch (\Exception $e) {
            Log::error('Elasticsearch trending query failed', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Fallback to database search if Elasticsearch fails
     */
    private function fallbackSearch(string $keyword, int $limit, int $offset): array
    {
        $products = \App\Models\Models\Product::where('pro_name', 'LIKE', "%{$keyword}%")
            ->orWhere('pro_description', 'LIKE', "%{$keyword}%")
            ->skip($offset)
            ->take($limit)
            ->get()
            ->toArray();

        return [
            'total' => count($products),
            'products' => $products,
            'fallback' => true,
        ];
    }
}
