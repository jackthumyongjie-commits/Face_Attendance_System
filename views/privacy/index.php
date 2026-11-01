<?php
$items = [
    ['icon' => 'bi-shield-check', 'title' => 'Attendance Verification', 'text' => 'Facial data is used for attendance verification.'],
    ['icon' => 'bi-fingerprint', 'title' => 'Face Descriptors', 'text' => 'Face descriptors (mathematical representations of faces) are stored for matching.'],
    ['icon' => 'bi-image', 'title' => 'Stored Images', 'text' => 'Registration and attendance images may be stored for verification and audit purposes.'],
    ['icon' => 'bi-people', 'title' => 'Role-Based Access', 'text' => 'Access is controlled by user roles (Administrator, Teacher, Student).'],
    ['icon' => 'bi-funnel', 'title' => 'Minimal Collection', 'text' => 'The system only collects information necessary for attendance management.'],
    ['icon' => 'bi-lock', 'title' => 'Data Protection', 'text' => 'Facial data is not exposed to unauthorized users.'],
];
?>

<?php if (!empty($standalone)): ?>
<div class="login-page privacy-page" style="--hero: url('<?= asset('assets/img/login-hero-hd.jpg') ?>?v=1')">
    <div class="login-bg"></div>
    <div class="login-bg-shade"></div>
    <div class="login-flow" aria-hidden="true">
        <span class="flow-orb orb-1"></span>
        <span class="flow-orb orb-2"></span>
        <span class="flow-orb orb-3"></span>
        <span class="flow-beam"></span>
        <span class="flow-particles"></span>
    </div>
    <div class="privacy-glow privacy-glow-a"></div>
    <div class="privacy-glow privacy-glow-b"></div>

    <div class="privacy-shell">
        <div class="privacy-topbar">
            <div class="fac-mark">
                <span class="fac-logo">FAC</span>
                <span class="fac-sub">Face Attendance Control</span>
            </div>
            <a class="privacy-back" href="<?= url('/login') ?>"><i class="bi bi-arrow-left"></i> Back to Login</a>
        </div>

        <div class="privacy-card privacy-card-pro">
            <div class="privacy-head privacy-head-pro">
                <div class="privacy-badge"><i class="bi bi-shield-lock"></i></div>
                <div>
                    <div class="privacy-kicker">FAC Privacy Policy</div>
                    <h1>Your Data, Protected</h1>
                    <p>FAC collects and processes personal information only as needed for attendance management.</p>
                </div>
            </div>

            <div class="privacy-stats">
                <div><strong>Encrypted</strong><span>Secure storage</span></div>
                <div><strong>Role-Based</strong><span>Access control</span></div>
                <div><strong>Minimal</strong><span>Data collection</span></div>
            </div>

            <div class="privacy-grid">
                <?php foreach ($items as $item): ?>
                    <div class="privacy-item">
                        <div class="privacy-icon"><i class="bi <?= e($item['icon']) ?>"></i></div>
                        <div>
                            <h3><?= e($item['title']) ?></h3>
                            <p><?= e($item['text']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="privacy-note">
                <i class="bi bi-info-circle"></i>
                If you have questions about data handling, contact your institution administrator.
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="privacy-card privacy-card-inapp privacy-card-pro">
    <div class="privacy-head privacy-head-pro">
        <div class="privacy-badge"><i class="bi bi-shield-lock"></i></div>
        <div>
            <div class="privacy-kicker">FAC Privacy Policy</div>
            <h1>Your Data, Protected</h1>
            <p>FAC collects and processes personal information only as needed for attendance management.</p>
        </div>
    </div>

    <div class="privacy-stats privacy-stats-light">
        <div><strong>Encrypted</strong><span>Secure storage</span></div>
        <div><strong>Role-Based</strong><span>Access control</span></div>
        <div><strong>Minimal</strong><span>Data collection</span></div>
    </div>

    <div class="privacy-grid">
        <?php foreach ($items as $item): ?>
            <div class="privacy-item">
                <div class="privacy-icon"><i class="bi <?= e($item['icon']) ?>"></i></div>
                <div>
                    <h3><?= e($item['title']) ?></h3>
                    <p><?= e($item['text']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="privacy-note">
        <i class="bi bi-info-circle"></i>
        If you have questions about data handling, contact your institution administrator.
    </div>
</div>
<link href="<?= asset('assets/css/privacy.css') ?>?v=fac-dark1" rel="stylesheet">
<?php endif; ?>
