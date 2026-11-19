<aside class="sidebar">
    <a class="brand text-decoration-none" href="<?= e(url('dashboard')) ?>">
        <span class="brand-symbol"><i class="bi bi-signpost-split"></i></span>
        <span><span class="brand-name">rota<span>flow</span></span><small>GESTÃO LOGÍSTICA</small></span>
    </a>
    <div class="sidebar-label">ESPAÇO DE TRABALHO</div>
    <nav class="nav-list" aria-label="Navegação principal">
        <a class="nav-link <?= e(active_route('dashboard')) ?>" href="<?= e(url('dashboard')) ?>"><i class="bi bi-grid-1x2"></i> Visão geral</a>
        <a class="nav-link <?= e(active_route('products')) ?>" href="<?= e(url('products')) ?>"><i class="bi bi-box-seam"></i> Produtos</a>
        <a class="nav-link <?= e(active_route('addresses')) ?>" href="<?= e(url('addresses')) ?>"><i class="bi bi-geo-alt"></i> Endereços</a>
        <a class="nav-link <?= e(active_route('carriers')) ?>" href="<?= e(url('carriers')) ?>"><i class="bi bi-truck"></i> Transportadores</a>
        <a class="nav-link <?= e(active_route('freights')) ?>" href="<?= e(url('freights')) ?>"><i class="bi bi-signpost-split"></i> Fretes <span class="nav-counter"><?= e((string) ($freightCount ?? '')) ?></span></a>
        <a class="nav-link <?= e(active_route('reports')) ?>" href="<?= e(url('reports')) ?>"><i class="bi bi-bar-chart-line"></i> Relatórios</a>
    </nav>
    <div class="sidebar-bottom">
        <div class="helper-card"><div class="helper-icon"><i class="bi bi-info-circle"></i></div><strong>Projeto acadêmico</strong><span>Interface baseada no protótipo da UNIPAR.</span></div>
        <div class="side-profile"><div class="avatar">RF</div><div><strong>Operação local</strong><small>RotaFlow <?= e(APP_VERSION) ?></small></div></div>
    </div>
</aside>

