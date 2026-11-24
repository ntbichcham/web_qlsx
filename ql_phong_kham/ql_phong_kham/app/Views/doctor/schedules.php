<?php
$weekdays = ['Mon' => 'Thứ 2', 'Tue' => 'Thứ 3', 'Wed' => 'Thứ 4', 'Thu' => 'Thứ 5', 'Fri' => 'Thứ 6', 'Sat' => 'Thứ 7', 'Sun' => 'Chủ nhật'];
$formatDate = static function (string $date) use ($weekdays): string {
    $time = strtotime($date);
    if ($time === false) return $date;
    return ($weekdays[date('D', $time)] ?? date('D', $time)) . ', ' . date('d/m/Y', $time);
};
$now = date('Y-m-d');
$upcoming = 0;
foreach ($schedules as $s) {
    if ($s['TrangThai'] === 'HOAT_DONG' && $s['NgayLamViec'] >= $now) $upcoming++;
}
?>
<section class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <span class="eyebrow">BÁC SĨ · <?= e(mb_strtoupper($doctor['HoTen'])) ?></span>
            <h1 class="h2 fw-bold mb-1">Lịch làm việc của tôi</h1>
            <p class="text-secondary mb-0">Tự đăng ký ca làm việc để bệnh nhân có thể chọn khung giờ phù hợp khi đặt lịch.</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge text-bg-primary fs-6 px-3 py-2"><?= $upcoming ?> ca sắp tới</span>
            <a class="btn btn-outline-primary" href="<?= e(url('doctor.visits')) ?>">Xem lịch khám</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-1">Đăng ký ca làm việc</h2>
                    <p class="text-secondary small">Ca làm việc có hiệu lực ngay sau khi đăng ký. Giờ nghỉ có thể bỏ trống.</p>
                    <form method="post" action="<?= e(url('doctor.schedule.create')) ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="workDate">Ngày làm việc</label>
                            <input class="form-control" id="workDate" type="date" name="NgayLamViec" min="<?= e($today) ?>" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label" for="workStart">Giờ bắt đầu</label>
                                <input class="form-control" id="workStart" type="time" name="GioBatDau" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="workEnd">Giờ kết thúc</label>
                                <input class="form-control" id="workEnd" type="time" name="GioKetThuc" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="breakStart">Bắt đầu nghỉ</label>
                                <input class="form-control" id="breakStart" type="time" name="BatDauNghi">
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="breakEnd">Kết thúc nghỉ</label>
                                <input class="form-control" id="breakEnd" type="time" name="KetThucNghi">
                            </div>
                        </div>
                        <div class="alert alert-light border small mt-3 mb-3">
                            <strong>Lưu ý:</strong> ca không được trùng giờ với ca khác trong cùng ngày. Chỉ có thể ngừng ca chưa diễn ra và chưa có lịch hẹn.
                        </div>
                        <button class="btn btn-primary w-100">
                            <span class="me-1">＋</span> Đăng ký ca làm việc
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <strong>Ca làm việc đã đăng ký</strong>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Mã ca</th>
                            <th>Ngày</th>
                            <th>Giờ làm</th>
                            <th>Giờ nghỉ</th>
                            <th>Lịch hẹn</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($schedules as $s): ?>
                            <?php
                            $isActive = $s['TrangThai'] === 'HOAT_DONG';
                            $isUpcoming = $s['NgayLamViec'] >= $now;
                            $appointments = (int)$s['SoLichHen'];
                            ?>
                            <tr>
                                <td>#<?= (int)$s['MaLLV'] ?></td>
                                <td>
                                    <?= e($formatDate((string)$s['NgayLamViec'])) ?>
                                    <?php if ($s['NgayLamViec'] === $now): ?><span class="badge text-bg-info">Hôm nay</span><?php endif; ?>
                                </td>
                                <td><?= e(substr($s['GioBatDau'], 0, 5)) ?>–<?= e(substr($s['GioKetThuc'], 0, 5)) ?></td>
                                <td>
                                    <?= $s['BatDauNghi'] && $s['KetThucNghi'] ? e(substr($s['BatDauNghi'], 0, 5)) . '–' . e(substr($s['KetThucNghi'], 0, 5)) : '—' ?>
                                </td>
                                <td>
                                    <?php if ($appointments > 0): ?>
                                        <span class="badge text-bg-warning"><?= $appointments ?> lịch hẹn</span>
                                    <?php else: ?>
                                        <span class="text-secondary small">Chưa có</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= status_badge($s['TrangThai']) ?></td>
                                <td class="text-end">
                                    <?php if ($isActive && $isUpcoming && $appointments === 0): ?>
                                        <form method="post" action="<?= e(url('doctor.schedule.cancel')) ?>"
                                              onsubmit="return confirm('Bạn chắc chắn muốn ngừng ca làm việc #<?= (int)$s['MaLLV'] ?>?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="MaLLV" value="<?= (int)$s['MaLLV'] ?>">
                                            <button class="btn btn-sm btn-outline-danger">Ngừng ca</button>
                                        </form>
                                    <?php elseif ($appointments > 0): ?>
                                        <span class="text-secondary small">Đang có lịch hẹn</span>
                                    <?php elseif (!$isActive): ?>
                                        <span class="text-secondary small">Đã ngừng</span>
                                    <?php else: ?>
                                        <span class="text-secondary small">Đã qua</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$schedules): ?>
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">
                                    Bạn chưa đăng ký ca làm việc nào. Hãy tạo ca đầu tiên để bệnh nhân có thể đặt lịch.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
