$(document).ready(function () {

  //  ĐĂNG NHẬP / ĐĂNG KÝ
  // Validate form ĐĂNG KÝ
  $('#register-form').submit(function (e) {
    // Lấy giá trị từ các input
    let name = $('input[name="name"]').val();
    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let confirmPassword = $('input[name="confirmPassword"]').val();
    let checkbox1 = $('input[name="checkbox1"]').is(':checked');
    let checkbox2 = $('input[name="checkbox2"]').is(':checked');

    let errorMessage = "";
    // Kiểm tra tên >= 3 ký tự
    if (name.length < 3) {
      errorMessage += "Họ và tên phải có ít nhất 3 ký tự. <br>";
    }
    // Regex kiểm tra email đúng format
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }
    // Mật khẩu tối thiểu 6 ký tự
    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }
    // Kiểm tra hai mật khẩu khớp nhau
    if (password != confirmPassword) {
      errorMessage += "Mật khẩu nhập lại không khớp. <br>";
    }
    // Người dùng có tick điều khoản chưa?
    if (!checkbox1 || !checkbox2) {
      errorMessage += "Bạn phải đồng ý với các điều khoản trước khi tạo tài khoản. <br>";
    }
    // Nếu có lỗi chặn submit
    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");
      e.preventDefault();
      return;
    }
  });
  // Validate form ĐĂNG NHẬP
  $('#login-form').submit(function (e) {
    toastr.clear(); // Xóa thông báo cũ

    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let errorMessage = "";
    // Regex email
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // Kiểm tra email
    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }
    // Kiểm tra mật khẩu
    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }
    // Nếu lỗi  chặn submit
    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");
      e.preventDefault();
    }
  });
  // Validate form RESET PASSWORD
  $('#reset-pasword-form').submit(function (e) {
    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let confirmPassword = $('input[name="password_confirmation"]').val();
    let errorMessage = "";
    // Regex email
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }

    // Mật khẩu phải >= 6 ký tự
    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }

    // Mật khẩu nhập lại không khớp
    if (password != confirmPassword) {
      errorMessage += "Mật khẩu nhập lại không khớp. <br>";
    }

    // Nếu có lỗi  chặn submit
    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");
      e.preventDefault();
    }
  });
  // ACCOUNT 


  // cập nhật thông tin tài khoản
  $("#update-account").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    let urlUpdate = $(this).attr('action');
    // Thiết lập CSRF token
    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
      }
    });

    $.ajax({
      url: urlUpdate,
      type: 'POST',
      data: formData,
      processData: false, // Không xử lý data
      contentType: false, // Không set header
      beforeSend: function () {
        $(".btn-wapper button").text("Đang cập nhật...").attr("disable", true);
      },

      success: function (response) {
        if (response.success) {
          toastr.success(response.message);
        }
      },

      error: function (xhr) {
        let errors = xhr.responseJSON.errors;
        $.each(errors, function (key, value) {
          toastr.error(value[0]); // Hiển thị từng lỗi
        });
      },

      complete: function () {
        $(".btn-wapper button").text("Cập nhật").attr("disabled", false);
      },
    })
  })
  // ─── ĐỊA CHỈ GHN: load tỉnh/quận/phường theo cascade ───────────────────

  var baseUrl = $('meta[name="base-url"]').attr('content') || '';
  var ghnProvinceLoaded = false;
  var ghnProvinceLoading = false;

  function loadProvinces(forceReload) {
    var $province = $('#province_id');

    if ($province.length === 0) {
      return;
    }

    if (ghnProvinceLoading) {
      return;
    }

    if (!forceReload && ghnProvinceLoaded && $province.find('option').length > 1) {
      return;
    }

    ghnProvinceLoading = true;
    $province.html('<option value="">--Đang tải--</option>').prop('disabled', true).niceSelect('update');
    $('#district_id').html('<option value="">--Chọn quận/huyện--</option>').prop('disabled', true).niceSelect('update');
    $('#ward_id').html('<option value="">--Chọn phường/xã--</option>').prop('disabled', true).niceSelect('update');

    $.get(baseUrl + '/ghn/provinces', function (res) {
      $province.prop('disabled', false);
      if (res.status && res.data) {
        var options = '<option value="">--Chọn tỉnh/thành--</option>';
        $.each(res.data, function (i, p) {
          options += '<option value="' + p.ProvinceID + '">' + p.ProvinceName + '</option>';
        });
        $province.html(options).niceSelect('update');
        ghnProvinceLoaded = true;
      } else {
        $province.html('<option value="">--Lỗi tải tỉnh/thành--</option>').niceSelect('update');
        toastr.error('Không thể tải danh sách tỉnh/thành. Kiểm tra cấu hình GHN.');
      }
    }).fail(function () {
      $province.prop('disabled', false).html('<option value="">--Lỗi kết nối--</option>').niceSelect('update');
      toastr.error('Lỗi kết nối khi tải tỉnh/thành.');
    }).always(function () {
      ghnProvinceLoading = false;
    });
  }

  $(document).on('change', '#province_id', function () {
    var provinceId = $(this).val();
    var provinceName = $(this).find(':selected').text();
    var $district = $('#district_id');
    var $ward = $('#ward_id');

    // Lưu tên tỉnh vào hidden input
    $('#province_name').val(provinceId ? provinceName : '');
    $('#district_name').val('');
    $('#ward_name').val('');

    $district.html('<option value="">--Đang tải--</option>').prop('disabled', true).niceSelect('update');
    $ward.html('<option value="">--Chọn phường/xã--</option>').prop('disabled', true).niceSelect('update');

    if (!provinceId) {
      $district.html('<option value="">--Chọn quận/huyện--</option>').niceSelect('update');
      return;
    }

    $.get(baseUrl + '/ghn/districts', { province_id: provinceId }, function (res) {
      $district.prop('disabled', false);
      if (res.status && res.data) {
        var options = '<option value="">--Chọn quận/huyện--</option>';
        $.each(res.data, function (i, d) {
          options += '<option value="' + d.DistrictID + '">' + d.DistrictName + '</option>';
        });
        $district.html(options).niceSelect('update');
      } else {
        $district.html('<option value="">--Lỗi tải quận/huyện--</option>').niceSelect('update');
        toastr.error('Không thể tải danh sách quận/huyện.');
      }
    }).fail(function () {
      $district.prop('disabled', false).html('<option value="">--Lỗi kết nối--</option>').niceSelect('update');
    });
  });

  $(document).on('change', '#district_id', function () {
    var districtId = $(this).val();
    var districtName = $(this).find(':selected').text();
    var $ward = $('#ward_id');

    // Lưu tên quận vào hidden input
    $('#district_name').val(districtId ? districtName : '');
    $('#ward_name').val('');

    $ward.html('<option value="">--Đang tải--</option>').prop('disabled', true).niceSelect('update');

    if (!districtId) {
      $ward.html('<option value="">--Chọn phường/xã--</option>').niceSelect('update');
      return;
    }

    $.get(baseUrl + '/ghn/wards', { district_id: districtId }, function (res) {
      $ward.prop('disabled', false);
      if (res.status && res.data) {
        var options = '<option value="">--Chọn phường/xã--</option>';
        $.each(res.data, function (i, w) {
          options += '<option value="' + w.WardCode + '" data-district="' + districtId + '">' + w.WardName + '</option>';
        });
        $ward.html(options).niceSelect('update');
      } else {
        $ward.html('<option value="">--Lỗi tải phường/xã--</option>').niceSelect('update');
        toastr.error('Không thể tải danh sách phường/xã.');
      }
    }).fail(function () {
      $ward.prop('disabled', false).html('<option value="">--Lỗi kết nối--</option>').niceSelect('update');
    });
  });

  // Khi chọn phường/xã xong => cập nhật tên phường/xã
  $(document).on('change', '#ward_id', function () {
    var wardName = $(this).find(':selected').text();

    $('#ward_name').val($(this).val() ? wardName : '');
  });

  if ($('#addAddressForm').length > 0) {
    loadProvinces();
  }

  // Mở modal thêm địa chỉ => load tỉnh
  $(document).on('show.bs.modal shown.bs.modal', '#addAddressModal', function () {
    loadProvinces();
  });

  $(document).on('focus click', '#province_id', function () {
    if ($(this).find('option').length <= 1) {
      loadProvinces(true);
    }
  });
  //   ĐỔI MẬT KHẨU
  $('#change-password-form').submit(function (e) {
    e.preventDefault();

    let current_password = $('input[name="current_password"]').val().trim();
    let new_password = $('input[name="new_password"]').val().trim();
    let confirm_new_password = $('input[name="confirm_new_password"]').val().trim();
    let errorMessage = "";

    // Validate mật khẩu
    if (current_password.length < 6) {
      errorMessage += "Mật khẩu cũ phải có ít nhất 6 ký tự. <br>";
    }

    if (new_password.length < 6) {
      errorMessage += "Mật khẩu mới phải có ít nhất 6 ký tự. <br>";
    }

    if (new_password != confirm_new_password) {
      errorMessage += "Mật khẩu nhập lại không khớp. <br>";
    }

    // Hiển thị lỗi
    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");
      return;
    }

    let formData = $(this).serialize();
    let urlUpdate = $(this).attr('action');

    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
      }
    });

    $.ajax({
      url: urlUpdate,
      type: 'POST',
      data: formData,

      beforeSend: function () {
        $(".btn-wapper button").text("Đang cập nhật...").attr("disable", true);
      },

      success: function (response) {
        if (response.success) {
          toastr.success(response.message);
          $('#change-password-form')[0].reset(); // Xóa input
        } else {
          toastr.error(response.message);
        }
      },

      error: function (xhr) {
        let errors = xhr.responseJSON.errors;
        $.each(errors, function (key, value) {
          toastr.error(value[0]); // Hiển thị từng lỗi
        });
      },

      complete: function () {
        $(".btn-wapper button").text("Cập nhật").attr("disabled", false);
      },
    })
  });

  //   FORM ĐỊA CHỈ
  $('#addAddressForm').submit(function (e) {
    e.preventDefault();

    let isValid = true;
    $('.error-message').remove();

    let fullName = $('#full_name').val().trim();
    let address = $('#address').val().trim();
    let phone = $('#phone').val().trim();
    let provinceId = $('#province_id').val();
    let districtId = $('#district_id').val();
    let wardId = $('#ward_id').val();

    // Validate tên
    if (fullName.length < 3) {
      isValid = false;
      $('#full_name').after('<p class="error-message text-danger">Họ và tên không được ít hơn 3 ký tự</p>');
    }

    if (address.length < 5) {
      isValid = false;
      $('#address').after('<p class="error-message text-danger">Vui lòng nhập địa chỉ cụ thể.</p>');
    }

    // Validate số điện thoại 10–11 số
    let phoneRegex = /^[0-9]{10,11}$/;
    if (!phoneRegex.test(phone)) {
      isValid = false;
      $('#phone').after('<p class="error-message text-danger">Số điện thoại không hợp lệ.</p>');
    }

    if (!provinceId) {
      isValid = false;
      $('#province_id').after('<p class="error-message text-danger">Vui lòng chọn tỉnh/thành.</p>');
    }

    if (!districtId) {
      isValid = false;
      $('#district_id').after('<p class="error-message text-danger">Vui lòng chọn quận/huyện.</p>');
    }

    if (!wardId) {
      isValid = false;
      $('#ward_id').after('<p class="error-message text-danger">Vui lòng chọn phường/xã.</p>');
    }

    if (isValid) {
      this.submit();
    }
  });

  //  PAGE PRODUCT (LỌC, PHÂN TRANG, SẮP XẾP, ADD TO CART)

  let currentPage = 1; // Trang hiện tại khi phân trang

  // Hàm kích hoạt nút + - số lượng sản phẩm
  function activateCartPlusMinus() {
    $(".cart-plus-minus").each(function () {
      var $this = $(this),
        $input = $this.find(".cart-plus-minus-box"),
        $inc = $('<span class="inc qtybutton">+</span>'),
        $dec = $('<span class="dec qtybutton">-</span>');


      if ($this.find(".qtybutton").length === 0) {
        $this.prepend($dec);
        $this.append($inc);
      }

      // Chỉ hoạt động khi không ở trang Cart
      if (window.location.pathname !== '/cart') {
        $this.off("click").on("click", ".qtybutton", function () {
          var oldValue = parseInt($input.val()) || 1;
          var max = parseInt($input.data("max")) || 9999;

          if ($(this).hasClass("inc")) {
            if (oldValue < max) $input.val(oldValue + 1);
          } else {
            if (oldValue > 1) $input.val(oldValue - 1);
          }
        });
      }
    });
  }

  // AJAX lấy sản phẩm theo filter
  function fetchProducts() {

    let category_id = $(".category-filter.active").data('id') || '';
    let minPrice = $(".slider-range").slider('values', 0);
    let maxPrice = $(".slider-range").slider('values', 1);
    let sort_by = $("#sort-by").val();

    $.ajaxSetup({
      headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') }
    });

    $.ajax({
      url: 'products/filter?page=' + currentPage,
      type: 'GET',
      data: {
        category_id: category_id,
        minPrice: minPrice,
        maxPrice: maxPrice,
        sort_by: sort_by,
      },

      beforeSend: function () {
        $("#loading-spinner").show();
        $("#liton_product_grid").hide();
      },

      success: function (response) {

        // Render sản phẩm
        $("#liton_product_grid").html(response.products);

        // Render phân trang
        $(".ltn__pagination").html(response.pagination);

        // Kích hoạt lại plugin nút + -
        activateCartPlusMinus();
      },

      complete: function () {
        $("#loading-spinner").hide();
        $("#liton_product_grid").show();
      },

      error: function () {
        alert("Có lỗi xảy ra khi tải sản phẩm!");
      }
    });
  }

  // CLICK phân trang
  $(document).on('click', '.pagination-link', function (e) {
    e.preventDefault();
    let pageUrl = $(this).attr('href');
    currentPage = pageUrl.split('page=')[1];
    fetchProducts();
  });

  // CLICK chọn danh mục
  $(document).on("click", ".category-filter", function () {
    $(".category-filter").removeClass('active');
    $(this).addClass('active');
    currentPage = 1;
    fetchProducts();
  });

  // Sắp xếp sản phẩm
  $("#sort-by").change(function () {
    currentPage = 1;
    fetchProducts();
  });

  // Slider lọc giá
  $(".slider-range").slider({
    range: true,
    min: 0,
    max: 500000,
    values: [0, 500000],
    slide: function (event, ui) {
      $(".amount").val(ui.values[0] + " - " + ui.values[1] + "vnđ");
    },
    change: function () {
      currentPage = 1;
      fetchProducts();
    }
  });

  // Hiển thị giá load mặc định
  $(".amount").val(
    $(".slider-range").slider("values", 0) +
    " - " +
    $(".slider-range").slider("values", 1) +
    "vnđ"
  );

  // AJAX thêm sản phẩm vào giỏ hàng
  $(document).on('click', '.add-to-cart-btn', function (e) {
    e.preventDefault();

    let productId = $(this).data('id');
    let quantity = $(this)
      .closest('.ltn__product-item, .ltn__product-details-menu-2')
      .find('.cart-plus-minus-box')
      .val() || 1;

    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $.ajax({
      url: '/cart/add',
      type: 'POST',
      data: { product_id: productId, quantity: quantity },

      success: function (response) {

        toastr.success(response.message);
        $('#cart_count').text(response.cart_count);

        // Đóng modal QuickView (nếu đang mở)
        $('.modal.show').modal('hide');

        // Mở modal “Thêm vào giỏ hàng thành công”
        $('#add_to_cart_modal-' + productId).modal('show');
      },

      error: function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
          toastr.error(xhr.responseJSON.message);
        } else {
          alert('Lỗi thêm vào giỏ hàng!');
        }
      }
    });
  });

  // Modal Quickview mở → kích hoạt + -
  $(document).on('shown.bs.modal', '.ltn__modal-area', function () {
    activateCartPlusMinus();
  });

  activateCartPlusMinus();

  //  – MINI CART (GIỎ HÀNG NHANH)

  // Mở mini cart
  $('.mini-cart-icon').on('click', function () {
    $.ajax({
      url: '/mini-cart',
      type: 'GET',
      success: function (response) {
        if (response.status) {
          $('#ltn__utilize-cart-menu .ltn__utilize-menu-inner')
            .html(response.html);

          $('#ltn__utilize-cart-menu').addClass('ltn__utilize-open');
        } else {
          toastr.error('Không thể tải giỏ hàng!');
        }
      }
    });
  });

  // Đóng mini cart
  $(document).on('click', '.ltn__utilize-close', function () {
    $('#ltn__utilize-cart-menu').removeClass('ltn__utilize-open');
    $('.ltn__utilize-overlay').hide();
  });

  // Xóa sản phẩm khỏi mini cart
  $(document).on('click', '.remove-from-cart-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    let productId = $(this).data('product-id');
    let row = $(this).closest('.mini-cart-item');

    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $.ajax({
      url: '/cart/remove',
      type: 'POST',
      data: { product_id: productId },

      beforeSend: function () {
        row.css('opacity', 0.5);
      },

      success: function (response) {
        if (response.success) {

          toastr.success(response.message);
          row.remove();
          $('#cart_count').text(response.cart_count);

          // Nếu giỏ trống → hiển thị nội dung giỏ hàng trống
          if (response.cart_count == 0) {
            $('.mini-cart-list').remove();
            $('.mini-cart-product-area')
              .html('<p class="text-center p-3">Giỏ hàng trống</p>');
            $('.mini-cart-sub-total h5 span').text('0 VNĐ');
          } else {
            $('.mini-cart-icon').trigger('click');
          }

        } else {
          toastr.error('Lỗi xóa sản phẩm!');
          row.css('opacity', 1);
        }
      },

      error: function () {
        toastr.error('Lỗi kết nối server!');
        row.css('opacity', 1);
      }
    });
  });

  //   CART PAGE (TRANG GIỎ HÀNG CHÍNH)
  // ---------------------------------------------------------------------

  if (window.location.pathname === '/cart') {

    // Xử lý nút + - số lượng
    $(document).on('click', '.qtybutton', function () {

      let $btn = $(this);
      let $input = $btn.siblings('input');
      let oldValue = parseInt($input.val());
      let maxStock = parseInt($input.data('max'));
      let productId = $input.data('id');

      let newValue = oldValue;

      if ($btn.hasClass('inc') && oldValue < maxStock) {
        newValue = oldValue + 1;
      } else if ($btn.hasClass('dec') && oldValue > 1) {
        newValue = oldValue - 1;
      }

      if (newValue !== oldValue) {
        updateCart(productId, newValue, $input);
      }
    });
  }

  // AJAX update số lượng trong giỏ hàng
  function updateCart(productId, quantity, $input) {

    $.ajaxSetup({
      headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
    });

    $.ajax({
      url: '/cart/update',
      type: 'POST',
      data: { product_id: productId, quantity: quantity },

      success: function (response) {

        // Update số lượng
        $input.val(response.quantity);

        // Update thành tiền dòng
        $input.closest('tr')
          .find('.cart-product-subtotal')
          .text(response.subtotal + 'đ');

        // Update tổng tiền hàng
        $('.cart-total').text(response.total + 'đ');

        // Update tổng thanh toán
      },

      error: function (xhr) {
        alert(xhr.responseJSON.error);
      }
    });
  }

  // Xóa sản phẩm khỏi giỏ hàng chính
  $('.remove-from-cart').on('click', function () {

    let productId = $(this).data('id');
    let row = $(this).closest('tr');

    $.ajaxSetup({
      headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
    });

    $.ajax({
      url: '/cart/remove-cart',
      type: 'POST',
      data: { product_id: productId },

      success: function (response) {

        row.remove();
        $('.cart-total').text(response.total + 'đ');

        // Nếu không còn sản phẩm → reload
        if ($('.cart-product-remove').length == 0) {
          location.reload();
        }
      },

      error: function (xhr) {
        alert(xhr.responseJSON.error);
      }
    });
  });

  // – CHECKOUT (THANH TOÁN)

  if (window.location.pathname === "/checkout") {
    $('#list_address').change(function () {
      var addressId = $(this).val();

      $.ajax({
        url: '/checkout/get-address',
        type: 'GET',
        data: { address_id: addressId },

        success: function (response) {
          if (response.success) {
            $('input[name="ltn__name"]').val(response.data.full_name);
            $('input[name="ltn__phone"]').val(response.data.phone);
            $('input[name="ltn__address"]').val(response.data.address);
            $('input[name="ltn__city"]').val(response.data.city);
            $('#checkout_address_id').val(response.data.id);

            if (!response.data.has_ghn_location) {
              setCheckoutReady(false, 'Địa chỉ này chưa có đủ tỉnh/quận/phường GHN. Vui lòng thêm địa chỉ giao hàng mới.');
              $('.shippingFee_Checkout').text('Chưa tính');
              return;
            }

            refreshShippingFee(addressId);
          }
        },

        error: function (xhr) {
          setCheckoutReady(false, 'Không lấy được địa chỉ giao hàng.');
          toastr.error(xhr.responseJSON?.message ?? "Lỗi không xác định");
        }
      });
    });

    var summaryEl = $("#checkout-summary");
    var shippingUrl = summaryEl.data("shipping-url");
    var currencyFormatter = new Intl.NumberFormat("vi-VN");
    var totalPriceNumber = parseFloat(summaryEl.data("total")) || 0;
    var shippingReady = true;
    var $shippingMessage = $('.checkout-shipping-message');
    var $orderButton = $('#order_button_cash');

    function setSummaryData(key, value) {
      summaryEl.data(key, value);
    }

    function setCheckoutReady(isReady, message) {
      shippingReady = isReady;
      $orderButton.prop('disabled', !isReady);
      $('#payment_paypal').prop('disabled', !isReady);

      if (!isReady && $('#payment_paypal').is(':checked')) {
        $('#payment_cod').prop('checked', true).trigger('change');
      }

      if (message) {
        $shippingMessage.removeClass('d-none').find('td').text(message);
      } else {
        $shippingMessage.addClass('d-none').find('td').text('');
      }
    }

    function updateSummaryValues(data) {
      $('.shippingFee_Checkout').text(currencyFormatter.format(data.shipping_fee) + ' đ');
      $('.totalPrice_Checkout').text(currencyFormatter.format(data.total) + ' đ');
      
      setSummaryData('subtotal', data.subtotal);
      setSummaryData('shipping-fee', data.shipping_fee);
      setSummaryData('total', data.total);
      
      totalPriceNumber = data.total;
      setCheckoutReady(true, '');
    }

    function refreshShippingFee(addressId) {
      if (!shippingUrl || !addressId) {
        setCheckoutReady(false, 'Vui lòng chọn địa chỉ giao hàng.');
        return;
      }

      setCheckoutReady(false, '');
      $('.shippingFee_Checkout').text('Đang tính...');

      $.ajax({
        url: shippingUrl,
        type: "GET",
        data: {
          address_id: addressId,
        },
        success: function (response) {
          if (response.status) {
            updateSummaryValues(response.data);
          } else {
            setCheckoutReady(false, response.message || 'Không tính được phí vận chuyển.');
            toastr.error(response.message || 'Không tính được phí vận chuyển.');
          }
        },
        error: function (xhr) {
          var message = xhr.responseJSON?.message || "Không tìm thấy đơn vị vận chuyển";
          setCheckoutReady(false, message);
          toastr.error(message);
        }
      });
    }

    $('#checkout-order-form').on('submit', function (e) {
      if (!$('#checkout_address_id').val()) {
        e.preventDefault();
        toastr.error('Vui lòng chọn địa chỉ giao hàng.');
        return;
      }

      if (!shippingReady) {
        e.preventDefault();
        toastr.error('Vui lòng kiểm tra lại phí vận chuyển trước khi đặt hàng.');
      }
    });

    //  – PAYPAL PAYMENT

    function togglePayment() {
      if ($('#payment_paypal').is(":checked")) {
        $('#order_button_cash').hide();
        $('#paypal-button-container').show();
      } else {
        $('#order_button_cash').show();
        $('#paypal-button-container').hide();
      }
    }
    togglePayment();

    $('input[name="payment_method"]').on('change', togglePayment);

    function renderPaypalButtons(waitCount) {
      if (!document.querySelector('#paypal-button-container')) {
        return;
      }

      if (!window.paypal) {
        if (waitCount < 20) {
          setTimeout(function () {
            renderPaypalButtons(waitCount + 1);
          }, 300);
        } else {
          $('#payment_paypal').prop('disabled', true);
        }
        return;
      }

      paypal.Buttons({
        createOrder: function (data, actions) {
          if (!shippingReady) {
            toastr.error('Vui lòng kiểm tra lại phí vận chuyển trước khi thanh toán.');
            return Promise.reject();
          }

          return actions.order.create({
            purchase_units: [{
              amount: { value: (totalPriceNumber / 25000).toFixed(2) }
            }]
          })
        },

        onApprove: function (data, actions) {
          return actions.order.capture().then(function (details) {
            fetch("/checkout/paypal", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
              },
              body: JSON.stringify({
                orderID: data.orderID,
                payerID: data.payerID,
                transactionID: details.id,
                amount: details.purchase_units[0].amount.value,
                address_id: $('#list_address').val(),
              })
            })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  toastr.success('Thanh toán thành công');
                  window.location.href = "/account";
                } else {
                  alert('Có lỗi xảy ra, vui lòng thử lại');
                }
              })
          })
        }
      }).render('#paypal-button-container');
    }

    renderPaypalButtons(0);
  }

  // – REVIEW PRODUCT (ĐÁNH GIÁ SẢN PHẨM)

  if (window.location.pathname.startsWith("/product")) {

    let selectedRating = 0;

    // Hover sao → hiển thị sao sáng
    $(".rating-star").hover(function () {
      highlightStars($(this).data("value"));
    }, function () {
      highlightStars(selectedRating);
    });

    // Click sao → chọn số sao
    $(".rating-star").click(function (e) {
      e.preventDefault();
      selectedRating = $(this).data("value");
      $("#rating-value").val(selectedRating);
      highlightStars(selectedRating);
    });

    // Hàm đổi màu sao dựa vào rating
    function highlightStars(value) {
      $(".rating-star i").each(function () {
        let starValue = $(this).parent().data('value');
        if (starValue <= value) {
          $(this).removeClass("far").addClass("fas"); // Sao sáng
        } else {
          $(this).removeClass("fas").addClass("far"); // Sao mờ
        }
      });
    }

    // Gửi đánh giá sản phẩm bằng AJAX
    $("#review-form").submit(function (e) {
      e.preventDefault();

      let productId = $(this).data("product-id");
      let rating = $("#rating-value").val();
      let content = $("#review-content").val();

      if (!rating || rating === "0") {
        alert("Vui lòng chọn số sao");
        return;
      }
      $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
      });

      $.ajax({
        url: "/review",
        type: "POST",
        data: {
          product_id: productId,
          rating: rating,
          comment: content
        },

        success: function (response) {
          $("#review-content").val("");
          highlightStars(0);
          selectedRating = 0;

          toastr.success(response.message);

          // Load lại danh sách đánh giá
          loadReviews(productId);
        },

        error: function (xhr) {
          alert(xhr.responseJSON.error);
        }
      });
    });

    // AJAX load danh sách review mới
    function loadReviews(productId) {
      $.ajax({
        url: "/review/" + productId,
        type: "GET",
        success: function (response) {
          $(".ltn__comment-inner").html(response);
        }
      });
    }
  }

  // – WISHLIST (YÊU THÍCH)

  // Thêm sản phẩm vào wishlist
  $(document).on('click', '.add-to-wishlist', function (e) {
    e.preventDefault();

    let productId = $(this).data('id');

    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $.ajax({
      url: '/wishlist/add',
      type: 'POST',
      data: { product_id: productId },

      success: function (response) {
        if (response.status) {
          $("#liton_wishlist_modal-" + productId).modal('show');
        }
      },

      error: function () {
        alert("Lỗi khi thêm vào wishlist");
      }
    });
  });

  // Xóa sản phẩm khỏi wishlist
  $(document).on('click', '.wishlist-product-remove', function (e) {
    e.preventDefault();

    let productId = $(this).data('id');
    let row = $(this).closest("tr");

    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $.ajax({
      url: '/wishlist/remove',
      type: 'POST',
      data: { product_id: productId },

      success: function (response) {
        if (response.status) {
          row.remove();
          toastr.success("Đã xóa sản phẩm khỏi danh sách yêu thích");
        }
      },

      error: function () {
        alert("Lỗi khi xóa khỏi wishlist");
      }
    });
  });

  //  – CONTACT PAGE (LIÊN HỆ)

  $("#contact-form").on("submit", function (e) {
    let name = $('input[name="name"]').val().trim();
    let email = $('input[name="email"]').val().trim();
    let phone = $('input[name="phone"]').val().trim();
    let message = $('textarea[name="message"]').val().trim();

    let errorMessage = "";

    // Validate họ tên >= 3 ký tự
    if (name.length < 3) {
      errorMessage += "Họ và tên phải có ít nhất 3 ký tự.<br>";
    }

    // SĐT phải đúng 10 số
    if (!/^[0-9]{10}$/.test(phone)) {
      errorMessage += "Số điện thoại phải là 10 số.<br>";
    }

    // Validate email
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ.<br>";
    }

    // Nếu có lỗi thì chặn submit
    if (errorMessage !== "") {
      toastr.error(errorMessage, "Lỗi");
      e.preventDefault();
    }
  });
  //tim kiem bang giong noi
