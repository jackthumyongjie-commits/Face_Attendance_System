<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\View;

class StudentMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            flash('error', 'Please login to continue.');
            redirect('/login');
        }
        if (!Auth::is('student')) {
            http_response_code(403);
            View::render('errors/403', [], 'layouts/error');
            exit;
        }
    }
}
