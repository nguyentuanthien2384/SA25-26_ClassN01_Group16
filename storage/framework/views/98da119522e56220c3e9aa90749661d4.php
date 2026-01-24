
<?php $__env->startSection('content'); ?>
    <style>
        .pro-reviewer-comment {
            width: 500px;
            margin-left: 90px;
            padding-left: 30px;
            border-radius: 1px;
            border: 1px solid #dfd4d4;
        }
        .product-buy {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .product-qty {
            min-width: 160px;
        }
        .product-qty .cart-plus-minus-box {
            width: 100%;
            height: 44px;
            line-height: 44px;
            text-align: center;
        }
        .product-action {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .product-action a {
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 22px;
            border-radius: 4px;
            font-weight: 600;
            min-width: 190px;
            white-space: nowrap;
            line-height: 1;
            gap: 8px;
        }
        .product-action a.btn-add-cart {
            border: 1px solid #f2583e;
            color: #f2583e;
            background: #fff;
        }
        .product-action a.btn-buy-now {
            border: 1px solid #f2583e;
            color: #fff;
            background: #f2583e;
        }
        .star-rating {
    unicode-bidi: bidi-override;
    direction: rtl;
    text-align: left;
  }
  .star-rating input[type="radio"] {
    display: none;
  }
  .star-rating label {
    display: inline-block;
    font-size: 30px;
    color: #ccc;
    cursor: pointer;
  }
  .star-rating label:before {
    content: '★';
    margin-right: 5px;
  }
  .star-rating input[type="radio"]:checked ~ label {
    color: #ffcc00;}
    </style>
    <div class="heading-banner-area overlay-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                </div>
            </div>
        </div>
    </div>
    <!-- HEADING-BANNER END -->
    <!-- PRODUCT-AREA START -->
    <div class="product-area single-pro-area pt-80 pb-80 product-style-2">
        <div class="container">

            <div class="row shop-list single-pro-info no-sidebar">
                <!-- Single-product start -->
                <div class="col-lg-12">
                    <div class="single-product clearfix">
                         <div class="single-pro-slider single-big-photo view-lightbox slider-for" style="width:55%;height:460px">
                            <div>
                                <img src="<?php echo e($productDetails->pro_image); ?>" alt="" style="width:auto;height:450px" />
                                <a class="view-full-screen" href="<?php echo e($productDetails->pro_image); ?>"
                                     data-lightbox="roadtrip" data-title="My caption">
                                    <i class="zmdi zmdi-zoom-in"></i>
                                </a>
                            </div>
                            <?php $__currentLoopData = $productimg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <img src="<?php echo e(asset('upload/' . $img->img_product)); ?>" alt="" style="width:auto;height:450px" />
                                <a class="view-full-screen" href="<?php echo e($productDetails->pro_image); ?>"
                                    data-lightbox="roadtrip" data-title="My caption">
                                    <i class="zmdi zmdi-zoom-in"></i>
                                </a>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php
                            $totalReviews = $productDetails->pro_total_number;
                            $totalStars = $productDetails->pro_total;
                            $averageRating = $totalReviews > 0 ? $totalReviews / $totalStars : 0;
                            $roundedNumber = round($averageRating * 2) / 2;
                        ?>
                        <div class="product-info"style="width:45%;height:460px">
                            <div class="fix">
                                <h4 class="post-title floatleft">Tên sản phẩm: <?php echo e($productDetails->pro_name); ?></h4>
                                <div class="pro-rating floatright" style="color:rgb(221, 172, 12);font-size: 25px; ">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <?php if($i <= $roundedNumber): ?>
                                            <i class="zmdi zmdi-star"></i>
                                        <?php elseif($i - 0.5 == $roundedNumber): ?>
                                            <i class="zmdi zmdi-star-half"></i>
                                            
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span style="color: black; font-size:18px"><?php echo e($totalStars); ?>(Đánh giá)</span>
                                </div>
                            </div>
                            <div class="fix mb-20" >
                                <label for="">
                                    <H5>Giá bán: </H5>
                                </label> 
                                <?php if($productDetails->pro_sale > 0 && $productDetails->pro_sale < $productDetails->pro_price): ?>
                                    <del style=" font-size: 17px">
                                        <span class="pro-price"><?php echo e(number_format($productDetails->pro_price, 0, ',', '.')); ?> Đ</span>
                                    </del> <br>
                                    <label for="">
                                        <H5>Giá khuyến mãi: </H5>
                                    </label>
                                    <span class="pro-price"><?php echo e(number_format($productDetails->pro_sale, 0, ',', '.')); ?> Đ</span>
                                <?php else: ?>
                                    <span class="pro-price"><?php echo e(number_format($productDetails->pro_price, 0, ',', '.')); ?> Đ</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-description" style="font-size: 14px">
                                <?php echo $productDetails->pro_description; ?>

                            </div>
                             <div class="product-buy">
                                <div class="product-qty">
                                    <input style="color: black" type="text" value="Số lượng: <?php echo e($productDetails->quantity); ?>" name="qtybutton"
                                        class="cart-plus-minus-box" readonly>
                                </div>
                                <div class="product-action">
                                    <a href="<?php echo e(route('cart.add', $productDetails->id)); ?>" data-bs-toggle="tooltip"
                                        data-placement="top" title="Add To Cart" class="btn-add-cart">Thêm vào giỏ hàng <i
                                            class="zmdi zmdi-shopping-cart-plus"></i></a>
                                    <a href="<?php echo e(route('cart.add', ['product' => $productDetails->id, 'buy_now' => 1])); ?>" data-bs-toggle="tooltip"
                                        data-placement="top" title="Buy Now" class="btn-buy-now">Mua ngay</a>
                                </div>
                            </div>
                            <!-- Single-pro-slider Small-photo start -->
                            <div class="single-pro-slider single-sml-photo slider-nav">
                                    <div>
                                        <img src="<?php echo e($productDetails->pro_image); ?>" alt="" style="width:105px;height:80px" />
                                    </div>
                                <?php $__currentLoopData = $productimg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div>
											<img src="<?php echo e(asset('upload/' . $img->img_product)); ?>" alt="" style="width:105px;height:80px" />
									</div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <!-- Single-pro-slider Small-photo end -->
                        </div>

                    </div>
                </div>

                <!-- Single-product end -->
            </div>
            <!-- single-product-tab start -->
            <div class="single-pro-tab" style="padding-top: 90px;">
                <div class="row">
                    <div class="col-md-3">
                        <div class="single-pro-tab-menu">
                            <!-- Nav tabs -->
                            <ul class="nav d-block">
                                <li><a href="#description" data-bs-toggle="tab">Miêu tả</a></li>
                                <li><a class="active" href="#" data-bs-toggle="tab">Đánh giá</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane" id="description">
                                <div class="pro-tab-info pro-description">
                                    <?php echo $productDetails->pro_content; ?>

                                </div>
                            </div>
                        </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="reviews">
                                    <div class="pro-tab-info pro-reviews">
                                        <div class="customer-review mb-60">
                                            <h3 class="tab-title title-border mb-30">Đánh giá khách hàng</h3>
                                            <ul class="product-comments clearfix">
                                                <li class="mb-30">
                                                    <?php $__currentLoopData = $ratings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="pro-reviewer">
                                                            <img src="img/reviewer/1.jpg" alt="" />
                                                        </div>
                                                        <div class="pro-reviewer-comment">
                                                            <div class="fix">
                                                                <div class="floatleft mbl-center">
                                                                    <h6 class="text-uppercase mb-0" style="paddin:  8px">
                                                                        <strong><?php echo e(optional($rating->user)->name ?? '[N/A]'); ?></strong>
                                                                    </h6>
                                                                    <p class="reply-date"><?php echo e($rating->created_at); ?></p>
                                                                </div>
                                                                <div class="comment-reply floatright">
                                                                    <a href="#"><i
                                                                            class="zmdi zmdi-mail-reply"></i></a>
                                                                    <a href="#"><i class="zmdi zmdi-close"></i></a>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0" style="font-size: 15px">
                                                                <?php echo e($rating->ra_content); ?></p>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </li>

                                                <li class="threaded-comments">
                                                    <div class="pro-reviewer">
                                                        <img src="img/reviewer/1.jpg" alt="" />
                                                    </div>

                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php if(Auth::check()): ?>
                                    <div class="sen">
                                        <h4 class="titl">Khách hàng gửi đánh giá</h4>
                                        <form id="" action="<?php echo e(route('postRating', $productDetails)); ?>"
                                            method="post">
                                            <?php echo csrf_field(); ?>
                                            <input type="text" name="ra_product_id" value="<?php echo e($productDetails->id); ?>"
                                                required>
                                                    <div class="star-rating">
                                                      <input type="radio" id="star5" name="ra_number" value="5"><label for="star5"></label>
                                                      <input type="radio" id="star4" name="ra_number" value="4"><label for="star4"></label>
                                                      <input type="radio" id="star3" name="ra_number" value="3"><label for="star3"></label>
                                                      <input type="radio" id="star2" name="ra_number" value="2"><label for="star2"></label>
                                                      <input type="radio" id="star1" name="ra_number" value="1"><label for="star1"></label>
                                                    </div>
                                            <input type="text" name="ra_content" placeholder="Nội dung đánh giá"
                                                required />
                                            <button type="submit">Gửi thông tin</button>
                                            <p class="form-message"></p>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- single-product-tab end -->
    </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/product/detail.blade.php ENDPATH**/ ?>