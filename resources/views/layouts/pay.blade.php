@extends('layouts.app')
@section('content')

<div class="checkout-area pt-80 pb-80">
    <div class="container">
        <div class="section-title text-center mb-40">
            <h2 class="title-border">Thanh toán đơn hàng</h2>
        </div>

        <form action="{{ url('oder/pay') }}" method="POST" id="checkout-form">
            @csrf
            <div class="row">
                <div class="col-lg-7">
                    <div class="checkout-card">
                        <h4 class="checkout-card__title">
                            <i class="zmdi zmdi-account"></i> Thông tin giao hàng
                        </h4>
                        @if(Auth::check())
                        <div class="checkout-field">
                            <label>Họ và tên</label>
                            <input type="text" name="name" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="checkout-field">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{ Auth::user()->email }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkout-field">
                                    <label>Số điện thoại</label>
                                    <input type="text" name="phone" value="{{ Auth::user()->phone }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="checkout-field">
                            <label>Địa chỉ nhận hàng</label>
                            <input type="text" name="address" value="{{ Auth::user()->address }}" required>
                        </div>
                        <div class="checkout-field">
                            <label>Ghi chú</label>
                            <textarea name="note" rows="3" placeholder="Ghi chú cho đơn hàng (tùy chọn)"></textarea>
                        </div>
                        @endif
                    </div>

                    <div class="checkout-card mt-20">
                        <h4 class="checkout-card__title">
                            <i class="zmdi zmdi-card"></i> Phương thức thanh toán
                        </h4>
                        <div class="payment-methods">
                            <label class="payment-method payment-method--active">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <span class="payment-method__radio"></span>
                                <span class="payment-method__icon" style="background:#27ae60;">
                                    <i class="zmdi zmdi-money"></i>
                                </span>
                                <span class="payment-method__info">
                                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                                    <small>Trả tiền mặt khi nhận hàng</small>
                                </span>
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="vnpay">
                                <span class="payment-method__radio"></span>
                                <span class="payment-method__icon" style="background:#0066b2;">VN</span>
                                <span class="payment-method__info">
                                    <strong>VNPay</strong>
                                    <small>ATM / Visa / MasterCard / QR Pay</small>
                                </span>
                                <span class="payment-method__badge">Phổ biến</span>
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="momo">
                                <span class="payment-method__radio"></span>
                                <span class="payment-method__icon" style="background:#a50064;">M</span>
                                <span class="payment-method__info">
                                    <strong>Ví MoMo</strong>
                                    <small>Thanh toán qua ví điện tử MoMo</small>
                                </span>
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="paypal">
                                <span class="payment-method__radio"></span>
                                <span class="payment-method__icon" style="background:#003087;">P</span>
                                <span class="payment-method__info">
                                    <strong>PayPal</strong>
                                    <small>Thanh toán quốc tế qua PayPal</small>
                                </span>
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="qrcode">
                                <span class="payment-method__radio"></span>
                                <span class="payment-method__icon" style="background:#1e88e5;">
                                    <i class="zmdi zmdi-smartphone"></i>
                                </span>
                                <span class="payment-method__info">
                                    <strong>QR Code chuyển khoản</strong>
                                    <small>Quét mã QR bằng app ngân hàng</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="checkout-card checkout-card--summary">
                        <h4 class="checkout-card__title">
                            <i class="zmdi zmdi-shopping-cart"></i> Đơn hàng của bạn
                        </h4>

                        @php
                            $sum = 0;
                            $sum_vat = 0;
                            $tong = 0;
                            foreach($carts as $item) {
                                $sum += $item->quantity * $item->price;
                                $sum_vat += $item->quantity * $item->price * 0.1;
                                $tong += $item->quantity;
                            }
                        @endphp

                        <div class="order-items">
                            @foreach($carts as $cart)
                            <div class="order-item">
                                <div class="order-item__img">
                                    <img src="{{ $cart->image ? (strpos($cart->image, 'http') === 0 ? $cart->image : asset($cart->image)) : asset('upload/no-image.jpg') }}" alt="{{ $cart->name }}">
                                    <span class="order-item__qty">{{ $cart->quantity }}</span>
                                </div>
                                <div class="order-item__info">
                                    <p class="order-item__name">{{ $cart->name }}</p>
                                    <p class="order-item__price">{{ number_format($cart->price) }}đ x {{ $cart->quantity }}</p>
                                </div>
                                <div class="order-item__total">
                                    {{ number_format($cart->quantity * $cart->price) }}đ
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="order-summary">
                            <div class="order-summary__row">
                                <span>Tạm tính ({{ $tong }} sản phẩm)</span>
                                <span>{{ number_format($sum) }}đ</span>
                            </div>
                            <div class="order-summary__row">
                                <span>VAT (10%)</span>
                                <span>{{ number_format($sum_vat) }}đ</span>
                            </div>
                            <div class="order-summary__row">
                                <span>Phí vận chuyển</span>
                                <span style="color:#27ae60;">Miễn phí</span>
                            </div>
                            <div class="order-summary__row order-summary__row--total">
                                <span>Tổng thanh toán</span>
                                <span class="order-total-price">{{ number_format($sum + $sum_vat) }}đ</span>
                            </div>
                        </div>

                        <button type="submit" class="btn-checkout" id="btn-place-order">
                            <i class="zmdi zmdi-lock"></i> Đặt hàng
                        </button>

                        <p class="checkout-secure">
                            <i class="zmdi zmdi-shield-check"></i>
                            Thông tin thanh toán được bảo mật bởi SSL 256-bit
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .checkout-card {
        background: #fff;
        border: 1px solid #e8ecf1;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,.04);
    }
    .checkout-card__title {
        font-size: 16px;
        font-weight: 700;
        color: #1f2a44;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f2f5;
    }
    .checkout-card__title i {
        margin-right: 8px;
        color: #4b5bff;
    }
    .checkout-field {
        margin-bottom: 16px;
    }
    .checkout-field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #3a476a;
        margin-bottom: 6px;
    }
    .checkout-field input,
    .checkout-field textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color .2s;
        outline: none;
    }
    .checkout-field input:focus,
    .checkout-field textarea:focus {
        border-color: #4b5bff;
        box-shadow: 0 0 0 3px rgba(75,91,255,.1);
    }

    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .payment-method {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 2px solid #e8ecf1;
        border-radius: 10px;
        cursor: pointer;
        transition: all .2s;
        position: relative;
    }
    .payment-method:hover {
        border-color: #b8c0ff;
        background: #f8f9ff;
    }
    .payment-method--active,
    .payment-method:has(input:checked) {
        border-color: #4b5bff;
        background: #f0f2ff;
    }
    .payment-method input { display: none; }
    .payment-method__radio {
        width: 20px;
        height: 20px;
        border: 2px solid #c0c6d4;
        border-radius: 50%;
        flex-shrink: 0;
        position: relative;
    }
    .payment-method:has(input:checked) .payment-method__radio {
        border-color: #4b5bff;
    }
    .payment-method:has(input:checked) .payment-method__radio::after {
        content: '';
        position: absolute;
        top: 3px; left: 3px;
        width: 10px; height: 10px;
        background: #4b5bff;
        border-radius: 50%;
    }
    .payment-method__icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
    }
    .payment-method__icon i { font-size: 20px; }
    .payment-method__info {
        flex: 1;
    }
    .payment-method__info strong {
        display: block;
        font-size: 14px;
        color: #1f2a44;
    }
    .payment-method__info small {
        font-size: 12px;
        color: #7a849b;
    }
    .payment-method__badge {
        position: absolute;
        top: -8px;
        right: 12px;
        background: #e53935;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .checkout-card--summary {
        position: sticky;
        top: 20px;
    }
    .order-items {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 16px;
    }
    .order-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f0f2f5;
    }
    .order-item:last-child { border-bottom: none; }
    .order-item__img {
        width: 56px;
        height: 56px;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
        border: 1px solid #eee;
    }
    .order-item__img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .order-item__qty {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #4b5bff;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .order-item__info { flex: 1; }
    .order-item__name {
        font-size: 13px;
        font-weight: 600;
        color: #1f2a44;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .order-item__price {
        font-size: 12px;
        color: #7a849b;
        margin: 2px 0 0;
    }
    .order-item__total {
        font-size: 14px;
        font-weight: 700;
        color: #1f2a44;
        white-space: nowrap;
    }
    .order-summary {
        border-top: 2px solid #f0f2f5;
        padding-top: 16px;
    }
    .order-summary__row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        font-size: 14px;
        color: #3a476a;
    }
    .order-summary__row--total {
        border-top: 2px solid #1f2a44;
        margin-top: 8px;
        padding-top: 12px;
        font-weight: 700;
        font-size: 16px;
        color: #1f2a44;
    }
    .order-total-price {
        color: #e53935;
        font-size: 20px;
    }
    .btn-checkout {
        display: block;
        width: 100%;
        padding: 14px;
        background: #4b5bff;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 20px;
        transition: background .2s;
    }
    .btn-checkout:hover { background: #3a48e0; }
    .btn-checkout i { margin-right: 6px; }
    .checkout-secure {
        text-align: center;
        font-size: 12px;
        color: #7a849b;
        margin-top: 12px;
    }
    .checkout-secure i { color: #27ae60; margin-right: 4px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var methods = document.querySelectorAll('.payment-method');
    methods.forEach(function(m) {
        m.addEventListener('click', function() {
            methods.forEach(function(el) { el.classList.remove('payment-method--active'); });
            m.classList.add('payment-method--active');
        });
    });
});
</script>

@stop
