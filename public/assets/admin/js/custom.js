$(document).ready(function () {
    // =========================management user=========================
    $(document).on('click', '.upgradeStaff', function (e) {
        let button = $(this);
        let userId = button.data('userid');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: 'user/upgrade',
            data: {
                user_id: userId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
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
            url: 'user/updateStatus',
            data: {
                user_id: userId,
                status: status,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    status == "banned" ? button.text("Đã chặn") : button.text("Đã xóa");
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
    // =========================management category=========================
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
    $('.category-image').on('change', function () {
        let categoryId = $(this).data('id');   // lấy ID danh mục
        let file = this.files[0];

        if (file) {
            let reader = new FileReader();

            reader.onload = function (event) {
                // Tìm đúng ảnh preview theo ID
                $('#image-preview-' + categoryId).attr('src', event.target.result).show();
            };

            reader.readAsDataURL(file);
        }
    });

    // update category
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

                    // Update row
                    let id = response.data.id;

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
    //delete category
    $(document).on('click', '.btn-delete-category', function (e) {
        e.preventDefault();
        let button = $(this);
        let categoryId = button.data('id');
        let row = button.closest('tr');
        if (confirm('Bạn có chắc chắn muốn xóa danh mục này không?')) {


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: 'categories/delete',
                data: {
                    category_id: categoryId,
                },
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
                    alert('Có lỗi xảy ra ' + error);
                }
            });
        }
    });
    // =========================management product=========================
    $("#product-images").change(function (e) {
        let files = e.target.files;
        let previewContainer = $("#image-preview-container");
        previewContainer.empty(); // Clear previous previews
        
        if(files.length > 0) {
            for(let i = 0; i < files.length; i++) {
                let file = files[i];
                let reader = new FileReader();
                reader.onload = function (event) {
                    let img = $('<img>')
                    .attr('src', event.target.result)
                    .addClass("image-preview");
                    previewContainer.append(img);
                }
                reader.readAsDataURL(file);
            }

        }else {
            previewContainer.html("");
        }
    });
    
});