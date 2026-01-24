<?php

namespace App\Http\Controllers;

use App\Models\Models\Product;
use App\Models\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', get_data_user('web'))
            ->orderBy('id', 'DESC')
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        $wishlist = Wishlist::where('user_id', get_data_user('web'))
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'removed',
                    'product_id' => $product->id,
                    'count' => Wishlist::where('user_id', get_data_user('web'))->count(),
                ]);
            }
            return redirect()->back()->with('success', 'Đã xoá khỏi yêu thích');
        }

        Wishlist::query()->create([
            'user_id' => get_data_user('web'),
            'product_id' => $product->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'added',
                'product_id' => $product->id,
                'count' => Wishlist::where('user_id', get_data_user('web'))->count(),
            ]);
        }
        return redirect()->back()->with('success', 'Đã thêm vào yêu thích');
    }

    public function remove(Product $product)
    {
        Wishlist::where('user_id', get_data_user('web'))
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->back()->with('success', 'Đã xoá khỏi yêu thích');
    }
}
