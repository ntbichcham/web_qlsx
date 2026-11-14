# Mẫu đặc tả yêu cầu chức năng (FR)

| Mã FR | Tên chức năng | Tác nhân | Tiền điều kiện | Luồng chính | Hậu điều kiện | Ngoại lệ/kiểm tra |
|---|---|---|---|---|---|---|
| FR01 | Đăng nhập | Bệnh nhân/Bác sĩ/Nhân viên/Quản trị viên | Tài khoản tồn tại và đang hoạt động | Nhập tên đăng nhập, mật khẩu; hệ thống xác thực; tạo session; chuyển theo vai trò | Người dùng vào đúng khu vực | Sai thông tin; tài khoản bị khóa |
| FR02 | Đăng ký bệnh nhân | Khách | Chưa đăng nhập | Nhập thông tin; kiểm tra dữ liệu; tạo tài khoản và hồ sơ bệnh nhân | Có tài khoản bệnh nhân | Tên đăng nhập đã tồn tại; mật khẩu không hợp lệ |
| FR08 | Đặt lịch trực tuyến | Bệnh nhân | Đã đăng nhập và có hồ sơ bệnh nhân | Chọn dịch vụ, ca làm việc, ngày/giờ; hệ thống kiểm tra; lưu lịch | Có lịch hẹn với MaNVTao = NULL | Ngoài giờ, giờ nghỉ, trùng lịch |

> Nhóm bổ sung các dòng FR còn lại theo phạm vi trong `PLAN.md`. Mỗi FR cần mô tả đúng chức năng đã thống nhất và vai trò được phép thực hiện.
