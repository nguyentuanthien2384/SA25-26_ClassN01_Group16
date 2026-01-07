<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Sản phẩm</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
   
    <div class="table-responsive">
        <h2>QUẢN LÝ NHÀ NHẬP HÀNG <a href="<?php echo e(route('admin.get.create.import')); ?>" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Tên nhà cung cấp</th>
                    <th>Giá nhập</th>
                    <th>Số lượng</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if( isset($importgoods)): ?>
                    <?php $__currentLoopData = $importgoods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $importgood): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($importgood->id); ?></td>
                        <td><?php echo e(isset($importgood->product->pro_name) ? $importgood->product->pro_name :'[N\A]'); ?></td>
                        <td><?php echo e(isset($importgood->supplier->s_name) ? $importgood->supplier->s_name :'[N\A]'); ?></td>
                        <td><?php echo e($importgood->price); ?></td>
                        <td><?php echo e($importgood->quantity); ?></td>
                        <td>
                            <a style="padding: 5px 10px; border: 1px solid #995" href="<?php echo e(route('admin.get.delete.importdood',$importgood->id)); ?>"><i class="fa-solid fa-trash" style="font-size:11px"></i>Xoá</a>
                        </td>
                    </tr>                
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php echo $importgoods->links(); ?>

            </tbody>
        </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules/Admin\resources/views/import/index.blade.php ENDPATH**/ ?>