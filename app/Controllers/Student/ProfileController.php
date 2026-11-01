<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Student;
use App\Models\User;

class ProfileController extends Controller
{
    public function index(): void
    {
        $student = (new Student())->findByUserId(Auth::id());
        $this->view('student/profile', [
            'title' => 'My Profile',
            'student' => $student,
        ]);
    }

    public function passwordForm(): void
    {
        $this->view('student/password', [
            'title' => 'Change Password',
        ]);
    }

    public function updatePassword(): void
    {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $user = Auth::user();
        if (!$user || !password_verify($current, $user['password'])) {
            flash('error', 'Current password is incorrect.');
            $this->redirect('/student/password');
        }
        if (strlen($new) < 6) {
            flash('error', 'New password must be at least 6 characters.');
            $this->redirect('/student/password');
        }
        if ($new !== $confirm) {
            flash('error', 'Password confirmation does not match.');
            $this->redirect('/student/password');
        }

        (new User())->updatePassword((int) $user['id'], $new);
        flash('success', 'Password updated successfully.');
        $this->redirect('/student/profile');
    }
}
