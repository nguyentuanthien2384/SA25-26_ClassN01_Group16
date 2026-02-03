<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\App\Http\Controllers\AuthUserController;
use Modules\Customer\App\Http\Controllers\UserController;
use Modules\Customer\App\Http\Controllers\WishlistController;

/*
|--------------------------------------------------------------------------
| Customer Module Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::group(['prefix' => 'auth'], function(){
    Route::get('/login', [AuthUserController::class, 'login'])->name('login');
    Route::post('/login', [AuthUserController::class, 'postLogin'])->name('postLogin');
    Route::get('/register', [AuthUserController::class, 'register'])->name('register');
    Route::post('/register', [AuthUserController::class, 'postRegister'])->name('postRegister');
    Route::get('/logout', [AuthUserController::class, 'logout'])->name('logout');
});

// User Profile
Route::group(['prefix' => 'user', 'middleware' => 'CheckLoginUser'], function(){
    Route::get('user', [UserController::class, 'index'])->name('user.index');
});

// Wishlist
Route::group(['prefix' => 'wishlist', 'middleware' => 'CheckLoginUser'], function () {
    Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::get('/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle.ajax');
    Route::get('/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
});
