<?php
$title = $freight ? 'Editar frete' : 'Novo frete';
$freightCount = null;
$pageScript = 'freight.js';
$oldProducts = $_SESSION['_old']['produtos'] ?? null;
$oldQuantities = $_SESSION['_old']['quantidades'] ?? null;
$selectedItems = $freight['items'] ?? (is_array($oldProducts)
    ? array_map(static fn (mixed $productId, int $index): array => [
        'produto_id' => $productId,
        'quantidade' => $oldQuantities[$index] ?? 1,
    ], $oldProducts, array_keys($oldProducts))
    : [['produto_id' => $products[0]['id'] ?? '', 'quantidade' => 1]]);
?>

<section class="page-heading">
    <div><p class="eyebrow">OPERAÇÃO / FRETES</p><h1><?= e($title) ?></h1><p class="subheading">Relacione origem, destino, transportador e os produtos da carga.</p></div>
    <a class="btn btn-outline-primary" href="<?= e(url('freights')) ?>"><i class="bi bi-list-ul me-2"></i>Consultar fretes</a>
</section>

<div class="freight-workflow">
    <div class="workflow-item active"><span>01</span> Informações da rota</div><div class="workflow-line"></div>
    <div class="workflow-item active"><span>02</span> Produtos da carga</div><div class="workflow-line"></div>
    <div class="workflow-item"><span>03</span> Confirmação</div>
</div>

<form method="post" action="<?= e(url('freights/save')) ?>" id="freight-form" class="freight-layout needs-validation" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e($freight['id'] ?? '') ?>">
    <div class="freight-form-stack">
        <div class="surface freight-card">
            <div class="inner-head">
                <span class="round-icon"><i class="bi bi-geo-alt"></i></span>
                <div><h2>Dados do transporte</h2><p>Defina a rota e o responsável pela entrega.</p></div>
            </div>
            <div class="form-grid-2">
                <label>Endereço de origem <span>*</span>
                    <select name="origem_id" required><option value="">Selecione uma opção</option>
                        <?php foreach ($addresses as $address): ?><option value="<?= e($address['id']) ?>" <?= (string) ($freight['origem_id'] ?? old('origem_id')) === (string) $address['id'] ? 'selected' : '' ?>><?= e($address['identificacao'] . ' — ' . $address['cidade'] . '/' . $address['estado']) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label>Endereço de destino <span>*</span>
                    <select name="destino_id" required><option value="">Selecione uma opção</option>
                        <?php foreach ($addresses as $address): ?><option value="<?= e($address['id']) ?>" <?= (string) ($freight['destino_id'] ?? old('destino_id')) === (string) $address['id'] ? 'selected' : '' ?>><?= e($address['identificacao'] . ' — ' . $address['cidade'] . '/' . $address['estado']) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label>Transportador <span>*</span>
                    <select name="transportador_id" required><option value="">Selecione uma opção</option>
                        <?php foreach ($carriers as $carrier): ?><option value="<?= e($carrier['id']) ?>" <?= (string) ($freight['transportador_id'] ?? old('transportador_id')) === (string) $carrier['id'] ? 'selected' : '' ?>><?= e($carrier['nome']) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label>Data do frete <span>*</span><input type="date" name="data_frete" required value="<?= e($freight['data_frete'] ?? old('data_frete', date('Y-m-d'))) ?>"></label>
                <label>Situação <span>*</span>
                    <select name="status" required><?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= ($freight['status'] ?? old('status', 'Em aberto')) === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select>
                </label>
            </div>
        </div>

        <div class="surface freight-card product-freight-card">
            <div class="inner-head product-head">
                <span class="round-icon purple"><i class="bi bi-box-seam"></i></span>
                <div><h2>Produtos da carga</h2><p>Adicione um ou mais produtos ao mesmo frete.</p></div>
                <span class="required-label">MÍNIMO 1 PRODUTO</span>
            </div>
            <div class="line-header"><span>PRODUTO</span><span>QUANTIDADE</span><span>PESO TOTAL</span><span>VALOR</span><span></span></div>
            <div id="freight-items" data-products='<?= e(json_encode($products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>'>
                <?php foreach ($selectedItems as $item): ?>
                    <div class="freight-line">
                        <select name="produtos[]" class="product-select" required>
                            <?php foreach ($products as $product): ?><option value="<?= e($product['id']) ?>" data-weight="<?= e($product['peso']) ?>" data-value="<?= e($product['valor']) ?>" <?= (string) $item['produto_id'] === (string) $product['id'] ? 'selected' : '' ?>><?= e($product['nome']) ?></option><?php endforeach; ?>
                        </select>
                        <input name="quantidades[]" class="quantity-input" type="number" min="1" step="1" required value="<?= e($item['quantidade']) ?>">
                        <span class="line-weight">0,00 kg</span><span class="line-subtotal">R$ 0,00</span>
                        <button type="button" class="icon-button remove-line" aria-label="Remover linha"><i class="bi bi-trash"></i></button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-line" id="add-freight-item"><i class="bi bi-plus-circle me-2"></i>Adicionar outro produto</button>
        </div>
    </div>

    <aside class="freight-aside">
        <div class="surface summary-card">
            <div class="summary-title"><i class="bi bi-clipboard2-check"></i><h2>Resumo do frete</h2></div>
            <div class="summary-row"><span>Produtos incluídos</span><strong id="freight-item-count">0 itens</strong></div>
            <div class="summary-row"><span>Quantidade total</span><strong id="freight-total-qty">0 unidades</strong></div>
            <div class="summary-row"><span>Peso total</span><strong id="freight-total-weight">0,00 kg</strong></div>
            <div class="summary-separator"></div>
            <div class="summary-total"><span>Valor dos produtos</span><strong id="freight-total-value">R$ 0,00</strong></div>
            <p class="summary-footnote">Valor dos itens; não representa o preço do frete.</p>
            <button class="btn btn-primary stretch" type="submit"><i class="bi bi-check-circle me-2"></i><?= e($freight ? 'Salvar alterações' : 'Gerar frete') ?></button>
            <p class="below-btn"><i class="bi bi-shield-check"></i> Confira os dados antes de confirmar.</p>
        </div>
        <div class="hint-panel"><i class="bi bi-lightbulb"></i><div><strong>Relacionamento 1 para muitos</strong><p>Um único frete pode incluir vários produtos, todos vinculados ao mesmo transportador.</p></div></div>
    </aside>
</form>
