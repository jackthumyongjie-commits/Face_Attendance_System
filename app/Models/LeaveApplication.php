<?php

namespace App\Models;

use App\Core\Model;

class LeaveApplication extends Model
{
    protected string $table = 'student_leave_applications';

    public function search(array $filters = []): array
    {
        $sql = 'SELECT l.*, s.student_code, u.full_name, c.name AS class_name,
                       ru.full_name AS reviewer_name
                FROM student_leave_applications l
                INNER JOIN students s ON s.id = l.student_id
                INNER JOIN users u ON u.id = s.user_id
                LEFT JOIN classes c ON c.id = s.class_id
                LEFT JOIN users ru ON ru.id = l.reviewed_by
                WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND l.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['student_id'])) {
            $sql .= ' AND l.student_id = ?';
            $params[] = $filters['student_id'];
        }
        if (!empty($filters['date'])) {
            $sql .= ' AND l.leave_date = ?';
            $params[] = $filters['date'];
        }

        $sql .= ' ORDER BY l.created_at DESC';
        return $this->query($sql, $params);
    }

    public function countPending(): int
    {
        return $this->count("status = 'pending'");
    }

    public function hasApprovedLeave(int $studentId, string $date): bool
    {
        $row = $this->queryOne(
            "SELECT id FROM student_leave_applications
             WHERE student_id = ? AND leave_date = ? AND status = 'approved' LIMIT 1",
            [$studentId, $date]
        );
        return $row !== null;
    }

    public function findWithDetails(int $id): ?array
    {
        return $this->queryOne(
            'SELECT l.*, s.student_code, u.full_name, u.email, c.name AS class_name
             FROM student_leave_applications l
             INNER JOIN students s ON s.id = l.student_id
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             WHERE l.id = ? LIMIT 1',
            [$id]
        );
    }
}
