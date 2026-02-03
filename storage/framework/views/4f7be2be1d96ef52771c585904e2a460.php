<?php $__env->startSection('content'); ?>
    <div class="login-area  pt-80 pb-80">
        <div class="container">
            
            <form id="login-form" class="form" action="<?php echo e(route('login')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="customer-login text-left">
                            <h4 class="title-1 title-border text-uppercase mb-30" style="text-align: center">ĐĂNG NHẬP NGƯỜI DÙNG</h4>
                            <?php if($message = Session::get('error')): ?>
                                <div class="alert alert-success alert-block">
	                                <button type="button" class="close" data-dismiss="alert">×</button>	
                                    <strong><?php echo e($message); ?></strong>
                                </div>
                            <?php endif; ?>  
                            <p class="text-gray">Nếu bạn có tài khoản với chúng tôi, vui lòng đăng nhập!</p>
                            <input type="text" placeholder="Email here..." name="email">
                            <input type="password" placeholder="Password" name="password">
                            <p><a href="#" class="text-gray">Quên mật khẩu?</a></p>
                            <button type="submit" data-text="login" class="button-one submit-button mt-15">DĂNG NHẬP</button>
                        </div>	

                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views\auth\login.blade.php ENDPATH**/ ?>