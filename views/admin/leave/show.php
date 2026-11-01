<div class="panel">
    <div class="panel-title">Leave Application #<?= (int)$leave['id'] ?></div>
    <div class="row g-3 mb-3">
        <div class="col-md-6"><strong>Student:</strong> <?= e($leave['full_name']) ?> (<?= e($leave['student_code']) ?>)</div>
        <div class="col-md-6"><strong>Class:</strong> <?= e($leave['class_name'] ?? '-') ?></div>
        <div class="col-md-6"><strong>Leave Date:</strong> <?= e($leave['leave_date']) ?></div>
        <div class="col-md-6"><strong>Status:</strong> <?= e($leave['status']) ?></div>
        <div class="col-12"><strong>Reason:</strong><br><?= nl2br(e($leave['reason'])) ?></div>
        <?php if (!empty($leave['document_path'])): ?>
            <div class="col-12"><strong>Document:</strong> <a href="<?= asset($leave['document_path']) ?>" target="_blank"><?= e($leave['document_original_name'] ?: 'View document') ?></a></div>
        <?php endif; ?>
    </div>
    <?php if ($leave['status'] === 'pending'): ?>
    <form method="post" action="<?= url('/admin/leave/'.$leave['id'].'/review') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Review Note</label>
            <textarea name="review_note" class="form-control" rows="3"></textarea>
        </div>
        <button class="btn btn-success" name="action" value="approved">Approve</button>
        <button class="btn btn-danger" name="action" value="rejected">Reject</button>
        <a href="<?= url('/admin/leave') ?>" class="btn btn-outline-secondary">Back</a>
    </form>
    <?php else: ?>
        <p><strong>Review Note:</strong> <?= e($leave['review_note'] ?? '-') ?></p>
        <a href="<?= url('/admin/leave') ?>" class="btn btn-outline-secondary">Back</a>
    <?php endif; ?>
</div>
