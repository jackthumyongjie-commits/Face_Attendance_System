<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, array_merge(['method:PUT'], $middleware));
    }

    public function delete(string $path, $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, array_merge(['method:DELETE'], $middleware));
    }

    private function add(string $method, string $path, $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH) ?: '/', '/');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        // Support method spoofing
        if ($method === 'POST' && !empty($_POST['_method'])) {
            $spoof = strtoupper($_POST['_method']);
            if (in_array($spoof, ['PUT', 'PATCH', 'DELETE'], true)) {
                // Keep as POST for matching routes registered with put/delete helpers
            }
        }

        foreach ($this->routes as $route) {
            $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            // Handle method middleware for spoofed verbs
            foreach ($route['middleware'] as $mw) {
                if (str_starts_with($mw, 'method:')) {
                    $required = substr($mw, 7);
                    $actual = strtoupper($_POST['_method'] ?? $method);
                    if ($actual !== $required) {
                        continue 2;
                    }
                }
            }

            $this->runMiddleware($route['middleware']);
            $this->invoke($route['handler'], $params);
            return;
        }

        http_response_code(404);
        View::render('errors/404', [], 'layouts/error');
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'method:')) {
                continue;
            }
            $class = "App\\Middleware\\" . $this->middlewareClass($mw);
            if (!class_exists($class)) {
                continue;
            }
            (new $class())->handle();
        }
    }

    private function middlewareClass(string $name): string
    {
        $map = [
            'guest' => 'GuestMiddleware',
            'auth' => 'AuthMiddleware',
            'admin' => 'AdminMiddleware',
            'teacher' => 'TeacherMiddleware',
            'student' => 'StudentMiddleware',
            'csrf' => 'CsrfMiddleware',
        ];
        return $map[$name] ?? ucfirst($name) . 'Middleware';
    }

    private function invoke($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$controller, $method] = explode('@', $handler);
            $class = "App\\Controllers\\{$controller}";
            if (!class_exists($class)) {
                throw new \RuntimeException("Controller not found: {$class}");
            }
            $instance = new $class();
            call_user_func_array([$instance, $method], $params);
            return;
        }

        throw new \RuntimeException('Invalid route handler');
    }
}
