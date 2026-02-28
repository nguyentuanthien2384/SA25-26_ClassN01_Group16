<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Models\Product;
use App\Models\Models\Category;
use App\Services\CQRS\ProductCommandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Catalog Service — Product & Category API
 *
 * Microservice: catalog-service (port 9005)
 * Database:     catalog_db (mysql-catalog:3310)
 * Patterns:     CQRS, Caching, Pagination, Circuit Breaker fallback
 */
class CatalogServiceController extends Controller
{
    private ProductCommandService $commandService;

    public function __construct(ProductCommandService $commandService)
    {
        $this->commandService = $commandService;
    }

    // ── READ (Query side — cached) ──────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $perPage  = min(max((int) $request->input('per_page', 20), 1), 60);
        $category = $request->input('category');
        $search   = $request->input('search');
        $sort     = $request->input('sort', 'newest');
        $page     = $request->input('page', 1);

        $cacheKey = "catalog:products:{$perPage}:{$page}:{$category}:{$search}:{$sort}";

        $products = Cache::remember($cacheKey, 300, function () use ($perPage, $category, $search, $sort) {
            $query = Product::select([
                'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                'pro_image', 'pro_description', 'pro_category_id',
            ])
                ->where('pro_active', Product::STATUS_PUBLIC)
                ->with(['category:id,c_name,c_slug']);

            if ($category) {
                $query->where('pro_category_id', $category);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pro_name', 'like', "%{$search}%")
                      ->orWhere('pro_description', 'like', "%{$search}%");
                });
            }

            match ($sort) {
                'price_asc'  => $query->orderBy('pro_price', 'ASC'),
                'price_desc' => $query->orderBy('pro_price', 'DESC'),
                'name_asc'   => $query->orderBy('pro_name', 'ASC'),
                'popular'    => $query->orderBy('pro_pay', 'DESC'),
                default      => $query->orderBy('id', 'DESC'),
            };

            return $query->paginate($perPage);
        });

        return response()->json($products)
            ->header('X-Cache-Status', Cache::has($cacheKey) ? 'HIT' : 'MISS')
            ->header('X-Service', 'catalog-service');
    }

    public function show(int $id): JsonResponse
    {
        $product = Cache::remember("catalog:product:{$id}", 300, function () use ($id) {
            return Product::with(['category:id,c_name,c_slug'])->findOrFail($id);
        });

        return response()->json(['success' => true, 'data' => $product])
            ->header('X-Service', 'catalog-service');
    }

    // ── WRITE (Command side — CQRS) ─────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pro_name'        => 'required|string|max:255',
            'pro_price'       => 'required|numeric|gt:0',
            'pro_category_id' => 'required|integer|exists:category,id',
            'pro_description' => 'nullable|string',
            'pro_sale'        => 'nullable|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 400);
        }

        try {
            $product = $this->commandService->create($request->all());

            Cache::tags(['catalog'])->flush();

            return response()->json([
                'success' => true,
                'data'    => $product,
                'message' => 'Product created via CQRS Command',
            ], 201)->header('X-Service', 'catalog-service');
        } catch (\Exception $e) {
            Log::error('[CATALOG] Create failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->commandService->update($id, $request->all());

            Cache::forget("catalog:product:{$id}");

            return response()->json([
                'success' => true,
                'data'    => $product,
                'message' => 'Product updated via CQRS Command',
            ])->header('X-Service', 'catalog-service');
        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return response()->json(['success' => false, 'error' => $e->getMessage()], $code);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->commandService->delete($id);

            Cache::forget("catalog:product:{$id}");

            return response()->json([
                'success' => true,
                'message' => 'Product deleted via CQRS Command',
            ])->header('X-Service', 'catalog-service');
        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return response()->json(['success' => false, 'error' => $e->getMessage()], $code);
        }
    }

    // ── Categories ──────────────────────────────────────────────

    public function categories(): JsonResponse
    {
        $categories = Cache::remember('catalog:categories', 600, function () {
            return Category::where('c_active', 1)
                ->select(['id', 'c_name', 'c_slug'])
                ->withCount('Products')
                ->get();
        });

        return response()->json(['success' => true, 'data' => $categories])
            ->header('X-Service', 'catalog-service');
    }
}
