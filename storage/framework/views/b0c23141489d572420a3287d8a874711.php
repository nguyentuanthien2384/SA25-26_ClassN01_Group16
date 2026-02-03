
<?php $__env->startSection('content'); ?>
<style>
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
</style>
<div class="heading-banner-area overlay-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="heading-banner">
                    <?php if(isset($searchKeyword)): ?>
                    <div class="breadcumbs pb-15">
                        <ul>
                            <li><a href="/">Trang chủ</a></li>
                            <li>Tìm kiếm: "<?php echo e($searchKeyword); ?>"</li>
                        </ul>
                        <div style="color: #fff; margin-top: 10px; font-size: 16px;">
                            <i class="zmdi zmdi-search"></i> Tìm thấy <strong><?php echo e($totalResults ?? 0); ?></strong> sản phẩm
                        </div>
                    </div>
                    <?php elseif(isset($cateProduct)): ?>
                    <div class="breadcumbs pb-15">
                        <ul>
                            <li><a href="/">Trang chủ</a></li>
                            <li><?php echo e($cateProduct->c_name ?? 'Danh mục'); ?></li>
                        </ul>
                    </div>
                    <?php elseif(isset($cateProducts)): ?>
                    <div class="breadcumbs pb-15">
                        <?php $__currentLoopData = $cateProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cateProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <ul>
                            <li><a href="/">Trang chủ</a></li>
                            <li><?php echo e($cateProduct->c_name); ?></li>
                        </ul>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- HEADING-BANNER END -->
