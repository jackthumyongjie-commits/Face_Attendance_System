<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Setting;
use App\Models\Student;

class FaceController extends Controller
{
    public function register(): void
    {
        $student = (new Student())->findByUserId(Auth::id());
        if (!$student) {
            flash('error', 'Student profile not found.');
            $this->redirect('/student/dashboard');
        }
        $this->view('student/face_register', [
            'title' => ((int) $student['face_registered'] === 1 ? 'Re-register' : 'Register') . ' Face',
            'student' => $student,
            'faceSettings' => (new Setting())->faceSettings(),
            'mode' => 'student',
        ]);
    }
}
