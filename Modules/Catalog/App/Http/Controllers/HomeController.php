<?php

namespace Modules\Catalog\App\Http\Controllers;

use App\Http\Controllers\FrontendController;
use App\Models\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends FrontendController
{
    private const CACHE_TTL = 300;
    private const PER_PAGE  = 8;

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $allProducts = Cache::remember('home:all_products', self::CACHE_TTL, function () {
            return Product::select([
                    'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                    'pro_image', 'pro_category_id', 'pro_hot', 'pro_pay',
                    'pro_active', 'quantity', 'pro_total', 'pro_total_number',
                    'created_at',
                ])
                ->where('pro_active', Product::STATUS_PUBLIC)
                ->with(['category:id,c_name,c_slug'])
                ->orderBy('id', 'DESC')
                ->get();
        });

        $productHot     = $allProducts->where('pro_hot', Product::HOT_ON)->values();
        $productNew     = $allProducts->sortByDesc('id')->take(self::PER_PAGE)->values();
        $productSelling = $allProducts->where('pro_pay', '>', 0)
                                      ->sortByDesc('pro_pay')
                                      ->take(self::PER_PAGE)
                                      ->values();
        $articleNews    = $allProducts->sortByDesc('created_at')->take(self::PER_PAGE)->values();

        return view('home.index', [
            'productHot'      => $productHot,
            'articleNews'     => $articleNews,
            'productNew'      => $productNew,
            'productSelling'  => $productSelling,
            'hotPaginate'     => false,
            'newPaginate'     => false,
            'sellingPaginate' => false,
            'newsPaginate'    => false,
        ]);
    }
}
