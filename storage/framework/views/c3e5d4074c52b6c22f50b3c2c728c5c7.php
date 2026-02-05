<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <form action="#" method="POST">
                <?php echo csrf_field(); ?>
            <div class="customer-login text-left">
                <h4 class="title-1 title-border text-uppercase mb-30" style="text-align: center">ĐĂNG KÝ TÀI KHOẢN</h4>
                <p class="text-gray">Nếu bạn có tài khoản với chúng tôi, vui lòng đăng nhập!</p>
                <input type="text" placeholder="Your name here..." name="name">
                <input type="text" placeholder="Email address here..." name="email">
                <input type="text" placeholder="Số điện thoại here..." name="phone">
                <input type="text" placeholder="Address here..." name="address">
                <input type="text" placeholder="Password" name="password">
                <input type="text" placeholder="Confirm password">
                <p class="mb-0">
                    <input type="checkbox" id="newsletter" name="newsletter" checked>
                    <label for="newsletter"><span>Sign up for our newsletter!</span></label>
                </p>
                <button type="submit" data-text="regiter" class="button-one submit-button mt-15">ĐĂNG KÝ</button>
            </div>
            </form>					
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/auth/register.blade.php ENDPATH**/ ?>