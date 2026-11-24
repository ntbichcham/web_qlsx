<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Clinical;
use App\Models\Schedule;
use App\Models\Service;
use Throwable;

class DoctorController extends Controller
{
    private function doctorProfile(): array
    {
        $doctor = (new Doctor())->findByUser((int)Auth::user()['MaND']);
        if (!$doctor) { throw new \RuntimeException('Tài khoản chưa có hồ sơ bác sĩ. Vui lòng liên hệ quản trị viên.'); }
        return $doctor;
    }

    public function visits(): void
    {
        $this->requireRole(['BAC_SI']);
        $doctor = $this->doctorProfile();
        $this->view('doctor/visits', ['pageTitle' => 'Lịch khám được phân công', 'appointments' => (new Appointment())->forDoctor((int)$doctor['MaBS'])]);
    }

    public function startVisit(): void
    {
        $this->requireRole(['BAC_SI']);
        $this->postOnly();
        $doctor = $this->doctorProfile();
        $appointmentId = (int)($_POST['MaLH'] ?? 0);
        try {
            $appointment = (new Appointment())->findForDoctor($appointmentId, (int)$doctor['MaBS']);
            if (!$appointment) throw new \RuntimeException('Lịch hẹn không thuộc phạm vi bác sĩ được phân công.');
            if ($appointment['TrangThai'] === 'DA_HUY') throw new \RuntimeException('Lịch hẹn đã hủy, không thể tiếp nhận.');
            $visitId = (new Clinical())->createVisitFromAppointment($appointment, (int)$doctor['MaBS']);
            flash('success', 'Đã mở hồ sơ lần khám.');
            redirect_to('doctor.visit', ['id' => $visitId]);
        } catch (Throwable $e) { flash('danger', $e->getMessage()); redirect_to('doctor.visits'); }
    }

    public function visit(): void
    {
        $this->requireRole(['BAC_SI']);
        $doctor = $this->doctorProfile();
        $id = (int)($_GET['id'] ?? 0);
        $clinical = new Clinical();
        $visit = $clinical->visitForDoctor($id, (int)$doctor['MaBS']);
        if (!$visit) { http_response_code(404); render_error(404, 'Không tìm thấy lần khám.'); return; }
        $db = \App\Core\Database::connection();
        $stmt = $db->prepare('SELECT x.*, s.TenDV FROM DICH_VU_LAN_KHAM x JOIN DICH_VU s ON s.MaDV=x.MaDV WHERE x.MaLKham=?');
        $stmt->execute([$id]);
        $items = $stmt->fetchAll();
        $prescription = $clinical->prescriptionForVisit($id);
        $medicines = $prescription ? $clinical->medicineRows((int)$prescription['MaDonThuoc']) : [];
        $this->view('doctor/visit', ['pageTitle' => 'Chi tiết lần khám', 'visit' => $visit, 'services' => (new Service())->all(true), 'items' => $items, 'prescription' => $prescription, 'medicines' => $medicines]);
    }

    public function saveVisit(): void
    {
        $this->requireRole(['BAC_SI']);
        $this->postOnly();
        $doctor = $this->doctorProfile();
        $id = (int)($_POST['MaLKham'] ?? 0);
        try {
            (new Clinical())->updateVisit($id, (int)$doctor['MaBS'], [
                'LyDoKham' => trim((string)($_POST['LyDoKham'] ?? '')),
                'TrieuChung' => trim((string)($_POST['TrieuChung'] ?? '')),
                'ChanDoan' => trim((string)($_POST['ChanDoan'] ?? '')),
                'KetQuaKham' => trim((string)($_POST['KetQuaKham'] ?? '')),
                'HuongXuLy' => trim((string)($_POST['HuongXuLy'] ?? '')),
                'GhiChu' => trim((string)($_POST['GhiChu'] ?? '')),
                'TrangThaiLanKham' => (string)($_POST['TrangThaiLanKham'] ?? 'DA_TIEP_NHAN'),
            ]);
            flash('success', 'Đã lưu thông tin lần khám.');
        } catch (Throwable $e) { flash('danger', $e->getMessage()); }
        redirect_to('doctor.visit', ['id' => $id]);
    }

