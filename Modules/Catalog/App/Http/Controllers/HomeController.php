<?php

namespace Modules\Catalog\App\Http\Controllers;

use App\Http\Controllers\FrontendController;
use App\Models\Models\Article;
use App\Models\Models\Banner;
use App\Models\Models\Category;
use App\Models\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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

        // Khung thời gian 30 ngày gần nhất
        $startOfPeriod = Carbon::now()->subDays(30)->startOfDay();
        $endOfPeriod   = Carbon::now()->endOfDay();

        // Sản phẩm nổi bật (đã có sẵn)
        $productHotQuery = Product::where([
            'pro_hot'    => Product::HOT_ON,
            'pro_active' => Product::STATUS_PUBLIC,
        ]);
        $productHot = $hotPaginate
            ? $productHotQuery->paginate($hotPerPage, ['*'], 'hot_page')
            : $productHotQuery->get();

        // Tin tức nổi bật: dùng danh sách sản phẩm của bạn
        $articleNewsQuery = Product::where('pro_active', Product::STATUS_PUBLIC)
            ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
            ->orderBy('id', 'DESC');
        if (!$articleNewsQuery->exists()) {
            $articleNewsQuery = Product::where('pro_active', Product::STATUS_PUBLIC)
                ->orderBy('id', 'DESC');
        }
        $articleNews = $newsPaginate
            ? $articleNewsQuery->paginate($newsPerPage, ['*'], 'news_page')
            : $articleNewsQuery->get();

        // Sản phẩm mới (mặc định lấy theo id giảm dần)
        $productNewQuery = Product::where('pro_active', Product::STATUS_PUBLIC)
            ->orderBy('id', 'DESC');
        $productNew = $newPaginate
            ? $productNewQuery->paginate($newPerPage, ['*'], 'new_page')
            : $productNewQuery->get();

        // Sản phẩm bán chạy (dựa theo pro_hot hoặc random)
        $productSellingQuery = Product::where('pro_active', Product::STATUS_PUBLIC)
            ->orderBy('id', 'DESC');
        $productSelling = $sellingPaginate
            ? $productSellingQuery->paginate($sellingPerPage, ['*'], 'selling_page')
            : $productSellingQuery->get();

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
