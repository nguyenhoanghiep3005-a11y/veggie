<ul>
    @forelse ($sanPham->danhGias as $danhGia)
        <li>
            <div class="ltn__comment-item clearfix">
                <div class="ltn__commenter-comment">
                    <h6>{{ $danhGia->nguoiDung ? $danhGia->nguoiDung->ten : 'Người dùng' }}</h6>
                    <div class="product-ratting">
                        <ul>
                            @for ($soSao = 1; $soSao <= 5; $soSao++)
                                <li><i class="{{ $soSao <= $danhGia->so_sao ? 'fas fa-star' : 'far fa-star' }}"></i></li>
                            @endfor
                        </ul>
                    </div>
                    <p>{{ $danhGia->binh_luan }}</p>
                    <span class="ltn__comment-reply-btn">{{ $danhGia->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </li>
    @empty
        <li>Chưa có đánh giá.</li>
    @endforelse
</ul>
