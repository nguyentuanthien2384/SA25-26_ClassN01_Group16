<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Payment Module Routes
|--------------------------------------------------------------------------
*/

// Thanh toán
Route::group(['prefix' => 'payment', 'middleware' => 'CheckLoginUser'], function(){
    Route::get('/{method}/{transaction}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/{method}/{transaction}/init', [PaymentController::class, 'init'])->name('payment.init');
    
    // MoMo
    Route::get('/momo/return/{transaction}', [PaymentController::class, 'momoReturn'])->name('payment.momo.return');
    Route::post('/momo/ipn/{transaction}', [PaymentController::class, 'momoIpn'])->name('payment.momo.ipn');
    
    // PayPal
    Route::get('/paypal/return/{transaction}', [PaymentController::class, 'paypalReturn'])->name('payment.paypal.return');
    Route::get('/paypal/cancel/{transaction}', [PaymentController::class, 'paypalCancel'])->name('payment.paypal.cancel');
    
    // QR Code
    Route::post('/qrcode/{transaction}/confirm', [PaymentController::class, 'qrcodeConfirm'])->name('payment.qrcode.confirm');
    
    // VNPay
    Route::get('/vnpay/return/{transaction}', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
    Route::get('/vnpay/ipn/{transaction}', [PaymentController::class, 'vnpayIpn'])->name('payment.vnpay.ipn');
});