<!-- PRODUCT-AREA START -->
<div class="product-area pt-80 pb-80 product-style-2 js-ajax-section" data-section="product-index">
    <div class="container">
        
        <div class="row">
            <div class="col-md-9 col-sm-12 col-xs-12">
                <div class="shop-content mt-tab-30 mt-xs-30">
                    <div class="product-option mb-30 clearfix">
                        <div class="widget widget-categories  mb-30">
                            <form class="tree-most" id="form_order" method="get">
                                <?php if(isset($searchKeyword)): ?>
                                    <input type="hidden" name="k" value="<?php echo e($searchKeyword); ?>">
                                <?php endif; ?>
                            <div class="widget-title">
                                <button type="submit"><h4>Sắp xếp</h4></button>
                                <select name="orderby" class="orderby">
                                    <?php if(isset($searchKeyword)): ?>
                                        <option <?php echo e(Request::get('orderby') == "relevance" || !Request::get('orderby') ? "selected = 'selected'" : ""); ?> value="relevance" selected= "selected">Liên quan nhất</option>
                                        <option <?php echo e(Request::get('orderby') == "newest" ? "selected = 'selected'" : ""); ?> value="newest">Mới nhất</option>
                                        <option <?php echo e(Request::get('orderby') == "name_asc" ? "selected = 'selected'" : ""); ?> value="name_asc">Tên A-Z</option>
                                        <option <?php echo e(Request::get('orderby') == "name_desc" ? "selected = 'selected'" : ""); ?> value="name_desc">Tên Z-A</option>
                                        <option <?php echo e(Request::get('orderby') == "price_asc" ? "selected = 'selected'" : ""); ?> value="price_asc">Giá tăng dần</option>
                                        <option <?php echo e(Request::get('orderby') == "price_desc" ? "selected = 'selected'" : ""); ?> value="price_desc">Giá giảm dần</option>
                                    <?php else: ?>
                                        <option <?php echo e(Request::get('orderby') == "md" || !Request::get('orderby') ? "selected = 'selected'" : ""); ?> value="md" selected= "selected">Mặc định</option>
                                        <option <?php echo e(Request::get('orderby') == "desc" ? "selected = 'selected'" : ""); ?> value="desc">Mới nhất</option>
                                        <option <?php echo e(Request::get('orderby') == "asc" ? "selected = 'selected'" : ""); ?> value="asc">Sản phẩm cũ</option>
                                        <option <?php echo e(Request::get('orderby') == "price_max" ? "selected = 'selected'" : ""); ?> value="price_max">Giá tăng dần</option>
                                        <option <?php echo e(Request::get('orderby') == "price_min" ? "selected = 'selected'" : ""); ?> value="price_min">Giá giảm dần</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            </form>
                        </div>
                    </div>
                    <!-- Tab panes --> 
                    <div class="tab-content">
                        <div class="tab-pane active" id="grid-view">
                            <div class="row">
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $salePercent = 0;
                                    if ($product->pro_sale > 0 && $product->pro_sale < $product->pro_price) {
                                        $salePercent = round((($product->pro_price - $product->pro_sale) / $product->pro_price) * 100);
                                    }
                                    $totalReviews = $product->pro_total_number ?? 0;
                                    $totalStars = $product->pro_total ?? 0;
                                    $avgRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
                                ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-30">
                                    <div class="p-card">
                                        <?php if($salePercent > 0): ?>
                                            <span class="p-badge">Giảm <?php echo e($salePercent); ?>%</span>
                                        <?php endif; ?>
                                        <span class="p-badge p-badge--installment">Trả góp 0%</span>
                                        <a href="<?php echo e(route('get.detail.product',[$product->pro_slug,$product->id])); ?>">
                                            <img src="<?php echo e($product->pro_image); ?>" alt="" class="p-img"/>
                                        </a>
                                        <div class="p-title">
                                            <a href="<?php echo e(route('get.detail.product',[$product->pro_slug,$product->id])); ?>"><?php echo e($product->pro_name); ?></a>
                                        </div>
                                        <div>
                                            <?php if($product->pro_sale > 0 && $product->pro_sale < $product->pro_price): ?>
                                                <span class="p-price"><?php echo e(number_format($product->pro_sale,0,',','.')); ?>đ</span>
                                                <span class="p-price-old"><?php echo e(number_format($product->pro_price,0,',','.')); ?>đ</span>
                                            <?php else: ?>
                                                <span class="p-price"><?php echo e(number_format($product->pro_price,0,',','.')); ?>đ</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="p-note">Trả góp 0% - 0đ phụ thu - 0đ trả trước - kỳ hạn đến 6 tháng</div>
                                        <div class="p-out">
                                            <?php if($product->quantity == 0): ?>
                                                Tạm hết hàng
                                            <?php else: ?>
                                                &nbsp;
                                            <?php endif; ?>
                                        </div>
                                        <div class="p-meta">
                                            <div class="p-rating">
                                                <i class="zmdi zmdi-star"></i><?php echo e($avgRating); ?>

                                            </div>
                                            <a href="<?php echo e(route('wishlist.toggle', $product->id)); ?>" class="p-wishlist js-wishlist-toggle" data-product-id="<?php echo e($product->id); ?>">
                                                <i class="zmdi <?php echo e(in_array($product->id, $wishlistIds ?? []) ? 'zmdi-favorite' : 'zmdi-favorite-outline'); ?>"></i>Yêu thích
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>  
                </div>
                    <?php if($isPaginated ?? false): ?>
                    <div class="pagination-wrap text-center">
                        <?php if(method_exists($products, 'appends')): ?>
                            <?php echo $products->appends(request()->query())->links('components.pagination'); ?>

                        <?php endif; ?>
                    </div>
                    <?php elseif(isset($searchKeyword) && $products->count() == 0): ?>
                    <div class="alert alert-info text-center" style="padding: 40px; margin: 20px 0;">
                        <i class="zmdi zmdi-search" style="font-size: 48px; color: #999; margin-bottom: 10px;"></i>
                        <h4>Không tìm thấy sản phẩm nào cho "<?php echo e($searchKeyword); ?>"</h4>
                        <p>Vui lòng thử lại với từ khóa khác hoặc <a href="/">quay về trang chủ</a></p>
                    </div>
                    <?php endif; ?>
                <!-- Shop-Content End -->
            </div>
            <div class="col-md-3 ">
                <!-- Widget-Search start -->
                <div class="widget widget-search mb-30">
                    <form action="<?php echo e(route('get.product.list')); ?>" method="GET">
                        <input type="text" name="k" placeholder="Tìm kiếm sản phẩm..." value="<?php echo e($searchKeyword ?? ''); ?>" />
                        <button type="submit">
                            <i class="zmdi zmdi-search"></i>
                        </button>
                    </form>
                </div>
                <aside class="widget widget-categories  mb-30">
                    <div class="widget-title">
                        <h4>Khoảng giá</h4>
                    </div>
                    <div id="cat-treeview"  class="widget-info product-cat boxscrol2">
                        <ul>
                            <li><a class="<?php echo e(Request::get('price') == 1 ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['price'=>1])); ?>">Dưới 1 Triệu</a></li>
                            <li><a class="<?php echo e(Request::get('price') == 2 ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['price'=>2])); ?>">1.000.000 - 3.000.000 Đ</a></li>
                            <li><a class="<?php echo e(Request::get('price') == 3 ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['price'=>3])); ?>">3.000.000 - 5.000.000 Đ</a></li>                   
                            <li><a class="<?php echo e(Request::get('price') == 4 ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['price'=>4])); ?>">5.000.000 - 7.000.000 Đ</a></li>
                            <li><a class="<?php echo e(Request::get('price') == 5 ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['price'=>5])); ?>">7.000.000 - 10.000.000 Đ</a></li>
                            <li><a class="<?php echo e(Request::get('price') == 6 ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['price'=>6])); ?>">Lớn hơn 10.000.000 Đ</a></li>
                        </ul>
                    </div>
                </aside>
             </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(function(){
           $('.orderby').change(function(){
            $("#form_order").submit() ;
           }) 
        })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views\product\index.blade.php ENDPATH**/ ?>