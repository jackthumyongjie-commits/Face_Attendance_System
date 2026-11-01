<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Setting;
use App\Models\Student;

class FaceController extends Controller
{
    public function register(string $id): void
    {
        $student = (new Student())->findWithDetails((int) $id);
        if (!$student) {
            flash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }
        $this->view('admin/students/face_register', [
            'title' => ((int) $student['face_registered'] === 1 ? 'Re-register' : 'Register') . ' Face',
            'student' => $student,
            'faceSettings' => (new Setting())->faceSettings(),
            'mode' => 'admin',
        ]);
    }
}