    public function addService(): void
    {
        $this->requireRole(['BAC_SI']);
        $this->postOnly();
        $doctor = $this->doctorProfile();
        $id = (int)($_POST['MaLKham'] ?? 0);
        try {
            (new Clinical())->addVisitService($id, (int)$doctor['MaBS'], ['MaDV' => $_POST['MaDV'] ?? 0, 'SoLuong' => $_POST['SoLuong'] ?? 1, 'GhiChu' => $_POST['GhiChu'] ?? '']);
            flash('success', 'Đã ghi nhận dịch vụ thực hiện.');
        } catch (Throwable $e) { flash('danger', $e->getMessage()); }
        redirect_to('doctor.visit', ['id' => $id]);
    }

    public function createPrescription(): void
    {
        $this->requireRole(['BAC_SI']);
        $this->postOnly();
        $doctor = $this->doctorProfile();
        $id = (int)($_POST['MaLKham'] ?? 0);
        try {
            $prescriptionId = (new Clinical())->createPrescription($id, (int)$doctor['MaBS'], trim((string)($_POST['LoiDan'] ?? '')));
            flash('success', 'Đã tạo hoặc mở đơn thuốc #' . $prescriptionId . '.');
        } catch (Throwable $e) { flash('danger', $e->getMessage()); }
        redirect_to('doctor.visit', ['id' => $id]);
    }

    public function addMedicine(): void
    {
        $this->requireRole(['BAC_SI']);
        $this->postOnly();
        $doctor = $this->doctorProfile();
        $visitId = (int)($_POST['MaLKham'] ?? 0);
        $prescriptionId = (int)($_POST['MaDonThuoc'] ?? 0);
        try {
            (new Clinical())->addMedicine($prescriptionId, (int)$doctor['MaBS'], [
                'TenThuoc' => trim((string)($_POST['TenThuoc'] ?? '')),
                'SoLuong' => $_POST['SoLuong'] ?? 0,
                'LieuLuong' => trim((string)($_POST['LieuLuong'] ?? '')),
                'CachDung' => trim((string)($_POST['CachDung'] ?? '')),
                'ThoiGianBatDau' => $_POST['ThoiGianBatDau'] ?? null,
                'ThoiGianKetThuc' => $_POST['ThoiGianKetThuc'] ?? null,
            ]);
            flash('success', 'Đã thêm thuốc vào đơn.');
        } catch (Throwable $e) { flash('danger', $e->getMessage()); }
        redirect_to('doctor.visit', ['id' => $visitId]);
    }

    public function treatments(): void
    {
        $this->requireRole(['BAC_SI']);
        $doctor = $this->doctorProfile();
        $this->view('doctor/treatments', ['pageTitle' => 'Đợt điều trị', 'treatments' => (new Clinical())->treatmentsForDoctor((int)$doctor['MaBS'])]);
    }

    public function treatment(): void
    {
        $this->requireRole(['BAC_SI']);
        $doctor = $this->doctorProfile();
        $clinical = new Clinical();
        $id = (int)($_GET['id'] ?? $_POST['MaDT'] ?? 0);
        $treatment = $clinical->treatmentForDoctor($id, (int)$doctor['MaBS']);
        if (!$treatment) {
            http_response_code(404);
            render_error(404, 'Không tìm thấy đợt điều trị.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            try {
                $clinical->updateTreatment($id, (int)$doctor['MaBS'], [
                    'ChuanDoan' => trim((string)($_POST['ChuanDoan'] ?? '')),
                    'KeHoachDieuTri' => trim((string)($_POST['KeHoachDieuTri'] ?? '')),
                    'NgayKetThuc' => trim((string)($_POST['NgayKetThuc'] ?? '')),
                    'TrangThai' => (string)($_POST['TrangThai'] ?? ''),
                    'KetQuaTongKet' => trim((string)($_POST['KetQuaTongKet'] ?? '')),
                ]);
                flash('success', 'Đã cập nhật đợt điều trị #' . $id . '.');
                redirect_to('doctor.treatment', ['id' => $id]);
            } catch (Throwable $e) {
                flash('danger', $e->getMessage());
                redirect_to('doctor.treatment', ['id' => $id]);
            }
        }

        $this->view('doctor/treatment', [
            'pageTitle' => 'Đợt điều trị',
            'treatment' => $treatment,
            'visits' => $clinical->visitsForTreatment($id, (int)$doctor['MaBS']),
        ]);
    }

