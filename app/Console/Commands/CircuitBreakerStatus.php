<?php

namespace App\Console\Commands;

use App\Services\ExternalApiService;
use Illuminate\Console\Command;

class CircuitBreakerStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'circuit-breaker:status {service?}';

    /**
     * The console command description.
     */
    protected $description = 'Show circuit breaker status for services';

    /**
     * Execute the console command.
     */
    public function handle(ExternalApiService $apiService): int
    {
        $service = $this->argument('service');

        if ($service) {
            $this->showServiceStatus($apiService, $service);
        } else {
            $this->showAllServicesStatus($apiService);
        }

        return Command::SUCCESS;
    }

    private function showServiceStatus(ExternalApiService $apiService, string $service): void
    {
        $status = $apiService->getStatus($service);

        $this->info("Circuit Breaker Status for: {$service}");
        $this->line('');
        $this->table(
            ['Property', 'Value'],
            [
                ['State', $this->formatState($status['state'])],
                ['Failures', $status['failures']],
                ['Opened At', $status['opened_at'] ? date('Y-m-d H:i:s', $status['opened_at']) : 'N/A'],
            ]
        );
    }

    private function showAllServicesStatus(ExternalApiService $apiService): void
    {
        $services = array_keys(config('circuit_breaker.services', []));

        $this->info('Circuit Breaker Status - All Services');
        $this->line('');

        $rows = [];
        foreach ($services as $service) {
            $status = $apiService->getStatus($service);
            $rows[] = [
                $service,
                $this->formatState($status['state']),
                $status['failures'],
                $status['opened_at'] ? date('Y-m-d H:i:s', $status['opened_at']) : 'N/A',
            ];
        }

        $this->table(
            ['Service', 'State', 'Failures', 'Opened At'],
            $rows
        );
    }

    private function formatState(string $state): string
    {
        return match ($state) {
            'closed' => '<fg=green>CLOSED</>',
            'open' => '<fg=red>OPEN</>',
            'half_open' => '<fg=yellow>HALF_OPEN</>',
            default => $state,
        };
    }
}
