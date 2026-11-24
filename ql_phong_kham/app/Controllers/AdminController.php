<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\Doctor;
use PDOException;
use Throwable;

class AdminController extends Controller
{
    public function accounts(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            $username = trim((string)($_POST['TenDN'] ?? ''));
            $password = (string)($_POST['MatKhau'] ?? '');
            $role = (string)($_POST['VaiTro'] ?? '');
            $name = trim((string)($_POST['HoTen'] ?? ''));
            $phone = trim((string)($_POST['SDT'] ?? ''));
            $email = trim((string)($_POST['Email'] ?? ''));
            $specialty = trim((string)($_POST['ChuyenMon'] ?? ''));
            $introduction = trim((string)($_POST['GioiThieu'] ?? ''));
            $position = trim((string)($_POST['ChucVu'] ?? ''));

            $roleLabels = ['BAC_SI' => 'bác sĩ', 'NHAN_VIEN' => 'nhân viên', 'QUAN_TRI_VIEN' => 'quản trị viên'];
            if (!isset($roleLabels[$role])) {
                flash('danger', 'Vai trò không hợp lệ.');
                redirect_to('admin.accounts');
            }
            if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
                flash('danger', 'Tên đăng nhập cần 3-50 ký tự, chỉ gồm chữ không dấu, số, dấu chấm, gạch dưới hoặc gạch ngang.');
                redirect_to('admin.accounts');
            }
            if (strlen($password) < 8) {
                flash('danger', 'Mật khẩu tối thiểu 8 ký tự.');
                redirect_to('admin.accounts');
            }
            if ($role !== 'QUAN_TRI_VIEN' && $name === '') {
                flash('danger', 'Vui lòng nhập họ tên cho tài khoản bác sĩ hoặc nhân viên.');
                redirect_to('admin.accounts');
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('danger', 'Email không hợp lệ.');
                redirect_to('admin.accounts');
            }

