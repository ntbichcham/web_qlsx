<?php
use App\Core\Auth;
$user = Auth::user();
$role = $user['VaiTro'] ?? '';
$menus = [
    'BENH_NHAN' => [
        ['patient.dashboard', 'Tổng quan'], ['services', 'Dịch vụ'], ['patient.book', 'Đặt lịch'],
        ['patient.appointments', 'Lịch hẹn của tôi'], ['patient.history', 'Lịch sử khám'], ['patient.profile', 'Hồ sơ cá nhân'],
    ],
    'NHAN_VIEN' => [
        ['staff.dashboard', 'Tổng quan'], ['staff.patients', 'Tiếp nhận bệnh nhân'], ['staff.book', 'Đặt lịch tại quầy'], ['staff.appointments', 'Quản lý lịch hẹn'],
    ],
    'BAC_SI' => [
        ['doctor.dashboard', 'Tổng quan'], ['doctor.schedules', 'Lịch làm việc'], ['doctor.visits', 'Lịch khám'], ['doctor.treatments', 'Đợt điều trị'],
    ],
    'QUAN_TRI_VIEN' => [
        ['admin.dashboard', 'Tổng quan'], ['admin.accounts', 'Tài khoản'], ['admin.services', 'Dịch vụ'], ['admin.schedules', 'Lịch làm việc'], ['admin.appointments', 'Lịch hẹn'],
    ],
];
$roleNames = ['BENH_NHAN'=>'Bệnh nhân','BAC_SI'=>'Bác sĩ','NHAN_VIEN'=>'Nhân viên','QUAN_TRI_VIEN'=>'Quản trị viên'];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? APP_NAME) ?> · <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/app.css')) ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark clinic-nav">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="<?= e(url('home')) ?>">🦷 Nha Khoa An Tâm</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (!$user): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('services')) ?>">Dịch vụ</a></li>
                <?php else: ?>
                    <?php foreach (($menus[$role] ?? []) as [$r, $label]): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= e(url($r)) ?>"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($user): ?>
                    <span class="text-white-50 small d-none d-md-inline"><?= e($user['HoTen'] ?? $user['TenDN']) ?> · <?= e($roleNames[$role] ?? $role) ?></span>
                    <form method="post" action="<?= e(url('auth.logout')) ?>" class="m-0"><?= csrf_field() ?><button class="btn btn-sm btn-outline-light">Đăng xuất</button></form>
                <?php else: ?>
                    <a class="btn btn-sm btn-outline-light" href="<?= e(url('auth.login')) ?>">Đăng nhập</a>
                    <a class="btn btn-sm btn-light" href="<?= e(url('auth.register')) ?>">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="min-vh-75">
    <div class="container py-3">
        <?php foreach (pull_flashes() as $message): ?>
            <div class="alert alert-<?= e($message['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($message['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    </div>
    <?= $content ?>
</main>
<footer class="border-top bg-white mt-5 py-4">
    <div class="container small text-secondary d-flex flex-column flex-md-row justify-content-between gap-2">
        <span>© <?= date('Y') ?> · Hệ thống quản lý phòng khám nha khoa</span>
        <span>Đồ án CT285 · Đề tài số 18 · <a class="link-secondary" href="<?= e(asset('images/credits.html')) ?>" target="_blank" rel="noopener">Nguồn ảnh</a></span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
