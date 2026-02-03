<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Support Module Routes
|--------------------------------------------------------------------------
*/

// Liên hệ
Route::get('lien-he', [ContactController::class, 'index'])->name('contact.index');
Route::post('lien-he', [ContactController::class, 'postContact']);
