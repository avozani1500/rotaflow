<?php
$title = 'Produtos';
$freightCount = null;
?>

<section class="page-heading">
    <div><p class="eyebrow">CADASTROS / PRODUTOS</p><h1>Produtos</h1><p class="subheading">Organize os itens disponíveis para transporte.</p></div>
    <a class="btn btn-primary" href="#produto-form"><i class="bi bi-plus-lg me-2"></i>Novo produto</a>
</section>
<div class="stat-strip">
    <span><i class="bi bi-box-seam"></i> <strong><?= e(count($products)) ?> produtos</strong> cadastrados</span>
    <span class="strip-separator"></span>
    <span><i class="bi bi-check-circle"></i> Prontos para vincular a fretes</span>
</div>

<section class="catalog-layout">
    <div class="surface">
        <div class="section-head">
            <div><h2>Lista de produtos</h2><p>Consulte e edite seus registros</p></div>
            <form class="searchbox" method="get"><input type="hidden" name="route" value="products"><i class="bi bi-search"></i><input name="q" value="<?= e($search) ?>" placeholder="Buscar produto..." aria-label="Buscar produto"></form>
        </div>
        <div class="table-wrap">
            <table class="data-table product-table">
                <thead><tr><th>PRODUTO</th><th>PESO</th><th>VALOR UNIT.</th><th>AÇÃO</th></tr></thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><span class="mini-product-icon"><i class="bi bi-box-seam"></i></span><span class="cell-strong"><?= e($product['nome']) ?></span><small class="d-block text-secondary ms-5"><?= e($product['descricao']) ?></small></td>
                            <td><?= e(number_format((float) $product['peso'], 2, ',', '.')) ?> kg</td>
                            <td><span class="cell-strong"><?= e(money($product['valor'])) ?></span></td>
                            <td>
                                <a class="edit-link" href="<?= e(url('products', ['edit' => $product['id']])) ?>#produto-form">Editar</a>
                                <form class="d-inline" method="post" action="<?= e(url('products/delete')) ?>" data-confirm="Excluir este produto? Se ele já estiver em um frete, a exclusão será bloqueada.">
                                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($product['id']) ?>">
                                    <button class="delete-link" type="submit" aria-label="Excluir produto"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$products): ?><tr><td colspan="4" class="text-center py-5 text-secondary">Nenhum produto encontrado.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="table-foot"><span>Exibindo <?= e(count($products)) ?> produto(s)</span><span>Dados reais do banco</span></div>
    </div>

    <div class="surface form-panel" id="produto-form">
        <div class="section-head"><div><span class="form-kicker"><i class="bi bi-pencil-square"></i> FORMULÁRIO</span><h2><?= $edit ? 'Editar produto' : 'Cadastrar produto' ?></h2><p>Preencha as informações do item.</p></div></div>
        <form method="post" action="<?= e(url('products/save')) ?>" class="needs-validation" novalidate>
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
            <label>Nome do produto <span>*</span><input name="nome" required maxlength="150" value="<?= old('nome', $edit['nome'] ?? '') ?>" placeholder="Ex.: Monitor LED 24 polegadas"><div class="invalid-feedback">Informe o nome do produto.</div></label>
            <label>Descrição<textarea name="descricao" rows="3" placeholder="Informações adicionais do produto"><?= old('descricao', $edit['descricao'] ?? '') ?></textarea></label>
            <div class="field-grid">
                <label>Peso (kg) <span>*</span><input name="peso" required min="0.01" step="0.01" type="number" value="<?= old('peso', $edit['peso'] ?? '') ?>" placeholder="0,00"><div class="invalid-feedback">Informe um peso maior que zero.</div></label>
                <label>Valor (R$) <span>*</span><input name="valor" required min="0" step="0.01" type="number" value="<?= old('valor', $edit['valor'] ?? '') ?>" placeholder="0,00"><div class="invalid-feedback">Informe o valor.</div></label>
            </div>
            <div class="form-actions"><a class="btn btn-light" href="<?= e(url('products')) ?>">Limpar</a><button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Salvar produto</button></div>
        </form>
    </div>
</section>
