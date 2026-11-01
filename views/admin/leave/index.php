<div class="panel">
    <div class="panel-title">Leave Applications</div>
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <?php foreach (['pending','approved','rejected'] as $st): ?>
                    <option value="<?= $st ?>" <?= ($filters['status']??'')===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Student</th><th>Code</th><th>Date</th><th>Reason</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($leaves as $l): ?>
                <tr>
                    <td><?= e($l['full_name']) ?></td>
                    <td><?= e($l['student_code']) ?></td>
                    <td><?= e($l['leave_date']) ?></td>
                    <td><?= e(mb_strimwidth($l['reason'],0,60,'...')) ?></td>
                    <td><span class="badge text-bg-secondary"><?= e($l['status']) ?></span></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/leave/'.$l['id']) ?>">Review</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
