<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\User;
use PDOException;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) redirect_to(Auth::homeRoute());
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            $username = trim((string)($_POST['TenDN'] ?? ''));
            $password = (string)($_POST['MatKhau'] ?? '');
            $user = (new User())->findForLogin($username);
            if (!$user || !password_verify($password, $user['MatKhau'])) {
                flash('danger', 'Tên đăng nhập hoặc mật khẩu không chính xác.');
                redirect_to('auth.login');
            }
            if ($user['TrangThai'] !== 'HOAT_DONG') {
                flash('danger', 'Tài khoản đang bị khóa hoặc ngừng hoạt động.');
                redirect_to('auth.login');
            }
            Auth::login([
                'MaND' => $user['MaND'], 'TenDN' => $user['TenDN'], 'VaiTro' => $user['VaiTro'],
                'TrangThai' => $user['TrangThai'], 'MaBN' => $user['MaBN'], 'MaBS' => $user['MaBS'],
                'MaNV' => $user['MaNV'], 'HoTen' => $user['HoTen'],
            ]);
            flash('success', 'Đăng nhập thành công.');
            redirect_to(Auth::homeRoute());
        }
        $this->view('auth/login', ['pageTitle' => 'Đăng nhập']);
    }

    public function register(): void
    {
        if (Auth::check()) redirect_to(Auth::homeRoute());
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->postOnly();
            $username = trim((string)($_POST['TenDN'] ?? ''));
            $password = (string)($_POST['MatKhau'] ?? '');
            $confirm = (string)($_POST['XacNhanMatKhau'] ?? '');
            $name = trim((string)($_POST['HoTen'] ?? ''));
            $phone = trim((string)($_POST['SDT'] ?? ''));
            $email = trim((string)($_POST['Email'] ?? ''));
            if ($username === '' || $name === '' || strlen($password) < 8 || $password !== $confirm) {
                flash('danger', 'Vui lòng nhập đầy đủ thông tin. Mật khẩu phải từ 8 ký tự và hai lần nhập phải khớp.');
                redirect_to('auth.register');
            }
            try {
                $db = Database::connection();
                $db->beginTransaction();
                $userId = (new User())->create($username, $password, 'BENH_NHAN');
                $stmt = $db->prepare('INSERT INTO BENH_NHAN (MaND, HoTen, SDT, Email) VALUES (?, ?, ?, ?)');
                $stmt->execute([$userId, $name, $phone ?: null, $email ?: null]);
                $db->commit();
                flash('success', 'Đăng ký thành công. Bạn có thể đăng nhập.');
                redirect_to('auth.login');
            } catch (PDOException $e) {
                if (isset($db) && $db->inTransaction()) $db->rollBack();
                flash('danger', 'Không thể đăng ký. Tên đăng nhập có thể đã tồn tại hoặc dữ liệu chưa hợp lệ.');
                redirect_to('auth.register');
            }
        }
        $this->view('auth/register', ['pageTitle' => 'Đăng ký bệnh nhân']);
    }

    public function logout(): void
    {
        $this->postOnly();
        Auth::logout();
        redirect_to('home');
    }
}
