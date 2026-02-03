<?php

namespace Modules\Catalog\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Models\Category;
use App\Models\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function getListProduct(Request $request){
        $url = $request->segment(2);
        $url = preg_split('/(-)/i', $url);
        
        if($id = array_pop($url)){
            // ⚡ OPTIMIZED: Cache category products
            $paginate = $request->boolean('paginate', true);
            $perPage = (int) $request->input('per_page', 4);
            if ($perPage <= 0) {
                $perPage = 4;
            }
            if ($perPage > 60) {
                $perPage = 60;
            }
            $page = $request->input('page', 1);
            $price = $request->input('price');
            $orderby = $request->input('orderby', 'desc');

            $cacheKey = "category:{$id}:products:{$paginate}:{$perPage}:{$page}:{$price}:{$orderby}";
            $products = Cache::remember($cacheKey, 300, function () use ($id, $request, $paginate, $perPage, $page, $price, $orderby) {
                $query = Product::select([
                    'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                    'pro_image', 'pro_description', 'pro_category_id'
                ])
                ->where([
                    'pro_category_id' => $id,
                    'pro_active' => Product::STATUS_PUBLIC 
                ])
                ->with(['category:id,c_name,c_slug']);

                // Price filter
                if($price){
                    switch($price){
                        case '1':
                            $query->where('pro_price','<',25);
                            break;
                        case '2':
                            $query->whereBetween('pro_price',[25,100000]);
                            break;
                        case '3':
                            $query->whereBetween('pro_price',[300000,5000000]);
                            break;
                        case '4':
                            $query->whereBetween('pro_price',[500000,7000000]);
                            break;
                        case '5':
                            $query->whereBetween('pro_price',[700000,10000000]);
                            break;
                        case '6':
                            $query->where('pro_price','>',10000000);
                            break;
                    }
                }

                // Ordering
                switch($orderby){
                    case 'asc':
                        $query->orderBy('id','ASC');
                        break;
                    case 'price_max':
                        $query->orderBy('pro_price','ASC');
                        break;
                    case 'price_min':
                        $query->orderBy('pro_price','DESC');
                        break;
                    case 'desc':
                    default:
                        $query->orderBy('id','DESC');
                        break;
                }

                return $paginate ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
            });

            $cateProduct = Cache::remember("category:{$id}", 300, function () use ($id) {
                return Category::find($id);
            });

            $viewData =[
                'products' => $products,
                'cateProduct' => $cateProduct,
                'isPaginated' => $paginate,
            ];
            return view('product.index',$viewData);
        }

        // ⚡ OPTIMIZED: Search functionality với pagination, cache, multi-field search
        if($request->k){
            $keyword = trim($request->k);
            $paginate = $request->boolean('paginate', true);
            $perPage = (int) $request->input('per_page', 20); // 20 items per page for search
            if ($perPage <= 0) {
                $perPage = 20;
            }
            if ($perPage > 60) {
                $perPage = 60;
            }
            $page = $request->input('page', 1);
            $orderby = $request->input('orderby', 'relevance');

            // Cache search results
            $cacheKey = "search:" . md5($keyword) . ":{$paginate}:{$perPage}:{$page}:{$orderby}";
            $products = Cache::remember($cacheKey, 300, function () use ($keyword, $paginate, $perPage, $page, $orderby) {
                $query = Product::select([
                    'id', 'pro_name', 'pro_slug', 'pro_price', 'pro_sale',
                    'pro_image', 'pro_description', 'pro_content', 'pro_category_id'
                ])
                ->where('pro_active', Product::STATUS_PUBLIC)
                ->with(['category:id,c_name,c_slug']);

                // ⚡ SEARCH IN MULTIPLE FIELDS (tên, mô tả, category)
                $query->where(function($q) use ($keyword) {
                    // Search in product name (highest priority)
                    $q->where('pro_name', 'like', '%' . $keyword . '%')
                      // Or search in description
                      ->orWhere('pro_description', 'like', '%' . $keyword . '%')
                      // Or search in content
                      ->orWhere('pro_content', 'like', '%' . $keyword . '%')
                      // Or search in category name
                      ->orWhereHas('category', function($catQuery) use ($keyword) {
                          $catQuery->where('c_name', 'like', '%' . $keyword . '%');
                      });
                });

                // Ordering
                switch($orderby){
                    case 'name_asc':
                        $query->orderBy('pro_name', 'ASC');
                        break;
                    case 'name_desc':
                        $query->orderBy('pro_name', 'DESC');
                        break;
                    case 'price_asc':
                        $query->orderBy('pro_price', 'ASC');
                        break;
                    case 'price_desc':
                        $query->orderBy('pro_price', 'DESC');
                        break;
                    case 'newest':
                        $query->orderBy('id', 'DESC');
                        break;
                    case 'oldest':
                        $query->orderBy('id', 'ASC');
                        break;
                    case 'relevance':
                    default:
                        // Sort by relevance: exact match in name first, then partial matches
                        $query->orderByRaw("
                            CASE 
                                WHEN pro_name = ? THEN 1
                                WHEN pro_name LIKE ? THEN 2
                                WHEN pro_name LIKE ? THEN 3
                                ELSE 4
                            END, id DESC
                        ", [$keyword, $keyword . '%', '%' . $keyword . '%']);
                        break;
                }

                return $paginate ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
            });

            $viewData = [
                'products' => $products,
                'isPaginated' => $paginate,
                'searchKeyword' => $keyword,
                'totalResults' => $products->total() ?? $products->count(),
            ];
            return view('product.index', $viewData);
        }

        return redirect('/');
    }
}
