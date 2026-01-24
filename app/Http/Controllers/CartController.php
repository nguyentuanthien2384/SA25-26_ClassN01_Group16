<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Models\Product;
use App\Models\Models\Cart;
use App\Models\Models\Order;
use App\Models\Models\Transaction;
use App\Models\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Can;
use App\Events\DashboardUpdated;

class CartController extends Controller
{   
    public function index(){
        $carts = Cart::with('product')->orderBy('id','DESC')->get();
        return view('layouts.cart',compact('carts'));
    }
    public function add(Product $product, User $user, Request $request){
        if($product->quantity == 0){
            return redirect()->back()->with('warning',"Sản phẩm tạm hết hàng");
        }
        $buyNow = (bool)$request->query('buy_now', false);
        $quantity = $request->quantity ? $request->quantity:1;
        $cartExit = Cart::where([
            'user_id' =>get_data_user('web'),
            'pro_id' =>$product-> id
        ])->first();

        if($cartExit){
            Cart::where([
                'user_id' =>get_data_user('web'),
                'pro_id' =>$product->id,
                'name' => $product->pro_name,
            ])->increment('quantity',$quantity);
            if ($buyNow) {
                return redirect()->route('form.pay')->with('success', 'Thành công');
            }
            return redirect()->route('cart.index')->with('success', 'Thành công');
        
        }else{
            $datas=[
                'user_id' =>get_data_user('web'),
                'pro_id' =>$product-> id,
                'name' => $product-> pro_name,
                'price' =>$product->pro_sale ? $product->pro_sale : $product->pro_price,
                'quantity' => $quantity,
                'sale' => $product->pro_sale,
                'image' =>$product->pro_image
            ];
            
            if(Cart:: create($datas)){
                if ($buyNow) {
                    return redirect()->route('form.pay')->with('success', "Thêm giỏ hàng thành công");
                }
                return redirect()->route('cart.index')->with('success', "Thêm giỏ hàng thành công");
        }
        }
        return redirect()->back()->with('danger', 'thất bại');
    }
    public function delete($product_id){
        Cart::where([
            'user_id' =>get_data_user('web'),
            'pro_id' =>$product_id
        ])->delete();
        return redirect()->back()->with('success', "Xoá thành công");
    }
    public function update(Product $product, Request $request){
        $quantity = $request->quantity ? $request->quantity : 1;
        $cartExit = Cart::where([
            'pro_id' =>$product->id
        ])->first();

        if($cartExit){
            $pro_quantity = $product->quantity;
        if ($quantity > $pro_quantity) {
            return redirect()->back()->with('danger', 'Số lượng trong giỏ hàng không thể lớn hơn số lượng có sẵn của sản phẩm');
        }
            Cart::where(['pro_id' =>$product->id])->update([
                'quantity' =>$quantity
            ]);
             return redirect()->route('cart.index')->with('success', 'Thành công');
        }
        return redirect()->back()->with('danger', 'thất bại');
    }
    public function clear(){
        Cart::where([
            'user_id' => get_data_user('web'),
        ])->delete();
        return redirect()->back()->with('success', 'xoá thành công');
    }
    public function getPay(){
        $carts = Cart::with('product')
            ->where('user_id', get_data_user('web'))
            ->orderBy('id', 'DESC')
            ->get();
        return view('layouts.pay', compact('carts'));
    }
    public function saveCart(Request $request, User $user, Product $product){
        $paymentMethod = $request->input('payment_method', 'cod');
        $allowedMethods = ['cod', 'momo', 'qrcode', 'paypal', 'vnpay'];
        if (!in_array($paymentMethod, $allowedMethods, true)) {
            $paymentMethod = 'cod';
        }

        $orders = Cart::where('user_id', get_data_user('web'))->get();
        $totalAmount = 0;
        foreach ($orders as $order) {
            $subtotal = $order->price * $order->quantity;
            $subtotalWithVAT = $subtotal * 1.1;
            $totalAmount += $subtotalWithVAT;
        }
        $transactionId = Transaction::insertGetId([
            'tr_user_id' => get_data_user('web'),
            'tr_total' => (int)$totalAmount,
            'tr_note' => $request-> note,
            'tr_phone' => $request-> phone,
            'tr_address' => $request-> address,
            'tr_payment_method' => $paymentMethod,
            'tr_status' => $paymentMethod === 'cod' ? Transaction::STATUS_DEFAULT : Transaction::STATUS_WAIT,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

        ]);
        if ($transactionId){
            $carts = Cart::where('user_id', get_data_user('web'))->get();
            foreach($carts as $cart){
                $product = Product::find($cart->pro_id); 


                 if($product) {
            Order::insert([
            'od_transaction_id' => $transactionId,
            'od_cart_id' => $cart->id,
            'od_quantity' => $cart->quantity,
            'od_price' => $cart->price,
            'od_product_id' => $product->id, // Sử dụng ID của sản phẩm
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
            }
        }
        if ($paymentMethod === 'cod') {
            $this->clear();
            event(new DashboardUpdated('order'));
            return redirect()->route('home')->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
        }

        return redirect()->route('payment.show', [
            'method' => $paymentMethod,
            'transaction' => $transactionId,
        ])->with('success', 'Vui lòng hoàn tất thanh toán.');
    }
}
