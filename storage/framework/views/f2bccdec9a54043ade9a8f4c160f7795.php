
<?php $__env->startSection('content'); ?>

<!-- HEADING-BANNER END -->
<!-- BLGO-AREA START -->
<div class="blog-area blog-2 blog-details-area  pt-80 pb-80">
    <div class="container">	
        <div class="blog">
            <div class="row">
                <!-- Single-blog start -->
               
                <div class="col-lg-12">
                    <div class="single-blog mb-30">
                        <div class="blog-photo">
                            <a href="#"><img src="<?php echo e($articless->a_avatar); ?>" alt="" /></a>
                            <div class="">
                                <span class="text-dark-red text-uppercase"><?php echo e($articless->created_at->format('d-m-Y')); ?></span>
                            </div>
                        </div>
                        <div class="blog-info blog-details-info">
                            <h4 class="post-title post-title-2"><a href="#"></a><?php echo e($articless->a_name); ?></h4>
                            <p><?php echo $articless->a_content; ?></p>									
                        </div>
                        
                    </div>
                </div>
                <!-- Single-blog end -->
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/article/detail.blade.php ENDPATH**/ ?>