<div class="panel">
    <div class="panel-title">Assign Teacher</div>
    <form method="post" action="<?= url('/admin/assignments/teachers') ?>" class="row g-2">
        <?= csrf_field() ?>
        <div class="col-md-3"><select name="teacher_id" class="form-select" required><option value="">Teacher</option><?php foreach($teachers as $t): ?><option value="<?= (int)$t['id'] ?>"><?= e($t['full_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><select name="class_id" class="form-select" required><option value="">Class</option><?php foreach($classes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><select name="subject_id" class="form-select" required><option value="">Subject</option><?php foreach($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><button class="btn btn-primary w-100">Assign</button></div>
    </form>
</div>
<div class="panel">
    <div class="panel-title">Teacher Assignments</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Teacher</th><th>Class</th><th>Subject</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($assignments as $a): ?>
                <tr>
                    <td><?= e($a['teacher_name']) ?> (<?= e($a['teacher_code']) ?>)</td>
                    <td><?= e($a['class_name']) ?></td>
                    <td><?= e($a['subject_name']) ?></td>
                    <td>
                        <form method="post" action="<?= url('/admin/assignments/teachers/'.$a['id'].'/delete') ?>" onsubmit="return confirm('Remove assignment?')">
                            <?= csrf_field() ?><?= method_field('DELETE') ?>
                            <button class="btn btn-sm btn-outline-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
