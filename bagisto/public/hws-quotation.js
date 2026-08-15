function initializeHwsQuotation() {
    'use strict';

    var itemsContainer = document.getElementById('itemsContainer');

    if (!itemsContainer) {
        return;
    }

    var products = [];
    var rowIndex = itemsContainer.querySelectorAll('tr').length;

    try {
        products = JSON.parse(itemsContainer.dataset.products || '[]');
    } catch (error) {
        console.error('Unable to load quotation products.', error);
    }

    function renderSuggestions(input, matches) {
        var list = input.closest('.suggestions-wrapper').querySelector('.suggestions-list');
        list.innerHTML = '';

        if (!matches.length) {
            var emptyItem = document.createElement('li');
            emptyItem.className = 'no-matches';
            emptyItem.textContent = 'No products found';
            list.appendChild(emptyItem);
        } else {
            matches.forEach(function (product) {
                var item = document.createElement('li');
                var name = document.createElement('span');
                var price = document.createElement('span');

                name.className = 'product-suggestion__name';
                name.textContent = product.name;
                price.className = 'product-suggestion__price';
                price.textContent = '₹' + Number(product.price).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                item.appendChild(name);
                item.appendChild(price);
                item.dataset.name = product.name;
                item.dataset.price = product.price;
                list.appendChild(item);
            });
        }

        list.style.display = 'block';
    }

    function filterProducts(input) {
        var query = input.value.toLowerCase().trim();
        var matches = products.filter(function (product) {
            return !query || product.name.toLowerCase().includes(query);
        });

        renderSuggestions(input, matches);
    }

    function calculateGrandTotal() {
        var subtotal = 0;
        var currentContainer = document.getElementById('itemsContainer');

        currentContainer.querySelectorAll('tr').forEach(function (row) {
            var qty = Number(row.querySelector('input[name*="[qty]"]').value) || 0;
            var rate = Number(row.querySelector('input[name*="[rate]"]').value) || 0;
            subtotal += qty * rate;
        });

        var discount = Number(document.getElementById('discountInput').value) || 0;
        var taxPercent = Number(document.getElementById('taxInput').value) || 0;
        var taxable = Math.max(0, subtotal - discount);
        var grandTotal = taxable + (taxable * taxPercent / 100);

        document.getElementById('subtotalDisplay').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('grandTotalDisplay').textContent = '₹' + grandTotal.toFixed(2);
    }

    function calculateRowTotal(input) {
        var row = input.closest('tr');
        var qty = Number(row.querySelector('input[name*="[qty]"]').value) || 0;
        var rate = Number(row.querySelector('input[name*="[rate]"]').value) || 0;
        row.querySelector('.row-total').textContent = '₹' + (qty * rate).toFixed(2);
        calculateGrandTotal();
    }

    function selectSuggestion(item) {
        var row = item.closest('tr');
        var input = row.querySelector('input[name*="[name]"]');
        var rateInput = row.querySelector('input[name*="[rate]"]');
        input.value = item.dataset.name;
        rateInput.value = Number(item.dataset.price).toFixed(2);
        item.closest('.suggestions-list').style.display = 'none';
        calculateRowTotal(rateInput);
    }

    document.addEventListener('focusin', function (event) {
        if (event.target.matches('input[name*="[name]"]')) {
            filterProducts(event.target);
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target.matches('input[name*="[name]"]')) {
            filterProducts(event.target);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (!event.target.matches('input[name*="[name]"]')) {
            return;
        }

        var list = event.target.closest('.suggestions-wrapper').querySelector('.suggestions-list');
        var suggestions = Array.from(list.querySelectorAll('li:not(.no-matches)'));
        var activeIndex = suggestions.findIndex(function (item) {
            return item.classList.contains('is-active');
        });

        if ((event.key === 'ArrowDown' || event.key === 'ArrowUp') && suggestions.length) {
            event.preventDefault();
            suggestions.forEach(function (item) { item.classList.remove('is-active'); });
            activeIndex = event.key === 'ArrowDown'
                ? (activeIndex + 1) % suggestions.length
                : (activeIndex - 1 + suggestions.length) % suggestions.length;
            suggestions[activeIndex].classList.add('is-active');
            suggestions[activeIndex].scrollIntoView({ block: 'nearest' });
        } else if (event.key === 'Enter' && activeIndex >= 0) {
            event.preventDefault();
            selectSuggestion(suggestions[activeIndex]);
        } else if (event.key === 'Escape') {
            list.style.display = 'none';
        }
    });

    document.addEventListener('focusout', function (event) {
        if (event.target.matches('input[name*="[name]"]')) {
            var list = event.target.closest('.suggestions-wrapper').querySelector('.suggestions-list');
            window.setTimeout(function () { list.style.display = 'none'; }, 150);
        }
    });

    document.addEventListener('mousedown', function (event) {
        var item = event.target.closest('.suggestions-list li:not(.no-matches)');
        if (item) {
            event.preventDefault();
            selectSuggestion(item);
        }
    });

    window.calculateRowTotal = calculateRowTotal;
    window.calculateGrandTotal = calculateGrandTotal;
    window.removeItemRow = function (button) {
        button.closest('tr').remove();
        calculateGrandTotal();
    };
    window.addItemRow = function () {
        var currentContainer = document.getElementById('itemsContainer');
        var row = document.createElement('tr');
        row.innerHTML = '<td style="padding:12px 8px"><div class="suggestions-wrapper"><input type="text" name="items[' + rowIndex + '][name]" autocomplete="off" required placeholder="Description of item" style="width:100%;border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;font-size:13.5px;height:38px;box-sizing:border-box"><ul class="suggestions-list"></ul></div></td>'
            + '<td style="padding:12px 8px"><input type="number" name="items[' + rowIndex + '][qty]" value="1" required min="1" oninput="calculateRowTotal(this)" style="width:100%;text-align:center;border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;font-size:13.5px;height:38px;box-sizing:border-box"></td>'
            + '<td style="padding:12px 8px"><input type="number" step="0.01" name="items[' + rowIndex + '][rate]" value="0" required min="0" oninput="calculateRowTotal(this)" style="width:100%;border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;font-size:13.5px;height:38px;box-sizing:border-box"></td>'
            + '<td style="padding:12px 8px;text-align:right;font-weight:700;color:#1e293b;font-size:14px" class="row-total">₹0.00</td>'
            + '<td style="padding:12px 8px;text-align:center"><button type="button" onclick="removeItemRow(this)" style="background:transparent;border:0;color:#ef4444;font-size:18px;cursor:pointer;font-weight:700">×</button></td>';
        currentContainer.appendChild(row);
        rowIndex += 1;
    };
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeHwsQuotation);
} else {
    initializeHwsQuotation();
}
