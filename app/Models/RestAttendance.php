<?php

namespace App\Models;

use App\Core\Model;

class RestAttendance extends Model
{
    protected string $table = 'rest_attendance';

    public function findToday(int $studentId, ?string $date = null): ?array
    {
        $date = $date ?: today();
        return $this->queryOne(
            'SELECT * FROM rest_attendance WHERE student_id = ? AND attendance_date = ? LIMIT 1',
            [$studentId, $date]
        );
    }
}
