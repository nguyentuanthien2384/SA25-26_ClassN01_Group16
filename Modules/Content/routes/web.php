<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| Content Module Routes
|--------------------------------------------------------------------------
*/

// Bài viết
Route::get('bai-viet', [ArticleController::class, 'getArticle'])->name('get.list.article');
Route::get('bai-viet/{slug}-{id}', [ArticleController::class, 'getDetailArticle'])->name('get.detail.article');
