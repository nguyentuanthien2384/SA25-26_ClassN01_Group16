<?php

namespace App\Providers;

use App\Models\Models\Cart;
use App\Models\Models\Wishlist;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function($view){
            $cart = new Cart();
            $carts = Cart::orderBy('quantity','DESC')->get();
            $wishlistIds = [];
            $wishlistCount = 0;
            if (get_data_user('web')) {
                $wishlistIds = Wishlist::where('user_id', get_data_user('web'))
                    ->pluck('product_id')
                    ->toArray();
                $wishlistCount = count($wishlistIds);
            }
            $view->with(compact('cart','carts','wishlistIds','wishlistCount'));
        });
    }
}
