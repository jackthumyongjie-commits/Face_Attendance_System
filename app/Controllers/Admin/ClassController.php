<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\SchoolClass;
use App\Services\AuditService;

class ClassController extends Controller
{
    private SchoolClass $classes;
    private AuditService $audit;

    public function __construct()
    {
        $this->classes = new SchoolClass();
        $this->audit = new AuditService();
    }

    public function index(): void
    {
        $this->view('admin/classes/index', [
            'title' => 'Classes',
            'classes' => $this->classes->withStudentCount(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/classes/form', [
            'title' => 'Add Class',
            'class' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        $errors = $this->validate($data, [
            'name' => 'required|max:120',
            'code' => 'required|max:50',
            'start_time' => 'required',
            'late_grace_minutes' => 'required|numeric',
        ]);
        if ($this->classes->where('code', $data['code'])) {
            $errors['code'][] = 'Class code already exists.';
        }
        if ($errors) {
            $this->withErrors($errors, $data);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/admin/classes/create');
        }
        $id = $this->classes->create($data);
        $this->audit->log('class_created', null, null, ['class_id' => $id]);
        flash('success', 'Class created successfully.');
        $this->redirect('/admin/classes');
    }

    public function edit(string $id): void
    {
        $class = $this->classes->find((int) $id);
        if (!$class) {
            flash('error', 'Class not found.');
            $this->redirect('/admin/classes');
        }
        $this->view('admin/classes/form', [
            'title' => 'Edit Class',
            'class' => $class,
        ]);
    }

    public function update(string $id): void
    {
        $class = $this->classes->find((int) $id);
        if (!$class) {
            flash('error', 'Class not found.');
            $this->redirect('/admin/classes');
        }
        $data = $this->input();
        $errors = $this->validate($data, [
            'name' => 'required|max:120',
            'code' => 'required|max:50',
            'start_time' => 'required',
            'late_grace_minutes' => 'required|numeric',
        ]);
        $byCode = $this->classes->where('code', $data['code']);
        if ($byCode && (int) $byCode['id'] !== (int) $id) {
            $errors['code'][] = 'Class code already exists.';
        }
        if ($errors) {
            $this->withErrors($errors, $data);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/admin/classes/' . $id . '/edit');
        }
        $this->classes->update((int) $id, $data);
        $this->audit->log('class_updated', null, null, ['class_id' => (int) $id]);
        flash('success', 'Class updated successfully.');
        $this->redirect('/admin/classes');
    }

    public function destroy(string $id): void
    {
        $class = $this->classes->find((int) $id);
        if (!$class) {
            flash('error', 'Class not found.');
            $this->redirect('/admin/classes');
        }
        $this->classes->delete((int) $id);
        $this->audit->log('class_deleted', null, null, ['class_code' => $class['code']]);
        flash('success', 'Class deleted successfully.');
        $this->redirect('/admin/classes');
    }

    private function input(): array
    {
        return [
            'name' => trim($_POST['name'] ?? ''),
            'code' => trim($_POST['code'] ?? ''),
            'start_time' => $_POST['start_time'] ?? '08:00',
            'late_grace_minutes' => (int) ($_POST['late_grace_minutes'] ?? 15),
            'rest_start' => $_POST['rest_start'] ?: null,
            'rest_end' => $_POST['rest_end'] ?: null,
            'status' => (int) ($_POST['status'] ?? 1),
        ];
    }
}
