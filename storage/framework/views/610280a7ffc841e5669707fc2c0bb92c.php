<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/dashboard/">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>ADMIN SYSTEM</title>
    <!-- Bootstrap core CSS -->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">

    <link href="<?php echo e(asset('public/theme_admin/css/bootstrap.min.css')); ?>" type="text/javascript">
    <script src="<?php echo e(asset('js/jquery-3.4.1.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('theme_admin/js/bootstrap.min.js')); ?>" type="text/javascript"></script>
    <script src="https://kit.fontawesome.com/550b63b5bc.js" crossorigin="anonymous"></script>
    
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.js"></script>

    
    
    <!-- Custom styles for this template -->
    <link href="<?php echo e(asset('theme_admin/css/dashboard.css')); ?>" rel="stylesheet">
    <style>
        .action-cell {
            white-space: nowrap;
        }
        .action-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .action-buttons a,
        .action-buttons button {
            padding: 4px 10px;
            border: 1px solid #c9b56f;
            background: #fff;
            border-radius: 4px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .action-buttons i {
            font-size: 11px;
        }
        .pagination-wrap {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination-custom {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .pagination-custom ul {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .pagination-custom li span,
        .pagination-custom li a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
            color: #333;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .pagination-custom li a:hover {
            border-color: #4b3b7a;
            color: #4b3b7a;
            background: #f8f8f8;
        }
        .pagination-custom li.active span {
            background: #4b3b7a;
            border-color: #4b3b7a;
            color: #fff;
        }
        .pagination-custom li.disabled span {
            color: #bbb;
            background: #f5f5f5;
            border-color: #e8e8e8;
            cursor: not-allowed;
        }
        .pagination-custom .pagination-prev,
        .pagination-custom .pagination-next {
            font-weight: 700;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo e(route('home')); ?>">Trang người dùng</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    
                    <li><a href="#">Xin chào: <?php echo e(get_data_user('admins','name')); ?></a></li>
                    <li><a href="<?php echo e(route('admin.logout')); ?>">Đăng xuất</a></li>
                </ul>
                
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar" style="background: rgb(21, 26, 32)">
                <ul class="nav nav-sidebar" style="font-weight:750;font-size: 18px">
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.home' ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('admin.home')); ?>"><i class="fa fa-home" aria-hidden="true"></i> TRANG CHỦ</a>
                    </li>
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.category' ? 'active' : ''); ?>">
                        <a href="<?php echo e(Route('admin.get.list.category')); ?>"  ><i class="fa fa-list" aria-hidden="true"></i> DANH MỤC</a>
                    </li>
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.product' ? 'active' : ''); ?>"><a
                            href="<?php echo e(Route('admin.get.list.product')); ?>"><i class="fa fa-indent" aria-hidden="true"></i> SẢN PHẨM</a></li>
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.warehouse' ? 'active' : ''); ?>"><a
                             href="<?php echo e(Route('admin.get.warehouse')); ?>"><i class="fa fa-bar-chart" aria-hidden="true"></i> KHO HÀNG</a></li>
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.supplier' ? 'active' : ''); ?>"><a
                            href="<?php echo e(Route('admin.get.list.supplier')); ?>"><i class="fa fa-bar-chart" aria-hidden="true"></i> NHÀ CUNG CẤP</a></li>
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.import' ? 'active' : ''); ?>"><a
                        href="<?php echo e(Route('admin.get.list.import')); ?>"><i class="fa fa-bar-chart" aria-hidden="true"></i> NHẬP HÀNG</a></li>
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.rating' ? 'active' : ''); ?>"><a
                            href="<?php echo e(Route('admin.get.list.rating')); ?>"><i class="fa fa-star" aria-hidden="true"></i> ĐÁNH GIÁ</a></li>
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.article' ? 'active' : ''); ?>"><a
                            href="<?php echo e(Route('admin.get.list.article')); ?>"><i class="fa fa-tags" aria-hidden="true"></i> TIN TỨC</a></li>
                     <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.transaction' ? 'active' : ''); ?>"><a
                            href="<?php echo e(Route('admin.get.list.transaction')); ?>"><i class="fa fa-newspaper-o" aria-hidden="true"></i> ĐƠN HÀNG</a></li>

                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.user' ? 'active' : ''); ?>"><a
                            href="<?php echo e(Route('admin.get.list.user')); ?>"><i class="fa fa-user" aria-hidden="true"></i> THÀNH VIÊN</a></li>
                    <li class="<?php echo e(\Request::route()->getName() == 'account.index' ? 'active' : ''); ?>"><a
                            href="<?php echo e(Route('account.index')); ?>"><i class="fa fa-user-circle" aria-hidden="true"></i> TÀI KHOẢN</a></li>
                    <li class="<?php echo e(\Request::route()->getName() == 'admin.get.list.contact' ? 'active' : ''); ?>"><a
                            href="<?php echo e(Route('admin.get.list.contact')); ?>"><i class="fa fa-volume-control-phone" aria-hidden="true"></i> LIÊN HỆ</a></li>
                </ul>

            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?php if(\Session::has('success')): ?>
                <div class="alert alert-success alert-dismissible" style="position: fixed; right:200px">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success!</strong> <?php echo e(\Session::get('success')); ?>

                  </div>
                  <?php endif; ?>
                  <?php if(\Session::has('danger')): ?>
                  <div class="alert alert-danger alert-dismissible" style="position: fixed; right:200px">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Success!</strong> <?php echo e(\Session::get('danger')); ?>

                    </div>
                    <?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript
            ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="<?php echo e(asset('theme_admin/js/bootstrap.min.js')); ?>"></script>
    
    <script src="<?php echo e(asset('js/jquery-ui.min.js')); ?>" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>

    
    <?php echo $__env->yieldContent('scripts'); ?>
    <script>
        function readURL(input) {
            if (input.files && input.flies[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#out_img').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("input_img").change(function() {
            readURL(this);
        });
    </script>
    <script>
        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 5000); // 5 giây

    </script>
    <script>
        $(document).on('click', 'a[data-confirm], a[href*="delete"]', function (e) {
            var message = $(this).data('confirm') || 'Bạn có chắc muốn xoá?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
<?php /**PATH D:\Web_Ban_Do_Dien_Tu\Modules\Admin\resources\views\layouts\master.blade.php ENDPATH**/ ?>