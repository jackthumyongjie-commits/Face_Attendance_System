<?php

namespace App\Models;

use App\Core\Model;

class FaceDescriptor extends Model
{
    protected string $table = 'face_descriptors';

    public function deactivateForStudent(int $studentId): void
    {
        $this->execute(
            'UPDATE face_descriptors SET is_active = 0 WHERE student_id = ?',
            [$studentId]
        );
    }

    public function activeAll(): array
    {
        return $this->query(
            'SELECT fd.*, s.student_code, u.full_name, s.class_id
             FROM face_descriptors fd
             INNER JOIN students s ON s.id = fd.student_id
             INNER JOIN users u ON u.id = s.user_id
             WHERE fd.is_active = 1 AND s.status = 1 AND s.face_registered = 1'
        );
    }

    public function activeForStudent(int $studentId): array
    {
        return $this->whereAll('student_id', $studentId);
    }
}
