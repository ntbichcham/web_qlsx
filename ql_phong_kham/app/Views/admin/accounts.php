<?php
$roleLabels = [
    'BENH_NHAN' => 'Bệnh nhân',
    'BAC_SI' => 'Bác sĩ',
    'NHAN_VIEN' => 'Nhân viên',
    'QUAN_TRI_VIEN' => 'Quản trị viên',
];
$currentUser = current_user();
?>
<section class="container py-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Quản lý tài khoản</h1>
        <p class="text-secondary mb-0">Tạo tài khoản bác sĩ, nhân viên và quản trị viên; quản lý trạng thái tài khoản.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5">Tạo tài khoản nội bộ</h2>
                    <p class="text-secondary small">Bác sĩ và nhân viên được tạo kèm hồ sơ tương ứng. Quản trị viên chỉ cần tài khoản đăng nhập.</p>
                    <form method="post" action="<?= e(url('admin.accounts')) ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="accountUsername">Tên đăng nhập</label>
                            <input class="form-control" id="accountUsername" name="TenDN" required minlength="3" maxlength="50"
                                   pattern="[A-Za-z0-9._\-]{3,50}" autocomplete="off">
                            <div class="form-text">3-50 ký tự: chữ không dấu, số, dấu chấm, gạch dưới hoặc gạch ngang.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="accountPassword">Mật khẩu tạm (ít nhất 8 ký tự)</label>
                            <input class="form-control" id="accountPassword" type="password" name="MatKhau" minlength="8"
                                   required autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="roleSelect">Vai trò</label>
                            <select class="form-select" name="VaiTro" id="roleSelect">
                                <option value="BAC_SI">Bác sĩ</option>
                                <option value="NHAN_VIEN">Nhân viên</option>
                                <option value="QUAN_TRI_VIEN">Quản trị viên</option>
                            </select>
                        </div>

                        <div id="profileGroup">
                            <div class="mb-3">
                                <label class="form-label" for="accountName">Họ tên</label>
                                <input class="form-control" id="accountName" name="HoTen" maxlength="100" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="accountPhone">Số điện thoại</label>
                                    <input class="form-control" id="accountPhone" name="SDT" maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="accountEmail">Email</label>
                                    <input class="form-control" id="accountEmail" type="email" name="Email" maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 mt-3" id="specialtyGroup">
                            <label class="form-label" for="accountSpecialty">Chuyên môn (bác sĩ)</label>
                            <input class="form-control" id="accountSpecialty" name="ChuyenMon" maxlength="100">
                        </div>
                        <div class="mb-3 d-none" id="positionGroup">
                            <label class="form-label" for="accountPosition">Chức vụ (nhân viên)</label>
                            <input class="form-control" id="accountPosition" name="ChucVu" maxlength="100" value="Nhân viên tiếp nhận">
                        </div>
                        <div class="mb-3" id="introductionGroup">
                            <label class="form-label" for="accountIntroduction">Giới thiệu (bác sĩ)</label>
                            <textarea class="form-control" id="accountIntroduction" name="GioiThieu" rows="3"></textarea>
                        </div>

                        <div class="alert alert-info d-none" id="adminNote">
                            Tài khoản quản trị viên chỉ có tên đăng nhập và mật khẩu, không cần hồ sơ bác sĩ/nhân viên.
                            Theo phân quyền, quản trị viên không mặc nhiên xem được nội dung bệnh án hoặc đơn thuốc.
                        </div>

                        <button class="btn btn-primary">Tạo tài khoản</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Danh sách tài khoản</strong></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ tên</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($accounts as $a): ?>
                            <?php $isCurrent = (int)$a['MaND'] === (int)($currentUser['MaND'] ?? 0); ?>
                            <tr>
                                <td><?= (int)$a['MaND'] ?></td>
                                <td>
                                    <?= e($a['TenDN']) ?>
                                    <?php if ($isCurrent): ?><span class="badge text-bg-light border">bạn</span><?php endif; ?>
                                </td>
                                <td><?= e($a['HoTen'] ?? '—') ?></td>
                                <td><?= e($roleLabels[$a['VaiTro']] ?? $a['VaiTro']) ?></td>
                                <td><?= status_badge($a['TrangThai']) ?></td>
                                <td class="small text-secondary"><?= e(date('d/m/Y H:i', strtotime((string)$a['NgayTao']))) ?></td>
                                <td>
                                    <?php if ($isCurrent): ?>
                                        <span class="text-secondary small">Đang đăng nhập</span>
                                    <?php else: ?>
                                        <form method="post" action="<?= e(url('admin.account.status')) ?>" class="d-flex gap-1">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="MaND" value="<?= (int)$a['MaND'] ?>">
                                            <select class="form-select form-select-sm" name="TrangThai" aria-label="Trạng thái tài khoản">
                                                <option value="HOAT_DONG" <?= $a['TrangThai'] === 'HOAT_DONG' ? 'selected' : '' ?>>Hoạt động</option>
                                                <option value="KHOA" <?= $a['TrangThai'] === 'KHOA' ? 'selected' : '' ?>>Khóa</option>
                                            </select>
                                            <button class="btn btn-sm btn-outline-primary">Lưu</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$accounts): ?>
                            <tr><td colspan="7" class="text-center text-secondary py-4">Chưa có tài khoản nào.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
