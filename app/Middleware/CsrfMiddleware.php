<?php

namespace App\Middleware;

use App\Core\Csrf;

class CsrfMiddleware
{
    public function handle(): void
    {
        // Prefer header for JSON APIs so php://input remains available to controllers.
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['_token'] ?? null;
        if (!Csrf::verify($token)) {
            if ($this->wantsJson()) {
                json_response(['success' => false, 'message' => 'Invalid CSRF token.'], 419);
            }
            http_response_code(419);
            flash('error', 'Invalid security token. Please try again.');
            $referer = $_SERVER['HTTP_REFERER'] ?? url('/login');
            header('Location: ' . $referer);
            exit;
        }
    }

    private function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return stripos($accept, 'application/json') !== false
            || strtolower($xhr) === 'xmlhttprequest'
            || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
    }
}
