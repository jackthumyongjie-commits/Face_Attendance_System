<div class="panel">
    <div class="panel-title">Attendance History</div>
    <?php if ($todayAttendance): ?>
        <div class="alert alert-info">Today: <?= e($todayAttendance['status']) ?> |
            In <?= e($todayAttendance['check_in_time'] ? date('H:i', strtotime($todayAttendance['check_in_time'])) : '-') ?> |
            Out <?= e($todayAttendance['check_out_time'] ? date('H:i', strtotime($todayAttendance['check_out_time'])) : '-') ?>
        </div>
    <?php endif; ?>
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-3"><input type="date" name="date" class="form-control" value="<?= e($filters['date'] ?? '') ?>"></div>
        <div class="col-md-3"><input type="month" name="month" class="form-control" value="<?= e($filters['month'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <?php foreach (['checked_in','checked_out','late','on_leave','absent'] as $st): ?>
                    <option value="<?= $st ?>" <?= ($filters['status']??'')===$st?'selected':'' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
    </form>
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
