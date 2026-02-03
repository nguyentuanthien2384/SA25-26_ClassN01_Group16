<?php

namespace App\Services\Saga;

use App\Models\Models\Transaction;

interface SagaStepInterface
{
    /**
     * Execute the step
     */
    public function execute(Transaction $transaction): void;

    /**
     * Compensate/rollback the step
     */
    public function compensate(Transaction $transaction): void;
}
