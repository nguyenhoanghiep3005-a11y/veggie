<!DOCTYPE html>
<html lang="vi">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Đăng nhập Admin</title>

  <link href="{{ asset('assets/admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/admin/vendors/animate.css/animate.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/admin/build/css/custom.min.css') }}" rel="stylesheet">

  <style>
    body.dangNhap {
      min-height: 100vh;
      background: #f3f6f4;
      color: #263238;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: Arial, sans-serif;
    }

    .dangNhap .login_wrapper {
      width: 420px;
      max-width: calc(100% - 30px);
      margin: 0;
    }

    .dangNhap .login_content {
      margin: 0;
      padding: 34px 36px 30px;
      background: #ffffff;
      border: 1px solid #dde5df;
      border-radius: 8px;
      box-shadow: 0 10px 30px rgba(38, 50, 56, 0.12);
      text-align: center;
      text-shadow: none;
    }

    .dangNhap .login_content h1 {
      margin: 0 0 24px;
      color: #263238;
      font-size: 26px;
      font-weight: 600;
      letter-spacing: 0;
      text-shadow: none;
    }

    .dangNhap .login_content h1::before,
    .dangNhap .login_content h1::after {
      display: none;
    }

    .dangNhap .login_content form input[type="text"],
    .dangNhap .login_content form input[type="password"] {
      height: 46px;
      margin: 0 0 16px;
      border: 1px solid #cfd8d2;
      border-radius: 4px;
      box-shadow: none;
      color: #263238;
      font-size: 15px;
    }

    .dangNhap .login_content form input:focus {
      border-color: #59b35d;
      box-shadow: 0 0 0 2px rgba(89, 179, 93, 0.15);
    }

    .dangNhap .login_content .submit {
      width: 100%;
      height: 44px;
      margin: 4px 0 22px;
      background: #59b35d;
      border: 1px solid #59b35d;
      border-radius: 4px;
      color: #ffffff;
      font-size: 15px;
      font-weight: 600;
      text-shadow: none;
    }

    .dangNhap .login_content .submit:hover {
      background: #4a9d4e;
      border-color: #4a9d4e;
      color: #ffffff;
    }

    .dangNhap .separator {
      margin-top: 6px;
      padding-top: 22px;
      border-top: 1px solid #e3ebe5;
    }

    .dangNhap .ten-cua-hang {
      margin: 0;
      color: #4f6457;
      font-size: 22px;
      font-weight: 600;
      text-shadow: none;
    }

    .dangNhap .ten-cua-hang i {
      color: #59b35d;
    }
  </style>
</head>

<body class="dangNhap">
  <div class="login_wrapper">
    <div class="animate form login_form">
      <section class="login_content">
        <form action="{{ route('admin.dang-nhap.xu-ly') }}" method="POST">
          @csrf
          <h1>Đăng nhập</h1>

          <div>
            <input type="text" class="form-control" placeholder="Email" required name="email" value="{{ old('email') }}">
          </div>
          <div>
            <input type="password" class="form-control" placeholder="Mật khẩu" required name="password">
          </div>
          <div>
            <button class="btn submit" type="submit">Đăng nhập</button>
          </div>

          <div class="separator">
            <h2 class="ten-cua-hang"><i class="fa fa-leaf"></i> HIEP SHOP</h2>
          </div>
        </form>
      </section>
    </div>
  </div>
</body>

</html>