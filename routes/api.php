<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Models\Models\Product;
use App\Http\Controllers\Api\Lab03ProductController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================================================
// HEALTH CHECK ENDPOINT (for Kong & Monitoring)
// ============================================================================
Route::get('/health', function () {
    try {
        // Check database connection
        \DB::connection()->getPdo();
        $dbStatus = 'ok';
    } catch (\Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }

    try {
        // Check Redis connection
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
        'service' => 'laravel-app',
        'version' => config('app.version', '1.0.0'),
        'checks' => [
            'database' => $dbStatus,
            'cache' => $redisStatus,
        ],
        'uptime' => round((microtime(true) - LARAVEL_START), 2) . 's',
    ], $httpCode);
});

// Simple health check (just return OK)
Route::get('/ping', function () {
    return response()->json(['message' => 'pong', 'timestamp' => now()->toIso8601String()]);
});

// ============================================================================
// PRODUCT API ENDPOINTS (with Cache & Pagination)
// ============================================================================

// Hot products
Route::get('/products/hot', function (Request $request) {
    $page = $request->input('page', 1);
    $perPage = (int) $request->input('per_page', 20);
    if ($perPage <= 0) $perPage = 20;
    if ($perPage > 60) $perPage = 60;

    $cacheKey = "api:products:hot:{$perPage}:{$page}";
    $products = Cache::remember($cacheKey, 300, function () use ($perPage, $page) {
        return Product::select([
            'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
            'pro_image', 'pro_description', 'pro_category_id'
        ])
        ->where([
            'pro_hot' => Product::HOT_ON,
            'pro_active' => Product::STATUS_PUBLIC,
        ])
        ->with(['category:id,c_name,c_slug'])
        ->paginate($perPage, ['*'], 'page', $page);
    });

    return response()->json($products)
        ->header('Cache-Control', 'public, max-age=300')
        ->header('X-Cache-Status', Cache::has($cacheKey) ? 'HIT' : 'MISS');
});

// New products
Route::get('/products/new', function (Request $request) {
    $page = $request->input('page', 1);
    $perPage = (int) $request->input('per_page', 20);
    if ($perPage <= 0) $perPage = 20;
    if ($perPage > 60) $perPage = 60;

    $cacheKey = "api:products:new:{$perPage}:{$page}";
    $products = Cache::remember($cacheKey, 300, function () use ($perPage, $page) {
        return Product::select([
            'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
            'pro_image', 'pro_description', 'pro_category_id', 'created_at'
        ])
        ->where('pro_active', Product::STATUS_PUBLIC)
        ->orderBy('id', 'DESC')
        ->with(['category:id,c_name,c_slug'])
        ->paginate($perPage, ['*'], 'page', $page);
    });

    return response()->json($products)
        ->header('Cache-Control', 'public, max-age=300')
        ->header('X-Cache-Status', Cache::has($cacheKey) ? 'HIT' : 'MISS');
});

// Best selling products
Route::get('/products/selling', function (Request $request) {
    $page = $request->input('page', 1);
    $perPage = (int) $request->input('per_page', 20);
    if ($perPage <= 0) $perPage = 20;
    if ($perPage > 60) $perPage = 60;

    $cacheKey = "api:products:selling:{$perPage}:{$page}";
    $products = Cache::remember($cacheKey, 300, function () use ($perPage, $page) {
        return Product::select([
            'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
            'pro_image', 'pro_description', 'pro_category_id', 'pro_pay'
        ])
        ->where('pro_active', Product::STATUS_PUBLIC)
        ->where('pro_pay', '>', 0)
        ->orderBy('pro_pay', 'DESC')
        ->with(['category:id,c_name,c_slug'])
        ->paginate($perPage, ['*'], 'page', $page);
    });

    return response()->json($products)
        ->header('Cache-Control', 'public, max-age=300')
        ->header('X-Cache-Status', Cache::has($cacheKey) ? 'HIT' : 'MISS');
});

// All products (with filters)
Route::get('/products', function (Request $request) {
    $page = $request->input('page', 1);
    $perPage = (int) $request->input('per_page', 20);
    if ($perPage <= 0) $perPage = 20;
    if ($perPage > 60) $perPage = 60;

    $category = $request->input('category');
    $search = $request->input('search');
    $sort = $request->input('sort', 'newest');

    $cacheKey = "api:products:all:{$perPage}:{$page}:{$category}:{$search}:{$sort}";
    $products = Cache::remember($cacheKey, 300, function () use ($perPage, $page, $category, $search, $sort) {
        $query = Product::select([
            'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
            'pro_image', 'pro_description', 'pro_category_id'
        ])
        ->where('pro_active', Product::STATUS_PUBLIC)
        ->with(['category:id,c_name,c_slug']);

        // Filter by category
        if ($category) {
            $query->where('pro_category_id', $category);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('pro_name', 'like', '%' . $search . '%')
                  ->orWhere('pro_description', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('pro_price', 'ASC');
                break;
            case 'price_desc':
                $query->orderBy('pro_price', 'DESC');
                break;
            case 'name_asc':
                $query->orderBy('pro_name', 'ASC');
                break;
            case 'name_desc':
                $query->orderBy('pro_name', 'DESC');
                break;
            case 'popular':
                // Sort by best selling instead of views
                $query->orderBy('pro_pay', 'DESC');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'DESC');
                break;
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    });

    return response()->json($products)
        ->header('Cache-Control', 'public, max-age=300')
        ->header('X-Cache-Status', Cache::has($cacheKey) ? 'HIT' : 'MISS');
});

// Get single product by ID
Route::get('/products/{id}', function ($id) {
    $cacheKey = "api:product:{$id}";
    $product = Cache::remember($cacheKey, 300, function () use ($id) {
        return Product::with(['category:id,c_name,c_slug'])
            ->findOrFail($id);
    });

    return response()->json($product)
        ->header('Cache-Control', 'public, max-age=300')
        ->header('X-Cache-Status', Cache::has($cacheKey) ? 'HIT' : 'MISS');
});

// ============================================================================
// METRICS ENDPOINT (for Prometheus)
// ============================================================================
Route::get('/metrics', function () {
    try {
        $metrics = [
            'laravel_app_up' => 1,
            'laravel_products_total' => Product::where('pro_active', 1)->count(),
            'laravel_cache_hits' => Cache::has('api:products:hot:20:1') ? 1 : 0,
        ];

        $output = '';
        foreach ($metrics as $key => $value) {
            $output .= "{$key} {$value}\n";
        }

        return response($output, 200)
            ->header('Content-Type', 'text/plain');
    } catch (\Exception $e) {
        return response('# Error collecting metrics', 500)
            ->header('Content-Type', 'text/plain');
    }
});
Route::prefix('lab03')->group(function () {
    Route::get('/products', [Lab03ProductController::class, 'index']);
    Route::get('/products/{id}', [Lab03ProductController::class, 'show']);
    Route::post('/products', [Lab03ProductController::class, 'store']);
    Route::put('/products/{id}', [Lab03ProductController::class, 'update']);
    Route::delete('/products/{id}', [Lab03ProductController::class, 'destroy']);
});