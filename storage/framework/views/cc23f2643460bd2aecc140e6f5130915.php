<?php if($orders): ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên Khách hàng</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th>Ghi chú</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($order->id); ?></td>
                <td><?php echo e($order->od_quantity); ?></td>
                
                <td><?php echo e(number_format($order->od_price,0,',','.')); ?> VND</td>
                <td>
                    <a style="padding: 5px 10px; border: 1px solid #995" href="<?php echo e(route('cart.delete',$item->pro_id)); ?>"><i class="fa-solid fa-trash" style="font-size:11px"></i>Xoá</a>
                   
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php endif; ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\components\order.blade.php ENDPATH**/ ?>