<section class="slider-banner-area clearfix">
    
    <div class="slider-right floatleft">
        <!-- Slider-area start -->
        <div class="slider-area">
            <div class="bend niceties preview-2">
                <?php if(isset($banners)): ?>
                <div id="ensign-nivoslider" class="slides">
                    <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <img src="<?php echo e($banner->b_image); ?>" alt="" title="#slider-direction-1"  />
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Slider-area end -->
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
    <!-- Sidebar-social-media start -->
    <div class="sidebar-account d-none d-md-block" style="margin-top: 10px">
        <div class="table">
            
            <div class="table-cell">
                <ul>
                    <?php if(Auth::check()): ?>
                    <li><a href="<?php echo e(route('user.index')); ?>" title="My-Account"><i class="zmdi zmdi-account"></i></a></li>
                    <li ><a href="<?php echo e(route('logout')); ?>"><i class="zmdi zmdi-close" ></i> </a></li>
                    </li>
                    <?php else: ?>
                    <li><a href="<?php echo e(route('admin.login')); ?>" title="My-Account"><i class="zmdi zmdi-account-circle"></i></a></li>
                    <li><a href="<?php echo e(route('login')); ?>"><i class="zmdi zmdi-account"></i></a></li>
                    <li><a href="<?php echo e(route('register')); ?>"><i class="zmdi zmdi-account-add"></i></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- Sidebar-social-media start -->
    
</section><?php /**PATH /var/www/resources/views/components/button.blade.php ENDPATH**/ ?>