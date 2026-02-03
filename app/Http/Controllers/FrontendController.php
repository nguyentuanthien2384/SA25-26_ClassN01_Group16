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
        // ⚡ Cache categories for 10 minutes (600 seconds)
        $categorys = Cache::remember('frontend:categories', 600, function () {
            return Category::all();
        });
        View::share('categorys', $categorys);
    }
}
