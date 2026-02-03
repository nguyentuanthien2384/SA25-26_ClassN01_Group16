<?php $__env->startSection('content'); ?>
<style>
    .table-responsive .active{
        color: red
    }
</style>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Kho </a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <dov class="row">
        <div class="col-sm-12">
            <form class="form-inline" action="" style="margin-bottom: 20px">
                <div class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="Tên sản phẩm..." value="<?php echo e(Request::get('name')); ?>">
                </div>
                <div class="form-group">
                    <select name="cate" id="" class="form-control" >
                        <option value="">Danh mục</option>
                        <?php if(isset($categorys)): ?>
                            <?php $__currentLoopData = $categorys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>"<?php echo e(\Request::get('cate') == $category->id ? "selected = 'selected" : ""); ?>><?php echo e($category->c_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </dov>
    <div class="table-responsive">
        <h2>QUẢN LÝ SẢN PHẨM <br> <a href="?type=inventory" class="<?php echo e(Request::get('type') == 'inventory' || !Request::has('type') ? 'active' : ''); ?>" style="font-size: 20px;">HÀNG TỒN</a>
            <a href="?type=pay" class="<?php echo e(Request::get('type') == 'pay' ? 'active' : ''); ?>" style="font-size: 20px;">HÀNG BÁN CHẠY</a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Loại sản phẩm</th>
                    <th>Giá bán</th>
                    <th>Hình ảnh</th>
                    <th>Số lượng</th>
                    <th>Số lần bán</th>
                </tr>
            </thead>
            <tbody>
                <?php if( isset($products)): ?>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $totalReviews = $product->pro_total_number;
                     $totalStars = $product->pro_total;
                    $averageRating = $totalReviews > 0 ? ($totalReviews /  $totalStars) : 0;
                    $roundedNumber = round($averageRating * 2) / 2;
                    ?>
                    <tr>
                        <td><?php echo e($product->id); ?></td>
                        <td>
                            <?php echo e($product->pro_name); ?>

                            <div class="average-rating" style="color:rgb(221, 172, 12)">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                              <?php if($i <= $roundedNumber): ?>
                                 <i class="fas fa-star"></i>
                                <?php elseif($i - 0.5 == $averageRating): ?>
                                <i class="fas fa-star-half-alt"></i>
                            <?php else: ?>
                            <i class="far fa-star"></i> 
                            <?php endif; ?>
                                <?php endfor; ?>
                                <span style="color: black"><?php echo e($roundedNumber); ?></span>
                            </div>
                        </td>
                        <td><?php echo e(isset($product->category->c_name) ? $product->category->c_name :'[N\A]'); ?></td>
                        <td><?php echo e(isset($product->pro_price) ? $product->pro_price :'[N\A]'); ?></td>
                        <td>
                            <img src="<?php echo e(isset($product->pro_image) ? $product->pro_image :'[N\A]'); ?>" alt="" style="max-width:60px;height: auto;">
                        </td>
                        <td><?php echo e(isset($product->quantity) ? $product->quantity :'[N\A]'); ?></td>
                        <td><?php echo e(isset($product->pro_pay) ? $product->pro_pay :'[N\A]'); ?></td>
                        <td>
                        </tr>                
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
            </tbody>
        </table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\warehouse\index.blade.php ENDPATH**/ ?>