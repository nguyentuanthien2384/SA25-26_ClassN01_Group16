<?php $__env->startSection('content'); ?>
<h1 class="page-header">Trang tổng quan</h1>
<div class="row">
    <div class="col-md-6">
        <h3>Biểu đồ doanh thu</h3>
        <div id="revenue-chart" style="height: 320px; background:#fff;"></div>
    </div>
    <div class="col-md-6">
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
            <tbody id="transactions-body">
                <?php $__currentLoopData = $transactionNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($transaction->id); ?></td>
                        <td><?php echo e(optional($transaction->user)->name ?? '[N/A]'); ?></td>
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
</div>
<div class="row">
    <div class="col-sm-6">
        <h2>Danh sách liên hệ mới nhất</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Tiêu đề</th>
                    <th>Nội dung</th>
                </tr>
            </thead>
            <tbody id="contacts-body">
                <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($contact->id); ?></td>
                        <td><?php echo e($contact->con_name); ?></td>
                        <td><?php echo e($contact->con_phone); ?></td>
                        <td><?php echo e($contact->con_email); ?></td>
                        <td><?php echo e($contact->con_title); ?></td>
                        <td><?php echo e($contact->con_message); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
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
                <tbody id="ratings-body">
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

<?php $__env->startSection('scripts'); ?>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        (function () {
            var dataMoney = <?php echo json_encode($dataMoney, 15, 512) ?>;
            window.revenueChart = Highcharts.chart('revenue-chart', {
                chart: { type: 'column' },
                title: { text: 'Biểu đồ doanh thu' },
                xAxis: { type: 'category' },
                yAxis: { title: { text: 'VND' } },
                series: [{
                    name: 'Giá trị',
                    data: dataMoney
                }],
                credits: { enabled: false }
            });
        })();
    </script>
    <script>
        (function () {
            var key = "<?php echo e(env('PUSHER_APP_KEY')); ?>";
            var cluster = "<?php echo e(env('PUSHER_APP_CLUSTER', 'mt1')); ?>";
            if (!key) return;

            var pusher = new Pusher(key, {
                cluster: cluster,
                forceTLS: true
            });
            var channel = pusher.subscribe('dashboard');
            channel.bind('dashboard.updated', function () {
                fetch("<?php echo e(route('admin.dashboard.data')); ?>", {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (window.revenueChart) {
                        window.revenueChart.series[0].setData(data.dataMoney, true);
                    }

                    var transactionsHtml = '';
                    data.transactions.forEach(function (item) {
                        var statusHtml = item.status_url
                            ? '<a href="' + item.status_url + '" class="label ' + item.status_class + '">Đang chờ</a>'
                            : '<a href="" class="label ' + item.status_class + '">' + item.status_text + '</a>';
                        transactionsHtml += '<tr>'
                            + '<td>' + item.id + '</td>'
                            + '<td>' + item.name + '</td>'
                            + '<td>' + (item.phone || '') + '</td>'
                            + '<td>' + item.total + '</td>'
                            + '<td>' + statusHtml + '</td>'
                            + '</tr>';
                    });
                    document.getElementById('transactions-body').innerHTML = transactionsHtml;

                    var contactsHtml = '';
                    data.contacts.forEach(function (item) {
                        contactsHtml += '<tr>'
                            + '<td>' + item.id + '</td>'
                            + '<td>' + (item.name || '') + '</td>'
                            + '<td>' + (item.phone || '') + '</td>'
                            + '<td>' + (item.email || '') + '</td>'
                            + '<td>' + (item.title || '') + '</td>'
                            + '<td>' + (item.message || '') + '</td>'
                            + '</tr>';
                    });
                    document.getElementById('contacts-body').innerHTML = contactsHtml;

                    var ratingsHtml = '';
                    data.ratings.forEach(function (item) {
                        ratingsHtml += '<tr>'
                            + '<td>' + item.id + '</td>'
                            + '<td>' + item.name + '</td>'
                            + '<td>' + item.product + '</td>'
                            + '<td>' + (item.content || '') + '</td>'
                            + '<td>' + item.number + '</td>'
                            + '</tr>';
                    });
                    document.getElementById('ratings-body').innerHTML = ratingsHtml;
                });
            });
        })();
    </script>
<?php $__env->stopSection(); ?>
 

<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules/Admin\resources/views/index.blade.php ENDPATH**/ ?>