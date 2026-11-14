# PLAN.md — Đề tài số 18: Hệ thống quản lý lịch hẹn và hoạt động khám chữa bệnh tại phòng khám nha khoa

> **Trạng thái:** Bản kế hoạch đã chốt các quyết định kiến trúc chính  
> **Phiên bản:** 1.0  
> **Phạm vi:** Đồ án CT285 — mở rộng từ Appointment Management thành hệ thống quản lý lịch hẹn và các nghiệp vụ khám chữa bệnh liên quan.

---

## 1. Tổng quan dự án

### 1.1. Mục tiêu

Xây dựng hệ thống web hỗ trợ bệnh nhân đăng ký tài khoản và đặt lịch khám trực tuyến; hỗ trợ nhân viên tiếp nhận bệnh nhân và tạo lịch hẹn tại phòng khám; hỗ trợ bác sĩ quản lý hồ sơ bệnh án, đợt điều trị, lần khám, dịch vụ đã thực hiện và đơn thuốc. Quản trị viên quản lý tài khoản, người dùng, danh mục dịch vụ và cấu hình hoạt động chung của hệ thống.

### 1.2. Phạm vi đã chốt

- Đối tượng sử dụng gồm **Bệnh nhân, Bác sĩ, Nhân viên và Quản trị viên**.
- Bệnh nhân có thể tự đăng ký tài khoản và tự đặt lịch trực tuyến.
- Nhân viên có thể tạo hồ sơ bệnh nhân chưa có tài khoản và đặt lịch thay bệnh nhân tại phòng khám hoặc qua điện thoại.
- Hồ sơ bệnh nhân có thể tồn tại khi chưa có tài khoản đăng nhập. Khi bệnh nhân đăng ký sau đó, tài khoản được liên kết với hồ sơ hiện có sau khi xác minh, tránh tạo hồ sơ trùng.
- Phạm vi mở rộng giữ các nghiệp vụ hồ sơ bệnh án, đợt điều trị, lần khám, dịch vụ lần khám, đơn thuốc và chi tiết đơn thuốc.
- CDM mở rộng hiện có **13 thực thể**; Database Schema triển khai có **13 bảng**.
- Không đưa thanh toán, hóa đơn, quản lý kho thuốc hoặc quản lý vật tư vào MVP vì các nghiệp vụ này chưa được chốt trong CDM và phạm vi hiện tại.

### 1.3. Vai trò và quyền truy cập

| Vai trò | Quyền chính | Giới hạn quan trọng |
|---|---|---|
| `BENH_NHAN` | Đăng ký, đăng nhập, cập nhật hồ sơ cá nhân, xem dịch vụ, đặt lịch, xem lịch hẹn và hồ sơ của chính mình | Không xem hoặc sửa dữ liệu của bệnh nhân khác; không sửa nội dung chuyên môn do bác sĩ ghi nhận |
| `BAC_SI` | Xem lịch được phân công; xem hồ sơ bệnh nhân thuộc phạm vi được phân công; quản lý đợt điều trị, lần khám, dịch vụ thực hiện và đơn thuốc | Chỉ truy cập hồ sơ phục vụ công việc khám/điều trị được phân công |
| `NHAN_VIEN` | Tiếp nhận bệnh nhân, tạo/cập nhật thông tin hành chính, hỗ trợ đặt lịch, cập nhật trạng thái lịch hẹn | Chỉ xem thông tin cần thiết cho tiếp nhận và đặt lịch; không xem nội dung bệnh án và đơn thuốc |
| `QUAN_TRI_VIEN` | Quản lý tài khoản, vai trò, bác sĩ, nhân viên, dịch vụ, lịch làm việc và cấu hình hệ thống | Không mặc nhiên có quyền xem nội dung chuyên môn của bệnh án hoặc đơn thuốc |

**Nguyên tắc:** Phân quyền phải được kiểm tra ở phía máy chủ trong mọi yêu cầu, không chỉ ẩn nút trên giao diện.

---

## 2. Danh sách trang và tính năng cốt lõi (MVP)

Các trang dưới đây là nhóm trang logic. Khi triển khai giao diện, có thể gộp các trang cùng nghiệp vụ thành một trang có tab hoặc khu vực nội dung riêng.

### 2.1. Trang dùng chung và xác thực

| Mã | Trang | Tính năng MVP |
|---|---|---|
| C01 | Trang giới thiệu / trang chủ | Giới thiệu phòng khám; hiển thị dịch vụ; điều hướng đăng nhập, đăng ký và đặt lịch |
| C02 | Đăng ký bệnh nhân | Tạo tài khoản bệnh nhân và hồ sơ bệnh nhân; kiểm tra tên đăng nhập; lưu mật khẩu đã băm |
| C03 | Đăng nhập | Xác thực tài khoản; chuyển đến khu vực theo vai trò |
| C04 | Đăng xuất | Hủy session và kết thúc phiên đăng nhập |
| C05 | Hồ sơ cá nhân | Cập nhật thông tin tài khoản/hồ sơ cá nhân theo quyền |

