<div class="panel">
    <div class="panel-title">
        <span>Teachers</span>
        <a href="<?= url('/admin/teachers/create') ?>" class="btn btn-sm btn-primary">Add Teacher</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Code</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($teachers as $t): ?>
                <tr>
                    <td><?= e($t['teacher_code']) ?></td>
                    <td><?= e($t['full_name']) ?></td>
                    <td><?= e($t['email']) ?></td>
                    <td><?= e($t['phone'] ?? '-') ?></td>
                    <td><?= (int)$t['status']===1?'Active':'Inactive' ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('/admin/teachers/'.$t['id'].'/edit') ?>">Edit</a>
                        <form class="d-inline" method="post" action="<?= url('/admin/teachers/'.$t['id'].'/delete') ?>" onsubmit="return confirm('Delete this teacher?')">
                            <?= csrf_field() ?><?= method_field('DELETE') ?>
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
