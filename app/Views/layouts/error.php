<section class="empty-state surface text-center py-5">
    <i class="bi bi-database-x display-4 text-primary"></i>
    <h1 class="h3 mt-3">RotaFlow indisponível</h1>
    <p class="text-secondary mb-4"><?= e($message ?? 'Não foi possível carregar a página.') ?></p>
    <a class="btn btn-primary" href="<?= e(url('dashboard')) ?>">Tentar novamente</a>
</section>

