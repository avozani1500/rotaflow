(function () {
    'use strict';
    const container = document.querySelector('#freight-items');
    if (!container) return;
    const products = window.ROTAFLOW_PRODUCT_DATA || [];
    const money = value => Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    const productOptions = selected => products.map(product => `<option value="${product.id}" data-weight="${product.peso}" data-value="${product.valor}" ${Number(selected) === Number(product.id) ? 'selected' : ''}>${escapeHtml(product.nome)}</option>`).join('');
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char]));

    function updateRow(row) {
        const select = row.querySelector('.product-select');
        const quantity = Math.max(1, Number.parseInt(row.querySelector('.quantity-input').value || '1', 10));
        row.querySelector('.quantity-input').value = quantity;
        const option = select.options[select.selectedIndex];
        const weight = Number(option?.dataset.weight || 0) * quantity;
        const value = Number(option?.dataset.value || 0) * quantity;
        row.querySelector('.line-weight').textContent = weight.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + ' kg';
        row.querySelector('.line-subtotal').textContent = money(value);
    }

    function updateSummary() {
        const rows = [...container.querySelectorAll('.freight-line')];
        let quantity = 0, weight = 0, value = 0;
        rows.forEach(row => {
            const select = row.querySelector('.product-select');
            const option = select.options[select.selectedIndex];
            const qty = Number(row.querySelector('.quantity-input').value || 0);
            quantity += qty;
            weight += Number(option?.dataset.weight || 0) * qty;
            value += Number(option?.dataset.value || 0) * qty;
            updateRow(row);
        });
        document.querySelector('#freight-item-count').textContent = rows.length + (rows.length === 1 ? ' item' : ' itens');
        document.querySelector('#freight-total-qty').textContent = quantity + (quantity === 1 ? ' unidade' : ' unidades');
        document.querySelector('#freight-total-weight').textContent = weight.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + ' kg';
        document.querySelector('#freight-total-value').textContent = money(value);
    }

    function bindRow(row) {
        row.querySelector('.product-select').addEventListener('change', updateSummary);
        row.querySelector('.quantity-input').addEventListener('input', updateSummary);
        row.querySelector('.remove-line').addEventListener('click', function () {
            if (container.querySelectorAll('.freight-line').length === 1) return;
            row.remove();
            updateSummary();
        });
    }

    document.querySelectorAll('.freight-line').forEach(bindRow);
    document.querySelector('#add-freight-item')?.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'freight-line';
        row.innerHTML = `<select name="produtos[]" class="product-select" required>${productOptions(products[0]?.id || '')}</select><input name="quantidades[]" class="quantity-input" type="number" min="1" step="1" required value="1"><span class="line-weight">0,00 kg</span><span class="line-subtotal">R$ 0,00</span><button type="button" class="icon-button remove-line" aria-label="Remover linha"><i class="bi bi-trash"></i></button>`;
        container.appendChild(row);
        bindRow(row);
        updateSummary();
    });
    document.querySelector('#freight-form')?.addEventListener('submit', function (event) {
        const rows = container.querySelectorAll('.freight-line');
        if (!rows.length) event.preventDefault();
        const origin = document.querySelector('[name="origem_id"]');
        const destination = document.querySelector('[name="destino_id"]');
        if (origin?.value && origin.value === destination?.value) {
            event.preventDefault();
            destination.setCustomValidity('A origem e o destino precisam ser diferentes.');
            destination.reportValidity();
        } else if (destination) {
            destination.setCustomValidity('');
        }
    });
    updateSummary();
})();

