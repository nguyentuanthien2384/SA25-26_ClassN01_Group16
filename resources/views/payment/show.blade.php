@extends('layouts.app')
@section('content')

<div class="elements-tab pb-80">
    <div class="blog-area blog-2 pt-30">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h2 class="title-border">Thanh toán</h2>
                    </div>
                </div>
            </div>
            <div class="row" style="color:black; font-size:15px">
                <div class="col-md-8 col-md-offset-2">
                    <div style="border:1px solid #eee; padding:20px; background:#fafafa;">
                        <p><strong>Mã đơn hàng:</strong> #{{ $transaction->id }}</p>
                        <p><strong>Tổng tiền:</strong> {{ number_format($transaction->tr_total) }} VND</p>
                        <p><strong>Phương thức:</strong> {{ strtoupper($method) }}</p>

                        @if($method === 'qrcode')
                            <p>Quét QR để thanh toán.</p>
                            @if($qrBank && $qrAccount && $qrName)
                                <p style="margin-bottom:5px;">
                                    <strong>Ngân hàng:</strong> {{ $qrBank }} |
                                    <strong>Số TK:</strong> {{ $qrAccount }} |
                                    <strong>Chủ TK:</strong> {{ $qrName }}
                                </p>
                            @else
                                <p style="color:#c00;">Chưa cấu hình VietQR. Đang dùng QR tạm.</p>
                            @endif
                            @if($qrUrl)
                                <img src="{{ $qrUrl }}" alt="QR Code">
                                <p style="margin-top:10px;"><small>{{ $qrData }}</small></p>
                            @endif
                        @elseif($method === 'momo')
                            <p>MoMo sandbox: bấm để chuyển sang cổng MoMo.</p>
                        @elseif($method === 'paypal')
                            <p>PayPal sandbox: bấm để chuyển sang cổng PayPal.</p>
                        @elseif($method === 'vnpay')
                            <p>VNPay sandbox: bấm để chuyển sang cổng VNPay.</p>
                        @endif

                        @if($method === 'qrcode')
                            <form method="POST" action="{{ route('payment.qrcode.confirm', ['transaction' => $transaction->id]) }}" style="margin-top:15px;">
                                @csrf
                                <button type="submit" class="button-one">Xác nhận đã thanh toán</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('payment.init', ['method' => $method, 'transaction' => $transaction->id]) }}" style="margin-top:15px;">
                                @csrf
                                <button type="submit" class="button-one">Thanh toán ngay</button>
                            </form>
                        @endif
                    </div>
                    <div style="margin-top:15px;">
                        <a href="{{ route('home') }}">Quay về trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