if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {

    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'vi-VN';
    recognition.interimResults = true;

    let isRecognizing = false;
    const mic = $("#voice-search");
    const input = $('input[name="keyword"]');

    // Click micro
    mic.on('click', function () {
        if (isRecognizing) {
            recognition.stop();
        } else {
            recognition.start();
        }
    });

    // Bắt đầu nghe
    recognition.onstart = function () {
        isRecognizing = true;
        mic.removeClass('fa-microphone').addClass('fa-microphone-slash');
        console.log('Speech recognition started');
    };

    // Có kết quả
    recognition.onresult = function (event) {
        let transcript = event.results[0][0].transcript.trim();

        // ❗ Xóa dấu . , ! ? ở cuối
        transcript = transcript.replace(/[.,!?]+$/, '');

        input.val(transcript);
    };

    // Lỗi
    recognition.onerror = function (event) {
        console.log('Speech recognition error:', event.error);
        toastr.error('Có lỗi xảy ra khi nhận diện giọng nói: ' + event.error);
    };

    // Kết thúc
    recognition.onend = function () {
        isRecognizing = false;
        mic.removeClass('fa-microphone-slash').addClass('fa-microphone');
        console.log('Speech recognition ended');
    };

} else {
    console.log("Trình duyệt không hỗ trợ");
    toastr.error("Trình duyệt của bạn không hỗ trợ giọng nói");
}
//giao hang nhanh


});
