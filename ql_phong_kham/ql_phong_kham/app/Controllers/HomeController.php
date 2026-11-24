<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Service;

class HomeController extends Controller
{
    public function index(): void
    {
        $services = (new Service())->all(true);
        $this->view('home/index', ['pageTitle' => 'Trang chủ', 'services' => array_slice($services, 0, 6)]);
    }

    public function services(): void
    {
        $services = (new Service())->all(true);
        $this->view('home/services', ['pageTitle' => 'Dịch vụ nha khoa', 'services' => $services]);
    }
}
