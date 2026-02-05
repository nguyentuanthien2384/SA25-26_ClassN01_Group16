<?php

namespace App\Lab03\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Models\Product;

/**
 * Interface ProductRepositoryInterface
 * 
 * Lab 03 - Layered Architecture
 * Repository Layer: Data Access Interface
 */
interface ProductRepositoryInterface
{
    /**
     * Get all products with pagination
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15);

    /**
     * Get all products without pagination
     * 
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Find product by ID
     * 
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product;

    /**
     * Create new product
     * 
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product;

    /**
     * Update existing product
     * 
     * @param int $id
     * @param array $data
     * @return Product|null
     */
    public function update(int $id, array $data): ?Product;

    /**
     * Delete product by ID
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Search products by name
     * 
     * @param string $keyword
     * @return Collection
     */
    public function searchByName(string $keyword): Collection;

    /**
     * Get products by category
     * 
     * @param int $categoryId
     * @return Collection
     */
    public function getByCategoryId(int $categoryId): Collection;

    /**
     * Check if product exists
     * 
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool;
}
