<?php

namespace App\Models;

use App\Core\Model;

class SchoolClass extends Model
{
    protected string $table = 'classes';

    public function active(): array
    {
        return $this->query('SELECT * FROM classes WHERE status = 1 ORDER BY name');
    }

    public function withStudentCount(): array
    {
        return $this->query(
            'SELECT c.*, COUNT(s.id) AS student_count
             FROM classes c
             LEFT JOIN students s ON s.class_id = c.id
             GROUP BY c.id
             ORDER BY c.name'
        );
    }
}
