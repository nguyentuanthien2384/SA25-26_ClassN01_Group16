<?php

namespace App\Services\Saga\Steps;

use App\Models\Models\Transaction;
use App\Services\Saga\SagaStepInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Saga Step: Reserve Stock in Inventory Service
 */
class ReserveStockStep implements SagaStepInterface
{
    public function execute(Transaction $transaction): void
    {
        $items = $transaction->orders->map(function ($order) {
            return [
                'product_id' => $order->or_product_id,
                'quantity' => $order->or_qty,
            ];
        })->toArray();

        // TODO: Call Inventory Service API
        // For now, simulate the reservation
        
        Log::info('Reserving stock', [
            'transaction_id' => $transaction->id,
            'items' => $items,
        ]);

        // Simulate API call
        /*
        $response = Http::timeout(30)->post(config('services.inventory.url') . '/reserve', [
            'transaction_id' => $transaction->id,
            'items' => $items,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to reserve stock: ' . $response->body());
        }
        */

        // Store reservation ID for compensation
        $transaction->update([
            'tr_metadata' => array_merge($transaction->tr_metadata ?? [], [
                'stock_reserved' => true,
                'stock_reservation_id' => 'RES-' . $transaction->id,
            ]),
        ]);

        Log::info('Stock reserved successfully', [
            'transaction_id' => $transaction->id,
        ]);
    }

    public function compensate(Transaction $transaction): void
    {
        $metadata = $transaction->tr_metadata ?? [];
        
        if (!($metadata['stock_reserved'] ?? false)) {
            return; // Nothing to compensate
        }

        Log::info('Releasing reserved stock', [
            'transaction_id' => $transaction->id,
            'reservation_id' => $metadata['stock_reservation_id'] ?? null,
        ]);

        // TODO: Call Inventory Service API to release stock
        /*
        Http::timeout(30)->post(config('services.inventory.url') . '/release', [
            'transaction_id' => $transaction->id,
            'reservation_id' => $metadata['stock_reservation_id'],
        ]);
        */

        $transaction->update([
            'tr_metadata' => array_merge($metadata, [
                'stock_reserved' => false,
                'stock_released' => true,
            ]),
        ]);

        Log::info('Stock released successfully', [
            'transaction_id' => $transaction->id,
        ]);
    }
}
