<?php

namespace App\Models;

use App\Core\Model;

class Student extends Model
{
    protected string $table = 'students';

    public function findWithDetails(int $id): ?array
    {
        return $this->queryOne(
            'SELECT s.*, u.username, u.email, u.full_name, u.status AS user_status,
                    c.name AS class_name, c.code AS class_code, c.start_time, c.late_grace_minutes,
                    c.rest_start, c.rest_end
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             WHERE s.id = ? LIMIT 1',
            [$id]
        );
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->queryOne(
            'SELECT s.*, u.username, u.email, u.full_name, c.name AS class_name, c.code AS class_code,
                    c.start_time, c.late_grace_minutes, c.rest_start, c.rest_end
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             WHERE s.user_id = ? LIMIT 1',
            [$userId]
        );
    }

    public function search(?string $q = null, ?int $classId = null, ?int $status = null): array
    {
        $sql = 'SELECT s.*, u.username, u.email, u.full_name, c.name AS class_name, c.code AS class_code
                FROM students s
                INNER JOIN users u ON u.id = s.user_id
                LEFT JOIN classes c ON c.id = s.class_id
                WHERE 1=1';
        $params = [];

        if ($q) {
            $sql .= ' AND (u.full_name LIKE ? OR s.student_code LIKE ? OR u.email LIKE ? OR u.username LIKE ?)';
            $like = '%' . $q . '%';
            $params = array_merge($params, [$like, $like, $like, $like]);
        }
        if ($classId) {
            $sql .= ' AND s.class_id = ?';
            $params[] = $classId;
        }
        if ($status !== null) {
            $sql .= ' AND s.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY u.full_name ASC';
        return $this->query($sql, $params);
    }

    public function countActive(): int
    {
        return $this->count('status = 1');
    }

    public function countFaceRegistered(): int
    {
        return $this->count('face_registered = 1 AND status = 1');
    }

    public function byClassIds(array $classIds): array
    {
        if (empty($classIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($classIds), '?'));
        return $this->query(
            "SELECT s.*, u.full_name, u.email, c.name AS class_name
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             WHERE s.class_id IN ($placeholders) AND s.status = 1
             ORDER BY u.full_name",
            $classIds
        );
    }

    public function exportRows(): array
    {
        return $this->query(
            'SELECT s.student_code, u.full_name, u.email, s.phone, s.gender, s.date_of_birth,
                    c.name AS class_name, s.status, s.face_registered, s.registration_version
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             ORDER BY s.student_code'
        );
    }
}
