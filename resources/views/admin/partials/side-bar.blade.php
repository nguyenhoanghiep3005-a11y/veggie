<div class="col-md-3 left_col">
  <div class="left_col scroll-view">
    <div class="navbar nav_title">
      <a href="{{ route('admin.dashboard') }}" class="site_title">
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
        <h2>Admin</h2>
      </div>
    </div>

    <br>

    @php
      $adminUser = Auth::guard('admin')->user();
      $permissions = null;
      $canUser = false;
      $canCategory = false;
      $canProduct = false;
      $canOrder = false;

      if ($adminUser && $adminUser->role) {
        $permissions = $adminUser->role->permissions;
      }

      if ($permissions) {
        $canUser = $permissions->contains('name', 'manage_user');
        $canCategory = $permissions->contains('name', 'manage_categories');
        $canProduct = $permissions->contains('name', 'manage_products');
        $canOrder = $permissions->contains('name', 'manage_order');
      }
    @endphp

    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
      <div class="menu_section">
        <h3>Tổng quan</h3>

        <ul class="nav side-menu">
          <li>
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
          </li>

          @if($canUser)
            <li>
              <a href="{{ route('admin.users.index') }}"><i class="fa fa-users"></i> Quản lý người dùng</a>
            </li>
          @endif

          @if($canCategory)
            <li>
              <a><i class="fa fa-lock"></i> Quản lý danh mục <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.categories.add') }}">Thêm danh mục</a></li>
                <li><a href="{{ route('admin.categories.index') }}">Danh sách danh mục</a></li>
              </ul>
            </li>
          @endif

          @if($canProduct)
            <li>
              <a><i class="fa fa-desktop"></i> Quản lý sản phẩm <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.product.add') }}">Thêm sản phẩm</a></li>
                <li><a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a></li>
              </ul>
            </li>

            <li>
              <a><i class="fa fa-archive"></i> Quản lý kho <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.warehouses.index') }}">Danh sách hàng trong kho</a></li>
                <li><a href="{{ route('admin.warehouses.damages') }}">Hàng hủy</a></li>
              </ul>
            </li>

            <li>
              <a><i class="fa fa-archive"></i> Quản lý nhập hàng <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li><a href="{{ route('admin.purchase-orders.index') }}">Phiếu đặt mua</a></li>
                <li><a href="{{ route('admin.import-receipts.index') }}">Phiếu nhập hàng</a></li>
                <li><a href="{{ route('admin.damage-slips.index') }}">Phiếu hủy hư, lỗi</a></li>
              </ul>
            </li>

            <li>
              <a href="{{ route('admin.suppliers.index') }}"><i class="fa fa-truck"></i> Quản lý nhà cung cấp</a>
            </li>

            <li>
              <a href="{{ route('admin.coupons.index') }}"><i class="fa fa-ticket"></i> Quản lý mã giảm giá</a>
            </li>
          @endif

          @if($canOrder)
            <li>
              <a href="{{ route('admin.orders.index') }}"><i class="fa fa-edit"></i> Quản lý đơn hàng</a>
            </li>
          @endif
        </ul>
      </div>
    </div>

    <div class="sidebar-footer hidden-small">
      <a data-toggle="tooltip" data-placement="top" title="Đăng xuất" href="{{ route('admin.logout') }}">
        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
      </a>
    </div>
  </div>
</div>