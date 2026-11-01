<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Helpers/functions.php';
require BASE_PATH . '/app/Core/Env.php';

\App\Core\Env::load(BASE_PATH . '/.env');

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

try {
    $app = new \App\Core\App();
    $app->run();
} catch (Throwable $e) {
    if (filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
        http_response_code(500);
        echo '<h1>Application Error</h1><pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo 'An unexpected error occurred.';
    }
}
