<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="<?php echo e(route('account.index')); ?>" title="Danh mục">Bài viết</a></li>
            <li><a href="">Cập nhật</a></li>
        </ol>
    </div>
    <div class="">
      <?php echo $__env->make("admin::account.form", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\account\update.blade.php ENDPATH**/ ?>