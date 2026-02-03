<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\Models\OutboxMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SaveOrderPlacedToOutbox
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event - Save to Outbox for reliable async processing
     */
    public function handle(OrderPlaced $event): void
    {
        try {
            // Get user email
            $user = \App\Models\User::find($event->transaction->tr_user_id);
            $userEmail = $user ? $user->email : null;

            OutboxMessage::create([
                'aggregate_type' => 'Transaction',
                'aggregate_id' => $event->transaction->id,
                'event_type' => 'OrderPlaced',
                'payload' => [
                    'transaction_id' => $event->transaction->id,
                    'user_id' => $event->transaction->tr_user_id,
                    'user_email' => $userEmail,
                    'total' => $event->transaction->tr_total,
                    'payment_method' => $event->transaction->tr_payment_method,
                    'order_details' => $event->orderDetails,
                ],
                'occurred_at' => now(),
            ]);

            Log::info('OrderPlaced event saved to outbox', [
                'transaction_id' => $event->transaction->id,
                'user_email' => $userEmail,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save OrderPlaced to outbox', [
                'transaction_id' => $event->transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
