<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $this->view('admin/users/index', [
            'title' => 'Users',
            'users' => (new User())->listByRole(),
        ]);
    }
}
