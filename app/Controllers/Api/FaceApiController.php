<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Setting;
use App\Models\Student;
use App\Services\AttendanceService;
use App\Services\FaceService;

class FaceApiController extends Controller
{
    public function settings(): void
    {
        $this->json([
            'success' => true,
            'settings' => (new Setting())->faceSettings(),
            'models_path' => asset(ltrim(config('app.face_models_path'), '/')),
        ]);
    }

    public function descriptors(): void
    {
        if (!Auth::check() || !in_array(Auth::role(), ['admin', 'teacher', 'student'], true)) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $this->json([
            'success' => true,
            'students' => (new FaceService())->getActiveDescriptorsPayload(),
        ]);
    }

    public function register(): void
    {
        if (!Auth::check()) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $payload = $this->jsonInput();
        $studentId = (int) ($payload['student_id'] ?? 0);
        $descriptors = $payload['descriptors'] ?? [];
        $preview = $payload['preview_image'] ?? null;

        if (Auth::is('student')) {
            $student = (new Student())->findByUserId(Auth::id());
            if (!$student || (int) $student['id'] !== $studentId) {
                $this->json(['success' => false, 'message' => 'Unauthorized student.'], 403);
            }
        } elseif (!Auth::is('admin')) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $result = (new FaceService())->register($studentId, $descriptors, $preview);
        $this->json($result, $result['success'] ? 200 : 422);
    }

    public function attendance(): void
    {
        if (!Auth::check() || !in_array(Auth::role(), ['admin', 'student'], true)) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $payload = $this->jsonInput();
        $studentId = (int) ($payload['student_id'] ?? 0);
        $confidence = (float) ($payload['confidence'] ?? 0);
        $photo = $payload['photo'] ?? null;

        if (Auth::is('student')) {
            $student = (new Student())->findByUserId(Auth::id());
            if (!$student || (int) $student['id'] !== $studentId) {
                $this->json(['success' => false, 'message' => 'You can only scan for your own attendance.'], 403);
            }
        }

        if ($studentId <= 0) {
            $this->json(['success' => false, 'message' => 'Unknown face.'], 422);
        }

        $result = (new AttendanceService())->processScan($studentId, $confidence, $photo);
        $this->json($result, $result['success'] ? 200 : 422);
    }

    private function jsonInput(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw ?: '[]', true);
        if (!is_array($data)) {
            // fallback to form post
            return $_POST;
        }
        return $data;
    }
}