### 2.2. Khu vực bệnh nhân

| Mã | Trang | Tính năng MVP |
|---|---|---|
| P01 | Dashboard bệnh nhân | Tóm tắt lịch hẹn sắp tới và trạng thái lịch hẹn |
| P02 | Danh sách dịch vụ | Xem tên, mô tả, giá, thời lượng và trạng thái dịch vụ |
| P03 | Đặt lịch khám | Chọn dịch vụ, ngày, ca làm việc/khung giờ phù hợp; gửi yêu cầu đặt lịch |
| P04 | Lịch hẹn của tôi | Xem danh sách và chi tiết lịch hẹn; gửi yêu cầu hủy theo quy tắc phòng khám |
| P05 | Lịch sử khám và hồ sơ của tôi | Chỉ xem hồ sơ, lần khám, kết quả và đơn thuốc thuộc chính bệnh nhân đó, theo chính sách phòng khám |

### 2.3. Khu vực nhân viên

| Mã | Trang | Tính năng MVP |
|---|---|---|
| S01 | Dashboard tiếp nhận | Tóm tắt lịch hẹn trong ngày và công việc tiếp nhận |
| S02 | Quản lý hồ sơ bệnh nhân | Tìm kiếm, tạo và cập nhật hồ sơ bệnh nhân; cho phép hồ sơ chưa có tài khoản (`MaND = NULL`) |
| S03 | Đặt lịch tại quầy | Tạo lịch hẹn thay bệnh nhân; ghi nhận nhân viên tạo lịch bằng `MaNVTao` |
| S04 | Quản lý lịch hẹn | Xem, xác nhận, cập nhật hoặc hủy lịch theo quyền được cấp |

### 2.4. Khu vực bác sĩ

| Mã | Trang | Tính năng MVP |
|---|---|---|
| D01 | Dashboard bác sĩ | Lịch khám trong ngày và danh sách lịch được phân công |
| D02 | Lịch khám được phân công | Xem lịch hẹn/ca làm việc và thông tin tiếp nhận cần thiết |
| D03 | Hồ sơ bệnh án | Xem và cập nhật tiền sử bệnh, dị ứng, ghi chú chuyên môn trong phạm vi được phân công |
| D04 | Quản lý đợt điều trị | Tạo, theo dõi và cập nhật chẩn đoán, kế hoạch, trạng thái và kết quả tổng kết |
| D05 | Ghi nhận lần khám | Ghi nhận thời gian, lý do khám, triệu chứng, chẩn đoán, kết quả và hướng xử lý |
| D06 | Dịch vụ lần khám | Ghi nhận các dịch vụ thực tế đã thực hiện, số lượng, đơn giá áp dụng và thời gian thực hiện |
| D07 | Đơn thuốc | Tạo đơn thuốc cho lần khám và thêm/sửa chi tiết thuốc theo quyền |

### 2.5. Khu vực quản trị viên

| Mã | Trang | Tính năng MVP |
|---|---|---|
| A01 | Dashboard quản trị | Thống kê nhanh bệnh nhân, bác sĩ, nhân viên và lịch hẹn |
| A02 | Quản lý tài khoản | Tạo/khoá/mở tài khoản; gán vai trò; không xem mật khẩu gốc |
| A03 | Quản lý bác sĩ | Tạo và cập nhật hồ sơ bác sĩ, chuyên môn, trạng thái và tài khoản liên kết |
| A04 | Quản lý nhân viên | Tạo và cập nhật hồ sơ nhân viên, chức vụ, trạng thái và tài khoản liên kết |
| A05 | Quản lý dịch vụ | Thêm, sửa, ngừng áp dụng dịch vụ; quản lý thời lượng và giá hiện hành |
| A06 | Quản lý lịch làm việc | Thiết lập ngày, giờ làm, giờ nghỉ và trạng thái lịch của bác sĩ |
| A07 | Theo dõi lịch hẹn | Tra cứu và giám sát lịch hẹn toàn phòng khám trong phạm vi quyền quản trị |
| A08 | Thống kê và báo cáo | Lọc theo khoảng ngày, bác sĩ, dịch vụ và trạng thái; thống kê số lượng lịch hẹn và lượt khám |

### 2.6. Chức năng ngoài MVP (để phát triển sau)

