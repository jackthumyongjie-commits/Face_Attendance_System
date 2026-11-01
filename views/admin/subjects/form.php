<?php $isEdit=!empty($subject); ?>
<div class="panel">
    <div class="panel-title"><?= $isEdit?'Edit Subject':'Add Subject' ?></div>
    <form method="post" action="<?= $isEdit?url('/admin/subjects/'.$subject['id']):url('/admin/subjects') ?>">
        <?= csrf_field() ?><?php if($isEdit): ?><?= method_field('PUT') ?><?php endif; ?>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Code</label><input class="form-control" name="code" required value="<?= e(old('code',$subject['code']??'')) ?>"></div>
            <div class="col-md-8"><label class="form-label">Name</label><input class="form-control" name="name" required value="<?= e(old('name',$subject['name']??'')) ?>"></div>
            <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"><?= e(old('description',$subject['description']??'')) ?></textarea></div>
            <div class="col-md-4"><label class="form-label">Status</label><?php $st=(int)old('status',$subject['status']??1); ?><select name="status" class="form-select"><option value="1" <?= $st===1?'selected':'' ?>>Active</option><option value="0" <?= $st===0?'selected':'' ?>>Inactive</option></select></div>
        </div>
        <div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Save</button><a href="<?= url('/admin/subjects') ?>" class="btn btn-outline-secondary">Cancel</a></div>
    </form>
</div>
