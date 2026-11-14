<?php
use App\Core\Auth;

$user = Auth::user();
$role = strtoupper(trim($user['VaiTro'] ?? ''));

// Hàm phân loại nhóm dịch vụ dựa trên tên dịch vụ
if (!function_exists('getServiceCategory')) {
    function getServiceCategory(string $name): array {
        $nameLower = mb_strtolower($name, 'UTF-8');
        if (str_contains($nameLower, 'khám') || str_contains($nameLower, 'tư vấn') || str_contains($nameLower, 'x-quang') || str_contains($nameLower, 'chẩn đoán')) {
            return [
                'id' => 'kham-chuandoan',
                'name' => 'Khám & Chẩn đoán',
                'badge' => 'Chẩn đoán lâm sàng',
                'icon' => '🩺',
                'color' => 'primary',
                'equipment' => 'Gương nha khoa, sensor chụp X-quang số, đèn khám LED',
                'target' => 'Mọi lứa tuổi, người cần tầm soát định kỳ hoặc phát hiện triệu chứng đau nhức răng'
            ];
        }
        if (str_contains($nameLower, 'tủy') || str_contains($nameLower, 'trám') || str_contains($nameLower, 'cao răng') || str_contains($nameLower, 'nội nha') || str_contains($nameLower, 'nha chu')) {
            return [
                'id' => 'dieutri-baoton',
                'name' => 'Điều trị & Bảo tồn',
                'badge' => 'Bảo tồn răng thật',
                'icon' => '🛡️',
                'color' => 'success',
                'equipment' => 'Máy lấy cao răng siêu âm, đèn quang trùng hợp, máy định vị chóp răng',
                'target' => 'Bệnh nhân sâu răng, viêm nướu, viêm tủy cần phục hồi bảo tồn răng thật'
            ];
        }
        if (str_contains($nameLower, 'nhổ') || str_contains($nameLower, 'tiểu phẫu') || str_contains($nameLower, 'răng khôn') || str_contains($nameLower, 'phẫu thuật')) {
            return [
                'id' => 'tieu-phau',
                'name' => 'Tiểu phẫu nha khoa',
                'badge' => 'Kỹ thuật an toàn',
                'icon' => '⚡',
                'color' => 'danger',
                'equipment' => 'Máy cắt siêu âm Piezotome, bộ dụng cụ tiểu phẫu vô trùng, thuốc tê vi điểm',
                'target' => 'Bệnh nhân có răng khôn mọc lệch, răng sâu hỏng vỡ lớn hoặc răng lung lay'
            ];
        }
        return [
            'id' => 'thammy-phuchinh',
            'name' => 'Thẩm mỹ & Phục hình',
            'badge' => 'Nha khoa thẩm mỹ',
            'icon' => '✨',
            'color' => 'info',
            'equipment' => 'Đèn tẩy trắng ánh sáng lạnh LED, máy quét 3D dấu hàm kỹ thuật số',
            'target' => 'Khách hàng có nhu cầu làm sáng răng, phục hình răng sứt mẻ hoặc chỉnh nha toàn diện'
        ];
    }
}
?>

