<?php

namespace App\Models;

use App\Core\Model;

class Subject extends Model
{
    protected string $table = 'subjects';

    public function active(): array
    {
        return $this->query('SELECT * FROM subjects WHERE status = 1 ORDER BY name');
    }
}
