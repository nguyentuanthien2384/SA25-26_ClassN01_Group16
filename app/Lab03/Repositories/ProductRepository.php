<?php

namespace App\Lab03\Repositories;

use App\Models\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * Class ProductRepository
 * 
 * Lab 03 - Layered Architecture
 * Repository Layer: Data Access Implementation
 * 
 * This class is responsible for all database operations related to products.
 * It implements the Repository pattern to separate data access logic from business logic.
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var Product
     */
    protected $model;

    /**
     * ProductRepository constructor.
     * 
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    /**
     * Get all products with pagination
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15)
    {
        return $this->model
            ->where('pro_active', 1)
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get all products without pagination
     * 
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model
            ->where('pro_active', 1)
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * Find product by ID
     * 
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product
    {
        return $this->model->find($id);
    }

    /**
     * Create new product
     * 
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        // Generate slug from name if not provided
        if (!isset($data['pro_slug']) && isset($data['pro_name'])) {
            $data['pro_slug'] = Str::slug($data['pro_name']) . '-' . time();
        }

        // Set default values
        $data['pro_active'] = $data['pro_active'] ?? 1;
        $data['pro_hot'] = $data['pro_hot'] ?? 0;
        $data['pro_sale'] = $data['pro_sale'] ?? 0;
        $data['pro_pay'] = $data['pro_pay'] ?? 0;

        return $this->model->create($data);
    }

    /**
     * Update existing product
     * 
     * @param int $id
     * @param array $data
     * @return Product|null
     */
    public function update(int $id, array $data): ?Product
    {
        $product = $this->findById($id);

        if (!$product) {
            return null;
        }

        // Update slug if name changed
        if (isset($data['pro_name']) && $data['pro_name'] !== $product->pro_name) {
            $data['pro_slug'] = Str::slug($data['pro_name']) . '-' . $product->id;
        }

        $product->update($data);
        
        return $product->fresh();
    }

    /**
     * Delete product by ID
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $product = $this->findById($id);

        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    /**
     * Search products by name
     * 
     * @param string $keyword
     * @return Collection
     */
    public function searchByName(string $keyword): Collection
    {
        return $this->model
            ->where('pro_name', 'LIKE', "%{$keyword}%")
            ->where('pro_active', 1)
            ->get();
    }

    /**
     * Get products by category
     * 
     * @param int $categoryId
     * @return Collection
     */
    public function getByCategoryId(int $categoryId): Collection
    {
        return $this->model
            ->where('pro_category_id', $categoryId)
            ->where('pro_active', 1)
            ->get();
    }

    /**
     * Check if product exists
     * 
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }
}
