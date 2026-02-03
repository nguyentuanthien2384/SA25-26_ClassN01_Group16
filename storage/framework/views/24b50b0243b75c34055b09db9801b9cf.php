<?php $__env->startSection('content'); ?>
    <div class="table-responsive">
        <h2>CHI TIẾT ĐƠN HÀNG</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Hình ảnh</th>
                    <th>Số lượng</th>
                    <th>Giá tiền</th>
                    <th>Tổng tiền</th>
                </tr>
            </thead>
            <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $sum = $order->od_quantity * $order->od_price
                     ?>
                    <tr>
                        <td><?php echo e($order->id); ?></td>
                        <td><?php echo e($order->products->pro_name); ?></td>
                        <td><img src="<?php echo e($order->products->pro_image); ?>" alt="" style="width:110px; height:70px"></td>
                        <td><?php echo e($order->od_quantity); ?></td>
                        <td><?php echo e(number_format($order->od_price)); ?></td>
                        <td><?php echo e(number_format($sum)); ?></td>
                        
                    </tr>                
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\transaction\view.blade.php ENDPATH**/ ?>