<?php

namespace App\Models;

use App\Core\Model;

class ClassSubject extends Model
{
    protected string $table = 'class_subjects';

    public function listAll(): array
    {
        return $this->query(
            'SELECT cs.*, c.name AS class_name, c.code AS class_code,
                    s.name AS subject_name, s.code AS subject_code
             FROM class_subjects cs
             INNER JOIN classes c ON c.id = cs.class_id
             INNER JOIN subjects s ON s.id = cs.subject_id
             ORDER BY c.name, s.name'
        );
    }

    public function exists(int $classId, int $subjectId): bool
    {
        $row = $this->queryOne(
            'SELECT id FROM class_subjects WHERE class_id = ? AND subject_id = ? LIMIT 1',
            [$classId, $subjectId]
        );
        return $row !== null;
    }

    public function forClass(int $classId): array
    {
        return $this->query(
            'SELECT cs.*, s.name AS subject_name, s.code AS subject_code
             FROM class_subjects cs
             INNER JOIN subjects s ON s.id = cs.subject_id
             WHERE cs.class_id = ?
             ORDER BY s.name',
            [$classId]
        );
    }
}
