<?php

namespace App\Http\Controllers;

use App\Models\Models\Article;
use App\Models\Models\Product;
use App\Models\Models\ProImage;
use App\Models\Models\Rating;
use App\Models\Models\User;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function productDetail(Request $request, $id){
        $url = $request->segment(2);
        $url = preg_split('/(-)/i', $url);
        $id = (int)$id;
        if($id = array_pop($url)){
            $productDetails = Product::find($id);
            $productimg = ProImage::where('product_id', $id)->get();
            $userDetails = User::find($productDetails->id);
            $ratings = Rating::where('ra_product_id', $id)->with('user')->orderBy('id',"DESC")->get();
            $articleNews = Product::where('pro_active', Product::STATUS_PUBLIC)
                ->orderBy('id', 'DESC')
                ->limit(10)
                ->get();
    
            $viewData = [
                'productDetails' => $productDetails,
                'userDetails' => $userDetails,
                'ratings' => $ratings,
                'productimg'=>$productimg,
                'articleNews' =>$articleNews
            ];
            return view('product.detail',$viewData);
        }
    }
}
