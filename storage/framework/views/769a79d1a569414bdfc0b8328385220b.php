<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title></title>
		<meta name="description" content="Hurst – Furniture Store eCommerce HTML Template is a clean and elegant design – suitable for selling flower, cookery, accessories, fashion, high fashion, accessories, digital, kids, watches, jewelries, shoes, kids, furniture, sports….. It has a fully responsive width adjusts automatically to any screen size or resolution.">
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('img/favicon.ico')); ?>">

		<!-- ⚡ OPTIMIZED: Preload critical CSS + defer Google Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href='https://fonts.googleapis.com/css?family=Lato:400,700,900&display=swap' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Bree+Serif&display=swap' rel='stylesheet' type='text/css'>

		<!-- Critical CSS (above-the-fold) -->
		<link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
		<!-- animate css -->
		<link rel="stylesheet" href="<?php echo e(asset('css/animate.min.css')); ?>">
		<!-- jquery-ui.min css -->
		<link rel="stylesheet" href="<?php echo e(asset('css/jquery-ui.min.css')); ?>">
		<!-- meanmenu css -->
		<link rel="stylesheet" href="<?php echo e(asset('css/meanmenu.min.css')); ?>">
		<link rel="stylesheet" href="<?php echo e(asset('css/default.css')); ?>">
		<!-- nivo-slider css -->
		<link rel="stylesheet" href="<?php echo e(asset('lib/css/nivo-slider.css')); ?>">
		<link rel="stylesheet" href="<?php echo e(asset('lib/css/preview.css')); ?>">
		<!-- slick css -->
		<link rel="stylesheet" href="<?php echo e(asset('css/slick.min.css')); ?>">
		<!-- lightbox css -->
		<link rel="stylesheet" href="<?php echo e(asset('css/lightbox.min.css')); ?>">
		<!-- material-design-iconic-font css -->
		<link rel="stylesheet" href="<?php echo e(asset('css/material-design-iconic-font.css')); ?>">
		<!-- All common css of theme -->

		<!-- style css -->
		<link rel="stylesheet" href="<?php echo e(asset('style.min.css')); ?>">
        <!-- shortcode css -->
        <link rel="stylesheet" href="<?php echo e(asset('css/shortcode.css')); ?>">
		<!-- responsive css -->
		<link rel="stylesheet" href="<?php echo e(asset('css/responsive.css')); ?>">
		<!-- Fast Pagination CSS - Load nhanh như chớp -->
		<link rel="stylesheet" href="<?php echo e(asset('css/fast-pagination.css')); ?>">
		<!-- modernizr css -->
		<script src="<?php echo e(asset('js/vendor/modernizr-3.11.2.min.js')); ?>"></script>
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
			.widget-categories {
				background: #fff;
				border: 1px solid #eef0f5;
				border-radius: 12px;
				padding: 16px;
				box-shadow: 0 10px 24px rgba(0, 0, 0, 0.04);
			}
			.widget-categories .widget-title h4 {
				font-size: 14px;
				font-weight: 700;
				color: #1f2a44;
				margin-bottom: 12px;
			}
			.product-cat ul {
				display: flex;
				flex-wrap: wrap;
				gap: 8px;
				list-style: none;
				margin: 0;
				padding: 0;
			}
			.product-cat li {
				margin: 0;
			}
			.product-cat a {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				padding: 8px 12px;
				border-radius: 999px;
				border: 1px solid #e5e8f0;
				background: #f7f8fb;
				color: #27304a;
				font-size: 12px;
				font-weight: 600;
				transition: all 0.2s ease;
				white-space: nowrap;
			}
			.product-cat a:hover {
				border-color: #e30019;
				color: #e30019;
				background: #fff1f2;
			}
			.product-cat a.active {
				border-color: #e30019;
				background: #e30019;
				color: #fff;
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
				gap: 14px;
				list-style: none;
				margin: 0;
				padding: 0;
			}
			.pagination-custom li span,
			.pagination-custom li a {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				min-width: 24px;
				height: 24px;
				padding: 0 4px;
				border: none;
				border-radius: 0;
				background: transparent;
				color: #3a476a;
				font-weight: 500;
				text-decoration: none;
			}
			.pagination-custom li a:hover {
				color: #2f3f75;
			}
			.pagination-custom li.active span {
				color: #2f3f75;
				border-bottom: 2px solid #4b5bff;
				padding-bottom: 4px;
			}
			.pagination-custom li.disabled span {
				color: #c0c6d4;
			}
			.pagination-custom .pagination-prev span,
			.pagination-custom .pagination-prev a,
			.pagination-custom .pagination-next span,
			.pagination-custom .pagination-next a {
				width: 34px;
				height: 34px;
				min-width: 34px;
				border-radius: 50%;
				background: #eaf1ff;
				color: #6a7bb6;
				font-size: 16px;
			}
			.pagination-custom .pagination-prev a:hover,
			.pagination-custom .pagination-next a:hover {
				background: #dfe9ff;
			}
			.js-ajax-section {
				transition: opacity 180ms ease;
				position: relative;
			}
			.js-ajax-section.is-loading {
				opacity: 0.6;
			}
			.js-ajax-section.is-fade-in {
				opacity: 0;
			}
			.js-ajax-section.is-fixed-height {
				overflow: hidden;
			}
			.js-ajax-section .ajax-overlay {
				position: absolute;
				inset: 0;
				background: rgba(255, 255, 255, 0.75);
				display: flex;
				align-items: center;
				justify-content: center;
				z-index: 2;
				backdrop-filter: blur(2px);
			}
			.js-ajax-section .ajax-skeleton-grid {
				display: grid;
				grid-template-columns: repeat(4, minmax(0, 1fr));
				gap: 20px;
				width: 100%;
				max-width: 1200px;
				padding: 0 15px;
			}
			@media (max-width: 1199px) {
				.js-ajax-section .ajax-skeleton-grid {
					grid-template-columns: repeat(3, minmax(0, 1fr));
				}
			}
			@media (max-width: 991px) {
				.js-ajax-section .ajax-skeleton-grid {
					grid-template-columns: repeat(2, minmax(0, 1fr));
				}
			}
			@media (max-width: 767px) {
				.js-ajax-section .ajax-skeleton-grid {
					grid-template-columns: repeat(1, minmax(0, 1fr));
				}
			}
			.js-ajax-section .ajax-skeleton-card {
				background: #fff;
				border-radius: 14px;
				box-shadow: 0 8px 20px rgba(0,0,0,0.06);
				padding: 12px 12px 16px;
			}
			.js-ajax-section .skeleton-block {
				position: relative;
				overflow: hidden;
				background: #edf1f7;
				border-radius: 8px;
			}
			.js-ajax-section .skeleton-block::after {
				content: '';
				position: absolute;
				inset: 0;
				background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0) 100%);
				transform: translateX(-100%);
				animation: shimmer 1.1s infinite;
			}
			.js-ajax-section .skeleton-img {
				height: 180px;
				border-radius: 10px;
				margin-bottom: 10px;
			}
			.js-ajax-section .skeleton-line {
				height: 12px;
				margin-bottom: 8px;
			}
			.js-ajax-section .skeleton-line.short {
				width: 60%;
			}
			.js-ajax-section .skeleton-line.tiny {
				width: 40%;
			}
			.js-ajax-section .skeleton-line.price {
				height: 14px;
				width: 45%;
			}
			.js-ajax-section .skeleton-line.meta {
				height: 10px;
				width: 30%;
				margin-bottom: 0;
			}
			@keyframes shimmer {
				100% { transform: translateX(100%); }
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
												<a href="<?php echo e(route('wishlist.index')); ?>" class="wishlist-badge">
													<i class="zmdi zmdi-favorite"></i>
													<?php if(($wishlistCount ?? 0) > 0): ?>
														<span class="wishlist-count" id="wishlist-count"><?php echo e($wishlistCount); ?></span>
													<?php endif; ?>
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
			<?php echo $__env->make('components.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php echo $__env->make('components.button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php if(\Session::has('success')): ?>
                <div class="alert alert-success alert-dismissible" style=" position: fixed;right: 200px;top: 40px;left: 60%;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Thành công</strong> <?php echo e(\Session::get('success')); ?>

                  </div>
                  <?php endif; ?>
				  <?php if(\Session::has('warning')): ?>
                  <div class="alert alert-warning alert-dismissible" style=" position: fixed;right: 200px;top: 40px;left: 60%;">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Thất bại</strong> <?php echo e(\Session::get('warning')); ?>

                    </div>
                    <?php endif; ?>
                  <?php if(\Session::has('danger')): ?>
                  <div class="alert alert-danger alert-dismissible" style=" position: fixed;right: 200px;top: 40px;left: 60%;">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Thất bại</strong> <?php echo e(\Session::get('danger')); ?>

                    </div>
                    <?php endif; ?>
			<?php echo $__env->yieldContent('content'); ?>
			<?php if(isset($productHot)): ?>
				<div class="product-area pt-80 pb-35 js-ajax-section" data-section="hot">
					<div class="container">
						<div class="row">
							<div class="col-lg-12">
								<div class="section-title text-center">
									<h2 class="title-border">Sản phẩm nổi bật</h2>
								</div>
								<div class="row">
									<!-- Single-product start -->
									<?php $__currentLoopData = $productHot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proHot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php
										$salePercent = 0;
										if ($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price) {
											$salePercent = round((($proHot->pro_price - $proHot->pro_sale) / $proHot->pro_price) * 100);
										}
										$totalReviews = $proHot->pro_total_number ?? 0;
										$totalStars = $proHot->pro_total ?? 0;
										$avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
									?>
									<div class="col-lg-3 col-md-4 col-sm-6 mb-30">
										<div class="p-card">
											<?php if($salePercent > 0): ?>
												<span class="p-badge">Giảm <?php echo e($salePercent); ?>%</span>
											<?php endif; ?>
											<span class="p-badge p-badge--installment">Trả góp 0%</span>
											<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>">
												<img src="<?php echo e($proHot->pro_image ? (strpos($proHot->pro_image, 'http') === 0 ? $proHot->pro_image : asset($proHot->pro_image)) : asset('upload/no-image.jpg')); ?>" alt="<?php echo e($proHot->pro_name); ?>" class="p-img" loading="lazy"/>
											</a>
											<div class="p-title">
												<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><?php echo e($proHot->pro_name); ?></a>
											</div>
											<div>
												<?php if($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price): ?>
													<span class="p-price"><?php echo e(number_format($proHot->pro_sale,0,',','.')); ?>đ</span>
													<span class="p-price-old"><?php echo e(number_format($proHot->pro_price,0,',','.')); ?>đ</span>
												<?php else: ?>
													<span class="p-price"><?php echo e(number_format($proHot->pro_price,0,',','.')); ?>đ</span>
												<?php endif; ?>
											</div>
											<div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
											<div class="p-out">
												<?php if($proHot->quantity <= 0): ?>
													Tạm hết hàng
												<?php else: ?>
													&nbsp;
												<?php endif; ?>
											</div>
											<div class="p-meta">
												<div class="p-rating">
													<i class="zmdi zmdi-star"></i><?php echo e($avgRating); ?>

												</div>
												<a href="<?php echo e(route('wishlist.toggle', $proHot->id)); ?>" class="p-wishlist js-wishlist-toggle" data-product-id="<?php echo e($proHot->id); ?>">
													<i class="zmdi <?php echo e(in_array($proHot->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline'); ?>"></i>Yêu thích
												</a>
											</div>
										</div>
									</div>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</div>
							<div class="pagination-wrap text-center">
								<?php if(method_exists($productHot, 'appends')): ?>
									<?php echo $productHot->appends(request()->query())->links('components.pagination'); ?>

								<?php endif; ?>
							</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<!-- PRODUCT-AREA END -->
			<!-- DISCOUNT-PRODUCT START -->
			<?php if(isset($bannersales)): ?>
			<div class="discount-product-area">
				<div class="container">
					<div class="row">
						<div class="discount-product-slider dots-bottom-right">
							<?php $__currentLoopData = $bannersales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bannersale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="col-lg-12">
								<div class="discount-product">
									<img src="<?php echo e($bannersale->b_image); ?>" alt="" style="width:100%"/>
									<div class="discount-img-brief">
										<div class="onsale">
											<span class="onsale-text">On Sale</span>
											<span class="onsale-price"><?php echo e($bannersale->b_discount); ?>%</span>
										</div>
										<div class="discount-info">
											<h1 class="text-dark-red d-none d-md-block">Discount <?php echo e($bannersale->b_discount); ?>%</h1>
											<a href="#" class="button-one font-16px style-3 text-uppercase mt-md-5" data-text="Buy now">MUA SẮM NGAY</a>
										</div>
									</div>
								</div>
							</div>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($productNew)): ?>
			<div class="product-area pt-80 pb-35 js-ajax-section" data-section="new">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Sản phẩm mới </h2>
							</div>
							<div class="row">
								<!-- Single-product start -->
								<?php $__currentLoopData = $productNew; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proHot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
									$salePercent = 0;
									if ($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price) {
										$salePercent = round((($proHot->pro_price - $proHot->pro_sale) / $proHot->pro_price) * 100);
									}
									$totalReviews = $proHot->pro_total_number ?? 0;
									$totalStars = $proHot->pro_total ?? 0;
									$avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
								?>
								<div class="col-lg-3 col-md-4 col-sm-6 mb-30">
									<div class="p-card">
										<?php if($salePercent > 0): ?>
											<span class="p-badge">Giảm <?php echo e($salePercent); ?>%</span>
										<?php endif; ?>
										<span class="p-badge p-badge--installment">Trả góp 0%</span>
										<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>">
											<img src="<?php echo e($proHot->pro_image ? (strpos($proHot->pro_image, 'http') === 0 ? $proHot->pro_image : asset($proHot->pro_image)) : asset('upload/no-image.jpg')); ?>" alt="<?php echo e($proHot->pro_name); ?>" class="p-img" loading="lazy"/>
										</a>
										<div class="p-title">
											<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><?php echo e($proHot->pro_name); ?></a>
										</div>
										<div>
											<?php if($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price): ?>
												<span class="p-price"><?php echo e(number_format($proHot->pro_sale,0,',','.')); ?>đ</span>
												<span class="p-price-old"><?php echo e(number_format($proHot->pro_price,0,',','.')); ?>đ</span>
											<?php else: ?>
												<span class="p-price"><?php echo e(number_format($proHot->pro_price,0,',','.')); ?>đ</span>
											<?php endif; ?>
										</div>
										<div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
										<div class="p-out">
											<?php if($proHot->quantity <= 0): ?>
												Tạm hết hàng
											<?php else: ?>
												&nbsp;
											<?php endif; ?>
										</div>
										<div class="p-meta">
											<div class="p-rating">
												<i class="zmdi zmdi-star"></i><?php echo e($avgRating); ?>

											</div>
											<a href="<?php echo e(route('wishlist.toggle', $proHot->id)); ?>" class="p-wishlist js-wishlist-toggle" data-product-id="<?php echo e($proHot->id); ?>">
												<i class="zmdi <?php echo e(in_array($proHot->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline'); ?>"></i>Yêu thích
											</a>
										</div>
									</div>
								</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
							<div class="pagination-wrap text-center">
								<?php if(method_exists($productNew, 'appends')): ?>
									<?php echo $productNew->appends(request()->query())->links('components.pagination'); ?>

								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($productSelling)): ?>
			<div class="product-area pt-80 pb-35 js-ajax-section" data-section="selling">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Sản phẩm bán nhiều nhất</h2>
							</div>
							<div class="row">
								<!-- Single-product start -->
								<?php $__currentLoopData = $productSelling; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proHot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
									$salePercent = 0;
									if ($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price) {
										$salePercent = round((($proHot->pro_price - $proHot->pro_sale) / $proHot->pro_price) * 100);
									}
									$totalReviews = $proHot->pro_total_number ?? 0;
									$totalStars = $proHot->pro_total ?? 0;
									$avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
								?>
								<div class="col-lg-3 col-md-4 col-sm-6 mb-30">
									<div class="p-card">
										<?php if($salePercent > 0): ?>
											<span class="p-badge">Giảm <?php echo e($salePercent); ?>%</span>
										<?php endif; ?>
										<?php if($proHot->pro_pay > 2): ?>
											<span class="p-badge" style="top:38px;background:#eb0505;">Bán chạy</span>
										<?php endif; ?>
										<span class="p-badge p-badge--installment">Trả góp 0%</span>
										<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>">
											<img src="<?php echo e($proHot->pro_image ? (strpos($proHot->pro_image, 'http') === 0 ? $proHot->pro_image : asset($proHot->pro_image)) : asset('upload/no-image.jpg')); ?>" alt="<?php echo e($proHot->pro_name); ?>" class="p-img" loading="lazy"/>
										</a>
										<div class="p-title">
											<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><?php echo e($proHot->pro_name); ?></a>
										</div>
										<div>
											<?php if($proHot->pro_sale > 0 && $proHot->pro_sale < $proHot->pro_price): ?>
												<span class="p-price"><?php echo e(number_format($proHot->pro_sale,0,',','.')); ?>đ</span>
												<span class="p-price-old"><?php echo e(number_format($proHot->pro_price,0,',','.')); ?>đ</span>
											<?php else: ?>
												<span class="p-price"><?php echo e(number_format($proHot->pro_price,0,',','.')); ?>đ</span>
											<?php endif; ?>
										</div>
										<div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
										<div class="p-out">
											<?php if($proHot->quantity <= 0): ?>
												Tạm hết hàng
											<?php else: ?>
												&nbsp;
											<?php endif; ?>
										</div>
										<div class="p-meta">
											<div class="p-rating">
												<i class="zmdi zmdi-star"></i><?php echo e($avgRating); ?>

											</div>
											<a href="<?php echo e(route('wishlist.toggle', $proHot->id)); ?>" class="p-wishlist js-wishlist-toggle" data-product-id="<?php echo e($proHot->id); ?>">
												<i class="zmdi <?php echo e(in_array($proHot->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline'); ?>"></i>Yêu thích
											</a>
										</div>
									</div>
								</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
							<div class="pagination-wrap text-center">
								<?php if(method_exists($productSelling, 'appends')): ?>
									<?php echo $productSelling->appends(request()->query())->links('components.pagination'); ?>

								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if(isset($articleNews)): ?>
			<div class="product-area pt-80 pb-35 js-ajax-section" data-section="news">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Tin tức nổi bật</h2>
							</div>
							<?php if($articleNews->count() === 0): ?>
								<div class="product-info clearfix" style="text-align:center; padding: 20px 0;">
									<p>Chưa có tin tức nổi bật.</p>
								</div>
							<?php else: ?>
							<div class="row">
								<!-- Single-product start -->
								<?php $__currentLoopData = $articleNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $articleNew): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
									$salePercent = 0;
									if ($articleNew->pro_sale > 0 && $articleNew->pro_sale < $articleNew->pro_price) {
										$salePercent = round((($articleNew->pro_price - $articleNew->pro_sale) / $articleNew->pro_price) * 100);
									}
									$totalReviews = $articleNew->pro_total_number ?? 0;
									$totalStars = $articleNew->pro_total ?? 0;
									$avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
								?>
								<div class="col-lg-3 col-md-4 col-sm-6 mb-30">
									<div class="p-card">
										<?php if($salePercent > 0): ?>
											<span class="p-badge">Giảm <?php echo e($salePercent); ?>%</span>
										<?php endif; ?>
										<span class="p-badge p-badge--installment">Trả góp 0%</span>
										<a href="<?php echo e(route('get.detail.product',[$articleNew->pro_slug, $articleNew->id])); ?>">
											<img src="<?php echo e($articleNew->pro_image ? (strpos($articleNew->pro_image, 'http') === 0 ? $articleNew->pro_image : asset($articleNew->pro_image)) : asset('upload/no-image.jpg')); ?>" alt="<?php echo e($articleNew->pro_name); ?>" class="p-img" loading="lazy"/>
										</a>
										<div class="p-title">
											<a href="<?php echo e(route('get.detail.product',[$articleNew->pro_slug, $articleNew->id])); ?>"><?php echo e($articleNew->pro_name); ?></a>
										</div>
										<div>
											<?php if($articleNew->pro_sale > 0 && $articleNew->pro_sale < $articleNew->pro_price): ?>
												<span class="p-price"><?php echo e(number_format($articleNew->pro_sale,0,',','.')); ?>đ</span>
												<span class="p-price-old"><?php echo e(number_format($articleNew->pro_price,0,',','.')); ?>đ</span>
											<?php else: ?>
												<span class="p-price"><?php echo e(number_format($articleNew->pro_price,0,',','.')); ?>đ</span>
											<?php endif; ?>
										</div>
										<div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
										<div class="p-out">
											<?php if($articleNew->quantity <= 0): ?>
												Tạm hết hàng
											<?php else: ?>
												&nbsp;
											<?php endif; ?>
										</div>
										<div class="p-meta">
											<div class="p-rating">
												<i class="zmdi zmdi-star"></i><?php echo e($avgRating); ?>

											</div>
											<a href="<?php echo e(route('wishlist.toggle', $articleNew->id)); ?>" class="p-wishlist js-wishlist-toggle" data-product-id="<?php echo e($articleNew->id); ?>">
												<i class="zmdi <?php echo e(in_array($articleNew->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline'); ?>"></i>Yêu thích
											</a>
										</div>
									</div>
								</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
							<div class="pagination-wrap text-center">
								<?php if(method_exists($articleNews, 'appends')): ?>
									<?php echo $articleNews->appends(request()->query())->links('components.pagination'); ?>

								<?php endif; ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
            <?php echo $__env->make('components.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		</div>
		<!-- WRAPPER END -->

		<!-- ⚡ OPTIMIZED: Critical JS first, defer non-critical -->
		<!-- jquery (critical - needed for everything) -->
		<script src="<?php echo e(asset('js/vendor/jquery-3.6.0.min.js')); ?>"></script>
		<script src="<?php echo e(asset('js/vendor/jquery-migrate-3.3.2.min.js')); ?>"></script>
		<!-- bootstrap js (critical - layout) -->
		<script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>
		<!-- slick slider (critical for product display) -->
		<script src="<?php echo e(asset('js/slick.min.js')); ?>"></script>
		<!-- Deferred JS (non-critical, load after page render) -->
		<script src="<?php echo e(asset('js/jquery.meanmenu.js')); ?>" defer></script>
		<script src="<?php echo e(asset('js/jquery.treeview.js')); ?>" defer></script>
		<script src="<?php echo e(asset('js/lightbox.min.js')); ?>" defer></script>
		<script src="<?php echo e(asset('js/jquery-ui.min.js')); ?>" defer></script>
		<script src="<?php echo e(asset('lib/js/jquery.nivo.slider.js')); ?>" defer></script>
		<script src="<?php echo e(asset('lib/home.js')); ?>" defer></script>
		<script src="<?php echo e(asset('js/jquery.nicescroll.min.js')); ?>" defer></script>
		<script src="<?php echo e(asset('js/countdon.min.js')); ?>" defer></script>
		<script src="<?php echo e(asset('js/wow.min.js')); ?>" defer></script>
		<!-- plugins js -->
		<script src="<?php echo e(asset('js/plugins.js')); ?>"></script>
		<!-- main js -->
		<script src="<?php echo e(asset('js/main.min.js')); ?>"></script>

		<script>
			setTimeout(function() {
				$('.alert').fadeOut('fast');
			}, 5000); // 5 giây

		</script>
		<script>
			(function () {
				var isLoggedIn = <?php echo e(Auth::check() ? 'true' : 'false'); ?>;
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
		<script>
			$(document).on('click', 'a[data-confirm], a[href*="delete"], a[href*="remove"]', function (e) {
				var message = $(this).data('confirm') || 'Bạn có chắc muốn xoá?';
				if (!confirm(message)) {
					e.preventDefault();
				}
			});
		</script>
		<script>
			(function () {
				function buildSkeletonGrid(count) {
					var items = [];
					for (var i = 0; i < count; i++) {
						items.push(
							'<div class="ajax-skeleton-card">' +
								'<div class="skeleton-block skeleton-img"></div>' +
								'<div class="skeleton-block skeleton-line"></div>' +
								'<div class="skeleton-block skeleton-line short"></div>' +
								'<div class="skeleton-block skeleton-line price"></div>' +
								'<div class="skeleton-block skeleton-line meta"></div>' +
							'</div>'
						);
					}
					return '<div class="ajax-overlay"><div class="ajax-skeleton-grid">' + items.join('') + '</div></div>';
				}

				function initProductSlider($scope) {
					var $sliders = $scope.find('.product-slider');
					if (!$sliders.length || typeof $.fn.slick !== 'function') return;
					$sliders.each(function () {
						var $slider = $(this);
						if ($slider.hasClass('slick-initialized')) {
							$slider.slick('unslick');
						}
						$slider.slick({
							speed: 300,
							slidesToShow: 4,
							slidesToScroll: 1,
							prevArrow: '<button type="button" class="slick-prev">p<br />r<br />e<br />v</button>',
							nextArrow: '<button type="button" class="slick-next">n<br />e<br />x<br />t</button>',
							responsive: [
								{ breakpoint: 1200, settings: { slidesToShow: 3 } },
								{ breakpoint: 992, settings: { slidesToShow: 2 } },
								{ breakpoint: 768, settings: { slidesToShow: 1 } }
							]
						});
					});
				}

				function loadSection($trigger) {
					var $section = $trigger.closest('.js-ajax-section');
					if (!$section.length) return;

					var url = $trigger.attr('href');
					if (!url) return;

					var sectionId = $section.data('section');
					var fixedHeight = $section.outerHeight();
					$section.addClass('is-loading is-fixed-height').css('min-height', fixedHeight);
					if (!$section.find('.ajax-overlay').length) {
						$section.append(buildSkeletonGrid(4));
					}

					$.get(url, function (html) {
						var $newSection = $(html)
							.find('.js-ajax-section[data-section="' + sectionId + '"]')
							.first();

						if (!$newSection.length) {
							window.location.href = url;
							return;
						}

						$newSection.addClass('is-fade-in');
						$newSection.addClass('is-fade-in').css('min-height', fixedHeight);
						$section.replaceWith($newSection);
						initProductSlider($newSection);
						if (window.history && window.history.pushState) {
							window.history.pushState({ section: sectionId }, '', url);
						}
						requestAnimationFrame(function () {
							$newSection.removeClass('is-fade-in is-fixed-height').css('min-height', '');
						});
					}).fail(function () {
						window.location.href = url;
					});
				}

				$(document).on('click', '.js-ajax-section .btn-view-all, .js-ajax-section .pagination-custom a', function (e) {
					e.preventDefault();
					loadSection($(this));
				});
			})();
		</script>
		
		<!-- Fast Pagination JS - AJAX super nhanh -->
		<script src="<?php echo e(asset('js/fast-pagination.js')); ?>"></script>
		<script>
			// Initialize Fast Pagination cho các sections
			document.addEventListener('DOMContentLoaded', function() {
				// Khởi tạo cho sản phẩm nổi bật
				if (document.querySelector('.products-hot-container')) {
					window.productsHotPagination = new FastPagination({
						container: '.products-hot-container',
						endpoint: '/api/products/hot',
						perPage: 4
					});
				}
				
				// Khởi tạo cho sản phẩm mới
				if (document.querySelector('.products-new-container')) {
					window.productsNewPagination = new FastPagination({
						container: '.products-new-container',
						endpoint: '/api/products/new',
						perPage: 4
					});
				}
				
				// Khởi tạo cho sản phẩm bán chạy
				if (document.querySelector('.products-selling-container')) {
					window.productsSellingPagination = new FastPagination({
						container: '.products-selling-container',
						endpoint: '/api/products/selling',
						perPage: 4
					});
				}
			});
		</script>
	</body>
</html>

<?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/layouts/app.blade.php ENDPATH**/ ?>