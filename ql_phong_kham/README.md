# Hệ thống quản lý phòng khám nha khoa — Đề tài CT285 số 18

Bộ mã nguồn khởi đầu theo `PLAN.md`: PHP thuần, MVC, XAMPP/Apache, MariaDB, PDO và Bootstrap 5. Dự án có 13 bảng dữ liệu và 4 vai trò: `BENH_NHAN`, `BAC_SI`, `NHAN_VIEN`, `QUAN_TRI_VIEN`.

> Đây là bản MVP khởi đầu để nhóm tiếp tục phát triển. Các luồng đăng nhập, đặt lịch, quản lý dịch vụ/lịch làm việc, tiếp nhận, ghi nhận lần khám và đơn thuốc đã được dựng. Cần tiếp tục kiểm thử trên phiên bản MariaDB/XAMPP thực tế, bổ sung đặc tả, giao diện và hoàn thiện nghiệp vụ trước khi nộp.

## 1. Yêu cầu môi trường

- XAMPP có Apache, PHP 8.x và MariaDB.
- PHP extensions: `pdo`, `pdo_mysql`, `mbstring`.
- Visual Studio Code hoặc trình soạn thảo tương đương.
- Trình duyệt web.

## 2. Cài đặt vào XAMPP

1. Giải nén thư mục `ql_phong_kham` vào `D:\xamp\htdocs\`.
2. Mở XAMPP Control Panel, nhấn **Start** cho Apache và MySQL.
3. Mở phpMyAdmin: `http://localhost/phpmyadmin`.
4. Có hai trường hợp tạo/cập nhật database:

### Trường hợp A — Database cũ đã có 12 bảng

- Chọn database `qlphongkhamnhakhoa` trong phpMyAdmin.
- Chọn tab **SQL**.
- Mở file `database/migrate_12_to_13.sql`, sao chép toàn bộ và chạy.
- Migration sẽ cho `BENH_NHAN.MaND` nhận NULL, thêm bảng `NHAN_VIEN`, thêm `LICH_HEN_KHAM.MaNVTao` và cập nhật vai trò nhân viên.
- Không chạy `schema.sql` đè lên database đã có dữ liệu.

### Trường hợp B — Chưa có database

- Trong phpMyAdmin, chọn tab **Import** hoặc tab SQL.
- Import/chạy file `database/schema.sql`.

5. Mở `config/database.php` và kiểm tra:
   - host: `127.0.0.1`
   - port: `3306`
   - database: `qlphongkhamnhakhoa`
   - username: `root` (mặc định XAMPP)
   - password: `''` nếu MariaDB không đặt mật khẩu.
6. (Tùy chọn) Import `database/sample_services.sql` để thêm dịch vụ mẫu. Chỉ chạy một lần để tránh dịch vụ trùng.
7. Tạo tài khoản quản trị đầu tiên bằng PowerShell/CMD:

```powershell
cd D:\xamp\htdocs\ql_phong_kham
D:\xamp\php\php.exe .\scripts\create_admin.php
```

Nhập tên đăng nhập và mật khẩu theo lời nhắc. Mật khẩu được lưu bằng `password_hash()`.

8. (Tùy chọn) Tạo nhanh tài khoản mẫu cho bác sĩ, nhân viên và quản trị viên:

```powershell
D:\xamp\php\php.exe .\scripts\seed_internal_accounts.php
```

Script bỏ qua tên đăng nhập đã tồn tại nên có thể chạy lại an toàn. Tài khoản mẫu:

| Tên đăng nhập | Mật khẩu | Vai trò |
|---|---|---|
| `bs01` | `12345678` | Bác sĩ |
| `nv01` | `12345678` | Nhân viên |
| `admin01` | `12345678` | Quản trị viên |

9. Kiểm tra kết nối:

```powershell
D:\xamp\php\php.exe .\scripts\check_environment.php
```

10. Mở ứng dụng:

`http://localhost/ql_phong_kham/public/`

Nếu bạn đổi tên thư mục dự án, ứng dụng tự xác định URL theo đường dẫn hiện tại.

## 3. Khởi tạo dữ liệu để thử nghiệm

1. Đăng nhập bằng tài khoản quản trị vừa tạo.
2. Vào **Tài khoản** để tạo tài khoản bác sĩ, nhân viên hoặc quản trị viên. Tài khoản bác sĩ/nhân viên được tạo kèm hồ sơ tương ứng; tài khoản quản trị viên chỉ cần tên đăng nhập và mật khẩu.
3. Vào **Dịch vụ** để thêm dịch vụ (hoặc import dữ liệu mẫu).
4. Vào **Lịch làm việc** để tạo ca cho bác sĩ hoặc để bác sĩ tự đăng ký ca trong mục **Lịch làm việc** của mình.
5. Mở cửa sổ ẩn danh/ trình duyệt khác, đăng ký tài khoản bệnh nhân.
6. Đăng nhập bệnh nhân và thử đặt lịch.
7. Nhân viên có thể tạo hồ sơ bệnh nhân chưa có tài khoản trong **Tiếp nhận bệnh nhân**, sau đó đặt lịch tại quầy.
8. Bác sĩ đăng nhập để xem lịch được phân công, tiếp nhận lịch, ghi nhận kết quả, dịch vụ đã thực hiện và đơn thuốc.

## 4. Chức năng hiện có trong bản khởi đầu

