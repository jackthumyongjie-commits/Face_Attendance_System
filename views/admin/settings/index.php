<div class="panel">
    <div class="panel-title">Face Recognition Settings</div>
    <form method="post" action="<?= url('/admin/settings') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <?php
            $fields = [
                'min_face_size' => 'Minimum Face Size',
                'max_face_size' => 'Maximum Face Size',
                'min_brightness' => 'Minimum Brightness',
                'max_brightness' => 'Maximum Brightness',
                'min_sharpness' => 'Minimum Sharpness',
                'detection_confidence' => 'Detection Confidence',
                'face_match_threshold' => 'Face Match Threshold (max distance)',
                'min_attendance_confidence' => 'Minimum Attendance Confidence',
                'stable_frames' => 'Stable Frames',
                'duplicate_scan_interval' => 'Duplicate Scan Interval (seconds)',
                'registration_samples' => 'Registration Samples',
            ];
            foreach ($fields as $key => $label):
            ?>
            <div class="col-md-4">
                <label class="form-label"><?= e($label) ?></label>
                <input type="number" step="any" class="form-control" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>" required>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-4"><button class="btn btn-primary">Save Settings</button></div>
    </form>
</div>
