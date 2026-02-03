<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Sản phẩm</a></li>
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
        <h2>QUẢN LÝ SẢN PHẨM <a href="<?php echo e(route('admin.get.create.product')); ?>" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="width:200px">Tên sản phẩm</th>
                    <th style="width :70px">Loại sản phẩm</th>
                    <th>Giá bán</th>
                    <th>Giá Khuyễn mãi</th>
                    <th>Hình ảnh</th>
                    <th style="width :50px">Số lượng</th>
                    <th>Trạng thái</th>
                    <th>Nổi bật</th>
                    <th>Thao tác</th>
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
                        <td><?php echo e(isset($product->pro_sale) ? $product->pro_sale :'[N\A]'); ?></td>
                        <td>
                            <img src="<?php echo e(isset($product->pro_image) ? $product->pro_image :'[N\A]'); ?>" alt="" style="max-width:60px;height: auto;">
                        </td>
                        <td><?php echo e(isset($product->quantity) ? $product->quantity :'[N\A]'); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.get.action.product',['active',$product->id])); ?>"class="label <?php echo e($product->getStatus($product->pro_active)['class']); ?>"><?php echo e($product->getStatus($product->pro_active)['name']); ?></a>
                        </td>
                        <td>
                            <a href="<?php echo e(route('admin.get.action.product',['hot',$product->id])); ?>"class="label <?php echo e($product->getHot($product->pro_hot)['class']); ?>"><?php echo e($product->getHot($product->pro_hot)['name']); ?></a>
                        </td>
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.get.edit.product',$product->id)); ?>"><i class="fa-solid fa-pen"></i>Chỉnh sửa</a>
                                <a href="<?php echo e(route('admin.get.action.product',['delete',$product->id])); ?>"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>                
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
            </tbody>
        </table>
        <div class="pagination-wrap">
            <?php echo $products->links('components.pagination'); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\product\index.blade.php ENDPATH**/ ?>