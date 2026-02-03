<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestProduct;
use App\Models\Models\Category;
use App\Models\Models\Product;
use App\Models\Models\ProImage;
use App\Events\DashboardUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminProductController extends Controller
{
   public function index(Request $request)
   {
      $products = Product::with('category:id,c_name');
      if ($request->name) $products->where('pro_name', 'like', '%' . $request->name . '%');
      if ($request->cate) $products->where('pro_category_id', $request->cate);
      $products = $products->orderByDesc('id')->simplePaginate(10);
      $categorys = $this->getCategory();
   
      $viewData = [
         'products' => $products,
         'categorys' => $categorys
      ];
      return view('admin::product.index', $viewData);
   }
   public function create()
   {  
      $categorys = $this->getCategory();
      return view('admin::product.create', compact('categorys'));
   }
   public function store(RequestProduct $requestProduct)
   {
      $this->insertOrUpdate($requestProduct);
      
      return redirect()->route('admin.get.list.product')->with('success', 'Thêm mới thành công');
   }
   public function edit($id)
   {
      $product = Product::find($id);
      $categorys = $this->getCategory();
      return view('admin::product.update', compact('product', 'categorys'));
   }
   public function update(RequestProduct $requestProduct, $id)
   {
      $this->insertOrUpdate($requestProduct, $id);
      return redirect()->route('admin.get.list.product')->with('success', 'Chỉnh sửa thành công');
   }
   public function getCategory()
   {
      return Category::all();
   }
   public function insertOrUpdate($requestProduct, $id = '' )
   {
      $product = new Product();
      if ($id) $product = Product::find($id);

      $product->pro_name = $requestProduct->pro_name;
      $product->pro_slug = str_slug($requestProduct->pro_name);
      $product->pro_category_id = $requestProduct->pro_category_id;
      $product->pro_price = $requestProduct->pro_price;
      $product->pro_sale = $requestProduct->pro_sale;
      $product->pro_description = $requestProduct->pro_description;
      $product->pro_content = $requestProduct->pro_content;
      $product->quantity = $requestProduct->quantity;
      $product->pro_image = $requestProduct->pro_image;

      if ($requestProduct->hasFile('file_upload')) {
         $this->validate(
            $requestProduct,
             [
                 'file_upload' => 'mimes:jpg,jpeg,png,gif|max:2048',
             ],
             [
                 'file_upload.mimes' => 'Chỉ chấp nhận hình thẻ với đuôi .jpg .jpeg .png .gif',
                 'file_upload.max' => 'Hình thẻ giới hạn dung lượng không quá 2M',
             ]
         );
         $file = $requestProduct->file_upload;
         $ext = $requestProduct->file_upload->extension();
         $file_name = time() . '-' . 'product.' . $ext;
         $file->move(public_path('upload'), $file_name);
         $requestProduct->merge(['image' => $file_name]);
     }
     $product->save();
     event(new DashboardUpdated('product'));

     // Lưu các hình ảnh chi tiết
     $product_id = $product->id;
     if ($requestProduct->hasFile('img_detail')) {
         foreach ($requestProduct->file('img_detail') as $img_item) {
             $file_name = time() . '-' . 'product_detail.' . '-' . $img_item->getClientOriginalName();
             $img_item->move(public_path('upload'), $file_name);
             ProImage::create(['product_id' => $product_id, 'img_product' => $file_name]);
         }
     }
  }

  public function action( $action,$id){
   $mesages = '';
   if($action){
       $product = Product::find($id);
       if (!$product) {
           return redirect()->back()->with('danger', 'Sản phẩm không tồn tại.');
       }
       switch($action){
           case 'delete':
               $product->delete();
               $mesages = 'Xoá dữ liệu thành công';
               break;
            case 'active':
               $product->pro_active = $product->pro_active ? 0: 1;
               $product->save();
               break;
            case 'hot':
                  $product->pro_hot = $product->pro_hot ? 0: 1;
                  $product->save();
                  break;
       }
   }
   event(new DashboardUpdated('product'));
   return redirect()->back()->with('success',$mesages);
}
}
