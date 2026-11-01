<div class="panel">
    <div class="panel-title"><span>Classes</span><a href="<?= url('/admin/classes/create') ?>" class="btn btn-sm btn-primary">Add Class</a></div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Code</th><th>Name</th><th>Start</th><th>Late Grace</th><th>Rest</th><th>Students</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($classes as $c): ?>
                <tr>
                    <td><?= e($c['code']) ?></td>
                    <td><?= e($c['name']) ?></td>
                    <td><?= e(substr($c['start_time'],0,5)) ?></td>
                    <td><?= (int)$c['late_grace_minutes'] ?> min</td>
                    <td><?= e(substr((string)$c['rest_start'],0,5)) ?>–<?= e(substr((string)$c['rest_end'],0,5)) ?></td>
                    <td><?= (int)$c['student_count'] ?></td>
                    <td><?= (int)$c['status']===1?'Active':'Inactive' ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('/admin/classes/'.$c['id'].'/edit') ?>">Edit</a>
                        <form class="d-inline" method="post" action="<?= url('/admin/classes/'.$c['id'].'/delete') ?>" onsubmit="return confirm('Delete this class?')">
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
