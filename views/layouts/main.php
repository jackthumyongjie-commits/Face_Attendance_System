<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> | FAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('assets/css/app.css') ?>?v=fac-status1" rel="stylesheet">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <script>window.APP_BASE = <?= json_encode(rtrim(config('app.url'), '/')) ?>;</script>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-mark">FAC</div>
            <div class="sidebar-brand-text">
                <strong>FAC</strong>
                <small>Face Attendance Control</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <?php $role = \App\Core\Auth::role(); ?>
            <?php if ($role === 'admin'): ?>
                <a href="<?= url('/admin/dashboard') ?>" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="<?= url('/admin/students') ?>" class="nav-link"><i class="bi bi-people"></i> Students</a>
                <a href="<?= url('/admin/teachers') ?>" class="nav-link"><i class="bi bi-person-badge"></i> Teachers</a>
                <a href="<?= url('/admin/classes') ?>" class="nav-link"><i class="bi bi-building"></i> Classes</a>
                <a href="<?= url('/admin/subjects') ?>" class="nav-link"><i class="bi bi-book"></i> Subjects</a>
                <a href="<?= url('/admin/assignments/class-subjects') ?>" class="nav-link"><i class="bi bi-link-45deg"></i> Class Subjects</a>
                <a href="<?= url('/admin/assignments/teachers') ?>" class="nav-link"><i class="bi bi-diagram-3"></i> Teacher Assign</a>
                <a href="<?= url('/admin/attendance') ?>" class="nav-link"><i class="bi bi-calendar-check"></i> Attendance</a>
                <a href="<?= url('/admin/leave') ?>" class="nav-link"><i class="bi bi-file-earmark-text"></i> Leave</a>
                <a href="<?= url('/admin/kiosk') ?>" class="nav-link"><i class="bi bi-webcam"></i> Kiosk</a>
                <a href="<?= url('/admin/settings') ?>" class="nav-link"><i class="bi bi-sliders"></i> Settings</a>
                <a href="<?= url('/admin/audit') ?>" class="nav-link"><i class="bi bi-journal-text"></i> Audit Logs</a>
                <a href="<?= url('/admin/users') ?>" class="nav-link"><i class="bi bi-shield-lock"></i> Users</a>
            <?php elseif ($role === 'teacher'): ?>
                <a href="<?= url('/teacher/dashboard') ?>" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="<?= url('/teacher/classes') ?>" class="nav-link"><i class="bi bi-building"></i> My Classes</a>
                <a href="<?= url('/teacher/attendance') ?>" class="nav-link"><i class="bi bi-calendar-check"></i> Attendance</a>
            <?php else: ?>
                <a href="<?= url('/student/dashboard') ?>" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="<?= url('/student/profile') ?>" class="nav-link"><i class="bi bi-person"></i> Profile</a>
                <a href="<?= url('/student/face/register') ?>" class="nav-link"><i class="bi bi-camera"></i> Face Register</a>
                <a href="<?= url('/student/attendance/scan') ?>" class="nav-link"><i class="bi bi-qr-code-scan"></i> Scan Attendance</a>
                <a href="<?= url('/student/attendance/history') ?>" class="nav-link"><i class="bi bi-clock-history"></i> History</a>
                <a href="<?= url('/student/attendance/statistics') ?>" class="nav-link"><i class="bi bi-bar-chart"></i> Statistics</a>
                <a href="<?= url('/student/leave') ?>" class="nav-link"><i class="bi bi-file-earmark-text"></i> Leave</a>
                <a href="<?= url('/student/password') ?>" class="nav-link"><i class="bi bi-key"></i> Password</a>
            <?php endif; ?>
            <a href="<?= url('/privacy') ?>" class="nav-link"><i class="bi bi-info-circle"></i> Privacy</a>
        </nav>
    </aside>
    <div class="main-area">
        <header class="topbar">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle" type="button">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-title"><?= e($title ?? '') ?></div>
            <div class="topbar-user dropdown">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <?= e(auth_user()['full_name'] ?? 'User') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text text-muted small"><?= e(ucfirst(\App\Core\Auth::role() ?? '')) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="post" action="<?= url('/logout') ?>">
                            <?= csrf_field() ?>
                            <button class="dropdown-item" type="submit">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>
        <main class="content">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= e($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (!empty($info)): ?>
                <div class="alert alert-info alert-dismissible fade show"><?= e($info) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
