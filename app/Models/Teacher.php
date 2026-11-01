<?php

namespace App\Models;

use App\Core\Model;

class Teacher extends Model
{
    protected string $table = 'teachers';

    public function findWithDetails(int $id): ?array
    {
        return $this->queryOne(
            'SELECT t.*, u.username, u.email, u.full_name, u.status AS user_status
             FROM teachers t
             INNER JOIN users u ON u.id = t.user_id
             WHERE t.id = ? LIMIT 1',
            [$id]
        );
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->queryOne(
            'SELECT t.*, u.username, u.email, u.full_name
             FROM teachers t
             INNER JOIN users u ON u.id = t.user_id
             WHERE t.user_id = ? LIMIT 1',
            [$userId]
        );
    }

    public function allWithDetails(): array
    {
        return $this->query(
            'SELECT t.*, u.username, u.email, u.full_name
             FROM teachers t
             INNER JOIN users u ON u.id = t.user_id
             ORDER BY u.full_name'
        );
    }

    public function assignedClassIds(int $teacherId): array
    {
        $rows = $this->query(
            'SELECT DISTINCT class_id FROM teacher_assignments WHERE teacher_id = ?',
            [$teacherId]
        );
        return array_map(fn($r) => (int) $r['class_id'], $rows);
    }

    public function assignments(int $teacherId): array
    {
        return $this->query(
            'SELECT ta.*, c.name AS class_name, c.code AS class_code,
                    s.name AS subject_name, s.code AS subject_code
             FROM teacher_assignments ta
             INNER JOIN classes c ON c.id = ta.class_id
             INNER JOIN subjects s ON s.id = ta.subject_id
             WHERE ta.teacher_id = ?
             ORDER BY c.name, s.name',
            [$teacherId]
        );
    }
}
