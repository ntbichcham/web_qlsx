# Mẫu Test Case

| Test ID | Chức năng | Tiền điều kiện | Dữ liệu kiểm thử | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|---|---|---|---|---|---|---|---|
| TC-AUTH-01 | Đăng nhập thành công | Tài khoản đang hoạt động | Username/password đúng | Mở trang đăng nhập; nhập thông tin; gửi form | Vào dashboard đúng vai trò | | Chưa chạy |
| TC-BOOK-01 | Đặt lịch hợp lệ | Bệnh nhân đăng nhập; ca còn trống | Dịch vụ và giờ phù hợp | Chọn dịch vụ, ca, ngày, giờ; gửi form | Tạo lịch chờ xác nhận; MaNVTao NULL | | Chưa chạy |
| TC-BOOK-02 | Chặn trùng lịch | Đã có lịch cùng bác sĩ và thời gian | Giờ giao nhau lịch hiện hữu | Thử đặt cùng khung giờ | Hệ thống từ chối và không tạo bản ghi | | Chưa chạy |
