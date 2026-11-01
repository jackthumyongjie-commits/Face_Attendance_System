<?php
$demos = [
    ['name' => 'Admin', 'username' => 'admin', 'password' => 'admin123', 'class' => 'role-admin'],
    ['name' => 'Teacher', 'username' => 'teacher1', 'password' => 'teacher123', 'class' => 'role-teacher'],
    ['name' => 'Student', 'username' => 'student1', 'password' => 'student123', 'class' => 'role-student'],
];
?>
<div class="login-page" style="--hero: url('<?= asset('assets/img/login-hero-hd.jpg') ?>?v=1')">
    <div class="login-bg"></div>
    <div class="login-bg-shade"></div>
    <div class="login-flow" aria-hidden="true">
        <span class="flow-orb orb-1"></span>
        <span class="flow-orb orb-2"></span>
        <span class="flow-orb orb-3"></span>
        <span class="flow-beam"></span>
        <span class="flow-particles"></span>
    </div>

    <aside class="login-visual">
        <div class="visual-content">
            <div class="brand fac-brand">
                <span class="fac-logo">FAC</span>
                <span class="fac-tag">Face Attendance Control</span>
            </div>
            <div class="visual-bottom">
                <h1>Welcome To <span class="fac-welcome">FAC</span></h1>
                <p>Smart facial recognition tools to help schools manage attendance more efficiently.</p>
            </div>
        </div>
    </aside>

    <main class="login-panel">
        <div class="panel-inner">
            <h2>Log in</h2>
            <p class="panel-sub">Access your account easily</p>

            <?php if (!empty($success)): ?>
                <div class="login-alert success"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="login-alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= url('/login') ?>" id="loginForm" class="login-form">
                <?= csrf_field() ?>

                <div class="field">
                    <input
                        type="text"
                        name="login"
                        id="loginInput"
                        placeholder="Username or email"
                        value="<?= e(old('login')) ?>"
                        required
                        autofocus
                    >
                    <i class="bi bi-envelope field-icon"></i>
                </div>

                <div class="field">
                    <input
                        type="password"
                        name="password"
                        id="passwordInput"
                        placeholder="Password"
                        required
                    >
                    <button type="button" class="field-icon btn-eye" id="togglePassword" aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <div class="form-row">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember Me</span>
                    </label>
                    <a class="privacy-link" href="<?= url('/privacy') ?>">Privacy</a>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>

            <div class="or-line"><span>OR</span></div>

            <div class="quick-roles">
                <?php foreach ($demos as $demo): ?>
                    <button
                        type="button"
                        class="role-chip <?= e($demo['class']) ?>"
                        data-username="<?= e($demo['username']) ?>"
                        data-password="<?= e($demo['password']) ?>"
                    ><?= e($demo['name']) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<script>
document.querySelectorAll('.role-chip').forEach((btn) => {
    btn.addEventListener('click', () => {
        document.getElementById('loginInput').value = btn.dataset.username || '';
        document.getElementById('passwordInput').value = btn.dataset.password || '';
        document.querySelectorAll('.role-chip').forEach((el) => el.classList.remove('is-active'));
        btn.classList.add('is-active');
        document.getElementById('loginInput').focus();
    });
});

const toggle = document.getElementById('togglePassword');
const passwordInput = document.getElementById('passwordInput');
toggle.addEventListener('click', () => {
    const show = passwordInput.type === 'password';
    passwordInput.type = show ? 'text' : 'password';
    toggle.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
});
</script>
