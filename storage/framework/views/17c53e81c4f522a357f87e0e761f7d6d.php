<form action="" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <div class="row">
        <div class="col-sm-8">
            <div class="form-group">
                <label for="pro_name">Tên sản phẩm</label>
                <input type="text" class="form-control" placeholder="Tên sản phẩm"
                    value="<?php echo e(old('pro_name', isset($product->pro_name) ? $product->pro_name : '')); ?>" name="pro_name">
                <?php if($errors->has('pro_name')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('pro_name')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="name">Mô tả ngắn</label>
                <textarea name="pro_description" class="form-control" id="ckeditor" cols="30" rows="3"
                    placeholder="Mô tả ngắn" value=""><?php echo e(old('pro_description', isset($product->pro_description) ? $product->pro_description : '')); ?></textarea>
                <?php if($errors->has('pro_name')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('pro_name')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="name">Mô tả sản phẩm</label>
                <textarea name="pro_content" class="form-control" id="ckeditor" cols="30" rows="3"
                    placeholder="Mô tả sản phẩm" value=""><?php echo e(old('pro_content', isset($product->pro_content) ? $product->pro_content : '')); ?></textarea>
                <?php if($errors->has('pro_name')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('pro_name')); ?>

                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="name">Loại Sản Phẩm</label>
                <select name="pro_category_id" id="" class="form-control">
                    <option value="">---Chọn Loại Sản Phẩm---</option>
                    <?php if(isset($product)): ?>
                        <?php $__currentLoopData = $categorys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>"
                                <?php echo e($category->id == $product->pro_category_id ? 'selected' : ''); ?>>
                                <?php echo e($category->c_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php if(isset($categorys)): ?>
                            <?php $__currentLoopData = $categorys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>"><?php echo e($category->c_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </select>
                <?php if($errors->has('pro_category_id')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('pro_category_id')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="pro_price">Giá Sản Phẩm</label>
                <input type="number" placeholder="Giá sản phẩm" class="form-control" name="pro_price"
                    value="<?php echo e(old('pro_price', isset($product->pro_price) ? $product->pro_price : '')); ?>">
                <?php if($errors->has('pro_price')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('pro_price')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="name">Giá khuyến mãi</label>
                <input type="number" placeholder="Giảm giá %" class="form-control" name="pro_sale" 
                value="<?php echo e(old('pro_sale', isset($product->pro_sale) ? $product->pro_sale : '')); ?>">
            </div>
            <div class="form-group">
                <label for="name">Số lượng</label>
                <input type="number" placeholder="Nhập số lượng" class="form-control" name="quantity" 
                value="<?php echo e(old('quantity', isset($product->quantity) ? $product->quantity : '')); ?>">
            </div>
            
            
            <div class="form-group">
                <label for="name">Hình ảnh</label>
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" id="lfm" data-input="thumbnail" data-preview="holder"
                            class="btn btn-primary">Thêm ảnh</button>
                    </div>
                    <input type="text" class="form-control" id="thumbnail" name="pro_image"
                        value="<?php echo e(old('pro_image', isset($product->pro_image) ? $product->pro_image : '')); ?>" required>
                    <?php $__errorArgs = ['pro_image'];
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
            <div class="form-group">
                <label for="">Ảnh chi tiết</label>
                <input type="file" class="form-control" id="img_detail" name="img_detail[]" multiple="">
            </div>
            <div class="form-group" id="holder">
                <img id="holder"
                    style="width: 100%;
                height: 100%;
                object-fit: contain;"
                    src="<?php echo e(old('pro_image', isset($product->pro_image) ? $product->pro_image : '')); ?>"
                    alt="Selected Image">
            </div>
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="pro_hot">Nổi bật
                    </label>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success">Lưu thông tin</button>
</form>
<?php $__env->startSection('scripts'); ?>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="/vendor/laravel-filemanager/js/filemanager.min.js"></script>

    <script src="<?php echo e(asset('/js/plugins/jquery-ui-1.13.2/jquery-ui.min.js')); ?>"></script>
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
<?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules/Admin\resources/views/product/form.blade.php ENDPATH**/ ?>