<!-- ==================== HERO SECTION ==================== -->
<section class="services-hero-section py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="<?= e(url('home')) ?>" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Danh mục Dịch vụ</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white shadow-sm border border-primary-subtle text-primary fw-semibold small mb-3">
                    <span>🦷 NHA KHOA AN TÂM</span>
                    <span class="text-secondary">·</span>
                    <span>CHUẨN Y KHOA QUỐC TẾ</span>
                </div>
                <h1 class="display-5 fw-bold text-dark mb-3">
                    Danh Mục Dịch Vụ <br class="d-none d-md-inline">
                    <span class="text-primary">Chăm Sóc & Điều Trị Răng Miệng</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    Hệ thống cung cấp đầy đủ các dịch vụ nha khoa chuyên sâu từ khám tổng quát, điều trị bảo tồn đến thẩm mỹ phục hình. 
                    Mọi dịch vụ đều có thời lượng dự kiến chuẩn xác, bảng giá niêm yết minh bạch và liên kết trực tiếp với hồ sơ bệnh án điện tử của bạn.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if ($user && $role === 'BENH_NHAN'): ?>
                        <a href="<?= e(url('patient.book')) ?>" class="btn btn-primary btn-lg shadow-sm">
                            <span class="me-1">📅</span> Đặt lịch khám ngay
                        </a>
                        <a href="<?= e(url('patient.appointments')) ?>" class="btn btn-outline-primary btn-lg">
                            Lịch hẹn của tôi
                        </a>
                    <?php elseif ($user && $role === 'NHAN_VIEN'): ?>
                        <a href="<?= e(url('staff.book')) ?>" class="btn btn-primary btn-lg shadow-sm">
                            <span class="me-1">📝</span> Đặt lịch tại quầy
                        </a>
                    <?php elseif ($user && $role === 'QUAN_TRI_VIEN'): ?>
                        <a href="<?= e(url('admin.services')) ?>" class="btn btn-primary btn-lg shadow-sm">
                            <span class="me-1">⚙️</span> Quản lý danh mục dịch vụ
                        </a>
                    <?php else: ?>
                        <a href="<?= e(url('auth.register')) ?>" class="btn btn-primary btn-lg shadow-sm">
                            <span class="me-1">🚀</span> Đăng ký đặt lịch khám
                        </a>
                        <a href="<?= e(url('auth.login')) ?>" class="btn btn-outline-primary btn-lg">
                            Đăng nhập hệ thống
                        </a>
                    <?php endif; ?>
                    <a href="#bang-gia-tong-hop" class="btn btn-outline-secondary btn-lg">
                        <span class="me-1">📋</span> Bảng giá niêm yết
                    </a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-primary">🦷</div>
                    <h3 class="h5 fw-bold text-dark mb-3">Cam Kết Chất Lượng Y Tế</h3>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li class="d-flex align-items-start gap-2">
                            <span class="badge rounded-circle bg-primary-subtle text-primary p-2">✓</span>
                            <div>
                                <strong class="d-block text-dark small">Bác sĩ chuyên khoa phụ trách chính</strong>
                                <span class="text-secondary small">Trực tiếp thăm khám, chẩn đoán và theo dõi xuyên suốt liệu trình.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <span class="badge rounded-circle bg-success-subtle text-success p-2">✓</span>
                            <div>
                                <strong class="d-block text-dark small">Vô trùng thiết bị khép kín</strong>
                                <span class="text-secondary small">Dụng cụ y tế tiệt trùng 100%, ghi nhận chi tiết thiết bị sử dụng trong từng lần khám.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <span class="badge rounded-circle bg-info-subtle text-info p-2">✓</span>
                            <div>
                                <strong class="d-block text-dark small">Hồ sơ bệnh án điện tử tập trung</strong>
                                <span class="text-secondary small">Lưu trữ lịch sử điều trị, hình ảnh X-quang và đơn thuốc trọn đời.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <span class="badge rounded-circle bg-warning-subtle text-warning p-2">✓</span>
                            <div>
                                <strong class="d-block text-dark small">Minh bạch dịch vụ & chi phí</strong>
                                <span class="text-secondary small">Không phát sinh chi phí ngoài dự kiến nếu chưa được bác sĩ tư vấn và bệnh nhân xác nhận.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== ẢNH MINH HỌA DỊCH VỤ ==================== -->
<section class="container pt-4">
    <figure class="services-photo-banner mb-0">
        <img src="<?= e(asset('images/services-banner.jpg')) ?>" alt="Bác sĩ nha khoa đang thực hiện điều trị chuyên khoa cho bệnh nhân" loading="lazy" decoding="async">
        <figcaption>
            <span class="badge text-bg-light text-primary mb-1">Chuyên môn &amp; tận tâm</span>
            <strong class="d-block">Điều trị chính xác, chăm sóc nhẹ nhàng</strong>
            <span class="small">Quy trình chuẩn y khoa, thiết bị hiện đại và hồ sơ điện tử xuyên suốt.</span>
        </figcaption>
    </figure>
</section>

