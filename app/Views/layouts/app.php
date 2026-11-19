<?php
$title = $title ?? 'RotaFlow';
$success = flash('success');
$error = flash('error');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | RotaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
<div class="app-shell">
    <?php require BASE_PATH . '/app/Views/partials/sidebar.php'; ?>
    <div class="main-area">
        <header class="topbar">
            <div class="crumb"><span>RotaFlow</span><i class="bi bi-chevron-right"></i><strong><?= e($title) ?></strong></div>
            <div class="top-actions"><span class="environment"><span class="pulse"></span> Ambiente local</span><span class="top-divider"></span><div class="user-avatar">RF</div></div>
        </header>
        <main class="main-content">
            <?php if ($success): ?><div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"><i class="bi bi-check-circle me-2"></i><?= e($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert"><i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?= $content ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
<?php if (!empty($pageScript)): ?><script src="<?= e(asset('js/' . $pageScript)) ?>"></script><?php endif; ?>
</body>
</html>

