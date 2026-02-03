<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="<?php echo e(route('admin.get.list.transaction')); ?>">Đơn hàng</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <form action="<?php echo e(route('admin.get.list.transaction')); ?>" method="GET">
        <div class="form-group">
            <label for="searchInput">Tìm kiếm theo số điện thoại:</label>
            <input type="text" class="form-control" id="searchInput" name="l" placeholder="Nhập số điện thoại...">
        </div>
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
    </form>
    <div class="table-responsive">
        <h2>QUẢN LÝ ĐƠN HÀNG <a href="<?php echo e(route('admin.get.create.article')); ?>" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Khách hàng</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th>Ghi chú</th>
                        <th>Tổng tiền</th>
                        <th>Ngày mua</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($transaction->id); ?></td>
                            <td><?php echo e(isset($transaction->user->name) ? $transaction->user->name : '[N/A]'); ?></td>
                            <td><?php echo e($transaction->tr_address); ?></td>
                            <td><?php echo e($transaction->tr_phone); ?></td>
                            <td><?php echo e($transaction->tr_note); ?></td>
                            <td><?php echo e(number_format($transaction->tr_total,0,',','.')); ?> VND</td>
                            <td><?php echo e($transaction->created_at->format('d-m-Y')); ?></td>
                            <td>
                                <?php if($transaction->tr_status == 1): ?>
                                    <a href="" class="label-success label">Đã xử lý</a>
                                <?php elseif($transaction->tr_status == 2): ?>
                                    <a href="" class="label label-danger">Đã huỷ</a>
                                
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.get.active.transaction', $transaction->id)); ?>" class="label label-default">Đang chờ</a>

                                <?php endif; ?>
                            </td>
                             <td class="action-cell">
                                <div class="action-buttons">
                                    <a href="<?php echo e(route('update.transaction',$transaction->id)); ?>"><i class="fa-solid fa-pen"></i>Chỉnh sửa</a>
                                    <a href="<?php echo e(route('admin.get.delete.transaction',$transaction->id)); ?>"><i class="fa-solid fa-trash"></i>Xoá</a>
                                    <a class="js_order_item" data-id="<?php echo e($transaction->id); ?>" href="<?php echo e(route('admin.get.view.order',$transaction->id)); ?>"><i class="fa-solid fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="pagination-wrap">
                <?php echo $transactions->links('components.pagination'); ?>

            </div>
        </div>
        <div id="myModalOrder" class="modal fade" role="dialog">
            <div class="modal-dialog">
          
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">
                  <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
          
            </div>
          </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
          <script>
            $(function(){
                $(".js_order_item").click(function(event){
                    event.preventDefault();
                    let $this = $(this);
                    let url = $this.attr('href');
                    $("#myModalOrder").modal('show');
                    console.log(url);
                });
            })
          </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\transaction\index.blade.php ENDPATH**/ ?>