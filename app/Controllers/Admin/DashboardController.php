<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index(): void
    {
        $students = new Student();
        $teachers = new Teacher();
        $classes = new SchoolClass();
        $attendance = new Attendance();
        $leaves = new LeaveApplication();

        $todayStats = $attendance->todayStats();
        $recentAttendance = $attendance->search(['date' => today()]);

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => [
                'total_students' => $students->count(),
                'active_students' => $students->countActive(),
                'total_teachers' => $teachers->count(),
                'total_classes' => $classes->count(),
                'today_attendance' => $todayStats['total'],
                'checked_in' => $todayStats['checked_in'],
                'checked_out' => $todayStats['checked_out'],
                'late' => $todayStats['late'],
                'pending_leave' => $leaves->countPending(),
                'face_registered' => $students->countFaceRegistered(),
            ],
            'recentAttendance' => array_slice($recentAttendance, 0, 10),
        ]);
    }
}
