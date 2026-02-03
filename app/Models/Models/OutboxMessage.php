<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutboxMessage extends Model
{
    use HasFactory;
    
    protected $table = 'outbox_messages';
    
    protected $fillable = [
        'aggregate_type',
        'aggregate_id',
        'event_type',
        'payload',
        'occurred_at',
        'published',
        'published_at',
    ];
    
    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'published_at' => 'datetime',
        'published' => 'boolean',
    ];
    
    /**
     * Scope unpublished messages
     */
    public function scopeUnpublished($query)
    {
        return $query->where('published', false)
                     ->orderBy('occurred_at', 'asc');
    }
    
    /**
     * Mark as published
     */
    public function markPublished()
    {
        $this->update([
            'published' => true,
            'published_at' => now(),
        ]);
    }
}
