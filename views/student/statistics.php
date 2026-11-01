<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-xl"><div class="stat-card"><div class="label">Total Records</div><div class="value"><?= (int)$stats['total'] ?></div></div></div>
    <div class="col-6 col-md-4 col-xl"><div class="stat-card"><div class="label">Present</div><div class="value"><?= (int)$stats['present'] ?></div></div></div>
    <div class="col-6 col-md-4 col-xl"><div class="stat-card"><div class="label">Late</div><div class="value"><?= (int)$stats['late'] ?></div></div></div>
    <div class="col-6 col-md-4 col-xl"><div class="stat-card"><div class="label">Leave</div><div class="value"><?= (int)$stats['leave_days'] ?></div></div></div>
    <div class="col-6 col-md-4 col-xl"><div class="stat-card"><div class="label">Attendance %</div><div class="value"><?= e($stats['percentage']) ?>%</div></div></div>
</div>
<div class="panel">
    <div class="panel-title">Recent Records</div>
    <div class="progress mb-3" style="height:22px">
        <div class="progress-bar bg-success" style="width: <?= min(100, (float)$stats['percentage']) ?>%"><?= e($stats['percentage']) ?>%</div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead><tr><th>Date</th><th>Status</th><th>Check In</th><th>Check Out</th></tr></thead>
            <tbody>
            <?php foreach (array_slice($records, 0, 20) as $r): ?>
                <tr>
                    <td><?= e($r['attendance_date']) ?></td>
                    <td><?= e($r['status']) ?></td>
                    <td><?= e($r['check_in_time'] ?? '-') ?></td>
                    <td><?= e($r['check_out_time'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
