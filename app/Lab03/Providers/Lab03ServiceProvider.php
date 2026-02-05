<?php

namespace App\Lab03\Providers;

use Illuminate\Support\ServiceProvider;
use App\Lab03\Repositories\ProductRepositoryInterface;
use App\Lab03\Repositories\ProductRepository;

/**
 * Class Lab03ServiceProvider
 * 
 * Lab 03 - Layered Architecture
 * Service Provider for Dependency Injection
 * 
 * This provider binds interfaces to their concrete implementations,
 * enabling dependency injection throughout the application.
 */
class Lab03ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind ProductRepositoryInterface to ProductRepository
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        // You can add more bindings here as needed
        // Example:
        // $this->app->bind(ServiceInterface::class, ServiceImplementation::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Register Lab03 routes
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }
}
