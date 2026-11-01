<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Services\AuditService;

class AssignmentController extends Controller
{
    public function classSubjects(): void
    {
        $this->view('admin/assignments/class_subjects', [
            'title' => 'Class-Subject Assignments',
            'assignments' => (new ClassSubject())->listAll(),
            'classes' => (new SchoolClass())->active(),
            'subjects' => (new Subject())->active(),
        ]);
    }

    public function storeClassSubject(): void
    {
        $classId = (int) ($_POST['class_id'] ?? 0);
        $subjectId = (int) ($_POST['subject_id'] ?? 0);
        $model = new ClassSubject();
        if (!$classId || !$subjectId) {
            flash('error', 'Please select class and subject.');
            $this->redirect('/admin/assignments/class-subjects');
        }
        if ($model->exists($classId, $subjectId)) {
            flash('error', 'This class-subject relationship already exists.');
            $this->redirect('/admin/assignments/class-subjects');
        }
        $model->create(['class_id' => $classId, 'subject_id' => $subjectId]);
        (new AuditService())->log('class_subject_assigned', null, null, compact('classId', 'subjectId'));
        flash('success', 'Subject assigned to class.');
        $this->redirect('/admin/assignments/class-subjects');
    }

    public function destroyClassSubject(string $id): void
    {
        (new ClassSubject())->delete((int) $id);
        flash('success', 'Assignment removed.');
        $this->redirect('/admin/assignments/class-subjects');
    }

    public function teacherAssignments(): void
    {
        $this->view('admin/assignments/teacher_assignments', [
            'title' => 'Teacher Assignments',
            'assignments' => (new TeacherAssignment())->listAll(),
            'teachers' => (new Teacher())->allWithDetails(),
            'classes' => (new SchoolClass())->active(),
            'subjects' => (new Subject())->active(),
        ]);
    }

    public function storeTeacherAssignment(): void
    {
        $teacherId = (int) ($_POST['teacher_id'] ?? 0);
        $classId = (int) ($_POST['class_id'] ?? 0);
        $subjectId = (int) ($_POST['subject_id'] ?? 0);
        $model = new TeacherAssignment();
        if (!$teacherId || !$classId || !$subjectId) {
            flash('error', 'Please select teacher, class and subject.');
            $this->redirect('/admin/assignments/teachers');
        }
        if ($model->exists($teacherId, $classId, $subjectId)) {
            flash('error', 'This teacher assignment already exists.');
            $this->redirect('/admin/assignments/teachers');
        }
        $model->create([
            'teacher_id' => $teacherId,
            'class_id' => $classId,
            'subject_id' => $subjectId,
        ]);
        (new AuditService())->log('teacher_assigned', null, null, compact('teacherId', 'classId', 'subjectId'));
        flash('success', 'Teacher assignment created.');
        $this->redirect('/admin/assignments/teachers');
    }

    public function destroyTeacherAssignment(string $id): void
    {
        (new TeacherAssignment())->delete((int) $id);
        flash('success', 'Teacher assignment removed.');
        $this->redirect('/admin/assignments/teachers');
    }
}
