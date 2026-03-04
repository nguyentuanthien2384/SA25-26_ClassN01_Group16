@if(isset($banners) && count($banners) > 0)
<section class="slider-banner-area clearfix">
    <div class="slider-right floatleft">
        <div class="slider-area">
            <div class="bend niceties preview-2">
                <div id="ensign-nivoslider" class="slides">
                    @foreach($banners as $banner)
                    <img src="{{$banner->b_image}}" alt="" title="#slider-direction-1" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-search animated slideOutUp">
        <div class="table">
            <div class="table-cell">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 offset-md-2 p-0">
                            <div class="search-form-wrap">
                                <button class="close-search"><i class="zmdi zmdi-close"></i></button>
                                <form action="#">
                                    <input type="text" placeholder="Search here..." />
                                    <button class="search-button" type="submit">
                                        <i class="zmdi zmdi-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-account d-none d-md-block" style="margin-top: 10px">
        <div class="table">
            <div class="table-cell">
                <ul>
                    @if(Auth::check())
                    <li><a href="{{route('user.index')}}" title="My-Account"><i class="zmdi zmdi-account"></i></a></li>
                    <li><a href="{{route('logout')}}"><i class="zmdi zmdi-close"></i></a></li>
                    @else
                    <li><a href="{{route('admin.login')}}" title="My-Account"><i class="zmdi zmdi-account-circle"></i></a></li>
                    <li><a href="{{route('login')}}"><i class="zmdi zmdi-account"></i></a></li>
                    <li><a href="{{route('register')}}"><i class="zmdi zmdi-account-add"></i></a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>
@else
<div class="d-none d-md-block" style="position:fixed; right:0; top:50%; transform:translateY(-50%); z-index:100; background:#fff; border-radius:8px 0 0 8px; box-shadow:-2px 0 12px rgba(0,0,0,.1); padding:10px 8px;">
    <ul style="list-style:none; margin:0; padding:0; text-align:center;">
        @if(Auth::check())
        <li style="margin-bottom:8px;"><a href="{{route('user.index')}}" title="Tài khoản"><i class="zmdi zmdi-account" style="font-size:20px;color:#333;"></i></a></li>
        <li><a href="{{route('logout')}}" title="Đăng xuất"><i class="zmdi zmdi-close" style="font-size:20px;color:#e53935;"></i></a></li>
        @else
        <li style="margin-bottom:8px;"><a href="{{route('admin.login')}}" title="Admin"><i class="zmdi zmdi-account-circle" style="font-size:20px;color:#333;"></i></a></li>
        <li style="margin-bottom:8px;"><a href="{{route('login')}}" title="Đăng nhập"><i class="zmdi zmdi-account" style="font-size:20px;color:#333;"></i></a></li>
        <li><a href="{{route('register')}}" title="Đăng ký"><i class="zmdi zmdi-account-add" style="font-size:20px;color:#333;"></i></a></li>
        @endif
    </ul>
</div>
@endif
