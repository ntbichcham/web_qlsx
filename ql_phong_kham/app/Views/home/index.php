<?php
use App\Core\Auth;
$u = Auth::user();
$r = strtoupper(trim($u['VaiTro'] ?? ''));
?>
<section class="hero-section">
  <div class="container py-5">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <span class="eyebrow">CHĂM SÓC NỤ CƯỜI CỦA BẠN</span>
        <h1 class="display-5 fw-bold mt-3">Đặt lịch dễ dàng.<br><span class="text-primary-emphasis">Chăm sóc tận tâm.</span></h1>
        <p class="lead text-secondary mt-3">Theo dõi lịch hẹn, thông tin khám và quá trình điều trị tại phòng khám nha khoa trên cùng một hệ thống.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
          <?php if (!$u): ?>
            <a class="btn btn-primary btn-lg" href="<?= e(url('auth.register')) ?>">Đăng ký đặt lịch</a>
          <?php elseif ($r === 'BENH_NHAN'): ?>
            <a class="btn btn-primary btn-lg" href="<?= e(url('patient.book')) ?>">📅 Đặt lịch khám ngay</a>
          <?php elseif ($r === 'NHAN_VIEN'): ?>
            <a class="btn btn-primary btn-lg" href="<?= e(url('staff.book')) ?>">📝 Đặt lịch tại quầy</a>
          <?php elseif ($r === 'BAC_SI'): ?>
            <a class="btn btn-primary btn-lg" href="<?= e(url('doctor.visits')) ?>">🩺 Xem lịch khám</a>
          <?php elseif ($r === 'QUAN_TRI_VIEN'): ?>
            <a class="btn btn-primary btn-lg" href="<?= e(url('admin.dashboard')) ?>">⚙️ Bảng điều khiển</a>
          <?php endif; ?>
          <a class="btn btn-outline-primary btn-lg" href="<?= e(url('services')) ?>">Xem dịch vụ</a>
        </div>
      </div>
      <div class="col-lg-5">
        <figure class="hero-photo mb-0">
          <img src="<?= e(asset('images/hero-clinic.jpg')) ?>" alt="Bác sĩ nha khoa thăm khám và hướng dẫn chăm sóc răng miệng cho bệnh nhân">
          <figcaption class="hero-photo-caption">
            <span class="badge text-bg-primary mb-1">Bác sĩ tận tâm</span>
            <strong class="d-block">Chăm sóc nhẹ nhàng, an toàn</strong>
            <span class="text-secondary small">Đồng hành cùng bạn trong suốt quá trình điều trị.</span>
          </figcaption>
        </figure>
      </div>
    </div>
  </div>
</section>
<section class="container py-5">
  <div class="row align-items-center g-4 g-lg-5">
    <div class="col-lg-6">
      <img class="about-photo" src="<?= e(asset('images/clinic-about.jpg')) ?>" alt="Không gian phòng điều trị nha khoa sạch sẽ, hiện đại tại Nha Khoa An Tâm" loading="lazy" decoding="async">
    </div>
    <div class="col-lg-6">
      <span class="eyebrow">KHÔNG GIAN PHÒNG KHÁM</span>
      <h2 class="h3 fw-bold mt-2">Sạch sẽ, hiện đại và an toàn cho mọi lứa tuổi</h2>
      <p class="text-secondary">Phòng điều trị được bố trí khoa học, thiết bị nha khoa hiện đại và quy trình vô trùng khép kín, giúp mỗi lần thăm khám trở nên nhẹ nhàng và thoải mái.</p>
      <div class="row g-3 mt-1">
        <div class="col-sm-6">
          <div class="d-flex align-items-start gap-2">
            <span class="fs-5">🦷</span>
            <div><strong class="d-block small">Thiết bị hiện đại</strong><span class="text-secondary small">Hỗ trợ chẩn đoán và điều trị chính xác.</span></div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="d-flex align-items-start gap-2">
            <span class="fs-5">🧼</span>
            <div><strong class="d-block small">Vô trùng khép kín</strong><span class="text-secondary small">Dụng cụ được tiệt trùng trước mỗi ca.</span></div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="d-flex align-items-start gap-2">
            <span class="fs-5">👩‍⚕️</span>
            <div><strong class="d-block small">Bác sĩ chuyên khoa</strong><span class="text-secondary small">Theo dõi liệu trình xuyên suốt.</span></div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="d-flex align-items-start gap-2">
            <span class="fs-5">📋</span>
            <div><strong class="d-block small">Hồ sơ điện tử</strong><span class="text-secondary small">Lưu trữ lịch sử khám và đơn thuốc.</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="container py-5">
  <div class="d-flex justify-content-between align-items-end mb-3"><div><span class="eyebrow">DỊCH VỤ</span><h2 class="h3 fw-bold">Dịch vụ nổi bật</h2></div><a href="<?= e(url('services')) ?>">Xem tất cả →</a></div>
  <div class="row g-3">
    <?php if (!$services): ?><div class="col-12"><div class="alert alert-light">Chưa có dịch vụ được công khai. Quản trị viên có thể thêm dịch vụ sau khi đăng nhập.</div></div><?php endif; ?>
    <?php foreach ($services as $service): ?>
      <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 d-flex flex-column">
          <div class="card-body flex-grow-1">
            <h3 class="h5"><?= e($service['TenDV']) ?></h3>
            <p class="text-secondary small"><?= e($service['MoTa']) ?></p>
            <div class="fw-semibold text-primary"><?= e(money($service['GiaDV'])) ?></div>
            <small class="text-secondary"><?= (int)$service['ThoiLuong'] ?> phút</small>
          </div>
          <div class="card-footer bg-white border-0 pt-0 pb-3">
            <?php if (!$u): ?>
              <a href="<?= e(url('auth.login')) ?>" class="btn btn-outline-primary btn-sm w-100">Đăng nhập để đặt lịch</a>
            <?php elseif ($r === 'BENH_NHAN'): ?>
              <a href="<?= e(url('patient.book', ['service' => $service['MaDV']])) ?>" class="btn btn-primary btn-sm w-100">📅 Đặt lịch</a>
            <?php elseif ($r === 'NHAN_VIEN'): ?>
              <a href="<?= e(url('staff.book', ['service' => $service['MaDV']])) ?>" class="btn btn-primary btn-sm w-100">📝 Đặt lịch tại quầy</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
