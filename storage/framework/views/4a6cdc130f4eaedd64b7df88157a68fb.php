<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Đánh giá</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ ĐÁNH GIÁ </h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên đánh giá</th>
                    <th>Số điện thoại</th>
                    <th>Sản phẩm</th>
                    <th>Nội dung</th>
                    <th>Rating</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($ratings)): ?>
                    <?php $__currentLoopData = $ratings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($rating->id); ?></td>
                        <td><?php echo e(optional($rating->user)->name ?? '[N/A]'); ?></td>
                        <td><?php echo e(optional($rating->user)->phone ?? '[N/A]'); ?></td>
                        <td><?php echo e(optional($rating->product)->pro_name ?? '[N/A]'); ?></td>
                        <td><?php echo e($rating->ra_content); ?></td>
                        <td><?php echo e($rating->ra_number); ?></td>
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.delete.rating',$rating->id)); ?>"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="pagination-wrap">
            <?php echo $ratings->links('components.pagination'); ?>

        </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\rating\index.blade.php ENDPATH**/ ?>