<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AuditService;

class TeacherController extends Controller
{
    private Teacher $teachers;
    private User $users;
    private AuditService $audit;

    public function __construct()
    {
        $this->teachers = new Teacher();
        $this->users = new User();
        $this->audit = new AuditService();
    }

    public function index(): void
    {
        $this->view('admin/teachers/index', [
            'title' => 'Teachers',
            'teachers' => $this->teachers->allWithDetails(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/teachers/form', [
            'title' => 'Add Teacher',
            'teacher' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        $errors = $this->validateTeacher($data);
        if ($this->users->findByUsername($data['username'])) {
            $errors['username'][] = 'Username already exists.';
        }
        if ($this->users->findByEmail($data['email'])) {
            $errors['email'][] = 'Email already exists.';
        }
        if ($this->teachers->where('teacher_code', $data['teacher_code'])) {
            $errors['teacher_code'][] = 'Teacher code already exists.';
        }
        if ($errors) {
            $this->withErrors($errors, $data);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/admin/teachers/create');
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $userId = $this->users->create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'full_name' => $data['full_name'],
                'role' => 'teacher',
                'status' => (int) $data['status'],
            ]);
            $this->teachers->create([
                'user_id' => $userId,
                'teacher_code' => $data['teacher_code'],
                'phone' => $data['phone'] ?: null,
                'status' => (int) $data['status'],
            ]);
            $pdo->commit();
            $this->audit->log('teacher_created', null, null, ['teacher_code' => $data['teacher_code']]);
            flash('success', 'Teacher created successfully.');
            $this->redirect('/admin/teachers');
        } catch (\Throwable $e) {
            $pdo->rollBack();
            flash('error', 'Failed to create teacher.');
            $this->redirect('/admin/teachers/create');
        }
    }

    public function edit(string $id): void
    {
        $teacher = $this->teachers->findWithDetails((int) $id);
        if (!$teacher) {
            flash('error', 'Teacher not found.');
            $this->redirect('/admin/teachers');
        }
        $this->view('admin/teachers/form', [
            'title' => 'Edit Teacher',
            'teacher' => $teacher,
        ]);
    }

    public function update(string $id): void
    {
        $teacher = $this->teachers->findWithDetails((int) $id);
        if (!$teacher) {
            flash('error', 'Teacher not found.');
            $this->redirect('/admin/teachers');
        }
        $data = $this->input();
        $errors = $this->validateTeacher($data, false);

        $byUser = $this->users->findByUsername($data['username']);
        if ($byUser && (int) $byUser['id'] !== (int) $teacher['user_id']) {
            $errors['username'][] = 'Username already exists.';
        }
        $byEmail = $this->users->findByEmail($data['email']);
        if ($byEmail && (int) $byEmail['id'] !== (int) $teacher['user_id']) {
            $errors['email'][] = 'Email already exists.';
        }
        $byCode = $this->teachers->where('teacher_code', $data['teacher_code']);
        if ($byCode && (int) $byCode['id'] !== (int) $teacher['id']) {
            $errors['teacher_code'][] = 'Teacher code already exists.';
        }
        if ($errors) {
            $this->withErrors($errors, $data);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/admin/teachers/' . $id . '/edit');
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
        $this->users->update((int) $teacher['user_id'], $userData);
        $this->teachers->update((int) $teacher['id'], [
            'teacher_code' => $data['teacher_code'],
            'phone' => $data['phone'] ?: null,
            'status' => (int) $data['status'],
        ]);
        $this->audit->log('teacher_updated', null, null, ['teacher_id' => (int) $id]);
        flash('success', 'Teacher updated successfully.');
        $this->redirect('/admin/teachers');
    }

    public function destroy(string $id): void
    {
        $teacher = $this->teachers->find((int) $id);
        if (!$teacher) {
            flash('error', 'Teacher not found.');
            $this->redirect('/admin/teachers');
        }
        $this->audit->log('teacher_deleted', null, null, ['teacher_code' => $teacher['teacher_code']]);
        $this->users->delete((int) $teacher['user_id']);
        flash('success', 'Teacher deleted successfully.');
        $this->redirect('/admin/teachers');
    }

    private function input(): array
    {
        return [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'full_name' => trim($_POST['full_name'] ?? ''),
            'teacher_code' => trim($_POST['teacher_code'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'status' => $_POST['status'] ?? '1',
        ];
    }

    private function validateTeacher(array $data, bool $requirePassword = true): array
    {
        $rules = [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'full_name' => 'required|min:2|max:120',
            'teacher_code' => 'required|max:50',
        ];
        if ($requirePassword) {
            $rules['password'] = 'required|min:6';
        } elseif (!empty($data['password'])) {
            $rules['password'] = 'min:6';
        }
        return $this->validate($data, $rules);
    }
}