- Gửi email/SMS xác nhận và nhắc lịch tự động.
- Thanh toán trực tuyến, hóa đơn và quản lý công nợ.
- Quản lý kho thuốc, nhập/xuất thuốc và vật tư nha khoa.
- Xuất báo cáo PDF/Excel nâng cao.
- Đặt lịch nhiều dịch vụ trong một lần đặt lịch nếu nghiệp vụ cần hỗ trợ.
- Ứng dụng di động hoặc tích hợp cổng dịch vụ bên ngoài.

---

## 3. Công nghệ sử dụng

| Thành phần | Công nghệ đã chốt | Mục đích |
|---|---|---|
| Môi trường phát triển | XAMPP | Môi trường chạy ứng dụng trên máy cá nhân |
| Web server | Apache | Tiếp nhận và phục vụ yêu cầu HTTP |
| Backend | PHP thuần | Xử lý nghiệp vụ phía máy chủ; không dùng Laravel ở phiên bản MVP |
| Kiến trúc mã nguồn | MVC | Tách Model, View và Controller |
| Cơ sở dữ liệu | MariaDB | Lưu dữ liệu nghiệp vụ |
| Quản trị cơ sở dữ liệu | phpMyAdmin | Tạo, xem và quản lý database/bảng |
| Kết nối dữ liệu | PDO | Truy vấn MariaDB có tham số, hạn chế SQL injection |
| Frontend | HTML5, CSS3, JavaScript | Xây dựng giao diện và tương tác |
| UI framework | Bootstrap 5 | Bố cục responsive và thành phần giao diện |
| IDE | Visual Studio Code | Soạn thảo và quản lý mã nguồn |
| Thiết kế giao diện | Figma | Thiết kế/wireframe trước khi lập trình |

**Phiên bản PHP:** Dùng phiên bản PHP đi kèm bản XAMPP đang cài; kiểm tra bằng `php -v` hoặc trang `phpinfo()` trước khi triển khai. Chưa cố định số phiên bản trong kế hoạch để tránh ghi sai môi trường thực tế.

### 3.1. Kiến trúc ứng dụng

Dự án dùng kiến trúc 3 lớp về mặt logic và MVC để tổ chức mã nguồn:

1. **Presentation:** View PHP/HTML, Bootstrap, CSS và JavaScript; hiển thị trang theo vai trò.
2. **Business Logic:** Controller và các lớp Service; xử lý đăng nhập, phân quyền, kiểm tra lịch trống, quy tắc đặt lịch, điều trị và kê đơn.
3. **Data Access:** Model/Repository dùng PDO để truy vấn MariaDB.

MVC là cách tổ chức mã nguồn; kiến trúc 3 lớp là cách phân chia trách nhiệm của ứng dụng. Hai khái niệm được dùng phối hợp, không phải hai công nghệ khác nhau.

### 3.2. Cấu trúc thư mục dự kiến

```text
htdocs/
└── ql_phong_kham/
    ├── app/
    │   ├── Controllers/
    │   ├── Models/
    │   ├── Views/
    │   │   ├── auth/
    │   │   ├── patient/
    │   │   ├── staff/
    │   │   ├── doctor/
    │   │   └── admin/
    │   ├── Services/
    │   └── Middleware/
    ├── config/
    │   └── database.php
    ├── public/
    │   ├── assets/
    │   │   ├── css/
    │   │   ├── js/
    │   │   └── images/
    │   └── index.php
    ├── routes/
    │   └── web.php
    ├── storage/
    │   └── logs/
    └── README.md
```

---

## 4. Database Schema — 13 bảng

> Quy ước: tên bảng/thuộc tính giữ theo CDM hiện tại. `PK` là khóa chính, `FK` là khóa ngoại, `UNIQUE` là duy nhất. Khi triển khai trong MariaDB, mã định danh dùng `INT AUTO_INCREMENT`; chuỗi tiếng Việt dùng `VARCHAR`/`TEXT` với `utf8mb4`; ngày và giờ dùng `DATE`, `TIME`, `DATETIME`; giá tiền dùng `DECIMAL(10,2)`.

### 4.1. Danh sách bảng và thuộc tính

