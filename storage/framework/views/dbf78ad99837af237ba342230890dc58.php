
<?php $__env->startSection('content'); ?>
<h1 class="page-header">Trang tổng quan</h1>
<div class="js-ajax-section" data-section="user-transactions">
<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">Tổng đơn hàng</div>
            <div class="panel-body">
                <h3 style="margin:0;"><?php echo e($totalTransaction); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-success">
            <div class="panel-heading">Đơn hàng đã xử lý</div>
            <div class="panel-body">
                <h3 style="margin:0;"><?php echo e($totalTransactionDone); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-warning">
            <div class="panel-heading">Đơn hàng chưa xử lý</div>
            <div class="panel-body">
                <h3 style="margin:0;"><?php echo e($totalTransaction - $totalTransactionDone); ?></h3>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2>Danh sách đơn hàng của bạn</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Địa chỉ</th>
                    <th>Số điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($transaction->id); ?></td>
                        <td><?php echo e(isset($transaction->user->address) ? $transaction->user->address : '[N/A]'); ?></td>
                        <td><?php echo e($transaction->tr_phone); ?></td>
                        <td><?php echo e(number_format($transaction->tr_total,0,',','.')); ?> VND</td>
                        <td>
                            <?php if($transaction->tr_status == 1): ?>
                                <a href="" class="label-success label">Đã xử lý</a>
                            
                            <?php else: ?>
                                <a href="#" class="label label-default">Đang chờ</a>
                        
                            
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($transaction->created_at); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="pagination-wrap text-center">
            <?php echo $transactions->appends(request()->query())->links('components.pagination'); ?>

        </div>
    </div>
</div>
</div>

            </table>
        </div>
    </div>
    

<?php $__env->stopSection(); ?>
<?php echo $__env->make('user.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/user/index.blade.php ENDPATH**/ ?>