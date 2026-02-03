<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Liên hệ</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ Liên hệ </h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên liên hệ</th>
                    <th>Số điện thoại</th>
                    <th>Emaill</th>
                    <th>Tiêu đề</th>
                    <th>Nội dung</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($contacts)): ?>
                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($contact->id); ?></td>
                        <td><?php echo e($contact->con_name); ?></td>
                        <td><?php echo e($contact->con_phone); ?></td>
                        <td><?php echo e($contact->con_email); ?></td>
                        <td><?php echo e($contact->con_title); ?></td>
                        <td><?php echo e($contact->con_message); ?></td>
                        
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.get.delete.contact',$contact->id)); ?>" onclick="return confirm('Bạn có chắc muốn xoá liên hệ này?')"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>  
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\contact\index.blade.php ENDPATH**/ ?>