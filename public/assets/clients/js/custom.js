$(document).ready(function () {

  // ==================== CLIENT - DANG NHAP, DANG KY, TAI KHOAN ====================

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
  $(document).on('submit', '#update-account', function (suKien) {
    suKien.preventDefault();

    var $bieuMau = $(this);
    var $nutLuu = $bieuMau.find('button[type="submit"]');

    $.ajax({
      url: '/tai-khoan/cap-nhat',
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
        var thongBao = 'Không thể cập nhật tài khoản.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          thongBao = xhr.responseJSON.message;
        }
        toastr.error(thongBao);
      },
      complete: function () {
        $nutLuu.prop('disabled', false);
      }
    });
  });

  // ==================== CLIENT - DIA CHI TAI KHOAN ====================
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

    $.get('/giao-hang-nhanh/tinh-thanh', function (response) {
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

    $.get('/giao-hang-nhanh/quan-huyen', { ma_tinh: maTinh }, function (response) {
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

    $.get('/giao-hang-nhanh/phuong-xa', { ma_huyen: maHuyen }, function (response) {
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
    var tenTinh = '';
    if (maTinh) {
      tenTinh = $(this).find(':selected').text();
    }

    $('#province_name').val(tenTinh);
    taiHuyenTaiKhoan(maTinh);
  });

  $(document).on('change', '#ma_huyen', function () {
    var maHuyen = $(this).val();
    var tenHuyen = '';
    if (maHuyen) {
      tenHuyen = $(this).find(':selected').text();
    }

    $('#district_name').val(tenHuyen);
    taiXaTaiKhoan(maHuyen);
  });

  $(document).on('change', '#ma_xa', function () {
    var tenXa = '';
    if ($(this).val()) {
      tenXa = $(this).find(':selected').text();
    }

    $('#ward_name').val(tenXa);
  });

  // Gui form doi mat khau.
  $(document).on('submit', '#change-password-form', function (suKien) {
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
      url: '/tai-khoan/doi-mat-khau',
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
        var thongBao = 'Không thể đổi mật khẩu.';
        if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
          thongBao = xhr.responseJSON.thong_bao;
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          thongBao = xhr.responseJSON.message;
        }
        toastr.error(thongBao);
      }
    });
  });

  // Kiem tra dia chi truoc khi gui.
  $(document).on('submit', '#addAddressForm', function (suKien) {
    var soDienThoai = $('#so_dien_thoai').val().trim();

    if (!/^0[0-9]{9,10}$/.test(soDienThoai)) {
      suKien.preventDefault();
      toastr.error('Số điện thoại phải bắt đầu bằng 0 và có 10 đến 11 số.');
      return;
    }

    if (!$('#ma_tinh').val() || !$('#ma_huyen').val() || !$('#ma_xa').val()) {
      suKien.preventDefault();
      toastr.error('Vui lòng chọn đầy đủ tỉnh, quận và phường.');
    }
  });


  // ==================== CLIENT - TRANG DANH SACH SAN PHAM ====================
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
    trangHienTai = 1;
    if (ketQuaTrang) {
      trangHienTai = Number(ketQuaTrang[1]);
    }
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

  // ==================== CLIENT - SO LUONG, GIO HANG, MINI CART ====================
  // Khoi tao nut tang giam so luong cho xem nhanh, chi tiet va gio hang.
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

  $(document).on('click', '.qtybutton', function (suKien) {
    suKien.preventDefault();

    var $oSoLuong = $(this).closest('.cart-plus-minus').find('.cart-plus-minus-box');
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
      .closest('.ltn__product-item, .ltn__modal-area, .modal-product-info, .shop-details-info')
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
        taiGioHangNho(false);

        if ($nutThem.hasClass('buy-now-btn')) {
          window.location.href = '/thanh-toan';
          return;
        }

        toastr.success(response.thong_bao);
      },
      error: function (xhr) {
        var thongBao = 'Không thể thêm sản phẩm vào giỏ hàng.';
        if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
          thongBao = xhr.responseJSON.thong_bao;
        }
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

        var $dongSanPham = $oSoLuong.closest('.mini-cart-item');
        $dongSanPham.find('.mini-cart-item-total').html(response.tam_tinh + '<small>d</small>');

        if (taiLaiGioHangNho) {
          taiGioHangNho(false);
        }
      },
      error: function (xhr) {
        var thongBao = 'Không thể cập nhật giỏ hàng.';
        if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
          thongBao = xhr.responseJSON.thong_bao;
        }
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


  // ==================== CLIENT - TRANG THANH TOAN ====================
  if (window.location.pathname === "/thanh-toan") {
    var $tongKetThanhToan = $('#tong-ket-thanh-toan');
    var tongTien = Number($tongKetThanhToan.data('tong-tien')) || 0;
    var daTinhPhiVanChuyen = Number($tongKetThanhToan.data('san-sang')) === 1;
    var daTaiTinhThanh = false;
    var boQuaLoiPayPal = false;

    function dinhDangTien(soTien) {
      return Number(soTien || 0).toLocaleString('vi-VN') + ' đ';
    }

    function capNhatNiceSelect($select) {
      if (typeof $select.niceSelect === 'function') {
        $select.niceSelect('update');
      }
    }

    function hienThongBaoPhi(thongBao) {
      daTinhPhiVanChuyen = false;
      $('.phi-van-chuyen-thanh-toan').text('Chưa tính');
      $('.thong-bao-phi-van-chuyen').removeClass('d-none').find('td').text(thongBao);
    }

    function capNhatTongTien(tien) {
      $('.phi-van-chuyen-thanh-toan').text(dinhDangTien(tien.phi_van_chuyen));
      $('.so-tien-giam-thanh-toan').text(dinhDangTien(tien.so_tien_giam));
      $('.tong-tien-thanh-toan').text(dinhDangTien(tien.tong_tien));

      $tongKetThanhToan.data('tam-tinh', tien.tam_tinh);
      $tongKetThanhToan.data('phi-van-chuyen', tien.phi_van_chuyen);
      $tongKetThanhToan.data('so-tien-giam', tien.so_tien_giam);
      $tongKetThanhToan.data('tong-tien', tien.tong_tien);

      tongTien = Number(tien.tong_tien) || 0;
      daTinhPhiVanChuyen = true;
      $('.thong-bao-phi-van-chuyen').addClass('d-none').find('td').text('');
    }

    function taiDanhSachTinh() {
      if (daTaiTinhThanh || $('#ma_tinh_moi').length === 0) {
        return;
      }

      $('#ma_tinh_moi').html('<option value="">Đang tải...</option>').prop('disabled', true);
      capNhatNiceSelect($('#ma_tinh_moi'));

      $.get('/giao-hang-nhanh/tinh-thanh', function (response) {
        var html = '<option value="">Tỉnh/thành *</option>';

        if (response.trang_thai && response.du_lieu) {
          $.each(response.du_lieu, function (viTri, tinh) {
            html += '<option value="' + tinh.ProvinceID + '">' + tinh.ProvinceName + '</option>';
          });
          daTaiTinhThanh = true;
        } else {
          toastr.error(response.thong_bao || 'Không tải được tỉnh/thành.');
        }

        $('#ma_tinh_moi').html(html).prop('disabled', false);
        capNhatNiceSelect($('#ma_tinh_moi'));
      }).fail(function () {
        $('#ma_tinh_moi').html('<option value="">Không tải được tỉnh/thành</option>').prop('disabled', false);
        capNhatNiceSelect($('#ma_tinh_moi'));
        toastr.error('Không thể kết nối dịch vụ địa chỉ.');
      });
    }

    function taiDanhSachHuyen(maTinh) {
      $('#ma_huyen_moi').html('<option value="">Quận/huyện *</option>').prop('disabled', true);
      $('#ma_xa_moi').html('<option value="">Phường/xã *</option>').prop('disabled', true);
      $('#ten_huyen_moi, #ten_xa_moi').val('');
      capNhatNiceSelect($('#ma_huyen_moi'));
      capNhatNiceSelect($('#ma_xa_moi'));

      if (!maTinh) {
        return;
      }

      $.get('/giao-hang-nhanh/quan-huyen', { ma_tinh: maTinh }, function (response) {
        var html = '<option value="">Quận/huyện *</option>';

        if (response.trang_thai && response.du_lieu) {
          $.each(response.du_lieu, function (viTri, huyen) {
            html += '<option value="' + huyen.DistrictID + '">' + huyen.DistrictName + '</option>';
          });
          $('#ma_huyen_moi').prop('disabled', false);
        } else {
          toastr.error(response.thong_bao || 'Không tải được quận/huyện.');
        }

        $('#ma_huyen_moi').html(html);
        capNhatNiceSelect($('#ma_huyen_moi'));
      });
    }

    function taiDanhSachXa(maHuyen) {
      $('#ma_xa_moi').html('<option value="">Phường/xã *</option>').prop('disabled', true);
      $('#ten_xa_moi').val('');
      capNhatNiceSelect($('#ma_xa_moi'));

      if (!maHuyen) {
        return;
      }

      $.get('/giao-hang-nhanh/phuong-xa', { ma_huyen: maHuyen }, function (response) {
        var html = '<option value="">Phường/xã *</option>';

        if (response.trang_thai && response.du_lieu) {
          $.each(response.du_lieu, function (viTri, xa) {
            html += '<option value="' + xa.WardCode + '">' + xa.WardName + '</option>';
          });
          $('#ma_xa_moi').prop('disabled', false);
        } else {
          toastr.error(response.thong_bao || 'Không tải được phường/xã.');
        }

        $('#ma_xa_moi').html(html);
        capNhatNiceSelect($('#ma_xa_moi'));
      });
    }

    function tinhPhiVanChuyen() {
      var data = {};

      if ($('input[name="loai_giao_hang"]:checked').val() === 'tai_khoan') {
        data.ma_dia_chi_giao_hang = $('#danh_sach_dia_chi').val();

        if (!data.ma_dia_chi_giao_hang) {
          hienThongBaoPhi('Vui lòng chọn địa chỉ giao hàng.');
          return;
        }
      } else {
        data.ma_tinh = $('#ma_tinh_moi').val();
        data.ma_huyen = $('#ma_huyen_moi').val();
        data.ma_xa = $('#ma_xa_moi').val();

        if (!data.ma_tinh || !data.ma_huyen || !data.ma_xa) {
          hienThongBaoPhi('Vui lòng chọn đầy đủ tỉnh, quận và phường.');
          return;
        }
      }

      $('.phi-van-chuyen-thanh-toan').text('Đang tính...');

      $.ajax({
        url: '/thanh-toan/phi-van-chuyen',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
        success: function (response) {
          if (response.trang_thai) {
            capNhatTongTien(response.du_lieu);
          } else {
            hienThongBaoPhi(response.thong_bao || 'Không tính được phí vận chuyển.');
          }
        },
        error: function (xhr) {
          var thongBao = 'Không tính được phí vận chuyển.';
          if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
            thongBao = xhr.responseJSON.thong_bao;
          }
          hienThongBaoPhi(thongBao);
          toastr.error(thongBao);
        }
      });
    }

    function layDiaChiDaLuu(maDiaChi) {
      if (!maDiaChi) {
        hienThongBaoPhi('Vui lòng chọn địa chỉ giao hàng.');
        return;
      }

      $.get('/thanh-toan/dia-chi', { ma_dia_chi_giao_hang: maDiaChi }, function (response) {
        if (!response.trang_thai) {
          hienThongBaoPhi(response.thong_bao || 'Không lấy được địa chỉ giao hàng.');
          return;
        }

        var diaChi = response.du_lieu;
        $('#ten-nguoi-nhan-hien-thi').text(diaChi.ho_ten);
        $('#so-dien-thoai-hien-thi').text(diaChi.so_dien_thoai);
        $('#dia-chi-hien-thi').text(diaChi.dia_chi + ', ' + diaChi.tinh_thanh);

        if (!diaChi.co_dia_chi_ghn) {
          hienThongBaoPhi('Địa chỉ này chưa có đủ mã khu vực GHN.');
          return;
        }

        tinhPhiVanChuyen();
      }).fail(function () {
        hienThongBaoPhi('Không lấy được địa chỉ giao hàng.');
      });
    }

    function capNhatPhuongThucThanhToan() {
      var chiDuocPayPal = $('input[name="loai_giao_hang"]:checked').val() === 'dia_chi_moi' || $('#lua-chon-tai-khoan').length === 0;

      if (chiDuocPayPal) {
        $('.checkout-cod-card').addClass('d-none');
        $('#thanh-toan-tien-mat').prop('checked', false).prop('disabled', true);
        $('#thanh-toan-paypal').prop('checked', true);
        $('.checkout-paypal-only-alert').removeClass('d-none');
      } else {
        $('.checkout-cod-card').removeClass('d-none');
        $('#thanh-toan-tien-mat').prop('disabled', false);
        $('.checkout-paypal-only-alert').addClass('d-none');
      }

      if ($('#thanh-toan-paypal').is(':checked')) {
        $('#nut-dat-hang').addClass('d-none');
        $('#paypal-button-container').removeClass('d-none');
      } else {
        $('#nut-dat-hang').removeClass('d-none');
        $('#paypal-button-container').addClass('d-none');
      }
    }

    window.chonLoaiGiaoHang = function (loaiGiaoHang) {
      $('input[name="loai_giao_hang"][value="' + loaiGiaoHang + '"]').prop('checked', true);
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
        taiDanhSachTinh();
        tinhPhiVanChuyen();
      }

      capNhatPhuongThucThanhToan();
    };

    $('#danh_sach_dia_chi').on('change', function () {
      layDiaChiDaLuu($(this).val());
    });

    $('#ma_tinh_moi').on('change', function () {
      $('#ten_tinh_moi').val($(this).val() ? $(this).find(':selected').text() : '');
      taiDanhSachHuyen($(this).val());
      tinhPhiVanChuyen();
    });

    $('#ma_huyen_moi').on('change', function () {
      $('#ten_huyen_moi').val($(this).val() ? $(this).find(':selected').text() : '');
      taiDanhSachXa($(this).val());
      tinhPhiVanChuyen();
    });

    $('#ma_xa_moi').on('change', function () {
      $('#ten_xa_moi').val($(this).val() ? $(this).find(':selected').text() : '');
      tinhPhiVanChuyen();
    });

    $('.checkout-voucher-item').on('click', function () {
      if ($(this).hasClass('is-disabled')) {
        return;
      }

      $('.checkout-voucher-item').removeClass('active');
      $(this).addClass('active');
      $('#ma_giam_gia').val($(this).data('ma-giam-gia'));
    });

    $('#nut-ap-dung-phieu').on('click', function () {
      var maGiamGia = $('#ma_giam_gia').val().trim();

      if (!maGiamGia) {
        $('#thong-bao-phieu').removeClass('text-success').addClass('text-danger').text('Vui lòng nhập mã giảm giá.');
        return;
      }

      $.ajax({
        url: '/thanh-toan/phieu-giam-gia',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          ma_giam_gia: maGiamGia,
          ma_dia_chi_giao_hang: $('input[name="loai_giao_hang"]:checked').val() === 'tai_khoan' ? $('#danh_sach_dia_chi').val() : '',
          ma_tinh: $('#ma_tinh_moi').val(),
          ma_huyen: $('#ma_huyen_moi').val(),
          ma_xa: $('#ma_xa_moi').val()
        },
        beforeSend: function () {
          $('#nut-ap-dung-phieu').prop('disabled', true).text('Đang kiểm tra...');
        },
        success: function (response) {
          $('#thong-bao-phieu').removeClass('text-danger').addClass('text-success').text(response.thong_bao);
          $('.checkout-selected-coupon').text('Đã chọn: ' + response.ma_giam_gia);
          capNhatTongTien(response.du_lieu);
        },
        error: function (xhr) {
          var thongBao = 'Không thể áp dụng mã giảm giá.';
          if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
            thongBao = xhr.responseJSON.thong_bao;
          }
          $('#thong-bao-phieu').removeClass('text-success').addClass('text-danger').text(thongBao);
        },
        complete: function () {
          $('#nut-ap-dung-phieu').prop('disabled', false).text('Áp dụng');
        }
      });
    });

    function kiemTraDiaChiMoi() {
      var hoTen = $('#ho_ten_nguoi_nhan').val().trim();
      var soDienThoai = $('#so_dien_thoai_nguoi_nhan').val().trim();
      var diaChi = $('#dia_chi_nguoi_nhan').val().trim();

      if (hoTen.length < 3) {
        toastr.error('Vui lòng nhập họ tên người nhận.');
        return false;
      }

      if (!/^0[0-9]{9}$/.test(soDienThoai)) {
        toastr.error('Số điện thoại phải bắt đầu bằng 0 và có 10 số.');
        return false;
      }

      if (diaChi.length < 5) {
        toastr.error('Vui lòng nhập địa chỉ cụ thể.');
        return false;
      }

      if (!$('#ma_tinh_moi').val() || !$('#ma_huyen_moi').val() || !$('#ma_xa_moi').val()) {
        toastr.error('Vui lòng chọn đầy đủ tỉnh, quận và phường.');
        return false;
      }

      return true;
    }

    $('#form-thanh-toan').on('submit', function (suKien) {
      if ($('#thanh-toan-paypal').is(':checked')) {
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
          if ($('input[name="loai_giao_hang"]:checked').val() === 'dia_chi_moi' && !kiemTraDiaChiMoi()) {
            boQuaLoiPayPal = true;
            return Promise.reject();
          }

          if (!daTinhPhiVanChuyen) {
            toastr.error('Vui lòng kiểm tra phí vận chuyển.');
            boQuaLoiPayPal = true;
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
            var duLieuDatHang = {
              ma_giao_dich: chiTietThanhToan.id,
              phuong_thuc: 'paypal',
              loai_giao_hang: $('input[name="loai_giao_hang"]:checked').val(),
              ma_dia_chi_giao_hang: $('input[name="loai_giao_hang"]:checked').val() === 'tai_khoan' ? $('#danh_sach_dia_chi').val() : '',
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

            return fetch('/thanh-toan/paypal', {
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
          if (boQuaLoiPayPal) {
            boQuaLoiPayPal = false;
            return;
          }

          toastr.error('Thanh toán PayPal không thành công.');
        }
      }).render('#paypal-button-container');
    }

    if ($('input[name="loai_giao_hang"]:checked').val() === 'tai_khoan') {
      layDiaChiDaLuu($('#danh_sach_dia_chi').val());
    } else {
      taiDanhSachTinh();
      tinhPhiVanChuyen();
    }

    $('input[name="phuong_thuc"]').on('change', capNhatPhuongThucThanhToan);
    capNhatPhuongThucThanhToan();
    khoiTaoNutPayPal(0);
  }
  // ==================== CLIENT - DANH GIA SAN PHAM ====================
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
        var thongBao = 'Không thể gửi đánh giá.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          thongBao = xhr.responseJSON.message;
        }
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
  // ==================== CLIENT - YEU THICH ====================
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


      },
      error: function (xhr) {
        if (xhr.status === 401) {
          window.location.href = '/dang-nhap';
          return;
        }

        var thongBao = 'Không thể thêm sản phẩm yêu thích.';
        if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
          thongBao = xhr.responseJSON.thong_bao;
        }
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
        var thongBao = 'Không thể xóa sản phẩm yêu thích.';
        if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
          thongBao = xhr.responseJSON.thong_bao;
        }
        toastr.error(thongBao);
      }
    });
  });

  // ==================== CLIENT - BIEN THE SAN PHAM ====================
  // Cap nhat trang chi tiet khi chon bien the san pham.
  $(document).on('click', '.product-variant-option', function (suKien) {
    suKien.preventDefault();

    var $bienThe = $(this);
    var duongDanChiTiet = $bienThe.attr('href');

    if (!duongDanChiTiet || $bienThe.hasClass('out-of-stock')) {
      return;
    }

    $bienThe.addClass('loading');

    $.ajax({
      url: $bienThe.attr('href') + '/bien-the',
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
        $('.product-detail-stock').text(sanPham.ten_ton_kho);
        $('.product-description-text').text(sanPham.mo_ta);
        if (sanPham.mo_ta) {
          $('.product-description-text').removeClass('d-none');
        } else {
          $('.product-description-text').addClass('d-none');
        }

        $('.product-storage-text').text(sanPham.bao_quan);
        if (sanPham.bao_quan) {
          $('.product-storage-row').removeClass('d-none');
        } else {
          $('.product-storage-row').addClass('d-none');
        }

        $('.product-brand-text').text(sanPham.thuong_hieu);
        if (sanPham.thuong_hieu) {
          $('.product-brand-row').removeClass('d-none');
        } else {
          $('.product-brand-row').addClass('d-none');
        }

        $('.product-manufacture-text').text(sanPham.san_xuat);
        if (sanPham.san_xuat) {
          $('.product-manufacture-row').removeClass('d-none');
        } else {
          $('.product-manufacture-row').addClass('d-none');
        }

        $('.product-use-text').text(sanPham.cach_dung);
        if (sanPham.cach_dung) {
          $('.product-use-row').removeClass('d-none');
        } else {
          $('.product-use-row').addClass('d-none');
        }

        $('.product-ingredients-text').text(sanPham.thanh_phan);
        if (sanPham.thanh_phan) {
          $('.product-ingredients-row').removeClass('d-none');
        } else {
          $('.product-ingredients-row').addClass('d-none');
        }

        var $oSoLuong = $('.product-detail-cart-action').closest('.modal-product-info, .shop-details-info').find('.cart-plus-minus-box');
        var soLuongMacDinh = 0;
        if (sanPham.co_the_mua) {
          soLuongMacDinh = 1;
        }

        $oSoLuong.val(soLuongMacDinh).attr('data-max', sanPham.ton_kho);

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

  // ==================== CLIENT - CHI TIET DON HANG / DOI TRA ====================
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
