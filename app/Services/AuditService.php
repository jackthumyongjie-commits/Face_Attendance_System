<?php

namespace App\Services;

use App\Core\Auth;
use App\Models\AuditLog;

class AuditService
{
    private AuditLog $logs;

    public function __construct()
    {
        $this->logs = new AuditLog();
    }

    public function log(
        string $action,
        ?int $userId = null,
        ?int $studentId = null,
        array $metadata = [],
        ?float $confidence = null,
        ?int $registrationVersion = null
    ): void {
        $ua = user_agent();
        $this->logs->create([
            'user_id' => $userId ?? Auth::id(),
            'student_id' => $studentId,
            'action' => $action,
            'ip_address' => client_ip(),
            'device' => parse_device($ua),
            'browser' => parse_browser($ua),
            'confidence_score' => $confidence,
            'registration_version' => $registrationVersion,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
        ]);
    }
}
