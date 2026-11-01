<?php

namespace App\Core;

use App\Models\User;
use App\Services\AuditService;

class Auth
{
    public static function attempt(string $login, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findByLogin($login);

        if (!$user || (int) $user['status'] !== 1) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['username'] = $user['username'];

        (new AuditService())->log('login', (int) $user['id'], null, [
            'username' => $user['username'],
        ]);

        return true;
    }

    public static function logout(): void
    {
        if (self::check()) {
            (new AuditService())->log('logout', self::id());
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        static $cached = null;
        if ($cached === null || (int) ($cached['id'] ?? 0) !== self::id()) {
            $cached = (new User())->find(self::id());
        }
        return $cached;
    }

    public static function is(string $role): bool
    {
        return self::role() === $role;
    }
}
