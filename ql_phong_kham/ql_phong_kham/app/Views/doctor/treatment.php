<?php
$t = $treatment;
$endDate = $t['NgayKetThuc'] ? substr((string)$t['NgayKetThuc'], 0, 10) : '';
?>
<section class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="eyebrow">THEO DÕI ĐIỀU TRỊ</span>
            <h1 class="h2 fw-bold mb-1">Đợt điều trị #<?= (int)$t['MaDT'] ?></h1>
            <p class="text-secondary mb-0">
                <?= e($t['TenBenhNhan']) ?> · Bắt đầu <?= e(date('d/m/Y', strtotime((string)$t['NgayBatDau']))) ?>
                <?= status_badge($t['TrangThai']) ?>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary" href="<?= e(url('doctor.record', ['id' => (int)$t['MaBN']])) ?>">Hồ sơ bệnh án</a>
            <a class="btn btn-outline-secondary" href="<?= e(url('doctor.treatments')) ?>">← Danh sách đợt điều trị</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Kế hoạch và kết quả điều trị</h2>
                    <div class="mb-3">
                        <label class="form-label">Lý do điều trị</label>
                        <input class="form-control" value="<?= e($t['LyDoDieuTri']) ?>" disabled>
                    </div>
                    <form method="post" action="<?= e(url('doctor.treatment', ['id' => (int)$t['MaDT']])) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="MaDT" value="<?= (int)$t['MaDT'] ?>">
                        <div class="mb-3">
                            <label class="form-label" for="treatmentDiagnosis">Chẩn đoán</label>
                            <textarea class="form-control" id="treatmentDiagnosis" name="ChuanDoan" rows="2"><?= e($t['ChuanDoan']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="treatmentPlan">Kế hoạch điều trị</label>
                            <textarea class="form-control" id="treatmentPlan" name="KeHoachDieuTri" rows="3"><?= e($t['KeHoachDieuTri']) ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ngày bắt đầu</label>
                                <input class="form-control" value="<?= e(date('d/m/Y', strtotime((string)$t['NgayBatDau']))) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="treatmentEnd">Ngày kết thúc</label>
                                <input class="form-control" id="treatmentEnd" type="date" name="NgayKetThuc"
                                       value="<?= e($endDate) ?>" min="<?= e(substr((string)$t['NgayBatDau'], 0, 10)) ?>">
                                <div class="form-text">Để trống khi đang điều trị; hệ thống tự điền ngày kết thúc nếu chọn Đã kết thúc.</div>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label" for="treatmentStatus">Trạng thái đợt điều trị</label>
                            <select class="form-select" id="treatmentStatus" name="TrangThai">
                                <option value="DANG_DIEU_TRI" <?= $t['TrangThai'] === 'DANG_DIEU_TRI' ? 'selected' : '' ?>>Đang điều trị</option>
                                <option value="TAM_DUNG" <?= $t['TrangThai'] === 'TAM_DUNG' ? 'selected' : '' ?>>Tạm dừng</option>
                                <option value="DA_KET_THUC" <?= $t['TrangThai'] === 'DA_KET_THUC' ? 'selected' : '' ?>>Đã kết thúc</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="treatmentSummary">Kết quả tổng kết</label>
                            <textarea class="form-control" id="treatmentSummary" name="KetQuaTongKet" rows="3"><?= e($t['KetQuaTongKet']) ?></textarea>
                            <div class="form-text">Bắt buộc khi chuyển đợt điều trị sang trạng thái Đã kết thúc.</div>
                        </div>
                        <button class="btn btn-primary">Lưu đợt điều trị</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <h2 class="h5">Hồ sơ bệnh án</h2>
                        <?= status_badge($t['TrangThaiHoSo']) ?>
                    </div>
                    <dl class="row small mt-3 mb-0">
                        <dt class="col-5">Mã hồ sơ</dt>
                        <dd class="col-7">#<?= (int)$t['MaHSBA'] ?></dd>
                        <dt class="col-5">Bệnh nhân</dt>
                        <dd class="col-7"><?= e($t['TenBenhNhan']) ?></dd>
                        <dt class="col-5">Điện thoại</dt>
                        <dd class="col-7"><?= e($t['SDT']) ?></dd>
                        <dt class="col-5">Tiền sử bệnh</dt>
                        <dd class="col-7"><?= e($t['TienSuBenh']) ?: '—' ?></dd>
                        <dt class="col-5">Dị ứng</dt>
                        <dd class="col-7"><?= e($t['DiUng']) ?: '—' ?></dd>
                        <dt class="col-5">Ghi chú</dt>
                        <dd class="col-7 mb-0"><?= e($t['GhiChuHoSo']) ?: '—' ?></dd>
                    </dl>
                    <a class="btn btn-sm btn-outline-primary mt-3" href="<?= e(url('doctor.record', ['id' => (int)$t['MaBN']])) ?>">Xem và cập nhật hồ sơ</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <strong>Các lần khám trong đợt điều trị</strong>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Mã</th>
                    <th>Thời điểm</th>
                    <th>Lý do khám</th>
                    <th>Chẩn đoán</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($visits as $v): ?>
                    <tr>
                        <td>#<?= (int)$v['MaLKham'] ?></td>
                        <td><?= e($v['NgayGioKham']) ?></td>
                        <td><?= e($v['LyDoKham']) ?></td>
                        <td><?= e($v['ChanDoan']) ?></td>
                        <td><?= status_badge($v['TrangThaiLanKham']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('doctor.visit', ['id' => (int)$v['MaLKham']])) ?>">Mở</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$visits): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">Chưa có lần khám nào trong đợt điều trị này.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
