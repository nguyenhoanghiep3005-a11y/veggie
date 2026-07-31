<div class="col-md-3 left_col">
  <div class="left_col scroll-view">
    <div class="navbar nav_title">
      <a href="{{ route('admin.tong-quan') }}" class="site_title">
        <i class="fa fa-leaf"></i> <span>HIEP SHOP</span>
      </a>
    </div>

    <div class="clearfix"></div>

    <div class="profile clearfix">
      <div class="profile_pic">
        <img src="{{ asset('assets/clients/img/logohiep.png') }}" alt="Logo" class="img-circle profile_img">
      </div>
      <div class="profile_info">
        <span>Xin chào,</span>
        <h2>{{ $nguoiDungQuanTri->ten }}</h2>
      </div>
    </div>

    <br>

<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
      <div class="menu_section">
        <h3>Tổng quan</h3>

        <ul class="nav side-menu">
          <li>
            <a href="{{ route('admin.tong-quan') }}"><i class="fa fa-home"></i> Tổng quan</a>
          </li>

          @if ($nguoiDungQuanTri->coQuyen('quan_ly_nguoi_dung'))
            <li>
              <a href="{{ route('admin.nguoi-dung.danh-sach') }}"><i class="fa fa-users"></i> Quản lý người dùng</a>
            </li>
          @endif

          @if ($nguoiDungQuanTri->coQuyen('quan_ly_danh_muc'))
            <li>
              <a><i class="fa fa-lock"></i> Quản lý danh mục <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.danh-muc.them') }}">Thêm danh mục</a></li>
                <li><a href="{{ route('admin.danh-muc.danh-sach') }}">Danh sách danh mục</a></li>
              </ul>
            </li>
          @endif

          @if ($nguoiDungQuanTri->coQuyen('quan_ly_san_pham'))
            <li>
              <a><i class="fa fa-desktop"></i> Quản lý sản phẩm <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.san-pham.them') }}">Thêm sản phẩm</a></li>
                <li><a href="{{ route('admin.san-pham.danh-sach') }}">Danh sách sản phẩm</a></li>
              </ul>
            </li>

            <li>
              <a><i class="fa fa-archive"></i> Quản lý kho <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.kho-hang.danh-sach') }}">Danh sách hàng trong kho</a></li>
                <li><a href="{{ route('admin.kho-hang.hang-hu') }}">Hàng hủy</a></li>
              </ul>
            </li>

            <li>
              <a><i class="fa fa-archive"></i> Quản lý nhập hàng <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.don-dat-nhap.danh-sach') }}">Phiếu đặt mua</a></li>
                <li><a href="{{ route('admin.phieu-nhap.danh-sach') }}">Phiếu nhập hàng</a></li>
                <li><a href="{{ route('admin.phieu-hang-hu.danh-sach') }}">Phiếu hủy hư, lỗi</a></li>
              </ul>
            </li>

            <li>
              <a href="{{ route('admin.nha-cung-cap.danh-sach') }}"><i class="fa fa-truck"></i> Quản lý nhà cung cấp</a>
            </li>

            <li>
              <a href="{{ route('admin.phieu-giam-gia.danh-sach') }}"><i class="fa fa-ticket"></i> Quản lý mã giảm giá</a>
            </li>
          @endif

          @if ($nguoiDungQuanTri->coQuyen('quan_ly_don_hang'))
            <li>
              <a href="{{ route('admin.don-hang.danh-sach') }}"><i class="fa fa-edit"></i> Quản lý đơn hàng</a>
            </li>
          @endif
        </ul>
      </div>
    </div>

    <div class="sidebar-footer hidden-small">
      <a data-toggle="tooltip" data-placement="top" title="Đăng xuất" href="{{ route('admin.dang-xuat') }}">
        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
      </a>
    </div>
  </div>
</div>