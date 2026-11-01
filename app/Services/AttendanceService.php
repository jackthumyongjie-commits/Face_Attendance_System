<?php

namespace App\Services;

use App\Core\Auth;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\RestAttendance;
use App\Models\Setting;
use App\Models\Student;

class AttendanceService
{
    private Attendance $attendance;
    private RestAttendance $rest;
    private LeaveApplication $leaves;
    private Student $students;
    private Setting $settings;
    private UploadService $uploads;
    private AuditService $audit;

    public function __construct()
    {
        $this->attendance = new Attendance();
        $this->rest = new RestAttendance();
        $this->leaves = new LeaveApplication();
        $this->students = new Student();
        $this->settings = new Setting();
        $this->uploads = new UploadService();
        $this->audit = new AuditService();
    }

    public function processScan(int $studentId, float $confidence, ?string $photoBase64 = null): array
    {
        $student = $this->students->findWithDetails($studentId);
        if (!$student || (int) $student['status'] !== 1) {
            return ['success' => false, 'message' => 'Student account is inactive.'];
        }
        if ((int) $student['face_registered'] !== 1) {
            return ['success' => false, 'message' => 'Student face has not been registered.'];
        }

        $settings = $this->settings->faceSettings();
        $minConfidence = (float) $settings['min_attendance_confidence'];
        if ($confidence < $minConfidence) {
            return ['success' => false, 'message' => 'Face match confidence is too low.'];
        }

        $today = today();
        if ($this->leaves->hasApprovedLeave($studentId, $today)) {
            return ['success' => false, 'message' => 'Student is on approved leave today.'];
        }

        $now = time();
        $currentTime = date('H:i:s', $now);

        // Rest period handling
        if ($this->isRestPeriod($student, $currentTime)) {
            return $this->processRestScan($student, $confidence, $photoBase64, $settings);
        }

        $record = $this->attendance->findToday($studentId, $today);
        $interval = (int) $settings['duplicate_scan_interval'];

        if ($record) {
            $lastAction = $record['check_out_time'] ?: $record['check_in_time'];
            if ($lastAction && ($now - strtotime($lastAction)) < $interval) {
                return [
                    'success' => false,
                    'message' => 'Attendance already recorded recently. Please wait before scanning again.',
                ];
            }

            if ($record['check_out_time']) {
                return [
                    'success' => false,
                    'message' => 'Attendance already recorded for today (checked out).',
                ];
            }

            // Check out
            $photo = $photoBase64 ? $this->uploads->saveBase64Image($photoBase64, 'attendance') : null;
            $this->attendance->update((int) $record['id'], [
                'check_out_time' => now(),
                'check_out_confidence' => $confidence,
                'check_out_photo' => $photo,
                'status' => 'checked_out',
            ]);

            $this->audit->log('attendance_check_out', Auth::id(), $studentId, [
                'attendance_id' => $record['id'],
            ], $confidence);

            return [
                'success' => true,
                'action' => 'check_out',
                'message' => 'Check-out successful.',
                'student' => [
                    'id' => (int) $student['id'],
                    'full_name' => $student['full_name'],
                    'student_code' => $student['student_code'],
                ],
                'confidence' => $confidence,
                'status' => 'checked_out',
                'time' => date('H:i:s'),
            ];
        }

        // Check in
        $isLate = $this->isLate($student, $currentTime);
        $status = $isLate ? 'late' : 'checked_in';
        $photo = $photoBase64 ? $this->uploads->saveBase64Image($photoBase64, 'attendance') : null;

        $id = $this->attendance->create([
            'student_id' => $studentId,
            'class_id' => $student['class_id'],
            'attendance_date' => $today,
            'check_in_time' => now(),
            'check_in_confidence' => $confidence,
            'check_in_photo' => $photo,
            'status' => $status,
        ]);

        $this->audit->log('attendance_check_in', Auth::id(), $studentId, [
            'attendance_id' => $id,
            'late' => $isLate,
        ], $confidence);

        $message = $isLate ? 'Check-in successful. You are marked late.' : 'Check-in successful.';

        return [
            'success' => true,
            'action' => 'check_in',
            'message' => $message,
            'student' => [
                'id' => (int) $student['id'],
                'full_name' => $student['full_name'],
                'student_code' => $student['student_code'],
            ],
            'confidence' => $confidence,
            'status' => $status,
            'late' => $isLate,
            'time' => date('H:i:s'),
        ];
    }

    private function processRestScan(array $student, float $confidence, ?string $photoBase64, array $settings): array
    {
        $studentId = (int) $student['id'];
        $today = today();
        $record = $this->rest->findToday($studentId, $today);
        $interval = (int) $settings['duplicate_scan_interval'];
        $now = time();

        if ($record) {
            $lastAction = $record['check_out_time'] ?: $record['check_in_time'];
            if ($lastAction && ($now - strtotime($lastAction)) < $interval) {
                return [
                    'success' => false,
                    'message' => 'Attendance already recorded recently. Please wait before scanning again.',
                ];
            }
            if ($record['check_out_time']) {
                return [
                    'success' => false,
                    'message' => 'Rest attendance already completed for today.',
                ];
            }

            $photo = $photoBase64 ? $this->uploads->saveBase64Image($photoBase64, 'attendance') : null;
            $this->rest->update((int) $record['id'], [
                'check_out_time' => now(),
                'check_out_confidence' => $confidence,
                'check_out_photo' => $photo,
                'status' => 'checked_out',
            ]);

            $this->audit->log('rest_check_out', Auth::id(), $studentId, [], $confidence);

            return [
                'success' => true,
                'action' => 'rest_check_out',
                'message' => 'Rest check-out successful.',
                'student' => [
                    'id' => $studentId,
                    'full_name' => $student['full_name'],
                    'student_code' => $student['student_code'],
                ],
                'confidence' => $confidence,
                'status' => 'checked_out',
                'time' => date('H:i:s'),
            ];
        }

        $photo = $photoBase64 ? $this->uploads->saveBase64Image($photoBase64, 'attendance') : null;
        $this->rest->create([
            'student_id' => $studentId,
            'class_id' => $student['class_id'],
            'attendance_date' => $today,
            'check_in_time' => now(),
            'check_in_confidence' => $confidence,
            'check_in_photo' => $photo,
            'status' => 'checked_in',
        ]);

        $this->audit->log('rest_check_in', Auth::id(), $studentId, [], $confidence);

        return [
            'success' => true,
            'action' => 'rest_check_in',
            'message' => 'Rest check-in successful.',
            'student' => [
                'id' => $studentId,
                'full_name' => $student['full_name'],
                'student_code' => $student['student_code'],
            ],
            'confidence' => $confidence,
            'status' => 'checked_in',
            'time' => date('H:i:s'),
        ];
    }

    private function isRestPeriod(array $student, string $currentTime): bool
    {
        if (empty($student['rest_start']) || empty($student['rest_end'])) {
            return false;
        }
        return $currentTime >= $student['rest_start'] && $currentTime <= $student['rest_end'];
    }

    private function isLate(array $student, string $currentTime): bool
    {
        if (empty($student['start_time'])) {
            return false;
        }
        $grace = (int) ($student['late_grace_minutes'] ?? 15);
        $deadline = strtotime($student['start_time']) + ($grace * 60);
        return strtotime($currentTime) > $deadline;
    }
}
