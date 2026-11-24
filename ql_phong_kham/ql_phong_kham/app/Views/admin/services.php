<section class="container py-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Quản lý dịch vụ</h1>
        <p class="text-secondary">Thêm, cập nhật dịch vụ và ngừng áp dụng dịch vụ không còn cung cấp.</p>
    </div>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5">Thêm dịch vụ</h2>
                    <form method="post" action="<?= e(url('admin.services')) ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Tên dịch vụ</label>
                            <input class="form-control" name="TenDV" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="MoTa" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thời lượng (phút)</label>
                            <input class="form-control" type="number" name="ThoiLuong" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá (VNĐ)</label>
                            <input class="form-control" type="number" name="GiaDV" min="0" step="1000" required>
                        </div>
                        <button class="btn btn-primary">Thêm dịch vụ</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Dịch vụ</th>
                            <th>Thời lượng</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($services as $s): ?>
                            <tr>
                                <td><?= (int)$s['MaDV'] ?></td>
                                <td><?= e($s['TenDV']) ?></td>
                                <td><?= (int)$s['ThoiLuong'] ?> phút</td>
                                <td><?= e(money($s['GiaDV'])) ?></td>
                                <td><?= status_badge($s['TrangThai']) ?></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a class="btn btn-sm btn-outline-primary" href="<?= e(url('admin.service.edit', ['id' => (int)$s['MaDV']])) ?>">Sửa</a>
                                        <form method="post" action="<?= e(url('admin.service.status')) ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="MaDV" value="<?= (int)$s['MaDV'] ?>">
                                            <input type="hidden" name="TrangThai" value="<?= $s['TrangThai'] === 'DANG_AP_DUNG' ? 'NGUNG_AP_DUNG' : 'DANG_AP_DUNG' ?>">
                                            <button class="btn btn-sm btn-outline-secondary"><?= $s['TrangThai'] === 'DANG_AP_DUNG' ? 'Ngừng áp dụng' : 'Kích hoạt' ?></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$services): ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Chưa có dịch vụ.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
