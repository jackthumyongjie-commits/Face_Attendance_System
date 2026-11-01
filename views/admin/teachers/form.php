<?php $isEdit = !empty($teacher); ?>
<div class="panel">
    <div class="panel-title"><?= $isEdit ? 'Edit Teacher' : 'Add Teacher' ?></div>
    <form method="post" action="<?= $isEdit ? url('/admin/teachers/'.$teacher['id']) : url('/admin/teachers') ?>">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><?= method_field('PUT') ?><?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input class="form-control" name="full_name" required value="<?= e(old('full_name', $teacher['full_name'] ?? '')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Teacher Code</label><input class="form-control" name="teacher_code" required value="<?= e(old('teacher_code', $teacher['teacher_code'] ?? '')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Username</label><input class="form-control" name="username" required value="<?= e(old('username', $teacher['username'] ?? '')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required value="<?= e(old('email', $teacher['email'] ?? '')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Password <?= $isEdit?'(leave blank to keep)':'' ?></label><input type="password" class="form-control" name="password" <?= $isEdit?'':'required' ?>></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?= e(old('phone', $teacher['phone'] ?? '')) ?>"></div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <?php $st=(int)old('status',$teacher['status']??1); ?>
                <select name="status" class="form-select"><option value="1" <?= $st===1?'selected':'' ?>>Active</option><option value="0" <?= $st===0?'selected':'' ?>>Inactive</option></select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Save</button>
            <a class="btn btn-outline-secondary" href="<?= url('/admin/teachers') ?>">Cancel</a>
        </div>
    </form>
</div>
