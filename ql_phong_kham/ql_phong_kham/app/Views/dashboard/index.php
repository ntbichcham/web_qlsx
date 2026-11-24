<section class="container py-4">
  <div class="mb-4"><span class="eyebrow">KHU VỰC <?= e(mb_strtoupper($roleLabel)) ?></span><h1 class="h2 fw-bold">Xin chào, <?= e(current_user()['HoTen'] ?? '') ?></h1><p class="text-secondary mb-0">Đây là thông tin tổng quan theo quyền truy cập của bạn.</p></div>
  <?php if (($roleLabel ?? '') === 'Bác sĩ'): ?>
  <div class="d-flex flex-wrap gap-2 mb-4">
    <a class="btn btn-primary" href="<?= e(url('doctor.schedules')) ?>">＋ Đăng ký ca làm việc</a>
    <a class="btn btn-outline-primary" href="<?= e(url('doctor.visits')) ?>">Xem lịch khám được phân công</a>
  </div>
  <?php endif; ?>
  <?php if (!empty($counts)): ?><div class="row g-3 mb-4">
    <?php $labels=['BENH_NHAN'=>'Bệnh nhân','BAC_SI'=>'Bác sĩ','NHAN_VIEN'=>'Nhân viên','DICH_VU'=>'Dịch vụ','LICH_HEN_KHAM'=>'Lịch hẹn']; foreach ($labels as $key=>$label): ?><div class="col-6 col-lg"><div class="stat-card"><div class="text-secondary small"><?= e($label) ?></div><div class="display-6 fw-semibold"><?= (int)($counts[$key] ?? 0) ?></div></div></div><?php endforeach; ?>
  </div><?php endif; ?>
  <?php if (!empty($appointments)): ?><div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h2 class="h5 mb-0">Lịch hẹn gần đây</h2></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Mã</th><th>Bệnh nhân</th><th>Dịch vụ</th><th>Ngày</th><th>Giờ</th><th>Trạng thái</th></tr></thead><tbody><?php foreach ($appointments as $a): ?><tr><td>#<?= (int)$a['MaLH'] ?></td><td><?= e($a['TenBenhNhan'] ?? 'Lịch của bạn') ?></td><td><?= e($a['TenDV']) ?></td><td><?= e($a['NgayKham']) ?></td><td><?= e(substr($a['GioBatDau'],0,5)) ?>–<?= e(substr($a['GioKetThuc'],0,5)) ?></td><td><?= status_badge($a['TrangThai']) ?></td></tr><?php endforeach; ?></tbody></table></div></div><?php else: ?><div class="empty-state"><h2 class="h5">Chưa có dữ liệu hiển thị</h2><p class="text-secondary">Khi phát sinh lịch hẹn hoặc dữ liệu nghiệp vụ, thông tin sẽ xuất hiện tại đây.</p></div><?php endif; ?>
</section>
