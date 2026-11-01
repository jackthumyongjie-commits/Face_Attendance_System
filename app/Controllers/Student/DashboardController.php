<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index(): void
    {
        $student = (new Student())->findByUserId(Auth::id());
        if (!$student) {
            flash('error', 'Student profile not found.');
            $this->redirect('/login');
        }
        $today = (new Attendance())->findToday((int) $student['id']);
        $stats = (new Attendance())->studentStats((int) $student['id']);

        $this->view('student/dashboard', [
            'title' => 'Student Dashboard',
            'student' => $student,
            'todayAttendance' => $today,
            'stats' => $stats,
        ]);
    }
}
