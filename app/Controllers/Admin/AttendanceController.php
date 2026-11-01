<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;

class AttendanceController extends Controller
{
    public function index(): void
    {
        $filters = [
            'date' => $_GET['date'] ?? today(),
            'student_id' => !empty($_GET['student_id']) ? (int) $_GET['student_id'] : null,
            'class_id' => !empty($_GET['class_id']) ? (int) $_GET['class_id'] : null,
            'status' => $_GET['status'] ?? '',
        ];
        $clean = array_filter($filters, fn($v) => $v !== null && $v !== '');

        $this->view('admin/attendance/index', [
            'title' => 'Attendance Records',
            'records' => (new Attendance())->search($clean),
            'classes' => (new SchoolClass())->all('name ASC'),
            'students' => (new Student())->search(),
            'filters' => $filters,
        ]);
    }

    public function export(): void
    {
        $filters = array_filter([
            'date' => $_GET['date'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'student_id' => !empty($_GET['student_id']) ? (int) $_GET['student_id'] : null,
            'class_id' => !empty($_GET['class_id']) ? (int) $_GET['class_id'] : null,
            'status' => $_GET['status'] ?? '',
        ], fn($v) => $v !== null && $v !== '');

        $rows = (new Attendance())->search($filters);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendance_' . date('Ymd_His') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Date', 'Student', 'Student Code', 'Class', 'Check In', 'Check Out', 'Confidence', 'Status']);
        foreach ($rows as $row) {
            $confidence = $row['check_out_confidence'] ?: $row['check_in_confidence'];
            fputcsv($out, [
                $row['attendance_date'],
                $row['full_name'],
                $row['student_code'],
                $row['class_name'],
                $row['check_in_time'],
                $row['check_out_time'],
                $confidence,
                $row['status'],
            ]);
        }
        fclose($out);
        exit;
    }
}
