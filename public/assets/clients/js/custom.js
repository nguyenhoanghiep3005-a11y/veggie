$(document).ready(function () {

  // Kiem tra form dang ky truoc khi gui.
  $('#register-form').on('submit', function (suKien) {
    var ten = $('input[name="ten"]').val().trim();
    var email = $('input[name="email"]').val().trim();
    var matKhau = $('input[name="password"]').val();
    var xacNhanMatKhau = $('input[name="confirmPassword"]').val();
    var mauEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var coLoi = false;

    if (ten.length < 3) {
      toastr.error('Họ tên phải có ít nhất 3 ký tự.');
      coLoi = true;
    }

    if (!mauEmail.test(email)) {
      toastr.error('Email không đúng định dạng.');
      coLoi = true;
    }

    if (matKhau.length < 6) {
      toastr.error('Mật khẩu phải có ít nhất 6 ký tự.');
      coLoi = true;
    }

    if (matKhau !== xacNhanMatKhau) {
      toastr.error('Mật khẩu xác nhận không khớp.');
      coLoi = true;
    }

    if (!$('input[name="checkbox1"]').is(':checked') || !$('input[name="checkbox2"]').is(':checked')) {
      toastr.error('Vui lòng đồng ý điều khoản và chính sách.');
      coLoi = true;
    }

    if (coLoi) {
      suKien.preventDefault();
    }
  });

  // Kiem tra form dang nhap truoc khi gui.
  $('#login-form').on('submit', function (suKien) {
    var email = $('input[name="email"]').val().trim();
    var matKhau = $('input[name="password"]').val();
    var mauEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!mauEmail.test(email) || matKhau.length < 6) {
      suKien.preventDefault();
      toastr.error('Email hoặc mật khẩu không hợp lệ.');
    }
  });

  // Kiem tra form dat lai mat khau truoc khi gui.
  $('#reset-password-form').on('submit', function (suKien) {
    var matKhau = $('input[name="password"]').val();
    var xacNhanMatKhau = $('input[name="password_confirmation"]').val();

    if (matKhau.length < 6 || matKhau !== xacNhanMatKhau) {
      suKien.preventDefault();
      toastr.error('Mật khẩu phải có ít nhất 6 ký tự và hai ô phải khớp nhau.');
    }
  });

  // Gui form cap nhat thong tin tai khoan.
  $('#update-account').on('submit', function (suKien) {
    suKien.preventDefault();

    var $bieuMau = $(this);
    var $nutLuu = $bieuMau.find('button[type="submit"]');

    $.ajax({
      url: $bieuMau.attr('action'),
      type: 'POST',
      data: new FormData(this),
      processData: false,
      contentType: false,
      beforeSend: function () {
        $nutLuu.prop('disabled', true);
      },
      success: function (response) {
        toastr.success(response.thong_bao);
      },
      error: function (xhr) {
        var thongBao = xhr.responseJSON && xhr.responseJSON.message
          ? xhr.responseJSON.message
          : 'Không thể cập nhật tài khoản.';
        toastr.error(thongBao);
      },
      complete: function () {
        $nutLuu.prop('disabled', false);
      }
    });
  });

  var duongDanGocTaiKhoan = $('meta[name="base-url"]').attr('content') || '';
  var daTaiTinhTaiKhoan = false;

  // Cap nhat niceSelect sau khi thay doi danh sach.
  function capNhatLuaChonTaiKhoan($oLuaChon) {
    if (typeof $oLuaChon.niceSelect === 'function') {
      $oLuaChon.niceSelect('update');
    }
  }

  // Tai danh sach tinh cho form them dia chi.
  function taiTinhTaiKhoan() {
    var $tinh = $('#ma_tinh');

    if (daTaiTinhTaiKhoan || $tinh.length === 0) {
      return;
    }

    $.get(duongDanGocTaiKhoan + '/giao-hang-nhanh/tinh-thanh', function (response) {
      var cacLuaChon = '<option value="">Chọn tỉnh/thành</option>';

      if (response.trang_thai && response.du_lieu) {
        $.each(response.du_lieu, function (viTri, tinh) {
          cacLuaChon += '<option value="' + tinh.ProvinceID + '">' + tinh.ProvinceName + '</option>';
        });

        daTaiTinhTaiKhoan = true;
        $tinh.html(cacLuaChon).prop('disabled', false);
        capNhatLuaChonTaiKhoan($tinh);
      } else {
        toastr.error(response.thong_bao || 'Không tải được tỉnh/thành.');
      }
    });
  }

  // Tai danh sach huyen theo tinh.
  function taiHuyenTaiKhoan(maTinh) {
    var $huyen = $('#ma_huyen');
    var $xa = $('#ma_xa');

    $huyen.html('<option value="">Chọn quận/huyện</option>').prop('disabled', true);
    $xa.html('<option value="">Chọn phường/xã</option>').prop('disabled', true);
    $('#district_name, #ward_name').val('');
    capNhatLuaChonTaiKhoan($huyen);
    capNhatLuaChonTaiKhoan($xa);

    if (!maTinh) {
      return;
    }

    $.get(duongDanGocTaiKhoan + '/giao-hang-nhanh/quan-huyen', { ma_tinh: maTinh }, function (response) {
      var cacLuaChon = '<option value="">Chọn quận/huyện</option>';

      if (response.trang_thai && response.du_lieu) {
        $.each(response.du_lieu, function (viTri, huyen) {
          cacLuaChon += '<option value="' + huyen.DistrictID + '">' + huyen.DistrictName + '</option>';
        });

        $huyen.html(cacLuaChon).prop('disabled', false);
        capNhatLuaChonTaiKhoan($huyen);
      }
    });
  }

  // Tai danh sach xa theo huyen.
  function taiXaTaiKhoan(maHuyen) {
    var $xa = $('#ma_xa');

    $xa.html('<option value="">Chọn phường/xã</option>').prop('disabled', true);
    $('#ward_name').val('');
    capNhatLuaChonTaiKhoan($xa);

    if (!maHuyen) {
      return;
    }

    $.get(duongDanGocTaiKhoan + '/giao-hang-nhanh/phuong-xa', { ma_huyen: maHuyen }, function (response) {
      var cacLuaChon = '<option value="">Chọn phường/xã</option>';

      if (response.trang_thai && response.du_lieu) {
        $.each(response.du_lieu, function (viTri, xa) {
          cacLuaChon += '<option value="' + xa.WardCode + '">' + xa.WardName + '</option>';
        });

        $xa.html(cacLuaChon).prop('disabled', false);
        capNhatLuaChonTaiKhoan($xa);
      }
    });
  }

  $(document).on('show.bs.modal', '#addAddressModal', taiTinhTaiKhoan);

  $(document).on('change', '#ma_tinh', function () {
    var maTinh = $(this).val();
    $('#province_name').val(maTinh ? $(this).find(':selected').text() : '');
    taiHuyenTaiKhoan(maTinh);
  });

  $(document).on('change', '#ma_huyen', function () {
    var maHuyen = $(this).val();
    $('#district_name').val(maHuyen ? $(this).find(':selected').text() : '');
    taiXaTaiKhoan(maHuyen);
  });

  $(document).on('change', '#ma_xa', function () {
    $('#ward_name').val($(this).val() ? $(this).find(':selected').text() : '');
  });

  // Gui form doi mat khau.
  $('#change-password-form').on('submit', function (suKien) {
    suKien.preventDefault();

    var $bieuMau = $(this);
    var matKhauHienTai = $bieuMau.find('input[name="current_password"]').val();
    var matKhauMoi = $bieuMau.find('input[name="new_password"]').val();
    var xacNhanMatKhauMoi = $bieuMau.find('input[name="confirm_new_password"]').val();

    if (matKhauMoi.length < 6 || matKhauMoi !== xacNhanMatKhauMoi) {
      toastr.error('Mật khẩu mới không hợp lệ hoặc hai ô không khớp.');
      return;
    }

    $.ajax({
      url: $bieuMau.attr('action'),
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        current_password: matKhauHienTai,
        new_password: matKhauMoi,
        confirm_new_password: xacNhanMatKhauMoi
      },
      success: function (response) {
        toastr.success(response.thong_bao);
        $bieuMau[0].reset();
      },
      error: function (xhr) {
        var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
          ? xhr.responseJSON.thong_bao
          : 'Không thể đổi mật khẩu.';
        toastr.error(thongBao);
      }
    });
  });

  // Kiem tra dia chi truoc khi gui.
  $('#addAddressForm').on('submit', function (suKien) {
    var soDienThoai = $('#so_dien_thoai').val().trim();

    if (!/^[0-9]{10,11}$/.test(soDienThoai)) {
      suKien.preventDefault();
      toastr.error('Số điện thoại phải có 10 đến 11 số.');
      return;
    }

    if (!$('#ma_tinh').val() || !$('#ma_huyen').val() || !$('#ma_xa').val()) {
      suKien.preventDefault();
      toastr.error('Vui lòng chọn đầy đủ tỉnh, quận và phường.');
    }
  });


  // Loc, sap xep va phan trang danh sach san pham.
  var trangHienTai = 1;

  // Goi lai cac plugin can thiet sau khi thay noi dung san pham.
  function khoiTaoLaiLuoiSanPham() {
    if (typeof $.fn.niceSelect === 'function') {
      $('#sort-by').niceSelect('update');
    }
  }

  // Gui bo loc va thay noi dung danh sach san pham.
  function locSanPham() {
    if ($('#liton_product_grid').length === 0) {
      return;
    }

    var maDanhMuc = $('.category-filter.active').data('id') || '';
    var giaTu = 0;
    var giaDen = 500000;
    var sapXep = $('#sort-by').val() || 'mac_dinh';

    if ($('.slider-range').length && typeof $('.slider-range').slider === 'function') {
      giaTu = $('.slider-range').slider('values', 0);
      giaDen = $('.slider-range').slider('values', 1);
    }

    $('#loading-spinner').show();

    $.ajax({
      url: '/san-pham/loc',
      type: 'GET',
      data: {
        ma_danh_muc: maDanhMuc,
        gia_tu: giaTu,
        gia_den: giaDen,
        sap_xep: sapXep,
        page: trangHienTai
      },
      success: function (response) {
        $('#liton_product_grid').html(response.noi_dung);
        $('.ltn__pagination').html(response.phan_trang);
        khoiTaoLaiLuoiSanPham();
      },
      error: function () {
        toastr.error('Không thể tải danh sách sản phẩm.');
      },
      complete: function () {
        $('#loading-spinner').hide();
      }
    });
  }

  $(document).on('click', '.pagination-link', function (suKien) {
    suKien.preventDefault();

    var ketQuaTrang = ($(this).attr('href') || '').match(/[?&]page=(\d+)/);
    trangHienTai = ketQuaTrang ? Number(ketQuaTrang[1]) : 1;
    locSanPham();
  });

  $(document).on('click', '.category-filter', function (suKien) {
    suKien.preventDefault();
    $('.category-filter').removeClass('active');
    $(this).addClass('active');
    trangHienTai = 1;
    locSanPham();
  });

  $('#sort-by').on('change', function () {
    trangHienTai = 1;
    locSanPham();
  });

  if ($('.slider-range').length && typeof $('.slider-range').slider === 'function') {
    $('.slider-range').slider({
      range: true,
      min: 0,
      max: 500000,
      values: [0, 500000],
      slide: function (suKien, giaoDien) {
        $('.so_tien').val(
          Number(giaoDien.values[0]).toLocaleString('vi-VN')
          + ' - '
          + Number(giaoDien.values[1]).toLocaleString('vi-VN')
          + ' đ'
        );
      },
      change: function () {
        trangHienTai = 1;
        locSanPham();
      }
    });

    $('.so_tien').val('0 - 500.000 đ');
  }

  // Khoi tao nut tang giam so luong cho xem nhanh va chi tiet san pham.
  function khoiTaoNutTangGiamSoLuong() {
    $('.cart-plus-minus').each(function () {
      var $khungSoLuong = $(this);

      if ($khungSoLuong.find('.qtybutton').length === 0) {
        $khungSoLuong.prepend('<button type="button" class="dec qtybutton">-</button>');
        $khungSoLuong.append('<button type="button" class="inc qtybutton">+</button>');
      }
    });
  }

  khoiTaoNutTangGiamSoLuong();

  $(document).on('shown.bs.modal', '.modal', function () {
    khoiTaoNutTangGiamSoLuong();
  });

  $(document).on('click', '.qtybutton', function () {
    var $oSoLuong = $(this).siblings('.cart-plus-minus-box');

    if (!$oSoLuong.length || $oSoLuong.closest('.shoping-cart-table').length) {
      return;
    }

    var soLuong = Number($oSoLuong.val()) || 1;
    var tonKho = Number($oSoLuong.data('max')) || 9999;

    if ($(this).hasClass('inc') && soLuong < tonKho) {
      soLuong++;
    }

    if ($(this).hasClass('dec') && soLuong > 1) {
      soLuong--;
    }

    $oSoLuong.val(soLuong);
  });
  // Them san pham vao gio hang.
  $(document).on('click', '.add-to-cart-btn', function (suKien) {
    suKien.preventDefault();

    var $nutThem = $(this);
    var maSanPham = Number($nutThem.data('id'));
    var soLuong = 1 ;
    var $oSoLuong = $nutThem
      .closest('.ltn__product-item, .ltn__modal-area, .ltn__shop-details-inner')
      .find('.cart-plus-minus-box')
      .first();

    if ($oSoLuong.length) {
      soLuong = Number($oSoLuong.val()) || 1;
    }

    if (!maSanPham) {
      toastr.error('Không tìm thấy sản phẩm.');
      return;
    }

    $.ajax({
      url: '/gio-hang/them',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        ma_san_pham: maSanPham,
        so_luong: soLuong
      },
      success: function (response) {
        $('#cart_count').text(response.so_luong_gio_hang);
        toastr.success(response.thong_bao);
        taiGioHangNho(false);

        var modalThemGioHang = document.getElementById('add_to_cart_modal-' + maSanPham);
        if (modalThemGioHang && window.bootstrap) {
          bootstrap.Modal.getOrCreateInstance(modalThemGioHang).show();
        }
      },
      error: function (xhr) {
        var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
          ? xhr.responseJSON.thong_bao
          : 'Không thể thêm sản phẩm vào giỏ hàng.';
        toastr.error(thongBao);
      }
    });
  });

  // Tai noi dung gio hang nho.
  function taiGioHangNho(moGioHang) {
    $.ajax({
      url: '/gio-hang/nho',
      type: 'GET',
      success: function (response) {
        if (!response.trang_thai) {
          return;
        }

        $('#ltn__utilize-cart-menu .ltn__utilize-menu-inner').html(response.noi_dung);
        $('#cart_count').text(response.so_luong_gio_hang);

        if (moGioHang) {
          $('#ltn__utilize-cart-menu').addClass('ltn__utilize-open');
          $('.ltn__utilize-overlay').addClass('ltn__utilize-overlay-open');
        }
      },
      error: function () {
        toastr.error('Không thể tải giỏ hàng.');
      }
    });
  }

  $('.mini-cart-icon .ltn__utilize-toggle').off('click').on('click', function (suKien) {
    suKien.preventDefault();
    taiGioHangNho(true);
  });

  $(document).on('click', '.ltn__utilize-close, .ltn__utilize-overlay', function () {
    $('#ltn__utilize-cart-menu').removeClass('ltn__utilize-open');
    $('.ltn__utilize-overlay').removeClass('ltn__utilize-overlay-open');
  });

  // Cap nhat so luong mot san pham trong gio hang.
  function capNhatGioHang(maSanPham, soLuong, $oSoLuong, taiLaiGioHangNho) {
    $.ajax({
      url: '/gio-hang/cap-nhat',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        ma_san_pham: maSanPham,
        so_luong: soLuong
      },
      success: function (response) {
        $oSoLuong.val(response.so_luong);
        $('#cart_count').text(response.so_luong_gio_hang);
        $('.cart-total').html(response.tong_tien + '<small>đ</small>');

        var $dongSanPham = $oSoLuong.closest('tr, .mini-cart-item');
        $dongSanPham.find('.cart-product-subtotal, .mini-cart-item-total').html(response.tam_tinh + '<small>đ</small>');

        if (taiLaiGioHangNho) {
          taiGioHangNho(false);
        }
      },
      error: function (xhr) {
        var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
          ? xhr.responseJSON.thong_bao
          : 'Không thể cập nhật giỏ hàng.';
        toastr.error(thongBao);
      }
    });
  }

  $(document).on('click', '.mini-cart-qty-btn', function (suKien) {
    suKien.preventDefault();

    var $nut = $(this);
    var $oSoLuong = $nut.siblings('.mini-cart-qty-input');
    var maSanPham = Number($nut.data('product-id'));
    var soLuong = Number($oSoLuong.val()) || 1;
    var tonKho = Number($oSoLuong.data('max')) || 1;

    if ($nut.data('action') === 'increase' && soLuong < tonKho) {
      soLuong++;
    }

    if ($nut.data('action') === 'decrease' && soLuong > 1) {
      soLuong--;
    }

    capNhatGioHang(maSanPham, soLuong, $oSoLuong, false);
  });

  $(document).on('click', '.qtybutton', function () {
    var $oSoLuong = $(this).siblings('.cart-plus-minus-box');

    if (!$oSoLuong.closest('.shoping-cart-table').length) {
      return;
    }

    var maSanPham = Number($oSoLuong.data('id'));
    var soLuong = Number($oSoLuong.val()) || 1;
    var tonKho = Number($oSoLuong.data('max')) || 1;

    if ($(this).hasClass('inc') && soLuong < tonKho) {
      soLuong++;
    }

    if ($(this).hasClass('dec') && soLuong > 1) {
      soLuong--;
    }

    capNhatGioHang(maSanPham, soLuong, $oSoLuong, true);
  });

  // Xoa san pham khoi gio hang nho.
  $(document).on('click', '.remove-from-cart-btn', function (suKien) {
    suKien.preventDefault();

    var maSanPham = Number($(this).data('product-id'));

    $.ajax({
      url: '/gio-hang/xoa-nho',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { ma_san_pham: maSanPham },
      success: function (response) {
        $('#cart_count').text(response.so_luong_gio_hang);
        taiGioHangNho(false);
        toastr.success(response.thong_bao);
      },
      error: function () {
        toastr.error('Không thể xóa sản phẩm khỏi giỏ hàng.');
      }
    });
  });

  // Xoa san pham khoi trang gio hang.
  $(document).on('click', '.remove-from-cart', function (suKien) {
    suKien.preventDefault();

    var $dongSanPham = $(this).closest('tr');
    var maSanPham = Number($(this).data('id'));

    $.ajax({
      url: '/gio-hang/xoa',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { ma_san_pham: maSanPham },
      success: function (response) {
        $dongSanPham.remove();
        $('#cart_count').text(response.so_luong_gio_hang);
        $('.cart-total').html(response.tong_tien + '<small>đ</small>');

        if ($('.remove-from-cart').length === 0) {
          window.location.reload();
        }
      },
      error: function () {
        toastr.error('Không thể xóa sản phẩm khỏi giỏ hàng.');
      }
    });
  });

  if (window.location.pathname === "/thanh-toan") {
    var duongDanGoc = $('meta[name="base-url"]').attr('content') || '';
    var $tongKetThanhToan = $('#tong-ket-thanh-toan');
    var duongDanDiaChi = $tongKetThanhToan.data('duong-dan-dia-chi');
    var duongDanPhiVanChuyen = $tongKetThanhToan.data('duong-dan-phi-van-chuyen');
    var duongDanPhieuGiamGia = $tongKetThanhToan.data('duong-dan-phieu-giam-gia');
    var duongDanPayPal = $tongKetThanhToan.data('duong-dan-paypal');
    var tongTien = Number($tongKetThanhToan.data('tong-tien')) || 0;
    var daTinhPhiVanChuyen = Number($tongKetThanhToan.data('san-sang')) === 1;
    var daTaiTinhThanh = false;

    // Dinh dang so tien theo dinh dang Viet Nam.
    function dinhDangTien(giaTien) {
      return Number(giaTien || 0).toLocaleString('vi-VN') + ' đ';
    }

    // Cap nhat cac khoan tien tren bang tong ket.
    function capNhatTongTien(cacKhoanTien) {
      $('.phi-van-chuyen-thanh-toan').text(dinhDangTien(cacKhoanTien.phi_van_chuyen));
      $('.so-tien-giam-thanh-toan').text(dinhDangTien(cacKhoanTien.so_tien_giam));
      $('.tong-tien-thanh-toan').text(dinhDangTien(cacKhoanTien.tong_tien));

      $tongKetThanhToan.data('tam-tinh', cacKhoanTien.tam_tinh);
      $tongKetThanhToan.data('phi-van-chuyen', cacKhoanTien.phi_van_chuyen);
      $tongKetThanhToan.data('so-tien-giam', cacKhoanTien.so_tien_giam);
      $tongKetThanhToan.data('tong-tien', cacKhoanTien.tong_tien);
      tongTien = Number(cacKhoanTien.tong_tien) || 0;
      datTrangThaiPhiVanChuyen(true, '');
    }

    // Bat hoac tat nut dat hang theo trang thai tinh phi.
    function datTrangThaiPhiVanChuyen(daSanSang, thongBao) {
      daTinhPhiVanChuyen = daSanSang;

      if (thongBao) {
        $('.thong-bao-phi-van-chuyen').removeClass('d-none').find('td').text(thongBao);
      } else {
        $('.thong-bao-phi-van-chuyen').addClass('d-none').find('td').text('');
      }
    }

    // Cap nhat giao dien cho plugin niceSelect sau khi doi option.
    function capNhatLuaChon($oLuaChon) {
      if (typeof $oLuaChon.niceSelect === 'function') {
        $oLuaChon.niceSelect('update');
      }
    }

    // Xoa danh sach huyen va xa khi nguoi dung doi tinh.
    function datLaiHuyenVaXa() {
      $('#ma_huyen_moi').html('<option value="">Quận/huyện *</option>').prop('disabled', true);
      $('#ma_xa_moi').html('<option value="">Phường/xã *</option>').prop('disabled', true);
      $('#ten_huyen_moi').val('');
      $('#ten_xa_moi').val('');
      capNhatLuaChon($('#ma_huyen_moi'));
      capNhatLuaChon($('#ma_xa_moi'));
    }

    // Tai danh sach tinh thanh tu GHN.
    function taiDanhSachTinh() {
      var $tinhThanh = $('#ma_tinh_moi');

      if (daTaiTinhThanh || $tinhThanh.length === 0) {
        return;
      }

      $tinhThanh.html('<option value="">Đang tải...</option>').prop('disabled', true);
      capNhatLuaChon($tinhThanh);

      $.get(duongDanGoc + '/giao-hang-nhanh/tinh-thanh', function (response) {
        var cacLuaChon = '<option value="">Tỉnh/thành *</option>';

        if (response.trang_thai && response.du_lieu) {
          $.each(response.du_lieu, function (viTri, tinhThanh) {
            cacLuaChon += '<option value="' + tinhThanh.ProvinceID + '">' + tinhThanh.ProvinceName + '</option>';
          });

          daTaiTinhThanh = true;
          $tinhThanh.html(cacLuaChon).prop('disabled', false);
        } else {
          $tinhThanh.html('<option value="">Không tải được tỉnh/thành</option>').prop('disabled', false);
          toastr.error(response.thong_bao || 'Không tải được tỉnh/thành.');
        }

        capNhatLuaChon($tinhThanh);
      }).fail(function () {
        $tinhThanh.html('<option value="">Không tải được tỉnh/thành</option>').prop('disabled', false);
        capNhatLuaChon($tinhThanh);
        toastr.error('Không thể kết nối dịch vụ địa chỉ.');
      });
    }

    // Tai danh sach quan huyen theo tinh da chon.
    function taiDanhSachHuyen(maTinh) {
      var $huyen = $('#ma_huyen_moi');
      datLaiHuyenVaXa();

      if (!maTinh) {
        return;
      }

      $huyen.html('<option value="">Đang tải...</option>').prop('disabled', true);
      capNhatLuaChon($huyen);

      $.get(duongDanGoc + '/giao-hang-nhanh/quan-huyen', { ma_tinh: maTinh }, function (response) {
        var cacLuaChon = '<option value="">Quận/huyện *</option>';

        if (response.trang_thai && response.du_lieu) {
          $.each(response.du_lieu, function (viTri, huyen) {
            cacLuaChon += '<option value="' + huyen.DistrictID + '">' + huyen.DistrictName + '</option>';
          });

          $huyen.html(cacLuaChon).prop('disabled', false);
        } else {
          $huyen.html('<option value="">Không tải được quận/huyện</option>').prop('disabled', false);
          toastr.error(response.thong_bao || 'Không tải được quận/huyện.');
        }

        capNhatLuaChon($huyen);
      }).fail(function () {
        $huyen.html('<option value="">Không tải được quận/huyện</option>').prop('disabled', false);
        capNhatLuaChon($huyen);
      });
    }

    // Tai danh sach phuong xa theo huyen da chon.
    function taiDanhSachXa(maHuyen) {
      var $xa = $('#ma_xa_moi');
      $('#ten_xa_moi').val('');
      $xa.html('<option value="">Phường/xã *</option>').prop('disabled', true);
      capNhatLuaChon($xa);

      if (!maHuyen) {
        return;
      }

      $xa.html('<option value="">Đang tải...</option>').prop('disabled', true);
      capNhatLuaChon($xa);

      $.get(duongDanGoc + '/giao-hang-nhanh/phuong-xa', { ma_huyen: maHuyen }, function (response) {
        var cacLuaChon = '<option value="">Phường/xã *</option>';

        if (response.trang_thai && response.du_lieu) {
          $.each(response.du_lieu, function (viTri, xa) {
            cacLuaChon += '<option value="' + xa.WardCode + '">' + xa.WardName + '</option>';
          });

          $xa.html(cacLuaChon).prop('disabled', false);
        } else {
          $xa.html('<option value="">Không tải được phường/xã</option>').prop('disabled', false);
          toastr.error(response.thong_bao || 'Không tải được phường/xã.');
        }

        capNhatLuaChon($xa);
      }).fail(function () {
        $xa.html('<option value="">Không tải được phường/xã</option>').prop('disabled', false);
        capNhatLuaChon($xa);
      });
    }

    // Tinh phi theo dia chi da luu trong tai khoan.
    function tinhPhiDiaChiDaLuu(maDiaChi) {
      if (!maDiaChi) {
        datTrangThaiPhiVanChuyen(false, 'Vui lòng chọn địa chỉ giao hàng.');
        return;
      }

      $('.phi-van-chuyen-thanh-toan').text('Đang tính...');

      $.ajax({
        url: duongDanPhiVanChuyen,
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { ma_dia_chi_giao_hang: maDiaChi },
        success: function (response) {
          if (response.trang_thai) {
            capNhatTongTien(response.du_lieu);
          } else {
            datTrangThaiPhiVanChuyen(false, response.thong_bao);
          }
        },
        error: function (xhr) {
          var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
            ? xhr.responseJSON.thong_bao
            : 'Không tính được phí vận chuyển.';
          datTrangThaiPhiVanChuyen(false, thongBao);
          toastr.error(thongBao);
        }
      });
    }

    // Tinh phi theo dia chi moi nguoi dung dang nhap.
    function tinhPhiDiaChiMoi() {
      var maTinh = $('#ma_tinh_moi').val();
      var maHuyen = $('#ma_huyen_moi').val();
      var maXa = $('#ma_xa_moi').val();

      if (!maTinh || !maHuyen || !maXa) {
        datTrangThaiPhiVanChuyen(false, 'Vui lòng chọn đầy đủ tỉnh, quận và phường.');
        $('.phi-van-chuyen-thanh-toan').text('Chưa tính');
        return;
      }

      $('.phi-van-chuyen-thanh-toan').text('Đang tính...');

      $.ajax({
        url: duongDanPhiVanChuyen,
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          ma_tinh: maTinh,
          ma_huyen: maHuyen,
          ma_xa: maXa
        },
        success: function (response) {
          if (response.trang_thai) {
            capNhatTongTien(response.du_lieu);
          } else {
            datTrangThaiPhiVanChuyen(false, response.thong_bao);
          }
        },
        error: function (xhr) {
          var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
            ? xhr.responseJSON.thong_bao
            : 'Không tính được phí vận chuyển.';
          datTrangThaiPhiVanChuyen(false, thongBao);
          toastr.error(thongBao);
        }
      });
    }

    // Lay va hien thi thong tin dia chi da luu.
    function layDiaChiDaLuu(maDiaChi) {
      if (!maDiaChi) {
        datTrangThaiPhiVanChuyen(false, 'Vui lòng chọn địa chỉ giao hàng.');
        return;
      }

      $.ajax({
        url: duongDanDiaChi,
        type: 'GET',
        data: { ma_dia_chi_giao_hang: maDiaChi },
        success: function (response) {
          if (!response.trang_thai) {
            datTrangThaiPhiVanChuyen(false, response.thong_bao);
            return;
          }

          var diaChi = response.du_lieu;
          $('#ten-nguoi-nhan-hien-thi').text(diaChi.ho_ten);
          $('#so-dien-thoai-hien-thi').text(diaChi.so_dien_thoai);
          $('#dia-chi-hien-thi').text(diaChi.dia_chi + ', ' + diaChi.tinh_thanh);
          $('#ma_dia_chi_giao_hang').val(diaChi.ma_dia_chi_giao_hang);

          if (!diaChi.co_dia_chi_ghn) {
            datTrangThaiPhiVanChuyen(false, 'Địa chỉ này chưa có đủ mã khu vực GHN.');
            return;
          }

          tinhPhiDiaChiDaLuu(maDiaChi);
        },
        error: function () {
          datTrangThaiPhiVanChuyen(false, 'Không lấy được địa chỉ giao hàng.');
        }
      });
    }

    // Chuyen giua dia chi tai khoan va dia chi moi.
    window.chonLoaiGiaoHang = function (loaiGiaoHang) {
      $('#loai_giao_hang').val(loaiGiaoHang);
      $('.delivery-option').removeClass('active');

      if (loaiGiaoHang === 'tai_khoan') {
        $('#lua-chon-tai-khoan').addClass('active');
        $('#khu-vuc-dia-chi-da-luu').removeClass('d-none');
        $('#khu-vuc-dia-chi-moi').addClass('d-none');
        layDiaChiDaLuu($('#danh_sach_dia_chi').val());
      } else {
        $('#lua-chon-dia-chi-moi').addClass('active');
        $('#khu-vuc-dia-chi-da-luu').addClass('d-none');
        $('#khu-vuc-dia-chi-moi').removeClass('d-none');
        $('#ma_dia_chi_giao_hang').val('');
        taiDanhSachTinh();
        tinhPhiDiaChiMoi();
      }
    };

    $('#danh_sach_dia_chi').on('change', function () {
      layDiaChiDaLuu($(this).val());
    });

    $(document).on('change', '#ma_tinh_moi', function () {
      var maTinh = $(this).val();
      $('#ten_tinh_moi').val(maTinh ? $(this).find(':selected').text() : '');
      taiDanhSachHuyen(maTinh);
      tinhPhiDiaChiMoi();
    });

    $(document).on('change', '#ma_huyen_moi', function () {
      var maHuyen = $(this).val();
      $('#ten_huyen_moi').val(maHuyen ? $(this).find(':selected').text() : '');
      taiDanhSachXa(maHuyen);
      tinhPhiDiaChiMoi();
    });

    $(document).on('change', '#ma_xa_moi', function () {
      var maXa = $(this).val();
      $('#ten_xa_moi').val(maXa ? $(this).find(':selected').text() : '');
      tinhPhiDiaChiMoi();
    });

    // Chon ma trong danh sach phieu giam gia.
    $(document).on('click', '.checkout-voucher-item', function () {
      if ($(this).hasClass('is-disabled')) {
        return;
      }

      $('.checkout-voucher-item').removeClass('active');
      $(this).addClass('active');
      $('#ma_giam_gia').val($(this).data('ma-giam-gia'));
    });

    // Gui ma giam gia va cap nhat tong tien.
    $('#nut-ap-dung-phieu').on('click', function () {
      var maGiamGia = $('#ma_giam_gia').val().trim();
      var data = {
        ma_giam_gia: maGiamGia,
        ma_dia_chi_giao_hang: $('#ma_dia_chi_giao_hang').val(),
        ma_tinh: $('#ma_tinh_moi').val(),
        ma_huyen: $('#ma_huyen_moi').val(),
        ma_xa: $('#ma_xa_moi').val()
      };

      if (!maGiamGia) {
        $('#thong-bao-phieu').addClass('text-danger').text('Vui lòng nhập mã giảm giá.');
        return;
      }

      $.ajax({
        url: duongDanPhieuGiamGia,
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
        beforeSend: function () {
          $('#nut-ap-dung-phieu').prop('disabled', true).text('Đang kiểm tra...');
        },
        success: function (response) {
          $('#thong-bao-phieu').removeClass('text-danger').addClass('text-success').text(response.thong_bao);
          $('.checkout-selected-coupon').text('Đã chọn: ' + response.ma_giam_gia);
          capNhatTongTien(response.du_lieu);
        },
        error: function (xhr) {
          var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
            ? xhr.responseJSON.thong_bao
            : 'Không thể áp dụng mã giảm giá.';
          $('#thong-bao-phieu').removeClass('text-success').addClass('text-danger').text(thongBao);
        },
        complete: function () {
          $('#nut-ap-dung-phieu').prop('disabled', false).text('Áp dụng');
        }
      });
    });

    // Kiem tra thong tin dia chi moi truoc khi dat hang.
    function kiemTraDiaChiMoi() {
      var cacLoi = [];
      var hoTen = $('#ho_ten_nguoi_nhan').val().trim();
      var soDienThoai = $('#so_dien_thoai_nguoi_nhan').val().trim();
      var diaChi = $('#dia_chi_nguoi_nhan').val().trim();

      if (hoTen.length < 2) {
        cacLoi.push('Vui lòng nhập họ tên người nhận.');
      }

      if (!/^[0-9]{10,11}$/.test(soDienThoai)) {
        cacLoi.push('Số điện thoại phải có 10 đến 11 số.');
      }

      if (diaChi.length < 5) {
        cacLoi.push('Vui lòng nhập địa chỉ cụ thể.');
      }

      if (!$('#ma_tinh_moi').val() || !$('#ma_huyen_moi').val() || !$('#ma_xa_moi').val()) {
        cacLoi.push('Vui lòng chọn đầy đủ tỉnh, quận và phường.');
      }

      for (var viTri = 0; viTri < cacLoi.length; viTri++) {
        toastr.error(cacLoi[viTri]);
      }

      return cacLoi.length === 0;
    }

    // Tao du lieu dia chi de gui khi thanh toan PayPal.
    function taoDuLieuDatHangPayPal() {
      return {
        ma_giao_dich: '',
        phuong_thuc: 'paypal',
        loai_giao_hang: $('#loai_giao_hang').val(),
        ma_dia_chi_giao_hang: $('#ma_dia_chi_giao_hang').val(),
        ho_ten_nguoi_nhan: $('#ho_ten_nguoi_nhan').val(),
        so_dien_thoai_nguoi_nhan: $('#so_dien_thoai_nguoi_nhan').val(),
        dia_chi_nguoi_nhan: $('#dia_chi_nguoi_nhan').val(),
        ma_tinh: $('#ma_tinh_moi').val(),
        ma_huyen: $('#ma_huyen_moi').val(),
        ma_xa: $('#ma_xa_moi').val(),
        ten_tinh: $('#ten_tinh_moi').val(),
        ten_huyen: $('#ten_huyen_moi').val(),
        ten_xa: $('#ten_xa_moi').val()
      };
    }

    // An hien nut COD va PayPal theo phuong thuc dang chon.
    function capNhatPhuongThucThanhToan() {
      if ($('#thanh-toan-paypal').is(':checked')) {
        $('#nut-dat-hang').addClass('d-none');
        $('#paypal-button-container').removeClass('d-none');
      } else {
        $('#nut-dat-hang').removeClass('d-none');
        $('#paypal-button-container').addClass('d-none');
      }
    }

    $('input[name="phuong_thuc"]').on('change', capNhatPhuongThucThanhToan);

    $('#form-thanh-toan').on('submit', function (suKien) {
      if ($('#thanh-toan-paypal').is(':checked')) {
        suKien.preventDefault();
        return;
      }

      if ($('#loai_giao_hang').val() === 'dia_chi_moi' && !kiemTraDiaChiMoi()) {
        suKien.preventDefault();
        return;
      }

      if (!daTinhPhiVanChuyen) {
        suKien.preventDefault();
        toastr.error('Vui lòng kiểm tra phí vận chuyển trước khi đặt hàng.');
        return;
      }

      $('#nut-dat-hang').prop('disabled', true).text('Đang đặt hàng...');
    });

    // Khoi tao nut PayPal theo ham bat buoc cua PayPal SDK.
    function khoiTaoNutPayPal(soLanCho) {
      if (!document.querySelector('#paypal-button-container')) {
        return;
      }

      if (!window.paypal) {
        if (soLanCho < 20) {
          setTimeout(function () {
            khoiTaoNutPayPal(soLanCho + 1);
          }, 300);
        }
        return;
      }

      paypal.Buttons({
        createOrder: function (data, actions) {
          if ($('#loai_giao_hang').val() === 'dia_chi_moi' && !kiemTraDiaChiMoi()) {
            return Promise.reject();
          }

          if (!daTinhPhiVanChuyen) {
            toastr.error('Vui lòng kiểm tra phí vận chuyển.');
            return Promise.reject();
          }

          return actions.order.create({
            purchase_units: [
              {
                amount: {
                  value: (tongTien / 25000).toFixed(2)
                }
              }
            ]
          });
        },
        onApprove: function (data, actions) {
          return actions.order.capture().then(function (chiTietThanhToan) {
            var duLieuDatHang = taoDuLieuDatHangPayPal();
            duLieuDatHang.ma_giao_dich = chiTietThanhToan.id;

            return fetch(duongDanPayPal, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              body: JSON.stringify(duLieuDatHang)
            }).then(function (phanHoi) {
              return phanHoi.json();
            }).then(function (response) {
              if (response.trang_thai) {
                toastr.success(response.thong_bao);
                window.location.href = response.duong_dan_chuyen;
              } else {
                toastr.error(response.thong_bao || 'Không thể lưu đơn hàng.');
              }
            });
          });
        },
        onError: function () {
          toastr.error('Thanh toán PayPal không thành công.');
        }
      }).render('#paypal-button-container');
    }

    if ($('#loai_giao_hang').val() === 'tai_khoan') {
      layDiaChiDaLuu($('#danh_sach_dia_chi').val());
    } else {
      taiDanhSachTinh();
      tinhPhiDiaChiMoi();
    }

    capNhatPhuongThucThanhToan();
    khoiTaoNutPayPal(0);
  }

  // Chon so sao va gui danh gia san pham.
  var soSaoDaChon = 0;

  function toMauSoSao(soSao) {
    $('.so_sao-star').each(function () {
      var giaTriSao = Number($(this).data('value'));
      $(this).find('i').toggleClass('fas', giaTriSao <= soSao);
      $(this).find('i').toggleClass('far', giaTriSao > soSao);
    });
  }

  $('.so_sao-star').on('mouseenter', function () {
    toMauSoSao(Number($(this).data('value')));
  }).on('mouseleave', function () {
    toMauSoSao(soSaoDaChon);
  }).on('click', function (suKien) {
    suKien.preventDefault();
    soSaoDaChon = Number($(this).data('value'));
    $('#rating-value').val(soSaoDaChon);
    toMauSoSao(soSaoDaChon);
  });

  $('#review-form').on('submit', function (suKien) {
    suKien.preventDefault();

    var maSanPham = Number($(this).data('product-id'));
    var soSao = Number($('#rating-value').val());
    var binhLuan = $('#review-content').val().trim();

    if (soSao < 1 || soSao > 5) {
      toastr.error('Vui lòng chọn số sao.');
      return;
    }

    $.ajax({
      url: '/danh-gia',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        ma_san_pham: maSanPham,
        so_sao: soSao,
        binh_luan: binhLuan
      },
      success: function (response) {
        toastr.success(response.thong_bao);
        $('#review-content').val('');
        $('#rating-value').val(0);
        soSaoDaChon = 0;
        toMauSoSao(0);
        taiDanhSachDanhGia(maSanPham);
      },
      error: function (xhr) {
        var thongBao = xhr.responseJSON && xhr.responseJSON.message
          ? xhr.responseJSON.message
          : 'Không thể gửi đánh giá.';
        toastr.error(thongBao);
      }
    });
  });

  // Tai lai danh sach danh gia sau khi them moi.
  function taiDanhSachDanhGia(maSanPham) {
    $.ajax({
      url: '/danh-gia/' + maSanPham,
      type: 'GET',
      success: function (noiDung) {
        $('#product-review-list').html(noiDung);
      }
    });
  }

  // Them san pham vao danh sach yeu thich.
  $(document).on('click', '.add-to-wishlist', function (suKien) {
    suKien.preventDefault();

    var maSanPham = Number($(this).data('id'));

    if (!maSanPham) {
      toastr.error('Không tìm thấy sản phẩm.');
      return;
    }

    $.ajax({
      url: '/yeu-thich/them',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { ma_san_pham: maSanPham },
      success: function (response) {
        toastr.success(response.thong_bao);

        var modalYeuThich = document.getElementById('liton_wishlist_modal-' + maSanPham);
        if (modalYeuThich && window.bootstrap) {
          bootstrap.Modal.getOrCreateInstance(modalYeuThich).show();
        }
      },
      error: function (xhr) {
        if (xhr.status === 401) {
          window.location.href = '/dang-nhap';
          return;
        }

        var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
          ? xhr.responseJSON.thong_bao
          : 'Không thể thêm sản phẩm yêu thích.';
        toastr.error(thongBao);
      }
    });
  });

  // Xoa san pham khoi danh sach yeu thich.
  $(document).on('click', '.wishlist-product-remove', function (suKien) {
    suKien.preventDefault();

    var $dongSanPham = $(this).closest('tr');
    var maSanPham = Number($(this).data('id'));

    $.ajax({
      url: '/yeu-thich/xoa',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { ma_san_pham: maSanPham },
      success: function (response) {
        $dongSanPham.remove();
        toastr.success(response.thong_bao);
      },
      error: function (xhr) {
        var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
          ? xhr.responseJSON.thong_bao
          : 'Không thể xóa sản phẩm yêu thích.';
        toastr.error(thongBao);
      }
    });
  });

  // Cap nhat trang chi tiet khi chon bien the san pham.
  $(document).on('click', '.product-variant-option', function (suKien) {
    suKien.preventDefault();

    var $bienThe = $(this);
    var duongDanLayBienThe = $bienThe.data('variant-url');
    var duongDanChiTiet = $bienThe.attr('href');

    if (!duongDanLayBienThe || $bienThe.hasClass('out-of-stock')) {
      return;
    }

    $bienThe.addClass('loading');

    $.ajax({
      url: duongDanLayBienThe,
      type: 'GET',
      success: function (response) {
        if (!response.trang_thai) {
          return;
        }

        var sanPham = response.du_lieu;
        $('.product-variant-option').removeClass('active');
        $bienThe.addClass('active');
        window.history.pushState(null, '', duongDanChiTiet);

        $('.product-detail-name').text(sanPham.ten_hien_thi);
        $('.product-detail-price').html(sanPham.gia + '<small class="product-price-symbol">đ</small>');

        if (sanPham.dang_khuyen_mai) {
          $('.product-detail-old-price')
            .html(sanPham.gia_goc + '<small class="product-price-symbol">đ</small>')
            .show();
        } else {
          $('.product-detail-old-price').hide();
        }

        $('.product-detail-rating-number').text(sanPham.so_sao_trung_binh);
        $('.product-detail-review-count').text(sanPham.tong_danh_gia + ' đánh giá');
        $('.product-detail-sold').text('Đã bán ' + sanPham.so_luong_da_ban);
        $('.product-description-text').text(sanPham.mo_ta);
        $('.product-origin-text').text(sanPham.nguon_goc);
        $('.product-unit-text').text(sanPham.ten_bien_the);
        $('.product-category-text').text(sanPham.ten_danh_muc);
        $('.product-storage-text').text(sanPham.bao_quan);
        $('.product-brand-text').text(sanPham.thuong_hieu);
        $('.product-manufacture-text').text(sanPham.san_xuat);

        var $oSoLuong = $('.product-detail-cart-action').closest('.ltn__shop-details-inner').find('.cart-plus-minus-box');
        $oSoLuong.val(sanPham.co_the_mua ? 1 : 0).attr('data-max', sanPham.ton_kho);

        var $khuVucGioHang = $('.product-detail-cart-action');
        if (sanPham.co_the_mua) {
          $khuVucGioHang.html(
            '<a href="javascript:void(0)" class="theme-btn-1 btn btn-effect-1 add-to-cart-btn" data-id="'
            + sanPham.ma_san_pham
            + '"><i class="fas fa-shopping-cart"></i><span>Thêm vào giỏ hàng</span></a>'
          );
        } else {
          $khuVucGioHang.html('<span class="theme-btn-1 btn btn-effect-1 product-action-disabled">Hết hàng</span>');
        }

        $('.product-detail-wishlist').attr('data-id', sanPham.ma_san_pham);

        if (sanPham.cac_hinh_anh && sanPham.cac_hinh_anh.length > 0) {
          capNhatHinhAnhBienThe(sanPham);
        }
      },
      error: function () {
        toastr.error('Không thể tải thông tin biến thể.');
      },
      complete: function () {
        $bienThe.removeClass('loading');
      }
    });
  });

  // Thay anh va khoi tao lai thu vien anh cua san pham.
  function capNhatHinhAnhBienThe(sanPham) {
    var $anhLon = $('.ltn__shop-details-large-img');
    var $anhNho = $('.ltn__shop-details-small-img');

    if ($anhLon.hasClass('slick-initialized')) {
      $anhLon.slick('unslick');
    }

    if ($anhNho.hasClass('slick-initialized')) {
      $anhNho.slick('unslick');
    }

    var noiDungAnhLon = '';
    var noiDungAnhNho = '';

    $.each(sanPham.cac_hinh_anh, function (viTri, duongDanHinhAnh) {
      noiDungAnhLon += '<div class="single-large-img"><img src="' + duongDanHinhAnh + '" alt="' + sanPham.ten_hien_thi + '"></div>';
      noiDungAnhNho += '<div class="single-small-img"><img src="' + duongDanHinhAnh + '" alt="' + sanPham.ten_hien_thi + '"></div>';
    });

    $anhLon.html(noiDungAnhLon);
    $anhNho.html(noiDungAnhNho);

    if (typeof $anhLon.slick === 'function') {
      $anhLon.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.ltn__shop-details-small-img'
      });

      $anhNho.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.ltn__shop-details-large-img',
        arrows: true,
        focusOnSelect: true
      });
    }
  }

  // Mo form yeu cau doi tra tren trang chi tiet don hang.
  var nutMoDoiTra = document.getElementById('show-return-request-form');
  var khuVucDoiTra = document.getElementById('return-request-form');

  if (nutMoDoiTra && khuVucDoiTra) {
    if (khuVucDoiTra.classList.contains('is-open')) {
      nutMoDoiTra.style.display = 'none';
    }

    nutMoDoiTra.addEventListener('click', function () {
      khuVucDoiTra.classList.add('is-open');
      nutMoDoiTra.style.display = 'none';
    });
  }

});
