<?php

namespace App\Http\Controllers;

use App\Models\Models\Article;
use App\Models\Models\Product;
use App\Models\Models\ProImage;
use App\Models\Models\Rating;
use App\Models\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductDetailController extends Controller
{
    public function productDetail(Request $request, $id){
        $url = $request->segment(2);
        $url = preg_split('/(-)/i', $url);
        $id = (int)$id;
        if($id = array_pop($url)){
            // ⚡ OPTIMIZED: Cache product details với eager loading
            $productDetails = Cache::remember("product:detail:{$id}", 300, function () use ($id) {
                return Product::with(['category:id,c_name,c_slug'])->find($id);
            });

            // ⚡ OPTIMIZED: Cache product images
            $productimg = Cache::remember("product:images:{$id}", 300, function () use ($id) {
                return ProImage::where('product_id', $id)
                    ->select('id', 'product_id', 'pi_name', 'pi_slug')
                    ->get();
            });

            // ⚡ OPTIMIZED: Cache ratings với eager loading
            $ratings = Cache::remember("product:ratings:{$id}", 180, function () use ($id) {
                return Rating::where('ra_product_id', $id)
                    ->with(['user:id,name,avatar'])
                    ->select('id', 'ra_product_id', 'ra_user_id', 'ra_number', 'ra_content', 'created_at')
                    ->orderBy('id', 'DESC')
                    ->get();
            });

            // User details (nếu cần)
            $userDetails = $productDetails ? User::find($productDetails->id) : null;

            // ⚡ OPTIMIZED: Cache article news với pagination
            $newsPage = $request->input('news_page', 1);
            $newsCacheKey = "product:news:{$newsPage}";
            $articleNews = Cache::remember($newsCacheKey, 300, function () use ($newsPage) {
                return Product::select([
                    'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                    'pro_image', 'pro_description', 'pro_category_id', 'created_at'
                ])
                ->where('pro_active', Product::STATUS_PUBLIC)
                ->orderBy('id', 'DESC')
                ->with(['category:id,c_name,c_slug'])
                ->paginate(10, ['*'], 'news_page', $newsPage);
            });
    
            $viewData = [
                'productDetails' => $productDetails,
                'userDetails' => $userDetails,
                'ratings' => $ratings,
                'productimg'=>$productimg,
                'articleNews' =>$articleNews
            ];
            return view('product.detail',$viewData);
        }
    }
}
