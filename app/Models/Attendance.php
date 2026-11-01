<?php

namespace App\Models;

use App\Core\Model;

class Attendance extends Model
{
    protected string $table = 'attendance';

    public function findToday(int $studentId, ?string $date = null): ?array
    {
        $date = $date ?: today();
        return $this->queryOne(
            'SELECT * FROM attendance WHERE student_id = ? AND attendance_date = ? LIMIT 1',
            [$studentId, $date]
        );
    }

    public function search(array $filters = []): array
    {
        $sql = 'SELECT a.*, s.student_code, u.full_name, c.name AS class_name
                FROM attendance a
                INNER JOIN students s ON s.id = a.student_id
                INNER JOIN users u ON u.id = s.user_id
                LEFT JOIN classes c ON c.id = a.class_id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['date'])) {
            $sql .= ' AND a.attendance_date = ?';
            $params[] = $filters['date'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= ' AND a.attendance_date >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= ' AND a.attendance_date <= ?';
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['month'])) {
            $sql .= ' AND DATE_FORMAT(a.attendance_date, "%Y-%m") = ?';
            $params[] = $filters['month'];
        }
        if (!empty($filters['student_id'])) {
            $sql .= ' AND a.student_id = ?';
            $params[] = $filters['student_id'];
        }
        if (!empty($filters['class_id'])) {
            $sql .= ' AND a.class_id = ?';
            $params[] = $filters['class_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND a.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['class_ids']) && is_array($filters['class_ids'])) {
            $placeholders = implode(',', array_fill(0, count($filters['class_ids']), '?'));
            $sql .= " AND a.class_id IN ($placeholders)";
            $params = array_merge($params, $filters['class_ids']);
        }

        $sql .= ' ORDER BY a.attendance_date DESC, a.check_in_time DESC';
        return $this->query($sql, $params);
    }

    public function todayStats(?string $date = null): array
    {
        $date = $date ?: today();
        $row = $this->queryOne(
            'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status IN ("checked_in","checked_out","late") THEN 1 ELSE 0 END) AS checked_in,
                SUM(CASE WHEN status = "checked_out" THEN 1 ELSE 0 END) AS checked_out,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) AS late,
                SUM(CASE WHEN status = "on_leave" THEN 1 ELSE 0 END) AS on_leave
             FROM attendance WHERE attendance_date = ?',
            [$date]
        );
        return [
            'total' => (int) ($row['total'] ?? 0),
            'checked_in' => (int) ($row['checked_in'] ?? 0),
            'checked_out' => (int) ($row['checked_out'] ?? 0),
            'late' => (int) ($row['late'] ?? 0),
            'on_leave' => (int) ($row['on_leave'] ?? 0),
        ];
    }

    public function studentStats(int $studentId): array
    {
        $row = $this->queryOne(
            'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status IN ("checked_in","checked_out") THEN 1 ELSE 0 END) AS present,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) AS late,
                SUM(CASE WHEN status = "on_leave" THEN 1 ELSE 0 END) AS leave_days,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) AS absent
             FROM attendance WHERE student_id = ?',
            [$studentId]
        );
        $total = (int) ($row['total'] ?? 0);
        $present = (int) ($row['present'] ?? 0) + (int) ($row['late'] ?? 0);
        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        return [
            'total' => $total,
            'present' => (int) ($row['present'] ?? 0),
            'late' => (int) ($row['late'] ?? 0),
            'leave_days' => (int) ($row['leave_days'] ?? 0),
            'absent' => (int) ($row['absent'] ?? 0),
            'percentage' => $percentage,
        ];
    }

    public function forStudent(int $studentId, array $filters = []): array
    {
        $filters['student_id'] = $studentId;
        return $this->search($filters);
    }
}
