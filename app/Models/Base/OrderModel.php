<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Model for Order Service
 * 
 * All Order-related models should extend this class
 * Uses: order_db database
 * 
 * Tables:
 * - transactions
 * - transaction_detail
 * - ratings
 */
abstract class OrderModel extends Model
{
    /**
     * The database connection to use
     *
     * @var string
     */
    protected $connection = 'order';

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
