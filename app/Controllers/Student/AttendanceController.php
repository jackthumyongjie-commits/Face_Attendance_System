<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\Student;

class AttendanceController extends Controller
{
    private function student(): array
    {
        $student = (new Student())->findByUserId(Auth::id());
        if (!$student) {
            flash('error', 'Student profile not found.');
            redirect('/login');
        }
        return $student;
    }

    public function scan(): void
    {
        $student = $this->student();
        if ((int) $student['face_registered'] !== 1) {
            flash('error', 'Please register your face before scanning attendance.');
            $this->redirect('/student/face/register');
        }
        $this->view('student/scan', [
            'title' => 'Scan Attendance',
            'student' => $student,
            'faceSettings' => (new Setting())->faceSettings(),
            'todayAttendance' => (new Attendance())->findToday((int) $student['id']),
        ]);
    }

    public function history(): void
    {
        $student = $this->student();
        $filters = [
            'date' => $_GET['date'] ?? '',
            'month' => $_GET['month'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];
        $clean = array_filter($filters);
        $this->view('student/history', [
            'title' => 'Attendance History',
            'student' => $student,
            'records' => (new Attendance())->forStudent((int) $student['id'], $clean),
            'todayAttendance' => (new Attendance())->findToday((int) $student['id']),
            'filters' => $filters,
        ]);
    }

    public function statistics(): void
    {
        $student = $this->student();
        $this->view('student/statistics', [
            'title' => 'Attendance Statistics',
            'student' => $student,
            'stats' => (new Attendance())->studentStats((int) $student['id']),
            'records' => (new Attendance())->forStudent((int) $student['id']),
        ]);
    }
}
