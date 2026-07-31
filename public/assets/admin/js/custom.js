$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Cap nhat trang thai tai khoan khach hang.
    $(document).on('click', '.changeStatus', function () {
        var nutBam = $(this);
        var maNguoiDung = nutBam.data('userid');
        var trangThai = nutBam.data('status');

        $.ajax({
            type: 'POST',
            url: '/admin/nguoi-dung/cap-nhat-trang-thai',
            data: {
                ma_nguoi_dung: maNguoiDung,
                trang_thai: trangThai
            },
            success: function (response) {
                if (!response.trang_thai) {
                    toastr.error(response.thong_bao);
                    return;
                }

                var dangBiKhoa = response.du_lieu.trang_thai === 'bi_khoa';
                var trangThaiTiepTheo = 'bi_khoa';
                var noiDungNut = '<i class="fa fa-ban"></i> Chặn';

                if (dangBiKhoa) {
                    trangThaiTiepTheo = 'hoat_dong';
                    noiDungNut = '<i class="fa fa-check"></i> Bỏ chặn';
                }

                nutBam
                    .data('status', trangThaiTiepTheo)
                    .attr('data-status', trangThaiTiepTheo)
                    .toggleClass('btn-warning', !dangBiKhoa)
                    .toggleClass('btn-success', dangBiKhoa)
                    .html(noiDungNut);

                nutBam.closest('.profile_view')
                    .find('.user-status')
                    .text(dangBiKhoa ? 'Đã chặn' : 'Đang hoạt động');

                toastr.success(response.thong_bao);
            },
            error: function (xhr) {
                var thongBao = 'Không thể cập nhật trạng thái tài khoản.';

                if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
                    thongBao = xhr.responseJSON.thong_bao;
                }

                toastr.error(thongBao);
            }
        });
    });

    // Xem truoc hinh anh khi them danh muc.
    $('#category-image').on('change', function () {
        var tepHinhAnh = this.files[0];

        if (!tepHinhAnh) {
            $('#image-preview').hide();
            return;
        }

        var docTep = new FileReader();
        docTep.onload = function (suKien) {
            $('#image-preview').attr('src', suKien.target.result).show();
        };
        docTep.readAsDataURL(tepHinhAnh);
    });

    // Xem truoc hinh anh khi cap nhat danh muc.
    $('.category-image').on('change', function () {
        var maDanhMuc = $(this).data('id');
        var tepHinhAnh = this.files[0];

        if (!tepHinhAnh) {
            return;
        }

        var docTep = new FileReader();
        docTep.onload = function (suKien) {
            $('#image-preview-' + maDanhMuc)
                .attr('src', suKien.target.result)
                .show();
        };
        docTep.readAsDataURL(tepHinhAnh);
    });

    // Cap nhat danh muc.
    $(document).on('click', '.btn-update-submit-category', function (suKien) {
        suKien.preventDefault();

        var nutBam = $(this);
        var maDanhMuc = nutBam.data('id');
        var form = nutBam.closest('.modal').find('form');
        var data = new FormData(form[0]);
        data.append('ma_danh_muc', maDanhMuc);

        $.ajax({
            type: 'POST',
            url: '/admin/danh-muc/cap-nhat',
            data: data,
            processData: false,
            contentType: false,
            beforeSend: function () {
                nutBam.prop('disabled', true).text('Đang cập nhật...');
            },
            success: function (response) {
                if (!response.trang_thai) {
                    toastr.error(response.thong_bao);
                    return;
                }

                var danhMuc = response.du_lieu;
                var dong = $('#category-row-' + danhMuc.ma_danh_muc);

                dong.find('td:eq(0) img').attr('src', danhMuc.duong_dan_hinh_anh);
                dong.find('td:eq(1)').text(danhMuc.ten);
                dong.find('td:eq(2)').text(danhMuc.duong_dan);
                dong.find('td:eq(3)').text(danhMuc.mo_ta);
                $('#modalupdate-' + danhMuc.ma_danh_muc).modal('hide');

                toastr.success(response.thong_bao);
            },
            error: function (xhr) {
                var thongBao = 'Không thể cập nhật danh mục.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    thongBao = xhr.responseJSON.message;
                }

                toastr.error(thongBao);
            },
            complete: function () {
                nutBam.prop('disabled', false).text('Cập nhật danh mục');
            }
        });
    });

    // Xoa danh muc.
    $(document).on('click', '.btn-delete-category', function (suKien) {
        suKien.preventDefault();

        if (!confirm('Bạn có chắc chắn muốn xóa danh mục này không?')) {
            return;
        }

        var nutBam = $(this);
        var maDanhMuc = nutBam.data('id');
        var dong = nutBam.closest('tr');

        $.ajax({
            type: 'POST',
            url: '/admin/danh-muc/xoa',
            data: { ma_danh_muc: maDanhMuc },
            success: function (response) {
                if (!response.trang_thai) {
                    toastr.error(response.thong_bao);
                    return;
                }

                toastr.success(response.thong_bao);
                dong.fadeOut(300, function () {
                    $(this).remove();
                });
            },
            error: function (xhr) {
                var thongBao = 'Không thể xóa danh mục.';

                if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
                    thongBao = xhr.responseJSON.thong_bao;
                }

                toastr.error(thongBao);
            }
        });
    });

    // Xem truoc cac hinh anh khi them san pham.
    $('#product-images').on('change', function (suKien) {
        var tepHinhAnhs = suKien.target.files;
        var khuVucXemTruoc = $('#image-preview-container');
        khuVucXemTruoc.empty();

        for (var viTri = 0; viTri < tepHinhAnhs.length; viTri++) {
            var docTep = new FileReader();

            docTep.onload = function (suKienDocTep) {
                var hinhAnh = $('<img>')
                    .attr('src', suKienDocTep.target.result)
                    .addClass('image-preview');

                khuVucXemTruoc.append(hinhAnh);
            };

            docTep.readAsDataURL(tepHinhAnhs[viTri]);
        }
    });

    // Xem truoc cac hinh anh khi cap nhat san pham.
    $(document).on('change', '.product-images', function (suKien) {
        var tepHinhAnhs = suKien.target.files;
        var maSanPham = $(this).data('id');
        var khuVucXemTruoc = $('#image-preview-container-' + maSanPham);
        khuVucXemTruoc.empty();

        for (var viTri = 0; viTri < tepHinhAnhs.length; viTri++) {
            var docTep = new FileReader();

            docTep.onload = function (suKienDocTep) {
                var hinhAnh = $('<img>')
                    .attr('src', suKienDocTep.target.result)
                    .addClass('image-preview');

                khuVucXemTruoc.append(hinhAnh);
            };

            docTep.readAsDataURL(tepHinhAnhs[viTri]);
        }
    });

    // Cap nhat san pham.
    $(document).on('click', '.btn-update-submit-product', function (suKien) {
        suKien.preventDefault();

        var nutBam = $(this);
        var maSanPham = nutBam.data('id');
        var form = nutBam.closest('.modal').find('form');
        var data = new FormData(form[0]);
        data.append('ma_san_pham', maSanPham);

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: data,
            processData: false,
            contentType: false,
            beforeSend: function () {
                nutBam.prop('disabled', true).text('Đang cập nhật...');
            },
            success: function (response) {
                if (!response.trang_thai) {
                    toastr.error(response.thong_bao);
                    return;
                }

                var sanPham = response.du_lieu;
                var dong = $('#product-row-' + sanPham.ma_san_pham);

                dong.find('td:eq(0) img').attr('src', sanPham.duong_dan_hinh_anh);
                dong.find('td:eq(1)').html('<strong>' + sanPham.ten_hien_thi + '</strong>');
                dong.find('td:eq(2)').text(sanPham.ten_danh_muc);
                dong.find('td:eq(3)').text(new Intl.NumberFormat('vi-VN').format(sanPham.gia) + ' đ');
                dong.find('td:eq(4)').text(sanPham.don_vi);

                $('#modalupdate-' + sanPham.ma_san_pham).modal('hide');
                toastr.success(response.thong_bao);
            },
            error: function (xhr) {
                var thongBao = 'Không thể cập nhật sản phẩm.';

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    for (var tenTruong in xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors[tenTruong].length > 0) {
                            thongBao = xhr.responseJSON.errors[tenTruong][0];
                            break;
                        }
                    }
                }

                toastr.error(thongBao);
            },
            complete: function () {
                nutBam.prop('disabled', false).text('Cập nhật sản phẩm');
            }
        });
    });

    // Xoa san pham.
    $(document).on('click', '.btn-delete-product', function (suKien) {
        suKien.preventDefault();

        if (!confirm('Bạn có chắc muốn xóa sản phẩm này không?')) {
            return;
        }

        var nutBam = $(this);
        var maSanPham = nutBam.data('id');
        var dong = nutBam.closest('tr');

        $.ajax({
            type: 'POST',
            url: '/admin/san-pham/xoa',
            data: { ma_san_pham: maSanPham },
            success: function (response) {
                if (!response.trang_thai) {
                    toastr.error(response.thong_bao);
                    return;
                }

                toastr.success(response.thong_bao);
                dong.fadeOut(300, function () {
                    $(this).remove();
                });
            },
            error: function (xhr) {
                var thongBao = 'Không thể xóa sản phẩm.';

                if (xhr.responseJSON && xhr.responseJSON.thong_bao) {
                    thongBao = xhr.responseJSON.thong_bao;
                }

                toastr.error(thongBao);
            }
        });
    });
    // Xu ly cac thao tac don hang o trang danh sach va chi tiet.
    // Gui thao tac don hang va tai lai trang khi thanh cong.
    function guiThaoTacDonHang(duongDan, data) {
        if (!duongDan) {
            toastr.error('Không tìm thấy đường dẫn xử lý.');
            return;
        }

        $.ajax({
            url: duongDan,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: function (response) {
                if (!response.trang_thai) {
                    toastr.error(response.thong_bao);
                    return;
                }

                toastr.success(response.thong_bao);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            },
            error: function (xhr) {
                var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
                    ? xhr.responseJSON.thong_bao
                    : 'Không thể xử lý đơn hàng.';
                toastr.error(thongBao);
            }
        });
    }

    $(document).on('click', '.nut-xac-nhan-don', function () {
        guiThaoTacDonHang(
            '/admin/don-hang/xac-nhan',
            { ma_don_hang: $(this).data('ma-don-hang') }
        );
    });

    $(document).on('click', '.nut-giao-don', function () {
        guiThaoTacDonHang(
            '/admin/don-hang/giao-hang',
            { ma_don_hang: $(this).data('ma-don-hang') }
        );
    });

    $(document).on('click', '.nut-hoan-tat-don', function () {
        guiThaoTacDonHang(
            '/admin/don-hang/hoan-tat',
            { ma_don_hang: $(this).data('ma-don-hang') }
        );
    });

    $(document).on('click', '.nut-hoan-ve', function () {
        guiThaoTacDonHang(
            '/admin/don-hang/hoan-ve-cua-hang',
            { ma_don_hang: $(this).data('ma-don-hang') }
        );
    });

    $(document).on('click', '.nut-hoan-tien-paypal', function () {
        guiThaoTacDonHang(
            '/admin/don-hang/hoan-tien-paypal',
            { ma_don_hang: $(this).data('ma-don-hang') }
        );
    });

    // Mo form nhap ly do huy don.
    $(document).on('click', '.nut-mo-huy-don', function () {
        $('#ma-don-hang-huy').val($(this).data('ma-don-hang'));
        $('#ly-do-huy-don').val('');
        $('#modal-huy-don').modal('show');
    });

    $('#nut-xac-nhan-huy-don').on('click', function () {
        var maDonHang = $('#ma-don-hang-huy').val();
        var lyDoHuy = $('#ly-do-huy-don').val().trim();

        if (!lyDoHuy) {
            toastr.error('Vui lòng nhập lý do hủy đơn.');
            return;
        }

        guiThaoTacDonHang(
            '/admin/don-hang/huy',
            {
                ma_don_hang: maDonHang,
                ly_do_huy: lyDoHuy
            }
        );
    });

    // Mo form ghi nhan giao hang that bai.
    $(document).on('click', '.nut-mo-giao-that-bai', function () {
        $('#ma-don-giao-that-bai').val($(this).data('ma-don-hang'));
        $('#ly-do-giao-that-bai').val('');
        $('#modal-giao-that-bai').modal('show');
    });

    $('#nut-xac-nhan-giao-that-bai').on('click', function () {
        var maDonHang = $('#ma-don-giao-that-bai').val();
        var lyDoGiaoThatBai = $('#ly-do-giao-that-bai').val().trim();

        if (!lyDoGiaoThatBai) {
            toastr.error('Vui lòng nhập lý do giao thất bại.');
            return;
        }

        guiThaoTacDonHang(
            '/admin/don-hang/giao-that-bai',
            {
                ma_don_hang: maDonHang,
                ly_do_giao_that_bai: lyDoGiaoThatBai
            }
        );
    });

    // Mo form kiem tra hang hoan ve cua hang.
    $(document).on('click', '.nut-mo-nhan-hang-hoan', function () {
        $('#ma-don-nhan-hang-hoan').val($(this).data('ma-don-hang'));
        $('#tinh-trang-hang-hoan').val('nguyen_ven').trigger('change');
        $('#modal-nhan-hang-hoan').modal('show');
    });

    $('#tinh-trang-hang-hoan').on('change', function () {
        if ($(this).val() === 'hu_hong') {
            $('#khu-vuc-hang-hoan-hu').removeClass('d-none');
        } else {
            $('#khu-vuc-hang-hoan-hu').addClass('d-none');
            $('#ly-do-hang-hoan-hu').val('');
            $('#minh-chung-hang-hoan').val('');
        }
    });

    $('#nut-xac-nhan-nhan-hang-hoan').on('click', function () {
        var maDonHang = $('#ma-don-nhan-hang-hoan').val();
        var tinhTrangHangHoan = $('#tinh-trang-hang-hoan').val();
        var lyDoHangHu = $('#ly-do-hang-hoan-hu').val().trim();
        var cacTep = $('#minh-chung-hang-hoan')[0].files;

        if (tinhTrangHangHoan === 'hu_hong' && (!lyDoHangHu || cacTep.length === 0)) {
            toastr.error('Hàng hư phải có mô tả và ít nhất một ảnh hoặc video minh chứng.');
            return;
        }

        var data = new FormData();
        data.append('ma_don_hang', maDonHang);
        data.append('tinh_trang_hang_hoan', tinhTrangHangHoan);
        data.append('ly_do_hang_hoan_hu', lyDoHangHu);

        for (var viTri = 0; viTri < cacTep.length; viTri++) {
            data.append('minh_chung[]', cacTep[viTri]);
        }

        $.ajax({
            url: '/admin/don-hang/nhan-hang-hoan',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.trang_thai) {
                    toastr.success(response.thong_bao);
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                } else {
                    toastr.error(response.thong_bao);
                }
            },
            error: function (xhr) {
                var thongBao = xhr.responseJSON && xhr.responseJSON.thong_bao
                    ? xhr.responseJSON.thong_bao
                    : 'Không thể xác nhận hàng hoàn.';
                toastr.error(thongBao);
            }
        });
    });

    // Gui cac buoc xu ly yeu cau doi tra.
    function guiThaoTacDoiTra(duongDanXuLy, maYeuCau) {
        guiThaoTacDonHang('/admin/doi-tra/' + maYeuCau + '/' + duongDanXuLy, {});
    }


    $(document).on('click', '.nut-duyet-doi-tra', function () {
        guiThaoTacDoiTra(
            'duyet',
            $(this).data('ma-yeu-cau')
        );
    });

    $(document).on('click', '.nut-nhan-doi-tra', function () {
        guiThaoTacDoiTra(
            'nhan-hang',
            $(this).data('ma-yeu-cau')
        );
    });

    $(document).on('click', '.nut-hoan-tat-doi-tra', function () {
        guiThaoTacDoiTra(
            'hoan-tat',
            $(this).data('ma-yeu-cau')
        );
    });

    // Hien hoac an danh sach khach hang theo pham vi su dung.
    function khoiTaoFormPhieuGiamGia() {
        function capNhatKhuVucKhachHang(oLuaChon) {
            var form = oLuaChon.closest('.admin-coupon-form');

            if (!form) {
                return;
            }

            var khuVucKhachHang = form.querySelector('.admin-coupon-customer-box');

            if (!khuVucKhachHang) {
                return;
            }

            khuVucKhachHang.classList.toggle('d-none', oLuaChon.value !== 'khach_hang');
        }

        var cacOLuaChon = document.querySelectorAll('.js-coupon-apply-type');

        for (var viTri = 0; viTri < cacOLuaChon.length; viTri++) {
            capNhatKhuVucKhachHang(cacOLuaChon[viTri]);

            cacOLuaChon[viTri].addEventListener('change', function () {
                capNhatKhuVucKhachHang(this);
            });
        }
    }
    // Khởi tạo các dòng sản phẩm trong form đơn đặt nhập.
    function khoiTaoFormDonDatNhap() {
        let thanBang = document.getElementById('purchase-order-items');
        let nutThem = document.getElementById('btn-add-purchase-row');
        let mauLuaChon = document.getElementById('purchase-product-options');

        if (!thanBang || !nutThem || !mauLuaChon) {
            return;
        }

        let viTri = thanBang.children.length;

        function themDong() {
            let dong = document.createElement('tr');
            dong.innerHTML =
                '<td><select name="chi_tiets[' + viTri + '][ma_san_pham]" class="form-control js-product-select" required>' + mauLuaChon.innerHTML + '</select></td>' +
                '<td><input type="number" name="chi_tiets[' + viTri + '][so_luong_dat]" class="form-control text-center" min="1" required></td>' +
                '<td><button type="button" class="btn btn-danger btn-sm js-remove-row"><i class="fa fa-trash"></i></button></td>';

            thanBang.appendChild(dong);
            viTri++;
        }

        thanBang.addEventListener('click', function (suKien) {
            let nutXoa = suKien.target.closest('.js-remove-row');
            if (!nutXoa) {
                return;
            }

            nutXoa.closest('tr').remove();
            if (!thanBang.children.length) {
                themDong();
            }
        });

        nutThem.addEventListener('click', themDong);

        if (!thanBang.children.length) {
            themDong();
        }
    }

    // Kiểm tra số lượng và minh chứng trong form nhập kho.
    function khoiTaoFormNhapKho() {
        let form = document.getElementById('purchase-import-form');
        let tepMinhChung = document.getElementById('defect-evidence');
        let moTaHangLoi = document.getElementById('defect-description');
        let dauBatBuocMoTa = document.getElementById('defect-required-mark');
        let dauBatBuocMinhChung = document.getElementById('defect-evidence-required-mark');
        let nutLuu = document.getElementById('purchase-import-submit');

        if (!form || !tepMinhChung || !moTaHangLoi || !dauBatBuocMoTa || !dauBatBuocMinhChung || !nutLuu) {
            return;
        }

        function capNhatFormNhapKho() {
            let coHangTuChoi = false;
            let duLieuKhongHopLe = false;
            let cacDong = form.querySelectorAll('tbody tr');

            cacDong.forEach(function (dong) {
                let soLuongDat = parseInt(dong.querySelector('.js-ordered').value, 10) || 0;
                let oSoLuongNhan = dong.querySelector('.js-received');
                let oSoLuongTuChoi = dong.querySelector('.js-rejected');
                let soLuongNhan = parseInt(oSoLuongNhan.value, 10) || 0;
                let soLuongTuChoi = parseInt(oSoLuongTuChoi.value, 10) || 0;
                let soLuongNhap = Math.max(0, soLuongNhan - soLuongTuChoi);

                dong.querySelector('.js-accepted').value = soLuongNhap;
                dong.querySelector('.js-manufactured').required = soLuongNhap > 0;
                dong.querySelector('.js-expired').required = soLuongNhap > 0;

                coHangTuChoi = coHangTuChoi || soLuongTuChoi > 0;
                duLieuKhongHopLe = duLieuKhongHopLe || soLuongNhan > soLuongDat || soLuongTuChoi > soLuongNhan;
                oSoLuongNhan.classList.toggle('parsley-error', soLuongNhan > soLuongDat);
                oSoLuongTuChoi.classList.toggle('parsley-error', soLuongTuChoi > soLuongNhan);
            });

            tepMinhChung.required = coHangTuChoi;
            moTaHangLoi.required = coHangTuChoi;
            dauBatBuocMoTa.classList.toggle('d-none', !coHangTuChoi);
            dauBatBuocMinhChung.classList.toggle('d-none', !coHangTuChoi);
            nutLuu.disabled = duLieuKhongHopLe;
        }

        form.addEventListener('input', capNhatFormNhapKho);
        capNhatFormNhapKho();
    }
    khoiTaoFormPhieuGiamGia();
    khoiTaoFormDonDatNhap();
    khoiTaoFormNhapKho();

});
// Ve cac bieu do bang du lieu da duoc TongQuanController chuan bi.
$(document).ready(function () {
    if (typeof Chart === 'undefined' || !document.getElementById('bieu-do-doanh-thu-thang')) {
        return;
    }

    function layDuLieuBieuDo(bieuDo, tenThuocTinh) {
        var duLieu = bieuDo.getAttribute(tenThuocTinh);

        if (!duLieu) {
            return [];
        }

        return JSON.parse(duLieu);
    }

    function dinhDangTienBieuDo(giaTien) {
        return Number(giaTien || 0).toLocaleString('vi-VN') + 'đ';
    }

    function tuyChonTrucTien() {
        return {
            ticks: {
                beginAtZero: true,
                callback: function (giaTri) {
                    return dinhDangTienBieuDo(giaTri);
                }
            }
        };
    }

    function tuyChonTrucSoLuong() {
        return {
            ticks: {
                beginAtZero: true,
                callback: function (giaTri) {
                    if (giaTri % 1 === 0) {
                        return giaTri;
                    }

                    return '';
                }
            }
        };
    }

    var bieuDoDoanhThuThang = document.getElementById('bieu-do-doanh-thu-thang');
    new Chart(bieuDoDoanhThuThang, {
        type: 'bar',
        data: {
            labels: layDuLieuBieuDo(bieuDoDoanhThuThang, 'data-nhan'),
            datasets: [{
                label: 'Doanh thu',
                data: layDuLieuBieuDo(bieuDoDoanhThuThang, 'data-gia-tri'),
                backgroundColor: '#3498db',
                borderColor: '#2471a3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [tuyChonTrucTien()]
            },
            tooltips: {
                callbacks: {
                    label: function (thongTin) {
                        return 'Doanh thu: ' + dinhDangTienBieuDo(thongTin.yLabel);
                    }
                }
            }
        }
    });

    var bieuDoDoanhThuTuan = document.getElementById('bieu-do-doanh-thu-tuan');
    new Chart(bieuDoDoanhThuTuan, {
        type: 'line',
        data: {
            labels: layDuLieuBieuDo(bieuDoDoanhThuTuan, 'data-nhan'),
            datasets: [{
                label: 'Doanh thu',
                data: layDuLieuBieuDo(bieuDoDoanhThuTuan, 'data-gia-tri'),
                backgroundColor: 'rgba(38, 185, 154, 0.18)',
                borderColor: '#26b99a',
                pointBackgroundColor: '#26b99a',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [tuyChonTrucTien()]
            },
            tooltips: {
                callbacks: {
                    label: function (thongTin) {
                        return 'Doanh thu: ' + dinhDangTienBieuDo(thongTin.yLabel);
                    }
                }
            }
        }
    });

    var bieuDoNguoiDung = document.getElementById('bieu-do-nguoi-dung');
    new Chart(bieuDoNguoiDung, {
        type: 'line',
        data: {
            labels: layDuLieuBieuDo(bieuDoNguoiDung, 'data-nhan'),
            datasets: [{
                label: 'Người dùng mới',
                data: layDuLieuBieuDo(bieuDoNguoiDung, 'data-gia-tri'),
                backgroundColor: 'rgba(240, 173, 78, 0.18)',
                borderColor: '#f0ad4e',
                pointBackgroundColor: '#f0ad4e',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [tuyChonTrucSoLuong()]
            }
        }
    });

    var bieuDoSanPhamYeuThich = document.getElementById('bieu-do-san-pham-yeu-thich');

    if (bieuDoSanPhamYeuThich) {
        new Chart(bieuDoSanPhamYeuThich, {
            type: 'horizontalBar',
            data: {
                labels: layDuLieuBieuDo(bieuDoSanPhamYeuThich, 'data-nhan'),
                datasets: [{
                    label: 'Lượt yêu thích',
                    data: layDuLieuBieuDo(bieuDoSanPhamYeuThich, 'data-gia-tri'),
                    backgroundColor: '#e74c3c',
                    borderColor: '#c0392b',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [tuyChonTrucSoLuong()]
                }
            }
        });
    }
});
