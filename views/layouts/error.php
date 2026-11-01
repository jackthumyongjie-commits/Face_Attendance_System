<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= asset('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body class="auth-body">
<div class="auth-wrap">
    <div class="auth-card text-center">
        <?= $content ?>
        <a href="<?= url('/') ?>" class="btn btn-primary mt-3">Go Home</a>
    </div>
</div>
</body>
</html>
