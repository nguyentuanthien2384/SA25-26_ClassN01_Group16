<?php

namespace App\Http\Controllers;

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
        $hotPerPage = $capPerPage($request->input('hot_per_page', 8), 8);
        $newPerPage = $capPerPage($request->input('new_per_page', 8), 8);
        $sellingPerPage = $capPerPage($request->input('selling_per_page', 8), 8);
        $newsPerPage = $capPerPage($request->input('news_per_page', 8), 8);

        // Khung thời gian 30 ngày gần nhất
        $startOfPeriod = Carbon::now()->subDays(30)->startOfDay();
        $endOfPeriod   = Carbon::now()->endOfDay();

        // Sản phẩm nổi bật (đã có sẵn)
        $productHotQuery = Product::where([
            'pro_hot'    => Product::HOT_ON,
            'pro_active' => Product::STATUS_PUBLIC,
        ]);
        $productHot = $productHotQuery->paginate($hotPerPage, ['*'], 'hot_page');

        // Tin tức nổi bật: dùng danh sách sản phẩm của bạn
        $articleNewsQuery = Product::where('pro_active', Product::STATUS_PUBLIC)
            ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
            ->orderBy('id', 'DESC');
        if (!$articleNewsQuery->exists()) {
            $articleNewsQuery = Product::where('pro_active', Product::STATUS_PUBLIC)
                ->orderBy('id', 'DESC');
        }
        $articleNews = $articleNewsQuery->paginate($newsPerPage, ['*'], 'news_page');

        // Sản phẩm mới (mặc định lấy theo id giảm dần)
        $productNew = Product::where('pro_active', Product::STATUS_PUBLIC)
            ->orderBy('id', 'DESC')
            ->paginate($newPerPage, ['*'], 'new_page');

        // Sản phẩm bán nhiều nhất trong 30 ngày (dựa theo số lần mua)
        $productSellingQuery = Product::where('pro_active', Product::STATUS_PUBLIC)
            ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
            ->where('pro_pay', '>', 0) // chỉ lấy những sản phẩm đã có đơn hàng
            ->orderBy('pro_pay', 'DESC');
        if (!$productSellingQuery->exists()) {
            $productSellingQuery = Product::where('pro_active', Product::STATUS_PUBLIC)
                ->where('pro_pay', '>', 0)
                ->orderBy('pro_pay', 'DESC');
        }
        $productSelling = $productSellingQuery->paginate($sellingPerPage, ['*'], 'selling_page');

        $viewData = [
            'productHot'     => $productHot,
            'articleNews'    => $articleNews,
            'productNew'     => $productNew,
            'productSelling' => $productSelling,
        ];

        return view('home.index', $viewData);
    }
}

