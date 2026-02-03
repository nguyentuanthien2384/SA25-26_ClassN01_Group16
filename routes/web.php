<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Core Web Routes
|--------------------------------------------------------------------------
|
| Core routes - Module routes are loaded from Modules/{Module}/routes/web.php
|
*/

// Laravel default auth routes (if needed)
Auth::routes();

// Laravel File Manager
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => []], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

// ============================================================================
// HEALTH CHECK (Web version)
// ============================================================================
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
        'service' => 'laravel-web',
    ]);
});
