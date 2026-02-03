<?php

namespace App\Console\Commands;

use App\Services\ExternalApiService;
use Illuminate\Console\Command;

class CircuitBreakerReset extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'circuit-breaker:reset {service}';

    /**
     * The console command description.
     */
    protected $description = 'Reset circuit breaker for a service';

    /**
     * Execute the console command.
     */
    public function handle(ExternalApiService $apiService): int
    {
        $service = $this->argument('service');

        if (!$this->confirm("Are you sure you want to reset circuit breaker for '{$service}'?")) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        try {
            $apiService->reset($service);
            $this->info("✓ Circuit breaker for '{$service}' has been reset.");
            
            // Show new status
            $status = $apiService->getStatus($service);
            $this->line('');
            $this->table(
                ['Property', 'Value'],
                [
                    ['State', $status['state']],
                    ['Failures', $status['failures']],
                ]
            );

        } catch (\Exception $e) {
            $this->error("Failed to reset circuit breaker: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
