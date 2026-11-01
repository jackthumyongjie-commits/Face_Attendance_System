<div class="panel dash-banner mb-3">
    <div class="fw-bold" style="font-size:1.2rem;">Welcome, <?= e($teacher['full_name']) ?></div>
    <div class="text-muted">FAC Teacher Portal — view your classes, subjects and attendance.</div>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="stat-card"><div class="label">Assigned Classes/Subjects</div><div class="value"><?= count($assignments) ?></div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="label">Students</div><div class="value"><?= count($students) ?></div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="label">Today's Records</div><div class="value"><?= count($attendance) ?></div></div></div>
</div>
<div class="panel">
    <div class="panel-title">My Assignments</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Class</th><th>Subject</th></tr></thead>
            <tbody>
            <?php foreach ($assignments as $a): ?>
                <tr><td><?= e($a['class_name']) ?></td><td><?= e($a['subject_name']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="panel">
    <div class="panel-title">Today's Attendance</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Student</th><th>Code</th><th>Class</th><th>Check In</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($attendance)): ?>
                <tr><td colspan="5" class="text-muted text-center">No attendance yet.</td></tr>
            <?php else: foreach ($attendance as $r): ?>
                <tr>
                    <td><a href="<?= url('/teacher/students/'.$r['student_id'].'/attendance') ?>"><?= e($r['full_name']) ?></a></td>
                    <td><?= e($r['student_code']) ?></td>
                    <td><?= e($r['class_name'] ?? '-') ?></td>
                    <td><?= e($r['check_in_time'] ? date('H:i:s', strtotime($r['check_in_time'])) : '-') ?></td>
                    <td><?= e($r['status']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
