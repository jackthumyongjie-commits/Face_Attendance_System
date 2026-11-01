<div class="panel dash-banner mb-3">
    <div class="fw-bold" style="font-size:1.2rem;">Welcome to FAC, <?= e($student['full_name']) ?></div>
    <div class="text-muted">Track attendance, register your face and manage leave applications.</div>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="stat-card"><div class="label">Present Days</div><div class="value"><?= (int)$stats['present'] ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="label">Late Days</div><div class="value"><?= (int)$stats['late'] ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="label">Leave Days</div><div class="value"><?= (int)$stats['leave_days'] ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="label">Attendance %</div><div class="value"><?= e($stats['percentage']) ?>%</div></div></div>
</div>
<div class="panel">
    <div class="panel-title">Welcome, <?= e($student['full_name']) ?></div>
    <p class="mb-2"><strong>Student Code:</strong> <?= e($student['student_code']) ?> &nbsp;|&nbsp; <strong>Class:</strong> <?= e($student['class_name'] ?? 'Unassigned') ?></p>
    <p class="mb-3"><strong>Face Status:</strong>
        <?php if ((int)$student['face_registered']===1): ?>
            <span class="badge text-bg-success">Registered (v<?= (int)$student['registration_version'] ?>)</span>
        <?php else: ?>
            <span class="badge text-bg-warning">Not Registered</span>
        <?php endif; ?>
    </p>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= url('/student/face/register') ?>" class="btn btn-outline-primary">Face Registration</a>
        <a href="<?= url('/student/attendance/scan') ?>" class="btn btn-primary">Scan Attendance</a>
        <a href="<?= url('/student/leave') ?>" class="btn btn-outline-secondary">Apply Leave</a>
    </div>
</div>
<div class="panel">
    <div class="panel-title">Today's Attendance</div>
    <?php if (!$todayAttendance): ?>
        <p class="text-muted mb-0">No attendance recorded yet today.</p>
    <?php else: ?>
        <div class="row g-2">
            <div class="col-md-3"><strong>Status:</strong> <?= e($todayAttendance['status']) ?></div>
            <div class="col-md-3"><strong>Check In:</strong> <?= e($todayAttendance['check_in_time'] ? date('H:i:s', strtotime($todayAttendance['check_in_time'])) : '-') ?></div>
            <div class="col-md-3"><strong>Check Out:</strong> <?= e($todayAttendance['check_out_time'] ? date('H:i:s', strtotime($todayAttendance['check_out_time'])) : '-') ?></div>
            <div class="col-md-3"><strong>Confidence:</strong> <?= e($todayAttendance['check_in_confidence'] ?? '-') ?></div>
        </div>
    <?php endif; ?>
</div>
