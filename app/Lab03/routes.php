<?php

use Illuminate\Support\Facades\Route;
use App\Lab03\Controllers\ProductController;

/**
 * Lab 03 - Layered Architecture API Routes
 * 
 * RESTful API endpoints for product CRUD operations
 * Base URL: /api/lab03
 */

Route::prefix('api/lab03')->group(function () {
    
    // Products API Routes
    Route::prefix('products')->group(function () {
        
        // GET /api/lab03/products - List all products (with pagination)
        Route::get('/', [ProductController::class, 'index'])
            ->name('lab03.products.index');
        
        // GET /api/lab03/products/search - Search products
        Route::get('/search', [ProductController::class, 'search'])
            ->name('lab03.products.search');
        
        // GET /api/lab03/products/{id} - Get single product
        Route::get('/{id}', [ProductController::class, 'show'])
            ->name('lab03.products.show')
            ->where('id', '[0-9]+');
        
        // POST /api/lab03/products - Create new product
        Route::post('/', [ProductController::class, 'store'])
            ->name('lab03.products.store');
        
        // PUT /api/lab03/products/{id} - Update product
        Route::put('/{id}', [ProductController::class, 'update'])
            ->name('lab03.products.update')
            ->where('id', '[0-9]+');
        
        // DELETE /api/lab03/products/{id} - Delete product
        Route::delete('/{id}', [ProductController::class, 'destroy'])
            ->name('lab03.products.destroy')
            ->where('id', '[0-9]+');
    });
    
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'OK',
            'message' => 'Lab 03 API is running',
            'timestamp' => now()->toIso8601String()
        ]);
    })->name('lab03.health');
});
