<!-- MODAL AREA START (Quick View Modal) -->
<div class="ltn__modal-area ltn__quick-view-modal-area">
    <div class="modal fade" id="quick_view_modal-{{$sanPham->ma_san_pham}}" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="ltn__quick-view-modal-inner">
                        <div class="modal-product-item">
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="modal-product-img">
                                        <img src="{{$sanPham->duong_dan_hinh_anh}}" alt="{{$sanPham->ten_hien_thi}}">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="modal-product-info">
                                        <div class="product-ratting">
                                            <ul>
                                                <li><a href="#"><i class="fas fa-star"></i></a></li>

                                            </ul>
                                        </div>
                                        <h3>{{$sanPham->ten_hien_thi}}</h3>
                                        <div class="product-price product-detail-price-box">
                                            @if($sanPham->gia_hien_tai < $sanPham->gia)
                                            <del class="product-detail-old-price">{{number_format($sanPham->gia, 0, ',', '.')}}<small class="product-price-symbol">đ</small></del>
                                            @endif
                                            <span>{{number_format($sanPham->gia_hien_tai , 0 , ',',".")}}<small class="product-price-symbol">đ</small></span>
                                        </div>
                                        <div class="product-detail-stock">{{ $sanPham->tenSoLuongCoTheBan() }}</div>
                                        <div class="modal-product-meta ltn__product-details-menu-1">
                                            <ul>
                                                <li>
                                                    <strong>Danh Mục:</strong>
                                                    <span>
                                                        <a href="javascript:void(0)">{{$sanPham->danhMuc->ten}}</a>

                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="ltn__product-details-menu-2">
                                            <ul>
                                                <li>
                                                    <div class="cart-plus-minus">
                                                        <input type="text" name="qtybutton" value="1"
                                                            class="cart-plus-minus-box" readonly
                                                            data-max="{{ $sanPham->soLuongCoTheBan() }}">
                                                    </div>
                                                </li>

                                                <li>
                                                    <a href="javascript:void(0)"
                                                        class="theme-btn-1 btn btn-effect-1 add-to-cart-btn"
                                                        title="Thêm vào giỏ hàng" data-id="{{$sanPham->ma_san_pham}}">
                                                        <i class="fas fa-shopping-cart"></i>
                                                        <span>Thêm vào giỏ hàng</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="ltn__product-details-menu-3">
                                            <ul>
                                                @auth
                                                <li>
                                                    <a href="javascript:void(0)" class="add-to-wishlist" title="Yêu thích" data-id="{{ $sanPham->ma_san_pham }}">
                                                        <i class="far fa-heart"></i>
                                                        <span>Yêu thích</span>
                                                    </a>
                                                </li>
                                                @endauth

                                            </ul>
                                        </div>
                                        <hr>
                                        <div class="ltn__social-media">
                                            <ul>
                                                <li>Chia sẻ:</li>
                                                <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                                </li>
                                                <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                                <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a>
                                                </li>
                                                <li><a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL AREA END -->
