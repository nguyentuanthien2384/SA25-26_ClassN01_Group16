<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Model for Customer Service
 * 
 * All Customer-related models should extend this class
 * Uses: customer_db database
 * 
 * Tables:
 * - users
 * - wishlists
 */
abstract class CustomerModel extends Model
{
    /**
     * The database connection to use
     *
     * @var string
     */
    protected $connection = 'customer';

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