### Chung
- Trang giới thiệu và danh sách dịch vụ đang áp dụng.
- Đăng ký bệnh nhân, đăng nhập, đăng xuất.
- Session và kiểm tra vai trò phía máy chủ.
- CSRF token cho các biểu mẫu POST.
- Kết nối MariaDB qua PDO và prepared statements.

### Bệnh nhân
- Dashboard.
- Cập nhật hồ sơ cá nhân.
- Xem dịch vụ.
- Đặt lịch trực tuyến; `MaNVTao` để NULL.
- Xem lịch hẹn và yêu cầu hủy lịch tương lai.
- Xem lịch sử khám và thông tin đơn thuốc của chính mình.

### Nhân viên
- Dashboard tiếp nhận.
- Tạo hồ sơ bệnh nhân chưa có tài khoản (`MaND = NULL`).
- Tìm kiếm danh sách bệnh nhân.
- Đặt lịch thay bệnh nhân; hệ thống ghi nhận `MaNVTao`.
- Xem và cập nhật trạng thái lịch hẹn.

### Bác sĩ
- Dashboard và lịch hẹn thuộc ca làm việc được phân công.
- Tự đăng ký ca làm việc cho chính mình; ca có hiệu lực ngay và có thể ngừng ca chưa diễn ra, chưa có lịch hẹn.
- Xem lịch sử ca làm việc và số lịch hẹn trong từng ca.
- Tiếp nhận lịch hẹn để tạo hồ sơ bệnh án/đợt điều trị/lần khám nếu chưa có lần khám.
- Cập nhật thông tin lần khám.
- Ghi nhận dịch vụ thực hiện.
- Tạo đơn thuốc và thêm nhiều chi tiết thuốc.
- Xem danh sách đợt điều trị do mình phụ trách.

### Quản trị viên
- Dashboard tổng quan và số liệu cơ bản.
- Tạo tài khoản bác sĩ/nhân viên kèm hồ sơ và tài khoản quản trị viên.
- Khóa/mở tài khoản.
- Thêm/ngừng áp dụng dịch vụ.
- Tạo lịch làm việc cho bác sĩ và bật/tắt các ca do bác sĩ tự đăng ký.
- Theo dõi lịch hẹn toàn phòng khám (không có màn hình truy cập bệnh án/đơn thuốc).

## 5. Cấu trúc thư mục

```text
ql_phong_kham/
├── app/
│   ├── Controllers/       # Điều phối yêu cầu và nghiệp vụ
│   ├── Core/              # Database, Auth, CSRF, Controller, Model, helpers
│   ├── Models/            # Truy vấn và thao tác dữ liệu
│   └── Views/             # Giao diện theo vai trò
├── config/                # Cấu hình ứng dụng và MariaDB
├── database/              # Schema mới, migration 12->13, dữ liệu dịch vụ mẫu
├── docs/                  # Mẫu FR, Use Case, test cases
├── public/                # Điểm vào web và tài nguyên tĩnh
├── scripts/               # Tạo admin, kiểm tra môi trường
├── storage/logs/          # Nhật ký lỗi ứng dụng
├── bootstrap.php
├── PLAN.md
└── README.md
```

## 6. Những việc cần hoàn thiện tiếp

- Hoàn thiện liên kết tài khoản mới với hồ sơ bệnh nhân có sẵn sau bước xác minh danh tính; bản khởi đầu chưa tự động ghép hồ sơ trùng.
- Bổ sung CRUD đầy đủ (sửa/xóa mềm/chi tiết) cho bác sĩ, nhân viên, bệnh nhân và lịch làm việc.
- Hoàn thiện bộ lọc ngày/trạng thái, báo cáo nâng cao và giao diện responsive theo Figma.
- Hoàn thiện quy trình tạo đợt điều trị độc lập và khám không có lịch hẹn nếu nhóm cần hỗ trợ đầy đủ.
- Xác minh bác sĩ kê đơn trùng với bác sĩ được gán cho lần khám theo quy tắc nghiệp vụ của phòng khám.
- Bổ sung kiểm thử cho trường hợp nhiều lịch làm việc trong cùng ngày, đồng thời kiểm tra xung đột giờ dưới tải đồng thời.
- Thêm quản lý log/audit cho truy cập và thay đổi hồ sơ bệnh án.
- Không dùng dữ liệu bệnh nhân thật trong môi trường học tập/demo; chỉ dùng dữ liệu giả lập.

## 7. Lưu ý kỹ thuật và bảo mật

- Không đưa mật khẩu database hoặc tài khoản thật lên GitHub công khai.
- Dùng `password_hash()`/`password_verify()`, không lưu mật khẩu thuần.
- Không chỉ ẩn menu để phân quyền; mọi route/controller đã kiểm tra role nhưng nhóm cần tiếp tục rà soát mọi chức năng mới.
- Nhân viên không được truy cập nội dung bệnh án/đơn thuốc; quản trị viên không mặc nhiên có quyền xem nội dung chuyên môn.
- Hãy sao lưu database trước khi chạy migration trên dữ liệu đang sử dụng.
- Hình ảnh minh họa trên trang chủ/trang dịch vụ dùng giấy phép Creative Commons; xem thông tin nguồn tại `public/assets/images/credits.html`.
- Bootstrap được tải từ CDN, vì vậy trình duyệt cần Internet để tải CSS/JS framework; các trang vẫn có CSS cơ bản trong `public/assets/css/app.css`.
