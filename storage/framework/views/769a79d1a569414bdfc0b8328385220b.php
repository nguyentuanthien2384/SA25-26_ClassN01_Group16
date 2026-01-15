<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title></title>
		<meta name="description" content="Hurst – Furniture Store eCommerce HTML Template is a clean and elegant design – suitable for selling flower, cookery, accessories, fashion, high fashion, accessories, digital, kids, watches, jewelries, shoes, kids, furniture, sports….. It has a fully responsive width adjusts automatically to any screen size or resolution.">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
		<!-- Place favicon.ico in the root directory -->

		<!-- Google Font -->
		<link href='https://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Bree+Serif' rel='stylesheet' type='text/css'>

		<!-- all css here -->
		<!-- bootstrap v3.3.6 css -->
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
		<!-- modernizr css -->
		<script src="<?php echo e(asset('js/vendor/modernizr-3.11.2.min.js')); ?>"></script>
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
											<li><a href="wishlist.html"><i class="zmdi zmdi-favorite"></i></a></li>
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
				<div class="product-area pt-80 pb-35">
					<div class="container">
						<div class="row">
							<div class="col-lg-12">
								<div class="section-title text-center">
									<h2 class="title-border">Sản phẩm nổi bật</h2>
								</div>
								<div class="product-slider style-1 arrow-left-right">
									<!-- Single-product start -->
									<?php $__currentLoopData = $productHot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proHot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<div class="single-product">
										<div class="product-img">
											<?php if($proHot->quantity <= 0): ?>
											<span class="pro-label new-label" style="position:absolute; background:#e91e63; left:0px ">Tạm hết hàng</span>
											<?php endif; ?>
											<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><img src="<?php echo e($proHot->pro_image); ?>" alt="" style="width:270px ; height:220px"/></a>
											<?php if(Auth::check()): ?>
											<div class="product-action clearfix">
												<a href="wishlist.html" data-bs-toggle="tooltip" data-placement="top" title="Wishlist"><i class="zmdi zmdi-favorite-outline"></i></a>
												<a href="#" data-bs-toggle="modal"  data-bs-target="#productModal" title="Quick View"><i class="zmdi zmdi-zoom-in"></i></a>
												<a href="#" data-bs-toggle="tooltip" data-placement="top" title="Compare"><i class="zmdi zmdi-refresh"></i></a>
												<a href="<?php echo e(route('cart.add',$proHot->id)); ?>" data-bs-toggle="tooltip" data-placement="top" title="Add To Cart"><i class="zmdi zmdi-shopping-cart-plus"></i></a>
											</div>
											<?php endif; ?>
										</div>
										<div class="product-info clearfix">
											<div class="fix">
												<h4 class="post-title floatleft"><a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><?php echo e($proHot->pro_name); ?></a></h4>

											</div>
											<div class="fix">
												<span class="pro-price floatleft"><del>Giá: <?php echo e($proHot->pro_price); ?></del></span>
												<span class="pro-price floatleft"> Giá khuyến mãi: <?php echo e($proHot->pro_sale); ?></span>
											</div>
										</div>

									</div>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
			<div class="product-area pt-80 pb-35">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Sản phẩm mới </h2>
							</div>
							<div class="product-slider style-1 arrow-left-right">
								<!-- Single-product start -->
								<?php $__currentLoopData = $productNew; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proHot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<div class="single-product">
									<div class="product-img">
										<?php if($proHot->quantity <= 0): ?>
										<span class="pro-label new-label" style="position:absolute; background:#e91e63; left:0px ">Tạm hết hàng</span>
										<?php endif; ?>
										<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><img src="<?php echo e($proHot->pro_image); ?>" alt="" style="width:270px ; height:220px"/></a>
										<?php if(Auth::check()): ?>
										<div class="product-action clearfix">
											<a href="wishlist.html" data-bs-toggle="tooltip" data-placement="top" title="Wishlist"><i class="zmdi zmdi-favorite-outline"></i></a>
											<a href="#" data-bs-toggle="modal"  data-bs-target="#productModal" title="Quick View"><i class="zmdi zmdi-zoom-in"></i></a>
											<a href="#" data-bs-toggle="tooltip" data-placement="top" title="Compare"><i class="zmdi zmdi-refresh"></i></a>
											<a href="<?php echo e(route('cart.add',$proHot->id)); ?>" data-bs-toggle="tooltip" data-placement="top" title="Add To Cart"><i class="zmdi zmdi-shopping-cart-plus"></i></a>
										</div>
										<?php endif; ?>
									</div>
									<div class="product-info clearfix">
										<div class="fix">
											<h4 class="post-title floatleft"><a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><?php echo e($proHot->pro_name); ?></a></h4>

										</div>
										<div class="fix">
											<span class="pro-price floatleft"><del>GIá: <?php echo e($proHot->pro_price); ?></del></span>
											<span class="pro-price floatleft"> Giá khuyến mãi: <?php echo e($proHot->pro_sale); ?></span>
										</div>
									</div>

								</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($productSelling)): ?>
			<div class="product-area pt-80 pb-35">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Sản phẩm bán nhiều nhất</h2>
							</div>
							<div class="product-slider style-1 arrow-left-right">
								<!-- Single-product start -->
								<?php $__currentLoopData = $productSelling; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proHot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<div class="single-product">
									<div class="product-img">
										<?php if($proHot->pro_pay > 2): ?>
										<span class="pro-label new-label" style="position:absolute; background:#eb0505; left:0px "> Bán chạy</span>
										<?php endif; ?>
										<a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><img src="<?php echo e($proHot->pro_image); ?>" alt="" style="width:270px ; height:220px"/></a>
										<?php if(Auth::check()): ?>
										<div class="product-action clearfix">
											<a href="#" data-bs-toggle="tooltip" data-placement="top" title="Wishlist"><i class="zmdi zmdi-favorite-outline"></i></a>
											<a href="#" data-bs-toggle="modal"  data-bs-target="#productModal" title="Quick View"><i class="zmdi zmdi-zoom-in"></i></a>
											<a href="#" data-bs-toggle="tooltip" data-placement="top" title="Compare"><i class="zmdi zmdi-refresh"></i></a>
											<a href="<?php echo e(route('cart.add',$proHot->id)); ?>" data-bs-toggle="tooltip" data-placement="top" title="Add To Cart"><i class="zmdi zmdi-shopping-cart-plus"></i></a>
										</div>
										<?php endif; ?>
									</div>
									<div class="product-info clearfix">
										<div class="fix">
											<h4 class="post-title floatleft"><a href="<?php echo e(route('get.detail.product',[$proHot->pro_slug,$proHot->id])); ?>"><?php echo e($proHot->pro_name); ?></a></h4>

										</div>
										<div class="fix">
											<span class="pro-price floatleft"><del>GIá: <?php echo e($proHot->pro_price); ?></del></span>
											<span class="pro-price floatleft"> Giá khuyến mãi: <?php echo e($proHot->pro_sale); ?></span>
										</div>
									</div>

								</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if(isset($articleNews)): ?>
			<div class="product-area pt-80 pb-35">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="section-title text-center">
								<h2 class="title-border">Tin tức nổi bật</h2>
							</div>
							<?php if($articleNews->isEmpty()): ?>
								<div class="product-info clearfix" style="text-align:center; padding: 20px 0;">
									<p>Chưa có tin tức nổi bật.</p>
								</div>
							<?php else: ?>
							<div class="product-slider style-1 arrow-left-right">
								<!-- Single-product start -->
								<?php $__currentLoopData = $articleNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $articleNew): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<div class="single-product">
									<div class="product-img">
										<a href="<?php echo e(route('get.detail.product',[$articleNew->pro_slug, $articleNew->id])); ?>">
											<?php if(!empty($articleNew->pro_image)): ?>
												<img src="<?php echo e($articleNew->pro_image); ?>" alt="" style="width: auto ; height:200px"/>
											<?php else: ?>
												<div style="height:200px; display:flex; align-items:center; justify-content:center; background:#f5f5f5;">
													<span>Không có ảnh</span>
												</div>
											<?php endif; ?>
										</a>
									</div>
									<div class="product-info clearfix">
										<div class="fix">
											<h4 class="post-title floatleft"><a href="<?php echo e(route('get.detail.product',[$articleNew->pro_slug, $articleNew->id])); ?>"><?php echo e($articleNew->pro_name); ?></a></h4>
											<p class="floatright hidden-sm d-none d-md-block" style="color: #c87065"><?php echo e($articleNew->created_at->format('d-m-Y')); ?></p>
										</div>

										<a href="<?php echo e(route('get.detail.product',[$articleNew->pro_slug, $articleNew->id])); ?>" class="button-2 text-dark-red">Xem thêm...</a>
									</div>

								</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

		<!-- all js here -->
		<!-- jquery latest version -->
		<script src="<?php echo e(asset('js/vendor/jquery-3.6.0.min.js')); ?>"></script>
		<script src="<?php echo e(asset('js/vendor/jquery-migrate-3.3.2.min.js')); ?>"></script>
		<!-- bootstrap js -->
		<script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>
		<!-- jquery.meanmenu js -->
		<script src="<?php echo e(asset('js/jquery.meanmenu.js')); ?>"></script>
		<!-- slick.min js -->
		<script src="<?php echo e(asset('js/slick.min.js')); ?>"></script>
		<!-- jquery.treeview js -->
		<script src="<?php echo e(asset('js/jquery.treeview.js')); ?>"></script>
		<!-- lightbox.min js -->
		<script src="<?php echo e(asset('js/lightbox.min.js')); ?>"></script>
		<!-- jquery-ui js -->
		<script src="<?php echo e(asset('js/jquery-ui.min.js')); ?>"></script>
		<!-- jquery.nivo.slider js -->
		<script src="<?php echo e(asset('lib/js/jquery.nivo.slider.js')); ?>"></script>
		<script src="<?php echo e(asset('lib/home.js')); ?>"></script>
		<!-- jquery.nicescroll.min js -->
		<script src="<?php echo e(asset('js/jquery.nicescroll.min.js')); ?>"></script>
		<!-- countdon.min js -->
		<script src="<?php echo e(asset('js/countdon.min.js')); ?>"></script>
		<!-- wow js -->
		<script src="<?php echo e(asset('js/wow.min.js')); ?>"></script>
		<!-- plugins js -->
		<script src="<?php echo e(asset('js/plugins.js')); ?>"></script>
		<!-- main js -->
		<script src="<?php echo e(asset('js/main.min.js')); ?>"></script>

		<script>
			setTimeout(function() {
				$('.alert').fadeOut('fast');
			}, 5000); // 5 giây

		</script>
	</body>
</html>

<?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/layouts/app.blade.php ENDPATH**/ ?>