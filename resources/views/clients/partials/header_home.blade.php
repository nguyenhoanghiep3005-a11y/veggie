<header class="ltn__header-area ltn__header-5 ltn__header-transparent-- gradient-color-4---">
    <div
        class="ltn__header-middle-area ltn__header-sticky ltn__sticky-bg-white sticky-active-into-mobile ltn__logo-right-menu-option plr--9---">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="site-logo-wrap">
                        <div class="site-logo">
                            <a href="{{ url('/') }}"><img src="{{asset('assets/clients/img/logohiep.png')}}"
                                    alt="Logo"></a>
                        </div>
                    </div>
                </div>
                <div class="col header-menu-column menu-color-white---">
                    <div class="header-menu d-none d-xl-block">
                        <nav>
                            <div class="ltn__main-menu">
                                <ul>
                                    <li class="menu-icon"><a href="\">Trang chủ</a> </li>
                                    <li class="menu-icon"><a href="javascript:void(0)">Danh mục</a>
                                        <ul>
                                            @foreach(\App\Models\Category::all() as $category)
                                                <li><a href="{{ route('products.index', ['category_id' => $category->id]) }}">{{ $category->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    <li class="menu-icon"><a href="{{route('products.index')}}">Sản phẩm</a>
                                    </li>
                                    <li><a href="{{ route('vouchers.index') }}">🎁 Voucher</a></li>
                                    <li class="special-link"><a href="{{route('contact.index')}}">Tư vấn mua hàng</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="ltn__header-options ltn__header-options-2 mb-sm-20">
                    <!-- header-search-1 -->
                    <div class="header-search-wrap">
                        <div class="header-search-1">
                            <div class="search-icon">
                                <i class="icon-search for-search-show"></i>
                                <i class="icon-cancel  for-search-close"></i>
                            </div>
                        </div>
                        <div class="header-search-1-form">
                            <form id="#" method="get" action="{{route('search')}}">
                                <input type="text" name="keyword" value="" placeholder="Tìm kiếm..." />
                                <i class="fas fa-microphone" aria-hidden="true" id="voice-search"></i>

                                <button type="submit">
                                    <span><i class="icon-search"></i></span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- user-menu -->
                    <div class="ltn__drop-menu user-menu">
                        <ul>
                            <li>
                                <a href="#"><i class="icon-user"></i></a>
                                <ul>
                                    @if (Auth::check())
                                    <li><a href="{{route('account')}}">Tài khoản</a></li>
                                    <li><a href="{{route('wishlist')}}">Yêu thích</a></li>
                                    <li><a href="{{route('logout')}}">Đăng xuất</a></li>
                                    @else
                                    <li><a href="{{route('login')}}">Đăng nhập</a></li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!-- mini-cart -->
                    <div class="mini-cart-icon">
                        <a href="#ltn__utilize-cart-menu" class="ltn__utilize-toggle">
                            <i class="icon-shopping-cart"></i>
                            <sup id="cart_count">
                                @php
                                    $cartCount = 0;
                                    foreach ((array) session('cart', []) as $cartLine) {
                                        if (is_array($cartLine)) {
                                            $cartCount += (int) ($cartLine['quantity'] ?? 0);
                                        } else {
                                            $cartCount += (int) $cartLine;
                                        }
                                    }
                                @endphp
                                {{ $cartCount }}
                            </sup>
                        </a>
                    </div>
                    <!-- mini-cart -->
                </div>
            </div>
        </div>
    </div>
    <!-- ltn__header-middle-area end -->
</header>
<!-- HEADER AREA END -->
<!-- Utilize Cart Menu Start -->
<div id="ltn__utilize-cart-menu" class="ltn__utilize ltn__utilize-cart-menu">
    <div class="ltn__utilize-menu-inner ltn__scrollbar">
    </div>
</div>
<!-- Utilize Cart Menu End -->
<div class="ltn__utilize-overlay"></div>
