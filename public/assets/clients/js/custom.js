// custom.js

$(document).ready(function () {
  //page login , register

  //validate register form
  $('#register-form').submit(function (e) {
    let name = $('input[name="name"]').val();
    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let confirmPassword = $('input[name="confirmPassword"]').val();
    let checkbox1 = $('input[name="checkbox1"]').is(':checked');
    let checkbox2 = $('input[name="checkbox2"]').is(':checked');

    let errorMessage = "";

    if (name.length < 3) {
      errorMessage += "Họ và tên phải có ít nhất 3 ký tự. <br>";
    }

    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }

    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }

    if (password != confirmPassword) {
      errorMessage += "Mật khẩu nhập lại không khớp. <br>";
    }

    if (!checkbox1 || !checkbox2) {
      errorMessage += " Bạn phải đồng ý với các điều khoản trước khi tạo tài khoản. <br>";
    }

    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");

      e.preventDefault();
      return;
    }
  });

  $('#login-form').submit(function (e) {
    toastr.clear();
    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let errorMessage = "";

    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }

    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }

    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");

      e.preventDefault();
    }
  });

  $('#reset-pasword-form').submit(function (e) {
    let email = $('input[name="email"]').val();
    let password = $('input[name="password"]').val();
    let confirmPassword = $('input[name="password_confirmation"]').val();

    let errorMessage = "";

    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
      errorMessage += "Email không hợp lệ. <br>";
    }

    if (password.length < 6) {
      errorMessage += "Mật khẩu phải có ít nhất 6 ký tự. <br>";
    }
    if (password != confirmPassword) {
      errorMessage += "Mật khẩu nhập lại không khớp. <br>";
    }
    if (errorMessage != "") {
      toastr.error(errorMessage, "Lỗi");

      e.preventDefault();
    }
  });

  //account
  $('.profile-pic').click(function () {
    $("#avatar").click();
  })

  $("#avatar").change(function () {
    let input = this;
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('#preview-image').attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  })

  $("#update-account").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);
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
      processData: false,
      contentType: false,
      beforeSend: function () {
        $(".btn-wapper button").text("Đang cập nhật...").attr("disable", true);
      },
      success: function (response) {
        if (response.success) {
          toastr.success(response.message);
          if (response.avatar) {
            $('#preview-image').attr('src', response.avatar);
          } else {
            toastr.error(response.message);
          }
        }
      },
      error: function (xhr) {
        let errors = xhr.responseJSON.errors;
        $.each(errors, function (key, value) {
          toastr.error(value[0]);
        });
      },
      complete: function () {
        $(".btn-wapper button")
          .text("Cập nhật")
          .attr("disabled", false);
      },
    })
  })

  //doi matkhau
  $('#change-password-form').submit(function (e) {
    e.preventDefault();
    let current_password = $('input[name="current_password"]').val().trim();
    let new_password = $('input[name="new_password"]').val().trim();
    let confirm_new_password = $('input[name="confirm_new_password"]').val().trim();

    let errorMessage = "";

    if (current_password.length < 6) {
      errorMessage += "Mật khẩu cũ phải có ít nhất 6 ký tự. <br>";
    }
    if (new_password.length < 6) {
      errorMessage += "Mật khẩu mới phải có ít nhất 6 ký tự. <br>";
    }
    if (new_password != confirm_new_password) {
      errorMessage += "Mật khẩu nhập lại không khớp. <br>";
    }
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
        $(".btn-wapper button")
          .text("Cập nhật")
          .attr("disabled", false);
      },
    })
  });

  //from address
  $('#addAddressForm').submit(function (e) {
    e.preventDefault();

    let isValid = true;
    $('.error-message').remove();
    let fullName = $('#full_name').val().trim();
    let phone = $('#phone').val().trim();

    if (fullName.length < 3) {
      isValid = false;
      $('#full_name').after(
        '<p class= "error-message text-danger">Họ và tên không được ít hơn 3 ký tự </p>'
      );
    }

    let phoneRegex = /^[0-9]{10,11}$/;
    if (!phoneRegex.test(phone)) {
      isValid = false;
      $('#phone').after(
        '<p class= "error-message text-danger">Số điện thoại không hợp lệ.</p>'
      );
    }
    if (isValid) {
      this.submit();
    }
  });

  // ========================= PAGE PRODUCT =========================

  let currentPage = 1;

  /** Hàm kích hoạt nút + - */
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

      // CHỈ bind click khi KHÔNG ở trang /cart
      if (window.location.pathname !== '/cart') {
        $this.off("click").on("click", ".qtybutton", function () {
          var oldValue = parseInt($input.val()) || 1;
          var max = parseInt($input.data("max")) || 9999;

          if ($(this).hasClass("inc")) {
            if (oldValue < max) {
              $input.val(oldValue + 1);
            }
          } else {
            if (oldValue > 1) {
              $input.val(oldValue - 1);
            }
          }
        });
      }
    });
  }

  /** AJAX lấy sản phẩm */
  function fetchProducts() {
    let category_id = $(".category-filter.active").data('id') || '';
    let minPrice = $(".slider-range").slider('values', 0);
    let maxPrice = $(".slider-range").slider('values', 1);
    let sort_by = $("#sort-by").val();

    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
      }
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
        $("#liton_product_grid").html(response.products);
        $(".ltn__pagination").html(response.pagination);

        // Kích hoạt lại nút tăng giảm cho sản phẩm vừa load
        activateCartPlusMinus();
      },
      complete: function () {
        $("#loading-spinner").hide();
        $("#liton_product_grid").show();
      },
      error: function (xhr) {
        console.log(xhr.responseText);
        alert("Có lỗi xảy ra khi tải sản phẩm!");
      }
    });
  }

  /** Sự kiện phân trang */
  $(document).on('click', '.pagination-link', function (e) {
    e.preventDefault();
    let pageUrl = $(this).attr('href');
    let page = pageUrl.split('page=')[1];
    currentPage = page;
    fetchProducts();
  });

  /** Chọn danh mục */
  $(document).on("click", ".category-filter", function () {
    $(".category-filter").removeClass('active');
    $(this).addClass('active');
    currentPage = 1;
    fetchProducts();
  });

  /** Sắp xếp sản phẩm */
  $("#sort-by").change(function () {
    currentPage = 1;
    fetchProducts();
  });

  /** Lọc giá */
  $(".slider-range").slider({
    range: true,
    min: 0,
    max: 300000,
    values: [0, 300000],
    slide: function (event, ui) {
      $(".amount").val(ui.values[0] + " - " + ui.values[1] + "vnđ");
    },
    change: function () {
      currentPage = 1;
      fetchProducts();
    }
  });

  // Hiển thị lại text giá
  $(".amount").val($(".slider-range").slider("values", 0) + " - " +
    $(".slider-range").slider("values", 1) + "vnđ");

  /** ADD TO CART AJAX */
  /** ADD TO CART AJAX */
  $(document).on('click', '.add-to-cart-btn', function (e) {
    e.preventDefault();

    let productId = $(this).data('id');
    let quantity = $(this)
      .closest('.ltn__product-item, .ltn__product-details-menu-2')
      .find('.cart-plus-minus-box')
      .val() || 1;

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url: '/cart/add',
      type: 'POST',
      data: {
        product_id: productId,
        quantity: quantity
      },

      success: function (response) {

        toastr.success(response.message);
        $('#cart_count').text(response.cart_count);

        // 🔥 ĐÓNG tất cả modal đang mở (ví dụ: Quick View)
        $('.modal.show').modal('hide');

        // 🔥 MỞ modal Add To Cart
        $('#add_to_cart_modal-' + productId).modal('show');
      },

      error: function (xhr) {
        console.log(xhr.responseText);
        alert('Lỗi thêm vào giỏ hàng!');
      }
    });
  });



  /** Kích hoạt nút +/- khi mở Modal Quick View */
  $(document).on('shown.bs.modal', '.ltn__modal-area', function () {
    activateCartPlusMinus();
  });

  // Kích hoạt ngay khi load trang ban đầu
  activateCartPlusMinus();

  //cart mini
  $('.mini-cart-icon').on('click', function (e) {
    $.ajax({
      url: '/mini-cart',
      type: 'GET',
      success: function (response) {
        if (response.status) {
          $('#ltn__utilize-cart-menu .ltn__utilize-menu-inner').html(response.html);
          $('#ltn__utilize-cart-menu').addClass('ltn__utilize-open');

        } else {
          toastr.error('Không thể tải giỏ hàng!');
        }
      }
    });
  });

  $(document).on('click', '.ltn__utilize-close', function () {
    $('#ltn__utilize-cart-menu').removeClass('ltn__utilize-open');
    $('.ltn__utilize-overlay').hide();
  })

  // === LOGIC XÓA SẢN PHẨM KHỎI MINI CART ===
  $(document).on('click', '.remove-from-cart-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    let productId = $(this).data('product-id');
    let $itemToRemove = $(this).closest('.mini-cart-item');

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url: '/cart/remove',
      type: 'POST',
      data: {
        product_id: productId
      },
      beforeSend: function () {
        $itemToRemove.css('opacity', 0.5);
      },
      success: function (response) {
        if (response.success) {
          toastr.success(response.message);

          $itemToRemove.remove();
          $('#cart_count').text(response.cart_count);

          if (response.cart_count > 0) {
            $('.mini-cart-icon').trigger('click');
          } else {
            let emptyCartHtml = '<p class="text-center p-3 mb-0">Giỏ hàng trống</p>';
            $('.mini-cart-list').remove();
            $('.mini-cart-product-area').html(emptyCartHtml);
            $('.mini-cart-sub-total h5 span').text('0 VNĐ');
          }

        } else {
          toastr.error('Lỗi xóa sản phẩm!');
          $itemToRemove.css('opacity', 1);
        }
      },
      error: function (xhr) {
        console.log(xhr.responseText);
        toastr.error('Lỗi kết nối server khi xóa sản phẩm!');
        $itemToRemove.css('opacity', 1);
      }
    });
  });

  // ========================= PAGE DETAIL / CART =========================

  // Trang CART: xử lý tăng/giảm + gọi AJAX
  if (window.location.pathname === '/cart') {
    $(document).on('click', '.qtybutton', function () {
      let $button = $(this);
      let $input = $button.siblings('input');
      let oldValue = parseInt($input.val());
      let maxStock = parseInt($input.data('max'));
      let productId = $input.data('id');
      let newValue = oldValue;

      if ($button.hasClass('inc') && oldValue < maxStock) {
        newValue = oldValue + 1;
      } else if ($button.hasClass('dec') && oldValue > 1) {
        newValue = oldValue - 1;
      }

      if (newValue !== oldValue) {
        updateCart(productId, newValue, $input);
      }
    });
  }

  // Hàm UPDATE CART dùng cho trang /cart
  function updateCart(productId, quantity, $input) {

    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      }
    });

    $.ajax({
      url: '/cart/update',
      type: 'POST',
      data: {
        product_id: productId,
        quantity: quantity
      },

      success: function (response) {
        // cập nhật số lượng
        $input.val(response.quantity);

        // tiền từng dòng
        $input.closest('tr')
          .find('.cart-product-subtotal')
          .text(response.subtotal + 'đ');

        // tổng tiền hàng
        $('.cart-total').text(response.total + 'đ');

        // tổng thanh toán
        $('.cart-grand-total').text(response.grandTotal + 'đ');
      },

      error: function (xhr) {
        alert(xhr.responseJSON.error);
      }
    });
  }

  $('.remove-from-cart').on('click', function (e) {
    let productId = $(this).data('id');
    let row = $(this).closest('tr');
    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      }
    });
    $.ajax({
      url: '/cart/remove-cart',
      type: 'POST',
      data: {
        product_id: productId,
      },

      success: function (response) {
        row.remove();
        //tong tien hang
        $('.cart-total').text(response.total + 'đ');

        // tổng thanh toán
        $('.cart-grand-total').text(response.grandTotal + 'đ');
        if ($('.cart-product-remove').length == 0) {
          location.reload();
        }

      },

      error: function (xhr) {
        alert(xhr.responseJSON.error);
      }
    });
  })
  // =========================PAGE CHECKOUT =========================
  $('#list_address').change(function () {
    var addressId = $(this).val();

    $.ajax({
      url: '/checkout/get-address',
      type: 'GET',
      data: {
        address_id: addressId,
      },

      success: function (response) {
        if (response.success) {
          $('input[name="ltn__name"]').val(response.data.full_name);
          $('input[name="ltn__phone"]').val(response.data.phone);
          $('input[name="ltn__address"]').val(response.data.address);
          $('input[name="ltn__city"]').val(response.data.city);
          $('input[name="address_id"]').val(response.data.id);

        }
      },

      error: function (xhr) {
        alert(xhr.responseJSON?.message ?? "Lỗi không xác định");
      }
    });
  });
  // =========================pAYPAL=========================
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

  var totalPriceText = $('.totalPrice_Checkout').text().trim();
  var totalPriceNumber = parseFloat(totalPriceText.replace(/\./g, "").replace(" đ", ""));

  $('input[name="payment_method"]').on('change', togglePayment);
  paypal.Buttons({
    createOrder: function (data, actions) {
      return actions.order.create({
        purchase_units: [
          {
            amount: {
              value: (totalPriceNumber / 25000).toFixed(2),
            }
          }
        ]
      })
    },
    onApprove: function (data, actions) {
      return actions.order.capture().then(function (details) {
        //gui tt den sv
        fetch("/checkout/paypal", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $(
              'meta[name="csrf-token"]'
            ).attr("content"),
          },
          body: JSON.stringify({
            orderID: data.orderID,
            payerID: data.payerID,
            transactionID: details.id,
            amount: details.purchase_units[0].amount.value,
            address_id: $('#list_address').val(),
          })
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              toastr.success('Thanh toán thành công');
              window.location.href = "/account";

            } else {
              alert('Có lỗi xãy ra vui lòng thử lại');
            }

          })
      })
    }
  }).render('#paypal-button-container');
});
