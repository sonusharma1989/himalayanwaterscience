(function () {
    'use strict';

    var newRowIndex = 0;

    function getContainer() { return document.getElementById('hwsOrderItems'); }
    function getProducts() {
        var container = getContainer();
        try { return JSON.parse(container ? (container.dataset.products || '[]') : '[]'); } catch (error) { return []; }
    }
    function getCurrency() {
        var container = getContainer();
        return container ? (container.dataset.currency || 'INR') : 'INR';
    }
    function money(value) { return getCurrency() + ' ' + Number(value || 0).toFixed(2); }

    function updateTotals() {
        var container = getContainer();
        if (!container) return;
        var subtotal = 0;
        container.querySelectorAll('tr').forEach(function (row) {
            var qtyInput = row.querySelector('.hws-item-qty');
            var priceInput = row.querySelector('.hws-item-price');
            var totalCell = row.querySelector('.hws-row-total');
            if (!qtyInput || !priceInput || !totalCell) return;
            var total = (parseFloat(qtyInput.value) || 0) * (parseFloat(priceInput.value) || 0);
            totalCell.textContent = money(total);
            subtotal += total;
        });
        var discountInput = document.getElementById('hwsDiscount');
        var taxInput = document.getElementById('hwsTaxPercent');
        var shippingInput = document.getElementById('hwsShipping');
        var discount = Math.min(parseFloat(discountInput ? discountInput.value : 0) || 0, subtotal);
        var taxPercent = parseFloat(taxInput ? taxInput.value : 0) || 0;
        var shipping = parseFloat(shippingInput ? shippingInput.value : 0) || 0;
        var tax = Math.max(0, subtotal - discount) * taxPercent / 100;
        var values = {hwsSubtotal: subtotal, hwsDiscountDisplay: discount, hwsTaxDisplay: tax, hwsShippingDisplay: shipping, hwsGrandTotal: subtotal - discount + tax + shipping};
        Object.keys(values).forEach(function (id) { var el = document.getElementById(id); if (el) el.textContent = money(values[id]); });
    }

    function chooseProduct(input, product) {
        var row = input.closest('tr');
        input.value = product.name || '';
        row.querySelector('.hws-product-id').value = product.product_id || '';
        row.querySelector('.hws-item-sku').value = product.sku || ('PRODUCT-' + product.product_id);
        row.querySelector('.hws-item-price').value = Number(product.price || 0).toFixed(2);
        row.querySelector('.hws-suggestions-list').style.display = 'none';
        updateTotals();
    }

    function showSuggestions(input) {
        var list = input.closest('.hws-suggestions').querySelector('.hws-suggestions-list');
        var query = input.value.toLowerCase().trim();
        var matches = getProducts().filter(function (product) {
            return !query || String(product.name || '').toLowerCase().includes(query) || String(product.sku || '').toLowerCase().includes(query);
        }).slice(0, 15);
        list.innerHTML = '';
        if (!matches.length) {
            var empty = document.createElement('li');
            empty.className = 'empty';
            empty.textContent = 'No products found';
            list.appendChild(empty);
        }
        matches.forEach(function (product) {
            var option = document.createElement('li');
            option.innerHTML = '<span><strong></strong><br><small></small></span><span class="price"></span>';
            option.querySelector('strong').textContent = product.name || '';
            option.querySelector('small').textContent = product.sku || '';
            option.querySelector('.price').textContent = money(product.price);
            option.addEventListener('mousedown', function (event) { event.preventDefault(); chooseProduct(input, product); });
            list.appendChild(option);
        });
        list.style.display = 'block';
    }

    function addProductRow() {
        var container = getContainer();
        if (!container) return;
        var key = 'new_' + Date.now() + '_' + (++newRowIndex);
        var row = document.createElement('tr');
        row.className = 'hws-new-item';
        row.innerHTML = '<td><div class="hws-suggestions"><input type="hidden" class="hws-product-id" name="items[' + key + '][product_id]"><input autocomplete="off" class="control hws-item-name" name="items[' + key + '][name]" placeholder="Search product name or SKU" onfocus="window.hwsShowProductSuggestions(this)" oninput="window.hwsProductNameChanged(this)" required><ul class="hws-suggestions-list"></ul></div></td>' +
            '<td><input class="control hws-item-sku" name="items[' + key + '][sku]" required></td>' +
            '<td><input class="control hws-item-qty" type="number" min="1" step="0.01" name="items[' + key + '][qty]" value="1" oninput="window.hwsUpdateOrderTotals()" required></td>' +
            '<td><input class="control hws-item-price" type="number" min="0" step="0.01" name="items[' + key + '][price]" value="0" oninput="window.hwsUpdateOrderTotals()" required></td>' +
            '<td class="hws-row-total">' + money(0) + '</td><td>0</td><td><button type="button" class="hws-remove-item" onclick="this.closest(\'tr\').remove();window.hwsUpdateOrderTotals()" aria-label="Remove product">×</button></td>';
        container.appendChild(row);
        updateTotals();
        row.querySelector('.hws-item-name').focus();
    }

    window.hwsAddProduct = addProductRow;
    window.hwsShowProductSuggestions = showSuggestions;
    window.hwsProductNameChanged = function (input) {
        input.closest('tr').querySelector('.hws-product-id').value = '';
        showSuggestions(input);
    };
    window.hwsUpdateOrderTotals = updateTotals;

    function boot() {
        var container = document.getElementById('hwsOrderItems');
        if (!container || container.__hwsOrderEditorBound) return;
        container.__hwsOrderEditorBound = true;
        container.dataset.ready = '1';
        container.addEventListener('focusin', function (event) {
            if (event.target.matches('.hws-item-name:not([readonly])')) showSuggestions(event.target);
        });
        container.addEventListener('input', function (event) {
            if (event.target.matches('.hws-item-name:not([readonly])')) {
                event.target.closest('tr').querySelector('.hws-product-id').value = '';
                showSuggestions(event.target);
            }
            if (event.target.matches('.hws-item-qty, .hws-item-price')) updateTotals();
        });
        updateTotals();
    }

    window.addEventListener('focusin', function (event) {
        if (event.target.matches('#hwsOrderItems .hws-item-name:not([readonly])')) showSuggestions(event.target);
    });
    window.addEventListener('input', function (event) {
        if (event.target.matches('#hwsOrderItems .hws-item-name:not([readonly])')) {
            event.target.closest('tr').querySelector('.hws-product-id').value = '';
            showSuggestions(event.target);
        }
        if (event.target.matches('#hwsOrderItems .hws-item-qty, #hwsOrderItems .hws-item-price, #hwsDiscount, #hwsTaxPercent, #hwsShipping')) updateTotals();
    });
    window.addEventListener('change', function (event) {
        if (event.target.matches('#gstInvoiceToggle')) {
            var gstFields = document.getElementById('gstBillingFields');
            if (gstFields) gstFields.style.display = event.target.checked ? 'grid' : 'none';
        }
    });

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
    window.addEventListener('load', boot);
    window.setInterval(boot, 300);
}());
