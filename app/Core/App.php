<?php

namespace App\Core;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        date_default_timezone_set(config('app.timezone', 'UTC'));

        $this->startSession();
        $this->registerRoutes();

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $this->resolveUri();
        $this->router->dispatch($method, $uri);
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $name = config('app.session.name', 'face_attendance_session');
        session_name($name);
        session_set_cookie_params([
            'lifetime' => config('app.session.lifetime', 7200),
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    private function resolveUri(): string
    {
        if (isset($_GET['url'])) {
            $uri = rawurldecode((string) $_GET['url']);
            $uri = '/' . trim($uri, '/');
            return $uri === '/' ? '/' : rtrim($uri, '/');
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
        if ($base && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim($path, '/');
        if ($path === '/index.php') {
            return '/';
        }
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    private function registerRoutes(): void
    {
        require BASE_PATH . '/config/routes.php';
    }
}
