<?php

namespace App\Models;

use App\Core\Model;

class TeacherAssignment extends Model
{
    protected string $table = 'teacher_assignments';

    public function listAll(): array
    {
        return $this->query(
            'SELECT ta.*, u.full_name AS teacher_name, t.teacher_code,
                    c.name AS class_name, c.code AS class_code,
                    s.name AS subject_name, s.code AS subject_code
             FROM teacher_assignments ta
             INNER JOIN teachers t ON t.id = ta.teacher_id
             INNER JOIN users u ON u.id = t.user_id
             INNER JOIN classes c ON c.id = ta.class_id
             INNER JOIN subjects s ON s.id = ta.subject_id
             ORDER BY u.full_name, c.name, s.name'
        );
    }

    public function exists(int $teacherId, int $classId, int $subjectId): bool
    {
        $row = $this->queryOne(
            'SELECT id FROM teacher_assignments WHERE teacher_id = ? AND class_id = ? AND subject_id = ? LIMIT 1',
            [$teacherId, $classId, $subjectId]
        );
        return $row !== null;
    }
}
