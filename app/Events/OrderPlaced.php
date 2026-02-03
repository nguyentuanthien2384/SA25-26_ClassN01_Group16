<?php

namespace App\Events;

use App\Models\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced
{
    use Dispatchable, SerializesModels;

    public Transaction $transaction;
    public array $orderDetails;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction, array $orderDetails = [])
    {
        $this->transaction = $transaction;
        $this->orderDetails = $orderDetails;
    }
}
