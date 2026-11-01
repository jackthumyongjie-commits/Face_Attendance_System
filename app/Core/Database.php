<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $host = config('database.host');
            $port = config('database.port');
            $name = config('database.name');
            $user = config('database.user');
            $pass = config('database.pass');
            $charset = config('database.charset', 'utf8mb4');

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                if (config('app.debug')) {
                    throw $e;
                }
                http_response_code(500);
                echo 'Database connection failed.';
                exit;
            }
        }

        return self::$pdo;
    }
}
