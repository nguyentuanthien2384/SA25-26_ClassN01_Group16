<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CatalogServiceController;
use App\Http\Controllers\Api\OrderServiceController;
use App\Http\Controllers\Api\UserServiceController;
use App\Http\Controllers\Api\NotificationServiceController;
use App\Http\Controllers\Api\MonitoringServiceController;

/*
|--------------------------------------------------------------------------
| Microservice Routes (service-level APIs)
|--------------------------------------------------------------------------
|
| Each prefix maps to a dedicated microservice:
|   /api/catalog/*       → CatalogServiceController   (port 9005)
|   /api/orders/*        → OrderServiceController      (port 9002)
|   /api/users/*         → UserServiceController       (port 9003)
|   /api/notifications/* → NotificationServiceController (port 9004)
|   /api/monitoring/*    → MonitoringServiceController
|
*/

// ── Catalog Service ─────────────────────────────────────────────
Route::prefix('catalog')->group(function () {
    Route::get('/products',       [CatalogServiceController::class, 'index']);
    Route::get('/products/{id}',  [CatalogServiceController::class, 'show']);
    Route::post('/products',      [CatalogServiceController::class, 'store']);
    Route::put('/products/{id}',  [CatalogServiceController::class, 'update']);
    Route::delete('/products/{id}', [CatalogServiceController::class, 'destroy']);
    Route::get('/categories',     [CatalogServiceController::class, 'categories']);
});

// ── Order Service ───────────────────────────────────────────────
Route::prefix('orders')->group(function () {
    Route::get('/',               [OrderServiceController::class, 'index']);
    Route::get('/{id}',           [OrderServiceController::class, 'show']);
    Route::post('/',              [OrderServiceController::class, 'store']);
    Route::patch('/{id}/status',  [OrderServiceController::class, 'updateStatus']);
});

// ── User Service ────────────────────────────────────────────────
Route::prefix('users')->group(function () {
    Route::post('/register',      [UserServiceController::class, 'register']);
    Route::post('/login',         [UserServiceController::class, 'login']);
    Route::get('/',               [UserServiceController::class, 'index']);
    Route::get('/{id}',           [UserServiceController::class, 'show']);
    Route::put('/{id}',           [UserServiceController::class, 'update']);
});

// ── Notification Service ────────────────────────────────────────
Route::prefix('notifications')->group(function () {
    Route::post('/send',          [NotificationServiceController::class, 'send']);
    Route::post('/outbox/process', [NotificationServiceController::class, 'processOutbox']);
    Route::get('/history',        [NotificationServiceController::class, 'history']);
});

// ── Monitoring Service ──────────────────────────────────────────
Route::prefix('monitoring')->group(function () {
    Route::get('/health',         [MonitoringServiceController::class, 'health']);
    Route::get('/metrics',        [MonitoringServiceController::class, 'metrics']);
    Route::get('/services',       [MonitoringServiceController::class, 'services']);
});
