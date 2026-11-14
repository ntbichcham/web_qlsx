<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PatientController;
use App\Controllers\StaffController;
use App\Controllers\AdminController;
use App\Controllers\DoctorController;

$routes = [
    'home' => [HomeController::class, 'index', 'GET'],
    'services' => [HomeController::class, 'services', 'GET'],
    'auth.login' => [AuthController::class, 'login', 'BOTH'],
    'auth.register' => [AuthController::class, 'register', 'BOTH'],
    'auth.logout' => [AuthController::class, 'logout', 'POST'],
    'patient.dashboard' => [DashboardController::class, 'patient', 'GET'],
    'staff.dashboard' => [DashboardController::class, 'staff', 'GET'],
    'admin.dashboard' => [DashboardController::class, 'admin', 'GET'],
    'doctor.dashboard' => [DashboardController::class, 'doctor', 'GET'],
    'patient.profile' => [PatientController::class, 'profile', 'BOTH'],
    'patient.appointments' => [PatientController::class, 'appointments', 'GET'],
    'patient.book' => [PatientController::class, 'book', 'BOTH'],
    'patient.cancel' => [PatientController::class, 'cancelAppointment', 'POST'],
    'patient.history' => [PatientController::class, 'history', 'GET'],
    'staff.patients' => [StaffController::class, 'patients', 'BOTH'],
    'staff.appointments' => [StaffController::class, 'appointments', 'GET'],
    'staff.book' => [StaffController::class, 'book', 'BOTH'],
    'staff.appointment.update' => [StaffController::class, 'updateAppointment', 'POST'],
    'admin.accounts' => [AdminController::class, 'accounts', 'BOTH'],
    'admin.account.status' => [AdminController::class, 'updateAccountStatus', 'POST'],
    'admin.services' => [AdminController::class, 'services', 'BOTH'],
    'admin.service.status' => [AdminController::class, 'serviceStatus', 'POST'],
    'admin.schedules' => [AdminController::class, 'schedules', 'BOTH'],
    'admin.schedule.status' => [AdminController::class, 'scheduleStatus', 'POST'],
    'admin.appointments' => [AdminController::class, 'appointments', 'GET'],
    'doctor.visits' => [DoctorController::class, 'visits', 'GET'],
    'doctor.visit.start' => [DoctorController::class, 'startVisit', 'POST'],
    'doctor.visit' => [DoctorController::class, 'visit', 'GET'],
    'doctor.visit.save' => [DoctorController::class, 'saveVisit', 'POST'],
    'doctor.visit.service.add' => [DoctorController::class, 'addService', 'POST'],
    'doctor.prescription.create' => [DoctorController::class, 'createPrescription', 'POST'],
    'doctor.medicine.add' => [DoctorController::class, 'addMedicine', 'POST'],
    'doctor.treatments' => [DoctorController::class, 'treatments', 'GET'],
    'doctor.schedules' => [DoctorController::class, 'schedules', 'GET'],
    'doctor.schedule.create' => [DoctorController::class, 'saveSchedule', 'POST'],
    'doctor.schedule.cancel' => [DoctorController::class, 'cancelSchedule', 'POST'],
];

$route = (string)($_GET['r'] ?? 'home');
$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if (!isset($routes[$route])) {
    http_response_code(404);
    render_error(404, 'Trang bạn yêu cầu không tồn tại.');
    exit;
}
[$class, $action, $allowed] = $routes[$route];
if ($allowed !== 'BOTH' && $allowed !== $method) {
    http_response_code(405);
    render_error(405, 'Phương thức yêu cầu không hợp lệ.');
    exit;
}

try {
    (new $class())->$action();
} catch (Throwable $e) {
    $log = '[' . date('Y-m-d H:i:s') . '] ' . $e . PHP_EOL;
    @file_put_contents(dirname(__DIR__) . '/storage/logs/app.log', $log, FILE_APPEND);
    http_response_code(500);
    if (APP_DEBUG) {
        render_error(500, $e->getMessage());
    } else {
        render_error(500, 'Đã xảy ra lỗi hệ thống. Hãy kiểm tra cấu hình database hoặc file storage/logs/app.log.');
    }
}