<!-- ==================== MAIN CONTENT ==================== -->
<section class="container py-4">

    <!-- BỘ LỌC VÀ TÌM KIẾM DỊCH VỤ -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <!-- Search input -->
                <div class="col-lg-5">
                    <label class="form-label small fw-semibold text-secondary mb-1">Tìm kiếm dịch vụ</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">🔍</span>
                        <input type="text" id="serviceSearchInput" class="form-control border-start-0 bg-light" placeholder="Nhập tên dịch vụ, kỹ thuật hoặc triệu chứng (vd: trám, lấy cao răng, tủy...)" autocomplete="off">
                        <button class="btn btn-outline-secondary border-start-0" type="button" id="clearSearchBtn" title="Xóa tìm kiếm">✕</button>
                    </div>
                </div>

                <!-- Price filter -->
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Khoảng chi phí</label>
                    <select id="priceFilter" class="form-select bg-light">
                        <option value="all">Tất cả mức giá</option>
                        <option value="under-500k">Dưới 500.000 đ</option>
                        <option value="500k-1m">500.000 đ - 1.000.000 đ</option>
                        <option value="over-1m">Trên 1.000.000 đ</option>
                    </select>
                </div>

                <!-- Duration filter -->
                <div class="col-sm-6 col-lg-2">
                    <label class="form-label small fw-semibold text-secondary mb-1">Thời lượng</label>
                    <select id="durationFilter" class="form-select bg-light">
                        <option value="all">Tất cả thời lượng</option>
                        <option value="short">Dưới 30 phút</option>
                        <option value="medium">30 - 45 phút</option>
                        <option value="long">Trên 45 phút</option>
                    </select>
                </div>

                <!-- Counter stats -->
                <div class="col-lg-2 text-lg-end text-center pt-lg-3">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 fs-6 rounded-pill">
                        <span id="serviceCountBadge"><?= count($services) ?></span> / <?= count($services) ?> dịch vụ
                    </span>
                </div>
            </div>

            <!-- Categories Tabs -->
            <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                <span class="small fw-semibold text-secondary me-2">Nhóm chuyên môn:</span>
                <button class="btn btn-sm btn-outline-primary category-btn active" data-category="all">
                    Tất cả (<?= count($services) ?>)
                </button>
                <button class="btn btn-sm btn-outline-primary category-btn" data-category="kham-chuandoan">
                    🩺 Khám & Chẩn đoán
                </button>
                <button class="btn btn-sm btn-outline-primary category-btn" data-category="dieutri-baoton">
                    🛡️ Điều trị & Bảo tồn
                </button>
                <button class="btn btn-sm btn-outline-primary category-btn" data-category="tieu-phau">
                    ⚡ Tiểu phẫu nha khoa
                </button>
                <button class="btn btn-sm btn-outline-primary category-btn" data-category="thammy-phuchinh">
                    ✨ Thẩm mỹ & Phục hình
                </button>
            </div>
        </div>
    </div>

    <!-- DANH SÁCH THẺ DỊCH VỤ -->
    <div class="row g-4" id="servicesGrid">
        <?php foreach ($services as $service): 
            $cat = getServiceCategory($service['TenDV']);
            $priceVal = (float)$service['GiaDV'];
            $durationVal = (int)$service['ThoiLuong'];
        ?>
            <div class="col-md-6 col-lg-4 service-card-wrapper" 
                 data-category="<?= e($cat['id']) ?>" 
                 data-price="<?= $priceVal ?>" 
                 data-duration="<?= $durationVal ?>"
                 data-keywords="<?= e(mb_strtolower($service['TenDV'] . ' ' . $service['MoTa'] . ' ' . $cat['name'], 'UTF-8')) ?>">
                <div class="card h-100 border-0 shadow-sm service-card rounded-4 position-relative">
                    <div class="card-body p-4 d-flex flex-column">
                        <!-- Header card -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="service-icon-box rounded-3 bg-<?= e($cat['color']) ?>-subtle text-<?= e($cat['color']) ?>">
                                <?= $cat['icon'] ?>
                            </span>
                            <span class="badge rounded-pill bg-light text-secondary border">
                                <?= e($cat['badge']) ?>
                            </span>
                        </div>

                        <!-- Tên dịch vụ -->
                        <h2 class="h5 fw-bold text-dark mb-2 service-title"><?= e($service['TenDV']) ?></h2>

                        <!-- Mô tả dịch vụ -->
                        <p class="text-secondary small flex-grow-1 service-description mb-3">
                            <?= e($service['MoTa'] ?: 'Dịch vụ nha khoa được thực hiện theo quy trình y khoa chuẩn tại phòng khám.') ?>
                        </p>

                        <!-- Thông số kỹ thuật / Thời lượng & Thiết bị -->
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-secondary d-flex align-items-center gap-1">
                                    <span>⏱️</span> Thời lượng khám:
                                </span>
                                <span class="fw-semibold text-dark small"><?= $durationVal ?> phút</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-secondary d-flex align-items-center gap-1">
                                    <span>🔬</span> Thiết bị chủ đạo:
                                </span>
                                <span class="small text-truncate text-secondary text-end ms-2" style="max-width: 140px;" title="<?= e($cat['equipment']) ?>">
                                    <?= e($cat['equipment']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Giá & Trạng thái -->
                        <div class="d-flex justify-content-between align-items-baseline pt-2 border-top mb-3">
                            <div>
                                <small class="text-secondary d-block">Giá niêm yết:</small>
                                <span class="h5 fw-bold text-primary mb-0"><?= e(money($service['GiaDV'])) ?></span>
                            </div>
                            <div>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                    ✓ Đang áp dụng
                                </span>
                            </div>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="d-flex gap-2">
                            <?php if ($user && $role === 'BENH_NHAN'): ?>
                                <a href="<?= e(url('patient.book', ['service' => $service['MaDV']])) ?>" class="btn btn-primary btn-sm flex-grow-1 fw-semibold">
                                    <span class="me-1">📅</span> Đặt lịch
                                </a>
                            <?php elseif ($user && $role === 'NHAN_VIEN'): ?>
                                <a href="<?= e(url('staff.book', ['service' => $service['MaDV']])) ?>" class="btn btn-primary btn-sm flex-grow-1 fw-semibold">
                                    <span class="me-1">📝</span> Đặt lịch tại quầy
                                </a>
                            <?php elseif ($user && $role === 'BAC_SI'): ?>
                                <a href="<?= e(url('doctor.visits')) ?>" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    Xem lịch khám
                                </a>
                            <?php elseif ($user && $role === 'QUAN_TRI_VIEN'): ?>
                                <a href="<?= e(url('admin.services')) ?>" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    Quản trị dịch vụ
                                </a>
                            <?php else: ?>
                                <a href="<?= e(url('auth.login')) ?>" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    Đăng nhập để đặt lịch
                                </a>
                            <?php endif; ?>

                            <button type="button" class="btn btn-outline-secondary btn-sm px-3 view-detail-btn"
                                    data-id="<?= (int)$service['MaDV'] ?>"
                                    data-name="<?= e($service['TenDV']) ?>"
                                    data-desc="<?= e($service['MoTa']) ?>"
                                    data-duration="<?= $durationVal ?>"
                                    data-price="<?= e(money($service['GiaDV'])) ?>"
                                    data-category="<?= e($cat['name']) ?>"
                                    data-equipment="<?= e($cat['equipment']) ?>"
                                    data-target="<?= e($cat['target']) ?>">
                                Chi tiết
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!$services): ?>
            <div class="col-12">
                <div class="alert alert-info py-4 text-center rounded-4">
                    <h4 class="h5">Chưa có dịch vụ nào đang áp dụng</h4>
                    <p class="mb-0 text-secondary">Vui lòng quay lại sau hoặc liên hệ quản trị viên phòng khám để được hỗ trợ.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Empty state khi tìm kiếm không có kết quả -->
    <div id="noResultsState" class="empty-state d-none my-5">
        <div class="fs-1 mb-2">🔍</div>
        <h3 class="h5 fw-bold text-dark">Không tìm thấy dịch vụ phù hợp</h3>
        <p class="text-secondary small mb-3">Vui lòng thử tìm với từ khóa khác hoặc điều chỉnh lại bộ lọc giá và nhóm dịch vụ.</p>
        <button class="btn btn-outline-primary btn-sm" id="resetFiltersBtn">Đặt lại tất cả bộ lọc</button>
    </div>

