<div class="panel">
    <div class="panel-title">Assign Subject to Class</div>
    <form method="post" action="<?= url('/admin/assignments/class-subjects') ?>" class="row g-2">
        <?= csrf_field() ?>
        <div class="col-md-5"><select name="class_id" class="form-select" required><option value="">Select Class</option><?php foreach($classes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-5"><select name="subject_id" class="form-select" required><option value="">Select Subject</option><?php foreach($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Assign</button></div>
    </form>
</div>
<div class="panel">
    <div class="panel-title">Current Assignments</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Class</th><th>Subject</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($assignments as $a): ?>
                <tr>
                    <td><?= e($a['class_name']) ?> (<?= e($a['class_code']) ?>)</td>
                    <td><?= e($a['subject_name']) ?> (<?= e($a['subject_code']) ?>)</td>
                    <td>
                        <form method="post" action="<?= url('/admin/assignments/class-subjects/'.$a['id'].'/delete') ?>" onsubmit="return confirm('Remove assignment?')">
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
