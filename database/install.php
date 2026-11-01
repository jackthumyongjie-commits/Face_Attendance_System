<?php
/**
 * Optional CLI installer:
 *   php database/install.php
 *
 * Reads DB credentials from .env and imports database/install.sql
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/Helpers/functions.php';
require BASE_PATH . '/app/Core/Env.php';

\App\Core\Env::load(BASE_PATH . '/.env');

$host = env('DB_HOST', '127.0.0.1');
$port = env('DB_PORT', '3306');
$user = env('DB_USER', 'root');
$pass = env('DB_PASS', '');
$sqlFile = BASE_PATH . '/database/install.sql';

if (!is_file($sqlFile)) {
    fwrite(STDERR, "install.sql not found\n");
    exit(1);
}

try {
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);
    echo "Database imported successfully.\n";
    $pdo->exec('USE `' . env('DB_NAME', 'face_attendance') . '`');
    $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    echo "Users in database: {$count}\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Import failed: ' . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, "Update DB_USER / DB_PASS in .env, then run again.\n");
    exit(1);
}
