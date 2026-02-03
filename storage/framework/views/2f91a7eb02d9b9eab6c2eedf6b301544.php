
<?php $__env->startSection('content'); ?>
<div class="contact-us-area  pt-80 pb-80">
    <div class="container">
        <div class="contact-us customer-login bg-white">
            <div class="row">
                <div class="col-lg-4 col-md-5">
                    <div class="contact-details">
                        <h4 class="title-1 title-border text-uppercase mb-30">Chi tiết liên hệ</h4>
                        <ul>
                            <li>
                                <i class="zmdi zmdi-pin"></i>

                            </li>
                            <li>
                                <i class="zmdi zmdi-phone"></i>

                                <span>+0123456789</span>
                            </li>
                            <li>
                                <i class="zmdi zmdi-email"></i>
                                <span>company-email@gmail.com</span>
                                <span>your-email@gmail.com</span>
                            </li>
                        </ul>
                    </div>
                    <div class="send-message mt-60">
                        <h4 class="title-1 title-border text-uppercase mb-30">Mời bạn điền thông tin</h4>
                        <form id="" action="" method="post">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="con_name" placeholder="Your name here..." required/>
                            <input type="text" name="con_phone" placeholder="Your phone here..." required/>
                            <input type="text" name="con_email" placeholder="Your email here..." required/>
                            <input type="text" name="con_title" placeholder="Title here..." required/>
                            <textarea class="custom-textarea" name="con_message" placeholder="Your comment here..."></textarea>
                            <button class="button-one submit-button mt-20" data-text="submit message" type="submit">Gửi thông tin</button>
                            <p class="form-message"></p>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7 mt-xs-30">
                    <div class="map-area">
                        <div id="googleMap" style="width:100%;height:600px;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3780.1102349383623!2d105.69317477424087!3d18.659048664931483!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3139cddf0bf20f23%3A0x86154b56a284fa6d!2sVinh%20University!5e0!3m2!1sen!2s!4v1713457646739!5m2!1sen!2s" width="700px" height="550px" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views\contact.blade.php ENDPATH**/ ?>