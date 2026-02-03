<form action="" method="POST">
    <?php echo csrf_field(); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="name">Tên người quản trị</label>
                <input type="text" class="form-control" placeholder="Tên sản phẩm"
                    value="<?php echo e(old('name', isset($admin->name) ? $admin->name : '')); ?>" name="name">
                <?php if($errors->has('name')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('name')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="text">Email</label>
                <input type="text" class="form-control" placeholder="Email"
                    value="<?php echo e(old('email', isset($admin->email) ? $admin->email : '')); ?>" name="email">
                <?php if($errors->has('email')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('email')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" class="form-control" placeholder="Số điện thoại"
                    value="<?php echo e(old('phone', isset($admin->phone) ? $admin->phone : '')); ?>" name="phone">
                <?php if($errors->has('phone')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('phone')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="address">Mật khẩu</label>
                <input type="text" class="form-control" placeholder="Mật khẩu"
                    value="<?php echo e(old('password', isset($admin->password) ? $admin->password : '')); ?>" name="password">
                <?php if($errors->has('password')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('password')); ?>

                    </span>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    <button type="submit" class="btn btn-success">Lưu thông tin</button>
</form>
<?php $__env->stopSection(); ?>
<?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\account\form.blade.php ENDPATH**/ ?>