<?php
$p = $patient;
$birthDate = $p['NgaySinh'] ? substr((string)$p['NgaySinh'], 0, 10) : '';
?>
<section class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="eyebrow">TIẾP NHẬN BỆNH NHÂN</span>
            <h1 class="h2 fw-bold mb-1">Cập nhật hồ sơ bệnh nhân #<?= (int)$p['MaBN'] ?></h1>
            <p class="text-secondary mb-0">
                <?= e($p['HoTen']) ?> ·
                <?= $p['MaND'] ? 'Đã liên kết tài khoản đăng nhập' : 'Chưa có tài khoản đăng nhập' ?>
            </p>
        </div>
        <a class="btn btn-outline-secondary" href="<?= e(url('staff.patients')) ?>">← Danh sách bệnh nhân</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Thông tin hành chính</h2>
                    <form method="post" action="<?= e(url('staff.patient.edit', ['id' => (int)$p['MaBN']])) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="MaBN" value="<?= (int)$p['MaBN'] ?>">
                        <div class="mb-3">
                            <label class="form-label" for="patientName">Họ tên *</label>
                            <input class="form-control" id="patientName" name="HoTen" maxlength="100"
                                   value="<?= e($p['HoTen']) ?>" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="patientPhone">Số điện thoại</label>
                                <input class="form-control" id="patientPhone" name="SDT" maxlength="20"
                                       value="<?= e($p['SDT']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="patientEmail">Email</label>
                                <input class="form-control" id="patientEmail" type="email" name="Email" maxlength="100"
                                       value="<?= e($p['Email']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="patientBirth">Ngày sinh</label>
                                <input class="form-control" id="patientBirth" type="date" name="NgaySinh"
                                       value="<?= e($birthDate) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="patientGender">Giới tính</label>
                                <select class="form-select" id="patientGender" name="GioiTinh">
                                    <option value="">Chưa cung cấp</option>
                                    <?php foreach (['Nam', 'Nữ', 'Khác'] as $gender): ?>
                                        <option value="<?= e($gender) ?>" <?= $p['GioiTinh'] === $gender ? 'selected' : '' ?>><?= e($gender) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label" for="patientAddress">Địa chỉ</label>
                            <textarea class="form-control" id="patientAddress" name="DiaChi" rows="2"><?= e($p['DiaChi']) ?></textarea>
                        </div>
                        <button class="btn btn-primary">Lưu thay đổi</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5">Lưu ý nghiệp vụ</h2>
                    <p class="text-secondary small mb-3">
                        Nhân viên chỉ cập nhật thông tin hành chính phục vụ tiếp nhận. Nội dung bệnh án,
                        chẩn đoán và đơn thuốc không hiển thị trong khu vực này.
                    </p>
                    <dl class="row small mb-0">
                        <dt class="col-5">Mã bệnh nhân</dt>
                        <dd class="col-7">#<?= (int)$p['MaBN'] ?></dd>
                        <dt class="col-5">Tài khoản</dt>
                        <dd class="col-7"><?= $p['MaND'] ? '#' . (int)$p['MaND'] : 'Chưa liên kết' ?></dd>
                        <dt class="col-5">Trạng thái hồ sơ</dt>
                        <dd class="col-7 mb-0">Đang quản lý</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>
