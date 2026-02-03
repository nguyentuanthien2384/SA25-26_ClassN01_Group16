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
    <title>Trang tổng quan User</title>
    <!-- Bootstrap core CSS -->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">

    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.css" rel="stylesheet">
    <link href="{{ asset('public/theme_admin/css/bootstrap.min.css') }}" type="text/javascript"></>
    <link href="{{ asset('public/summernote/summernote.min.css') }}" type="text/javascript"></>
    {{-- <script src="{{ asset('public/summernote/summernote.min.js')}}" type="text/javascript"></></script> --}}
    <script src="{{ asset('summernote/summernote.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ asset('public/js/jquery-3.4.1.min.js')}}"type="text/javascript"></></script> --}}
    <script src="{{ asset('theme_admin/js/bootstrap.min.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ asset('public/theme_admin/js/bootstrap.min.js')}}"type="text/javascript"></></script> --}}
    <script src="https://kit.fontawesome.com/550b63b5bc.js" crossorigin="anonymous"></script>
    {{-- <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet"> --}}
    <!-- Custom styles for this template -->
    <link href="{{ asset('theme_admin/css/dashboard.css') }}" rel="stylesheet">
    <style>
        .pagination-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
            margin-top: 20px;
        }
        .btn-view-all {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            padding: 12px 24px;
            background: #4b3b7a;
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
        }
        .pagination-custom {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .pagination-custom ul {
            display: flex;
            align-items: center;
            gap: 14px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .pagination-custom li span,
        .pagination-custom li a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 4px;
            border: none;
            border-radius: 0;
            background: transparent;
            color: #3a476a;
            font-weight: 500;
            text-decoration: none;
        }
        .pagination-custom li a:hover {
            color: #2f3f75;
        }
        .pagination-custom li.active span {
            color: #2f3f75;
            border-bottom: 2px solid #4b5bff;
            padding-bottom: 4px;
        }
        .pagination-custom li.disabled span {
            color: #c0c6d4;
        }
        .pagination-custom .pagination-prev span,
        .pagination-custom .pagination-prev a,
        .pagination-custom .pagination-next span,
        .pagination-custom .pagination-next a {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 50%;
            background: #eaf1ff;
            color: #6a7bb6;
            font-size: 16px;
        }
        .pagination-custom .pagination-prev a:hover,
        .pagination-custom .pagination-next a:hover {
            background: #dfe9ff;
        }
        .js-ajax-section {
            transition: opacity 180ms ease;
            position: relative;
        }
        .js-ajax-section.is-loading {
            opacity: 0.6;
        }
        .js-ajax-section.is-fade-in {
            opacity: 0;
        }
        .js-ajax-section.is-fixed-height {
            overflow: hidden;
        }
        .js-ajax-section .ajax-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            backdrop-filter: blur(2px);
        }
        .js-ajax-section .ajax-skeleton-lines {
            width: 90%;
            max-width: 560px;
            display: grid;
            gap: 10px;
        }
        .js-ajax-section .skeleton-block {
            position: relative;
            overflow: hidden;
            background: #edf1f7;
            border-radius: 8px;
            height: 12px;
        }
        .js-ajax-section .skeleton-block::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0) 100%);
            transform: translateX(-100%);
            animation: shimmer 1.1s infinite;
        }
        .js-ajax-section .skeleton-block.wide {
            height: 14px;
        }
        .js-ajax-section .skeleton-block.short {
            width: 65%;
        }
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{route('home')}}">Trang chủ</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    {{-- <li><a href="#">Dashboard</a></li>
                    <li><a href="#">Settings</a></li> --}}
                    <li><a href="#">Xin chào: {{get_data_user('web','name')}}</a></li>
                    <li><a href="{{route('logout')}}">Đăng xuất</a></li>
                </ul>
                {{-- <form class="navbar-form navbar-right">
                    <input type="text" class="form-control" placeholder="Search...">
                </form> --}}
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <li class="{{ \Request::route()->getName() == 'admin.home' ? 'active' : '' }}">
                        <a href="{{ route('home') }}">TRANG CHỦ</a>
                    </li>
                </ul>

            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                @if(\Session::has('success'))
                <div class="alert alert-success alert-dismissible" style="position: fixed; right:200px">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success!</strong> {{\Session::get('success')}}
                  </div>
                  @endif
                  @if(\Session::has('danger'))
                  <div class="alert alert-danger alert-dismissible" style="position: fixed; right:200px">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Success!</strong> {{\Session::get('danger')}}
                    </div>
                    @endif
                @yield('content')
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript
            ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="{{ asset('theme_admin/js/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ asset('public/js/jquery-ui.min.js') }}"></script> --}}
    <script src="{{ asset('js/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>
   
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    @yield('scripts')
    <script>
        $(document).on('click', 'a[data-confirm], a[href*="delete"], a[href*="remove"]', function (e) {
            var message = $(this).data('confirm') || 'Bạn có chắc muốn xoá?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    </script>
    <script>
        (function () {
            function buildSkeletonLines() {
                return '<div class="ajax-overlay"><div class="ajax-skeleton-lines">' +
                    '<div class="skeleton-block wide"></div>' +
                    '<div class="skeleton-block"></div>' +
                    '<div class="skeleton-block"></div>' +
                    '<div class="skeleton-block short"></div>' +
                '</div></div>';
            }

            function loadSection($trigger) {
                var $section = $trigger.closest('.js-ajax-section');
                if (!$section.length) return;

                var url = $trigger.attr('href');
                if (!url) return;

                var sectionId = $section.data('section');
                var fixedHeight = $section.outerHeight();
                $section.addClass('is-loading is-fixed-height').css('min-height', fixedHeight);
                if (!$section.find('.ajax-overlay').length) {
                    $section.append(buildSkeletonLines());
                }
                $.get(url, function (html) {
                    var $newSection = $(html)
                        .find('.js-ajax-section[data-section="' + sectionId + '"]')
                        .first();

                    if (!$newSection.length) {
                        window.location.href = url;
                        return;
                    }

                    $newSection.addClass('is-fade-in').css('min-height', fixedHeight);
                    $section.replaceWith($newSection);
                    if (window.history && window.history.pushState) {
                        window.history.pushState({ section: sectionId }, '', url);
                    }
                    requestAnimationFrame(function () {
                        $newSection.removeClass('is-fade-in is-fixed-height').css('min-height', '');
                    });
                }).fail(function () {
                    window.location.href = url;
                });
            }

            $(document).on('click', '.js-ajax-section .btn-view-all, .js-ajax-section .pagination-custom a', function (e) {
                e.preventDefault();
                loadSection($(this));
            });
        })();
    </script>
    
</body>

</html>
