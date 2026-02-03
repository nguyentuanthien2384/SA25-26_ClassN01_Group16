<?php

namespace App\Services\Saga;

use App\Models\Models\Transaction;
use Illuminate\Support\Facades\Log;

/**
 * Order Saga - Orchestrates distributed transaction
 * 
 * Steps:
 * 1. Reserve Stock (Inventory Service)
 * 2. Process Payment (Payment Service)
 * 3. Create Shipment (Shipping Service)
 * 4. Send Notification (Notification Service)
 */
class OrderSaga
{
    private array $steps = [];
    private array $executedSteps = [];
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Add a step to the saga
     */
    public function addStep(SagaStepInterface $step): self
    {
        $this->steps[] = $step;
        return $this;
    }

    /**
     * Execute the saga
     */
    public function execute(): bool
    {
        Log::info('Starting Order Saga', [
            'transaction_id' => $this->transaction->id,
            'steps_count' => count($this->steps),
        ]);

        try {
            // Execute each step
            foreach ($this->steps as $step) {
                $stepName = class_basename($step);
                
                Log::info("Executing saga step: {$stepName}", [
                    'transaction_id' => $this->transaction->id,
                ]);

                $step->execute($this->transaction);
                $this->executedSteps[] = $step;

                Log::info("Saga step completed: {$stepName}");
            }

            Log::info('Order Saga completed successfully', [
                'transaction_id' => $this->transaction->id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Order Saga failed, initiating compensation', [
                'transaction_id' => $this->transaction->id,
                'failed_at_step' => count($this->executedSteps),
                'error' => $e->getMessage(),
            ]);

            // Compensate in reverse order
            $this->compensate();

            throw $e;
        }
    }

    /**
     * Compensate executed steps
     */
    private function compensate(): void
    {
        $compensatedSteps = [];

        foreach (array_reverse($this->executedSteps) as $step) {
            $stepName = class_basename($step);

            try {
                Log::info("Compensating step: {$stepName}", [
                    'transaction_id' => $this->transaction->id,
                ]);

                $step->compensate($this->transaction);
                $compensatedSteps[] = $stepName;

                Log::info("Step compensated: {$stepName}");

            } catch (\Exception $compensateError) {
                Log::critical('Saga compensation failed', [
                    'transaction_id' => $this->transaction->id,
                    'step' => $stepName,
                    'error' => $compensateError->getMessage(),
                    'compensated_so_far' => $compensatedSteps,
                ]);

                // Continue compensating other steps even if one fails
            }
        }

        Log::warning('Saga compensation completed', [
            'transaction_id' => $this->transaction->id,
            'compensated_steps' => $compensatedSteps,
        ]);
    }

    /**
     * Get saga status
     */
    public function getStatus(): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'total_steps' => count($this->steps),
            'executed_steps' => count($this->executedSteps),
            'steps' => array_map(fn($step) => class_basename($step), $this->steps),
            'executed' => array_map(fn($step) => class_basename($step), $this->executedSteps),
        ];
    }
}
