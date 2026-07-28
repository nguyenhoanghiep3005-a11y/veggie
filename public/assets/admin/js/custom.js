$(document).ready(function () {

    // =============== QUẢN LÝ NGƯỜI DÙNG (MANAGEMENT USER) ===============

    // Chặn hoặc bỏ chặn tài khoản khách hàng
    $(document).on('click', '.changeStatus', function (e) {
        let button = $(this);
        let userId = button.data('userid');
        let status = button.data('status');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: button.data('url'),
            data: {
                user_id: userId,
                status: status,
            },

            success: function (response) {

                if (response.status) {
                    toastr.success(response.message);

                    let isBanned = status === 'banned';
                    let nextStatus = isBanned ? 'active' : 'banned';
                    let buttonLabel = isBanned
                        ? '<i class="fa fa-check"></i> Bỏ chặn'
                        : '<i class="fa fa-ban"></i> Chặn';

                    button
                        .data('status', nextStatus)
                        .attr('data-status', nextStatus)
                        .toggleClass('btn-warning', !isBanned)
                        .toggleClass('btn-success', isBanned)
                        .html(buttonLabel);

                    button.closest('.profile_view')
                        .find('.user-status')
                        .text(isBanned ? 'Đã chặn' : 'Đang hoạt động');
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Không thể cập nhật trạng thái tài khoản.');
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

                    let row = $('#category-row-' + id);

                    row.find('td:eq(0) img').attr('src', response.data.image);
                    row.find('td:eq(1)').text(response.data.name);
                    row.find('td:eq(2)').text(response.data.slug);
                    row.find('td:eq(3)').text(response.data.description);

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

    //  Xem trước ảnh khi update sản phẩm (dùng event delegation để hoạt động trong modal)
    $(document).on('change', '.product-images', function (e) {
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
                        .css({ width: '100px', height: '100px', objectFit: 'cover', marginRight: '10px', marginTop: '10px' });

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

        // FormData đã lấy trực tiếp file từ input trong form.
        // Không xoá rồi append lại vì có thể làm mất file trên một số trình duyệt.
        let formData = new FormData(form[0]);
        formData.append('product_id', productId);

        let imageInput = form.find('.product-images')[0];
        let allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        let allowedImageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

        if (imageInput && imageInput.files.length > 0) {
            for (let i = 0; i < imageInput.files.length; i++) {
                let file = imageInput.files[i];
                let extension = file.name.split('.').pop().toLowerCase();

                if ((!file.type || !allowedImageTypes.includes(file.type)) && !allowedImageExtensions.includes(extension)) {
                    toastr.error('Ảnh sản phẩm chỉ được dùng file jpeg, jpg, png, gif hoặc webp.');
                    return;
                }
            }
        }

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function () {
                button.prop('disabled', true).text('Đang cập nhật...');
            },

            success: function (response) {

                if (response.status) {

                    let product = response.data;

                    let imageSrc = product.image_url || (product.images && product.images.length > 0
                        ? product.images[0]
                        : "/storage/uploads/products/default.png");

                    let row = $('#product-row-' + product.id);
                    row.find('td:eq(0) img').attr('src', imageSrc);
                    row.find('td:eq(1)').html('<strong>' + product.display_name + '</strong>');
                    row.find('td:eq(2)').text(product.category_name);
                    row.find('td:eq(3)').text(new Intl.NumberFormat('vi-VN').format(product.price) + ' đ');
                    row.find('td:eq(4)').text(product.unit);

                    toastr.success(response.message);
                    $('#modalupdate-' + product.id).modal('hide');

                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let messages = [];

                    Object.values(xhr.responseJSON.errors).forEach(function (errors) {
                        messages = messages.concat(errors);
                    });

                    alert(messages.join('\n'));
                    return;
                }

                alert('Có lỗi xảy ra khi cập nhật sản phẩm.');
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

    // =============== DANH SÁCH ĐƠN HÀNG ===============
    function ordersPage() {
        return $('.admin-orders-page');
    }

    function orderListUrl(name) {
        return ordersPage().data(name);
    }

    function orderCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function disableOrderButton(button, text) {
        button.addClass('disabled')
            .css('pointer-events', 'none')
            .text(text);
    }

    function enableOrderButton(button, text) {
        button.removeClass('disabled')
            .css('pointer-events', '')
            .text(text);
    }

    $(document).on('click', '.confirm-order', function (event) {
        if (!ordersPage().length) {
            return;
        }

        event.preventDefault();

        let button = $(this);
        let orderId = button.data('id');

        if (!confirm('Bạn có chắc muốn xác nhận đơn hàng #' + orderId + ' không?')) {
            return;
        }

        $.ajax({
            url: orderListUrl('confirmUrl'),
            type: 'POST',
            data: {
                id: orderId,
                _token: orderCsrfToken()
            },
            beforeSend: function () {
                disableOrderButton(button, 'Đang xác nhận...');
            },
            success: function (response) {
                if (!response.status) {
                    toastr.error(response.message || 'Không thể xác nhận đơn hàng.');
                    enableOrderButton(button, 'Xác nhận');
                    return;
                }

                toastr.success(response.message);
                button.closest('tr').find('.order-status').html('<span class="custom-badge badge badge-primary">Đã xác nhận</span>');
                button.remove();
            },
            error: function (xhr) {
                let message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Không thể xác nhận đơn hàng. Vui lòng kiểm tra lại trạng thái đơn.';
                toastr.error(message);
                enableOrderButton(button, 'Xác nhận');
            }
        });
    });

    // Giao hàng (confirmed → shipping) — danh sách
    $(document).on('click', '.ship-order', function (event) {
        if (!ordersPage().length) {
            return;
        }

        event.preventDefault();

        let button = $(this);
        let orderId = button.data('id');

        if (!confirm('Bạn có chắc muốn giao đơn hàng #' + orderId + ' không?')) {
            return;
        }

        $.ajax({
            url: orderListUrl('shipUrl'),
            type: 'POST',
            data: {
                id: orderId,
                _token: orderCsrfToken()
            },
            success: function (response) {
                if (!response.status) {
                    toastr.error(response.message || 'Không thể giao đơn hàng.');
                    return;
                }

                toastr.success(response.message);
                let row = button.closest('tr');
                row.find('.order-status').html('<span class="custom-badge badge badge-info">Đang giao</span>');
                row.find('.confirm-order, .ship-order, .cancel-order').remove();
            },
            error: function () {
                toastr.error('Có lỗi xảy ra khi giao đơn hàng.');
            }
        });
    });

    // Hủy đơn hàng — mở modal nhập lý do (danh sách)
    $(document).on('click', '.cancel-order', function (event) {
        if (!ordersPage().length) {
            return;
        }

        event.preventDefault();

        let orderId = $(this).data('id');
        $('#cancel-order-id').val(orderId);
        $('#cancel-reason').val('');
        $('#cancel-reason-error').addClass('d-none');
        $('#cancelOrderModal').modal('show');
    });

    // Xác nhận hủy đơn hàng sau khi nhập lý do (danh sách + chi tiết)
    $(document).on('click', '#confirm-cancel-order', function () {
        let orderId = $('#cancel-order-id').val();
        let cancelReason = $('#cancel-reason').val().trim();

        if (!cancelReason) {
            $('#cancel-reason-error').removeClass('d-none');
            return;
        }

        $('#cancel-reason-error').addClass('d-none');
        let cancelBtn = $(this);
        cancelBtn.prop('disabled', true).text('Đang xử lý...');

        let cancelUrl = ordersPage().length
            ? orderListUrl('cancelUrl')
            : orderDetailUrl('cancelUrl');

        $.ajax({
            url: cancelUrl,
            type: 'POST',
            data: {
                id: orderId,
                cancel_reason: cancelReason,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#cancelOrderModal').modal('hide');
                if (!response.status) {
                    toastr.error(response.message || 'Không thể hủy đơn hàng.');
                    cancelBtn.prop('disabled', false).text('Xác nhận hủy');
                    return;
                }

                toastr.success(response.message);

                if (ordersPage().length) {
                    // Cập nhật trên danh sách
                    let row = $('[data-id="' + orderId + '"]').closest('tr');
                    row.find('.order-status').html('<span class="custom-badge badge badge-danger">QTV hủy đơn</span>');
                    row.find('.confirm-order, .ship-order, .cancel-order').remove();
                } else {
                    // Cập nhật trên chi tiết
                    setTimeout(function () { location.reload(); }, 1200);
                }
            },
            error: function (xhr) {
                $('#cancelOrderModal').modal('hide');
                toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra khi hủy đơn hàng.');
            },
            complete: function () {
                cancelBtn.prop('disabled', false).text('Xác nhận hủy');
            }
        });
    });

    // =============== CHI TIẾT ĐƠN HÀNG ===============
    function orderDetailPage() {
        return $('.admin-order-detail-page');
    }

    function orderReturnPage() {
        return $('.admin-order-detail-page');
    }

    function orderDetailUrl(name, id) {
        let page = orderDetailPage();
        let url = page.data(name);

        return id ? String(url).replace('__ID__', id) : url;
    }

    function orderReturnUrl(name, id) {
        let page = orderReturnPage();
        let url = page.data(name);

        return id ? String(url).replace('__ID__', id) : url;
    }

    function showOrderDetailAlert(type, message) {
        let cssClass = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#alert-box').html(
            '<div class="alert ' + cssClass + ' alert-dismissible">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            message +
            '</div>'
        );
    }

    function postOrderAction(url, data, successDelay = 1200) {
        data._token = $('meta[name="csrf-token"]').attr('content');

        $.post(url, data, function (response) {
            showOrderDetailAlert(response.status ? 'success' : 'danger', response.message || 'Không thể xử lý yêu cầu.');
            if (response.status) {
                setTimeout(function () { location.reload(); }, successDelay);
            }
        }).fail(function (xhr) {
            showOrderDetailAlert('danger', xhr.responseJSON?.message || 'Không thể xử lý yêu cầu.');
        });
    }

    $(document).on('click', '.confirm-order-btn', function () {
        if (!orderDetailPage().length || !confirm('Xác nhận đơn hàng này và gửi hóa đơn cho khách?')) {
            return;
        }

        let button = $(this);
        button.prop('disabled', true);
        postOrderAction(orderDetailUrl('confirmUrl'), { id: button.data('id') });
    });

    $(document).on('click', '.ship-order-btn', function () {
        if (!orderDetailPage().length || !confirm('Giao đơn hàng này?')) {
            return;
        }

        postOrderAction(orderDetailUrl('shipUrl'), { id: $(this).data('id') });
    });

    $(document).on('click', '.complete-order-btn', function () {
        if (!orderDetailPage().length || !confirm('Đánh dấu đơn hàng đã giao thành công?')) {
            return;
        }

        postOrderAction(orderDetailUrl('completeUrl'), {
            id: $(this).data('id'),
            status: 'completed'
        });
    });

    // Hủy đơn hàng — mở modal nhập lý do (chi tiết)
    $(document).on('click', '.cancel-order-btn', function () {
        if (!orderDetailPage().length) {
            return;
        }

        let orderId = $(this).data('id');
        $('#cancel-order-id').val(orderId);
        $('#cancel-reason').val('');
        $('#cancel-reason-error').addClass('d-none');
        $('#cancelOrderModal').modal('show');
    });

    $(document).on('click', '.approve-return-btn', function () {
        let id = $(this).data('id');
        if (!orderReturnPage().length || !confirm('Duyệt yêu cầu xử lý hàng lỗi/hư?')) {
            return;
        }

        postOrderAction(orderReturnUrl('returnApproveUrl', id), {}, 1000);
    });

    $(document).on('click', '.receive-return-btn', function () {
        let id = $(this).data('id');
        if (!orderReturnPage().length || !confirm('Xác nhận đã nhận hàng lỗi/hư từ khách?')) {
            return;
        }

        postOrderAction(orderReturnUrl('returnReceiveUrl', id), {}, 1000);
    });

    $(document).on('click', '.complete-return-btn', function () {
        let id = $(this).data('id');
        if (!orderReturnPage().length || !confirm('Hoàn tất yêu cầu đổi hàng này?')) {
            return;
        }

        postOrderAction(orderReturnUrl('returnCompleteUrl', id), {}, 1000);
    });

    // =============== PHIẾU ĐẶT MUA ===============
    function initCouponForm() {
        function toggleCustomerBox(select) {
            let form = select.closest('.admin-coupon-form');
            if (!form) {
                return;
            }

            let customerBox = form.querySelector('.admin-coupon-customer-box');
            if (!customerBox) {
                return;
            }

            customerBox.classList.toggle('d-none', select.value !== 'customer');
        }

        document.querySelectorAll('.js-coupon-apply-type').forEach(function (select) {
            toggleCustomerBox(select);

            select.addEventListener('change', function () {
                toggleCustomerBox(select);
            });
        });
    }

    function initPurchaseOrderForm() {
        let tbody = document.getElementById('purchase-order-items');
        let addButton = document.getElementById('btn-add-purchase-row');
        let optionTemplate = document.getElementById('purchase-product-options');

        if (!tbody || !addButton || !optionTemplate) {
            return;
        }

        let index = tbody.children.length;

        function addRow() {
            let row = document.createElement('tr');
            row.innerHTML =
                '<td><select name="items[' + index + '][product_id]" class="form-control js-product-select" required>' + optionTemplate.innerHTML + '</select></td>' +
                '<td><input type="number" name="items[' + index + '][quantity_ordered]" class="form-control text-center" min="1" required></td>' +
                '<td><button type="button" class="btn btn-danger btn-sm js-remove-row"><i class="fa fa-trash"></i></button></td>';

            tbody.appendChild(row);
            index++;
        }

        tbody.addEventListener('click', function (event) {
            let button = event.target.closest('.js-remove-row');
            if (!button) {
                return;
            }

            button.closest('tr').remove();
            if (!tbody.children.length) {
                addRow();
            }
        });

        addButton.addEventListener('click', addRow);

        if (!tbody.children.length) {
            addRow();
        }
    }

    // =============== PHIẾU NHẬP HÀNG ===============
    function initPurchaseImportForm() {
        let form = document.getElementById('purchase-import-form');
        let evidence = document.getElementById('defect-evidence');
        let description = document.getElementById('defect-description');
        let mark = document.getElementById('defect-required-mark');
        let evidenceMark = document.getElementById('defect-evidence-required-mark');
        let submit = document.getElementById('btn-submit-import');

        if (!form || !evidence || !description || !mark || !evidenceMark || !submit) {
            return;
        }

        function refreshImportForm() {
            let hasRejected = false;
            let invalid = false;

            document.querySelectorAll('.js-import-row').forEach(function (row) {
                let ordered = parseInt(row.querySelector('.js-ordered').value, 10) || 0;
                let receivedInput = row.querySelector('.js-received');
                let rejectedInput = row.querySelector('.js-rejected');
                let received = parseInt(receivedInput.value, 10) || 0;
                let rejected = parseInt(rejectedInput.value, 10) || 0;
                let accepted = Math.max(0, received - rejected);

                row.querySelector('.js-accepted').value = accepted;
                row.querySelector('.js-manufactured').required = accepted > 0;
                row.querySelector('.js-expired').required = accepted > 0;

                hasRejected = hasRejected || rejected > 0;
                invalid = invalid || received > ordered || rejected > received;
                receivedInput.classList.toggle('parsley-error', received > ordered);
                rejectedInput.classList.toggle('parsley-error', rejected > received);
            });

            evidence.required = hasRejected;
            description.required = hasRejected;
            mark.classList.toggle('d-none', !hasRejected);
            evidenceMark.classList.toggle('d-none', !hasRejected);
            submit.disabled = invalid;
        }

        form.addEventListener('input', refreshImportForm);
        refreshImportForm();
    }

    initCouponForm();
    initPurchaseOrderForm();
    initPurchaseImportForm();

});
