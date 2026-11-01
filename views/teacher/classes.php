<div class="panel">
    <div class="panel-title">Assigned Classes & Subjects</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Class</th><th>Code</th><th>Subject</th></tr></thead>
            <tbody>
            <?php foreach ($assignments as $a): ?>
                <tr>
                    <td><?= e($a['class_name']) ?></td>
                    <td><?= e($a['class_code']) ?></td>
                    <td><?= e($a['subject_name']) ?> (<?= e($a['subject_code']) ?>)</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="panel">
    <div class="panel-title">Students in Assigned Classes</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Code</th><th>Name</th><th>Class</th><th>Face</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= e($s['student_code']) ?></td>
                    <td><?= e($s['full_name']) ?></td>
                    <td><?= e($s['class_name'] ?? '-') ?></td>
                    <td><?= (int)$s['face_registered']===1?'Yes':'No' ?></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="<?= url('/teacher/students/'.$s['id'].'/attendance') ?>">Attendance</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
