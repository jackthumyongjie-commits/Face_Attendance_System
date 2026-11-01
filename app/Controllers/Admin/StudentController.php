<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;

class StudentController extends Controller
{
    private Student $students;
    private User $users;
    private SchoolClass $classes;
    private AuditService $audit;

    public function __construct()
    {
        $this->students = new Student();
        $this->users = new User();
        $this->classes = new SchoolClass();
        $this->audit = new AuditService();
    }

    public function index(): void
    {
        $q = trim($_GET['q'] ?? '');
        $classId = !empty($_GET['class_id']) ? (int) $_GET['class_id'] : null;
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : null;

        $this->view('admin/students/index', [
            'title' => 'Students',
            'students' => $this->students->search($q ?: null, $classId, $status),
            'classes' => $this->classes->all('name ASC'),
            'filters' => compact('q', 'classId', 'status'),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/students/form', [
            'title' => 'Add Student',
            'student' => null,
            'classes' => $this->classes->active(),
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        $errors = $this->validateStudent($data);
        if ($this->users->findByUsername($data['username'])) {
            $errors['username'][] = 'Username already exists.';
        }
        if ($this->users->findByEmail($data['email'])) {
            $errors['email'][] = 'Email already exists.';
        }
        if ($this->students->where('student_code', $data['student_code'])) {
            $errors['student_code'][] = 'Student code already exists.';
        }
        if ($errors) {
            $this->withErrors($errors, $data);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/admin/students/create');
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $userId = $this->users->create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'full_name' => $data['full_name'],
                'role' => 'student',
                'status' => (int) $data['status'],
            ]);
            $studentId = $this->students->create([
                'user_id' => $userId,
                'class_id' => $data['class_id'] ?: null,
                'student_code' => $data['student_code'],
                'phone' => $data['phone'] ?: null,
                'gender' => $data['gender'] ?: null,
                'date_of_birth' => $data['date_of_birth'] ?: null,
                'status' => (int) $data['status'],
            ]);
            $pdo->commit();
            $this->audit->log('student_created', null, $studentId, ['student_code' => $data['student_code']]);
            flash('success', 'Student created successfully.');
            $this->redirect('/admin/students');
        } catch (\Throwable $e) {
            $pdo->rollBack();
            flash('error', 'Failed to create student.');
            $this->redirect('/admin/students/create');
        }
    }

    public function show(string $id): void
    {
        $student = $this->students->findWithDetails((int) $id);
        if (!$student) {
            flash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }
        $this->view('admin/students/show', [
            'title' => 'Student Details',
            'student' => $student,
        ]);
    }

    public function edit(string $id): void
    {
        $student = $this->students->findWithDetails((int) $id);
        if (!$student) {
            flash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }
        $this->view('admin/students/form', [
            'title' => 'Edit Student',
            'student' => $student,
            'classes' => $this->classes->active(),
        ]);
    }

    public function update(string $id): void
    {
        $student = $this->students->findWithDetails((int) $id);
        if (!$student) {
            flash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }

        $data = $this->input();
        $errors = $this->validateStudent($data, false);

        $byUser = $this->users->findByUsername($data['username']);
        if ($byUser && (int) $byUser['id'] !== (int) $student['user_id']) {
            $errors['username'][] = 'Username already exists.';
        }
        $byEmail = $this->users->findByEmail($data['email']);
        if ($byEmail && (int) $byEmail['id'] !== (int) $student['user_id']) {
            $errors['email'][] = 'Email already exists.';
        }
        $byCode = $this->students->where('student_code', $data['student_code']);
        if ($byCode && (int) $byCode['id'] !== (int) $student['id']) {
            $errors['student_code'][] = 'Student code already exists.';
        }

        if ($errors) {
            $this->withErrors($errors, $data);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/admin/students/' . $id . '/edit');
        }

        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'full_name' => $data['full_name'],
            'status' => (int) $data['status'],
        ];
        if (!empty($data['password'])) {
            $userData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $this->users->update((int) $student['user_id'], $userData);
        $this->students->update((int) $student['id'], [
            'class_id' => $data['class_id'] ?: null,
            'student_code' => $data['student_code'],
            'phone' => $data['phone'] ?: null,
            'gender' => $data['gender'] ?: null,
            'date_of_birth' => $data['date_of_birth'] ?: null,
            'status' => (int) $data['status'],
        ]);

        $this->audit->log('student_updated', null, (int) $student['id']);
        flash('success', 'Student updated successfully.');
        $this->redirect('/admin/students');
    }

    public function destroy(string $id): void
    {
        $student = $this->students->find((int) $id);
        if (!$student) {
            flash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }
        $userId = (int) $student['user_id'];
        $this->audit->log('student_deleted', null, (int) $student['id'], ['student_code' => $student['student_code']]);
        $this->users->delete($userId); // cascades to student
        flash('success', 'Student deleted successfully.');
        $this->redirect('/admin/students');
    }

    public function export(): void
    {
        $rows = $this->students->exportRows();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="students_' . date('Ymd_His') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Student Code', 'Full Name', 'Email', 'Phone', 'Gender', 'Date of Birth', 'Class', 'Status', 'Face Registered', 'Registration Version']);
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['student_code'],
                $row['full_name'],
                $row['email'],
                $row['phone'],
                $row['gender'],
                $row['date_of_birth'],
                $row['class_name'],
                (int) $row['status'] === 1 ? 'Active' : 'Inactive',
                (int) $row['face_registered'] === 1 ? 'Yes' : 'No',
                $row['registration_version'],
            ]);
        }
        fclose($out);
        exit;
    }

    private function input(): array
    {
        return [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'full_name' => trim($_POST['full_name'] ?? ''),
            'student_code' => trim($_POST['student_code'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'gender' => $_POST['gender'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? '',
            'class_id' => $_POST['class_id'] ?? '',
            'status' => $_POST['status'] ?? '1',
        ];
    }

    private function validateStudent(array $data, bool $requirePassword = true): array
    {
        $rules = [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'full_name' => 'required|min:2|max:120',
            'student_code' => 'required|max:50',
        ];
        if ($requirePassword) {
            $rules['password'] = 'required|min:6';
        } elseif (!empty($data['password'])) {
            $rules['password'] = 'min:6';
        }
        return $this->validate($data, $rules);
    }
}