| # | Bảng | Thuộc tính và ràng buộc chính |
|---:|---|---|
| 1 | `TAI_KHOAN_NGUOI_DUNG` | `MaND` INT PK AI; `TenDN` VARCHAR(50) UNIQUE NOT NULL; `MatKhau` VARCHAR(255) NOT NULL (lưu password hash); `VaiTro` VARCHAR(20) NOT NULL; `TrangThai` VARCHAR(20) NOT NULL; `NgayTao` DATETIME NOT NULL |
| 2 | `BENH_NHAN` | `MaBN` INT PK AI; `MaND` INT NULL UNIQUE FK → `TAI_KHOAN_NGUOI_DUNG.MaND`; `HoTen` VARCHAR(100) NOT NULL; `SDT` VARCHAR(20); `Email` VARCHAR(100); `DiaChi` TEXT; `GioiTinh` VARCHAR(10); `NgaySinh` DATE |
| 3 | `BAC_SI` | `MaBS` INT PK AI; `MaND` INT NOT NULL UNIQUE FK → `TAI_KHOAN_NGUOI_DUNG.MaND`; `HoTen` VARCHAR(100) NOT NULL; `SDT` VARCHAR(20); `Email` VARCHAR(100); `ChuyenMon` VARCHAR(100); `GioiThieu` TEXT; `TrangThai` VARCHAR(20) |
| 4 | `NHAN_VIEN` | `MaNV` INT PK AI; `MaND` INT NOT NULL UNIQUE FK → `TAI_KHOAN_NGUOI_DUNG.MaND`; `HoTen` VARCHAR(100) NOT NULL; `SDT` VARCHAR(20); `Email` VARCHAR(100); `ChucVu` VARCHAR(100); `TrangThai` VARCHAR(20) |
| 5 | `DICH_VU` | `MaDV` INT PK AI; `TenDV` VARCHAR(100) NOT NULL; `MoTa` TEXT; `ThoiLuong` INT; `GiaDV` DECIMAL(10,2); `TrangThai` VARCHAR(20) |
| 6 | `LICH_LAM_VIEC` | `MaLLV` INT PK AI; `MaBS` INT NOT NULL FK → `BAC_SI.MaBS`; `NgayLamViec` DATE; `GioBatDau` TIME; `GioKetThuc` TIME; `BatDauNghi` TIME NULL; `KetThucNghi` TIME NULL; `TrangThai` VARCHAR(20) |
| 7 | `LICH_HEN_KHAM` | `MaLH` INT PK AI; `MaBN` INT NOT NULL FK → `BENH_NHAN.MaBN`; `MaLLV` INT NOT NULL FK → `LICH_LAM_VIEC.MaLLV`; `MaDV` INT NOT NULL FK → `DICH_VU.MaDV`; `MaNVTao` INT NULL FK → `NHAN_VIEN.MaNV`; `NgayKham` DATE; `GioBatDau` TIME; `GioKetThuc` TIME; `TrangThai` VARCHAR(20); `GhiChu` TEXT; `NgayTao` DATETIME |
| 8 | `HO_SO_BENH_AN` | `MaHSBA` INT PK AI; `MaBN` INT NOT NULL UNIQUE FK → `BENH_NHAN.MaBN`; `TienSuBenh` TEXT; `DiUng` TEXT; `GhiChu` TEXT; `NgayLap` DATETIME; `TrangThai` VARCHAR(20) |
| 9 | `DOT_DIEU_TRI` | `MaDT` INT PK AI; `MaHSBA` INT NOT NULL FK → `HO_SO_BENH_AN.MaHSBA`; `MaBS` INT NOT NULL FK → `BAC_SI.MaBS`; `LyDoDieuTri` VARCHAR(300); `ChuanDoan` TEXT; `KeHoachDieuTri` TEXT; `NgayBatDau` DATE; `NgayKetThuc` DATE NULL; `TrangThai` VARCHAR(20); `KetQuaTongKet` TEXT |
| 10 | `LAN_KHAM` | `MaLKham` INT PK AI; `MaDT` INT NOT NULL FK → `DOT_DIEU_TRI.MaDT`; `MaBS` INT NOT NULL FK → `BAC_SI.MaBS`; `MaLH` INT NULL UNIQUE FK → `LICH_HEN_KHAM.MaLH`; `NgayGioKham` DATETIME; `LyDoKham` VARCHAR(300); `TrieuChung` TEXT; `ChanDoan` TEXT; `KetQuaKham` TEXT; `HuongXuLy` TEXT; `GhiChu` TEXT; `TrangThaiLanKham` VARCHAR(20); `NgayTao` DATETIME |
| 11 | `DICH_VU_LAN_KHAM` | `MaDVLK` INT PK AI; `MaLKham` INT NOT NULL FK → `LAN_KHAM.MaLKham`; `MaDV` INT NOT NULL FK → `DICH_VU.MaDV`; `SoLuong` INT; `DonGiaApDung` DECIMAL(10,2); `ThoiGianThucHien` DATETIME NULL; `GhiChu` TEXT |
| 12 | `DON_THUOC` | `MaDonThuoc` INT PK AI; `MaLKham` INT NOT NULL UNIQUE FK → `LAN_KHAM.MaLKham`; `MaBS` INT NOT NULL FK → `BAC_SI.MaBS`; `NgayKeDon` DATE; `LoiDan` TEXT; `GhiChu` TEXT |
| 13 | `CHI_TIET_DON_THUOC` | `MaCTDT` INT PK AI; `MaDonThuoc` INT NOT NULL FK → `DON_THUOC.MaDonThuoc`; `TenThuoc` VARCHAR(200) NOT NULL; `SoLuong` DECIMAL(10,2); `LieuLuong` VARCHAR(100); `CachDung` VARCHAR(255); `ThoiGianBatDau` DATE NULL; `ThoiGianKetThuc` DATE NULL |

