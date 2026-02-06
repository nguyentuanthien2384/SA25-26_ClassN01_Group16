<header id="sticky-menu" class="header">
    <div class="header-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 offset-md-4 col-7">
                    <div class="logo text-md-center">
                        <a href="{{route('home')}}"><img src="{{asset('img/logo/logo.png')}}" alt="Logo" style="height:100px" loading="lazy" /></a>
                    </div>
                </div>
                <div class="col-md-4 col-5">
                    <div class="input-group" style="top:43px">
                        <form action="{{route('get.product.list')}}" method="GET" id="searchform" style="display: flex; align-items: center;">
                            <input type="text" class="form-control" name="k" placeholder="Tìm kiếm" style="flex: 1; margin-right: 5px; margin-bottom: 0">
                            <button type="submit" class="btn btn-default" style="font-size: 35px"><i class="zmdi zmdi-search"></i></button>
                        </form>
                    </div>
                    <div class="mini-cart text-end">
                        <ul>
                            <li>
                                <a class="cart-icon" href="{{route('cart.index')}}">
                                    <i class="zmdi zmdi-shopping-cart"></i>
                                    <span>{{$carts->sum('quantity')}}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MAIN-MENU START -->
    <div class="menu-toggle hamburger hamburger--emphatic d-none d-md-block">
        <div class="hamburger-box">
            <div class="hamburger-inner"></div>
        </div>
    </div>
    <div class="main-menu d-none d-md-block">
        <nav>
            <ul>
                <li><a href="{{route('home')}}">Trang chủ</a></li>
                <li><a href="#">Sản phẩm</a>
                    <div class="mega-menu menu-scroll">
                        <div class="table">
                            <div class="table-cell">
                                <div class="half-width">
                                    <ul class="level1">
                                        {{-- ⚡ OPTIMIZED: Dùng data đã pre-load, KHÔNG query DB ở đây --}}
                                        @foreach ($catParent ?? [] as $parentCat)
                                        <li class="level1 first parent">
                                            <a class="active" href="{{route('get.list.product',[$parentCat->c_slug,$parentCat->id])}}">{{$parentCat->c_name}}</a>
                                            @if(isset($catChildren[$parentCat->id]))
                                            <ul class="level2">
                                                @foreach ($catChildren[$parentCat->id] as $childCat)
                                                <li class="level2 nav-2-1-1 first">
                                                    <a href="{{route('get.list.product',[$childCat->c_slug,$childCat->id])}}">{{$childCat->c_name}}</a>
                                                </li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="{{route('get.list.article')}}">Tin tức</a></li>
                <li><a href="">Giới thiệu</a></li>
                <li><a href="{{route('contact.index')}}">Liên hệ</a></li>
            </ul>
        </nav>
    </div>
</header>
