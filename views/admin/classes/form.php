<?php $isEdit=!empty($class); ?>
<div class="panel">
    <div class="panel-title"><?= $isEdit?'Edit Class':'Add Class' ?></div>
    <form method="post" action="<?= $isEdit?url('/admin/classes/'.$class['id']):url('/admin/classes') ?>">
        <?= csrf_field() ?><?php if($isEdit): ?><?= method_field('PUT') ?><?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Class Name</label><input class="form-control" name="name" required value="<?= e(old('name',$class['name']??'')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Code</label><input class="form-control" name="code" required value="<?= e(old('code',$class['code']??'')) ?>"></div>
            <div class="col-md-4"><label class="form-label">Start Time</label><input type="time" class="form-control" name="start_time" required value="<?= e(old('start_time', isset($class['start_time'])?substr($class['start_time'],0,5):'08:00')) ?>"></div>
            <div class="col-md-4"><label class="form-label">Late Grace (minutes)</label><input type="number" class="form-control" name="late_grace_minutes" min="0" required value="<?= e(old('late_grace_minutes',$class['late_grace_minutes']??15)) ?>"></div>
            <div class="col-md-4"><label class="form-label">Status</label><?php $st=(int)old('status',$class['status']??1); ?><select name="status" class="form-select"><option value="1" <?= $st===1?'selected':'' ?>>Active</option><option value="0" <?= $st===0?'selected':'' ?>>Inactive</option></select></div>
            <div class="col-md-6"><label class="form-label">Rest Start</label><input type="time" class="form-control" name="rest_start" value="<?= e(old('rest_start', isset($class['rest_start'])?substr((string)$class['rest_start'],0,5):'12:00')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Rest End</label><input type="time" class="form-control" name="rest_end" value="<?= e(old('rest_end', isset($class['rest_end'])?substr((string)$class['rest_end'],0,5):'13:00')) ?>"></div>
        </div>
        <div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Save</button><a href="<?= url('/admin/classes') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div>