### 4.2. Quan hệ giữa các bảng

| Bảng cha | Bảng con | Kiểu quan hệ | Ghi chú |
|---|---|---|---|
| `TAI_KHOAN_NGUOI_DUNG` | `BENH_NHAN` | 1 – 0..1 | `BENH_NHAN.MaND` nullable, unique |
| `TAI_KHOAN_NGUOI_DUNG` | `BAC_SI` | 1 – 0..1 | `BAC_SI.MaND` not null, unique |
| `TAI_KHOAN_NGUOI_DUNG` | `NHAN_VIEN` | 1 – 0..1 | `NHAN_VIEN.MaND` not null, unique |
| `BENH_NHAN` | `HO_SO_BENH_AN` | 1 – 0..1 | Một hồ sơ tổng hợp cho một bệnh nhân trong thiết kế hiện tại |
| `HO_SO_BENH_AN` | `DOT_DIEU_TRI` | 1 – N | Một hồ sơ có thể có nhiều đợt điều trị |
| `BAC_SI` | `DOT_DIEU_TRI` | 1 – N | Bác sĩ phụ trách đợt điều trị |
| `DOT_DIEU_TRI` | `LAN_KHAM` | 1 – N | Lần khám thuộc một đợt điều trị |
| `BAC_SI` | `LAN_KHAM` | 1 – N | Bác sĩ thực hiện lần khám |
| `BAC_SI` | `LICH_LAM_VIEC` | 1 – N | Một bác sĩ có nhiều lịch làm việc |
| `BENH_NHAN` | `LICH_HEN_KHAM` | 1 – N | Bệnh nhân đặt nhiều lịch hẹn |
| `LICH_LAM_VIEC` | `LICH_HEN_KHAM` | 1 – N | Lịch hẹn được xếp vào một lịch làm việc |
| `NHAN_VIEN` | `LICH_HEN_KHAM` | 1 – N tùy chọn ở phía lịch hẹn | `MaNVTao` nullable khi bệnh nhân tự đặt trực tuyến |
| `DICH_VU` | `LICH_HEN_KHAM` | 1 – N | Mỗi lịch hẹn chọn một dịch vụ chính |
| `LICH_HEN_KHAM` | `LAN_KHAM` | 1 – 0..1 | `LAN_KHAM.MaLH` nullable, unique; cho phép khám trực tiếp không đặt lịch |
| `LAN_KHAM` | `DICH_VU_LAN_KHAM` | 1 – N | Bảng liên kết ghi nhận dịch vụ thực tế |
| `DICH_VU` | `DICH_VU_LAN_KHAM` | 1 – N | Một dịch vụ xuất hiện ở nhiều lần khám |
| `LAN_KHAM` | `DON_THUOC` | 1 – 0..1 | `DON_THUOC.MaLKham` unique |
| `BAC_SI` | `DON_THUOC` | 1 – N | Ghi nhận bác sĩ kê đơn |
| `DON_THUOC` | `CHI_TIET_DON_THUOC` | 1 – N | Một đơn thuốc có một hoặc nhiều dòng thuốc |

### 4.3. Quy tắc dữ liệu và nghiệp vụ quan trọng

