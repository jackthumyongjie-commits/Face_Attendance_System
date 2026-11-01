<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected string $table = 'audit_logs';

    public function recent(int $limit = 100, ?string $action = null): array
    {
        $sql = 'SELECT a.*, u.full_name AS user_name, u.username,
                       s.student_code, su.full_name AS student_name
                FROM audit_logs a
                LEFT JOIN users u ON u.id = a.user_id
                LEFT JOIN students s ON s.id = a.student_id
                LEFT JOIN users su ON su.id = s.user_id
                WHERE 1=1';
        $params = [];
        if ($action) {
            $sql .= ' AND a.action = ?';
            $params[] = $action;
        }
        $sql .= ' ORDER BY a.created_at DESC LIMIT ' . (int) $limit;
        return $this->query($sql, $params);
    }
}
