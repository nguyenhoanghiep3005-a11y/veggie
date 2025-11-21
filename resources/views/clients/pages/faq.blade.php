@extends('layouts.client')

@section('title','FAQ')
@section('breadcrumb','Những câu hỏi thường gặp')

@section('content')
  <!-- FAQ AREA START (faq-2) (ID > accordion_2) -->
        <div class="ltn__faq-area mb-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="ltn__faq-inner ltn__faq-inner-2">
                            <div id="accordion_2">
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-1" aria-expanded="false">
                                        Làm thế nào để mua sản phẩm?
                                    </h6>
                                    <div id="faq-item-2-1" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Để mua sản phẩm, bạn chỉ cần chọn mặt hàng mong muốn, thêm vào giỏ hàng và tiến hành thanh toán. Chúng tôi hỗ trợ nhiều phương thức thanh toán và giao hàng tận nơi trên toàn quốc.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq-item-2-2"
                                        aria-expanded="true">
                                        Làm sao để hoàn tiền từ website?
                                    </h6>
                                    <div id="faq-item-2-2" class="collapse show" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <div class="ltn__video-img alignleft">
                                                <img src="{{asset('assets/clients/img/bg/17.jpg')}}" alt="video popup bg image">
                                                <a class="ltn__video-icon-2 ltn__video-icon-2-small ltn__video-icon-2-border----"
                                                    href="https://www.youtube.com/embed/LjCzPp-MK48?autoplay=1&amp;showinfo=0"
                                                    data-rel="lightcase:myCollection">
                                                    <i class="fa fa-play"></i>
                                                </a>
                                            </div>
                                            <p>Bạn có thể yêu cầu hoàn tiền trong vòng 3 ngày kể từ khi nhận hàng nếu sản phẩm bị lỗi hoặc không đúng mô tả. Vui lòng liên hệ đội ngũ hỗ trợ để được hướng dẫn chi tiết quy trình hoàn trả.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-3" aria-expanded="false">
                                        Tôi là người dùng mới, bắt đầu như thế nào?
                                    </h6>
                                    <div id="faq-item-2-3" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Rất đơn giản! Bạn chỉ cần tạo tài khoản miễn phí bằng địa chỉ email của mình, sau đó đăng nhập để xem sản phẩm, thêm vào giỏ hàng và tiến hành thanh toán.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-4" aria-expanded="false">
                                        Chính sách đổi trả và hoàn tiền
                                    </h6>
                                    <div id="faq-item-2-4" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Chúng tôi chấp nhận đổi trả sản phẩm trong vòng 3 ngày kể từ khi nhận hàng, với điều kiện sản phẩm còn nguyên tem, bao bì và chưa qua sử dụng. Hoàn tiền sẽ được xử lý sau khi kiểm tra sản phẩm.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-5" aria-expanded="false">
                                        Thông tin của tôi có được bảo mật không?
                                    </h6>
                                    <div id="faq-item-2-5" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Chúng tôi cam kết bảo mật tuyệt đối thông tin cá nhân của khách hàng. Dữ liệu được mã hóa và chỉ sử dụng cho mục đích giao dịch, không chia sẻ cho bên thứ ba.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-6" aria-expanded="false">
                                        Mã giảm giá không sử dụng được
                                    </h6>
                                    <div id="faq-item-2-6" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Vui lòng kiểm tra lại điều kiện áp dụng của mã giảm giá (thời hạn, giá trị đơn hàng tối thiểu, hoặc danh mục áp dụng). Nếu vẫn không được, hãy liên hệ bộ phận hỗ trợ của chúng tôi.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-7" aria-expanded="false">
                                        Làm thế nào để thanh toán bằng thẻ tín dụng?
                                    </h6>
                                    <div id="faq-item-2-7" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Chúng tôi hỗ trợ thanh toán qua thẻ Visa, MasterCard, và các loại thẻ nội địa. Chọn “Thanh toán bằng thẻ” ở bước cuối cùng của quá trình đặt hàng và nhập thông tin thẻ để hoàn tất giao dịch.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="need-support text-center mt-100">
                                <h2>Vẫn cần trợ giúp? Liên hệ hỗ trợ 24/7:</h2>
                                <div class="btn-wrapper mb-30">
                                    <a href="contact.html" class="theme-btn-1 btn">Liên hệ ngay</a>
                                </div>
                                <h3><i class="fas fa-phone"></i> +0123-456-789</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <aside class="sidebar-area ltn__right-sidebar">
                            <!-- Newsletter Widget -->
                            <div class="widget ltn__search-widget ltn__newsletter-widget">
                                <h6 class="ltn__widget-sub-title">// Đăng ký nhận tin</h6>
                                <h4 class="ltn__widget-title">Nhận Bản Tin Mới Nhất</h4>
                                <form action="#">
                                    <input type="text" name="search" placeholder="Tìm kiếm...">
                                    <button type="submit"><i class="fas fa-search"></i></button>
                                </form>
                                <div class="ltn__newsletter-bg-icon">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                            </div>
                            <!-- Banner Widget -->
                            <div class="widget ltn__banner-widget">
                                <a href="shop.html"><img src="{{asset('assets/clients/img/banner/banner-3.jpg')}}" alt="Banner Image"></a>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ AREA START -->

        <!-- COUNTER UP AREA START -->
     <div class="ltn__counterup-area bg-image bg-overlay-theme-black-80 pt-115 pb-70" data-bg="{{asset('assets/clients/img/bg/5.jpg')}}">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/2.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">733</span><span class="counterUp-icon">+</span></h1>
                    <h6>Khách Hàng Đang Hoạt Động</h6>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/3.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">33</span><span class="counterUp-letter">K</span><span class="counterUp-icon">+</span></h1>
                    <h6>Tách Cà Phê Được Thưởng Thức</h6>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/4.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">100</span><span class="counterUp-icon">+</span></h1>
                    <h6>Giải Thưởng Đạt Được</h6>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/5.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">21</span><span class="counterUp-icon">+</span></h1>
                    <h6>Quốc Gia Phân Phối</h6>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- COUNTER UP AREA END -->
@endsection

