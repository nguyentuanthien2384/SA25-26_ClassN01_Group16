<header id="sticky-menu" class="header">
    <div class="header-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 offset-md-4 col-7">
                    <div class="logo text-md-center">
                        <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset('img/logo/logo.png')); ?>" alt="Logo" style="height:100px" loading="lazy" /></a>
                    </div>
                </div>
                <div class="col-md-4 col-5">
                    <div class="input-group" style="top:43px">
                        <form action="<?php echo e(route('get.product.list')); ?>" method="GET" id="searchform" style="display: flex; align-items: center;">
                            <input type="text" class="form-control" name="k" placeholder="Tìm kiếm" style="flex: 1; margin-right: 5px; margin-bottom: 0">
                            <button type="submit" class="btn btn-default" style="font-size: 35px"><i class="zmdi zmdi-search"></i></button>
                        </form>
                    </div>
                    <div class="mini-cart text-end">
                        <ul>
                            <li>
                                <a class="cart-icon" href="<?php echo e(route('cart.index')); ?>">
                                    <i class="zmdi zmdi-shopping-cart"></i>
                                    <span><?php echo e($carts->sum('quantity')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MAIN-MENU START -->
    <div class="menu-toggle hamburger hamburger--emphatic d-none d-md-block">
        <div class="hamburger-box">
            <div class="hamburger-inner"></div>
        </div>
    </div>
    <div class="main-menu d-none d-md-block">
        <nav>
            <ul>
                <li><a href="<?php echo e(route('home')); ?>">Trang chủ</a></li>
                <li><a href="#">Sản phẩm</a>
                    <div class="mega-menu menu-scroll">
                        <div class="table">
                            <div class="table-cell">
                                <div class="half-width">
                                    <ul class="level1">
                                        
                                        <?php $__currentLoopData = $catParent ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parentCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="level1 first parent">
                                            <a class="active" href="<?php echo e(route('get.list.product',[$parentCat->c_slug,$parentCat->id])); ?>"><?php echo e($parentCat->c_name); ?></a>
                                            <?php if(isset($catChildren[$parentCat->id])): ?>
                                            <ul class="level2">
                                                <?php $__currentLoopData = $catChildren[$parentCat->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="level2 nav-2-1-1 first">
                                                    <a href="<?php echo e(route('get.list.product',[$childCat->c_slug,$childCat->id])); ?>"><?php echo e($childCat->c_name); ?></a>
                                                </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                            <?php endif; ?>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="<?php echo e(route('get.list.article')); ?>">Tin tức</a></li>
                <li><a href="">Giới thiệu</a></li>
                <li><a href="<?php echo e(route('contact.index')); ?>">Liên hệ</a></li>
            </ul>
        </nav>
    </div>
</header>
<?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/components/header.blade.php ENDPATH**/ ?>