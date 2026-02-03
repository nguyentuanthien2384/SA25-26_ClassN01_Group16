<form action="" method="POST">
    <?php echo csrf_field(); ?>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="a_name">Tên bài viết</label>
                <input type="text" class="form-control" placeholder="Tên bài viết"
                    value="<?php echo e(old('a_name', isset($article->a_name) ? $article->a_name : '')); ?>" name="a_name">
                <?php if($errors->has('a_name')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('a_name')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="name">Mô tả</label>
                <textarea name="a_description" class="form-control" id="ckeditor" cols="30" rows="3"
                    placeholder="Mô tả ngắn" value=""><?php echo e(old('a_description', isset($article->a_description) ? $article->a_description : '')); ?></textarea>
                <?php if($errors->has('a_description')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('a_description')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="name">Nội dung</label>
                <textarea name="a_content" class="form-control" id="ckeditor" cols="30" rows="3"
                    placeholder="Nội dung bài viết"><?php echo e(old('a_content', isset($article->a_content) ? $article->a_content : '')); ?></textarea>
                <?php if($errors->has('a_content')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('a_content')); ?>

                    </span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="name">Hình ảnh</label>
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" id="lfm" data-input="thumbnail" data-preview="holder"
                            class="btn btn-primary">Thêm ảnh</button>
                    </div>
                    <input type="text" class="form-control" id="thumbnail" name="a_avatar"
                        value="<?php echo e(old('a_avatar', isset($article->a_avatar) ? $article->a_avatar : '')); ?>" required>
                    <?php $__errorArgs = ['a_avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="form-group" id="holder">
                <img id="holder" style="width: 100%;
              height: 15rem;
              object-fit: contain;"
                    src="<?php echo e(old('a_avatar', isset($article->a_avatar) ? $article->a_avatar : '')); ?>"
                    alt="Selected Image">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success">Lưu thông tin</button>
</form>
<?php $__env->startSection('scripts'); ?>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="/vendor/laravel-filemanager/js/filemanager.min.js"></script>

    <script src="<?php echo e(asset('/js/jquery-ui.min.js')); ?>"></script>
    <script>
        var route_prefix = "/laravel-filemanager";
        $('#lfm').filemanager('image', {
            prefix: route_prefix,
        });
    </script>

    <!-- CKEditor init -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.11/ckeditor.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.11/adapters/jquery.js"></script>
    <script>
        $('textarea[id=ckeditor]').ckeditor({
            height: 100,
            filebrowserImageBrowseUrl: route_prefix + '?type=Images',
            filebrowserImageUploadUrl: route_prefix + '/upload?type=Images&_token=<?php echo e(csrf_token()); ?>',
            filebrowserBrowseUrl: route_prefix + '?type=Files',
            filebrowserUploadUrl: route_prefix + '/upload?type=Files&_token=<?php echo e(csrf_token()); ?>'
        });
    </script>
<?php $__env->stopSection(); ?>
<?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\article\form.blade.php ENDPATH**/ ?>