<?php

namespace App\Providers;

use App\Services\ServiceDiscovery;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

/**
 * Service Discovery Provider
 * 
 * Auto-registers the application with Consul on boot
 * and deregisters on shutdown
 */
class ServiceDiscoveryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ServiceDiscovery::class, function ($app) {
            return new ServiceDiscovery();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only register if not in console (except for serve command)
        if ($this->app->runningInConsole() && !$this->isServeCommand()) {
            return;
        }

        if (!config('services.consul.enabled', false)) {
            return;
        }

        try {
            $serviceDiscovery = $this->app->make(ServiceDiscovery::class);

            if ($serviceDiscovery->register()) {
                Log::info('Service registered with Consul');
            }

            register_shutdown_function(function () use ($serviceDiscovery) {
                try {
                    $serviceDiscovery->deregister();
                } catch (\Exception $e) {
                    Log::error('Failed to deregister from Consul', ['error' => $e->getMessage()]);
                }
            });

        } catch (\Exception $e) {
            Log::warning('Could not register with Consul', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Check if running serve command
     */
    private function isServeCommand(): bool
    {
        $argv = $_SERVER['argv'] ?? [];
        return in_array('serve', $argv);
    }
}
