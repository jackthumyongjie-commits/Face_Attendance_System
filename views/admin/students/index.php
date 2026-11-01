<div class="panel">
    <div class="panel-title">
        <span>Student List</span>
        <div class="d-flex gap-2">
            <a href="<?= url('/admin/students/export') ?>" class="btn btn-sm btn-outline-secondary">Export CSV</a>
            <a href="<?= url('/admin/students/create') ?>" class="btn btn-sm btn-primary">Add Student</a>
        </div>
    </div>
    <form class="row g-2 mb-3" method="get">
        <div class="col-md-4">
            <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" class="form-control" placeholder="Search name, code, email...">
        </div>
        <div class="col-md-3">
            <select name="class_id" class="form-select">
                <option value="">All Classes</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ((int)($filters['classId'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="1" <?= ($filters['status'] ?? null) === 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= ($filters['status'] ?? null) === 0 ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Filter</button></div>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Code</th><th>Name</th><th>Email</th><th>Class</th><th>Face</th><th>Status</th><th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= e($s['student_code']) ?></td>
                    <td><?= e($s['full_name']) ?></td>
                    <td><?= e($s['email']) ?></td>
                    <td><?= e($s['class_name'] ?? '-') ?></td>
                    <td>
                        <?php if ((int)$s['face_registered'] === 1): ?>
                            <span class="badge text-bg-success">Registered v<?= (int)$s['registration_version'] ?></span>
                        <?php else: ?>
                            <span class="badge text-bg-warning">Not Registered</span>
                        <?php endif; ?>
                    </td>
                    <td><?= (int)$s['status'] === 1 ? 'Active' : 'Inactive' ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/students/'.$s['id']) ?>">View</a>
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('/admin/students/'.$s['id'].'/edit') ?>">Edit</a>
                        <a class="btn btn-sm btn-outline-success" href="<?= url('/admin/students/'.$s['id'].'/face') ?>">Face</a>
                        <form class="d-inline" method="post" action="<?= url('/admin/students/'.$s['id'].'/delete') ?>" onsubmit="return confirm('Delete this student?')">
                            <?= csrf_field() ?><?= method_field('DELETE') ?>
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
