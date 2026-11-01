<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $viewFile = view_path(str_replace('.', '/', $view) . '.php');
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo "View not found: {$view}";
            exit;
        }

        extract($data, EXTR_SKIP);
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
        $success = flash('success');
        $error = flash('error');
        $info = flash('info');

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = view_path(str_replace('.', '/', $layout) . '.php');
            if (!is_file($layoutFile)) {
                echo $content;
                return;
            }
            require $layoutFile;
            return;
        }

        echo $content;
    }

    public static function partial(string $partial, array $data = []): void
    {
        $file = view_path(str_replace('.', '/', $partial) . '.php');
        if (is_file($file)) {
            extract($data, EXTR_SKIP);
            require $file;
        }
    }
}