</section>

<!-- ==================== QUY TRÌNH 4 GIAI ĐOẠN KHÁM CHUẨN Y KHOA ==================== -->
<section class="bg-white py-5 mt-5 border-top border-bottom">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="eyebrow text-uppercase text-primary fw-bold">QUY TRÌNH TIÊU CHUẨN THEO KHẢO SÁT HỆ THỐNG</span>
            <h2 class="h2 fw-bold text-dark mt-2">Hành Trình Khám & Điều Trị Tại Nha Khoa An Tâm</h2>
            <p class="text-secondary">
                Toàn bộ quy trình được quản trị khép kín trên hệ thống từ lúc đặt lịch hẹn, tạo hồ sơ lần khám, ghi nhận dịch vụ - thiết bị y tế cho đến theo dõi đợt điều trị và hẹn tái khám.
            </p>
        </div>

        <div class="row g-4">
            <!-- Giai đoạn 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center position-relative">
                    <div class="badge rounded-pill bg-primary text-white position-absolute top-0 start-50 translate-middle px-3 py-2">
                        Giai đoạn 1
                    </div>
                    <div class="fs-1 my-3">📅</div>
                    <h3 class="h6 fw-bold text-dark">Đặt Lịch & Tiếp Nhận</h3>
                    <p class="small text-secondary mb-0">
                        Bệnh nhân chủ động chọn dịch vụ, bác sĩ và khung giờ còn trống. Nhân viên tiếp nhận tra cứu hoặc tạo hồ sơ bệnh nhân khi đến phòng khám.
                    </p>
                </div>
            </div>

            <!-- Giai đoạn 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center position-relative">
                    <div class="badge rounded-pill bg-info text-white position-absolute top-0 start-50 translate-middle px-3 py-2">
                        Giai đoạn 2
                    </div>
                    <div class="fs-1 my-3">📋</div>
                    <h3 class="h6 fw-bold text-dark">Lập Hồ Sơ Lần Khám</h3>
                    <p class="small text-secondary mb-0">
                        Bác sĩ thăm khám lâm sàng, kiểm tra tiền sử dị ứng, ghi nhận triệu chứng, chẩn đoán và liên kết với đợt điều trị tương ứng.
                    </p>
                </div>
            </div>

            <!-- Giai đoạn 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center position-relative">
                    <div class="badge rounded-pill bg-warning text-dark position-absolute top-0 start-50 translate-middle px-3 py-2">
                        Giai đoạn 3
                    </div>
                    <div class="fs-1 my-3">🔬</div>
                    <h3 class="h6 fw-bold text-dark">Điều Trị & Dịch Vụ Phát Sinh</h3>
                    <p class="small text-secondary mb-0">
                        Bác sĩ và nhân viên y tế thực hiện thủ thuật, ghi nhận đầy đủ thiết bị y tế sử dụng và bổ sung các dịch vụ thực tế phát sinh hợp lý.
                    </p>
                </div>
            </div>

            <!-- Giai đoạn 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center position-relative">
                    <div class="badge rounded-pill bg-success text-white position-absolute top-0 start-50 translate-middle px-3 py-2">
                        Giai đoạn 4
                    </div>
                    <div class="fs-1 my-3">✨</div>
                    <h3 class="h6 fw-bold text-dark">Hoàn Tất & Hẹn Tái Khám</h3>
                    <p class="small text-secondary mb-0">
                        Bác sĩ kê đơn thuốc, hướng dẫn chăm sóc tại nhà, thiết lập lịch hẹn tái khám để theo dõi tiến triển toàn bộ liệu trình điều trị.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== BẢNG GIÁ NIÊM YẾT MINH BẠCH ==================== -->
