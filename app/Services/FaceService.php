<?php

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\FaceDescriptor;
use App\Models\FaceRegistration;
use App\Models\Student;

class FaceService
{
    private FaceRegistration $registrations;
    private FaceDescriptor $descriptors;
    private Student $students;
    private UploadService $uploads;
    private AuditService $audit;

    public function __construct()
    {
        $this->registrations = new FaceRegistration();
        $this->descriptors = new FaceDescriptor();
        $this->students = new Student();
        $this->uploads = new UploadService();
        $this->audit = new AuditService();
    }

    public function register(int $studentId, array $descriptors, ?string $previewBase64 = null): array
    {
        $student = $this->students->find($studentId);
        if (!$student) {
            return ['success' => false, 'message' => 'Student not found.'];
        }
        if (empty($descriptors)) {
            return ['success' => false, 'message' => 'No face descriptors provided.'];
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $newVersion = ((int) $student['registration_version']) + 1;
            $isReregister = (int) $student['face_registered'] === 1;

            $this->registrations->deactivateForStudent($studentId);
            $this->descriptors->deactivateForStudent($studentId);

            $previewPath = null;
            if ($previewBase64) {
                $previewPath = $this->uploads->saveBase64Image($previewBase64, 'faces');
            }

            $registrationId = $this->registrations->create([
                'student_id' => $studentId,
                'version' => $newVersion,
                'sample_count' => count($descriptors),
                'preview_image' => $previewPath,
                'is_active' => 1,
                'registered_by' => Auth::id(),
            ]);

            $index = 1;
            foreach ($descriptors as $descriptor) {
                if (!is_array($descriptor)) {
                    continue;
                }
                $this->descriptors->create([
                    'student_id' => $studentId,
                    'registration_id' => $registrationId,
                    'descriptor_json' => json_encode(array_values($descriptor)),
                    'sample_index' => $index++,
                    'is_active' => 1,
                ]);
            }

            $this->students->update($studentId, [
                'face_registered' => 1,
                'registration_version' => $newVersion,
            ]);

            $pdo->commit();

            $this->audit->log(
                $isReregister ? 'face_re_registered' : 'face_registered',
                Auth::id(),
                $studentId,
                ['samples' => count($descriptors)],
                null,
                $newVersion
            );

            return [
                'success' => true,
                'message' => 'Face registration successful.',
                'version' => $newVersion,
            ];
        } catch (\Throwable $e) {
            $pdo->rollBack();
            if (config('app.debug')) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
            return ['success' => false, 'message' => 'Face registration failed.'];
        }
    }

    public function getActiveDescriptorsPayload(): array
    {
        $rows = $this->descriptors->activeAll();
        $grouped = [];
        foreach ($rows as $row) {
            $sid = (int) $row['student_id'];
            if (!isset($grouped[$sid])) {
                $grouped[$sid] = [
                    'student_id' => $sid,
                    'student_code' => $row['student_code'],
                    'full_name' => $row['full_name'],
                    'class_id' => $row['class_id'] ? (int) $row['class_id'] : null,
                    'descriptors' => [],
                ];
            }
            $grouped[$sid]['descriptors'][] = json_decode($row['descriptor_json'], true);
        }
        return array_values($grouped);
    }
}
