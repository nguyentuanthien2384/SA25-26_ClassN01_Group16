<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title></title>
		<meta name="description" content="Hurst – Furniture Store eCommerce HTML Template is a clean and elegant design – suitable for selling flower, cookery, accessories, fashion, high fashion, accessories, digital, kids, watches, jewelries, shoes, kids, furniture, sports….. It has a fully responsive width adjusts automatically to any screen size or resolution.">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
		<!-- Place favicon.ico in the root directory -->

		<!-- Google Font -->
		<link href='https://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Bree+Serif' rel='stylesheet' type='text/css'>

		<!-- all css here -->
		<!-- bootstrap v3.3.6 css -->
		<link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
		<!-- animate css -->
		<link rel="stylesheet" href="{{asset('css/animate.min.css')}}">
		<!-- jquery-ui.min css -->
		<link rel="stylesheet" href="{{asset('css/jquery-ui.min.css')}}">
		<!-- meanmenu css -->
		<link rel="stylesheet" href="{{asset('css/meanmenu.min.css')}}">
		<link rel="stylesheet" href="{{asset('css/default.css')}}">
		<!-- nivo-slider css -->
		<link rel="stylesheet" href="{{asset('lib/css/nivo-slider.css')}}">
		<link rel="stylesheet" href="{{asset('lib/css/preview.css')}}">
		<!-- slick css -->
		<link rel="stylesheet" href="{{asset('css/slick.min.css')}}">
		<!-- lightbox css -->
		<link rel="stylesheet" href="{{asset('css/lightbox.min.css')}}">
		<!-- material-design-iconic-font css -->
		<link rel="stylesheet" href="{{asset('css/material-design-iconic-font.css')}}">
		<!-- All common css of theme -->

		<!-- style css -->
		<link rel="stylesheet" href="{{asset('style.min.css')}}">
        <!-- shortcode css -->
        <link rel="stylesheet" href="{{asset('css/shortcode.css')}}">
		<!-- responsive css -->
		<link rel="stylesheet" href="{{asset('css/responsive.css')}}">
		<!-- modernizr css -->
		<script src="{{asset('js/vendor/modernizr-3.11.2.min.js')}}"></script>
		<style>
			.wishlist-badge {
				position: relative;
				display: inline-block;
			}
			.wishlist-count {
				position: absolute;
				top: -6px;
				right: -6px;
				background: #c87065;
				color: #fff;
				border-radius: 50%;
				font-size: 10px;
				line-height: 1;
				padding: 3px 5px;
			}
			.price-wrap {
				display: flex;
				gap: 8px;
				align-items: baseline;
				flex-wrap: wrap;
			}
			.price {
				color: #111;
				font-weight: 700;
			}
			.price--old {
				color: #555;
				text-decoration: line-through;
				font-weight: 500;
			}
			.price__label {
				color: #111;
				font-weight: 500;
			}
			.p-card {
				background: #fff;
				border-radius: 14px;
				box-shadow: 0 8px 20px rgba(0,0,0,0.06);
				padding: 12px 12px 16px;
				position: relative;
				height: 100%;
				display: flex;
				flex-direction: column;
			}
			.product-slider .single-product {
				height: 100%;
				display: flex;
			}
			.product-slider .single-product .p-card {
				width: 100%;
			}
			.p-badge {
				position: absolute;
				top: 10px;
				left: 10px;
				background: #e53935;
				color: #fff;
				font-size: 12px;
				font-weight: 700;
				padding: 4px 8px;
				border-radius: 6px;
			}
			.p-badge--installment {
				left: auto;
				right: 10px;
				background: #e3f2fd;
				color: #1e88e5;
				font-weight: 700;
			}
			.p-img {
				display: block;
				width: 100%;
				height: 200px;
				object-fit: contain;
			}
			.p-title {
				font-size: 14px;
				font-weight: 700;
				line-height: 1.4;
				margin: 8px 0 6px;
				color: #111;
				min-height: 40px;
			}
			.p-price {
				font-size: 16px;
				font-weight: 800;
				color: #d32f2f;
			}
			.p-price-old {
				font-size: 13px;
				color: #9e9e9e;
				text-decoration: line-through;
				margin-left: 6px;
			}
			.p-note {
				background: #f5f5f5;
				border-radius: 6px;
				padding: 6px 8px;
				font-size: 12px;
				color: #555;
				margin-top: 6px;
			}
			.p-meta {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-top: 10px;
				font-size: 13px;
				margin-top: auto;
			}
			.p-rating {
				display: flex;
				align-items: center;
				gap: 6px;
				color: #f5a623;
				font-weight: 700;
			}
			.p-wishlist {
				display: flex;
				align-items: center;
				gap: 6px;
				color: #1e88e5;
				font-weight: 600;
			}
			.p-wishlist i {
				font-size: 18px;
			}
			.p-out {
				color: #e91e63;
				font-size: 12px;
				font-weight: 700;
				margin-top: 6px;
				min-height: 18px;
			}
			.pagination-wrap {
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: 18px;
				margin-top: 20px;
			}
			.btn-view-all {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				min-width: 160px;
				padding: 12px 24px;
				background: #4b3b7a;
				color: #fff;
				border-radius: 8px;
				font-weight: 600;
			}
			.pagination-custom {
				display: flex;
				justify-content: center;
				width: 100%;
			}
			.pagination-custom ul {
				display: flex;
				align-items: center;
				gap: 16px;
				list-style: none;
				margin: 0;
				padding: 0;
			}
			.pagination-custom li span,
			.pagination-custom li a {
				color: #5a57c6;
				font-weight: 600;
				text-decoration: none;
				padding: 4px 2px;
			}
			.pagination-custom li.active span {
				color: #4b3b7a;
				border-bottom: 2px solid #4b3b7a;
				padding-bottom: 6px;
			}
			.pagination-custom li.disabled span {
				color: #999;
			}
			.slick-prev,
			.slick-next {
				display: none !important;
			}
		</style>
	</head>
	<body>
		<!-- WRAPPER START -->
		<div class="wrapper">

			<!-- Mobile-header-top Start -->
			<div class="mobile-header-top d-block d-md-none">
				<div class="container">
					<div class="row" style="height: 20px">
						<div class="col-12">
							<!-- header-search-mobile start -->
							<div class="header-search-mobile">
								<div class="table">
									<div class="table-cell">
										<ul>
											<li><a class="search-open" href="#"><i class="zmdi zmdi-search"></i></a></li>
											<li><a href="login.html"><i class="zmdi zmdi-lock"></i></a></li>
											<li><a href="my-account.html"><i class="zmdi zmdi-account"></i></a></li>
											<li>
												<a href="{{ route('wishlist.index') }}" class="wishlist-badge">
													<i class="zmdi zmdi-favorite"></i>
													@if(($wishlistCount ?? 0) > 0)
														<span class="wishlist-count" id="wishlist-count">{{ $wishlistCount }}</span>
													@endif
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<!-- header-search-mobile start -->
						</div>
					</div>
				</div>
			</div>
			<!-- Mobile-header-top End -->
			<!-- HEADER-AREA START -->
			@include('components.header')
			@include('components.button')
			@if(\Session::has('success'))
                <div class="alert alert-success alert-dismissible" style=" position: fixed;right: 200px;top: 40px;left: 60%;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Thành công</strong> {{\Session::get('success')}}
                  </div>
                  @endif
				  @if(\Session::has('warning'))
                  <div class="alert alert-warning alert-dismissible" style=" position: fixed;right: 200px;top: 40px;left: 60%;">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Thất bại</strong> {{\Session::get('warning')}}
                    </div>
                    @endif
                  @if(\Session::has('danger'))
                  <div class="alert alert-danger alert-dismissible" style=" position: fixed;right: 200px;top: 40px;left: 60%;">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Thất bại</strong> {{\Session::get('danger')}}
                    </div>
                    @endif
			@yield('content')
			@if(isset($productHot))
				<div class="product-area pt-80 pb-35">
					<div class="container">
						<div class="row">
							<div class="col-lg-12">
								<div class="section-title text-center">
									<h2 class="title-border">Sản phẩm nổi bật</h2>
								</div>
								<div class="product-slider style-1 arrow-left-right">
									<!-- Single-product start -->
									@foreach( $productHot as $proHot)
									@php
										$salePercent = 0;
										if ($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price) {
											$salePercent = round((($proHot->pro_price - $proHot->pro_sale) / $proHot->pro_price) * 100);
										}
										$totalReviews = $proHot->pro_total_number ?? 0;
										$totalStars = $proHot->pro_total ?? 0;
										$avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
									@endphp
									<div class="single-product">
										<div class="p-card">
											@if($salePercent > 0)
												<span class="p-badge">Giảm {{ $salePercent }}%</span>
											@endif
											<span class="p-badge p-badge--installment">Trả góp 0%</span>
											<a href="{{route('get.detail.product',[$proHot->pro_slug,$proHot->id])}}">
												<img src="{{$proHot->pro_image}}" alt="" class="p-img"/>
											</a>
											<div class="p-title">
												<a href="{{route('get.detail.product',[$proHot->pro_slug,$proHot->id])}}">{{$proHot->pro_name}}</a>
											</div>
											<div>
												@if($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price)
													<span class="p-price">{{ number_format($proHot->pro_sale,0,',','.') }}đ</span>
													<span class="p-price-old">{{ number_format($proHot->pro_price,0,',','.') }}đ</span>
												@else
													<span class="p-price">{{ number_format($proHot->pro_price,0,',','.') }}đ</span>
												@endif
											</div>
											<div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
											<div class="p-out">
												@if($proHot->quantity <= 0)
													Tạm hết hàng
												@else
													&nbsp;
												@endif
											</div>
											<div class="p-meta">
												<div class="p-rating">
													<i class="zmdi zmdi-star"></i>{{ $avgRating }}
												</div>
												<a href="{{ route('wishlist.toggle', $proHot->id) }}" class="p-wishlist js-wishlist-toggle" data-product-id="{{ $proHot->id }}">
													<i class="zmdi {{ in_array($proHot->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline' }}"></i>Yêu thích
												</a>
											</div>
										</div>
									</div>
									@endforeach
								</div>
								@php
									$hotPerPage = (int) request()->get('hot_per_page', 8);
								@endphp
								<div class="pagination-wrap text-center">
									@if($hotPerPage < 24 && $productHot->currentPage() === 1)
										<a class="btn-view-all" href="{{ request()->fullUrlWithQuery(['hot_per_page' => 24, 'hot_page' => 1]) }}">Xem tất cả</a>
									@endif
									{!! $productHot->appends(request()->query())->links('components.pagination') !!}
								</div>
							</div>
						</div>
					</div>
				</div>
			@endif
			<!-- PRODUCT-AREA END -->
			<!-- DISCOUNT-PRODUCT START -->
			@if(isset($bannersales))
			<div class="discount-product-area">
				<div class="container">
					<div class="row">
						<div class="discount-product-slider dots-bottom-right">
							@foreach($bannersales as $bannersale)
							<div class="col-lg-12">
								<div class="discount-product">
									<img src="{{$bannersale->b_image}}" alt="" style="width:100%"/>
									<div class="discount-img-brief">
										<div class="onsale">
											<span class="onsale-text">On Sale</span>
											<span class="onsale-price">{{$bannersale->b_discount}}%</span>
										</div>
										<div class="discount-info">
											<h1 class="text-dark-red d-none d-md-block">Discount {{$bannersale->b_discount}}%</h1>
											<a href="#" class="button-one font-16px style-3 text-uppercase mt-md-5" data-text="Buy now">MUA SẮM NGAY</a>
										</div>
									</div>
								</div>
							</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
			@endif
			@if(isset($productNew))
			<div class="product-area pt-80 pb-35">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Sản phẩm mới </h2>
							</div>
							<div class="product-slider style-1 arrow-left-right">
								<!-- Single-product start -->
								@foreach( $productNew as $proHot)
								@php
									$salePercent = 0;
									if ($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price) {
										$salePercent = round((($proHot->pro_price - $proHot->pro_sale) / $proHot->pro_price) * 100);
									}
									$totalReviews = $proHot->pro_total_number ?? 0;
									$totalStars = $proHot->pro_total ?? 0;
									$avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
								@endphp
								<div class="single-product">
									<div class="p-card">
										@if($salePercent > 0)
											<span class="p-badge">Giảm {{ $salePercent }}%</span>
										@endif
										<span class="p-badge p-badge--installment">Trả góp 0%</span>
										<a href="{{route('get.detail.product',[$proHot->pro_slug,$proHot->id])}}">
											<img src="{{$proHot->pro_image}}" alt="" class="p-img"/>
										</a>
										<div class="p-title">
											<a href="{{route('get.detail.product',[$proHot->pro_slug,$proHot->id])}}">{{$proHot->pro_name}}</a>
										</div>
										<div>
											@if($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price)
												<span class="p-price">{{ number_format($proHot->pro_sale,0,',','.') }}đ</span>
												<span class="p-price-old">{{ number_format($proHot->pro_price,0,',','.') }}đ</span>
											@else
												<span class="p-price">{{ number_format($proHot->pro_price,0,',','.') }}đ</span>
											@endif
										</div>
										<div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
										<div class="p-out">
											@if($proHot->quantity <= 0)
												Tạm hết hàng
											@else
												&nbsp;
											@endif
										</div>
										<div class="p-meta">
											<div class="p-rating">
												<i class="zmdi zmdi-star"></i>{{ $avgRating }}
											</div>
											<a href="{{ route('wishlist.toggle', $proHot->id) }}" class="p-wishlist js-wishlist-toggle" data-product-id="{{ $proHot->id }}">
												<i class="zmdi {{ in_array($proHot->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline' }}"></i>Yêu thích
											</a>
										</div>
									</div>
								</div>
								@endforeach
							</div>
							@php
								$newPerPage = (int) request()->get('new_per_page', 8);
							@endphp
							<div class="pagination-wrap text-center">
								@if($newPerPage < 24 && $productNew->currentPage() === 1)
									<a class="btn-view-all" href="{{ request()->fullUrlWithQuery(['new_per_page' => 24, 'new_page' => 1]) }}">Xem tất cả</a>
								@endif
								{!! $productNew->appends(request()->query())->links('components.pagination') !!}
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif
			@if(isset($productSelling))
			<div class="product-area pt-80 pb-35">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Sản phẩm bán nhiều nhất</h2>
							</div>
							<div class="product-slider style-1 arrow-left-right">
								<!-- Single-product start -->
								@foreach( $productSelling as $proHot)
								@php
									$salePercent = 0;
									if ($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price) {
										$salePercent = round((($proHot->pro_price - $proHot->pro_sale) / $proHot->pro_price) * 100);
									}
									$totalReviews = $proHot->pro_total_number ?? 0;
									$totalStars = $proHot->pro_total ?? 0;
									$avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
								@endphp
								<div class="single-product">
									<div class="p-card">
										@if($salePercent > 0)
											<span class="p-badge">Giảm {{ $salePercent }}%</span>
										@endif
										@if($proHot->pro_pay > 2)
											<span class="p-badge" style="top:38px;background:#eb0505;">Bán chạy</span>
										@endif
										<span class="p-badge p-badge--installment">Trả góp 0%</span>
										<a href="{{route('get.detail.product',[$proHot->pro_slug,$proHot->id])}}">
											<img src="{{$proHot->pro_image}}" alt="" class="p-img"/>
										</a>
										<div class="p-title">
											<a href="{{route('get.detail.product',[$proHot->pro_slug,$proHot->id])}}">{{$proHot->pro_name}}</a>
										</div>
										<div>
											@if($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price)
												<span class="p-price">{{ number_format($proHot->pro_sale,0,',','.') }}đ</span>
												<span class="p-price-old">{{ number_format($proHot->pro_price,0,',','.') }}đ</span>
											@else
												<span class="p-price">{{ number_format($proHot->pro_price,0,',','.') }}đ</span>
											@endif
										</div>
										<div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
										<div class="p-out">
											@if($proHot->quantity <= 0)
												Tạm hết hàng
											@else
												&nbsp;
											@endif
										</div>
										<div class="p-meta">
											<div class="p-rating">
												<i class="zmdi zmdi-star"></i>{{ $avgRating }}
											</div>
											<a href="{{ route('wishlist.toggle', $proHot->id) }}" class="p-wishlist js-wishlist-toggle" data-product-id="{{ $proHot->id }}">
												<i class="zmdi {{ in_array($proHot->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline' }}"></i>Yêu thích
											</a>
										</div>
									</div>
								</div>
								@endforeach
							</div>
							@php
								$sellingPerPage = (int) request()->get('selling_per_page', 8);
							@endphp
							<div class="pagination-wrap text-center">
								@if($sellingPerPage < 24 && $productSelling->currentPage() === 1)
									<a class="btn-view-all" href="{{ request()->fullUrlWithQuery(['selling_per_page' => 24, 'selling_page' => 1]) }}">Xem tất cả</a>
								@endif
								{!! $productSelling->appends(request()->query())->links('components.pagination') !!}
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

			@if(isset($articleNews))
			<div class="product-area pt-80 pb-35">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Tin tức nổi bật</h2>
							</div>
							@if($articleNews->count() === 0)
								<div class="product-info clearfix" style="text-align:center; padding: 20px 0;">
									<p>Chưa có tin tức nổi bật.</p>
								</div>
							@else
							<div class="product-slider style-1 arrow-left-right">
								<!-- Single-product start -->
								@foreach( $articleNews as $articleNew)
								@php
									$salePercent = 0;
									if ($articleNew->pro_sale > 0 && $articleNew->pro_sale < $articleNew->pro_price) {
										$salePercent = round((($articleNew->pro_price - $articleNew->pro_sale) / $articleNew->pro_price) * 100);
									}
									$totalReviews = $articleNew->pro_total_number ?? 0;
									$totalStars = $articleNew->pro_total ?? 0;
									$avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
								@endphp
								<div class="single-product">
									<div class="p-card">
										@if($salePercent > 0)
											<span class="p-badge">Giảm {{ $salePercent }}%</span>
										@endif
										<span class="p-badge p-badge--installment">Trả góp 0%</span>
										<a href="{{route('get.detail.product',[$articleNew->pro_slug, $articleNew->id])}}">
											<img src="{{$articleNew->pro_image}}" alt="" class="p-img"/>
										</a>
										<div class="p-title">
											<a href="{{route('get.detail.product',[$articleNew->pro_slug, $articleNew->id])}}">{{$articleNew->pro_name}}</a>
										</div>
										<div>
											@if($articleNew->pro_sale > 0 && $articleNew->pro_sale < $articleNew->pro_price)
												<span class="p-price">{{ number_format($articleNew->pro_sale,0,',','.') }}đ</span>
												<span class="p-price-old">{{ number_format($articleNew->pro_price,0,',','.') }}đ</span>
											@else
												<span class="p-price">{{ number_format($articleNew->pro_price,0,',','.') }}đ</span>
											@endif
										</div>
										<div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
										<div class="p-out">
											@if($articleNew->quantity <= 0)
												Tạm hết hàng
											@else
												&nbsp;
											@endif
										</div>
										<div class="p-meta">
											<div class="p-rating">
												<i class="zmdi zmdi-star"></i>{{ $avgRating }}
											</div>
											<a href="{{ route('wishlist.toggle', $articleNew->id) }}" class="p-wishlist js-wishlist-toggle" data-product-id="{{ $articleNew->id }}">
												<i class="zmdi {{ in_array($articleNew->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline' }}"></i>Yêu thích
											</a>
										</div>
									</div>
								</div>
								@endforeach
							</div>
							@php
								$newsPerPage = (int) request()->get('news_per_page', 8);
							@endphp
							<div class="pagination-wrap text-center">
								@if($newsPerPage < 24 && $articleNews->currentPage() === 1)
									<a class="btn-view-all" href="{{ request()->fullUrlWithQuery(['news_per_page' => 24, 'news_page' => 1]) }}">Xem tất cả</a>
								@endif
								{!! $articleNews->appends(request()->query())->links('components.pagination') !!}
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
			@endif
            @include('components.footer')
		</div>
		<!-- WRAPPER END -->

		<!-- all js here -->
		<!-- jquery latest version -->
		<script src="{{asset('js/vendor/jquery-3.6.0.min.js')}}"></script>
		<script src="{{asset('js/vendor/jquery-migrate-3.3.2.min.js')}}"></script>
		<!-- bootstrap js -->
		<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
		<!-- jquery.meanmenu js -->
		<script src="{{asset('js/jquery.meanmenu.js')}}"></script>
		<!-- slick.min js -->
		<script src="{{asset('js/slick.min.js')}}"></script>
		<!-- jquery.treeview js -->
		<script src="{{asset('js/jquery.treeview.js')}}"></script>
		<!-- lightbox.min js -->
		<script src="{{asset('js/lightbox.min.js')}}"></script>
		<!-- jquery-ui js -->
		<script src="{{asset('js/jquery-ui.min.js')}}"></script>
		<!-- jquery.nivo.slider js -->
		<script src="{{asset('lib/js/jquery.nivo.slider.js')}}"></script>
		<script src="{{asset('lib/home.js')}}"></script>
		<!-- jquery.nicescroll.min js -->
		<script src="{{asset('js/jquery.nicescroll.min.js')}}"></script>
		<!-- countdon.min js -->
		<script src="{{asset('js/countdon.min.js')}}"></script>
		<!-- wow js -->
		<script src="{{asset('js/wow.min.js')}}"></script>
		<!-- plugins js -->
		<script src="{{asset('js/plugins.js')}}"></script>
		<!-- main js -->
		<script src="{{asset('js/main.min.js')}}"></script>

		<script>
			setTimeout(function() {
				$('.alert').fadeOut('fast');
			}, 5000); // 5 giây

		</script>
		<script>
			(function () {
				var isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
				if (!isLoggedIn) return;

				function updateWishlistCount(count) {
					var badge = document.getElementById('wishlist-count');
					if (!badge) {
						var anchor = document.querySelector('.wishlist-badge');
						if (anchor) {
							badge = document.createElement('span');
							badge.id = 'wishlist-count';
							badge.className = 'wishlist-count';
							anchor.appendChild(badge);
						}
					}
					if (badge) {
						if (count > 0) {
							badge.textContent = count;
							badge.style.display = 'inline-block';
						} else {
							badge.textContent = '';
							badge.style.display = 'none';
						}
					}
				}

				$(document).on('click', '.js-wishlist-toggle', function (e) {
					e.preventDefault();
					var $link = $(this);
					var url = $link.attr('href');
					var token = $('meta[name="csrf-token"]').attr('content');

					$.ajax({
						method: 'POST',
						url: url,
						headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
						success: function (res) {
							var $icon = $link.find('i.zmdi');
							if (res.status === 'added') {
								$icon.removeClass('zmdi-favorite-outline').addClass('zmdi-favorite');
							} else if (res.status === 'removed') {
								$icon.removeClass('zmdi-favorite').addClass('zmdi-favorite-outline');
							}
							if (typeof res.count !== 'undefined') {
								updateWishlistCount(res.count);
							}
						}
					});
				});
			})();
		</script>
	</body>
</html>
{{-- https://sharecode.vn/source-code/code-website-thuong-mai-dien-tu-php-laravel-30701.htm#anh-demo --}}
