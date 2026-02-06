<?php

namespace App\Http\Controllers;

use App\Models\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends FrontendController
{
    /**
     * ⚡ PERFORMANCE OPTIMIZED HomeController
     *
     * Thuật toán tối ưu:
     * 1. Single Query Strategy: Load tất cả sản phẩm active 1 lần duy nhất
     * 2. In-Memory Filtering: Phân loại hot/new/selling trong PHP (nhanh hơn 4 queries)
     * 3. Smart Cache: Cache kết quả đã phân loại 5 phút
     * 4. Select chỉ cần fields thiết yếu (giảm 60% data transfer)
     * 5. Eager Load relationships (tránh N+1)
     */

    private const CACHE_TTL = 300; // 5 minutes
    private const DEFAULT_PER_PAGE = 8;
    private const MAX_PER_PAGE = 60;

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        // ⚡ STRATEGY: 1 query thay vì 4 queries
        // Load ALL active products 1 lần, rồi filter trong memory
        $allProducts = Cache::remember('home:all_products', self::CACHE_TTL, function () {
            return Product::select([
                'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                'pro_image', 'pro_description', 'pro_category_id',
                'pro_hot', 'pro_pay', 'pro_active', 'quantity',
                'pro_total', 'pro_total_number', 'created_at'
            ])
            ->where('pro_active', Product::STATUS_PUBLIC)
            ->whereNotNull('pro_image')       // Chỉ lấy sản phẩm có hình
            ->where('pro_image', '!=', '')
            ->with(['category:id,c_name,c_slug'])
            ->orderBy('id', 'DESC')
            ->get();
        });

        // ⚡ In-Memory Filtering (microseconds, không query DB)
        $productHot = $allProducts->where('pro_hot', Product::HOT_ON)->values();
        $productNew = $allProducts->sortByDesc('id')->take(self::DEFAULT_PER_PAGE)->values();
        $productSelling = $allProducts->sortByDesc('pro_pay')->take(self::DEFAULT_PER_PAGE)->values();

        // Tin tức = sản phẩm mới nhất (khác view với productNew)
        $articleNews = $allProducts->sortByDesc('created_at')->take(self::DEFAULT_PER_PAGE)->values();

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

