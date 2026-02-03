<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\App\Http\Controllers\AdminController;

route::prefix('authenticate')->group(function () {
    Route::get('/login', 'AdminAuthController@getLogin')->name('admin.login');
    Route::post('/login', 'AdminAuthController@postLogin');

    Route::get('/logout', 'AdminAuthController@logoutAdmin')->name('admin.logout');
});
route::prefix('admin')->middleware('CheckLoginAdmin')->group(function () {
    Route::get('/', 'AdminController@index')->name('admin.home');
    Route::get('/dashboard/data', 'AdminController@dashboardData')->name('admin.dashboard.data');

    route::group(['prefix' => 'category'], function () {
        route::get('/', 'AdminCategoryController@index')->name('admin.get.list.category');
        route::get('/create', 'AdminCategoryController@create')->name('admin.get.create.category');
        route::post('/create', 'AdminCategoryController@store');
        route::get('/update{id}', 'AdminCategoryController@edit')->name('admin.get.edit.category');
        route::post('/update{id}', 'AdminCategoryController@update');
        route::get('/{action}/{id}', 'AdminCategoryController@action')->name('admin.get.action.category');
    });
    route::group(['prefix' => 'account'], function () {
        route::get('/', 'AdminUserController@indexAdmin')->name('account.index');
        route::get('/create', 'AdminUserController@create')->name('account.create');
        route::post('/create', 'AdminUserController@store');
        route::get('/update{id}', 'AdminUserController@edit')->name('account.update');
        route::post('/update{id}', 'AdminUserController@update');
        route::get('/delete{id}', 'AdminUserController@deletea')->name('account.delete');
    });
    route::group(['prefix' => 'product'], function () {
        route::get('/', 'AdminProductController@index')->name('admin.get.list.product');
        route::get('/create', 'AdminProductController@create')->name('admin.get.create.product');
        route::post('/create', 'AdminProductController@store');
        route::get('/update{id}', 'AdminProductController@edit')->name('admin.get.edit.product');
        route::post('/update{id}', 'AdminProductController@update');
        route::get('/{action}/{id}', 'AdminProductController@action')->name('admin.get.action.product');
    });
    route::group(['prefix' => 'article'], function () {
        route::get('/', 'AdminArticleController@index')->name('admin.get.list.article');
        route::get('/create', 'AdminArticleController@create')->name('admin.get.create.article');
        route::post('/create', 'AdminArticleController@store');
        route::get('/update{id}', 'AdminArticleController@edit')->name('admin.get.edit.article');
        route::post('/update{id}', 'AdminArticleController@update');
        route::get('/{action}/{id}', 'AdminArticleController@action')->name('admin.get.action.article');
    });
    route::group(['prefix' => 'banner'], function () {
        route::get('/', 'AdminBannerController@index')->name('admin.get.list.banner');
        route::get('/create', 'AdminBannerController@create')->name('admin.get.create.banner');
        route::post('/create', 'AdminBannerController@store');
        route::get('/update{id}', 'AdminBannerController@edit')->name('admin.get.edit.banner');
        route::post('/update{id}', 'AdminBannerController@update');
        route::get('/{action}/{id}', 'AdminBannerController@action')->name('admin.get.action.banner');
    });
    route::group(['prefix' => 'warehouse'], function () {
        route::get('/', 'AdminWarehouseController@getWarehouseProduct')->name('admin.get.warehouse');
    });
    route::group(['prefix' => 'user'], function () {
        route::get('/', 'AdminUserController@index')->name('admin.get.list.user');
        route::get('/delete{id}', 'AdminUserController@deleteUser')->name('user.delete');
    });
    route::group(['prefix' => 'transaction'], function () {
        route::get('/', 'AdminTransactionController@index')->name('admin.get.list.transaction');
        route::get('/view/{id}', 'AdminTransactionController@viewOder')->name('admin.get.view.order');
        route::get('/active/{id}', 'AdminTransactionController@actionTransaction')->name('admin.get.active.transaction');
        route::get('/update/{id}', 'AdminTransactionController@edit')->name('update.transaction');
        route::post('/update/{id}', 'AdminTransactionController@update')->name('update.transaction');
        route::get('/delete/{id}', 'AdminTransactionController@deleteTransacction')->name('admin.get.delete.transaction');
    });
    route::group(['prefix' => 'rating'], function () {
        route::get('/', 'AdminRatingController@index')->name('admin.get.list.rating');
        route::get('/delete/{id}', 'AdminRatingController@delete')->name('admin.delete.rating');
    });
    route::group(['prefix' => 'contact'], function () {
        route::get('/', 'AdminContactController@index')->name('admin.get.list.contact');
        route::get('/delete/{id}', 'AdminContactController@delete')->name('admin.get.delete.contact');
    });
    route::group(['prefix' => 'supplier'], function () {
        route::get('/', 'AdminSupplierController@index')->name('admin.get.list.supplier');
        route::get('/create', 'AdminSupplierController@create')->name('admin.get.create.supplier');
        route::post('/create', 'AdminSupplierController@store');
        route::get('/delete/{id}', 'AdminSupplierController@deleteSupplier')->name('admin.get.delete.supplier');
    });
    route::group(['prefix' => 'import'], function () {
        route::get('/', 'AdminImportGoodsController@index')->name('admin.get.list.import');
        route::get('/create', 'AdminImportGoodsController@create')->name('admin.get.create.import');
        route::post('/create', 'AdminImportGoodsController@store');
        route::get('/delete/{id}', 'AdminImportGoodsController@delete')->name('admin.get.delete.importdood');
    });
});
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => []], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

