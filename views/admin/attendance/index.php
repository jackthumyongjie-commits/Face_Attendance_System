<div class="panel">
    <div class="panel-title">
        <span>Attendance Records</span>
        <a class="btn btn-sm btn-outline-secondary" href="<?= url('/admin/attendance/export?'.http_build_query(array_filter($filters))) ?>">Export CSV</a>
    </div>
    <form class="row g-2 mb-3" method="get">
        <div class="col-md-2"><input type="date" name="date" class="form-control" value="<?= e($filters['date'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="class_id" class="form-select">
                <option value="">All Classes</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ((int)($filters['class_id']??0)===(int)$c['id'])?'selected':'' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="student_id" class="form-select">
                <option value="">All Students</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?= (int)$s['id'] ?>" <?= ((int)($filters['student_id']??0)===(int)$s['id'])?'selected':'' ?>><?= e($s['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <?php foreach (['checked_in','checked_out','late','absent','on_leave'] as $st): ?>
                    <option value="<?= $st ?>" <?= ($filters['status']??'')===$st?'selected':'' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Date</th><th>Student</th><th>Code</th><th>Class</th><th>Check In</th><th>Check Out</th><th>Confidence</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($records)): ?>
                <tr><td colspan="8" class="text-center text-muted">No records found.</td></tr>
            <?php else: foreach ($records as $r): ?>
                <tr>
                    <td><?= e($r['attendance_date']) ?></td>
                    <td><?= e($r['full_name']) ?></td>
                    <td><?= e($r['student_code']) ?></td>
                    <td><?= e($r['class_name'] ?? '-') ?></td>
                    <td><?= e($r['check_in_time'] ? date('H:i:s', strtotime($r['check_in_time'])) : '-') ?></td>
                    <td><?= e($r['check_out_time'] ? date('H:i:s', strtotime($r['check_out_time'])) : '-') ?></td>
                    <td><?= e($r['check_out_confidence'] ?: $r['check_in_confidence'] ?: '-') ?></td>
                    <td><span class="badge text-bg-secondary badge-status"><?= e($r['status']) ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
