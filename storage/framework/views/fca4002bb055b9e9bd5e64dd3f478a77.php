<?php if(isset($banners) && count($banners) > 0): ?>
<section class="slider-banner-area clearfix">
    <div class="slider-right floatleft">
        <div class="slider-area">
            <div class="bend niceties preview-2">
                <div id="ensign-nivoslider" class="slides">
                    <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <img src="<?php echo e($banner->b_image); ?>" alt="" title="#slider-direction-1" />
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-search animated slideOutUp">
        <div class="table">
            <div class="table-cell">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 offset-md-2 p-0">
                            <div class="search-form-wrap">
                                <button class="close-search"><i class="zmdi zmdi-close"></i></button>
                                <form action="#">
                                    <input type="text" placeholder="Search here..." />
                                    <button class="search-button" type="submit">
                                        <i class="zmdi zmdi-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-account d-none d-md-block" style="margin-top: 10px">
        <div class="table">
            <div class="table-cell">
                <ul>
                    <?php if(Auth::check()): ?>
                    <li><a href="<?php echo e(route('user.index')); ?>" title="My-Account"><i class="zmdi zmdi-account"></i></a></li>
                    <li><a href="<?php echo e(route('logout')); ?>"><i class="zmdi zmdi-close"></i></a></li>
                    <?php else: ?>
                    <li><a href="<?php echo e(route('admin.login')); ?>" title="My-Account"><i class="zmdi zmdi-account-circle"></i></a></li>
                    <li><a href="<?php echo e(route('login')); ?>"><i class="zmdi zmdi-account"></i></a></li>
                    <li><a href="<?php echo e(route('register')); ?>"><i class="zmdi zmdi-account-add"></i></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
<?php else: ?>
<div class="d-none d-md-block" style="position:fixed; right:0; top:50%; transform:translateY(-50%); z-index:100; background:#fff; border-radius:8px 0 0 8px; box-shadow:-2px 0 12px rgba(0,0,0,.1); padding:10px 8px;">
    <ul style="list-style:none; margin:0; padding:0; text-align:center;">
        <?php if(Auth::check()): ?>
        <li style="margin-bottom:8px;"><a href="<?php echo e(route('user.index')); ?>" title="Tài khoản"><i class="zmdi zmdi-account" style="font-size:20px;color:#333;"></i></a></li>
        <li><a href="<?php echo e(route('logout')); ?>" title="Đăng xuất"><i class="zmdi zmdi-close" style="font-size:20px;color:#e53935;"></i></a></li>
        <?php else: ?>
        <li style="margin-bottom:8px;"><a href="<?php echo e(route('admin.login')); ?>" title="Admin"><i class="zmdi zmdi-account-circle" style="font-size:20px;color:#333;"></i></a></li>
        <li style="margin-bottom:8px;"><a href="<?php echo e(route('login')); ?>" title="Đăng nhập"><i class="zmdi zmdi-account" style="font-size:20px;color:#333;"></i></a></li>
        <li><a href="<?php echo e(route('register')); ?>" title="Đăng ký"><i class="zmdi zmdi-account-add" style="font-size:20px;color:#333;"></i></a></li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
<?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/components/button.blade.php ENDPATH**/ ?>