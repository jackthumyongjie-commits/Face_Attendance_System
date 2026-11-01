<div class="row g-3 mb-3">
    <?php
    $cards = [
        ['Total Students', $stats['total_students'], 'bi-people'],
        ['Active Students', $stats['active_students'], 'bi-person-check'],
        ['Teachers', $stats['total_teachers'], 'bi-person-badge'],
        ['Classes', $stats['total_classes'], 'bi-building'],
        ["Today's Attendance", $stats['today_attendance'], 'bi-calendar-check'],
        ['Checked In', $stats['checked_in'], 'bi-box-arrow-in-right'],
        ['Checked Out', $stats['checked_out'], 'bi-box-arrow-right'],
        ['Late', $stats['late'], 'bi-alarm'],
        ['Pending Leave', $stats['pending_leave'], 'bi-hourglass-split'],
        ['Faces Registered', $stats['face_registered'], 'bi-emoji-smile'],
    ];
    foreach ($cards as [$label, $value, $icon]):
    ?>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="label"><?= e($label) ?></div>
                    <div class="value"><?= (int) $value ?></div>
                </div>
                <div class="icon"><i class="bi <?= e($icon) ?>"></i></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="panel dash-banner mb-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="fw-bold" style="font-size:1.25rem;letter-spacing:.02em;">FAC Admin Dashboard</div>
            <div class="text-muted">Monitor students, attendance, leave and face registration in one place.</div>
        </div>
        <a href="<?= url('/admin/kiosk') ?>" class="btn btn-primary btn-sm"><i class="bi bi-webcam"></i> Open Kiosk</a>
    </div>
</div>

<div class="panel">
    <div class="panel-title">
        <span>Today's Attendance</span>
        <a href="<?= url('/admin/attendance') ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th>Student</th>
                <th>Code</th>
                <th>Class</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($recentAttendance)): ?>
                <tr><td colspan="6" class="text-muted text-center">No attendance records for today.</td></tr>
            <?php else: foreach ($recentAttendance as $row): ?>
                <tr>
                    <td><?= e($row['full_name']) ?></td>
                    <td><?= e($row['student_code']) ?></td>
                    <td><?= e($row['class_name'] ?? '-') ?></td>
                    <td><?= e($row['check_in_time'] ? date('H:i:s', strtotime($row['check_in_time'])) : '-') ?></td>
                    <td><?= e($row['check_out_time'] ? date('H:i:s', strtotime($row['check_out_time'])) : '-') ?></td>
                    <td><span class="badge text-bg-secondary badge-status"><?= e($row['status']) ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
