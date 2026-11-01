<div class="panel">
    <div class="panel-title"><?= e($student['full_name']) ?> — Attendance</div>
    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="stat-card"><div class="label">Present</div><div class="value"><?= (int)$stats['present'] ?></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="label">Late</div><div class="value"><?= (int)$stats['late'] ?></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="label">Leave</div><div class="value"><?= (int)$stats['leave_days'] ?></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="label">Attendance %</div><div class="value"><?= e($stats['percentage']) ?>%</div></div></div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Confidence</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= e($r['attendance_date']) ?></td>
                    <td><?= e($r['check_in_time'] ?? '-') ?></td>
                    <td><?= e($r['check_out_time'] ?? '-') ?></td>
                    <td><?= e($r['check_in_confidence'] ?? '-') ?></td>
                    <td><?= e($r['status']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
