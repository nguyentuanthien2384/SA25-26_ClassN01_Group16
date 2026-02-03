<?php

namespace App\Services\Saga\Steps;

use App\Models\Models\Transaction;
use App\Services\Saga\SagaStepInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Saga Step: Process Payment
 */
class ProcessPaymentStep implements SagaStepInterface
{
    public function execute(Transaction $transaction): void
    {
        Log::info('Processing payment', [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->tr_total,
            'method' => $transaction->tr_payment_method,
        ]);

        // If COD, mark as pending payment
        if ($transaction->tr_payment_method === 'cod') {
            $transaction->update([
                'tr_payment_status' => 0, // Pending
                'tr_status' => Transaction::STATUS_WAIT,
            ]);
            
            Log::info('Payment method is COD, marked as pending');
            return;
        }

        // For online payments (MoMo, VNPay, PayPal)
        // Check if payment is already completed
        if ($transaction->tr_payment_status == 1) {
            Log::info('Payment already completed');
            return;
        }

        // TODO: Verify payment with external gateway
        /*
        $response = Http::timeout(30)->post(config('services.payment.url') . '/verify', [
            'transaction_id' => $transaction->id,
            'payment_code' => $transaction->tr_payment_code,
        ]);

        if (!$response->successful() || $response->json('status') !== 'success') {
            throw new \Exception('Payment verification failed');
        }
        */

        // Mark payment as successful
        $transaction->update([
            'tr_payment_status' => 1, // Success
            'tr_status' => Transaction::STATUS_SUCCESS,
        ]);

        Log::info('Payment processed successfully', [
            'transaction_id' => $transaction->id,
        ]);
    }

    public function compensate(Transaction $transaction): void
    {
        // Refund payment if it was processed
        if ($transaction->tr_payment_status != 1) {
            return; // Nothing to refund
        }

        Log::info('Initiating payment refund', [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->tr_total,
        ]);

        // TODO: Call Payment Gateway to refund
        /*
        Http::timeout(30)->post(config('services.payment.url') . '/refund', [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->tr_total,
        ]);
        */

        $transaction->update([
            'tr_payment_status' => 2, // Refunded
            'tr_status' => Transaction::STATUS_CANCELLED,
        ]);

        Log::info('Payment refunded successfully', [
            'transaction_id' => $transaction->id,
        ]);
    }
}
