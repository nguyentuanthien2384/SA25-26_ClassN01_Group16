<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductDetailController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\RegisterController;
// use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();
Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('danh-muc/{slug}-{id}', [CategoryController::class, 'getListProduct'])->name('get.list.product');
Route::get('san-pham/{slug}-{id}', [ProductDetailController::class, 'productDetail'])->name('get.detail.product');
Route::get('san-pham',[CategoryController::class, 'getListProduct'])->name('get.product.list');

Route::get('bai-viet', [ArticleController::class, 'getArticle'])->name('get.list.article');
Route::get('bai-viet/{slug}-{id}', [ArticleController::class, 'getDetailArticle'])->name('get.detail.article');

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => []], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::group(['namespace'=>'Auth'],function(){
Route::get('/login', [AuthUserController::class, 'login'])->name('login');
Route::post('/login', [AuthUserController::class, 'postLogin'])->name('postLogin');
Route::get('/register', [AuthUserController::class, 'register'])->name('register');
Route::post('/register', [AuthUserController::class, 'postRegister'])->name('postRegister');
Route::get('/logout', [AuthUserController::class, 'logout'])->name('logout');
});

Route::group(['prefix'=>'cart'], function(){
    Route::get('/',[CartController::class, 'index'])->name('cart.index');
    Route::get('/add/{product}',[CartController::class, 'add'])->name('cart.add');
    Route::get('/delete/{product}',[CartController::class, 'delete'])->name('cart.delete');
    Route::get('/update/{product}',[CartController::class, 'update'])->name('cart.update');
    Route::get('/clear',[CartController::class, 'clear'])->name('cart.clear');
});
Route::group(['prefix'=>'oder','middleware'=>'CheckLoginUser'],function(){
    Route::get('/pay',[CartController::class, 'getpay'])->name('form.pay');
    Route::post('/pay',[CartController::class, 'saveCart']);
});

Route::group(['prefix'=>'payment','middleware'=>'CheckLoginUser'],function(){
    Route::get('/{method}/{transaction}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/{method}/{transaction}/init', [PaymentController::class, 'init'])->name('payment.init');
    Route::get('/momo/return/{transaction}', [PaymentController::class, 'momoReturn'])->name('payment.momo.return');
    Route::post('/momo/ipn/{transaction}', [PaymentController::class, 'momoIpn'])->name('payment.momo.ipn');
    Route::get('/paypal/return/{transaction}', [PaymentController::class, 'paypalReturn'])->name('payment.paypal.return');
    Route::get('/paypal/cancel/{transaction}', [PaymentController::class, 'paypalCancel'])->name('payment.paypal.cancel');
    Route::post('/qrcode/{transaction}/confirm', [PaymentController::class, 'qrcodeConfirm'])->name('payment.qrcode.confirm');
    Route::get('/vnpay/return/{transaction}', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
    Route::get('/vnpay/ipn/{transaction}', [PaymentController::class, 'vnpayIpn'])->name('payment.vnpay.ipn');
});

Route::group(['prefix'=>'rating','middleware'=>'CheckLoginUser'],function(){
    Route::post('/danh-gia/{id}',[RatingController::class, 'postRating'])->name('postRating');
});

Route::get('lien-he', [ContactController::class,'index'])->name('contact.index');
Route::post('lien-he', [ContactController::class,'postContact']);

Route::group(['prefix'=>'user','middleware'=>'CheckLoginUser'],function(){
    Route::get('user',[UserController::class, 'index'])->name('user.index');
});