    public function records(): void
    {
        $this->requireRole(['BAC_SI']);
        $doctor = $this->doctorProfile();
        $this->view('doctor/records', [
            'pageTitle' => 'Hồ sơ bệnh án',
            'patients' => (new Clinical())->patientsForDoctor((int)$doctor['MaBS']),
        ]);
    }

    public function record(): void
    {
        $this->requireRole(['BAC_SI']);
        $doctor = $this->doctorProfile();
        $clinical = new Clinical();
        $patientId = (int)($_GET['id'] ?? $_POST['MaBN'] ?? 0);
        $record = $clinical->recordForDoctor($patientId, (int)$doctor['MaBS']);
        if (!$record) {
            http_response_code(404);
            render_error(404, 'Không tìm thấy hồ sơ bệnh án trong phạm vi được phân công.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            try {
                $clinical->updateMedicalRecord((int)$record['MaHSBA'], (int)$doctor['MaBS'], [
                    'TienSuBenh' => trim((string)($_POST['TienSuBenh'] ?? '')),
                    'DiUng' => trim((string)($_POST['DiUng'] ?? '')),
                    'GhiChu' => trim((string)($_POST['GhiChu'] ?? '')),
                    'TrangThai' => (string)($_POST['TrangThai'] ?? ''),
                ]);
                flash('success', 'Đã cập nhật hồ sơ bệnh án của ' . $record['HoTen'] . '.');
                redirect_to('doctor.record', ['id' => $patientId]);
            } catch (Throwable $e) {
                flash('danger', $e->getMessage());
                redirect_to('doctor.record', ['id' => $patientId]);
            }
        }

        $this->view('doctor/record', [
            'pageTitle' => 'Hồ sơ bệnh án',
            'record' => $record,
            'treatments' => $clinical->treatmentsForPatient($patientId, (int)$doctor['MaBS']),
        ]);
    }

    public function schedules(): void
    {
        $this->requireRole(['BAC_SI']);
        $doctor = $this->doctorProfile();
        $this->view('doctor/schedules', [
            'pageTitle' => 'Lịch làm việc của tôi',
            'doctor' => $doctor,
            'schedules' => (new Schedule())->forDoctor((int)$doctor['MaBS']),
            'today' => date('Y-m-d'),
        ]);
    }

    public function saveSchedule(): void
    {
        $this->requireRole(['BAC_SI']);
        $this->postOnly();
        $doctor = $this->doctorProfile();
        try {
            $scheduleId = (new Schedule())->createForDoctor((int)$doctor['MaBS'], [
                'NgayLamViec' => $_POST['NgayLamViec'] ?? '',
                'GioBatDau' => $_POST['GioBatDau'] ?? '',
                'GioKetThuc' => $_POST['GioKetThuc'] ?? '',
                'BatDauNghi' => $_POST['BatDauNghi'] ?? '',
                'KetThucNghi' => $_POST['KetThucNghi'] ?? '',
            ]);
            flash('success', 'Đã đăng ký ca làm việc #' . $scheduleId . '. Ca có hiệu lực ngay để bệnh nhân đặt lịch.');
        } catch (Throwable $e) {
            flash('danger', $e->getMessage());
        }
        redirect_to('doctor.schedules');
    }

    public function cancelSchedule(): void
    {
        $this->requireRole(['BAC_SI']);
        $this->postOnly();
        $doctor = $this->doctorProfile();
        try {
            (new Schedule())->cancelForDoctor((int)($_POST['MaLLV'] ?? 0), (int)$doctor['MaBS']);
            flash('success', 'Đã ngừng ca làm việc.');
        } catch (Throwable $e) {
            flash('danger', $e->getMessage());
        }
        redirect_to('doctor.schedules');
    }
}
