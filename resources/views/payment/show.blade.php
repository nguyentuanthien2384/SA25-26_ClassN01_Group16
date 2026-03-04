@extends('layouts.app')
@section('content')

<div class="payment-page pt-80 pb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="pay-card">
                    <div class="pay-card__header">
                        @if($method === 'vnpay')
                            <div class="pay-card__icon" style="background:#0066b2;">VN</div>
                            <h3>Thanh toán VNPay</h3>
                            <p>ATM nội địa / Visa / MasterCard / QR Pay</p>
                        @elseif($method === 'momo')
                            <div class="pay-card__icon" style="background:#a50064;">M</div>
                            <h3>Thanh toán MoMo</h3>
                            <p>Ví điện tử MoMo</p>
                        @elseif($method === 'paypal')
                            <div class="pay-card__icon" style="background:#003087;">P</div>
                            <h3>Thanh toán PayPal</h3>
                            <p>Thanh toán quốc tế</p>
                        @elseif($method === 'qrcode')
                            <div class="pay-card__icon" style="background:#1e88e5;"><i class="zmdi zmdi-smartphone"></i></div>
                            <h3>Chuyển khoản QR Code</h3>
                            <p>Quét mã bằng app ngân hàng</p>
                        @endif
                    </div>

                    <div class="pay-card__body">
                        <div class="pay-order-info">
                            <div class="pay-order-info__row">
                                <span>Mã đơn hàng</span>
                                <strong>#{{ $transaction->id }}</strong>
                            </div>
                            <div class="pay-order-info__row pay-order-info__row--total">
                                <span>Tổng thanh toán</span>
                                <strong>{{ number_format($transaction->tr_total) }}đ</strong>
                            </div>
                        </div>

                        @if(session('danger'))
                            <div class="pay-alert pay-alert--error">
                                <i class="zmdi zmdi-alert-circle"></i> {{ session('danger') }}
                            </div>
                        @endif
                        @if(session('warning'))
                            <div class="pay-alert pay-alert--warning">
                                <i class="zmdi zmdi-alert-triangle"></i> {{ session('warning') }}
                            </div>
                        @endif

                        @if($method === 'qrcode')
                            @if($qrBank && $qrAccount && $qrName)
                            <div class="qr-bank-info">
                                <div class="qr-bank-info__row">
                                    <span>Ngân hàng</span>
                                    <strong>{{ $qrBank }}</strong>
                                </div>
                                <div class="qr-bank-info__row">
                                    <span>Số tài khoản</span>
                                    <strong>{{ $qrAccount }}</strong>
                                </div>
                                <div class="qr-bank-info__row">
                                    <span>Chủ tài khoản</span>
                                    <strong>{{ $qrName }}</strong>
                                </div>
                                <div class="qr-bank-info__row">
                                    <span>Nội dung CK</span>
                                    <strong>DH{{ $transaction->id }}</strong>
                                </div>
                            </div>
                            @endif

                            @if($qrUrl)
                            <div class="qr-image-wrap">
                                <img src="{{ $qrUrl }}" alt="QR Code" class="qr-image">
                                <p class="qr-hint">Mở app ngân hàng &rarr; Quét mã QR &rarr; Xác nhận thanh toán</p>
                            </div>
                            @endif

                            <form method="POST" action="{{ route('payment.qrcode.confirm', ['transaction' => $transaction->id]) }}">
                                @csrf
                                <button type="submit" class="btn-pay btn-pay--confirm">
                                    <i class="zmdi zmdi-check-circle"></i> Tôi đã chuyển khoản
                                </button>
                            </form>
                        @else
                            <div class="pay-gateway-info">
                                @if($method === 'vnpay')
                                    <p>Bạn sẽ được chuyển đến cổng thanh toán VNPay để hoàn tất.</p>
                                    <div class="pay-test-info">
                                        <strong>Thẻ test VNPay Sandbox:</strong><br>
                                        Số thẻ: <code>9704198526191432198</code><br>
                                        Tên: <code>NGUYEN VAN A</code><br>
                                        Ngày phát hành: <code>07/15</code><br>
                                        OTP: <code>123456</code>
                                    </div>
                                @elseif($method === 'momo')
                                    <p>Bạn sẽ được chuyển đến cổng thanh toán MoMo.</p>
                                    <div class="pay-test-info">
                                        <strong>MoMo Test:</strong> Xác nhận thanh toán trên trang MoMo test.
                                    </div>
                                @elseif($method === 'paypal')
                                    <p>Bạn sẽ được chuyển đến PayPal để thanh toán.</p>
                                    <p class="pay-exchange">
                                        Tỷ giá: {{ number_format($transaction->tr_total) }}đ
                                        &asymp; ${{ number_format($transaction->tr_total / 24000, 2) }} USD
                                    </p>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('payment.init', ['method' => $method, 'transaction' => $transaction->id]) }}">
                                @csrf
                                <button type="submit" class="btn-pay">
                                    <i class="zmdi zmdi-lock"></i> Thanh toán {{ number_format($transaction->tr_total) }}đ
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('home') }}" class="pay-back">
                            <i class="zmdi zmdi-arrow-left"></i> Quay về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pay-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,.08);
        overflow: hidden;
    }
    .pay-card__header {
        text-align: center;
        padding: 30px 24px 20px;
        background: linear-gradient(135deg, #f8f9ff 0%, #eef1ff 100%);
        border-bottom: 1px solid #e8ecf1;
    }
    .pay-card__icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: 22px;
        margin-bottom: 12px;
    }
    .pay-card__icon i { font-size: 24px; }
    .pay-card__header h3 {
        font-size: 20px;
        font-weight: 700;
        color: #1f2a44;
        margin: 0 0 4px;
    }
    .pay-card__header p {
        font-size: 14px;
        color: #7a849b;
        margin: 0;
    }
    .pay-card__body {
        padding: 24px;
    }
    .pay-order-info {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .pay-order-info__row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        font-size: 14px;
        color: #3a476a;
    }
    .pay-order-info__row--total {
        border-top: 1px solid #e0e3ea;
        margin-top: 6px;
        padding-top: 10px;
    }
    .pay-order-info__row--total strong {
        font-size: 20px;
        color: #e53935;
    }
    .pay-alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }
    .pay-alert--error {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .pay-alert--warning {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .pay-alert i { margin-right: 6px; }
    .qr-bank-info {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .qr-bank-info__row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 14px;
    }
    .qr-bank-info__row span { color: #64748b; }
    .qr-bank-info__row strong { color: #1f2a44; }
    .qr-image-wrap {
        text-align: center;
        margin: 20px 0;
    }
    .qr-image {
        max-width: 240px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,.1);
    }
    .qr-hint {
        font-size: 13px;
        color: #7a849b;
        margin-top: 12px;
    }
    .pay-gateway-info {
        margin-bottom: 20px;
    }
    .pay-gateway-info p {
        font-size: 14px;
        color: #3a476a;
        margin-bottom: 12px;
    }
    .pay-test-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13px;
        color: #1e40af;
        line-height: 1.8;
    }
    .pay-test-info code {
        background: #dbeafe;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
    }
    .pay-exchange {
        font-size: 13px;
        color: #7a849b;
    }
    .btn-pay {
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
        transition: background .2s;
        margin-top: 8px;
    }
    .btn-pay:hover { background: #3a48e0; }
    .btn-pay--confirm { background: #27ae60; }
    .btn-pay--confirm:hover { background: #219a52; }
    .btn-pay i { margin-right: 6px; }
    .pay-back {
        display: block;
        text-align: center;
        margin-top: 16px;
        font-size: 14px;
        color: #7a849b;
        text-decoration: none;
    }
    .pay-back:hover { color: #4b5bff; }
    .pay-back i { margin-right: 4px; }
</style>

@stop
