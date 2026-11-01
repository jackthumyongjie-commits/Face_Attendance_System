<div class="panel">
    <div class="panel-title">Class Attendance</div>
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-3"><input type="date" name="date" class="form-control" value="<?= e($filters['date'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select name="class_id" class="form-select">
                <option value="">All Assigned Classes</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ((int)($filters['class_id']??0)===(int)$c['id'])?'selected':'' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <?php foreach (['checked_in','checked_out','late','on_leave'] as $st): ?>
                    <option value="<?= $st ?>" <?= ($filters['status']??'')===$st?'selected':'' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Date</th><th>Student</th><th>Class</th><th>Check In</th><th>Check Out</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= e($r['attendance_date']) ?></td>
                    <td><a href="<?= url('/teacher/students/'.$r['student_id'].'/attendance') ?>"><?= e($r['full_name']) ?></a></td>
                    <td><?= e($r['class_name'] ?? '-') ?></td>
                    <td><?= e($r['check_in_time'] ? date('H:i:s', strtotime($r['check_in_time'])) : '-') ?></td>
                    <td><?= e($r['check_out_time'] ? date('H:i:s', strtotime($r['check_out_time'])) : '-') ?></td>
                    <td><?= e($r['status']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
