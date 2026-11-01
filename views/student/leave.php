<div class="panel">
    <div class="panel-title">Submit Leave Application</div>
    <form method="post" action="<?= url('/student/leave') ?>" enctype="multipart/form-data" class="row g-3">
        <?= csrf_field() ?>
        <div class="col-md-4"><label class="form-label">Leave Date</label><input type="date" name="leave_date" class="form-control" required value="<?= e(old('leave_date')) ?>"></div>
        <div class="col-md-8"><label class="form-label">Supporting Document (PDF/JPG/PNG)</label><input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
        <div class="col-12"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="3" required><?= e(old('reason')) ?></textarea></div>
        <div class="col-12"><button class="btn btn-primary">Submit</button></div>
    </form>
</div>
<div class="panel">
    <div class="panel-title">My Leave Applications</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Date</th><th>Reason</th><th>Document</th><th>Status</th><th>Review Note</th></tr></thead>
            <tbody>
            <?php foreach ($leaves as $l): ?>
                <tr>
                    <td><?= e($l['leave_date']) ?></td>
                    <td><?= e($l['reason']) ?></td>
                    <td><?php if ($l['document_path']): ?><a href="<?= asset($l['document_path']) ?>" target="_blank">View</a><?php else: ?>-<?php endif; ?></td>
                    <td><?= e($l['status']) ?></td>
                    <td><?= e($l['review_note'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
