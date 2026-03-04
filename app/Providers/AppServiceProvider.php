<?php

namespace App\Providers;

use App\Models\Models\Cart;
use App\Models\Models\Wishlist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        view()->composer('layouts.app', function ($view) {
            $userId = get_data_user('web');

            if ($userId) {
                $carts = Cache::remember("cart:user:{$userId}", 60, function () use ($userId) {
                    return Cart::select(['id', 'pro_id', 'quantity', 'price'])
                        ->where('user_id', $userId)
                        ->orderBy('quantity', 'DESC')
                        ->get();
                });

                $wishlistIds = Cache::remember("wishlist:ids:{$userId}", 120, function () use ($userId) {
                    return Wishlist::where('user_id', $userId)
                        ->pluck('product_id')
                        ->toArray();
                });
                $wishlistCount = count($wishlistIds);
            } else {
                $carts = collect();
                $wishlistIds = [];
                $wishlistCount = 0;
            }

            $view->with(compact('carts', 'wishlistIds', 'wishlistCount'));
        });
    }
}
