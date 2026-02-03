<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\App\Http\Controllers\HomeController;
use Modules\Catalog\App\Http\Controllers\CategoryController;
use Modules\Catalog\App\Http\Controllers\ProductDetailController;

/*
|--------------------------------------------------------------------------
| Catalog Module Routes
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Danh mục sản phẩm
Route::get('danh-muc/{slug}-{id}', [CategoryController::class, 'getListProduct'])->name('get.list.product');
Route::get('san-pham', [CategoryController::class, 'getListProduct'])->name('get.product.list');

// Chi tiết sản phẩm
Route::get('san-pham/{slug}-{id}', [ProductDetailController::class, 'productDetail'])->name('get.detail.product');
