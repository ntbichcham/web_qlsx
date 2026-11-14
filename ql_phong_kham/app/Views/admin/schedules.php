<?php $today = date('Y-m-d'); ?>
<section class="container py-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-1">Quản lý lịch làm việc</h1>
        <p class="text-secondary mb-0">Tạo ca cho bác sĩ và theo dõi các ca bác sĩ tự đăng ký; thời gian nghỉ có thể bỏ trống.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-1">Tạo lịch làm việc</h2>
                    <p class="text-secondary small">Dùng khi cần xếp ca thay bác sĩ. Bác sĩ cũng có thể tự đăng ký trong khu vực của mình.</p>
                    <form method="post" action="<?= e(url('admin.schedules')) ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="scheduleDoctor">Bác sĩ</label>
                            <select class="form-select" id="scheduleDoctor" name="MaBS" required>
                                <?php foreach ($doctors as $d): ?>
                                    <option value="<?= (int)$d['MaBS'] ?>"><?= e($d['HoTen']) ?><?= $d['ChuyenMon'] ? ' · ' . e($d['ChuyenMon']) : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="scheduleDate">Ngày làm việc</label>
                            <input class="form-control" id="scheduleDate" type="date" name="NgayLamViec" min="<?= e($today) ?>" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label" for="scheduleStart">Giờ bắt đầu</label>
                                <input class="form-control" id="scheduleStart" type="time" name="GioBatDau" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="scheduleEnd">Giờ kết thúc</label>
                                <input class="form-control" id="scheduleEnd" type="time" name="GioKetThuc" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="scheduleBreakStart">Bắt đầu nghỉ</label>
                                <input class="form-control" id="scheduleBreakStart" type="time" name="BatDauNghi">
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="scheduleBreakEnd">Kết thúc nghỉ</label>
                                <input class="form-control" id="scheduleBreakEnd" type="time" name="KetThucNghi">
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3">Lưu lịch làm việc</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><strong>Danh sách lịch làm việc</strong></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Bác sĩ</th>
                            <th>Ngày</th>
                            <th>Giờ làm</th>
                            <th>Giờ nghỉ</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($schedules as $s): ?>
                            <tr>
                                <td>
                                    <?= e($s['TenBacSi']) ?>
                                    <?php if (!empty($s['ChuyenMon'])): ?><br><small class="text-secondary"><?= e($s['ChuyenMon']) ?></small><?php endif; ?>
                                </td>
                                <td>
                                    <?= e(date('d/m/Y', strtotime((string)$s['NgayLamViec']))) ?>
                                    <?php if ($s['NgayLamViec'] === $today): ?><span class="badge text-bg-info">Hôm nay</span><?php endif; ?>
                                </td>
                                <td><?= e(substr($s['GioBatDau'], 0, 5)) ?>–<?= e(substr($s['GioKetThuc'], 0, 5)) ?></td>
                                <td><?= $s['BatDauNghi'] && $s['KetThucNghi'] ? e(substr($s['BatDauNghi'], 0, 5)) . '–' . e(substr($s['KetThucNghi'], 0, 5)) : 'Không có' ?></td>
                                <td><?= status_badge($s['TrangThai']) ?></td>
                                <td>
                                    <form method="post" action="<?= e(url('admin.schedule.status')) ?>"
                                          <?= $s['TrangThai'] === 'HOAT_DONG' ? 'onsubmit="return confirm(\'Ngừng ca làm việc này?\');"' : '' ?>>
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="MaLLV" value="<?= (int)$s['MaLLV'] ?>">
                                        <?php if ($s['TrangThai'] === 'HOAT_DONG'): ?>
                                            <input type="hidden" name="TrangThai" value="NGUNG_HOAT_DONG">
                                            <button class="btn btn-sm btn-outline-danger">Ngừng</button>
                                        <?php else: ?>
                                            <input type="hidden" name="TrangThai" value="HOAT_DONG">
                                            <button class="btn btn-sm btn-outline-success">Mở lại</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$schedules): ?>
                            <tr><td colspan="6" class="text-center text-secondary py-4">Chưa có lịch làm việc nào.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
