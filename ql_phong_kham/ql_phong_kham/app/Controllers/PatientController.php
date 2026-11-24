<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\Clinical;
use PDOException;
use Throwable;

class PatientController extends Controller
{
    public function profile(): void
    {
        $this->requireRole(['BENH_NHAN']);
        $model = new Patient();
        $patient = $model->findByUser((int)Auth::user()['MaND']);
        if (!$patient) { flash('danger', 'Không tìm thấy hồ sơ bệnh nhân.'); redirect_to('patient.dashboard'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            $data = $this->profileData();
            if ($data['HoTen'] === '') { flash('danger', 'Họ tên không được để trống.'); redirect_to('patient.profile'); }
            $model->update((int)$patient['MaBN'], $data);
            flash('success', 'Đã cập nhật hồ sơ cá nhân.');
            redirect_to('patient.profile');
        }
        $this->view('patient/profile', ['pageTitle' => 'Hồ sơ cá nhân', 'patient' => $patient]);
    }

    public function appointments(): void
    {
        $this->requireRole(['BENH_NHAN']);
        $patient = (new Patient())->findByUser((int)Auth::user()['MaND']);
        $appointments = $patient ? (new Appointment())->forPatient((int)$patient['MaBN']) : [];
        $this->view('patient/appointments', ['pageTitle' => 'Lịch hẹn của tôi', 'appointments' => $appointments]);
    }

    public function book(): void
    {
        $this->requireRole(['BENH_NHAN']);
        $patient = (new Patient())->findByUser((int)Auth::user()['MaND']);
        if (!$patient) { flash('danger', 'Tài khoản chưa được liên kết với hồ sơ bệnh nhân.'); redirect_to('patient.profile'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            try {
                (new Appointment())->create([
                    'MaBN' => $patient['MaBN'], 'MaLLV' => $_POST['MaLLV'] ?? 0,
                    'MaDV' => $_POST['MaDV'] ?? 0, 'NgayKham' => $_POST['NgayKham'] ?? '',
                    'GioBatDau' => $_POST['GioBatDau'] ?? '', 'GhiChu' => trim((string)($_POST['GhiChu'] ?? '')),
                ], null);
                flash('success', 'Đã gửi yêu cầu đặt lịch. Vui lòng theo dõi trạng thái xác nhận.');
                redirect_to('patient.appointments');
            } catch (Throwable $e) {
                flash('danger', $e->getMessage());
                redirect_to('patient.book');
            }
        }
        $this->view('patient/book', ['pageTitle' => 'Đặt lịch khám', 'services' => (new Service())->all(true), 'schedules' => (new Schedule())->upcoming()]);
    }

    public function cancelAppointment(): void
    {
        $this->requireRole(['BENH_NHAN']);
        $this->postOnly();
        $patient = (new Patient())->findByUser((int)Auth::user()['MaND']);
        $id = (int)($_POST['MaLH'] ?? 0);
        if (!$patient) redirect_to('patient.appointments');
        try {
            $db = Database::connection();
            $stmt = $db->prepare("UPDATE LICH_HEN_KHAM SET TrangThai='DA_HUY' WHERE MaLH=? AND MaBN=? AND TrangThai IN ('CHO_XAC_NHAN','DA_XAC_NHAN') AND NgayKham >= CURDATE()");
            $stmt->execute([$id, (int)$patient['MaBN']]);
            flash($stmt->rowCount() ? 'success' : 'warning', $stmt->rowCount() ? 'Đã hủy lịch hẹn.' : 'Không thể hủy lịch này. Vui lòng liên hệ phòng khám.');
        } catch (Throwable $e) { flash('danger', 'Không thể cập nhật lịch hẹn.'); }
        redirect_to('patient.appointments');
    }

    public function history(): void
    {
        $this->requireRole(['BENH_NHAN']);
        $patient = (new Patient())->findByUser((int)Auth::user()['MaND']);
        $visits = $patient ? (new Clinical())->patientHistory((int)$patient['MaBN']) : [];
        $this->view('patient/history', ['pageTitle' => 'Lịch sử khám của tôi', 'visits' => $visits]);
    }

    private function profileData(): array
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
