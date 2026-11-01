<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';
    private static ?array $cache = null;

    public function allKeyed(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $rows = $this->all('setting_key ASC');
        $keyed = [];
        foreach ($rows as $row) {
            $keyed[$row['setting_key']] = $row['setting_value'];
        }
        self::$cache = $keyed;
        return $keyed;
    }

    public function get(string $key, $default = null)
    {
        $all = $this->allKeyed();
        return $all[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $existing = $this->where('setting_key', $key);
        if ($existing) {
            $this->update((int) $existing['id'], ['setting_value' => (string) $value]);
        } else {
            $this->create([
                'setting_key' => $key,
                'setting_value' => (string) $value,
            ]);
        }
        self::$cache = null;
    }

    public function updateMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function faceSettings(): array
    {
        $all = $this->allKeyed();
        return [
            'min_face_size' => (float) ($all['min_face_size'] ?? 80),
            'max_face_size' => (float) ($all['max_face_size'] ?? 420),
            'min_brightness' => (float) ($all['min_brightness'] ?? 40),
            'max_brightness' => (float) ($all['max_brightness'] ?? 220),
            'min_sharpness' => (float) ($all['min_sharpness'] ?? 20),
            'detection_confidence' => (float) ($all['detection_confidence'] ?? 0.50),
            'face_match_threshold' => (float) ($all['face_match_threshold'] ?? 0.55),
            'min_attendance_confidence' => (float) ($all['min_attendance_confidence'] ?? 0.45),
            'stable_frames' => (int) ($all['stable_frames'] ?? 3),
            'duplicate_scan_interval' => (int) ($all['duplicate_scan_interval'] ?? 300),
            'registration_samples' => (int) ($all['registration_samples'] ?? 3),
        ];
    }
}
