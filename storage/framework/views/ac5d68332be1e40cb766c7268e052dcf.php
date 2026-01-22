
<?php $__env->startSection('content'); ?>

<div class="elements-tab pb-80">
    <div class="blog-area blog-2 pt-30">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h2 class="title-border">Thanh toán</h2>
                    </div>
                </div>
            </div>
            <div class="row" style="color:black; font-size:15px">
                <div class="col-md-8 col-md-offset-2">
                    <div style="border:1px solid #eee; padding:20px; background:#fafafa;">
                        <p><strong>Mã đơn hàng:</strong> #<?php echo e($transaction->id); ?></p>
                        <p><strong>Tổng tiền:</strong> <?php echo e(number_format($transaction->tr_total)); ?> VND</p>
                        <p><strong>Phương thức:</strong> <?php echo e(strtoupper($method)); ?></p>

                        <?php if($method === 'qrcode'): ?>
                            <p>Quét QR để thanh toán.</p>
                            <?php if($qrBank && $qrAccount && $qrName): ?>
                                <p style="margin-bottom:5px;">
                                    <strong>Ngân hàng:</strong> <?php echo e($qrBank); ?> |
                                    <strong>Số TK:</strong> <?php echo e($qrAccount); ?> |
                                    <strong>Chủ TK:</strong> <?php echo e($qrName); ?>

                                </p>
                            <?php else: ?>
                                <p style="color:#c00;">Chưa cấu hình VietQR. Đang dùng QR tạm.</p>
                            <?php endif; ?>
                            <?php if($qrUrl): ?>
                                <img src="<?php echo e($qrUrl); ?>" alt="QR Code">
                                <p style="margin-top:10px;"><small><?php echo e($qrData); ?></small></p>
                            <?php endif; ?>
                        <?php elseif($method === 'momo'): ?>
                            <p>MoMo sandbox: bấm để chuyển sang cổng MoMo.</p>
                        <?php elseif($method === 'paypal'): ?>
                            <p>PayPal sandbox: bấm để chuyển sang cổng PayPal.</p>
                        <?php elseif($method === 'vnpay'): ?>
                            <p>VNPay sandbox: bấm để chuyển sang cổng VNPay.</p>
                        <?php endif; ?>

                        <?php if($method === 'qrcode'): ?>
                            <form method="POST" action="<?php echo e(route('payment.qrcode.confirm', ['transaction' => $transaction->id])); ?>" style="margin-top:15px;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="button-one">Xác nhận đã thanh toán</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="<?php echo e(route('payment.init', ['method' => $method, 'transaction' => $transaction->id])); ?>" style="margin-top:15px;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="button-one">Thanh toán ngay</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top:15px;">
                        <a href="<?php echo e(route('home')); ?>">Quay về trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/payment/show.blade.php ENDPATH**/ ?>