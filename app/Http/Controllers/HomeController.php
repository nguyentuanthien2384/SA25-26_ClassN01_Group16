<?php

namespace App\Http\Controllers;

use App\Models\Models\Article;
use App\Models\Models\Banner;
use App\Models\Models\Category;
use App\Models\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
class HomeController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index(Request $request)
    {
        $capPerPage = function ($value, $default) {
            $perPage = (int) $value;
            if ($perPage <= 0) {
                $perPage = $default;
            }
            if ($perPage > 60) {
                $perPage = 60;
            }
            return $perPage;
        };
        $hotPaginate = $request->boolean('hot_paginate', true);
        $newPaginate = $request->boolean('new_paginate', true);
        $sellingPaginate = $request->boolean('selling_paginate', true);
        $newsPaginate = $request->boolean('news_paginate', true);

        $hotPerPage = $capPerPage($request->input('hot_per_page', 4), 4);
        $newPerPage = $capPerPage($request->input('new_per_page', 4), 4);
        $sellingPerPage = $capPerPage($request->input('selling_per_page', 4), 4);
        $newsPerPage = $capPerPage($request->input('news_per_page', 4), 4);

        $hotPage = $request->input('hot_page', 1);
        $newPage = $request->input('new_page', 1);
        $sellingPage = $request->input('selling_page', 1);
        $newsPage = $request->input('news_page', 1);

        // Khung thời gian 30 ngày gần nhất
        $startOfPeriod = Carbon::now()->subDays(30)->startOfDay();
        $endOfPeriod   = Carbon::now()->endOfDay();

        // ⚡ OPTIMIZED: Sản phẩm nổi bật với cache + eager loading
        $hotCacheKey = "home:products:hot:{$hotPaginate}:{$hotPerPage}:{$hotPage}";
        $productHot = Cache::remember($hotCacheKey, 300, function () use ($hotPaginate, $hotPerPage, $hotPage) {
            $query = Product::select([
                'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale', 
                'pro_image', 'pro_description', 'pro_category_id'
            ])
            ->where([
                'pro_hot'    => Product::HOT_ON,
                'pro_active' => Product::STATUS_PUBLIC,
            ])
            ->with(['category:id,c_name,c_slug']);

            return $hotPaginate
                ? $query->paginate($hotPerPage, ['*'], 'hot_page', $hotPage)
                : $query->get();
        });

        // ⚡ OPTIMIZED: Tin tức nổi bật với cache
        $newsCacheKey = "home:news:{$newsPaginate}:{$newsPerPage}:{$newsPage}";
        $articleNews = Cache::remember($newsCacheKey, 300, function () use ($startOfPeriod, $endOfPeriod, $newsPaginate, $newsPerPage, $newsPage) {
            $query = Product::select([
                'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                'pro_image', 'pro_description', 'pro_category_id', 'created_at'
            ])
            ->where('pro_active', Product::STATUS_PUBLIC)
            ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
            ->orderBy('id', 'DESC')
            ->with(['category:id,c_name,c_slug']);

            // Fallback nếu không có sản phẩm trong 30 ngày
            if (!$query->exists()) {
                $query = Product::select([
                    'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                    'pro_image', 'pro_description', 'pro_category_id', 'created_at'
                ])
                ->where('pro_active', Product::STATUS_PUBLIC)
                ->orderBy('id', 'DESC')
                ->with(['category:id,c_name,c_slug']);
            }

            return $newsPaginate
                ? $query->paginate($newsPerPage, ['*'], 'news_page', $newsPage)
                : $query->get();
        });

        // ⚡ OPTIMIZED: Sản phẩm mới với cache + eager loading
        $newCacheKey = "home:products:new:{$newPaginate}:{$newPerPage}:{$newPage}";
        $productNew = Cache::remember($newCacheKey, 300, function () use ($newPaginate, $newPerPage, $newPage) {
            $query = Product::select([
                'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                'pro_image', 'pro_description', 'pro_category_id', 'created_at'
            ])
            ->where('pro_active', Product::STATUS_PUBLIC)
            ->orderBy('id', 'DESC')
            ->with(['category:id,c_name,c_slug']);

            return $newPaginate
                ? $query->paginate($newPerPage, ['*'], 'new_page', $newPage)
                : $query->get();
        });

        // ⚡ OPTIMIZED: Sản phẩm bán chạy với cache
        $sellingCacheKey = "home:products:selling:{$sellingPaginate}:{$sellingPerPage}:{$sellingPage}";
        $productSelling = Cache::remember($sellingCacheKey, 300, function () use ($sellingPaginate, $sellingPerPage, $sellingPage) {
            $query = Product::select([
                'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                'pro_image', 'pro_description', 'pro_category_id'
            ])
            ->where('pro_active', Product::STATUS_PUBLIC)
            ->orderBy('id', 'DESC')
            ->with(['category:id,c_name,c_slug']);

            return $sellingPaginate
                ? $query->paginate($sellingPerPage, ['*'], 'selling_page', $sellingPage)
                : $query->get();
        });

        $viewData = [
            'productHot'     => $productHot,
            'articleNews'    => $articleNews,
            'productNew'     => $productNew,
            'productSelling' => $productSelling,
            'hotPaginate'    => $hotPaginate,
            'newPaginate'    => $newPaginate,
            'sellingPaginate'=> $sellingPaginate,
            'newsPaginate'   => $newsPaginate,
        ];

        return view('home.index', $viewData);
    }
}

