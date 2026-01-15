<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Tài khoản</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ TÀI KHOẢN </h2>
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
                <?php if(isset($userss)): ?>
                    <?php $__currentLoopData = $userss; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $users): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($users->id); ?></td>
                        <td>
                            <?php echo e($users->name); ?>

                        
                        </td>
                        <td>
                            <?php echo e($users->email); ?>

                        
                        </td>
                        <td>
                            <?php echo e($users->phone); ?>

                        
                        </td>
                        
                        <td>
                            <a style="padding: 5px 10px; border: 1px solid #995" href="<?php echo e(route('user.delete',$users->id)); ?>"><i class="fa-solid fa-trash" style="font-size:11px"></i>Xoá</a>
                        </td>
                    </tr>  
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules/Admin\resources/views/user/index.blade.php ENDPATH**/ ?>