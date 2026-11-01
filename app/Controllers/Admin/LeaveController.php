<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Services\AuditService;

class LeaveController extends Controller
{
    private LeaveApplication $leaves;
    private AuditService $audit;

    public function __construct()
    {
        $this->leaves = new LeaveApplication();
        $this->audit = new AuditService();
    }

    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $filters = array_filter(['status' => $status]);
        $this->view('admin/leave/index', [
            'title' => 'Leave Applications',
            'leaves' => $this->leaves->search($filters),
            'filters' => ['status' => $status],
        ]);
    }

    public function show(string $id): void
    {
        $leave = $this->leaves->findWithDetails((int) $id);
        if (!$leave) {
            flash('error', 'Leave application not found.');
            $this->redirect('/admin/leave');
        }
        $this->view('admin/leave/show', [
            'title' => 'Leave Application',
            'leave' => $leave,
        ]);
    }

    public function review(string $id): void
    {
        $leave = $this->leaves->findWithDetails((int) $id);
        if (!$leave) {
            flash('error', 'Leave application not found.');
            $this->redirect('/admin/leave');
        }

        $action = $_POST['action'] ?? '';
        $note = trim($_POST['review_note'] ?? '');
        if (!in_array($action, ['approved', 'rejected'], true)) {
            flash('error', 'Invalid review action.');
            $this->redirect('/admin/leave/' . $id);
        }

        $this->leaves->update((int) $id, [
            'status' => $action,
            'review_note' => $note ?: null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($action === 'approved') {
            $attendance = new Attendance();
            $existing = $attendance->findToday((int) $leave['student_id'], $leave['leave_date']);
            if (!$existing) {
                $attendance->create([
                    'student_id' => (int) $leave['student_id'],
                    'class_id' => null,
                    'attendance_date' => $leave['leave_date'],
                    'status' => 'on_leave',
                    'notes' => 'Approved leave',
                ]);
            } else {
                $attendance->update((int) $existing['id'], [
                    'status' => 'on_leave',
                    'notes' => 'Approved leave',
                ]);
            }
            $this->audit->log('leave_approved', Auth::id(), (int) $leave['student_id'], ['leave_id' => (int) $id]);
            flash('success', 'Leave application approved.');
        } else {
            $this->audit->log('leave_rejected', Auth::id(), (int) $leave['student_id'], ['leave_id' => (int) $id]);
            flash('success', 'Leave application rejected.');
        }

        $this->redirect('/admin/leave');
    }
}
