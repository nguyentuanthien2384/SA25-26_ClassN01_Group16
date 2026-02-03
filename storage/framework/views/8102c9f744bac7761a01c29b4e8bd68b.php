<?php $__env->startSection('content'); ?>
    <h1>Hello World</h1>

    <p>Module: <?php echo config('customer.name'); ?></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('customer::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Customer\resources\views\index.blade.php ENDPATH**/ ?>