1. `TAI_KHOAN_NGUOI_DUNG.VaiTro` chỉ nhận một trong bốn giá trị: `BENH_NHAN`, `BAC_SI`, `NHAN_VIEN`, `QUAN_TRI_VIEN`.
2. `BENH_NHAN.MaND` được phép `NULL` để nhân viên có thể tạo hồ sơ cho bệnh nhân chưa đăng ký tài khoản. Nếu bệnh nhân đăng ký sau, hệ thống xác minh rồi cập nhật tài khoản vào hồ sơ hiện có.
3. `BAC_SI.MaND` và `NHAN_VIEN.MaND` bắt buộc có tài khoản tương ứng.
4. `LICH_HEN_KHAM.MaNVTao` được phép `NULL`: NULL nghĩa là bệnh nhân tự đặt lịch; có giá trị nghĩa là nhân viên tạo lịch thay bệnh nhân.
5. Bác sĩ của lịch hẹn được xác định thông qua `LICH_HEN_KHAM.MaLLV` → `LICH_LAM_VIEC.MaBS`; không cần thêm `MaBS` trực tiếp trong `LICH_HEN_KHAM` theo CDM hiện tại.
6. `LAN_KHAM.MaLH` nullable và unique: một lần khám có thể gắn với một lịch hẹn hoặc là lượt khám trực tiếp; mỗi lịch hẹn phát sinh tối đa một lần khám.
7. `DON_THUOC.MaLKham` unique theo phương án một lần khám tối đa một đơn thuốc. Nếu nhóm đổi nghiệp vụ cho phép nhiều đơn thuốc mỗi lần khám, bỏ unique này và điều chỉnh quan hệ.
8. Phải kiểm tra lịch hẹn nằm trong giờ làm, ngoài thời gian nghỉ và không trùng với lịch hẹn khác của cùng ca/bác sĩ. Kiểm tra khoảng thời gian giao nhau cần xử lý trong Business Logic với transaction; chỉ có CHECK constraint là chưa đủ.
9. Mỗi lần khám phải gắn với một đợt điều trị trong thiết kế hiện tại. Nếu cho phép khám độc lập không thuộc đợt điều trị, cần đổi `LAN_KHAM.MaDT` thành nullable và cập nhật quy tắc nghiệp vụ.
10. Không lưu mật khẩu dạng văn bản thuần; dùng `password_hash()` và `password_verify()` của PHP.
11. Dữ liệu bệnh án và đơn thuốc phải được kiểm soát truy cập ở backend; nhân viên không được truy cập nội dung chuyên môn theo chính sách đã chốt.

---

## 5. Quy trình nghiệp vụ MVP

### 5.1. Bệnh nhân tự đặt lịch trực tuyến

1. Bệnh nhân đăng ký tài khoản hoặc đăng nhập.
2. Hệ thống tạo/liên kết hồ sơ bệnh nhân.
3. Bệnh nhân chọn dịch vụ.
4. Hệ thống hiển thị ngày làm việc và khung giờ khả dụng của bác sĩ.
5. Bệnh nhân chọn lịch và gửi yêu cầu.
6. Hệ thống kiểm tra giờ làm, giờ nghỉ, thời lượng dịch vụ và xung đột lịch.
7. Hệ thống lưu `LICH_HEN_KHAM` với `MaNVTao = NULL`.
8. Bệnh nhân theo dõi trạng thái lịch hẹn.

### 5.2. Nhân viên đặt lịch tại phòng khám

1. Nhân viên đăng nhập.
2. Tìm bệnh nhân theo thông tin định danh phù hợp; nếu chưa có hồ sơ thì tạo hồ sơ mới.
3. Hồ sơ mới có thể chưa có tài khoản (`MaND = NULL`).
4. Nhân viên chọn dịch vụ, lịch làm việc và khung giờ.
5. Hệ thống kiểm tra xung đột lịch.
6. Hệ thống lưu lịch hẹn với `MaNVTao` là mã nhân viên đăng nhập.

### 5.3. Bác sĩ tiếp nhận và điều trị

1. Bác sĩ đăng nhập và xem lịch được phân công.
2. Bác sĩ mở hồ sơ bệnh án trong phạm vi được phân công.
3. Bác sĩ tạo hoặc cập nhật đợt điều trị.
4. Bác sĩ tạo lần khám, liên kết lịch hẹn nếu có.
5. Bác sĩ ghi nhận triệu chứng, chẩn đoán, kết quả và hướng xử lý.
6. Bác sĩ ghi nhận một hoặc nhiều dịch vụ thực tế đã thực hiện.
7. Nếu cần, bác sĩ lập đơn thuốc và thêm các chi tiết thuốc.

### 5.4. Quản trị và nhân viên

- Quản trị viên quản lý tài khoản, vai trò, bác sĩ, nhân viên, dịch vụ và lịch làm việc.
- Nhân viên thực hiện nghiệp vụ tiếp nhận, hồ sơ hành chính và lịch hẹn; không truy cập dữ liệu bệnh án/đơn thuốc.
- Quản trị viên xem số liệu vận hành tổng hợp nhưng không mặc nhiên được xem nội dung chuyên môn của bệnh án.

---

## 6. Kế hoạch thực hiện theo giai đoạn

### Giai đoạn 1 — Chốt phân tích yêu cầu

- [ ] Chốt phạm vi MVP và các chức năng ngoài MVP.
- [ ] Hoàn thiện bảng yêu cầu chức năng FR.
- [ ] Hoàn thiện bảng tác nhân và ma trận phân quyền.
- [ ] Vẽ Use Case tổng quát và Use Case theo từng vai trò.
- [ ] Đặc tả các quy trình chính: đăng ký, đặt lịch, nhân viên đặt lịch, bác sĩ tiếp nhận/điều trị/kê đơn.

