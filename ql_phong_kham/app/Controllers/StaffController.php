<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use Throwable;

class StaffController extends Controller
{
    public function patients(): void
    {
        $this->requireRole(['NHAN_VIEN']);
        $model = new Patient();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            $data = $this->patientData();
            if ($data['HoTen'] === '') { flash('danger', 'Họ tên bệnh nhân không được để trống.'); redirect_to('staff.patients'); }
            try {
                $id = $model->create($data, null);
                flash('success', 'Đã tạo hồ sơ bệnh nhân #' . $id . ' (chưa có tài khoản đăng nhập).');
            } catch (Throwable $e) { flash('danger', 'Không thể tạo hồ sơ. Vui lòng kiểm tra dữ liệu.'); }
            redirect_to('staff.patients');
        }
        $this->view('staff/patients', ['pageTitle' => 'Tiếp nhận bệnh nhân', 'patients' => $model->all(trim((string)($_GET['q'] ?? '')))]);
    }

    public function appointments(): void
    {
        $this->requireRole(['NHAN_VIEN']);
        $this->view('staff/appointments', ['pageTitle' => 'Quản lý lịch hẹn', 'appointments' => (new Appointment())->all()]);
    }

    public function book(): void
    {
        $this->requireRole(['NHAN_VIEN']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            try {
                $staff = new \App\Models\Staff();
                $profile = $staff->findByUser((int)Auth::user()['MaND']);
                if (!$profile) throw new \RuntimeException('Tài khoản chưa có hồ sơ nhân viên. Vui lòng liên hệ quản trị viên.');
                $id = (new Appointment())->create([
                    'MaBN' => $_POST['MaBN'] ?? 0, 'MaLLV' => $_POST['MaLLV'] ?? 0,
                    'MaDV' => $_POST['MaDV'] ?? 0, 'NgayKham' => $_POST['NgayKham'] ?? '',
                    'GioBatDau' => $_POST['GioBatDau'] ?? '', 'GhiChu' => trim((string)($_POST['GhiChu'] ?? '')),
                ], (int)$profile['MaNV']);
                flash('success', 'Đã tạo lịch hẹn #' . $id . ' thay bệnh nhân.');
                redirect_to('staff.appointments');
            } catch (Throwable $e) { flash('danger', $e->getMessage()); redirect_to('staff.book'); }
        }
        $this->view('staff/book', [
            'pageTitle' => 'Đặt lịch tại quầy',
            'patients' => (new Patient())->all(),
            'schedules' => (new Schedule())->upcoming(),
            'services' => (new Service())->all(true),
        ]);
    }

    public function updateAppointment(): void
    {
        $this->requireRole(['NHAN_VIEN']);
        $this->postOnly();
        try {
            (new Appointment())->updateStatus((int)($_POST['MaLH'] ?? 0), (string)($_POST['TrangThai'] ?? ''));
            flash('success', 'Đã cập nhật trạng thái lịch hẹn.');
        } catch (Throwable $e) { flash('danger', $e->getMessage()); }
        redirect_to('staff.appointments');
    }

    private function patientData(): array
    {
        return [
            'HoTen' => trim((string)($_POST['HoTen'] ?? '')),
            'SDT' => trim((string)($_POST['SDT'] ?? '')),
            'Email' => trim((string)($_POST['Email'] ?? '')),
            'DiaChi' => trim((string)($_POST['DiaChi'] ?? '')),
            'GioiTinh' => trim((string)($_POST['GioiTinh'] ?? '')),
            'NgaySinh' => trim((string)($_POST['NgaySinh'] ?? '')),
        ];
    }
}
