<?php

namespace Modules\Review\App\Http\Controllers;

use App\Models\Models\Product;
use App\Models\Models\Rating;
use App\Models\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    public function postRating(Request $request, $id){
        $productId = $request->ra_product_id;
        $userBoughtProduct = DB::table('oders')
        ->join('transactions', 'oders.od_transaction_id', '=', 'transactions.id')
        ->where('transactions.tr_user_id', get_data_user('web'))
        ->whereExists(function ($query) use ($productId) {
        $query->select(DB::raw(1))
            ->from('oders')
            ->whereRaw('oders.od_product_id = '.$productId);
    })
    ->exists();

if (!$userBoughtProduct) {
    return redirect()->back()->with('danger', 'Bạn phải mua sản phẩm này trước khi đánh giá.');
}
        
        $existingRating = Rating::where('ra_user_id', get_data_user('web'))
                                ->where('ra_product_id', $request->ra_product_id)
                                ->exists();
        if($existingRating) { 
            return redirect()->back()->with('warning', 'Bạn đã đánh giá sản phẩm này trước đó.');
        }
    
        // Kiểm tra xem người dùng có quyền đánh giá sản phẩm không (ví dụ: chỉ những người mua hàng mới có thể đánh giá)
        // $user = get_data_user('web');
        // if(!get_data_user('web')instanceof User){
        //     // Xử lý nếu hàm get_data_user('web') không trả về đúng kiểu dữ liệu
        //     return redirect()->back()->with('warning', 'Bạn phải mua sản phẩm mới được đánh giá.');
        // }
    
        // if(!get_data_user('web')->hasPurchased($request->ra_product_id)) {
        //     // Nếu người dùng không có quyền đánh giá sản phẩm, bạn có thể xử lý theo ý của mình, ví dụ: hiển thị thông báo lỗi
        //     return redirect()->back()->with('warning', 'Bạn không có quyền đánh giá sản phẩm này.');
        // }
        // Nếu người dùng có quyền đánh giá và chưa đánh giá trước đó, bạn có thể tạo bản ghi đánh giá mới
        $trt = Rating::create([
            'ra_user_id' => get_data_user('web'),
            'ra_number' => $request->ra_number,
            'ra_content' => $request->ra_content,
            'ra_product_id' => $request->ra_product_id 
        ]);
    
        if ($trt) {
            $product = Product::find($id); // Lấy sản phẩm dựa trên $productDetails['id']
            if ($product) {
                $product->pro_total_number += $request->ra_number;
                $product->pro_total ++;
                $product->save();
            }
        }
        
        return redirect()->back()->with('success', 'Bạn đã đánh giá thành công.');
    }
}