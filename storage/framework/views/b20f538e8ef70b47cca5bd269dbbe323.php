<?php $__env->startSection('content'); ?>
<form action="" method="POST">
    <?php echo csrf_field(); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="name">Tên sản phẩm</label>
                <select name="product_id" id="" class="form-control">
                    <option value="">---Chọn Sản Phẩm---</option>
                    <?php if(isset($importgood)): ?>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($product->id); ?>" <?php echo e($product->id == $importgood->product_id ? 'selected' : ''); ?>>
                                <?php echo e($product->pro_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($product->id); ?>"><?php echo e($product->pro_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
                <?php if($errors->has('product_id')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('product_id')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="name">Tên Nhà cung cấp</label>
                <select name="supplier_id" id="" class="form-control">
                    <option value="">---Chọn nhà cung cấp---</option>
                    <?php if(isset($importgood)): ?>
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($supplier->id); ?>" <?php echo e($supplier->id == $importgood->supplier_id ? 'selected' : ''); ?>>
                                <?php echo e($supplier->s_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->s_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
                <?php if($errors->has('supplier_id')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('supplier_id')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="b_name">Giá nhập hàng</label>
                <input type="text" class="form-control" placeholder="Giá nhập"
                    value="<?php echo e(old('price', isset($importgood->price) ? $importgood->price : '')); ?>" name="price">
                <?php if($errors->has('price')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('price')); ?>

                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="b_name">Số lượng</label>
                <input type="text" class="form-control" placeholder="số lượng"
                    value="<?php echo e(old('quantity', isset($importgood->quantity) ? $importgood->quantity : '')); ?>" name="quantity">
                <?php if($errors->has('quantity')): ?>
                    <span class="error-text">
                        <?php echo e($errors->first('quantity')); ?>

                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success">Lưu thông tin</button>
</form>
  <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\import\create.blade.php ENDPATH**/ ?>