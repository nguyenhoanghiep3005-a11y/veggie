<!-- ADD TO CART MODAL -->
<div class="modal fade" id="add_to_cart_modal-{{$product->id}}" tabindex="-1">
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
                                <img src="{{$product->image_url}}" alt="{{$product->display_name}}" class="add-cart-modal-image">
                            </div>

                            <div class="modal-product-info">
                                <h5 class="mb-2">{{$product->display_name}}</h5>

                                <p class="added-cart mb-3">
                                    <i class="fa fa-check-circle text-success"></i>
                                    Đã thêm thành công vào giỏ hàng
                                </p>

                                <div class="btn-wrapper">
                                    <a href="{{ route('cart.index') }}" class="theme-btn-1 btn btn-effect-1">
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
