<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Stats;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Clinical;
use App\Models\Doctor;

class DashboardController extends Controller
{
    public function patient(): void
    {
        $this->requireRole(['BENH_NHAN']);
        $user = Auth::user();
        $patient = (new Patient())->findByUser((int)$user['MaND']);
        $appointments = $patient ? (new Appointment())->forPatient((int)$patient['MaBN']) : [];
        $this->view('dashboard/index', ['pageTitle' => 'Tổng quan bệnh nhân', 'roleLabel' => 'Bệnh nhân', 'appointments' => array_slice($appointments, 0, 5), 'counts' => []]);
    }

    public function staff(): void
    {
        $this->requireRole(['NHAN_VIEN']);
        $counts = (new Stats())->counts();
        $this->view('dashboard/index', ['pageTitle' => 'Tổng quan tiếp nhận', 'roleLabel' => 'Nhân viên', 'counts' => $counts, 'appointments' => array_slice((new Appointment())->all(), 0, 8)]);
    }

    public function admin(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        $counts = (new Stats())->counts();
        $this->view('dashboard/index', ['pageTitle' => 'Tổng quan quản trị', 'roleLabel' => 'Quản trị viên', 'counts' => $counts, 'appointments' => array_slice((new Appointment())->all(), 0, 8)]);
    }

    public function doctor(): void
    {
        $this->requireRole(['BAC_SI']);
        $doctor = (new Doctor())->findByUser((int)Auth::user()['MaND']);
        $appointments = $doctor ? (new Appointment())->forDoctor((int)$doctor['MaBS']) : [];
        $this->view('dashboard/index', ['pageTitle' => 'Tổng quan bác sĩ', 'roleLabel' => 'Bác sĩ', 'counts' => [], 'appointments' => array_slice($appointments, 0, 8)]);
    }
}
