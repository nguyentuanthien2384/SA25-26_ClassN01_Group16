@extends('layouts.app')
@section('content')
    <style>
        .pd-wrap {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        .pd-gallery {
            width: 50%;
            flex-shrink: 0;
        }
        .pd-gallery .main-img {
            width: 100%;
            max-height: 460px;
            object-fit: contain;
            border-radius: 12px;
            background: #f8f9fc;
            display: block;
        }
        .pd-gallery .thumb-list {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            overflow-x: auto;
        }
        .pd-gallery .thumb-list img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border: 2px solid #e8ecf1;
            border-radius: 8px;
            cursor: pointer;
            transition: border-color .2s;
            background: #fff;
        }
        .pd-gallery .thumb-list img:hover,
        .pd-gallery .thumb-list img.active {
            border-color: #4b5bff;
        }
        .pd-info {
            flex: 1;
            min-width: 0;
        }
        .pd-info .pd-name {
            font-size: 22px;
            font-weight: 700;
            color: #1f2a44;
            margin: 0 0 10px;
            line-height: 1.3;
        }
        .pd-info .pd-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
            color: #f5a623;
            font-size: 20px;
        }
        .pd-info .pd-rating span {
            color: #7a849b;
            font-size: 14px;
        }
        .pd-info .pd-price-wrap {
            background: #fef2f2;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 16px;
        }
        .pd-info .pd-price {
            font-size: 24px;
            font-weight: 800;
            color: #e53935;
        }
        .pd-info .pd-price-old {
            font-size: 16px;
            color: #9e9e9e;
            text-decoration: line-through;
            margin-left: 10px;
        }
        .pd-info .pd-desc {
            font-size: 14px;
            color: #3a476a;
            line-height: 1.7;
            margin-bottom: 16px;
            max-height: 120px;
            overflow-y: auto;
        }
        .pd-info .pd-stock {
            font-size: 14px;
            color: #3a476a;
            margin-bottom: 16px;
            padding: 8px 14px;
            background: #f0fdf4;
            border-radius: 8px;
            display: inline-block;
        }
        .pd-info .pd-stock strong {
            color: #16a34a;
        }
        .pd-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .pd-actions .btn-cart,
        .pd-actions .btn-buy {
            flex: 1;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }
        .pd-actions .btn-cart {
            border: 2px solid #4b5bff;
            color: #4b5bff;
            background: #fff;
        }
        .pd-actions .btn-cart:hover {
            background: #f0f2ff;
        }
        .pd-actions .btn-buy {
            border: 2px solid #e53935;
            color: #fff;
            background: #e53935;
        }
        .pd-actions .btn-buy:hover {
            background: #c62828;
        }

        .pd-tabs {
            margin-top: 40px;
        }
        .pd-tabs .tab-nav {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #e8ecf1;
            margin-bottom: 24px;
        }
        .pd-tabs .tab-nav button {
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            color: #7a849b;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all .2s;
            margin-bottom: -2px;
        }
        .pd-tabs .tab-nav button.active {
            color: #4b5bff;
            border-bottom-color: #4b5bff;
        }
        .pd-tabs .tab-panel {
            display: none;
        }
        .pd-tabs .tab-panel.active {
            display: block;
        }
        .pd-tabs .tab-panel .pro-content {
            font-size: 14px;
            line-height: 1.8;
            color: #3a476a;
        }
        .pd-tabs .tab-panel .pro-content img {
            max-width: 100%;
            height: auto;
        }

        .review-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .review-item {
            display: flex;
            gap: 14px;
            padding: 16px 0;
            border-bottom: 1px solid #f0f2f5;
        }
        .review-item:last-child {
            border-bottom: none;
        }
        .review-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e8ecf1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
            font-weight: 700;
            color: #4b5bff;
        }
        .review-body {
            flex: 1;
            min-width: 0;
        }
        .review-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }
        .review-name {
            font-weight: 700;
            font-size: 14px;
            color: #1f2a44;
        }
        .review-date {
            font-size: 12px;
            color: #7a849b;
        }
        .review-text {
            font-size: 14px;
            color: #3a476a;
            margin: 0;
            word-break: break-word;
        }

        .review-form {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 20px;
            margin-top: 24px;
        }
        .review-form h4 {
            font-size: 16px;
            font-weight: 700;
            color: #1f2a44;
            margin: 0 0 16px;
        }
        .star-rating {
            unicode-bidi: bidi-override;
            direction: rtl;
            text-align: left;
            margin-bottom: 12px;
        }
        .star-rating input[type="radio"] { display: none; }
        .star-rating label {
            display: inline-block;
            font-size: 28px;
            color: #d1d5db;
            cursor: pointer;
            transition: color .15s;
        }
        .star-rating label:before { content: '★'; }
        .star-rating input[type="radio"]:checked ~ label { color: #f5a623; }
        .star-rating label:hover,
        .star-rating label:hover ~ label { color: #f5a623; }
        .review-form input[type="text"],
        .review-form textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 12px;
            outline: none;
        }
        .review-form input[type="text"]:focus,
        .review-form textarea:focus {
            border-color: #4b5bff;
            box-shadow: 0 0 0 3px rgba(75,91,255,.1);
        }
        .review-form button[type="submit"] {
            padding: 10px 24px;
            background: #4b5bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
        }
        .review-form button[type="submit"]:hover { background: #3a48e0; }

        @media (max-width: 768px) {
            .pd-wrap {
                flex-direction: column;
            }
            .pd-gallery {
                width: 100%;
            }
            .pd-actions {
                flex-direction: column;
            }
        }
    </style>

    <div class="product-area pt-80 pb-80">
        <div class="container">
            @php
                $totalReviews = $productDetails->pro_total_number ?? 0;
                $totalStars = $productDetails->pro_total ?? 0;
                $avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
            @endphp

            <div class="pd-wrap">
                <div class="pd-gallery">
                    <img
                        id="mainImage"
                        class="main-img"
                        src="{{ $productDetails->pro_image ? (strpos($productDetails->pro_image, 'http') === 0 ? $productDetails->pro_image : asset($productDetails->pro_image)) : asset('upload/no-image.jpg') }}"
                        alt="{{ $productDetails->pro_name }}"
                    />
                    <div class="thumb-list">
                        <img
                            class="active"
                            src="{{ $productDetails->pro_image ? (strpos($productDetails->pro_image, 'http') === 0 ? $productDetails->pro_image : asset($productDetails->pro_image)) : asset('upload/no-image.jpg') }}"
                            alt="{{ $productDetails->pro_name }}"
                            onclick="changeImage(this)"
                        />
                        @foreach($productimg as $img)
                        <img
                            src="{{ asset('upload/' . $img->img_product) }}"
                            alt=""
                            onclick="changeImage(this)"
                        />
                        @endforeach
                    </div>
                </div>

                <div class="pd-info">
                    <h1 class="pd-name">{{ $productDetails->pro_name }}</h1>

                    <div class="pd-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($avgRating))
                                <i class="zmdi zmdi-star"></i>
                            @elseif ($i - 0.5 <= $avgRating)
                                <i class="zmdi zmdi-star-half"></i>
                            @else
                                <i class="zmdi zmdi-star-outline"></i>
                            @endif
                        @endfor
                        <span>{{ $avgRating }} ({{ $totalReviews }} đánh giá)</span>
                    </div>

                    <div class="pd-price-wrap">
                        @if($productDetails->pro_sale > 0 && $productDetails->pro_sale < $productDetails->pro_price)
                            <span class="pd-price">{{ number_format($productDetails->pro_sale, 0, ',', '.') }}đ</span>
                            <span class="pd-price-old">{{ number_format($productDetails->pro_price, 0, ',', '.') }}đ</span>
                        @else
                            <span class="pd-price">{{ number_format($productDetails->pro_price, 0, ',', '.') }}đ</span>
                        @endif
                    </div>

                    <div class="pd-desc">
                        {!! $productDetails->pro_description !!}
                    </div>

                    <div class="pd-stock">
                        Kho: <strong>{{ $productDetails->quantity > 0 ? $productDetails->quantity . ' sản phẩm' : 'Tạm hết hàng' }}</strong>
                    </div>

                    <div class="pd-actions">
                        <a href="{{ route('cart.add', $productDetails->id) }}" class="btn-cart">
                            <i class="zmdi zmdi-shopping-cart-plus"></i> Thêm vào giỏ
                        </a>
                        <a href="{{ route('cart.add', ['product' => $productDetails->id, 'buy_now' => 1]) }}" class="btn-buy">
                            <i class="zmdi zmdi-flash"></i> Mua ngay
                        </a>
                    </div>
                </div>
            </div>

            <div class="pd-tabs">
                <div class="tab-nav">
                    <button class="active" onclick="switchTab(event, 'tab-desc')">Mô tả sản phẩm</button>
                    <button onclick="switchTab(event, 'tab-reviews')">Đánh giá ({{ $totalReviews }})</button>
                </div>

                <div id="tab-desc" class="tab-panel active">
                    <div class="pro-content">
                        {!! $productDetails->pro_content !!}
                    </div>
                </div>

                <div id="tab-reviews" class="tab-panel">
                    <h3 style="font-size:18px; font-weight:700; color:#1f2a44; margin-bottom:16px;">
                        Đánh giá khách hàng
                    </h3>

                    @if($ratings->count() > 0)
                    <ul class="review-list">
                        @foreach($ratings as $rating)
                        <li class="review-item">
                            <div class="review-avatar">
                                {{ strtoupper(substr(optional($rating->user)->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="review-body">
                                <div class="review-header">
                                    <span class="review-name">{{ optional($rating->user)->name ?? 'Ẩn danh' }}</span>
                                    <span class="review-date">{{ $rating->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="review-text">{{ $rating->ra_content }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                        <p style="color:#7a849b;">Chưa có đánh giá nào.</p>
                    @endif

                    @if(Auth::check())
                    <div class="review-form">
                        <h4>Gửi đánh giá của bạn</h4>
                        <form action="{{ route('postRating', $productDetails) }}" method="POST">
                            @csrf
                            <input type="hidden" name="ra_product_id" value="{{ $productDetails->id }}">
                            <div class="star-rating">
                                <input type="radio" id="star5" name="ra_number" value="5"><label for="star5"></label>
                                <input type="radio" id="star4" name="ra_number" value="4"><label for="star4"></label>
                                <input type="radio" id="star3" name="ra_number" value="3" checked><label for="star3"></label>
                                <input type="radio" id="star2" name="ra_number" value="2"><label for="star2"></label>
                                <input type="radio" id="star1" name="ra_number" value="1"><label for="star1"></label>
                            </div>
                            <input type="text" name="ra_content" placeholder="Nhập nội dung đánh giá..." required>
                            <button type="submit">Gửi đánh giá</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    function changeImage(thumb) {
        document.getElementById('mainImage').src = thumb.src;
        document.querySelectorAll('.thumb-list img').forEach(function(img) {
            img.classList.remove('active');
        });
        thumb.classList.add('active');
    }

    function switchTab(e, tabId) {
        document.querySelectorAll('.pd-tabs .tab-panel').forEach(function(p) { p.classList.remove('active'); });
        document.querySelectorAll('.pd-tabs .tab-nav button').forEach(function(b) { b.classList.remove('active'); });
        document.getElementById(tabId).classList.add('active');
        e.target.classList.add('active');
    }
    </script>
@stop
