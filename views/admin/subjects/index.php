<div class="panel">
    <div class="panel-title"><span>Subjects</span><a href="<?= url('/admin/subjects/create') ?>" class="btn btn-sm btn-primary">Add Subject</a></div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Code</th><th>Name</th><th>Description</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($subjects as $s): ?>
                <tr>
                    <td><?= e($s['code']) ?></td>
                    <td><?= e($s['name']) ?></td>
                    <td><?= e($s['description'] ?? '-') ?></td>
                    <td><?= (int)$s['status']===1?'Active':'Inactive' ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('/admin/subjects/'.$s['id'].'/edit') ?>">Edit</a>
                        <form class="d-inline" method="post" action="<?= url('/admin/subjects/'.$s['id'].'/delete') ?>" onsubmit="return confirm('Delete this subject?')">
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
