<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Model for Content Service
 * 
 * All Content-related models should extend this class
 * Uses: content_db database
 * 
 * Tables:
 * - articles
 * - banners
 * - contacts
 */
abstract class ContentModel extends Model
{
    /**
     * The database connection to use
     *
     * @var string
     */
    protected $connection = 'content';

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
