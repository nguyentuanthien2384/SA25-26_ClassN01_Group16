
<?php $__env->startSection('content'); ?>

<div class="shopping-cart-area  pt-80 pb-80">
    <div class="container">	
        <div class="row">
            <div class="col-lg-12">
                <div class="shopping-cart">
                    <!-- Nav tabs -->
                    <ul class="cart-page-menu nav row clearfix mb-30">
                        <li><a class="active" href="#shopping-cart" data-bs-toggle="tab">Giỏ hàng</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!-- shopping-cart start -->
                        <div class="tab-pane active" id="shopping-cart">
                            <form action="#">
                                <div class="shop-cart-table">
                                    <div class="table-content table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="product-thumbnail">Sản phẩm</th>
                                                    <th class="product-price">Giá</th>
                                                    <th class="product-quantity">Số Lượng</th>
                                                    <th class="product-subtotal">Tổng</th>
                                                    <th class="product-remove">Xoá</th>
                                                </tr>
                                            </thead>
                                            
                                            <tbody>
                                                <?php $__currentLoopData = $carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="product-thumbnail  text-left">
                                                        <!-- Single-product start -->
                                                        <div class="single-product">
                                                            <div class="product-img">
                                                                <a href="single-product.html"><img src="<?php echo e($item->   image); ?>" alt="" /></a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h4 class="post-title"><?php echo e($item->name); ?></h4>
                                                            </div>
                                                        </div>
                                                        <!-- Single-product end -->												
                                                    </td>
                                                    <td class="product-price"><?php echo e(number_format($item->price, 0, ',', '.')); ?> (VND)</td>
                                                    <td> 
                                                        <form action="<?php echo e(route('cart.update', $item->pro_id)); ?>" method="get">
                                                            <input type="number" value="<?php echo e($item->quantity); ?>" name="quantity" style="width:40px; text-algin:center">
                                                            <button class="btn btn-primary btn-sm">Cập nhật</button>
                                                        </form>
                                                    </td>
                                                    <td class="product-subtotal"><?php echo e(number_format($item->quantity * $item->price, 0, ',', '.')); ?> (VND)</td>
                                                    <td class="product-remove">
                                                        <a onclick="return confirm('Bạn chắc chắn xoá sản phẩm')" href="<?php echo e(route('cart.delete',$item->pro_id)); ?>"><i class="zmdi zmdi-close"></i></a>
                                                    </td>
                                                </tr>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="customer-login payment-details mt-30">
                                            <?php
                                            $sum = 0 ;
                                            $sum_vat = 0 ;
                                            $tong = 0;
                                            foreach($carts as $item) {
                                            $sum = $sum + $item->quantity * $item->price;
                                            $sum_vat = $sum_vat + $item->quantity * $item->price * 0.1 ;
                                            $tong = $tong + $item->quantity ;
                                            }
                                             ?>
                                            <h4 class="title-1 title-border text-uppercase">Chi tiết thanh thoán</h4>
                                            <table>
                                                <tbody style=" color:black; font-size:15px">
                                                    <tr>
                                                        <td class="text-left">Tổng số lượng của giỏ hàng</td>
                                                        <td class="text-end"><?php echo e($tong); ?> (SP)</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-left">Tổng phụ của giỏ hàng</td>
                                                        <td class="text-end"><?php echo e(number_format($sum)); ?> (VND)</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-left">Thuế Vat (10%)</td>
                                                        <td class="text-end"><?php echo e(number_format($sum_vat)); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-left">Tổng thanh toán</td>
                                                        <td class="text-end"> <?php echo e(number_format($sum + $sum_vat)); ?> (VND)</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-center" style="padding-top: 20px">
                                                <a href="<?php echo e(route('home')); ?>" class="btn btn-primary btn-sm">Tiếp tục mua hàng</a>
                                                <a href="<?php echo e(route('cart.clear')); ?>" class="btn btn-danger btn-sm">Xoá hết hàng</a>
                                                <a href="<?php echo e(route('form.pay')); ?>" class="btn btn-success btn-sm" id="clearCartButton">Thanh toán</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </form>		
                        </div>
                        
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views\layouts\cart.blade.php ENDPATH**/ ?>