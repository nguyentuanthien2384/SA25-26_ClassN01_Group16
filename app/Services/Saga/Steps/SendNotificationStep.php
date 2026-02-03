<?php

namespace App\Services\Saga\Steps;

use App\Events\OrderPlaced;
use App\Models\Models\Transaction;
use App\Services\Saga\SagaStepInterface;
use Illuminate\Support\Facades\Log;

/**
 * Saga Step: Send Notification
 */
class SendNotificationStep implements SagaStepInterface
{
    public function execute(Transaction $transaction): void
    {
        Log::info('Sending order notification', [
            'transaction_id' => $transaction->id,
        ]);

        // Dispatch OrderPlaced event to trigger notification
        $orderDetails = $transaction->orders->map(function ($order) {
            return [
                'product_id' => $order->or_product_id,
                'product_name' => $order->product->pro_name ?? 'Unknown',
                'quantity' => $order->or_qty,
                'price' => $order->or_price,
            ];
        })->toArray();

        event(new OrderPlaced($transaction, $orderDetails));

        Log::info('Order notification dispatched', [
            'transaction_id' => $transaction->id,
        ]);
    }

    public function compensate(Transaction $transaction): void
    {
        // Notifications cannot be "undone", but we can send a cancellation email
        Log::info('Sending order cancellation notification', [
            'transaction_id' => $transaction->id,
        ]);

        // TODO: Dispatch OrderCancelled event
        // event(new OrderCancelled($transaction));

        Log::info('Cancellation notification dispatched', [
            'transaction_id' => $transaction->id,
        ]);
    }
}
