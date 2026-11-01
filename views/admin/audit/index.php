<div class="panel">
    <div class="panel-title">Audit Logs</div>
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-4"><input type="text" name="action" class="form-control" placeholder="Filter by action" value="<?= e($filters['action'] ?? '') ?>"></div>
        <div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
    </form>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead><tr><th>Time</th><th>User</th><th>Student</th><th>Action</th><th>IP</th><th>Device</th><th>Browser</th><th>Confidence</th></tr></thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= e($log['created_at']) ?></td>
                    <td><?= e($log['user_name'] ?? '-') ?></td>
                    <td><?= e($log['student_name'] ?? ($log['student_code'] ?? '-')) ?></td>
                    <td><?= e($log['action']) ?></td>
                    <td><?= e($log['ip_address']) ?></td>
                    <td><?= e($log['device']) ?></td>
                    <td><?= e($log['browser']) ?></td>
                    <td><?= e($log['confidence_score'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