**Đầu ra:** Tài liệu yêu cầu, ma trận phân quyền, Use Case và đặc tả nghiệp vụ.

### Giai đoạn 2 — Chốt thiết kế

- [ ] Cập nhật CDM từ 12 lên 13 thực thể (đã thêm `NHAN_VIEN`).
- [ ] Bổ sung quan hệ `NHAN_VIEN` – `TAI_KHOAN_NGUOI_DUNG`.
- [ ] Bổ sung quan hệ `NHAN_VIEN` – `LICH_HEN_KHAM` và thuộc tính FK `MaNVTao` ở mức PDM/SQL.
- [ ] Chốt `BENH_NHAN.MaND` nullable.
- [ ] Chuyển CDM sang PDM và chuẩn hóa kiểu dữ liệu.
- [ ] Đối chiếu tất cả PK, FK, UNIQUE và nullable với Database Schema.
- [ ] Hoàn thiện sơ đồ kiến trúc 3 lớp và thiết kế giao diện Figma.

**Đầu ra:** CDM, PDM, Database Schema, kiến trúc hệ thống và prototype giao diện.

### Giai đoạn 3 — Hoàn thiện cơ sở dữ liệu và nền tảng dự án

- [ ] Cập nhật database hiện tại từ 12 lên 13 bảng; không tạo lại database nếu đã có dữ liệu cần giữ.
- [ ] Cập nhật vai trò `NHAN_VIEN`.
- [ ] Cho phép `BENH_NHAN.MaND` là NULL.
- [ ] Thêm bảng `NHAN_VIEN` và FK `MaNVTao` trong `LICH_HEN_KHAM`.
- [ ] Kiểm tra toàn bộ khóa ngoại và ràng buộc.
- [ ] Tạo dữ liệu mẫu giả lập cho bốn vai trò và các quy trình.
- [ ] Tạo cấu trúc thư mục PHP MVC, file cấu hình PDO và router cơ bản.
- [ ] Kiểm tra kết nối PHP ↔ MariaDB.

**Đầu ra:** Database chạy được, dữ liệu mẫu và ứng dụng PHP khởi chạy được trên XAMPP.

### Giai đoạn 4 — Lập trình MVP

Thứ tự triển khai đề xuất:

1. Cấu hình ứng dụng, kết nối database, router và layout chung.
2. Đăng nhập/đăng xuất, session, middleware phân quyền.
3. Đăng ký bệnh nhân và liên kết tài khoản với hồ sơ bệnh nhân.
4. CRUD bệnh nhân, bác sĩ, nhân viên và dịch vụ.
5. Quản lý lịch làm việc.
6. Đặt lịch trực tuyến và đặt lịch do nhân viên tạo.
7. Quản lý, xác nhận, cập nhật và hủy lịch hẹn.
8. Hồ sơ bệnh án và đợt điều trị.
9. Lần khám và ghi nhận kết quả.
10. Dịch vụ thực hiện trong lần khám.
11. Đơn thuốc và chi tiết đơn thuốc.
12. Tìm kiếm, lọc, thống kê và báo cáo cơ bản.

**Đầu ra:** Các phân hệ MVP hoạt động và tích hợp với cùng một database.

### Giai đoạn 5 — Kiểm thử và sửa lỗi

- [ ] Kiểm thử từng chức năng và trường hợp nhập dữ liệu đúng/sai.
- [ ] Kiểm thử phân quyền cho bốn vai trò.
- [ ] Kiểm thử bệnh nhân không có tài khoản được nhân viên tạo hồ sơ và đặt lịch.
- [ ] Kiểm thử bệnh nhân tự đăng ký, tự đặt lịch (`MaNVTao = NULL`).
- [ ] Kiểm thử lịch trùng, ngoài giờ làm và trong giờ nghỉ.
- [ ] Kiểm thử liên kết lịch hẹn với lần khám.
- [ ] Kiểm thử đợt điều trị, dịch vụ lần khám, đơn thuốc và chi tiết thuốc.
- [ ] Kiểm thử truy cập trái phép vào hồ sơ bệnh án và đơn thuốc.
- [ ] Kiểm thử toàn quy trình từ đặt lịch đến khám và kê đơn.
- [ ] Ghi nhận lỗi, sửa và kiểm thử hồi quy.

**Đầu ra:** Test case, kết quả kiểm thử và danh sách lỗi đã xử lý.

### Giai đoạn 6 — Báo cáo, thuyết trình và demo

Báo cáo theo các mục giảng viên yêu cầu:

