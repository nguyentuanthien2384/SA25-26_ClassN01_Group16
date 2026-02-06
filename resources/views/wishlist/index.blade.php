@extends('layouts.app')
@section('content')
<div class="elements-tab pb-80">
    <div class="blog-area blog-2 pt-30">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h2 class="title-border">Danh sách yêu thích</h2>
                    </div>
                </div>
            </div>
            <div class="row" style="color:black; font-size:15px">
                <div class="col-md-12">
                    @if($wishlists->isEmpty())
                        <div style="border:1px solid #eee; padding:20px; background:#fafafa;">
                            <p>Bạn chưa có sản phẩm yêu thích nào.</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($wishlists as $item)
                                @if(!$item->product)
                                    @continue
                                @endif
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="single-product">
                                        <div class="product-img">
                                            @if($item->product->quantity == 0)
                                                <span class="pro-label new-label" style="position:absolute; background:#e91e63; left:0px ">Tạm hết hàng</span>
                                            @endif
                                            @if(($item->product->pro_pay ?? 0) > 2)
                                                <span class="pro-label new-label" style="position:absolute; background:#eb0505; left:0px; top:28px;">Bán chạy</span>
                                            @endif
                                            @if(($item->product->pro_hot ?? 0) == 1)
                                                <span class="pro-label new-label" style="position:absolute; background:#ff9800; left:0px; top:56px;">Nổi bật</span>
                                            @endif
                                            @if(($item->product->pro_sale ?? 0) > 0 && ($item->product->pro_price ?? 0) > 0)
                                                @php
                                                    $salePercent = round((($item->product->pro_price - $item->product->pro_sale) / $item->product->pro_price) * 100);
                                                @endphp
                                                <span class="pro-label new-label" style="position:absolute; background:#4caf50; right:0px; top:0px;">-{{ $salePercent }}%</span>
                                            @endif
                                            <a href="{{ route('get.detail.product', [$item->product->pro_slug, $item->product->id]) }}">
                                                <img src="{{ $item->product->pro_image ? (strpos($item->product->pro_image, 'http') === 0 ? $item->product->pro_image : asset($item->product->pro_image)) : asset('upload/no-image.jpg') }}" alt="{{$item->product->pro_name}}" style="width: 230px; height: 150px; margin-left: auto; margin-right: auto; display: block;"/>
                                            </a>
                                        </div>
                                        <div class="product-info clearfix text-center">
                                            <div class="fix">
                                                <h4 class="post-title">
                                                    <a href="{{ route('get.detail.product', [$item->product->pro_slug, $item->product->id]) }}">
                                                        {{ $item->product->pro_name }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="fix">
                                                <div class="price-wrap">
                                                    @if($item->product->pro_sale > 0 && $item->product->pro_sale < $item->product->pro_price)
                                                        <span class="price__label">Giá:</span>
                                                        <span class="price--old">{{ number_format($item->product->pro_price, 0, ',', '.') }} Đ</span>
                                                        <span class="price__label">Khuyến mãi:</span>
                                                        <span class="price">{{ number_format($item->product->pro_sale, 0, ',', '.') }} Đ</span>
                                                    @else
                                                        <span class="price__label">Giá:</span>
                                                        <span class="price">{{ number_format($item->product->pro_price, 0, ',', '.') }} Đ</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @php
                                                $totalReviews = $item->product->pro_total_number ?? 0;
                                                $totalStars = $item->product->pro_total ?? 0;
                                                $averageRating = $totalReviews > 0 ? $totalStars / $totalReviews : 0;
                                                $roundedNumber = round($averageRating * 2) / 2;
                                            @endphp
                                            <div class="fix">
                                                <span class="pro-rating" style="color:rgb(221, 172, 12);">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $roundedNumber)
                                                            <i class="zmdi zmdi-star"></i>
                                                        @elseif ($i - 0.5 == $roundedNumber)
                                                            <i class="zmdi zmdi-star-half"></i>
                                                        @endif
                                                    @endfor
                                                    <span style="color: #666; font-size:12px;">({{ $totalReviews }})</span>
                                                </span>
                                            </div>
                                            <div class="fix" style="font-size: 13px; color:#666;">
                                                Số lượng còn lại: {{ $item->product->quantity }}
                                            </div>
                                            <div class="product-action clearfix">
                                                <a href="{{ route('wishlist.remove', $item->product->id) }}" data-bs-toggle="tooltip" data-placement="top" title="Bỏ yêu thích">
                                                    <i class="zmdi zmdi-close"></i>
                                                </a>
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#productModal" title="Quick View">
                                                    <i class="zmdi zmdi-zoom-in"></i>
                                                </a>
                                                <a href="#" data-bs-toggle="tooltip" data-placement="top" title="Compare">
                                                    <i class="zmdi zmdi-refresh"></i>
                                                </a>
                                                <a href="{{ route('cart.add', $item->product->id) }}" data-bs-toggle="tooltip" data-placement="top" title="Add To Cart">
                                                    <i class="zmdi zmdi-shopping-cart-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
