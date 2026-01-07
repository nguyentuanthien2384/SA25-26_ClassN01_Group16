<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Sản phẩm</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
   
    <div class="table-responsive">
        <h2>QUẢN LÝ NHÀ CUNG CẤP <a href="<?php echo e(route('admin.get.create.supplier')); ?>" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên nhà cung cấp</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if( isset($suppliers)): ?>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($supplier->id); ?></td>
                        <td><?php echo e(isset($supplier->s_name) ? $supplier->s_name :'[N\A]'); ?></td>
                        <td>
                         
                            <a style="padding: 5px 10px; border: 1px solid #995" href="<?php echo e(route('admin.get.delete.supplier',$supplier->id)); ?>"><i class="fa-solid fa-trash" style="font-size:11px"></i>Xoá</a>
                        </td>
                    </tr>                
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php echo $suppliers->links(); ?>

            </tbody>
        </table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules/Admin\resources/views/supplier/index.blade.php ENDPATH**/ ?>