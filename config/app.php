<?php

return [
    'name' => env('APP_NAME', 'Face Attendance System'),
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/'),
    'env' => env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN),
    'key' => env('APP_KEY', 'fas_default_key_change_me'),
    'timezone' => 'Asia/Kuala_Lumpur',
    'upload_max_size' => (int) env('UPLOAD_MAX_SIZE', 5242880),
    'face_models_path' => env('FACE_MODELS_PATH', '/assets/models'),
    'session' => [
        'name' => env('SESSION_NAME', 'face_attendance_session'),
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
    ],
];
