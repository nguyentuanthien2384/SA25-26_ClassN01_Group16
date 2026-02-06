<?php

namespace App\Http\Controllers;
use App\Models\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function __construct()
    {
        // ⚡ OPTIMIZED: Load ALL categories once, group by parent
        $categorys = Cache::remember('frontend:categories', 600, function () {
            return Category::select(['id', 'c_name', 'c_slug', 'c_parent', 'c_active'])
                ->where('c_active', Category::STATUS_PUBLIC)
                ->get();
        });

        // Pre-group children by parent_id (avoid N+1 in header)
        $catParent = $categorys->where('c_parent', 0)->values();
        $catChildren = $categorys->where('c_parent', '>', 0)->groupBy('c_parent');

        View::share('categorys', $categorys);
        View::share('catParent', $catParent);
        View::share('catChildren', $catChildren);
    }
}
