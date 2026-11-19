<?php
$title = 'Transportadores';
$freightCount = null;
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">CADASTROS / TRANSPORTADORES</p>
        <h1>Transportadores</h1>
        <p class="subheading">Gerencie os responsáveis pela movimentação de cargas.</p>
    </div>
    <a class="btn btn-primary" href="#transportador-form"><i class="bi bi-plus-lg me-2"></i>Novo transportador</a>
</section>
<div class="stat-strip">
    <span><i class="bi bi-truck"></i> <strong><?= e(count($carriers)) ?> transportadores</strong> cadastrados</span>
    <span class="strip-separator"></span>
    <span><i class="bi bi-check-circle"></i> Um transportador pode levar vários produtos</span>
</div>

<section class="catalog-layout">
    <div class="surface">
        <div class="section-head">
            <div><h2>Transportadores cadastrados</h2><p>Selecione um registro para editar</p></div>
            <form class="searchbox" method="get">
                <input type="hidden" name="route" value="carriers">
                <i class="bi bi-search"></i>
                <input name="q" value="<?= e($search) ?>" placeholder="Buscar transportador..." aria-label="Buscar transportador">
            </form>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>TRANSPORTADOR</th><th>DOCUMENTO</th><th>CONTATO</th><th>AÇÃO</th></tr></thead>
                <tbody>
                    <?php foreach ($carriers as $carrier): ?>
                        <tr>
                            <td><span class="mini-carrier-icon"><i class="bi bi-truck"></i></span><span class="cell-strong"><?= e($carrier['nome']) ?></span></td>
                            <td><?= e($carrier['documento']) ?></td>
                            <td><?= e($carrier['telefone']) ?><small class="d-block text-secondary"><?= e($carrier['email']) ?></small></td>
                            <td>
                                <a class="edit-link" href="<?= e(url('carriers', ['edit' => $carrier['id']])) ?>#transportador-form">Editar</a>
                                <form class="d-inline" method="post" action="<?= e(url('carriers/delete')) ?>" onsubmit="return confirm('Excluir este transportador? Fretes relacionados impedem a exclusão.');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($carrier['id']) ?>">
                                    <button class="delete-link" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$carriers): ?><tr><td colspan="4" class="text-center py-5 text-secondary">Nenhum transportador encontrado.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="table-foot"><span>Exibindo <?= e(count($carriers)) ?> transportador(es)</span><span>Dados reais do banco</span></div>
    </div>

    <div class="surface form-panel" id="transportador-form">
        <div class="section-head">
            <div><span class="form-kicker"><i class="bi bi-pencil-square"></i> FORMULÁRIO</span><h2><?= $edit ? 'Editar transportador' : 'Cadastrar transportador' ?></h2><p>Dados cadastrais e de contato.</p></div>
        </div>
        <form method="post" action="<?= e(url('carriers/save')) ?>" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
            <label>Nome / razão social <span>*</span><input name="nome" required maxlength="150" value="<?= old('nome', $edit['nome'] ?? '') ?>" placeholder="Ex.: Transportes Exemplo Ltda"></label>
            <label>CPF / CNPJ <span>*</span><input name="documento" required value="<?= old('documento', $edit['documento'] ?? '') ?>" placeholder="00.000.000/0000-00"></label>
            <div class="field-grid">
                <label>Telefone <span>*</span><input name="telefone" required value="<?= old('telefone', $edit['telefone'] ?? '') ?>" placeholder="(00) 00000-0000"></label>
                <label>Placa do veículo<input name="placa_veiculo" maxlength="8" value="<?= old('placa_veiculo', $edit['placa_veiculo'] ?? '') ?>" placeholder="ABC1D23"></label>
            </div>
            <label>E-mail<input name="email" type="email" value="<?= old('email', $edit['email'] ?? '') ?>" placeholder="contato@transportadora.com.br"></label>
            <div class="form-actions"><a class="btn btn-light" href="<?= e(url('carriers')) ?>">Limpar</a><button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Salvar transportador</button></div>
        </form>
    </div>
</section>
