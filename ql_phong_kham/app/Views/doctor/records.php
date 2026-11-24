<section class="container py-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Hồ sơ bệnh án</h1>
        <p class="text-secondary mb-0">Bệnh nhân đã có đợt điều trị do bác sĩ hiện tại phụ trách. Chỉ hiển thị hồ sơ trong phạm vi được phân công.</p>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Mã BN</th>
                    <th>Họ tên</th>
                    <th>Điện thoại</th>
                    <th>Ngày sinh</th>
                    <th>Hồ sơ</th>
                    <th>Số đợt điều trị</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($patients as $p): ?>
                    <tr>
                        <td>#<?= (int)$p['MaBN'] ?></td>
                        <td><?= e($p['HoTen']) ?></td>
                        <td><?= e($p['SDT']) ?></td>
                        <td><?= $p['NgaySinh'] ? e(date('d/m/Y', strtotime((string)$p['NgaySinh']))) : '—' ?></td>
                        <td><?= status_badge($p['TrangThaiHoSo']) ?></td>
                        <td><?= (int)$p['SoDotDieuTri'] ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('doctor.record', ['id' => (int)$p['MaBN']])) ?>">Mở hồ sơ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$patients): ?>
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">Chưa có bệnh nhân nào trong phạm vi phụ trách.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
