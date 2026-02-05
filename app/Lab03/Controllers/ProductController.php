<?php

namespace App\Lab03\Controllers;

use App\Http\Controllers\Controller;
use App\Lab03\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Class ProductController
 * 
 * Lab 03 - Layered Architecture
 * Presentation Layer: API Controller
 * 
 * This controller handles HTTP requests and responses for product operations.
 * It delegates all business logic to the Service layer.
 * 
 * RESTful API Endpoints:
 * - GET    /api/lab03/products       - List all products
 * - GET    /api/lab03/products/{id}  - Get single product
 * - POST   /api/lab03/products       - Create new product
 * - PUT    /api/lab03/products/{id}  - Update product
 * - DELETE /api/lab03/products/{id}  - Delete product
 * - GET    /api/lab03/products/search - Search products
 */
class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * ProductController constructor.
     * 
     * Dependency Injection: Service is injected via constructor
     * 
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products
     * 
     * GET /api/lab03/products
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            
            $products = $this->productService->getAllProducts($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified product
     * 
     * GET /api/lab03/products/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return $this->errorResponse($e->getMessage(), $code);
        }
    }

    /**
     * Store a newly created product
     * 
     * POST /api/lab03/products
     * 
     * Required fields:
     * - pro_name (string)
     * - pro_price (numeric, >= 0)
     * - pro_category_id (integer, exists in categories table)
     * 
     * Optional fields:
     * - pro_content (string)
     * - pro_image (string)
     * - quantity (integer, >= 0)
     * - pro_sale (integer, 0-100)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            $product = $this->productService->createProduct($data);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified product
     * 
     * PUT /api/lab03/products/{id}
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->all();
            
            $product = $this->productService->updateProduct($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);

        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return $this->errorResponse($e->getMessage(), $code);
        }
    }

    /**
     * Remove the specified product
     * 
     * DELETE /api/lab03/products/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return $this->errorResponse($e->getMessage(), $code);
        }
    }

    /**
     * Search products by keyword
     * 
     * GET /api/lab03/products/search?q=keyword
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $keyword = $request->get('q', '');

            if (empty($keyword)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search keyword is required'
                ], 400);
            }

            $results = $this->productService->searchProducts($keyword);

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $results
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Return error response
     * 
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => [
                'code' => $code,
                'description' => $this->getErrorDescription($code)
            ]
        ], $code);
    }

    /**
     * Get error description by code
     * 
     * @param int $code
     * @return string
     */
    protected function getErrorDescription(int $code): string
    {
        $descriptions = [
            400 => 'Bad Request - Invalid input data',
            404 => 'Not Found - Resource does not exist',
            500 => 'Internal Server Error - Something went wrong'
        ];

        return $descriptions[$code] ?? 'Unknown error';
    }
}
