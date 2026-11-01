<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', [
            'title' => 'Login',
        ], 'layouts/login');
    }

    public function login(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = $this->validate([
            'login' => $login,
            'password' => $password,
        ], [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($errors) {
            $this->withErrors($errors, ['login' => $login]);
            flash('error', 'Please fill in all required fields.');
            $this->redirect('/login');
        }

        if (!Auth::attempt($login, $password)) {
            $this->withErrors(['login' => ['Invalid username/email or password.']], ['login' => $login]);
            flash('error', 'Invalid credentials.');
            $this->redirect('/login');
        }

        (new User())->updateLastLogin(Auth::id());

        $role = Auth::role();
        if ($role === 'admin') {
            $this->redirect('/admin/dashboard');
        }
        if ($role === 'teacher') {
            $this->redirect('/teacher/dashboard');
        }
        $this->redirect('/student/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        flash('success', 'You have been logged out.');
        $this->redirect('/login');
    }
}
