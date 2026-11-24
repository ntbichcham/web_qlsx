<section class="container py-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Đợt điều trị</h1>
        <p class="text-secondary">Danh sách đợt điều trị do bác sĩ hiện tại phụ trách. Chọn một đợt để xem các lần khám và cập nhật kế hoạch điều trị.</p>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Mã</th>
                    <th>Bệnh nhân</th>
                    <th>Lý do</th>
                    <th>Chẩn đoán</th>
                    <th>Bắt đầu</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($treatments as $t): ?>
                    <tr>
                        <td>#<?= (int)$t['MaDT'] ?></td>
                        <td><?= e($t['TenBenhNhan']) ?></td>
                        <td><?= e($t['LyDoDieuTri']) ?></td>
                        <td><?= e($t['ChuanDoan']) ?></td>
                        <td><?= e($t['NgayBatDau']) ?></td>
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
