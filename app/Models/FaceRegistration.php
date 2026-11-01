<?php

namespace App\Models;

use App\Core\Model;

class FaceRegistration extends Model
{
    protected string $table = 'face_registrations';

    public function deactivateForStudent(int $studentId): void
    {
        $this->execute(
            'UPDATE face_registrations SET is_active = 0 WHERE student_id = ?',
            [$studentId]
        );
    }

    public function history(int $studentId): array
    {
        return $this->query(
            'SELECT fr.*, u.full_name AS registered_by_name
             FROM face_registrations fr
             LEFT JOIN users u ON u.id = fr.registered_by
             WHERE fr.student_id = ?
             ORDER BY fr.version DESC',
            [$studentId]
        );
    }
}
