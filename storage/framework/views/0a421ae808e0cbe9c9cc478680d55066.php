<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.home')); ?>">Trang chủ</a></li>
            <li><a href="">Bài viết</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ BÀI VIẾT <a href="<?php echo e(route('admin.get.create.article')); ?>" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="width: 250px">Tên bài viết</th>
                    <th style=" width: 300px ">Mô tả</th>
                    <th>Hình ảnh</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if( isset($articles)): ?>
                    <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($article->id); ?></td>
                        <td>
                            <?php echo e($article->a_name); ?>

                        </td>
                        <td><?php echo e($article->a_description); ?></td>
                        <td>
                            <img src="<?php echo e(isset($article->a_avatar) ? $article->a_avatar :'[N\A]'); ?>" alt="" style="max-width:60px;height: auto;">
                        </td>
                        <td>
                            <a href="<?php echo e(route('admin.get.action.article',['active',$article->id])); ?>"class="label <?php echo e($article->getStatus($article->a_active)['class']); ?>"><?php echo e($article->getStatus($article->a_active)['name']); ?></a>
                        </td>
                        <td><?php echo e($article->created_at->format('d-m-Y')); ?></td>
                
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="<?php echo e(route('admin.get.edit.article',$article->id)); ?>"><i class="fa-solid fa-pen"></i>Chỉnh sửa</a>
                                <a href="<?php echo e(route('admin.get.action.article',['delete',$article->id])); ?>"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>                
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
            </tbody>
        </table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\article\index.blade.php ENDPATH**/ ?>