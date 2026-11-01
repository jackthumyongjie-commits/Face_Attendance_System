<?php

namespace App\Controllers\Teacher;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index(): void
    {
        $teacher = (new Teacher())->findByUserId(Auth::id());
        if (!$teacher) {
            flash('error', 'Teacher profile not found.');
            $this->redirect('/login');
        }
        $assignments = (new Teacher())->assignments((int) $teacher['id']);
        $classIds = (new Teacher())->assignedClassIds((int) $teacher['id']);
        $students = (new Student())->byClassIds($classIds);
        $attendance = [];
        if ($classIds) {
            $attendance = (new Attendance())->search([
                'date' => today(),
                'class_ids' => $classIds,
            ]);
        }

        $this->view('teacher/dashboard', [
            'title' => 'Teacher Dashboard',
            'teacher' => $teacher,
            'assignments' => $assignments,
            'students' => $students,
            'attendance' => $attendance,
        ]);
    }
}
