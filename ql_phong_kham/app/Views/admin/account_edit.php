<?php
$a = $account;
$roleLabels = [
    'BENH_NHAN' => 'Bệnh nhân',
    'BAC_SI' => 'Bác sĩ',
    'NHAN_VIEN' => 'Nhân viên',
    'QUAN_TRI_VIEN' => 'Quản trị viên',
];
$role = (string)$a['VaiTro'];
$name = $a['TenBS'] ?? ($a['TenNV'] ?? ($a['TenBN'] ?? ''));
$phone = $a['SDTBS'] ?? ($a['SDTNV'] ?? '');
$email = $a['EmailBS'] ?? ($a['EmailNV'] ?? '');
$isDoctor = $role === 'BAC_SI';
$isStaff = $role === 'NHAN_VIEN';
$hasProfile = $isDoctor || $isStaff;
$currentUser = current_user();
$isSelf = (int)$a['MaND'] === (int)($currentUser['MaND'] ?? 0);
?>
<section class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="eyebrow">QUẢN LÝ TÀI KHOẢN</span>
            <h1 class="h2 fw-bold mb-1">Cập nhật tài khoản #<?= (int)$a['MaND'] ?></h1>
            <p class="text-secondary mb-0">
                <?= e($a['TenDN']) ?> · <?= e($roleLabels[$role] ?? $role) ?>
                <?php if ($isSelf): ?><span class="badge text-bg-light border">tài khoản đang đăng nhập</span><?php endif; ?>
            </p>
        </div>
        <a class="btn btn-outline-secondary" href="<?= e(url('admin.accounts')) ?>">← Danh sách tài khoản</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Thông tin tài khoản và hồ sơ</h2>
                    <form method="post" action="<?= e(url('admin.account.edit', ['id' => (int)$a['MaND']])) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="MaND" value="<?= (int)$a['MaND'] ?>">

                        <?php if ($hasProfile): ?>
                            <div class="mb-3">
                                <label class="form-label" for="editName">Họ tên *</label>
                                <input class="form-control" id="editName" name="HoTen" maxlength="100"
                                       value="<?= e($name) ?>" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="editPhone">Số điện thoại</label>
                                    <input class="form-control" id="editPhone" name="SDT" maxlength="20"
                                           value="<?= e($phone) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="editEmail">Email</label>
                                    <input class="form-control" id="editEmail" type="email" name="Email" maxlength="100"
                                           value="<?= e($email) ?>">
                                </div>
                            </div>

                            <?php if ($isDoctor): ?>
                                <div class="mb-3 mt-3">
                                    <label class="form-label" for="editSpecialty">Chuyên môn</label>
                                    <input class="form-control" id="editSpecialty" name="ChuyenMon" maxlength="100"
                                           value="<?= e($a['ChuyenMon']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="editIntroduction">Giới thiệu</label>
                                    <textarea class="form-control" id="editIntroduction" name="GioiThieu" rows="3"><?= e($a['GioiThieu']) ?></textarea>
                                </div>
                            <?php else: ?>
                                <div class="mb-3 mt-3">
                                    <label class="form-label" for="editPosition">Chức vụ</label>
                                    <input class="form-control" id="editPosition" name="ChucVu" maxlength="100"
                                           value="<?= e($a['ChucVu']) ?>">
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Tài khoản <?= e(mb_strtolower($roleLabels[$role] ?? $role)) ?>
                                không có hồ sơ bác sĩ/nhân viên. Chỉ có thể đặt lại mật khẩu tại đây.
                                <?php if ($role === 'BENH_NHAN'): ?>
                                    Thông tin cá nhân của bệnh nhân được cập nhật tại hồ sơ bệnh nhân.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">
                        <h3 class="h6">Đặt lại mật khẩu</h3>
                        <p class="text-secondary small mb-2">Bỏ trống nếu không muốn thay đổi mật khẩu hiện tại.</p>
                        <div class="mb-3">
                            <label class="form-label" for="editPassword">Mật khẩu mới</label>
                            <input class="form-control" id="editPassword" type="password" name="MatKhau" minlength="8"
                                   autocomplete="new-password">
                            <div class="form-text">Tối thiểu 8 ký tự.</div>
                        </div>

                        <button class="btn btn-primary">Lưu thay đổi</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5">Thông tin chung</h2>
                    <dl class="row small mb-0">
                        <dt class="col-5">Mã tài khoản</dt>
                        <dd class="col-7">#<?= (int)$a['MaND'] ?></dd>
                        <dt class="col-5">Tên đăng nhập</dt>
                        <dd class="col-7"><?= e($a['TenDN']) ?></dd>
                        <dt class="col-5">Vai trò</dt>
                        <dd class="col-7"><?= e($roleLabels[$role] ?? $role) ?></dd>
                        <dt class="col-5">Trạng thái</dt>
                        <dd class="col-7"><?= status_badge($a['TrangThai']) ?></dd>
                        <dt class="col-5">Ngày tạo</dt>
                        <dd class="col-7 mb-0"><?= e(date('d/m/Y H:i', strtotime((string)$a['NgayTao']))) ?></dd>
                    </dl>
                    <hr>
                    <p class="text-secondary small mb-0">
                        Trạng thái hoạt động được thay đổi tại danh sách tài khoản để tránh vô tình khóa tài khoản đang dùng.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
