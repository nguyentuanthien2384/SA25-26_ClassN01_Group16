<?php

use Illuminate\Support\Facades\Route;
use Modules\Review\App\Http\Controllers\RatingController;

/*
|--------------------------------------------------------------------------
| Review Module Routes
|--------------------------------------------------------------------------
*/

// Đánh giá sản phẩm
Route::group(['prefix' => 'rating', 'middleware' => 'CheckLoginUser'], function(){
    Route::post('/danh-gia/{id}', [RatingController::class, 'postRating'])->name('postRating');
});
