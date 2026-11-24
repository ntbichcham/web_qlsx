<?php $r = $record; ?>
<section class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="eyebrow">HỒ SƠ BỆNH ÁN</span>
            <h1 class="h2 fw-bold mb-1"><?= e($r['HoTen']) ?></h1>
            <p class="text-secondary mb-0">
                Mã bệnh nhân #<?= (int)$r['MaBN'] ?> · Hồ sơ #<?= (int)$r['MaHSBA'] ?> · <?= status_badge($r['TrangThai']) ?>
            </p>
        </div>
        <a class="btn btn-outline-secondary" href="<?= e(url('doctor.records')) ?>">← Danh sách hồ sơ</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Thông tin bệnh án</h2>
                    <form method="post" action="<?= e(url('doctor.record', ['id' => (int)$r['MaBN']])) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="MaBN" value="<?= (int)$r['MaBN'] ?>">
                        <div class="mb-3">
                            <label class="form-label" for="recordHistory">Tiền sử bệnh</label>
                            <textarea class="form-control" id="recordHistory" name="TienSuBenh" rows="3"><?= e($r['TienSuBenh']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="recordAllergy">Dị ứng</label>
                            <textarea class="form-control" id="recordAllergy" name="DiUng" rows="2"><?= e($r['DiUng']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="recordNote">Ghi chú</label>
                            <textarea class="form-control" id="recordNote" name="GhiChu" rows="3"><?= e($r['GhiChu']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="recordStatus">Trạng thái hồ sơ</label>
                            <select class="form-select" id="recordStatus" name="TrangThai">
                                <option value="DANG_THEO_DOI" <?= $r['TrangThai'] === 'DANG_THEO_DOI' ? 'selected' : '' ?>>Đang theo dõi</option>
                                <option value="DA_DONG" <?= $r['TrangThai'] === 'DA_DONG' ? 'selected' : '' ?>>Đã đóng</option>
                            </select>
                        </div>
                        <button class="btn btn-primary">Lưu hồ sơ bệnh án</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5">Thông tin bệnh nhân</h2>
                    <dl class="row small mb-0">
                        <dt class="col-5">Họ tên</dt>
                        <dd class="col-7"><?= e($r['HoTen']) ?></dd>
                        <dt class="col-5">Điện thoại</dt>
                        <dd class="col-7"><?= e($r['SDT']) ?></dd>
                        <dt class="col-5">Email</dt>
                        <dd class="col-7"><?= e($r['Email']) ?: '—' ?></dd>
                        <dt class="col-5">Ngày sinh</dt>
                        <dd class="col-7"><?= $r['NgaySinh'] ? e(date('d/m/Y', strtotime((string)$r['NgaySinh']))) : '—' ?></dd>
                        <dt class="col-5">Giới tính</dt>
                        <dd class="col-7"><?= e($r['GioiTinh']) ?: '—' ?></dd>
                        <dt class="col-5">Địa chỉ</dt>
                        <dd class="col-7"><?= e($r['DiaChi']) ?: '—' ?></dd>
                        <dt class="col-5">Ngày lập</dt>
                        <dd class="col-7 mb-0"><?= e(date('d/m/Y', strtotime((string)$r['NgayLap']))) ?></dd>
                    </dl>
                </div>
            </div>
            <div class="alert alert-warning small mt-3">
                Hồ sơ bệnh án chứa dữ liệu chuyên môn. Chỉ bác sĩ phụ trách được xem và cập nhật trong phạm vi điều trị.
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <strong>Các đợt điều trị của bác sĩ hiện tại</strong>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Mã</th>
                    <th>Lý do điều trị</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Số lần khám</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($treatments as $t): ?>
                    <tr>
                        <td>#<?= (int)$t['MaDT'] ?></td>
                        <td><?= e($t['LyDoDieuTri']) ?></td>
                        <td><?= e(date('d/m/Y', strtotime((string)$t['NgayBatDau']))) ?></td>
                        <td><?= $t['NgayKetThuc'] ? e(date('d/m/Y', strtotime((string)$t['NgayKetThuc']))) : '—' ?></td>
                        <td><?= (int)$t['SoLanKham'] ?></td>
                        <td><?= status_badge($t['TrangThai']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('doctor.treatment', ['id' => (int)$t['MaDT']])) ?>">Chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$treatments): ?>
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">Chưa có đợt điều trị.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
