<?php

namespace App\Middleware;

use App\Core\Auth;

class GuestMiddleware
{
    public function handle(): void
    {
        if (Auth::check()) {
            $role = Auth::role();
            if ($role === 'admin') {
                redirect('/admin/dashboard');
            }
            if ($role === 'teacher') {
                redirect('/teacher/dashboard');
            }
            redirect('/student/dashboard');
        }
    }
}
