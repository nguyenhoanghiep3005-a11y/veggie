$(document).ready(function () {

  // ĐĂNG KÝ: kiểm tra dữ liệu trên trình duyệt trước khi gửi form lên controller.
  $('#register-form').submit(function (e) {
    // Lấy dữ liệu người dùng nhập trong form đăng ký.
    let name = $('input[name="name"]').val();
    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let confirmPassword = $('input[name="confirmPassword"]').val();
    let checkbox1 = $('input[name="checkbox1"]').is(':checked');
    let checkbox2 = $('input[name="checkbox2"]').is(':checked');

    let errorMessage = "";
    // Tên phải có ít nhất 3 ký tự.
    if (name.length < 3) {
      errorMessage += "Họ và tên phải có ít nhất 3 ký tự. <br>";
    }
    // Email phải đúng định dạng.
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }
    // Mật khẩu phải có ít nhất 6 ký tự.
    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }
    // Mật khẩu xác nhận phải trùng với mật khẩu chính.
    if (password != confirmPassword) {
      errorMessage += "Mật khẩu nhập lại không khớp. <br>";
    }
    // Người dùng phải đồng ý các điều khoản bắt buộc.
    if (!checkbox1 || !checkbox2) {
      errorMessage += "Bạn phải đồng ý với các điều khoản trước khi tạo tài khoản. <br>";
    }
    // Có lỗi thì chặn submit và hiển thị toastr.
    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");
      e.preventDefault();
      return;
    }
  });
  // ĐĂNG NHẬP: kiểm tra email và mật khẩu trước khi gửi form.
  $('#login-form').submit(function (e) {
    toastr.clear(); // Xóa thông báo cũ

    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let errorMessage = "";
    // Email phải đúng định dạng.
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }
    // Mật khẩu phải có ít nhất 6 ký tự.
    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }
    // Có lỗi thì chặn submit và hiển thị toastr.
    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");
      e.preventDefault();
    }
  });
  // ĐẶT LẠI MẬT KHẨU: kiểm tra email và hai ô mật khẩu mới.
  $('#reset-password-form').submit(function (e) {
    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let confirmPassword = $('input[name="password_confirmation"]').val();
    let errorMessage = "";
    // Email phải đúng định dạng.
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }

    // Mật khẩu mới phải có ít nhất 6 ký tự.
    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }

    // Mật khẩu xác nhận phải trùng với mật khẩu mới.
    if (password != confirmPassword) {
      errorMessage += "Mật khẩu nhập lại không khớp. <br>";
    }

    // Có lỗi thì chặn submit và hiển thị toastr.
    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");
      e.preventDefault();
    }
  });

  // ACCOUNT

  // Cập nhật thông tin cá nhân trong tab tài khoản.
  $('#update-account').submit(function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    let urlUpdate = $(this).attr('action');
    let $button = $(this).find('.btn-wrapper button');

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url: urlUpdate,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,

      beforeSend: function () {
        $button.text('Đang cập nhật...').attr('disabled', true);
      },

      success: function (response) {
        if (response.success) {
          toastr.success(response.message);
        }
      },

      error: function (xhr) {
        let errors = xhr.responseJSON.errors;
        $.each(errors, function (key, value) {
          toastr.error(value[0]);
        });
      },

      complete: function () {
        $button.text('Cập nhật').attr('disabled', false);
      }
    });
  });

  // Load tỉnh/thành GHN để lưu địa chỉ có đủ dữ liệu tính phí ship.
  var baseUrl = $('meta[name="base-url"]').attr('content') || '';
  var provinceLoaded = false;

  function loadProvinces() {
    var $province = $('#province_id');

    if ($province.length == 0 || provinceLoaded) {
      return;
    }

    $province.html('<option value="">--Đang tải--</option>').prop('disabled', true).niceSelect('update');
    $('#district_id').html('<option value="">--Chọn quận/huyện--</option>').prop('disabled', true).niceSelect('update');
    $('#ward_id').html('<option value="">--Chọn phường/xã--</option>').prop('disabled', true).niceSelect('update');

    $.get(baseUrl + '/ghn/provinces', function (res) {
      var options = '<option value="">--Chọn tỉnh/thành--</option>';

      if (res.status && res.data) {
        $.each(res.data, function (i, province) {
          options += '<option value="' + province.ProvinceID + '">' + province.ProvinceName + '</option>';
        });

        provinceLoaded = true;
        $province.html(options).prop('disabled', false).niceSelect('update');
      } else {
        $province.html('<option value="">--Lỗi tải tỉnh/thành--</option>').prop('disabled', false).niceSelect('update');
        toastr.error('Không thể tải danh sách tỉnh/thành.');
      }
    }).fail(function () {
      $province.html('<option value="">--Lỗi kết nối--</option>').prop('disabled', false).niceSelect('update');
      toastr.error('Lỗi kết nối khi tải tỉnh/thành.');
    });
  }

  // Chọn tỉnh/thành xong thì load quận/huyện.
  $(document).on('change', '#province_id', function () {
    var provinceId = $(this).val();
    var provinceName = $(this).find(':selected').text();
    var $district = $('#district_id');
    var $ward = $('#ward_id');

    $('#province_name').val(provinceId ? provinceName : '');
    $('#district_name').val('');
    $('#ward_name').val('');

    $district.html('<option value="">--Chọn quận/huyện--</option>').prop('disabled', true).niceSelect('update');
    $ward.html('<option value="">--Chọn phường/xã--</option>').prop('disabled', true).niceSelect('update');

    if (!provinceId) {
      return;
    }

    $district.html('<option value="">--Đang tải--</option>').niceSelect('update');

    $.get(baseUrl + '/ghn/districts', { province_id: provinceId }, function (res) {
      var options = '<option value="">--Chọn quận/huyện--</option>';

      if (res.status && res.data) {
        $.each(res.data, function (i, district) {
          options += '<option value="' + district.DistrictID + '">' + district.DistrictName + '</option>';
        });

        $district.html(options).prop('disabled', false).niceSelect('update');
      } else {
        $district.html('<option value="">--Lỗi tải quận/huyện--</option>').prop('disabled', false).niceSelect('update');
        toastr.error('Không thể tải danh sách quận/huyện.');
      }
    }).fail(function () {
      $district.html('<option value="">--Lỗi kết nối--</option>').prop('disabled', false).niceSelect('update');
    });
  });

  // Chọn quận/huyện xong thì load phường/xã.
  $(document).on('change', '#district_id', function () {
    var districtId = $(this).val();
    var districtName = $(this).find(':selected').text();
    var $ward = $('#ward_id');

    $('#district_name').val(districtId ? districtName : '');
    $('#ward_name').val('');

    $ward.html('<option value="">--Chọn phường/xã--</option>').prop('disabled', true).niceSelect('update');

    if (!districtId) {
      return;
    }

    $ward.html('<option value="">--Đang tải--</option>').niceSelect('update');

    $.get(baseUrl + '/ghn/wards', { district_id: districtId }, function (res) {
      var options = '<option value="">--Chọn phường/xã--</option>';

      if (res.status && res.data) {
        $.each(res.data, function (i, ward) {
          options += '<option value="' + ward.WardCode + '">' + ward.WardName + '</option>';
        });

        $ward.html(options).prop('disabled', false).niceSelect('update');
      } else {
        $ward.html('<option value="">--Lỗi tải phường/xã--</option>').prop('disabled', false).niceSelect('update');
        toastr.error('Không thể tải danh sách phường/xã.');
      }
    }).fail(function () {
      $ward.html('<option value="">--Lỗi kết nối--</option>').prop('disabled', false).niceSelect('update');
    });
  });

  // Lưu tên phường/xã vào input ẩn để controller ghép địa chỉ.
  $(document).on('change', '#ward_id', function () {
    var wardName = $(this).find(':selected').text();
    $('#ward_name').val($(this).val() ? wardName : '');
  });

  if ($('#addAddressForm').length > 0) {
    loadProvinces();
  }

  $(document).on('show.bs.modal', '#addAddressModal', function () {
    loadProvinces();
  });

  // Đổi mật khẩu tài khoản.
  $('#change-password-form').submit(function (e) {
    e.preventDefault();

    let currentPassword = $('input[name="current_password"]').val().trim();
    let newPassword = $('input[name="new_password"]').val().trim();
    let confirmPassword = $('input[name="confirm_new_password"]').val().trim();
    let errorMessage = '';
    let $button = $(this).find('.btn-wrapper button');

    if (currentPassword.length < 6) {
      errorMessage += 'Mật khẩu hiện tại phải có ít nhất 6 ký tự. <br>';
    }

    if (newPassword.length < 6) {
      errorMessage += 'Mật khẩu mới phải có ít nhất 6 ký tự. <br>';
    }

    if (newPassword != confirmPassword) {
      errorMessage += 'Mật khẩu nhập lại không khớp. <br>';
    }

    if (errorMessage != '') {
      toastr.error(errorMessage, 'Lỗi');
      return;
    }

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),

      beforeSend: function () {
        $button.text('Đang cập nhật...').attr('disabled', true);
      },

      success: function (response) {
        if (response.success) {
          toastr.success(response.message);
          $('#change-password-form')[0].reset();
        } else {
          toastr.error(response.message);
        }
      },

      error: function (xhr) {
        let errors = xhr.responseJSON.errors;
        $.each(errors, function (key, value) {
          toastr.error(value[0]);
        });
      },

      complete: function () {
        $button.text('Đổi mật khẩu').attr('disabled', false);
      }
    });
  });

  // Kiểm tra form thêm địa chỉ trước khi gửi lên controller.
  $('#addAddressForm').submit(function (e) {
    let isValid = true;
    let phoneRegex = /^[0-9]{10,11}$/;

    $('.error-message').remove();

    if ($('#full_name').val().trim().length < 3) {
      isValid = false;
      $('#full_name').after('<p class="error-message text-danger">Họ và tên không được ít hơn 3 ký tự.</p>');
    }

    if ($('#phone').val().trim() == '' || !phoneRegex.test($('#phone').val().trim())) {
      isValid = false;
      $('#phone').after('<p class="error-message text-danger">Số điện thoại không hợp lệ.</p>');
    }

    if ($('#address').val().trim().length < 5) {
      isValid = false;
      $('#address').after('<p class="error-message text-danger">Vui lòng nhập địa chỉ cụ thể.</p>');
    }

    if ($('#province_id').val() == '') {
      isValid = false;
      $('#province_id').after('<p class="error-message text-danger">Vui lòng chọn tỉnh/thành.</p>');
    }

    if ($('#district_id').val() == '') {
      isValid = false;
      $('#district_id').after('<p class="error-message text-danger">Vui lòng chọn quận/huyện.</p>');
    }

    if ($('#ward_id').val() == '') {
      isValid = false;
      $('#ward_id').after('<p class="error-message text-danger">Vui lòng chọn phường/xã.</p>');
    }

    if (!isValid) {
      e.preventDefault();
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

    var category_id = $(".category-filter.active").data('id') || '';
    var minPrice = $(".slider-range").slider('values', 0);
    var maxPrice = $(".slider-range").slider('values', 1);
    var sort_by = $("#sort-by").val();

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
    var pageUrl = $(this).attr('href');
    currentPage = pageUrl.split('page=')[1];
    fetchProducts();
  });

  // CLICK chọn danh mục
  $(document).on("click", ".category-filter", function (e) {
    e.preventDefault();
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


  // AJAX thêm sản phẩm vào giỏ hàng.
  $(document).on('click', '.add-to-cart-btn', function (e) {
    e.preventDefault();

    let $button = $(this);
    let productId = parseInt($button.data('id'), 10);
    let quantity = 1;

    if ($button.data('loading')) {
      return;
    }

    if (!productId) {
      toastr.error('Không xác định được sản phẩm cần thêm vào giỏ hàng.');
      return;
    }

    let $quantityInput = $button.closest('.ltn__product-item, .ltn__product-details-menu-2').find('.cart-plus-minus-box');
    if ($quantityInput.length > 0) {
      quantity = parseInt($quantityInput.val(), 10) || 1;
    }

    $.ajax({
      url: baseUrl + '/cart/add',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        product_id: productId,
        quantity: quantity
      },

      beforeSend: function () {
        $button.data('loading', true).addClass('disabled').css('pointer-events', 'none');
      },

      success: function (response) {
        if (!response.success) {
          toastr.error(response.message || 'Không thể thêm sản phẩm vào giỏ hàng.');
          return;
        }

        toastr.success(response.message);
        $('#cart_count').text(response.cart_count);
        loadMiniCart(false);

        $('.modal.show').modal('hide');

        let $addModal = $('#add_to_cart_modal-' + productId);
        if ($addModal.length > 0) {
          $addModal.modal('show');
        }
      },

      error: function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
          toastr.error(xhr.responseJSON.message);
        } else {
          toastr.error('Lỗi thêm vào giỏ hàng.');
        }
      },

      complete: function () {
        $button.data('loading', false).removeClass('disabled').css('pointer-events', '');
      }
    });
  });

  // Khi mở quick view thì kích hoạt lại nút tăng giảm số lượng.
  $(document).on('shown.bs.modal', '.ltn__modal-area', function () {
    activateCartPlusMinus();
  });

  activateCartPlusMinus();

  // Load nội dung mini cart bằng AJAX.
  function loadMiniCart(openCart) {
    $.ajax({
      url: baseUrl + '/mini-cart',
      type: 'GET',

      success: function (response) {
        if (!response.status) {
          toastr.error('Không thể tải giỏ hàng.');
          return;
        }

        $('#ltn__utilize-cart-menu .ltn__utilize-menu-inner').html(response.html);
        $('#cart_count').text(response.cart_count);

        if (openCart) {
          $('body').addClass('ltn__utilize-open');
          $('#ltn__utilize-cart-menu').addClass('ltn__utilize-open');
          $('.ltn__utilize-overlay').fadeIn();
        }
      },

      error: function () {
        toastr.error('Không thể tải giỏ hàng.');
      }
    });
  }

  // Mở mini cart.
  $('.mini-cart-icon .ltn__utilize-toggle').off('click').on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    loadMiniCart(true);
  });

  // Đóng mini cart.
  $(document).on('click', '.ltn__utilize-close', function () {
    $('body').removeClass('ltn__utilize-open');
    $('#ltn__utilize-cart-menu').removeClass('ltn__utilize-open');
    $('.ltn__utilize-overlay').fadeOut();
  });

  // Tăng giảm số lượng trong mini cart.
  $(document).on('click', '.mini-cart-qty-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    let $button = $(this);
    let $item = $button.closest('.mini-cart-item');
    let $input = $item.find('.mini-cart-qty-input');
    let productId = parseInt($button.data('product-id'), 10);
    let oldQuantity = parseInt($input.val(), 10) || 1;
    let maxStock = parseInt($input.data('max'), 10) || 0;
    let quantity = oldQuantity;

    if ($button.data('action') == 'increase') {
      if (oldQuantity >= maxStock) {
        toastr.warning('Số lượng đã đạt tồn sản phẩm hiện có.');
        return;
      }

      quantity = oldQuantity + 1;
    } else {
      if (oldQuantity > 1) {
        quantity = oldQuantity - 1;
      }
    }

    if (quantity == oldQuantity) {
      return;
    }

    updateCart(productId, quantity, $input, true);
  });

  // Xóa sản phẩm trong mini cart.
  $(document).on('click', '.remove-from-cart-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    let productId = $(this).data('product-id');
    let $row = $(this).closest('.mini-cart-item');

    $.ajax({
      url: baseUrl + '/cart/remove',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        product_id: productId
      },

      beforeSend: function () {
        $row.css('opacity', 0.5);
      },

      success: function (response) {
        if (!response.success) {
          toastr.error('Lỗi xóa sản phẩm.');
          $row.css('opacity', 1);
          return;
        }

        toastr.success(response.message);
        $('#cart_count').text(response.cart_count);
        loadMiniCart(false);
      },

      error: function () {
        toastr.error('Lỗi kết nối server.');
        $row.css('opacity', 1);
      }
    });
  });

  // Tăng giảm số lượng ở trang giỏ hàng lớn.
  if (window.location.pathname === '/cart') {
    $(document).on('click', '.qtybutton', function () {
      let $button = $(this);
      let $input = $button.siblings('input');
      let oldQuantity = parseInt($input.val(), 10) || 1;
      let maxStock = parseInt($input.data('max'), 10) || 0;
      let productId = $input.data('id');
      let quantity = oldQuantity;

      if ($input.data('updating')) {
        return;
      }

      if ($button.hasClass('inc')) {
        if (oldQuantity >= maxStock) {
          toastr.warning('Số lượng đã đạt tồn sản phẩm hiện có.');
          return;
        }

        quantity = oldQuantity + 1;
      } else {
        if (oldQuantity > 1) {
          quantity = oldQuantity - 1;
        }
      }

      if (quantity != oldQuantity) {
        updateCart(productId, quantity, $input, false);
      }
    });
  }

  // Cập nhật số lượng sản phẩm trong giỏ hàng.
  function updateCart(productId, quantity, $input, refreshMiniCart) {
    let oldQuantity = $input.val();

    if ($input.data('updating')) {
      return;
    }

    $input.data('updating', true);

    $.ajax({
      url: baseUrl + '/cart/update',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        product_id: productId,
        quantity: quantity
      },

      success: function (response) {
        if (!response.success) {
          toastr.error(response.error || 'Không thể cập nhật số lượng.');
          return;
        }

        $input.val(response.quantity);
        $('#cart_count').text(response.cart_count);

        if (refreshMiniCart) {
          let $miniCartItem = $input.closest('.mini-cart-item');
          $miniCartItem.find('.mini-cart-item-total').text(response.subtotal + 'đ');
          $('.mini-cart-sub-total h5 span').text(response.total + 'đ');
          return;
        }

        $input.closest('tr').find('.cart-product-subtotal').text(response.subtotal + 'đ');
        $('.cart-total').text(response.total + 'đ');
      },

      error: function (xhr) {
        $input.val(oldQuantity);
        if (xhr.responseJSON && xhr.responseJSON.error) {
          toastr.error(xhr.responseJSON.error);
        } else {
          toastr.error('Không thể cập nhật số lượng.');
        }
      },

      complete: function () {
        $input.data('updating', false);
      }
    });
  }

  // Xóa sản phẩm ở trang giỏ hàng lớn.
  $(document).on('click', '.remove-from-cart', function (e) {
    e.preventDefault();

    let productId = $(this).data('id');
    let $row = $(this).closest('tr');

    $.ajax({
      url: baseUrl + '/cart/remove-cart',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        product_id: productId
      },

      success: function (response) {
        $row.remove();
        $('#cart_count').text(response.cart_count);
        $('.cart-total').text(response.total + 'đ');

        if ($('.cart-product-remove').length == 0) {
          location.reload();
        }
      },

      error: function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.error) {
          toastr.error(xhr.responseJSON.error);
        } else {
          toastr.error('Không thể xóa sản phẩm khỏi giỏ hàng.');
        }
      }
    });
  });


  // – CHECKOUT (THANH TOÁN)

  if (window.location.pathname === "/checkout") {
    var baseUrl = $('meta[name="base-url"]').attr('content') || '';
    var summaryEl = $('#checkout-summary');
    var shippingUrl = summaryEl.data('shipping-url');
    var shippingUrlGuest = summaryEl.data('shipping-url-guest');
    var currencyFormatter = new Intl.NumberFormat('vi-VN');
    var totalPriceNumber = parseFloat(summaryEl.data('total')) || 0;
    var shippingReady = false;
    var provinceLoaded = false;
    var $shippingMessage = $('.checkout-shipping-message');
    var $orderButton = $('#order_button_cash');

    function formatMoney(value) {
      return currencyFormatter.format(value || 0) + ' đ';
    }

    function updateSummaryValues(data) {
      $('.shippingFee_Checkout').text(formatMoney(data.shipping_fee));
      $('.discount_Checkout').text(formatMoney(data.discount || 0));
      $('.totalPrice_Checkout').text(formatMoney(data.total));

      summaryEl.data('subtotal', data.subtotal);
      summaryEl.data('shipping-fee', data.shipping_fee);
      summaryEl.data('discount', data.discount || 0);
      summaryEl.data('total', data.total);
      totalPriceNumber = data.total;

      setCheckoutReady(true, '');
    }

    function showTotalWithoutShipping() {
      var subtotal = parseFloat(summaryEl.data('subtotal')) || 0;
      var discount = parseFloat(summaryEl.data('discount')) || 0;
      var totalWithoutShipping = Math.max(0, subtotal - discount);

      $('.shippingFee_Checkout').text('Chưa tính');
      $('.totalPrice_Checkout').text(formatMoney(totalWithoutShipping));
      totalPriceNumber = totalWithoutShipping;
    }

    function setCheckoutReady(isReady, message) {
      shippingReady = isReady;

      if ($orderButton.length) {
        $orderButton.prop('disabled', !isReady);
      }

      $('#payment_paypal').prop('disabled', !isReady);

      if (message) {
        $shippingMessage.removeClass('d-none').find('td').text(message);
      } else {
        $shippingMessage.addClass('d-none').find('td').text('');
      }
    }

    function isPaypalOnlyCheckout() {
      return $('#checkout_delivery_type').val() == 'new' || !$('#payment_cod').length;
    }

    function syncPaymentByDeliveryType(type) {
      if (type == 'new') {
        $('.checkout-cod-card').addClass('d-none');
        $('.checkout-paypal-only-alert').removeClass('d-none');
        $('#payment_paypal').prop('checked', true);
      } else {
        $('.checkout-cod-card').removeClass('d-none');
        $('.checkout-paypal-only-alert').addClass('d-none');

        if ($('#payment_cod').length && !$('#payment_paypal').is(':checked')) {
          $('#payment_cod').prop('checked', true);
        }
      }

      togglePayment();
    }

    function updateNiceSelect($select) {
      if (typeof $select.niceSelect === 'function') {
        $select.niceSelect('update');
      }
    }

    function resetGuestDistrictAndWard() {
      $('#new_district_id').html('<option value="">Quận/huyện *</option>').prop('disabled', true);
      $('#new_ward_id').html('<option value="">Phường/xã *</option>').prop('disabled', true);
      $('#new_district_name').val('');
      $('#new_ward_name').val('');
      updateNiceSelect($('#new_district_id'));
      updateNiceSelect($('#new_ward_id'));
    }

    function loadGuestProvinces() {
      var $province = $('#new_province_id');

      if (provinceLoaded || $province.length == 0) {
        return;
      }

      $province.html('<option value="">--Đang tải--</option>').prop('disabled', true);
      updateNiceSelect($province);

      $.get(baseUrl + '/ghn/provinces', function (response) {
        var options = '<option value="">Tỉnh/thành *</option>';

        if (response.status && response.data) {
          $.each(response.data, function (index, province) {
            options += '<option value="' + province.ProvinceID + '">' + province.ProvinceName + '</option>';
          });

          provinceLoaded = true;
          $province.html(options).prop('disabled', false);
        } else {
          $province.html('<option value="">--Lỗi tải tỉnh/thành--</option>').prop('disabled', false);
          toastr.error(response.message || 'Không thể tải tỉnh/thành.');
        }

        updateNiceSelect($province);
      }).fail(function () {
        $province.html('<option value="">--Lỗi kết nối--</option>').prop('disabled', false);
        updateNiceSelect($province);
        toastr.error('Không thể tải tỉnh/thành.');
      });
    }

    function loadGuestDistricts(provinceId) {
      var $district = $('#new_district_id');

      resetGuestDistrictAndWard();

      if (!provinceId) {
        return;
      }

      $district.html('<option value="">--Đang tải--</option>').prop('disabled', true);
      updateNiceSelect($district);

      $.get(baseUrl + '/ghn/districts', { province_id: provinceId }, function (response) {
        var options = '<option value="">Quận/huyện *</option>';

        if (response.status && response.data) {
          $.each(response.data, function (index, district) {
            options += '<option value="' + district.DistrictID + '">' + district.DistrictName + '</option>';
          });

          $district.html(options).prop('disabled', false);
        } else {
          $district.html('<option value="">--Lỗi tải quận/huyện--</option>').prop('disabled', false);
          toastr.error(response.message || 'Không thể tải quận/huyện.');
        }

        updateNiceSelect($district);
      }).fail(function () {
        $district.html('<option value="">--Lỗi kết nối--</option>').prop('disabled', false);
        updateNiceSelect($district);
      });
    }

    function loadGuestWards(districtId) {
      var $ward = $('#new_ward_id');

      $('#new_ward_name').val('');
      $ward.html('<option value="">Phường/xã *</option>').prop('disabled', true);
      updateNiceSelect($ward);

      if (!districtId) {
        return;
      }

      $ward.html('<option value="">--Đang tải--</option>').prop('disabled', true);
      updateNiceSelect($ward);

      $.get(baseUrl + '/ghn/wards', { district_id: districtId }, function (response) {
        var options = '<option value="">Phường/xã *</option>';

        if (response.status && response.data) {
          $.each(response.data, function (index, ward) {
            options += '<option value="' + ward.WardCode + '">' + ward.WardName + '</option>';
          });

          $ward.html(options).prop('disabled', false);
        } else {
          $ward.html('<option value="">--Lỗi tải phường/xã--</option>').prop('disabled', false);
          toastr.error(response.message || 'Không thể tải phường/xã.');
        }

        updateNiceSelect($ward);
      }).fail(function () {
        $ward.html('<option value="">--Lỗi kết nối--</option>').prop('disabled', false);
        updateNiceSelect($ward);
      });
    }

    function calculateShippingForAccount(addressId) {
      if (!shippingUrl || !addressId) {
        setCheckoutReady(false, 'Vui lòng chọn địa chỉ.');
        showTotalWithoutShipping();
        return;
      }

      setCheckoutReady(false, '');
      $('.shippingFee_Checkout').text('Đang tính...');

      $.ajax({
        url: shippingUrl,
        type: 'GET',
        data: { address_id: addressId },
        success: function (response) {
          if (response.status) {
            updateSummaryValues(response.data);
          } else {
            setCheckoutReady(false, response.message || 'Không tính được phí vận chuyển.');
            toastr.error(response.message || 'Không tính được phí vận chuyển.');
          }
        },
        error: function (xhr) {
          var message = xhr.responseJSON && xhr.responseJSON.message
            ? xhr.responseJSON.message
            : 'Không tìm thấy đơn vị vận chuyển.';
          setCheckoutReady(false, message);
          toastr.error(message);
        }
      });
    }

    function calculateShippingForGuest() {
      var provinceId = $('#new_province_id').val();
      var districtId = $('#new_district_id').val();
      var wardId = $('#new_ward_id').val();

      if (!provinceId || !districtId || !wardId) {
        setCheckoutReady(false, 'Vui lòng chọn đầy đủ tỉnh/quận/phường để tính phí vận chuyển.');
        showTotalWithoutShipping();
        return;
      }

      setCheckoutReady(false, '');
      $('.shippingFee_Checkout').text('Đang tính...');

      $.ajax({
        url: shippingUrlGuest,
        type: 'GET',
        data: {
          province_id: provinceId,
          district_id: districtId,
          ward_id: wardId
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
          var message = xhr.responseJSON && xhr.responseJSON.message
            ? xhr.responseJSON.message
            : 'Lỗi kết nối khi tính phí vận chuyển.';
          setCheckoutReady(false, message);
          toastr.error(message);
        }
      });
    }

    function showAccountAddress(address) {
      $('#show_name').val(address.full_name);
      $('#show_phone').val(address.phone);
      $('#show_address').val(address.address);
      $('#show_city').val(address.city);
      $('#show_name_text').text(address.full_name);
      $('#show_phone_text').text(address.phone);
      $('#show_address_text').text(address.address + ', ' + address.city);
      $('#checkout_address_id').val(address.id);
    }

    function loadAccountAddress(addressId) {
      if (!addressId) {
        setCheckoutReady(false, 'Vui lòng chọn địa chỉ giao hàng.');
        return;
      }

      $.ajax({
        url: baseUrl + '/checkout/get-address',
        type: 'GET',
        data: { address_id: addressId },
        success: function (response) {
          if (!response.success) {
            setCheckoutReady(false, 'Không lấy được địa chỉ giao hàng.');
            return;
          }

          showAccountAddress(response.data);

          if (!response.data.has_ghn_location) {
            setCheckoutReady(false, 'Địa chỉ này chưa có đủ tỉnh/quận/phường GHN.');
            showTotalWithoutShipping();
            return;
          }

          calculateShippingForAccount(addressId);
        },
        error: function () {
          setCheckoutReady(false, 'Không lấy được địa chỉ giao hàng.');
        }
      });
    }

    window.selectDeliveryType = function (type) {
      $('#checkout_delivery_type').val(type);
      $('.delivery-option').removeClass('active');
      $('#option-' + type).addClass('active');
      $('input[name="delivery_type_ui"][value="' + type + '"]').prop('checked', true);

      if (type == 'account') {
        $('#section-account').removeClass('d-none');
        $('#section-new').addClass('d-none');
        loadAccountAddress($('#list_address').val());
      } else {
        $('#section-account').addClass('d-none');
        $('#section-new').removeClass('d-none');
        $('#checkout_address_id').val('');
        loadGuestProvinces();
        calculateShippingForGuest();
      }

      syncPaymentByDeliveryType(type);
    };

    $('#list_address').change(function () {
      loadAccountAddress($(this).val());
    });

    $(document).on('change', '#new_province_id', function () {
      var provinceId = $(this).val();
      var provinceName = $(this).find(':selected').text();

      $('#new_province_name').val(provinceId ? provinceName : '');
      loadGuestDistricts(provinceId);
      calculateShippingForGuest();
    });

    $(document).on('change', '#new_district_id', function () {
      var districtId = $(this).val();
      var districtName = $(this).find(':selected').text();

      $('#new_district_name').val(districtId ? districtName : '');
      loadGuestWards(districtId);
      calculateShippingForGuest();
    });

    $(document).on('change', '#new_ward_id', function () {
      var wardId = $(this).val();
      var wardName = $(this).find(':selected').text();

      $('#new_ward_name').val(wardId ? wardName : '');
      calculateShippingForGuest();
    });

    syncPaymentByDeliveryType($('#checkout_delivery_type').val());

    if ($('#checkout_delivery_type').val() == 'account') {
      loadAccountAddress($('#checkout_address_id').val() || $('#list_address').val());
    } else {
      loadGuestProvinces();
      calculateShippingForGuest();
    }

    $(document).on('click', '.checkout-voucher-item', function () {
      if ($(this).hasClass('is-disabled') || $(this).prop('disabled')) {
        return;
      }

      var code = $(this).data('code');
      $('.checkout-voucher-item').removeClass('active');
      $('.checkout-voucher-item:not(.is-disabled) .checkout-voucher-action').text('Áp dụng');
      $(this).addClass('active');
      $(this).find('.checkout-voucher-action').text('Đã chọn');
      $('#coupon_code').val(code);
    });

    $(document).on('click', '#confirm-selected-coupon', function () {
      $('#apply-coupon-btn').trigger('click');
    });

    $(document).on('click', '#apply-coupon-btn', function () {
      var code = $('#coupon_code').val().trim();
      if (!code) {
        $('#coupon-message').removeClass('text-success').addClass('text-danger').text('Vui lòng nhập mã giảm giá.');
        return;
      }

      $.ajax({
        url: '/checkout/coupon',
        type: 'POST',
        data: { code: code, address_id: $('#checkout_address_id').val() },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        beforeSend: function () { $('#apply-coupon-btn').prop('disabled', true).text('Đang kiểm tra...'); },
        success: function (response) {
          $('#coupon-message').removeClass('text-danger').addClass('text-success').text(response.message);
          $('.checkout-selected-coupon').text('Đã chọn: ' + response.coupon);
          $('.discount_Checkout').text(response.formatted_discount);
          $('.totalPrice_Checkout').text(response.formatted_total);
          setSummaryData('discount', response.discount);
          setSummaryData('total', response.total);
          totalPriceNumber = response.total;
          var modalElement = document.getElementById('checkoutVoucherModal');
          if (modalElement && window.bootstrap) {
            var modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modalInstance.hide();
          }
        },
        error: function (xhr) {
          var message = 'Không thể áp dụng mã giảm giá.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          }
          $('#coupon-message').removeClass('text-success').addClass('text-danger').text(message);
        },
        complete: function () { $('#apply-coupon-btn').prop('disabled', false).text('Áp dụng'); }
      });
    });

    // Validate và submit
    $('#checkout-order-form').on('submit', function (e) {
      var deliveryType = $('#checkout_delivery_type').val();
      if (deliveryType === 'new') {
        var name    = $('#guest_name').val().trim();
        var phone   = $('#guest_phone').val().trim();
        var addr    = $('#guest_address').val().trim();
        var prov    = $('#new_province_id').val();
        var dist    = $('#new_district_id').val();
        var ward    = $('#new_ward_id').val();
        var email   = $('#guest_email').length && $('#guest_email').val() ? $('#guest_email').val().trim() : '';
        var emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var phoneRx = /^[0-9]{10,11}$/;
        var errors  = [];
        if (name.length < 2)                 errors.push('Vui lòng nhập họ và tên người nhận (ít nhất 2 ký tự).');
        if (!phoneRx.test(phone))             errors.push('Số điện thoại không hợp lệ (10-11 số).');
        if (addr.length < 5)                  errors.push('Vui lòng nhập địa chỉ cụ thể (ít nhất 5 ký tự).');
        if (!prov)                            errors.push('Vui lòng chọn tỉnh/thành.');
        if (!dist)                            errors.push('Vui lòng chọn quận/huyện.');
        if (!ward)                            errors.push('Vui lòng chọn phường/xã.');
        if (email && !emailRx.test(email))    errors.push('Email không hợp lệ.');
        if (errors.length > 0) {
          e.preventDefault();
          errors.forEach(function (msg) { toastr.error(msg); });
          return;
        }
        $('#checkout_address_id').val('');
        e.preventDefault();
        toastr.error('Địa chỉ khác hoặc đặt cho người thân chỉ được thanh toán bằng PayPal.');
        return;
      } else {
        if (!$('#checkout_address_id').val()) {
          e.preventDefault(); toastr.error('Vui lòng chọn địa chỉ giao hàng.'); return;
        }
      }
      if (!shippingReady) { e.preventDefault(); toastr.error('Vui lòng kiểm tra lại phí vận chuyển trước khi đặt hàng.'); return; }

      if ($orderButton.length && $('#payment_cod').is(':checked')) {
        $orderButton.prop('disabled', true).text('Đang đặt hàng...');
      }
    });

    // PayPal toggle
    function togglePayment() {
      if (isPaypalOnlyCheckout()) {
        $('#payment_paypal').prop('checked', true);
        if ($orderButton.length) $orderButton.hide();
        $('#paypal-button-container').show();
        return;
      }

      if ($('#payment_paypal').is(":checked")) {
        if ($orderButton.length) $orderButton.hide();
        $('#paypal-button-container').show();
      } else {
        if ($orderButton.length) $orderButton.show();
        $('#paypal-button-container').hide();
      }
    }
    syncPaymentByDeliveryType($('#checkout_delivery_type').val());
    $('input[name="payment_method"]').on('change', togglePayment);

    function renderPaypalButtons(waitCount) {
      if (!document.querySelector('#paypal-button-container')) return;
      if (!window.paypal) {
        if (waitCount < 20) { setTimeout(function () { renderPaypalButtons(waitCount + 1); }, 300); }
        else { $('#payment_paypal').prop('disabled', true); }
        return;
      }
      paypal.Buttons({
        createOrder: function (data, actions) {
          if (!shippingReady) { toastr.error('Vui lòng kiểm tra lại phí vận chuyển.'); return Promise.reject(); }
          return actions.order.create({ purchase_units: [{ amount: { value: (totalPriceNumber / 25000).toFixed(2) } }] });
        },
        onApprove: function (data, actions) {
          return actions.order.capture().then(function (details) {
            var deliveryType = $('#checkout_delivery_type').val();
            var payload = {
              orderID: data.orderID, payerID: data.payerID,
              transactionID: details.id, amount: details.purchase_units[0].amount.value,
              delivery_type: deliveryType, address_id: $('#checkout_address_id').val() || null,
            };
            if (deliveryType === 'new') {
              payload.guest_name          = $('#guest_name').val();
              payload.guest_phone         = $('#guest_phone').val();
              payload.guest_email         = $('#guest_email').val();
              payload.guest_address       = $('#guest_address').val();
              payload.guest_province_id   = $('#new_province_id').val();
              payload.guest_district_id   = $('#new_district_id').val();
              payload.guest_ward_id       = $('#new_ward_id').val();
              payload.guest_province_name = $('#new_province_name').val();
              payload.guest_district_name = $('#new_district_name').val();
              payload.guest_ward_name     = $('#new_ward_name').val();
            }
            fetch("/checkout/paypal", {
              method: "POST",
              headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
              body: JSON.stringify(payload)
            }).then(function(res) { return res.json(); }).then(function(res) {
              if (res.success) {
                toastr.success('Thanh toán thành công!');
                // Nếu đã đăng nhập chuyển về trang quản lý tài khoản, ngược lại chuyển về trang chủ
                if ($('#checkout_delivery_type').val() === 'account') {
                  window.location.href = "/account";
                } else {
                  window.location.href = "/";
                }
              }
              else { alert(res.message || 'Có lỗi xảy ra, vui lòng thử lại.'); }
            });
          });
        }
      }).render('#paypal-button-container');
    }

    renderPaypalButtons(0);

    if ($('#checkout_delivery_type').val() === 'new') { loadNewProvinces(); }
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
  // WISHLIST
  function showClientMessage(type, message) {
    if (typeof toastr !== 'undefined') {
      toastr[type](message);
      return;
    }

    alert(message);
  }

  function wishlistErrorMessage(xhr, defaultMessage) {
    if (xhr.status === 401) {
      return 'Vui lòng đăng nhập để sử dụng danh sách yêu thích.';
    }

    if (xhr.responseJSON && xhr.responseJSON.message) {
      return xhr.responseJSON.message;
    }

    if (xhr.responseJSON && xhr.responseJSON.errors) {
      var firstKey = Object.keys(xhr.responseJSON.errors)[0];
      return xhr.responseJSON.errors[firstKey][0] || defaultMessage;
    }

    return defaultMessage;
  }

  // Thêm sản phẩm vào danh sách yêu thích
  $(document).on('click', '.add-to-wishlist', function (e) {
    e.preventDefault();

    var productId = $(this).data('id');

    if (!productId) {
      showClientMessage('error', 'Không tìm thấy sản phẩm cần thêm vào yêu thích.');
      return;
    }

    $.ajax({
      url: baseUrl + '/wishlist/add',
      type: 'POST',
      dataType: 'json',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { product_id: productId },

      success: function (response) {
        if (!response.status) {
          showClientMessage('error', response.message || 'Không thể thêm sản phẩm vào yêu thích.');
          return;
        }

        var $modal = $('#liton_wishlist_modal-' + productId);
        if ($modal.length) {
          $modal.modal('show');
          return;
        }

        showClientMessage('success', response.message || 'Đã thêm sản phẩm vào danh sách yêu thích.');
      },

      error: function (xhr) {
        showClientMessage('error', wishlistErrorMessage(xhr, 'Lỗi khi thêm sản phẩm vào yêu thích.'));
      }
    });
  });

  // Xóa sản phẩm khỏi danh sách yêu thích
  $(document).on('click', '.wishlist-product-remove', function (e) {
    e.preventDefault();

    var productId = $(this).data('id');
    var $row = $(this).closest('tr');

    $.ajax({
      url: baseUrl + '/wishlist/remove',
      type: 'POST',
      dataType: 'json',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { product_id: productId },

      success: function (response) {
        if (!response.status) {
          showClientMessage('error', response.message || 'Không thể xóa sản phẩm khỏi yêu thích.');
          return;
        }

        $row.remove();
        showClientMessage('success', response.message || 'Đã xóa sản phẩm khỏi danh sách yêu thích.');

        if ($('.wishlist-row').length === 0) {
          $('.shoping-cart-table tbody').html('<tr class="wishlist-empty-row"><td colspan="6" class="text-center">Danh sách yêu thích của bạn đang trống.</td></tr>');
        }
      },

      error: function (xhr) {
        showClientMessage('error', wishlistErrorMessage(xhr, 'Lỗi khi xóa sản phẩm khỏi yêu thích.'));
      }
    });
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
        toastr.error('Có lỗi xảy ra khi nhận diện giọng nói: ' + event.error);
    };

    // Kết thúc
    recognition.onend = function () {
        isRecognizing = false;
        mic.removeClass('fa-microphone-slash').addClass('fa-microphone');
    };

} else {
    toastr.error("Trình duyệt của bạn không hỗ trợ giọng nói");
}

  // Chuyển đổi khối lượng sản phẩm qua AJAX không load lại trang
  $(document).on('click', '.product-variant-option', function (e) {
    e.preventDefault();
    var $this = $(this);
    var variantUrl = $this.data('variant-url'); // Link lấy thông tin biến thể
    var detailUrl = $this.attr('href'); // Link chi tiết để cập nhật thanh địa chỉ

    if (!variantUrl) return;

    // Báo trạng thái đang tải
    $('.product-variant-option').removeClass('loading');
    $this.addClass('loading');

    $.ajax({
      url: variantUrl,
      type: 'GET',
      dataType: 'json',
      success: function (res) {
        $this.removeClass('loading');

        // 1. Cập nhật active class ở danh sách lựa chọn
        $('.product-variant-option').removeClass('active');
        $this.addClass('active');

        // 2. Cập nhật URL trình duyệt (không reload)
        window.history.pushState(null, '', detailUrl);

        // 3. Cập nhật thông tin cơ bản
        $('.product-detail-name').text(res.display_name);
        $('.product-detail-price').html(res.price + '<small class="product-price-symbol">đ</small>');

        if (res.is_on_sale) {
          $('.product-detail-old-price').html(res.old_price + '<small class="product-price-symbol">đ</small>').show();
        } else {
          $('.product-detail-old-price').hide();
        }

        $('.product-detail-rating-number').text(res.avg_rating);
        $('.product-detail-review-count').html(res.total_reviews + ' Đánh giá');
        $('.product-detail-rating-line span').last().html('Đã bán ' + res.sold_quantity);

        // Cập nhật thông tin chi tiết Fact
        $('.product-description-text').text(res.description_text);
        $('.product-origin-text').text(res.origin_text);
        $('.product-unit-text').text(res.variant_label);
        $('.product-category-text').text(res.category_name);
        $('.product-storage-text').text(res.storage_text);
        $('.product-brand-text').text(res.brand_text);
        $('.product-manufacture-text').text(res.manufacture_text);

        // 4. Cập nhật số lượng tối đa cho giỏ hàng
        var $quantityInput = $('.cart-plus-minus-box');
        if (res.stock > 0) {
          $quantityInput.val(1);
        } else {
          $quantityInput.val(0);
        }
        $quantityInput.attr('data-max', res.stock);

        // 5. Cập nhật nút Thêm vào giỏ hàng
        var $cartAction = $('.product-detail-cart-action');
        if (res.stock > 0) {
          $cartAction.html(`
            <a href="javascript:void(0)" class="theme-btn-1 btn btn-effect-1 add-to-cart-btn" data-id="${res.id}">
              <i class="fas fa-shopping-cart"></i>
              <span>Thêm vào giỏ hàng</span>
            </a>
          `);
        } else {
          $cartAction.html(`
            <span class="theme-btn-1 btn btn-effect-1 product-action-disabled">
              Hết hàng
            </span>
          `);
        }

        // 6. Cập nhật nút Yêu thích
        var $wishlistBtn = $('.product-detail-wishlist');
        $wishlistBtn.addClass('add-to-wishlist');
        $wishlistBtn.attr('data-id', res.id);
        $wishlistBtn.removeAttr('data-bs-toggle data-bs-target');
        // 7. Cập nhật Gallery ảnh (Hủy slick cũ, chèn HTML mới, reinit slick)
        if (res.images && res.images.length > 0) {
          // Tắt slick cũ trước khi thay ảnh
          var $largeImg = $('.ltn__shop-details-large-img');
          var $smallImg = $('.ltn__shop-details-small-img');

          if ($largeImg.hasClass('slick-initialized')) {
            $largeImg.slick('unslick');
          }
          if ($smallImg.hasClass('slick-initialized')) {
            $smallImg.slick('unslick');
          }

          // Tạo HTML ảnh lớn
          var largeHtml = '';
          $.each(res.images, function (index, imgUrl) {
            largeHtml += '<div class="single-large-img">';
            largeHtml += '<a href="' + imgUrl + '" data-rel="lightcase:myCollection">';
            largeHtml += '<img src="' + imgUrl + '" alt="' + res.name + '" class="product-detail-image">';
            largeHtml += '</a>';
            largeHtml += '</div>';
          });
          $largeImg.html(largeHtml);

          // Tạo HTML ảnh nhỏ
          var smallHtml = '';
          $.each(res.images, function (index, imgUrl) {
            smallHtml += '<div class="single-small-img">';
            smallHtml += '<img src="' + imgUrl + '" alt="' + res.name + '" class="product-detail-image">';
            smallHtml += '</div>';
          });
          $smallImg.html(smallHtml);

          // Khởi tạo lại slick
          $largeImg.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.ltn__shop-details-small-img'
          });
          $smallImg.slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.ltn__shop-details-large-img',
            dots: false,
            arrows: true,
            focusOnSelect: true,
            prevArrow: '<a class="slick-prev"><i class="fas fa-arrow-left" alt="Arrow Icon"></i></a>',
            nextArrow: '<a class="slick-next"><i class="fas fa-arrow-right" alt="Arrow Icon"></i></a>',
            responsive: [
              {
                breakpoint: 992,
                settings: {
                  slidesToShow: 4,
                  slidesToScroll: 1
                }
              },
              {
                breakpoint: 768,
                settings: {
                  slidesToShow: 3,
                  slidesToScroll: 1
                }
              },
              {
                breakpoint: 580,
                settings: {
                  slidesToShow: 3,
                  slidesToScroll: 1
                }
              }
            ]
          });

          // Khởi tạo lại xem ảnh
          $('a[data-rel^=lightcase]').lightcase({
            transition: 'elastic',
            swipe: true,
            maxWidth: 1170,
            maxHeight: 600,
          });
        }
      },
      error: function () {
        $this.removeClass('loading');
        toastr.error('Không thể tải thông tin biến thể.');
      }
    });
  });

});

document.addEventListener('DOMContentLoaded', function () {
  var returnButton = document.getElementById('show-return-request-form');
  var returnPanel = document.getElementById('return-request-form');

  if (!returnButton || !returnPanel) {
    return;
  }

  if (returnPanel.classList.contains('is-open')) {
    returnButton.style.display = 'none';
  }

  returnButton.addEventListener('click', function () {
    returnPanel.classList.add('is-open');
    returnButton.style.display = 'none';
  });
});
