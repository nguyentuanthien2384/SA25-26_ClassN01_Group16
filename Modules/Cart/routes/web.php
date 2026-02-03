<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Cart Module Routes
|--------------------------------------------------------------------------
*/

// Giỏ hàng
Route::group(['prefix' => 'cart'], function(){
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::get('/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/delete/{product}', [CartController::class, 'delete'])->name('cart.delete');
    Route::get('/update/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Đặt hàng (checkout)
Route::group(['prefix' => 'oder', 'middleware' => 'CheckLoginUser'], function(){
    Route::get('/pay', [CartController::class, 'getpay'])->name('form.pay');
    Route::post('/pay', [CartController::class, 'saveCart']);
});
