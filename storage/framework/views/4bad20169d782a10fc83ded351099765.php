<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Danh mục</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ DANH MỤC <a href="<?php echo e(route('admin.get.create.category')); ?>" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên danh mục</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if( isset($categorys)): ?>
                    <?php $__currentLoopData = $categorys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($category->id); ?></td>
                        <td><?php echo e($category->c_name); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.get.action.category',['active',$category->id])); ?>"class="label <?php echo e($category->getStatus($category->c_active)['class']); ?>"><?php echo e($category->getStatus($category->c_active)['name']); ?></a>
                        </td>
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.get.edit.category',$category->id)); ?>"><i class="fa-solid fa-pen"></i>Chỉnh sửa</a>
                                <a href="<?php echo e(route('admin.get.action.category',['delete',$category->id])); ?>"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>                
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules/Admin\resources/views/category/index.blade.php ENDPATH**/ ?>