            $userModel = new User();
            if ($userModel->usernameExists($username)) {
                flash('danger', 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.');
                redirect_to('admin.accounts');
            }

            $db = Database::connection();
            try {
                $db->beginTransaction();
                $userId = $userModel->create($username, $password, $role);
                if ($role === 'BAC_SI') {
                    $stmt = $db->prepare('INSERT INTO BAC_SI (MaND, HoTen, SDT, Email, ChuyenMon, GioiThieu, TrangThai) VALUES (?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$userId, $name, $phone !== '' ? $phone : null, $email !== '' ? $email : null, $specialty !== '' ? $specialty : null, $introduction !== '' ? $introduction : null, 'HOAT_DONG']);
                } elseif ($role === 'NHAN_VIEN') {
                    $stmt = $db->prepare('INSERT INTO NHAN_VIEN (MaND, HoTen, SDT, Email, ChucVu, TrangThai) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$userId, $name, $phone !== '' ? $phone : null, $email !== '' ? $email : null, $position !== '' ? $position : 'Nhân viên tiếp nhận', 'HOAT_DONG']);
                }
                $db->commit();
                flash('success', 'Đã tạo tài khoản ' . $roleLabels[$role] . ($role === 'QUAN_TRI_VIEN' ? '.' : ' và hồ sơ tương ứng.'));
            } catch (PDOException $e) {
                if ($db->inTransaction()) $db->rollBack();
                $isDuplicate = (string)$e->getCode() === '23000' || (int)($e->errorInfo[1] ?? 0) === 1062;
                flash('danger', $isDuplicate
                    ? 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.'
                    : 'Không thể tạo tài khoản. Hãy kiểm tra cấu trúc database.');
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                flash('danger', 'Không thể tạo tài khoản. Hãy kiểm tra tên đăng nhập và cấu trúc database đã có BAC_SI/NHAN_VIEN chưa.');
            }
            redirect_to('admin.accounts');
        }
        $this->view('admin/accounts', ['pageTitle' => 'Quản lý tài khoản', 'accounts' => (new User())->all()]);
    }

    public function updateAccountStatus(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        $this->postOnly();
        $id = (int)($_POST['MaND'] ?? 0);
        if ($id === (int)\App\Core\Auth::user()['MaND']) { flash('warning', 'Không thể tự khóa tài khoản đang đăng nhập.'); redirect_to('admin.accounts'); }
        $status = (string)($_POST['TrangThai'] ?? '');
        if (!in_array($status, ['HOAT_DONG', 'KHOA'], true)) { flash('danger', 'Trạng thái không hợp lệ.'); redirect_to('admin.accounts'); }
        (new User())->updateStatus($id, $status);
        flash('success', 'Đã cập nhật trạng thái tài khoản.');
        redirect_to('admin.accounts');
    }

    public function editAccount(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        $id = (int)($_GET['id'] ?? $_POST['MaND'] ?? 0);
        $userModel = new User();
        $account = $userModel->find($id);
        if (!$account) {
            http_response_code(404);
            render_error(404, 'Không tìm thấy tài khoản.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            $password = (string)($_POST['MatKhau'] ?? '');
            $name = trim((string)($_POST['HoTen'] ?? ''));
            $phone = trim((string)($_POST['SDT'] ?? ''));
            $email = trim((string)($_POST['Email'] ?? ''));

            if ($password !== '' && strlen($password) < 8) {
                flash('danger', 'Mật khẩu mới tối thiểu 8 ký tự.');
                redirect_to('admin.account.edit', ['id' => $id]);
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('danger', 'Email không hợp lệ.');
                redirect_to('admin.account.edit', ['id' => $id]);
            }

            try {
                if ($account['VaiTro'] === 'BAC_SI') {
                    if (!$account['MaBS']) throw new \RuntimeException('Tài khoản bác sĩ chưa có hồ sơ BAC_SI. Vui lòng kiểm tra dữ liệu.');
                    if ($name === '') throw new \RuntimeException('Họ tên bác sĩ không được để trống.');
                    $userModel->updateDoctorProfile((int)$account['MaBS'], [
                        'HoTen' => $name,
                        'SDT' => $phone,
                        'Email' => $email,
                        'ChuyenMon' => trim((string)($_POST['ChuyenMon'] ?? '')),
                        'GioiThieu' => trim((string)($_POST['GioiThieu'] ?? '')),
                    ]);
                } elseif ($account['VaiTro'] === 'NHAN_VIEN') {
                    if (!$account['MaNV']) throw new \RuntimeException('Tài khoản nhân viên chưa có hồ sơ NHAN_VIEN. Vui lòng kiểm tra dữ liệu.');
                    if ($name === '') throw new \RuntimeException('Họ tên nhân viên không được để trống.');
                    $userModel->updateStaffProfile((int)$account['MaNV'], [
                        'HoTen' => $name,
                        'SDT' => $phone,
                        'Email' => $email,
                        'ChucVu' => trim((string)($_POST['ChucVu'] ?? '')),
                    ]);
                }
                if ($password !== '') {
                    $userModel->updatePassword($id, $password);
                }
                flash('success', 'Đã cập nhật tài khoản' . ($password !== '' ? ' và đặt lại mật khẩu.' : '.'));
                redirect_to('admin.accounts');
            } catch (Throwable $e) {
                flash('danger', $e->getMessage());
                redirect_to('admin.account.edit', ['id' => $id]);
            }
        }

        $this->view('admin/account_edit', ['pageTitle' => 'Cập nhật tài khoản', 'account' => $account]);
    }

    public function services(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            try {
                (new Service())->create([
                    'TenDV' => trim((string)($_POST['TenDV'] ?? '')),
                    'MoTa' => trim((string)($_POST['MoTa'] ?? '')),
                    'ThoiLuong' => (int)($_POST['ThoiLuong'] ?? 0),
                    'GiaDV' => (float)($_POST['GiaDV'] ?? 0),
                    'TrangThai' => 'DANG_AP_DUNG',
                ]);
                flash('success', 'Đã thêm dịch vụ.');
            } catch (Throwable $e) { flash('danger', 'Không thể thêm dịch vụ. Kiểm tra thời lượng và giá.'); }
            redirect_to('admin.services');
        }
        $this->view('admin/services', ['pageTitle' => 'Quản lý dịch vụ', 'services' => (new Service())->all(false)]);
    }

    public function serviceStatus(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        $this->postOnly();
        $status = (string)($_POST['TrangThai'] ?? '');
        if (!in_array($status, ['DANG_AP_DUNG', 'NGUNG_AP_DUNG'], true)) { flash('danger', 'Trạng thái không hợp lệ.'); redirect_to('admin.services'); }
        (new Service())->updateStatus((int)($_POST['MaDV'] ?? 0), $status);
        flash('success', 'Đã cập nhật dịch vụ.');
        redirect_to('admin.services');
    }

    public function editService(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        $model = new Service();
        $id = (int)($_GET['id'] ?? $_POST['MaDV'] ?? 0);
        $service = $model->find($id);
        if (!$service) {
            http_response_code(404);
            render_error(404, 'Không tìm thấy dịch vụ.');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            $name = trim((string)($_POST['TenDV'] ?? ''));
            $duration = (int)($_POST['ThoiLuong'] ?? 0);
            $price = (float)($_POST['GiaDV'] ?? -1);
            $status = (string)($_POST['TrangThai'] ?? '');
            if ($name === '') {
                flash('danger', 'Tên dịch vụ không được để trống.');
                redirect_to('admin.service.edit', ['id' => $id]);
            }
            if ($duration < 1) {
                flash('danger', 'Thời lượng phải lớn hơn 0 phút.');
                redirect_to('admin.service.edit', ['id' => $id]);
            }
            if ($price < 0) {
                flash('danger', 'Giá dịch vụ không hợp lệ.');
                redirect_to('admin.service.edit', ['id' => $id]);
            }
            if (!in_array($status, ['DANG_AP_DUNG', 'NGUNG_AP_DUNG'], true)) {
                flash('danger', 'Trạng thái dịch vụ không hợp lệ.');
                redirect_to('admin.service.edit', ['id' => $id]);
            }
            try {
                $model->update($id, [
                    'TenDV' => $name,
                    'MoTa' => trim((string)($_POST['MoTa'] ?? '')),
                    'ThoiLuong' => $duration,
                    'GiaDV' => $price,
                    'TrangThai' => $status,
                ]);
                flash('success', 'Đã cập nhật dịch vụ #' . $id . '.');
                redirect_to('admin.services');
            } catch (Throwable $e) {
                flash('danger', 'Không thể cập nhật dịch vụ. Vui lòng kiểm tra dữ liệu.');
                redirect_to('admin.service.edit', ['id' => $id]);
            }
        }

        $this->view('admin/service_edit', ['pageTitle' => 'Cập nhật dịch vụ', 'service' => $service]);
    }

    public function schedules(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            try {
                (new Schedule())->create([
                    'MaBS' => $_POST['MaBS'] ?? 0, 'NgayLamViec' => $_POST['NgayLamViec'] ?? '',
                    'GioBatDau' => $_POST['GioBatDau'] ?? '', 'GioKetThuc' => $_POST['GioKetThuc'] ?? '',
                    'BatDauNghi' => $_POST['BatDauNghi'] ?? '', 'KetThucNghi' => $_POST['KetThucNghi'] ?? '',
                    'TrangThai' => 'HOAT_DONG',
                ]);
                flash('success', 'Đã tạo lịch làm việc.');
            } catch (Throwable $e) { flash('danger', 'Không thể tạo lịch làm việc. Kiểm tra bác sĩ, ngày và thời gian.'); }
            redirect_to('admin.schedules');
        }
        $this->view('admin/schedules', ['pageTitle' => 'Quản lý lịch làm việc', 'schedules' => (new Schedule())->all(), 'doctors' => (new Doctor())->all()]);
    }

    public function appointments(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        $this->view('admin/appointments', ['pageTitle' => 'Theo dõi lịch hẹn', 'appointments' => (new \App\Models\Appointment())->all()]);
    }

    public function scheduleStatus(): void
    {
        $this->requireRole(['QUAN_TRI_VIEN']);
        $this->postOnly();
        $status = (string)($_POST['TrangThai'] ?? '');
        try {
            (new Schedule())->updateStatusByAdmin((int)($_POST['MaLLV'] ?? 0), $status);
            flash('success', 'Đã cập nhật trạng thái ca làm việc.');
        } catch (Throwable $e) {
            flash('danger', $e->getMessage());
        }
        redirect_to('admin.schedules');
    }
}
