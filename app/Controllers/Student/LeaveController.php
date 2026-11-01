<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\LeaveApplication;
use App\Models\Student;
use App\Services\UploadService;

class LeaveController extends Controller
{
    public function index(): void
    {
        $student = (new Student())->findByUserId(Auth::id());
        $this->view('student/leave', [
            'title' => 'Leave Applications',
            'leaves' => (new LeaveApplication())->search(['student_id' => (int) $student['id']]),
        ]);
    }

    public function store(): void
    {
        $student = (new Student())->findByUserId(Auth::id());
        $leaveDate = $_POST['leave_date'] ?? '';
        $reason = trim($_POST['reason'] ?? '');

        $errors = $this->validate([
            'leave_date' => $leaveDate,
            'reason' => $reason,
        ], [
            'leave_date' => 'required',
            'reason' => 'required|min:5',
        ]);

        $documentPath = null;
        $originalName = null;
        if (!empty($_FILES['document']['name'])) {
            $upload = (new UploadService())->saveLeaveDocument($_FILES['document']);
            if (!$upload['success']) {
                $errors['document'][] = $upload['message'];
            } else {
                $documentPath = $upload['path'];
                $originalName = $upload['original_name'];
            }
        }

        if ($errors) {
            $this->withErrors($errors, $_POST);
            flash('error', 'Please fix the validation errors.');
            $this->redirect('/student/leave');
        }

        (new LeaveApplication())->create([
            'student_id' => (int) $student['id'],
            'leave_date' => $leaveDate,
            'reason' => $reason,
            'document_path' => $documentPath,
            'document_original_name' => $originalName,
            'status' => 'pending',
        ]);

        flash('success', 'Leave application submitted.');
        $this->redirect('/student/leave');
    }
}
