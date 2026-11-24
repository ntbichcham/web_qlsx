<?php $s = $service; ?>
<section class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="eyebrow">DANH MỤC DỊCH VỤ</span>
            <h1 class="h2 fw-bold mb-1">Cập nhật dịch vụ #<?= (int)$s['MaDV'] ?></h1>
            <p class="text-secondary mb-0"><?= e($s['TenDV']) ?></p>
        </div>
        <a class="btn btn-outline-secondary" href="<?= e(url('admin.services')) ?>">← Danh sách dịch vụ</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Thông tin dịch vụ</h2>
                    <form method="post" action="<?= e(url('admin.service.edit', ['id' => (int)$s['MaDV']])) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="MaDV" value="<?= (int)$s['MaDV'] ?>">
                        <div class="mb-3">
                            <label class="form-label" for="serviceName">Tên dịch vụ *</label>
                            <input class="form-control" id="serviceName" name="TenDV" maxlength="100"
                                   value="<?= e($s['TenDV']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="serviceDescription">Mô tả</label>
                            <textarea class="form-control" id="serviceDescription" name="MoTa" rows="4"><?= e($s['MoTa']) ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="serviceDuration">Thời lượng (phút) *</label>
                                <input class="form-control" id="serviceDuration" type="number" name="ThoiLuong"
                                       min="1" value="<?= (int)$s['ThoiLuong'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="servicePrice">Giá (VNĐ) *</label>
                                <input class="form-control" id="servicePrice" type="number" name="GiaDV"
                                       min="0" step="1000" value="<?= (int)$s['GiaDV'] ?>" required>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label" for="serviceStatus">Trạng thái áp dụng</label>
                            <select class="form-select" id="serviceStatus" name="TrangThai">
                                <option value="DANG_AP_DUNG" <?= $s['TrangThai'] === 'DANG_AP_DUNG' ? 'selected' : '' ?>>Đang áp dụng</option>
                                <option value="NGUNG_AP_DUNG" <?= $s['TrangThai'] === 'NGUNG_AP_DUNG' ? 'selected' : '' ?>>Ngừng áp dụng</option>
                            </select>
                            <div class="form-text">Dịch vụ ngừng áp dụng không hiển thị khi đặt lịch mới nhưng vẫn giữ trong lịch sử.</div>
                        </div>
                        <button class="btn btn-primary">Lưu thay đổi</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5">Lưu ý</h2>
                    <p class="text-secondary small mb-3">
                        Giá và thời lượng mới chỉ áp dụng cho lần khám phát sinh sau khi lưu. Các bản ghi dịch vụ
                        đã thực hiện vẫn giữ đơn giá tại thời điểm thực hiện.
                    </p>
                    <dl class="row small mb-0">
                        <dt class="col-6">Mã dịch vụ</dt>
                        <dd class="col-6">#<?= (int)$s['MaDV'] ?></dd>
                        <dt class="col-6">Trạng thái</dt>
                        <dd class="col-6 mb-0"><?= status_badge($s['TrangThai']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>