1. Giới thiệu đề tài.
2. Khảo sát hệ thống Odoo.
3. Phân tích yêu cầu.
4. Thiết kế hệ thống.
5. Thiết kế cơ sở dữ liệu.
6. Thiết kế giao diện.
7. Các chức năng đã xây dựng.
8. Kiểm thử.
9. Kết quả đạt được và hạn chế.
10. Hướng phát triển.

Chuẩn bị slide và kịch bản demo xuyên suốt: bệnh nhân tự đặt lịch hoặc nhân viên đặt lịch → nhân viên/quản trị xử lý lịch → bác sĩ tiếp nhận → cập nhật hồ sơ và đợt điều trị → ghi nhận lần khám và dịch vụ → kê đơn thuốc.

---

## 7. Phân chia công việc đề xuất cho nhóm 3 thành viên

Phân chia dưới đây là đề xuất ban đầu; nhóm có thể điều chỉnh theo năng lực và phân công thực tế.

| Thành viên | Phụ trách chính | Phối hợp |
|---|---|---|
| Thành viên 1 | Khảo sát Odoo, yêu cầu chức năng, Use Case, đặc tả quy trình, nội dung báo cáo | Kiểm thử nghiệp vụ và chuẩn bị demo |
| Thành viên 2 | CDM/PDM, MariaDB, SQL, dữ liệu mẫu, Model/Repository và kiểm tra dữ liệu | Tích hợp backend và kiểm thử cơ sở dữ liệu |
| Thành viên 3 | Figma, HTML/CSS/Bootstrap, View, bố cục giao diện | Tích hợp giao diện và kiểm thử UI |
| Cả nhóm | Thống nhất nghiệp vụ, Controller/Service, tích hợp, kiểm thử tổng thể, báo cáo và thuyết trình | Cùng chịu trách nhiệm sản phẩm cuối |

---

## 8. Tiêu chí hoàn thành MVP

MVP được xem là hoàn thành khi:

- [ ] Bốn vai trò đăng nhập và chỉ truy cập chức năng được cấp quyền.
- [ ] Bệnh nhân tự đăng ký tài khoản và đặt lịch trực tuyến thành công.
- [ ] Nhân viên tạo được hồ sơ bệnh nhân chưa có tài khoản và đặt lịch thay bệnh nhân.
- [ ] Hệ thống ghi nhận đúng người tạo lịch (`MaNVTao` có thể NULL).
- [ ] Quản trị viên quản lý được bác sĩ, nhân viên, dịch vụ và lịch làm việc.
- [ ] Hệ thống ngăn đặt lịch ngoài giờ làm, trong giờ nghỉ và trùng khung giờ theo quy tắc nghiệp vụ.
- [ ] Bác sĩ quản lý được hồ sơ, đợt điều trị và lần khám thuộc phạm vi được phân công.
- [ ] Bác sĩ ghi nhận được dịch vụ lần khám và lập đơn thuốc có nhiều chi tiết thuốc.
- [ ] Tìm kiếm, lọc và thống kê cơ bản hoạt động.
- [ ] Các test case quan trọng được chạy và có kết quả lưu lại.
- [ ] Báo cáo, slide và kịch bản demo phản ánh đúng chức năng thực sự đã xây dựng.

---

## 9. Các quyết định cần giữ nhất quán trong quá trình phát triển

1. Không tự ý thay đổi 13 thực thể đã chốt nếu chưa thống nhất với cả nhóm.
2. `MaND` trong `BENH_NHAN` phải nullable; `MaND` trong `BAC_SI` và `NHAN_VIEN` bắt buộc.
3. `MaNVTao` trong `LICH_HEN_KHAM` phải nullable để phân biệt lịch do nhân viên tạo và lịch bệnh nhân tự đặt.
4. Bác sĩ của lịch hẹn được suy ra từ `LICH_HEN_KHAM.MaLLV` → `LICH_LAM_VIEC.MaBS`.
5. `LAN_KHAM.MaBS` là bác sĩ thực tế thực hiện lần khám; có thể khác bác sĩ thuộc ca làm việc nếu quy trình cho phép điều phối.
6. `LAN_KHAM.MaLH` nullable và unique để hỗ trợ khám trực tiếp và bảo đảm một lịch hẹn không phát sinh hai lần khám.
7. `DON_THUOC.MaLKham` unique theo giả định hiện tại: tối đa một đơn thuốc cho một lần khám.
8. Dữ liệu bệnh án và đơn thuốc phải được kiểm soát theo vai trò ở backend.
9. Mọi thay đổi Database Schema phải được phản ánh đồng thời trong CDM/PDM, SQL, Model và tài liệu.
10. Chỉ ghi trong báo cáo và demo những chức năng nhóm đã thực sự xây dựng và kiểm thử.

