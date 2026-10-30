# StockPro — PHP + MySQL Inventory & Sales

Dự án portfolio quản lý kho và bán hàng, chạy local trên XAMPP.

## Yêu cầu
- XAMPP có PHP 8.1+ và MySQL/MariaDB
- Extension `pdo_mysql` bật trong PHP
- Trình duyệt hiện đại; Bootstrap tải từ CDN nên cần Internet để có giao diện đầy đủ

## Cài đặt trên Windows
1. Giải nén thư mục `php_inventory_sales` vào `C:\xampp\htdocs\`.
2. Mở XAMPP Control Panel, bấm **Start** cho Apache và MySQL.
3. Truy cập `http://localhost/phpmyadmin`.
4. Chọn **Import** → chọn `database/stockpro_db.sql` → **Go**. File SQL tạo database `stockpro_db` và dữ liệu demo.
5. Mở `config/config.php`; kiểm tra `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`. XAMPP thường dùng `root` và mật khẩu trống.
6. Mở `http://localhost/php_inventory_sales/public/login.php`.
7. Đăng nhập:
   - Admin: `admin` / `password`
   - Nhân viên: `staff` / `password`

> Lưu ý: SQL demo chứa hash mật khẩu mẫu. Nếu đăng nhập không hoạt động trên môi trường PHP của bạn, tạo hash mới bằng lệnh `php -r "echo password_hash('password', PASSWORD_DEFAULT), PHP_EOL;"`, rồi cập nhật `users.password_hash` trong phpMyAdmin.

## Chức năng
- Đăng nhập/đăng xuất, session regeneration, phân quyền Admin/Staff.
- Dashboard: số sản phẩm, giá trị tồn kho, đơn hôm nay, doanh thu tháng, cảnh báo tồn thấp.
- CRUD cơ bản sản phẩm (ngừng kinh doanh thay vì xóa vật lý), tìm kiếm SKU/tên.
- Danh mục, nhà cung cấp.
- Phiếu nhập: tạo phiếu, cập nhật giá vốn và tồn kho trong transaction, ghi stock movement.
- Đơn hàng: kiểm tra tồn kho, khóa hàng bằng `SELECT ... FOR UPDATE`, lưu đơn/chi tiết, trừ kho và ghi lịch sử trong transaction.
- Báo cáo theo ngày, top sản phẩm và xuất CSV.
- Quản lý tài khoản (Admin).
- PDO prepared statements, escaping đầu ra, CSRF token cho biểu mẫu POST.

## Cấu trúc
```
config/config.php          # cấu hình DB và PDO singleton
app/helpers.php             # escape, CSRF, login, role, flash, format tiền
app/layout/                 # header/footer
public/                     # entry pages, assets
database/stockpro_db.sql    # schema + dữ liệu demo
docs/                       # tài liệu thuyết trình và câu hỏi phỏng vấn
```

## Kiểm tra demo đề xuất
1. Đăng nhập admin.
2. Sản phẩm → thêm SKU `DEMO-01`.
3. Nhập kho → chọn nhà cung cấp/sản phẩm, nhập số lượng 10.
4. Đơn hàng → tạo đơn bán số lượng 2.
5. Sản phẩm → xác minh tồn kho giảm 2.
6. Báo cáo → chọn khoảng ngày có đơn, thử xuất CSV.
7. Đăng xuất, đăng nhập bằng staff; kiểm tra mục Tài khoản không hiển thị và URL `users.php` bị từ chối.

## Lưu ý trước khi dùng thật
Đây là dự án học tập, không phải phần mềm sẵn sàng cho production. Cần bổ sung kiểm thử tự động, pagination, logging, xử lý lỗi không lộ chi tiết, chính sách mật khẩu, rate limiting, backup, quyền MySQL tối thiểu, cấu hình secrets ngoài source, và kiểm tra nghiệp vụ hoàn/hủy đơn. Đặt `APP_DEBUG` false trước khi triển khai. Cập nhật `BASE_URL` nếu thư mục/đường dẫn khác.
