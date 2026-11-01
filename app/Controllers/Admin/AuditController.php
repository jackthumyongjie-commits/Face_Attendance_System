<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\AuditLog;

class AuditController extends Controller
{
    public function index(): void
    {
        $action = $_GET['action'] ?? '';
        $this->view('admin/audit/index', [
            'title' => 'Audit Logs',
            'logs' => (new AuditLog())->recent(200, $action ?: null),
            'filters' => ['action' => $action],
        ]);
    }
}
