<?php $__env->startSection('content'); ?>
    <h1>Hello World</h1>

    <p>Module: <?php echo config('cart.name'); ?></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('cart::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Cart\resources\views\index.blade.php ENDPATH**/ ?>