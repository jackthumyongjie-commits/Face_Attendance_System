<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        if (Auth::check()) {
            $role = Auth::role();
            if ($role === 'admin') {
                $this->redirect('/admin/dashboard');
            }
            if ($role === 'teacher') {
                $this->redirect('/teacher/dashboard');
            }
            $this->redirect('/student/dashboard');
        }
        $this->redirect('/login');
    }

    public function privacy(): void
    {
        $loggedIn = Auth::check();
        $this->view('privacy/index', [
            'title' => 'Privacy Information',
            'standalone' => !$loggedIn,
        ], $loggedIn ? 'layouts/main' : 'layouts/privacy');
    }
}
