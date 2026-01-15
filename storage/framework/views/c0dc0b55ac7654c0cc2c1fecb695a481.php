<?php $__env->startSection('content'); ?>
<h1 class="page-header">Trang tổng quan</h1>
<div class="row">
    <!-- Có thể giữ lại các phần khác nếu cần, đã xóa phần biểu đồ doanh thu -->
</div>
<div class="row">
    <div class="col-sm-6">
        <h2>Danh sách đơn hàng mới</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $transactionNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($transaction->id); ?></td>
                        <td><?php echo e(isset($transaction->user->name) ? $transaction->user->name : '[N/A]'); ?></td>
                        <td><?php echo e($transaction->tr_phone); ?></td>
                        <td><?php echo e(number_format($transaction->tr_total,0,',','.')); ?> VND</td>
                        <td>
                            <?php if($transaction->tr_status == 1): ?>
                            <a href="" class="label-success label">Đã xử lý</a>
                        <?php elseif($transaction->tr_status == 2): ?>
                            <a href="" class="label label-danger">Đã huỷ</a>
                        
                        <?php else: ?>
                            <a href="<?php echo e(route('admin.get.active.transaction', $transaction->id)); ?>" class="label label-default">Đang chờ</a>

                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <?php echo $transactionNews->links(); ?>

        </table>
    </div>
    <div class="col-sm-6">
        <h2 class="sub-header">Danh sách đánh giá mới nhất</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên đánh giá</th>
                        <th>Sản phẩm</th>
                        <th>Nội dung</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($ratings)): ?>
                        <?php $__currentLoopData = $ratings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($rating->id); ?></td>
                            <td><?php echo e(optional($rating->user)->name ?? '[N/A]'); ?></td>
                            <td><?php echo e(optional($rating->product)->pro_name ?? '[N/A]'); ?></td>
                            <td><?php echo e($rating->ra_content); ?></td>
                            <td><?php echo e($rating->ra_number); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
 

<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules/Admin\resources/views/index.blade.php ENDPATH**/ ?>