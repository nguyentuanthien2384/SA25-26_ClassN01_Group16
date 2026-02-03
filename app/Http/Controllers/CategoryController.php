<?php

namespace App\Http\Controllers;

use App\Models\Models\Category;
use App\Models\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getListProduct(Request $request){
        $url = $request->segment(2);
        $url = preg_split('/(-)/i', $url);
        
        if($id = array_pop($url)){
            $products = Product::where([
                'pro_category_id' => $id,
                'pro_active' => Product::STATUS_PUBLIC 
            ]);
            if($request->price){
                $price = $request->price;
                switch($price){
                    case '1':
                        $products ->where('pro_price','<',25);
                        break;
                    case '2':
                        $products ->whereBetween('pro_price',[25,100000]);
                        break;
                    case '3':
                        $products ->where('pro_price',[300000,5000000]);
                        break;
                    case '4':
                        $products ->where('pro_price',[500000,7000000]);
                        break;
                    case '5':
                        $products ->where('pro_price',[700000,10000000]);
                        break;
                    case '6':
                        $products ->where('pro_price','>',10000000);
                        break;
                }
            }
            if($request->orderby){
                $orderby = $request->orderby;
                switch($orderby){
                    case 'desc':
                        $products->orderby('id','DESC');
                        break;
                    case 'asc':
                        $products->orderby('id','ASC');
                        break;
                    case 'price_max':
                        $products->orderby('pro_price','ASC');
                        break;
                    case 'price_min':
                        $products->orderby('pro_price','DESC');
                        break;
                }
            }
            $paginate = $request->boolean('paginate', true);
            $perPage = (int) $request->input('per_page', 4);
            if ($perPage <= 0) {
                $perPage = 4;
            }
            if ($perPage > 60) {
                $perPage = 60;
            }
            $products = $paginate ? $products->paginate($perPage) : $products->get();
            $cateProduct = Category::find($id);
            $viewData =[
                'products' => $products,
                'cateProduct' => $cateProduct,
                'isPaginated' => $paginate,
            ];
            return view('product.index',$viewData);
        }
        if($request->k){
            $products = Product::where([
                'pro_active' => Product::STATUS_PUBLIC 
            ])->where('pro_name','like','%'.$request->k.'%');
            $paginate = false;
            $products = $products->get();
            $viewData = [
                'products'=> $products,
                'isPaginated' => $paginate,
            ];
            return view('product.index',$viewData);
        }
        return redirect('/');
    }
}