<section class="container py-5" id="bang-gia-tong-hop">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
        <div>
            <span class="eyebrow text-uppercase text-primary fw-bold">MINH BẠCH CHI PHÍ</span>
            <h2 class="h3 fw-bold text-dark mt-1">Bảng Tra Cứu Đơn Giá Dịch Vụ Niêm Yết</h2>
            <p class="text-secondary small mb-0">Đơn giá tham khảo được bảo toàn lịch sử tại thời điểm thực hiện theo tài liệu khảo sát hệ thống.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <span>🖨️</span> In bảng giá
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã DV</th>
                        <th>Tên Dịch Vụ Nha Khoa</th>
                        <th>Nhóm Chuyên Môn</th>
                        <th class="text-center">Thời Lượng</th>
                        <th class="text-end">Đơn Giá Niêm Yết</th>
                        <th class="text-center pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): 
                        $cat = getServiceCategory($s['TenDV']);
                    ?>
                    <tr>
                        <td class="ps-4 fw-semibold text-secondary">#DV<?= sprintf('%03d', $s['MaDV']) ?></td>
                        <td>
                            <strong class="text-dark d-block"><?= e($s['TenDV']) ?></strong>
                            <small class="text-secondary text-truncate d-block" style="max-width: 380px;">
                                <?= e($s['MoTa']) ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-<?= e($cat['color']) ?>-subtle text-<?= e($cat['color']) ?>">
                                <?= e($cat['name']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">
                                ⏱️ <?= (int)$s['ThoiLuong'] ?> phút
                            </span>
                        </td>
                        <td class="text-end fw-bold text-primary">
                            <?= e(money($s['GiaDV'])) ?>
                        </td>
                        <td class="text-center pe-4">
                            <?php if ($user && $role === 'BENH_NHAN'): ?>
                                <a href="<?= e(url('patient.book', ['service' => $s['MaDV']])) ?>" class="btn btn-sm btn-primary fw-semibold">
                                    <span class="me-1">📅</span> Đặt lịch
                                </a>
                            <?php elseif ($user && $role === 'NHAN_VIEN'): ?>
                                <a href="<?= e(url('staff.book', ['service' => $s['MaDV']])) ?>" class="btn btn-sm btn-primary fw-semibold">
                                    Đặt lịch tại quầy
                                </a>
                            <?php else: ?>
                                <a href="<?= e(url('auth.login')) ?>" class="btn btn-sm btn-outline-primary">
                                    Đăng nhập để đặt lịch
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ==================== CÂU HỎI THƯỜNG GẶP (FAQ) ==================== -->
<section class="container py-4 mb-5">
    <div class="row g-4 align-items-center">
        <div class="col-lg-5">
            <span class="eyebrow text-uppercase text-primary fw-bold">HỎI ĐÁP & CHÍNH SÁCH</span>
            <h2 class="h3 fw-bold text-dark mt-1">Những Điều Bệnh Nhân Cần Biết Khi Khám Răng</h2>
            <p class="text-secondary">
                Hệ thống Nha Khoa An Tâm hỗ trợ quản lý hồ sơ khám và lịch hẹn minh bạch. Dưới đây là giải đáp cho các câu hỏi phổ biến nhất.
            </p>
            <div class="card border-0 bg-primary-subtle text-primary p-4 rounded-4 mt-3">
                <h4 class="h6 fw-bold mb-2">📞 Tổng đài hỗ trợ đặt lịch 24/7</h4>
                <p class="small mb-3 text-primary-emphasis">Đội ngũ nhân viên tiếp nhận sẵn sàng hướng dẫn bạn chọn bác sĩ và thời gian khám thuận tiện nhất.</p>
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-4">☎️</span>
                    <strong class="fs-5">1900 6868</strong>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="accordion accordion-flush" id="serviceFaqAccordion">
                <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                    <h3 class="accordion-header" id="faq1">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1">
                            1. Tôi có thể đặt lịch trước cho nhiều dịch vụ cùng lúc không?
                        </button>
                    </h3>
                    <div id="faqCollapse1" class="accordion-collapse collapse" data-bs-parent="#serviceFaqAccordion">
                        <div class="accordion-body text-secondary small">
                            Khi đặt lịch trực tuyến, bạn chọn trước dịch vụ chính quan tâm (ví dụ: Khám tổng quát hoặc Lấy cao răng). 
                            Trong quá trình thăm khám thực tế, nếu cần thiết, bác sĩ có thể bổ sung các dịch vụ phát sinh (như chụp X-quang, trám răng sâu) trực tiếp vào hồ sơ lần khám của bạn.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                    <h3 class="accordion-header" id="faq2">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2">
                            2. Thời lượng của dịch vụ được tính toán như thế nào?
                        </button>
                    </h3>
                    <div id="faqCollapse2" class="accordion-collapse collapse" data-bs-parent="#serviceFaqAccordion">
                        <div class="accordion-body text-secondary small">
                            Mỗi dịch vụ được thiết lập thời lượng dự kiến chuẩn (ví dụ 30 phút, 45 phút, 60 phút). 
                            Khi bạn chọn dịch vụ và giờ bắt đầu, hệ thống sẽ tự động tính toán thời gian kết thúc và kiểm tra khung giờ làm việc của bác sĩ để đảm bảo lịch hẹn không bị trùng lặp.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                    <h3 class="accordion-header" id="faq3">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3">
                            3. Điều trị tủy hoặc chỉnh nha có cần đến nhiều lần không?
                        </button>
                    </h3>
                    <div id="faqCollapse3" class="accordion-collapse collapse" data-bs-parent="#serviceFaqAccordion">
                        <div class="accordion-body text-secondary small">
                            Có. Đối với các kỹ thuật chuyên sâu như chữa tủy hoặc niềng răng, hệ thống sẽ tạo một <strong>Đợt điều trị (Liệu trình)</strong>. 
                            Mỗi lần bạn đến khám sẽ là một hồ sơ lần khám gắn với đợt điều trị đó, giúp bác sĩ theo dõi tiến triển từng bước từ lúc bắt đầu cho đến khi hoàn thành.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                    <h3 class="accordion-header" id="faq4">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4">
                            4. Tôi có thể hủy hoặc đổi lịch khám đã đặt không?
                        </button>
                    </h3>
                    <div id="faqCollapse4" class="accordion-collapse collapse" data-bs-parent="#serviceFaqAccordion">
                        <div class="accordion-body text-secondary small">
                            Bệnh nhân có thể xem danh sách lịch hẹn trong trang cá nhân và chủ động hủy các lịch hẹn đang ở trạng thái "Chờ xác nhận" hoặc "Đã xác nhận" nếu lịch hẹn chưa diễn ra.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== MODAL CHI TIẾT DỊCH VỤ ==================== -->
<div class="modal fade" id="serviceDetailModal" tabindex="-1" aria-labelledby="serviceDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2" id="modalCategoryBadge">Nhóm dịch vụ</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-4">
                <h3 class="h4 fw-bold text-dark mb-3" id="modalServiceName">Tên dịch vụ</h3>
                <p class="text-secondary mb-4" id="modalServiceDesc">Mô tả chi tiết dịch vụ.</p>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-secondary small d-block">⏱️ Thời lượng thực hiện dự kiến</span>
                            <strong class="text-dark fs-5" id="modalServiceDuration">-- phút</strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-primary-subtle rounded-3">
                            <span class="text-primary-emphasis small d-block">💰 Đơn giá niêm yết hiện hành</span>
                            <strong class="text-primary fs-5" id="modalServicePrice">-- đ</strong>
                        </div>
                    </div>
                </div>

                <div class="card border bg-white rounded-3 p-3 mb-3">
                    <h5 class="h6 fw-bold text-dark mb-2">🎯 Đối tượng & Chỉ định điều trị:</h5>
                    <p class="small text-secondary mb-0" id="modalServiceTarget">--</p>
                </div>

                <div class="card border bg-white rounded-3 p-3 mb-3">
                    <h5 class="h6 fw-bold text-dark mb-2">🔬 Thiết bị y tế ứng dụng:</h5>
                    <p class="small text-secondary mb-0" id="modalServiceEquipment">--</p>
                </div>

                <div class="card border bg-light rounded-3 p-3">
                    <h5 class="h6 fw-bold text-dark mb-2">📌 Cam kết quy trình y khoa:</h5>
                    <ul class="small text-secondary mb-0 ps-3">
                        <li>Thực hiện bởi Bác sĩ Răng Hàm Mặt phụ trách chính và Nhân viên y tế hỗ trợ.</li>
                        <li>Dụng cụ y tế được tiệt trùng vô khuẩn khép kín trước mỗi ca điều trị.</li>
                        <li>Thông tin lần khám được lưu trữ tự động vào Hồ sơ bệnh án điện tử.</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                <a href="#" id="modalBookLink" class="btn btn-primary px-4 fw-semibold">Đặt lịch dịch vụ này →</a>
            </div>
        </div>
    </div>
</div>

<!-- ==================== JAVASCRIPT LỌC & TÌM KIẾM DỊCH VỤ ==================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('serviceSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const priceFilter = document.getElementById('priceFilter');
    const durationFilter = document.getElementById('durationFilter');
    const categoryBtns = document.querySelectorAll('.category-btn');
    const serviceWrappers = document.querySelectorAll('.service-card-wrapper');
    const countBadge = document.getElementById('serviceCountBadge');
    const noResultsState = document.getElementById('noResultsState');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');

    let activeCategory = 'all';

    function filterServices() {
        const query = searchInput.value.trim().toLowerCase();
        const priceVal = priceFilter.value;
        const durationVal = durationFilter.value;
        let visibleCount = 0;

        serviceWrappers.forEach(function (card) {
            const cardCat = card.getAttribute('data-category');
            const cardPrice = parseFloat(card.getAttribute('data-price')) || 0;
            const cardDuration = parseInt(card.getAttribute('data-duration')) || 0;
            const keywords = card.getAttribute('data-keywords') || '';

            // Kiểm tra tìm kiếm từ khóa
            const matchQuery = !query || keywords.includes(query);

            // Kiểm tra chuyên mục
            const matchCategory = (activeCategory === 'all') || (cardCat === activeCategory);

            // Kiểm tra khoảng giá
            let matchPrice = true;
            if (priceVal === 'under-500k') {
                matchPrice = cardPrice < 500000;
            } else if (priceVal === '500k-1m') {
                matchPrice = cardPrice >= 500000 && cardPrice <= 1000000;
            } else if (priceVal === 'over-1m') {
                matchPrice = cardPrice > 1000000;
            }

            // Kiểm tra thời lượng
            let matchDuration = true;
            if (durationVal === 'short') {
                matchDuration = cardDuration < 30;
            } else if (durationVal === 'medium') {
                matchDuration = cardDuration >= 30 && cardDuration <= 45;
            } else if (durationVal === 'long') {
                matchDuration = cardDuration > 45;
            }

            if (matchQuery && matchCategory && matchPrice && matchDuration) {
                card.classList.remove('d-none');
                visibleCount++;
            } else {
                card.classList.add('d-none');
            }
        });

        countBadge.textContent = visibleCount;
        if (visibleCount === 0) {
            noResultsState.classList.remove('d-none');
        } else {
            noResultsState.classList.add('d-none');
        }
    }

    // Sự kiện gõ tìm kiếm
    searchInput.addEventListener('input', filterServices);

    // Xóa tìm kiếm
    clearSearchBtn.addEventListener('click', function () {
        searchInput.value = '';
        searchInput.focus();
        filterServices();
    });

    // Thay đổi lọc giá & thời lượng
    priceFilter.addEventListener('change', filterServices);
    durationFilter.addEventListener('change', filterServices);

    // Chuyển tab danh mục
    categoryBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeCategory = this.getAttribute('data-category');
            filterServices();
        });
    });

    // Reset bộ lọc
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function () {
            searchInput.value = '';
            priceFilter.value = 'all';
            durationFilter.value = 'all';
            activeCategory = 'all';
            categoryBtns.forEach(b => b.classList.remove('active'));
            document.querySelector('.category-btn[data-category="all"]').classList.add('active');
            filterServices();
        });
    }

    // Modal chi tiết dịch vụ
    const detailModal = document.getElementById('serviceDetailModal');
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const name = button.getAttribute('data-name');
            const desc = button.getAttribute('data-desc');
            const duration = button.getAttribute('data-duration');
            const price = button.getAttribute('data-price');
            const cat = button.getAttribute('data-category');
            const equipment = button.getAttribute('data-equipment');
            const target = button.getAttribute('data-target');
            const id = button.getAttribute('data-id');

            document.getElementById('modalServiceName').textContent = name;
            document.getElementById('modalServiceDesc').textContent = desc || 'Chưa có mô tả chi tiết.';
            document.getElementById('modalServiceDuration').textContent = duration + ' phút';
            document.getElementById('modalServicePrice').textContent = price;
            document.getElementById('modalCategoryBadge').textContent = cat;
            document.getElementById('modalServiceEquipment').textContent = equipment || 'Thiết bị chuyên khoa nha khoa';
            document.getElementById('modalServiceTarget').textContent = target || 'Bệnh nhân có nhu cầu điều trị nha khoa';

            const bookLink = document.getElementById('modalBookLink');
            <?php if ($user && $role === 'BENH_NHAN'): ?>
                bookLink.href = '<?= e(url('patient.book')) ?>&service=' + id;
                bookLink.textContent = 'Đặt lịch dịch vụ này →';
            <?php elseif ($user && $role === 'NHAN_VIEN'): ?>
                bookLink.href = '<?= e(url('staff.book')) ?>&service=' + id;
                bookLink.textContent = 'Tạo lịch tại quầy →';
            <?php elseif ($user && $role === 'QUAN_TRI_VIEN'): ?>
                bookLink.href = '<?= e(url('admin.services')) ?>';
                bookLink.textContent = 'Quản trị dịch vụ →';
            <?php else: ?>
                bookLink.href = '<?= e(url('auth.login')) ?>';
                bookLink.textContent = 'Đăng nhập để đặt lịch →';
            <?php endif; ?>
        });
    }
});
</script>
