<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\View;

class AdminMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            flash('error', 'Please login to continue.');
            redirect('/login');
        }
        if (!Auth::is('admin')) {
            http_response_code(403);
            View::render('errors/403', [], 'layouts/error');
            exit;
        }
    }
}
