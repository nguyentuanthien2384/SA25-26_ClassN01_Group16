<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $page     = $request->input('page', 1);
        $perPage  = min(max((int) $request->input('per_page', 20), 1), 60);
        $category = $request->input('category');
        $search   = $request->input('search');
        $sort     = $request->input('sort', 'newest');

        $cacheKey = "api:products:all:{$perPage}:{$page}:{$category}:{$search}:{$sort}";

        $products = Cache::remember($cacheKey, 300, function () use (
            $perPage, $page, $category, $search, $sort
        ) {
            $query = Product::select([
                'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                'pro_image', 'pro_description', 'pro_category_id'
            ])->where('pro_active', Product::STATUS_PUBLIC)
              ->with(['category:id,c_name,c_slug']);

            if ($category) $query->where('pro_category_id', $category);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('pro_name', 'like', '%'.$search.'%')
                      ->orWhere('pro_description', 'like', '%'.$search.'%');
                });
            }

            switch ($sort) {
                case 'price_asc':  $query->orderBy('pro_price', 'ASC');  break;
                case 'price_desc': $query->orderBy('pro_price', 'DESC'); break;
                case 'name_asc':   $query->orderBy('pro_name', 'ASC');   break;
                case 'name_desc':  $query->orderBy('pro_name', 'DESC');  break;
                case 'popular':    $query->orderBy('pro_pay', 'DESC');   break;
                default:           $query->orderBy('id', 'DESC');        break;
            }

            return $query->paginate($perPage, ['*'], 'page', $page);
        });

        return response()->json($products)
            ->header('Cache-Control', 'public, max-age=300')
            ->header('X-Cache-Status', Cache::has($cacheKey) ? 'HIT' : 'MISS');
    }

    public function show(int $id)
    {
        $product = Cache::remember("api:product:{$id}", 300, function () use ($id) {
            return Product::with(['category:id,c_name,c_slug'])
                ->where('pro_active', Product::STATUS_PUBLIC)->findOrFail($id);
        });

        return response()->json($product);
    }
}
