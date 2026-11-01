<?php $isEdit = !empty($student); ?>
<div class="panel">
    <div class="panel-title"><?= $isEdit ? 'Edit Student' : 'Add Student' ?></div>
    <form method="post" action="<?= $isEdit ? url('/admin/students/'.$student['id']) : url('/admin/students') ?>">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><?= method_field('PUT') ?><?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required value="<?= e(old('full_name', $student['full_name'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Student Code</label>
                <input type="text" name="student_code" class="form-control" required value="<?= e(old('student_code', $student['student_code'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required value="<?= e(old('username', $student['username'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="<?= e(old('email', $student['email'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Password <?= $isEdit ? '(leave blank to keep)' : '' ?></label>
                <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= e(old('phone', $student['phone'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <?php $g = old('gender', $student['gender'] ?? ''); ?>
                    <option value="">Select</option>
                    <option value="male" <?= $g==='male'?'selected':'' ?>>Male</option>
                    <option value="female" <?= $g==='female'?'selected':'' ?>>Female</option>
                    <option value="other" <?= $g==='other'?'selected':'' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="<?= e(old('date_of_birth', $student['date_of_birth'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select">
                    <option value="">Unassigned</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= ((int)old('class_id', $student['class_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php $st = (int) old('status', $student['status'] ?? 1); ?>
                    <option value="1" <?= $st===1?'selected':'' ?>>Active</option>
                    <option value="0" <?= $st===0?'selected':'' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a href="<?= url('/admin/students') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
