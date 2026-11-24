<section class="container py-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Tiếp nhận bệnh nhân</h1>
        <p class="text-secondary mb-0">Nhân viên có thể tạo hồ sơ hành chính cho bệnh nhân chưa có tài khoản đăng nhập và cập nhật thông tin tiếp nhận.</p>
    </div>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5">Tạo hồ sơ bệnh nhân</h2>
                    <form method="post" action="<?= e(url('staff.patients')) ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Họ tên *</label>
                            <input class="form-control" name="HoTen" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input class="form-control" name="SDT">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="Email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input class="form-control" type="date" name="NgaySinh">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giới tính</label>
                            <select class="form-select" name="GioiTinh">
                                <option value="">Chưa cung cấp</option>
                                <option>Nam</option>
                                <option>Nữ</option>
                                <option>Khác</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" name="DiaChi" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">Tạo hồ sơ</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form class="d-flex gap-2 mb-3" method="get">
                        <input type="hidden" name="r" value="staff.patients">
                        <input class="form-control" name="q" placeholder="Tìm theo tên, số điện thoại, email" value="<?= e($_GET['q'] ?? '') ?>">
                        <button class="btn btn-outline-primary">Tìm</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Họ tên</th>
                                <th>Điện thoại</th>
                                <th>Tài khoản</th>
                                <th>Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($patients as $p): ?>
                                <tr>
                                    <td><?= (int)$p['MaBN'] ?></td>
                                    <td><?= e($p['HoTen']) ?></td>
                                    <td><?= e($p['SDT']) ?></td>
                                    <td><?= $p['MaND'] ? 'Đã liên kết' : 'Chưa có' ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="<?= e(url('staff.patient.edit', ['id' => (int)$p['MaBN']])) ?>">Sửa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$patients): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">Chưa có hồ sơ bệnh nhân.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
