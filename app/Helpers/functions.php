<?php

function env(string $key, $default = null)
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = [
            'app' => require BASE_PATH . '/config/app.php',
            'database' => require BASE_PATH . '/config/database.php',
        ];
    }

    $segments = explode('.', $key);
    $value = $config;
    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function base_path(string $path = ''): string
{
    return BASE_PATH . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
}

function public_path(string $path = ''): string
{
    return BASE_PATH . '/public' . ($path ? '/' . ltrim($path, '/') : '');
}

function view_path(string $path = ''): string
{
    return BASE_PATH . '/views' . ($path ? '/' . ltrim($path, '/') : '');
}

function url(string $path = ''): string
{
    $base = rtrim(config('app.url'), '/');
    $path = ltrim(trim($path), '/');
    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function asset(string $path): string
{
    $base = rtrim(config('app.url'), '/');
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function old(string $key, $default = '')
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, $value = null)
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }
    $val = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $val;
}

function csrf_token(): string
{
    return \App\Core\Csrf::token();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
}

function auth_user(): ?array
{
    return \App\Core\Auth::user();
}

function is_logged_in(): bool
{
    return \App\Core\Auth::check();
}

function role_is(string $role): bool
{
    return \App\Core\Auth::role() === $role;
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function today(): string
{
    return date('Y-m-d');
}

function client_ip(): string
{
    return $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';
}

function user_agent(): string
{
    return substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 500);
}

function parse_browser(string $ua): string
{
    if (stripos($ua, 'Edg') !== false) return 'Microsoft Edge';
    if (stripos($ua, 'Chrome') !== false) return 'Chrome';
    if (stripos($ua, 'Firefox') !== false) return 'Firefox';
    if (stripos($ua, 'Safari') !== false) return 'Safari';
    return 'Unknown';
}

function parse_device(string $ua): string
{
    if (preg_match('/Mobile|Android|iPhone|iPad/i', $ua)) return 'Mobile';
    return 'Desktop';
}
