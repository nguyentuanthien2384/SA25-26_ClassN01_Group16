<?php

namespace App\Providers;

use App\Services\ExternalApiService;
use Illuminate\Support\ServiceProvider;

class CircuitBreakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ExternalApiService::class, function ($app) {
            return new ExternalApiService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/circuit_breaker.php' => config_path('circuit_breaker.php'),
        ], 'circuit-breaker-config');
    }
}
