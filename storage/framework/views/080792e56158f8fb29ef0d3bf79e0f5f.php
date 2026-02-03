<?php $__env->startSection('content'); ?>
<form action="" method="POST">
    <?php echo csrf_field(); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="b_name">Tên nhà cung cấp</label>
                <input type="text" class="form-control" placeholder="Tên nhà cung cấp"
                    value="<?php echo e(old('s_name', isset($supplier->s_name) ? $supplier->s_name : '')); ?>" name="s_name">
                <?php if($errors->has('s_name')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('s_name')); ?>

                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success">Lưu thông tin</button>
</form>
  <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\supplier\create.blade.php ENDPATH**/ ?>