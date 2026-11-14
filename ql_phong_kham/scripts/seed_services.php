<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\Database;

$db = Database::connection();

$services = [
    [
        'TenDV' => 'Khám tổng quát và tư vấn nha khoa',
        'MoTa' => 'Kiểm tra tổng thể sức khỏe răng miệng, tầm soát sớm sâu răng, viêm nướu, cao răng và tư vấn kế hoạch chăm sóc răng miệng định kỳ chuẩn y khoa.',
        'ThoiLuong' => 30,
        'GiaDV' => 100000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Lấy cao răng và đánh bóng siêu âm',
        'MoTa' => 'Làm sạch mảng bám vôi răng trên và dưới nướu bằng công nghệ sóng siêu âm êm ái, hạn chế ê buốt, kết hợp đánh bóng men răng sáng sạch tự nhiên.',
        'ThoiLuong' => 45,
        'GiaDV' => 250000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Trám răng thẩm mỹ Composite',
        'MoTa' => 'Phục hồi răng sâu ngà, răng mẻ hoặc mòn cổ chân răng bằng chất liệu Composite cao cấp đồng màu răng tự nhiên, chiếu đèn quang trùng hợp bền chắc.',
        'ThoiLuong' => 45,
        'GiaDV' => 350000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Điều trị tủy răng (Nội nha)',
        'MoTa' => 'Làm sạch ống tủy viêm nhiễm, khử khuẩn triệt để và trám bít kín hệ thống ống tủy, bảo tồn răng thật tối đa. Được quản lý theo dõi theo đợt điều trị chuyên sâu.',
        'ThoiLuong' => 60,
        'GiaDV' => 850000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Nhổ răng an toàn không đau',
        'MoTa' => 'Nhổ răng sữa lung lay, răng sâu vỡ lớn hoặc chân răng còn sót theo chỉ định bác sĩ. Ứng dụng kỹ thuật gây tê vi điểm nhẹ nhàng, an toàn và mau lành thương.',
        'ThoiLuong' => 45,
        'GiaDV' => 500000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Tiểu phẫu nhổ răng khôn (Răng số 8)',
        'MoTa' => 'Tiểu phẫu loại bỏ răng khôn mọc lệch, mọc ngầm, đâm ngang gây đau buốt hoặc chèn ép răng bên cạnh, thực hiện vô trùng tuyệt đối với máy cắt siêu âm.',
        'ThoiLuong' => 60,
        'GiaDV' => 1500000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Tẩy trắng răng công nghệ ánh sáng lạnh',
        'MoTa' => 'Dịch vụ thẩm mỹ nha khoa làm sáng màu men răng 2-3 tông chỉ sau 60 phút, sử dụng thuốc tẩy trắng chính hãng kích hoạt bằng ánh sáng LED an toàn.',
        'ThoiLuong' => 60,
        'GiaDV' => 1200000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Bọc răng sứ thẩm mỹ cao cấp',
        'MoTa' => 'Phục hình răng sứ toàn phần tái tạo nụ cười hoàn hảo, cải thiện hình thể răng thưa, ố màu, vỡ lớn hoặc sau chữa tủy, bảo hành chính hãng lâu dài.',
        'ThoiLuong' => 60,
        'GiaDV' => 2500000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Chụp X-quang nha khoa chẩn đoán hình ảnh',
        'MoTa' => 'Chụp phim kiểm tra chẩn đoán tình trạng chân răng, xương ổ răng và cuống răng phục vụ nhổ răng, chữa tủy hoặc lập đợt điều trị (Dịch vụ phát sinh).',
        'ThoiLuong' => 15,
        'GiaDV' => 150000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
    [
        'TenDV' => 'Tư vấn và lập phác đồ chỉnh nha (Niềng răng)',
        'MoTa' => 'Khám chuyên sâu sai lệch khớp cắn, lấy mẫu dấu hàm, phân tích cấu trúc xương hàm và thiết lập phác đồ đợt điều trị chỉnh nha theo dõi tiến triển từng giai đoạn.',
        'ThoiLuong' => 45,
        'GiaDV' => 300000,
        'TrangThai' => 'DANG_AP_DUNG',
    ],
];

$stmt = $db->prepare('INSERT INTO DICH_VU (TenDV, MoTa, ThoiLuong, GiaDV, TrangThai) VALUES (?, ?, ?, ?, ?)');

$count = 0;
foreach ($services as $s) {
    // Check if exists
    $check = $db->prepare('SELECT COUNT(*) FROM DICH_VU WHERE TenDV = ?');
    $check->execute([$s['TenDV']]);
    if ($check->fetchColumn() == 0) {
        $stmt->execute([$s['TenDV'], $s['MoTa'], $s['ThoiLuong'], $s['GiaDV'], $s['TrangThai']]);
        $count++;
    }
}

echo "Successfully seeded $count services into DICH_VU table.\n";
