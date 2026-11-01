<?php

namespace App\Controllers\Teacher;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;

class AttendanceController extends Controller
{
    private function teacherContext(): array
    {
        $teacher = (new Teacher())->findByUserId(Auth::id());
        if (!$teacher) {
            flash('error', 'Teacher profile not found.');
            redirect('/login');
        }
        $classIds = (new Teacher())->assignedClassIds((int) $teacher['id']);
        return [$teacher, $classIds];
    }

    public function index(): void
    {
        [, $classIds] = $this->teacherContext();
        $filters = [
            'date' => $_GET['date'] ?? today(),
            'class_id' => !empty($_GET['class_id']) ? (int) $_GET['class_id'] : null,
            'status' => $_GET['status'] ?? '',
        ];
        $search = array_filter($filters, fn($v) => $v !== null && $v !== '');
        if ($classIds) {
            $search['class_ids'] = $classIds;
        } else {
            $search['class_ids'] = [0];
        }
        if (!empty($filters['class_id']) && !in_array($filters['class_id'], $classIds, true)) {
            flash('error', 'Unauthorized class access.');
            $this->redirect('/teacher/attendance');
        }

        $classes = [];
        if ($classIds) {
            foreach ((new SchoolClass())->all('name ASC') as $c) {
                if (in_array((int) $c['id'], $classIds, true)) {
                    $classes[] = $c;
                }
            }
        }

        $this->view('teacher/attendance', [
            'title' => 'Class Attendance',
            'records' => (new Attendance())->search($search),
            'classes' => $classes,
            'filters' => $filters,
        ]);
    }

    public function student(string $id): void
    {
        [, $classIds] = $this->teacherContext();
        $student = (new Student())->findWithDetails((int) $id);
        if (!$student || !in_array((int) $student['class_id'], $classIds, true)) {
            http_response_code(403);
            $this->view('errors/403', [], 'layouts/error');
            return;
        }
        $this->view('teacher/student_attendance', [
            'title' => 'Student Attendance',
            'student' => $student,
            'records' => (new Attendance())->forStudent((int) $id),
            'stats' => (new Attendance())->studentStats((int) $id),
        ]);
    }

    public function classes(): void
    {
        [$teacher, $classIds] = $this->teacherContext();
        $this->view('teacher/classes', [
            'title' => 'Assigned Classes',
            'assignments' => (new Teacher())->assignments((int) $teacher['id']),
            'students' => (new Student())->byClassIds($classIds),
        ]);
    }
}
