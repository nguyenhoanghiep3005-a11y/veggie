<!-- ADD TO CART MODAL -->
<div class="modal fade" id="add_to_cart_modal-{{$sanPham->ma_san_pham}}" tabindex="-1">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <button type="button" class="close modal-close-button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body pt-0">
                <div class="ltn__quick-view-modal-inner">
                    <div class="modal-product-item">
                        <div class="text-center">

                            <div class="modal-product-img mb-3">
                                <img src="{{$sanPham->duong_dan_hinh_anh}}" alt="{{$sanPham->ten_hien_thi}}" class="add-cart-modal-image">
                            </div>

                            <div class="modal-product-info">
                                <h5 class="mb-2">{{$sanPham->ten_hien_thi}}</h5>

                                <p class="added-cart mb-3">
                                    <i class="fa fa-check-circle text-success"></i>
                                    Đã thêm thành công vào giỏ hàng
                                </p>

                                <div class="btn-wrapper">
                                    <a href="{{ route('gio-hang.hien-thi') }}" class="theme-btn-1 btn btn-effect-1">
                                        Xem
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
