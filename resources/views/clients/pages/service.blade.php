@extends('layouts.client')

@section('title','Dịch vụ')
@section('breadcrumb','Dịch vụ')
@section('content')
 <!-- ABOUT US AREA START -->
            <div class="ltn__about-us-area pb-115">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 align-self-center">
                            <div class="about-us-img-wrap ltn__img-shape-left about-img-left">
                                <img src="{{asset('assets/clients/img/service/11.jpg')}}" alt="Image">
                            </div>
                        </div>
                        <div class="col-lg-7 align-self-center">
                            <div class="about-us-info-wrap">
                                <div class="section-title-area ltn__section-title-2">
                                    <h1 class="section-title">Chúng Tôi Là Đội Ngũ
                                        Chuyên Nghiệp & Giàu Kinh Nghiệm<span>.</span></h1>
                                    <p>Chúng tôi cam kết mang đến những sản phẩm và dịch vụ chất lượng, đặt uy tín và sự hài lòng của khách hàng lên hàng đầu.</p>
                                </div>
                                <div class="about-us-info-wrap-inner about-us-info-devide">
                                    <p>Với nhiều năm kinh nghiệm trong lĩnh vực cung cấp thực phẩm sạch và dịch vụ uy tín, chúng tôi không ngừng cải tiến để phục vụ khách hàng tốt hơn mỗi ngày. Sự tin tưởng của bạn chính là động lực phát triển của chúng tôi.</p>
                                    <div class="list-item-with-icon">
                                        <ul>
                                            <li><a href="contact.html">Giao hàng miễn phí 24/7</a></li>
                                            <li><a href="team.html">Đội ngũ chuyên gia</a></li>
                                            <li><a href="service-details.html">Trang thiết bị hiện đại</a></li>
                                            <li><a href="shop.html">Sản phẩm đa dạng</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- ABOUT US AREA END -->

        <!-- SERVICE AREA START (Service 1) -->
        <div class="ltn__service-area section-bg-1 pt-115 pb-70">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-area ltn__section-title-2 text-center">
                            <h1 class="section-title white-color---">Dịch Vụ Của Chúng Tôi</h1>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__service-item-1">
                            <div class="service-item-img">
                                <a href="service-details.html"><img src="{{asset('assets/clients/img/service/1.jpg')}}" alt="#"></a>
                            </div>
                            <div class="service-item-brief">
                                <h3><a href="service-details.html">Rau Củ Hữu Cơ</a></h3>
                                <p>Cung cấp rau củ tươi sạch, được trồng hoàn toàn tự nhiên, đảm bảo an toàn cho sức khỏe người tiêu dùng.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__service-item-1">
                            <div class="service-item-img">
                                <a href="service-details.html"><img src="{{asset('assets/clients/img/service/2.jpg')}}" alt="#"></a>
                            </div>
                            <div class="service-item-brief">
                                <h3><a href="service-details.html">Trái Cây Sạch</a></h3>
                                <p>Những loại trái cây tươi ngon được tuyển chọn kỹ lưỡng từ các nông trại đạt chuẩn chất lượng cao.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__service-item-1">
                            <div class="service-item-img">
                                <a href="service-details.html"><img src="{{asset('assets/clients/img/service/3.jpg')}}" alt="#"></a>
                            </div>
                            <div class="service-item-brief">
                                <h3><a href="service-details.html">Thực Phẩm Tươi Sống</a></h3>
                                <p>Phân phối các loại thịt, cá, trứng và hải sản tươi sống, đảm bảo nguồn gốc rõ ràng và an toàn thực phẩm.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__service-item-1">
                            <div class="service-item-img">
                                <a href="service-details.html"><img src="{{asset('assets/clients/img/service/3.jpg')}}" alt="#"></a>
                            </div>
                            <div class="service-item-brief">
                                <h3><a href="service-details.html">Giao Hàng Tận Nơi</a></h3>
                                <p>Dịch vụ giao hàng nhanh chóng, tận nhà, giúp khách hàng nhận được sản phẩm tươi ngon mọi lúc, mọi nơi.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__service-item-1">
                            <div class="service-item-img">
                                <a href="service-details.html"><img src="{{asset('assets/clients/img/service/1.jpg')}}" alt="#"></a>
                            </div>
                            <div class="service-item-brief">
                                <h3><a href="service-details.html">Sản Phẩm Chất Lượng Cao</a></h3>
                                <p>Tất cả sản phẩm đều trải qua quy trình kiểm định nghiêm ngặt, mang đến chất lượng tốt nhất cho người dùng.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__service-item-1">
                            <div class="service-item-img">
                                <a href="service-details.html"><img src="{{asset('assets/clients/img/service/2.jpg')}}" alt="#"></a>
                            </div>
                            <div class="service-item-brief">
                                <h3><a href="service-details.html">Hỗ Trợ Khách Hàng 24/7</a></h3>
                                <p>Đội ngũ tư vấn luôn sẵn sàng hỗ trợ, giải đáp và phục vụ khách hàng bất cứ khi nào cần thiết.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SERVICE AREA END -->

        <!-- OUR JOURNEY AREA START -->
        <div class="ltn__our-journey-area bg-image bg-overlay-theme-90 pt-280 pb-350 mb-35 plr--9"
            data-bg="{{asset('assets/clients/img/bg/8.jpg')}}">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ltn__our-journey-wrap ">
                            <ul>
                                <li><span class="ltn__journey-icon">1900</span>
                                    <ul>
                                        <li>
                                            <div class="ltn__journey-history-item-info clearfix">
                                                <div class="ltn__journey-history-img">
                                                    <img src="{{asset('assets/clients/img/service/history-1.jpg')}}" alt="#">
                                                </div>
                                                <div class="ltn__journey-history-info">
                                                    <h3>Bắt Đầu Hành Trình</h3>
                                                    <p>Chúng tôi khởi nguồn với niềm đam mê mang đến những sản phẩm chất lượng và dịch vụ tận tâm cho khách hàng.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>

                                <li class="active"><span class="ltn__journey-icon">1950</span>
                                    <ul>
                                        <li>
                                            <div class="ltn__journey-history-item-info clearfix">
                                                <div class="ltn__journey-history-img">
                                                    <img src="{{asset('assets/clients/img/service/history-1.jpg')}}" alt="#">
                                                </div>
                                                <div class="ltn__journey-history-info">
                                                    <h3>Đạt Được Thành Tựu Đầu Tiên</h3>
                                                    <p>Nhờ nỗ lực không ngừng nghỉ, chúng tôi được ghi nhận với những giải thưởng uy tín trong lĩnh vực hoạt động.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>

                                <li><span class="ltn__journey-icon">1994</span>
                                    <ul>
                                        <li>
                                            <div class="ltn__journey-history-item-info clearfix">
                                                <div class="ltn__journey-history-img">
                                                    <img src="{{asset('assets/clients/img/service/history-1.jpg')}}" alt="#">
                                                </div>
                                                <div class="ltn__journey-history-info">
                                                    <h3>Phát Triển Mạnh Mẽ</h3>
                                                    <p>Chúng tôi mở rộng quy mô sản xuất, nâng cao chất lượng sản phẩm và khẳng định vị thế trên thị trường trong nước.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>

                                <li><span class="ltn__journey-icon">2010</span>
                                    <ul>
                                        <li>
                                            <div class="ltn__journey-history-item-info clearfix">
                                                <div class="ltn__journey-history-img">
                                                    <img src="{{asset('assets/clients/img/service/history-1.jpg')}}" alt="#">
                                                </div>
                                                <div class="ltn__journey-history-info">
                                                    <h3>Đổi Mới Và Chuyển Mình</h3>
                                                    <p>Ứng dụng công nghệ hiện đại, cải tiến quy trình sản xuất và dịch vụ để đáp ứng nhu cầu ngày càng cao của khách hàng.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>

                                <li><span class="ltn__journey-icon">2020</span>
                                    <ul>
                                        <li>
                                            <div class="ltn__journey-history-item-info clearfix">
                                                <div class="ltn__journey-history-img">
                                                    <img src="{{asset('assets/clients/img/service/history-1.jpg')}}" alt="#">
                                                </div>
                                                <div class="ltn__journey-history-info">
                                                    <h3>Vươn Tầm Quốc Tế</h3>
                                                    <p>Tiếp tục mở rộng thị trường, hợp tác cùng các đối tác trong và ngoài nước, hướng tới mục tiêu phát triển bền vững.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OUR JOURNEY AREA END -->

@endsection

