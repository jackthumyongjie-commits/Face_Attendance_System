<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Setting;
use App\Services\AuditService;

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->view('admin/settings/index', [
            'title' => 'Face Recognition Settings',
            'settings' => (new Setting())->faceSettings(),
        ]);
    }

    public function update(): void
    {
        $keys = [
            'min_face_size', 'max_face_size', 'min_brightness', 'max_brightness',
            'min_sharpness', 'detection_confidence', 'face_match_threshold',
            'min_attendance_confidence', 'stable_frames', 'duplicate_scan_interval',
            'registration_samples',
        ];
        $values = [];
        foreach ($keys as $key) {
            if (isset($_POST[$key]) && $_POST[$key] !== '') {
                $values[$key] = $_POST[$key];
            }
        }
        (new Setting())->updateMany($values);
        (new AuditService())->log('settings_changed', null, null, $values);
        flash('success', 'Settings updated successfully.');
        $this->redirect('/admin/settings');
    }
}
