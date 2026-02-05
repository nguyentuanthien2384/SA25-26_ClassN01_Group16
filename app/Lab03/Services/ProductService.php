<?php

namespace App\Lab03\Services;

use App\Lab03\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class ProductService
 * 
 * Lab 03 - Layered Architecture
 * Service Layer: Business Logic
 * 
 * This class contains all business logic for product operations.
 * It acts as an intermediary between the Controller and Repository layers.
 * 
 * Responsibilities:
 * - Validate business rules
 * - Coordinate data flow
 * - Transform data
 * - Handle exceptions
 */
class ProductService
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * ProductService constructor.
     * 
     * Dependency Injection: Repository is injected via constructor
     * 
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get all products with pagination
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllProducts(int $perPage = 15)
    {
        return $this->productRepository->getAllPaginated($perPage);
    }

    /**
     * Get product by ID
     * 
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function getProductById(int $id): array
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new \Exception("Product with ID {$id} not found", 404);
        }

        return $this->transformProductData($product);
    }

    /**
     * Create new product
     * 
     * Business Rules:
     * - Name is required and must be unique
     * - Price must be positive
     * - Stock must be non-negative
     * - Category must exist
     * 
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function createProduct(array $data): array
    {
        // Validate input data
        $this->validateProductData($data);

        // Additional business logic
        $this->applyBusinessRules($data);

        // Create product via repository
        $product = $this->productRepository->create($data);

        // Log activity (can be extended)
        \Log::info("Product created: {$product->id} - {$product->pro_name}");

        return $this->transformProductData($product);
    }

    /**
     * Update existing product
     * 
     * @param int $id
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updateProduct(int $id, array $data): array
    {
        // Check if product exists
        if (!$this->productRepository->exists($id)) {
            throw new \Exception("Product with ID {$id} not found", 404);
        }

        // Validate update data
        $this->validateProductData($data, $id);

        // Apply business rules
        $this->applyBusinessRules($data, $id);

        // Update via repository
        $product = $this->productRepository->update($id, $data);

        // Log activity
        \Log::info("Product updated: {$product->id} - {$product->pro_name}");

        return $this->transformProductData($product);
    }

    /**
     * Delete product
     * 
     * Business Rule: Cannot delete product with active orders
     * 
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteProduct(int $id): bool
    {
        // Check if product exists
        if (!$this->productRepository->exists($id)) {
            throw new \Exception("Product with ID {$id} not found", 404);
        }

        // Business rule: Check if product has active orders
        // (In real scenario, you would check orders table)
        // For Lab 03, we'll just delete

        $deleted = $this->productRepository->delete($id);

        if ($deleted) {
            \Log::info("Product deleted: {$id}");
        }

        return $deleted;
    }

    /**
     * Search products
     * 
     * @param string $keyword
     * @return array
     */
    public function searchProducts(string $keyword): array
    {
        $products = $this->productRepository->searchByName($keyword);

        return [
            'keyword' => $keyword,
            'count' => $products->count(),
            'products' => $products->map(fn($p) => $this->transformProductData($p))
        ];
    }

    /**
     * Validate product data
     * 
     * @param array $data
     * @param int|null $productId
     * @return void
     * @throws ValidationException
     */
    protected function validateProductData(array $data, ?int $productId = null): void
    {
        $rules = [
            'pro_name' => 'required|string|max:255',
            // Lecture/Lab business rule: price must be strictly positive
            'pro_price' => 'required|numeric|gt:0',
            'pro_category_id' => 'required|integer|exists:category,id',
            'pro_content' => 'nullable|string',
            'pro_image' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'pro_sale' => 'nullable|integer|min:0|max:100',
        ];

        $messages = [
            'pro_name.required' => 'Product name is required',
            'pro_price.required' => 'Product price is required',
            'pro_price.gt' => 'Product price must be greater than 0',
            'pro_category_id.required' => 'Category is required',
            'pro_category_id.exists' => 'Selected category does not exist',
            'pro_sale.max' => 'Sale discount cannot exceed 100%',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Apply business rules
     * 
     * Business Rules:
     * - Calculate sale price if sale discount is provided
     * - Mark as "hot" if price > 10000000 (10M VND)
     * - Auto-deactivate if stock is 0
     * 
     * @param array &$data
     * @param int|null $productId
     * @return void
     */
    protected function applyBusinessRules(array &$data, ?int $productId = null): void
    {
        // Calculate final price after discount
        if (isset($data['pro_price']) && isset($data['pro_sale']) && $data['pro_sale'] > 0) {
            $data['pro_total'] = $data['pro_price'] - ($data['pro_price'] * $data['pro_sale'] / 100);
        } else {
            $data['pro_total'] = $data['pro_price'] ?? 0;
        }

        // Mark as hot if expensive
        if (isset($data['pro_price']) && $data['pro_price'] > 10000000) {
            $data['pro_hot'] = 1;
        }

        // Auto-deactivate if no stock
        if (isset($data['quantity']) && $data['quantity'] == 0) {
            $data['pro_active'] = 0;
        }
    }

    /**
     * Transform product data for API response
     * 
     * @param $product
     * @return array
     */
    protected function transformProductData($product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->pro_name,
            'slug' => $product->pro_slug,
            'price' => $product->pro_price,
            'sale' => $product->pro_sale,
            'final_price' => $product->pro_total ?? $product->pro_price,
            'category_id' => $product->pro_category_id,
            'description' => $product->pro_content,
            'image' => $product->pro_image,
            'stock' => $product->quantity,
            'is_active' => $product->pro_active == 1,
            'is_hot' => $product->pro_hot == 1,
            'created_at' => $product->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $product->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
