<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Subject;
use App\Services\AuditService;

class SubjectController extends Controller
{
    private Subject $subjects;
    private AuditService $audit;

    public function __construct()
    {
        $this->subjects = new Subject();
        $this->audit = new AuditService();
    }

    public function index(): void
    {
        $this->view('admin/subjects/index', [
            'title' => 'Subjects',
            'subjects' => $this->subjects->all('name ASC'),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/subjects/form', [
            'title' => 'Add Subject',
            'subject' => null,
        ]);
    }

    public function store(): void
    {
        $data = $this->input();
        $errors = $this->validate($data, [
            'code' => 'required|max:50',
            'name' => 'required|max:150',
        ]);
        if ($this->subjects->where('code', $data['code'])) {
            $errors['code'][] = 'Subject code already exists.';
        }
        if ($errors) {
            $this->withErrors($errors, $data);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/admin/subjects/create');
        }
        $id = $this->subjects->create($data);
        $this->audit->log('subject_created', null, null, ['subject_id' => $id]);
        flash('success', 'Subject created successfully.');
        $this->redirect('/admin/subjects');
    }

    public function edit(string $id): void
    {
        $subject = $this->subjects->find((int) $id);
        if (!$subject) {
            flash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
        }
        $this->view('admin/subjects/form', [
            'title' => 'Edit Subject',
            'subject' => $subject,
        ]);
    }

    public function update(string $id): void
    {
        $subject = $this->subjects->find((int) $id);
        if (!$subject) {
            flash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
        }
        $data = $this->input();
        $errors = $this->validate($data, [
            'code' => 'required|max:50',
            'name' => 'required|max:150',
        ]);
        $byCode = $this->subjects->where('code', $data['code']);
        if ($byCode && (int) $byCode['id'] !== (int) $id) {
            $errors['code'][] = 'Subject code already exists.';
        }
        if ($errors) {
            $this->withErrors($errors, $data);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/admin/subjects/' . $id . '/edit');
        }
        $this->subjects->update((int) $id, $data);
        $this->audit->log('subject_updated', null, null, ['subject_id' => (int) $id]);
        flash('success', 'Subject updated successfully.');
        $this->redirect('/admin/subjects');
    }

    public function destroy(string $id): void
    {
        $subject = $this->subjects->find((int) $id);
        if (!$subject) {
            flash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
        }
        $this->subjects->delete((int) $id);
        $this->audit->log('subject_deleted', null, null, ['subject_code' => $subject['code']]);
        flash('success', 'Subject deleted successfully.');
        $this->redirect('/admin/subjects');
    }

    private function input(): array
    {
        return [
            'code' => trim($_POST['code'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => (int) ($_POST['status'] ?? 1),
        ];
    }
}
