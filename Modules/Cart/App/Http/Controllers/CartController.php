<?php

namespace Modules\Cart\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Models\Product;
use App\Models\Models\Cart;
use App\Models\Models\Order;
use App\Models\Models\Transaction;
use App\Models\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Events\DashboardUpdated;

use App\Http\Controllers\Controller;

class CartController extends Controller
{
    private function getUserId()
    {
        $userId = get_data_user('web');
        if (!$userId) {
            return null;
        }
        return $userId;
    }

    private function clearCartCache(): void
    {
        $userId = $this->getUserId();
        if ($userId) {
            Cache::forget("cart:user:{$userId}");
        }
    }

    public function index()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập');
        }

        $carts = Cart::with('product')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->get();
        return view('layouts.cart', compact('carts'));
    }

    public function add(Product $product, Request $request)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thêm giỏ hàng');
        }

        if ($product->quantity == 0) {
            return redirect()->back()->with('warning', "Sản phẩm tạm hết hàng");
        }

        $buyNow = (bool) $request->query('buy_now', false);
        $quantity = max(1, (int) $request->input('quantity', 1));

        $cartExit = Cart::where('user_id', $userId)
            ->where('pro_id', $product->id)
            ->first();

        if ($cartExit) {
            $cartExit->increment('quantity', $quantity);
            $this->clearCartCache();

            if ($buyNow) {
                return redirect()->route('form.pay')->with('success', 'Thành công');
            }
            return redirect()->route('cart.index')->with('success', 'Thành công');
        }

        $datas = [
            'user_id' => $userId,
            'pro_id' => $product->id,
            'name' => $product->pro_name,
            'price' => $product->pro_sale ? $product->pro_sale : $product->pro_price,
            'quantity' => $quantity,
            'image' => $product->pro_image,
        ];

        if (Cart::create($datas)) {
            $this->clearCartCache();
            if ($buyNow) {
                return redirect()->route('form.pay')->with('success', "Thêm giỏ hàng thành công");
            }
            return redirect()->route('cart.index')->with('success', "Thêm giỏ hàng thành công");
        }

        return redirect()->back()->with('danger', 'Thất bại');
    }

    public function delete($product_id)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->route('login');
        }

        Cart::where('user_id', $userId)
            ->where('pro_id', $product_id)
            ->delete();

        $this->clearCartCache();

        return redirect()->route('cart.index')->with('success', "Xoá thành công");
    }

    public function update(Product $product, Request $request)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->route('login');
        }

        $quantity = max(1, (int) $request->input('quantity', 1));

        $cartExit = Cart::where('user_id', $userId)
            ->where('pro_id', $product->id)
            ->first();

        if ($cartExit) {
            if ($quantity > $product->quantity) {
                return redirect()->back()->with('danger', 'Số lượng trong giỏ hàng không thể lớn hơn số lượng có sẵn của sản phẩm');
            }

            $cartExit->update(['quantity' => $quantity]);
            $this->clearCartCache();

            return redirect()->route('cart.index')->with('success', 'Thành công');
        }

        return redirect()->back()->with('danger', 'Thất bại');
    }

    public function clear()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->route('login');
        }

        Cart::where('user_id', $userId)->delete();
        $this->clearCartCache();

        return redirect()->back()->with('success', 'Xoá thành công');
    }

    public function getPay()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->route('login');
        }

        $carts = Cart::with('product')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->get();
        return view('layouts.pay', compact('carts'));
    }

    public function saveCart(Request $request, User $user, Product $product)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->route('login');
        }

        $paymentMethod = $request->input('payment_method', 'cod');
        $allowedMethods = ['cod', 'momo', 'qrcode', 'paypal', 'vnpay'];
        if (!in_array($paymentMethod, $allowedMethods, true)) {
            $paymentMethod = 'cod';
        }

        $orders = Cart::where('user_id', $userId)->get();
        $totalAmount = 0;
        foreach ($orders as $order) {
            $subtotal = $order->price * $order->quantity;
            $subtotalWithVAT = $subtotal * 1.1;
            $totalAmount += $subtotalWithVAT;
        }

        $transactionId = Transaction::insertGetId([
            'tr_user_id' => $userId,
            'tr_total' => (int) $totalAmount,
            'tr_note' => $request->note,
            'tr_phone' => $request->phone,
            'tr_address' => $request->address,
            'tr_payment_method' => $paymentMethod,
            'tr_status' => $paymentMethod === 'cod' ? Transaction::STATUS_DEFAULT : Transaction::STATUS_WAIT,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if ($transactionId) {
            $carts = Cart::where('user_id', $userId)->get();
            foreach ($carts as $cart) {
                $product = Product::find($cart->pro_id);
                if ($product) {
                    Order::insert([
                        'od_transaction_id' => $transactionId,
                        'od_cart_id' => $cart->id,
                        'od_quantity' => $cart->quantity,
                        'od_price' => $cart->price,
                        'od_product_id' => $product->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }

        $transaction = Transaction::find($transactionId);
        $orderDetails = $carts->map(function ($cart) {
            return [
                'product_id' => $cart->pro_id,
                'product_name' => $cart->name,
                'quantity' => $cart->quantity,
                'price' => $cart->price,
            ];
        })->toArray();

        event(new \App\Events\OrderPlaced($transaction, $orderDetails));

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
