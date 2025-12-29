1.Cài đặt
Yêu cầu môi trường
Framework: Laravel 12, PHP >= 8.2
Composer 2.8.12
NodeJS và NPM
XXAMPP hoặc WampServer (nếu sài XXAMPP thì vào xampp\php\php.ini tìm dòng ;extension=zip xóa dấu ; để thành extension=zip) 
cấu hình env và nạp file database veggie.sql vào cơ sở dữ liệu 
tên table là :veggie hoặc tên khác tùy ý (lưu ý:chỉnh sửa DB_DATABASE=??? tên giống với trong file .env)
2.Lệnh chạy chương trình
Mở terminal trong vscode chạy lần lượt lệnh sau:
- composer install
- php artisan migrate
- php artisan storage:link (nếu chạy lệnh này bị lỗi thì dô phần veggie/public/storage xóa thư mục storage chạy lại)
- php artisan serve(lệnh dùng để khởi động server chạy sẽ hiện ví dụ: http://127.0.0.1:8000)
3.tài khoản
Có thể thực hiện đăng ký bằng email cá nhân hoặc có thể đăng nhập bằng tài khoản
Người dùng: nguyenhoanghiep3005@gmail.com
Mật khẩu: 123456
thanh toán paypal dùng tài khoản ảo:
Tài khoản: sb-zeazj47680052@personal.example.com
mật khẩu: Vwr4C<=5
Trang admin thì thêm /admin/login ví dụ http://127.0.0.1:8000/admin/login 
ADMIN: 	admin@example.com
Mật khẩu: 123456
Hosting: https://nongsan.infinityfreeapp.com/
