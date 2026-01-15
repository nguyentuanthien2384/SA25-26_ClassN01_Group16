<?php

namespace App\Http\Controllers;

use App\Models\Models\Article;
use App\Models\Models\Banner;
use App\Models\Models\Category;
use App\Models\Models\Product;
use Illuminate\Http\Request;
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
    public function index()
    {
        // Khung thời gian 30 ngày gần nhất
        $startOfPeriod = Carbon::now()->subDays(30)->startOfDay();
        $endOfPeriod   = Carbon::now()->endOfDay();

        // Sản phẩm nổi bật (đã có sẵn)
        $productHot = Product::where([
            'pro_hot'    => Product::HOT_ON,
            'pro_active' => Product::STATUS_PUBLIC,
        ])->limit(10)->get();

        // Tin tức nổi bật: ưu tiên bài viết có lượt xem cao trong 30 ngày
        $articleNews = Article::where([
            'a_active' => Product::STATUS_PUBLIC,
        ])->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
          ->orderBy('a_view', 'DESC')
          ->limit(10)
          ->get();

        // Sản phẩm mới (mặc định lấy theo id giảm dần)
        $productNew = Product::where('pro_active', Product::STATUS_PUBLIC)
            ->orderBy('id', 'DESC')
            ->limit(20)
            ->get();

        // Sản phẩm bán nhiều nhất trong 30 ngày (dựa theo số lần mua)
        $productSelling = Product::where('pro_active', Product::STATUS_PUBLIC)
            ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
            ->where('pro_pay', '>', 0) // chỉ lấy những sản phẩm đã có đơn hàng
            ->orderBy('pro_pay', 'DESC')
            ->limit(10)
            ->get();

        $viewData = [
            'productHot'     => $productHot,
            'articleNews'    => $articleNews,
            'productNew'     => $productNew,
            'productSelling' => $productSelling,
        ];

        return view('home.index', $viewData);
    }
}

