<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Model for Catalog Service
 * 
 * All Catalog-related models should extend this class
 * Uses: catalog_db database
 * 
 * Tables:
 * - categories
 * - products
 * - pro_image
 * - suppliers
 * - warehouses
 * - import_goods
 * - import_goods_detail
 */
abstract class CatalogModel extends Model
{
    /**
     * The database connection to use
     *
     * @var string
     */
    protected $connection = 'catalog';

    /**
     * Get the connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        return $this->connection;
    }
}
