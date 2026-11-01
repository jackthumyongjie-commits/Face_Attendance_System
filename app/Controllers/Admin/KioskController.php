<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Setting;

class KioskController extends Controller
{
    public function index(): void
    {
        $this->view('kiosk/index', [
            'title' => 'Attendance Kiosk',
            'faceSettings' => (new Setting())->faceSettings(),
        ], 'layouts/kiosk');
    }
}
