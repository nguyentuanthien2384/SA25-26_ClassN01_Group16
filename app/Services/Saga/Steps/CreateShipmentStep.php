<?php

namespace App\Services\Saga\Steps;

use App\Models\Models\Transaction;
use App\Services\Saga\SagaStepInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Saga Step: Create Shipment
 */
class CreateShipmentStep implements SagaStepInterface
{
    public function execute(Transaction $transaction): void
    {
        Log::info('Creating shipment', [
            'transaction_id' => $transaction->id,
        ]);

        $shipmentData = [
            'transaction_id' => $transaction->id,
            'recipient_name' => $transaction->tr_name,
            'recipient_phone' => $transaction->tr_phone,
            'recipient_email' => $transaction->tr_email,
            'recipient_address' => $transaction->tr_address,
            'items' => $transaction->orders->map(function ($order) {
                return [
                    'product_id' => $order->or_product_id,
                    'quantity' => $order->or_qty,
                    'price' => $order->or_price,
                ];
            })->toArray(),
        ];

        // TODO: Call Shipping Service API
        /*
        $response = Http::timeout(30)->post(config('services.shipping.url') . '/create', $shipmentData);

        if (!$response->successful()) {
            throw new \Exception('Failed to create shipment: ' . $response->body());
        }

        $shipmentId = $response->json('shipment_id');
        $trackingCode = $response->json('tracking_code');
        */

        // Simulate shipment creation
        $shipmentId = 'SHIP-' . $transaction->id;
        $trackingCode = 'TRACK-' . strtoupper(substr(md5($transaction->id), 0, 10));

        $transaction->update([
            'tr_metadata' => array_merge($transaction->tr_metadata ?? [], [
                'shipment_id' => $shipmentId,
                'tracking_code' => $trackingCode,
                'shipment_created' => true,
            ]),
        ]);

        Log::info('Shipment created successfully', [
            'transaction_id' => $transaction->id,
            'shipment_id' => $shipmentId,
            'tracking_code' => $trackingCode,
        ]);
    }

    public function compensate(Transaction $transaction): void
    {
        $metadata = $transaction->tr_metadata ?? [];
        
        if (!($metadata['shipment_created'] ?? false)) {
            return; // Nothing to cancel
        }

        Log::info('Cancelling shipment', [
            'transaction_id' => $transaction->id,
            'shipment_id' => $metadata['shipment_id'] ?? null,
        ]);

        // TODO: Call Shipping Service API to cancel
        /*
        Http::timeout(30)->post(config('services.shipping.url') . '/cancel', [
            'shipment_id' => $metadata['shipment_id'],
        ]);
        */

        $transaction->update([
            'tr_metadata' => array_merge($metadata, [
                'shipment_created' => false,
                'shipment_cancelled' => true,
            ]),
        ]);

        Log::info('Shipment cancelled successfully', [
            'transaction_id' => $transaction->id,
        ]);
    }
}
