<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Tài khoản</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ QUẢN TRỊ <a href="<?php echo e(route('account.create')); ?>" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên </th>
                    <th>Email</th>
                    <th>Số diện thoại</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($admins)): ?>
                    <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $users): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($users->id); ?></td>
                        <td>
                            <?php echo e(isset($users->name) ? $users->name :'[N\A]'); ?>

                        </td>
                        <td>
                            <?php echo e($users->email); ?>

                        
                        </td>
                        <td>
                            <?php echo e($users->phone); ?>

                        
                        </td>
                        
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="<?php echo e(route('account.update',$users->id)); ?>"><i class="fa-solid fa-pen"></i>Chỉnh sửa</a>
                                <a href="<?php echo e(route('account.delete',$users->id)); ?>"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>  
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\account\index.blade.php ENDPATH**/ ?>