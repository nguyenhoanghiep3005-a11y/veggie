$(document).ready(function () {

    // =============== QUẢN LÝ NGƯỜI DÙNG (MANAGEMENT USER) ===============

    //   User sang Staff
    $(document).on('click', '.upgradeStaff', function (e) {
        let button = $(this);
        let userId = button.data('userid'); // Lấy user_id từ nút bấm

        // Thêm CSRF token cho AJAX POST của Laravel
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: 'user/upgrade', // route xử lý nâng cấp user
            data: {
                user_id: userId,
            },

            success: function (response) {

                if (response.status) {
                    toastr.success(response.message);

                    // Cập nhật giao diện trong danh sách user
                    button.closest('.profile_view').find('.brief i').text('Staff');
                    button.closest('.profile_view').find('.changeStatus').hide();
                    button.hide();
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr, status, error) {
                alert('AJAX error: ' + error);
            }
        });
    });

    //  Khóa / Xóa người dùng
    $(document).on('click', '.changeStatus', function (e) {

        let button = $(this);
        let userId = button.data('userid');
        let status = button.data('status');   // banned hoặc deleted

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: 'user/updateStatus',
            data: {
                user_id: userId,
                status: status,
            },

            success: function (response) {

                if (response.status) {
                    toastr.success(response.message);

                    // Đổi text nút khi update thành công
                    status == "banned"
                        ? button.text("Đã chặn")
                        : button.text("Đã xóa");

                    // Disable nút
                    button.addClass("disabled").prop("disabled", true);
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr, status, error) {
                alert('AJAX error: ' + error);
            }
        });
    });

    // =============== QUẢN LÝ DANH MỤC (MANAGEMENT CATEGORY) ============

    //  Xem trước ảnh khi thêm danh mục mới
    $("#category-image").change(function () {
        let file = this.files[0];

        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#image-preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').hide();
        }
    });

    //  Xem trước ảnh khi cập nhật danh mục (đúng theo ID danh mục)
    $('.category-image').on('change', function () {
        let categoryId = $(this).data('id');
        let file = this.files[0];

        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                $('#image-preview-' + categoryId)
                    .attr('src', event.target.result)
                    .show();
            };
            reader.readAsDataURL(file);
        }
    });

    //  Cập nhật danh mục bằng AJAX
    $(document).on('click', '.btn-update-submit-category', function (e) {
        e.preventDefault();

        let button = $(this);
        let categoryId = button.data('id');
        let form = button.closest(".modal").find('form');
        let formData = new FormData(form[0]);
        formData.append('category_id', categoryId);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: 'categories/update',
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function () {
                button.prop('disabled', true).text('Đang cập nhật...');
            },

            success: function (response) {

                if (response.status) {
                    toastr.success(response.message);

                    let id = response.data.id;

                    // Cập nhật lại dòng danh mục
                    let newRow = `
                <tr id="category-row-${id}">
                    <td><img src="${response.data.image}" width="50"></td>
                    <td>${response.data.name}</td>
                    <td>${response.data.slug}</td>
                    <td>${response.data.description}</td>
                    <td>
                        <a class="btn btn-app btn-update-category" data-toggle="modal"
                        data-target="#modalupdate-${id}">
                        <i class="fa fa-edit"></i>Chỉnh sửa</a>
                    </td>
                    <td>
                        <a class="btn btn-app btn-delete-category" data-id="${id}">
                        <i class="fa fa-close"></i>Xóa</a>
                    </td>
                </tr>`;

                    $('#category-row-' + id).replaceWith(newRow);

                    // Đóng modal
                    $('#modalupdate-' + id).modal('hide');

                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr) {
                alert(xhr.responseText);
            },

            complete: function () {
                button.prop('disabled', false).text('Cập nhật danh mục');
            }
        });
    });

    //  Xóa danh mục
    $(document).on('click', '.btn-delete-category', function (e) {

        e.preventDefault();

        let button = $(this);
        let categoryId = button.data('id');
        let row = button.closest('tr');

        if (confirm('Bạn có chắc chắn muốn xóa danh mục này không?')) {

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $.ajax({
                type: 'POST',
                url: 'categories/delete',
                data: { category_id: categoryId },

                success: function (response) {

                    if (response.status) {
                        toastr.success(response.message);

                        // Xóa hàng khỏi bảng
                        row.fadeOut(300, function () {
                            $(this).remove();
                        });

                    } else {
                        toastr.error(response.message);
                    }
                },

                error: function (xhr, status, error) {
                    alert('Có lỗi xảy ra ' + error);
                }
            });
        }
    });

    // =============== QUẢN LÝ SẢN PHẨM (MANAGEMENT PRODUCT) =============

    //  Xem trước ảnh khi thêm sản phẩm mới
    $("#product-images").change(function (e) {
        let files = e.target.files;
        let previewContainer = $("#image-preview-container");

        previewContainer.empty(); // xóa ảnh cũ

        if (files.length > 0) {

            for (let i = 0; i < files.length; i++) {

                let file = files[i];
                let reader = new FileReader();

                reader.onload = function (event) {
                    let img = $('<img>')
                        .attr('src', event.target.result)
                        .addClass("image-preview");

                    previewContainer.append(img);
                };

                reader.readAsDataURL(file);
            }
        }
    });

    //  Xem trước ảnh khi update sản phẩm
    $(".product-images").change(function (e) {
        let files = e.target.files;
        let productId = $(this).data("id");
        let previewContainer = $("#image-preview-container-" + productId);

        previewContainer.empty();

        if (files.length > 0) {

            for (let i = 0; i < files.length; i++) {

                let file = files[i];
                let reader = new FileReader();

                reader.onload = function (event) {
                    let img = $('<img>')
                        .attr('src', event.target.result)
                        .addClass("image-preview");

                    previewContainer.append(img);
                };

                reader.readAsDataURL(file);
            }
        }
    });

    //  Update sản phẩm
    $(document).on('click', '.btn-update-submit-product', function (e) {

        e.preventDefault();

        let button = $(this);
        let productId = button.data("id");
        let form = button.closest(".modal").find('form');

        let formData = new FormData(form[0]);
        formData.append('product_id', productId);

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            type: 'POST',
            url: 'product/update',
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function () {
                button.prop('disabled', true).text('Đang cập nhật...');
            },

            success: function (response) {

                if (response.status) {

                    let product = response.data;

                    let imageSrc = product.images.length > 0
                        ? product.images[0]
                        : "storage/products/product_default.png";

                    // Render dòng mới
                    let newRow = `
                <tr id="product-row-${product.id}">
                    <td><img src="${imageSrc}" width="50"></td>
                    <td>${product.name}</td>
                    <td>${product.category_name}</td>
                    <td>${product.slug}</td>
                    <td>${product.description}</td>
                    <td>${product.stock}</td>
                    <td>${new Intl.NumberFormat("vi-VN").format(product.price)} VND</td>
                    <td>${product.unit}</td>
                    <td>${product.status}</td>
                    <td>
                        <a class="btn btn-app btn-update-product" data-toggle="modal"
                        data-target="#modalupdate-${product.id}">
                        <i class="fa fa-edit"></i>Chỉnh sửa</a>
                    </td>
                    <td>
                        <a class="btn btn-app btn-delete-product" data-id="${product.id}">
                        <i class="fa fa-close"></i>Xóa</a>
                    </td>
                </tr>`;

                    $('#product-row-' + product.id).replaceWith(newRow);

                    toastr.success(response.message);
                    $('#modalupdate-' + product.id).modal('hide');

                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr) {
                alert(xhr.responseText);
            },

            complete: function () {
                button.prop('disabled', false).text('Cập nhật sản phẩm');
            }
        });
    });

    //  Xóa sản phẩm
    $(document).on('click', '.btn-delete-product', function (e) {
        e.preventDefault();

        let button = $(this);
        let productId = button.data('id');
        let row = button.closest('tr');

        if (confirm('Bạn có chắc muốn xóa sản phẩm này không?')) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: 'product/delete',
                data: { product_id: productId },

                success: function (response) {

                    if (response.status) {

                        toastr.success(response.message);

                        row.fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },

                error: function (xhr, status, error) {
                    alert('Có lỗi xảy ra: ' + error);
                }
            });
        }
    });

    // =============== QUẢN LÝ ĐƠN HÀNG (MANAGEMENT ORDER) ===============

    //  Xác nhận đơn hàng
    $(document).on('click', '.confirm-order', function (e) {

        e.preventDefault();

        let button = $(this);
        let orderId = button.data("id");

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            type: 'POST',
            url: 'order/confirm',
            data: { id: orderId },

            success: function (response) {
                if (response.status) {

                    toastr.success(response.message);

                    // cập nhật trạng thái đơn hàng
                    button.closest("tr")
                        .find(".order-status")
                        .html('<span class="custom-badge badge badge-info">Đang giao</span>');

                    button.hide();
                }
                else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr, status, error) {
                alert('AJAX error: ' + error);
            }
        });
    });

    //  Hủy đơn hàng
    $(document).on('click', '.cancel-order', function (e) {

        e.preventDefault();

        let button = $(this);
        let orderId = button.data("id");

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            type: 'POST',
            url: '/admin/order-detail/cancel-order',
            data: { id: orderId },

            success: function (response) {

                if (response.status) {
                    toastr.success(response.message);
                    button.remove(); // Xóa nút cancel
                }
                else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr, status, error) {
                alert('AJAX error: ' + error);
            }
        });
    });

});
