<?php $__env->startSection('content'); ?>
<form action="" method="POST">
    <?php echo csrf_field(); ?>
    <div class="form-group">
      <label for="tr_status">Tạng thái đơn hàng</label>
      <input type="text" class="form-control" placeholder="trạng thái đơn hàng" value="<?php echo e(old('tr_status',isset($transaction->tr_status)? $transaction->tr_status : '')); ?>" name="tr_status">
      <?php if($errors->has('tr_status')): ?>
        <span class="error-text">
          <?php echo e($errors->first('tr_status')); ?>

        </span>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-success">Lưu thông tin</button>
  </form>
  <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\transaction\update.blade.php ENDPATH**/ ?>