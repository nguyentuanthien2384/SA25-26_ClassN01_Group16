<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Models\Models\Product;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Health Check
Route::get('/health', function () {
    try {
        \DB::connection()->getPdo();
        $dbStatus = 'ok';
    } catch (\Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }

    try {
        Cache::get('health_check');
        $redisStatus = 'ok';
    } catch (\Exception $e) {
        $redisStatus = 'error: ' . $e->getMessage();
    }

    $status = ($dbStatus === 'ok' && $redisStatus === 'ok') ? 'healthy' : 'unhealthy';
    $httpCode = ($status === 'healthy') ? 200 : 503;

    return response()->json([
        'status' => $status,
        'timestamp' => now()->toIso8601String(),
        'service' => env('SERVICE_NAME', 'laravel-app'),
        'version' => config('app.version', '1.0.0'),
        'checks' => [
            'database' => $dbStatus,
            'cache' => $redisStatus,
        ],
        'uptime' => round((microtime(true) - LARAVEL_START), 2) . 's',
    ], $httpCode);
});

Route::get('/ping', function () {
    return response()->json(['message' => 'pong', 'timestamp' => now()->toIso8601String()]);
});

// Products
Route::get('/products/hot', function (Request $request) {
    $perPage = min(max((int) $request->input('per_page', 20), 1), 60);
    $products = Product::select([
        'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
        'pro_image', 'pro_description', 'pro_category_id'
    ])
    ->where(['pro_hot' => Product::HOT_ON, 'pro_active' => Product::STATUS_PUBLIC])
    ->with(['category:id,c_name,c_slug'])
    ->paginate($perPage);

    return response()->json($products);
});

Route::get('/products', function (Request $request) {
    $perPage = min(max((int) $request->input('per_page', 20), 1), 60);
    $category = $request->input('category');
    $search = $request->input('search');
    $sort = $request->input('sort', 'newest');

    $query = Product::select([
        'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
        'pro_image', 'pro_description', 'pro_category_id'
    ])
    ->where('pro_active', Product::STATUS_PUBLIC)
    ->with(['category:id,c_name,c_slug']);

    if ($category) {
        $query->where('pro_category_id', $category);
    }
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('pro_name', 'like', '%' . $search . '%')
              ->orWhere('pro_description', 'like', '%' . $search . '%');
        });
    }

    switch ($sort) {
        case 'price_asc': $query->orderBy('pro_price', 'ASC'); break;
        case 'price_desc': $query->orderBy('pro_price', 'DESC'); break;
        case 'name_asc': $query->orderBy('pro_name', 'ASC'); break;
        default: $query->orderBy('id', 'DESC'); break;
    }

    return response()->json($query->paginate($perPage));
});

Route::get('/products/{id}', function ($id) {
    $product = Product::with(['category:id,c_name,c_slug'])->findOrFail($id);
    return response()->json($product);
});

// Metrics
Route::get('/metrics', function () {
    try {
        $output = "laravel_app_up 1\n";
        $output .= "laravel_products_total " . Product::where('pro_active', 1)->count() . "\n";
        return response($output, 200)->header('Content-Type', 'text/plain');
    } catch (\Exception $e) {
        return response('# Error collecting metrics', 500)->header('Content-Type', 'text/plain');
    }
});
