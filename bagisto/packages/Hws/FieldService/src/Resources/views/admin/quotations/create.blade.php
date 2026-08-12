@extends('hws::admin.layouts.menu')

@section('page_title')
    Create Quotation
@stop

@section('page-content')
<div style="padding: 0 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    @if($errors->any())
        <div style="padding: 12px 20px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
            {{ $errors->first() }}
        </div>
    @endif

    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Create Quotation</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0;">Generate a new proposal quotation for <strong>{{ $lead->customer_name }}</strong>.</p>
        </div>
        <div>
            <a href="{{ route('hws.admin.sales-leads.index') }}" style="background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px; border: 1px solid #e2e8f0;">
                Back
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('hws.admin.quotations.store') }}">
        @csrf
        <input type="hidden" name="lead_id" value="{{ $lead->id }}">

        <!-- Customer Card -->
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);">
            <h3 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0 0 16px; text-transform: uppercase; letter-spacing: 0.5px;">Client Information</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Client Name</label>
                    <input type="text" name="customer_name" value="{{ $lead->customer_name }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 12px; font-size: 14px;"/>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Client Email</label>
                    <input type="email" name="customer_email" value="{{ $lead->customer_email }}" placeholder="client@example.com" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 12px; font-size: 14px;"/>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Client Phone</label>
                    <input type="text" name="customer_phone" value="{{ $lead->customer_phone }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 12px; font-size: 14px;"/>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Billing / Delivery Address</label>
                <textarea name="customer_address" rows="2" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 12px; font-size: 14px; resize: vertical;">{{ $lead->customer_address }}</textarea>
            </div>
        </div>

        <!-- Items Table Card -->
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Quotation Items</h3>
                <button type="button" onclick="addItemRow()" style="background: #059669; color: #fff; padding: 6px 12px; border-radius: 6px; font-weight: 600; border: 0; cursor: pointer; font-size: 12.5px;">
                    + Add Item
                </button>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 10px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b;">Item Description</th>
                        <th style="padding: 10px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; width: 100px; text-align: center;">Qty</th>
                        <th style="padding: 10px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; width: 150px;">Rate (₹)</th>
                        <th style="padding: 10px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; width: 150px; text-align: right;">Total Amount</th>
                        <th style="padding: 10px 8px; width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsContainer">
                    <tr>
                        <td style="padding: 12px 8px;">
                            <input type="text" name="items[0][name]" required placeholder="STP Installation / RO Filter membrane" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 10px; font-size: 13.5px;"/>
                        </td>
                        <td style="padding: 12px 8px;">
                            <input type="number" name="items[0][qty]" value="1" required min="1" oninput="calculateRowTotal(this)" style="width: 100%; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 10px; font-size: 13.5px;"/>
                        </td>
                        <td style="padding: 12px 8px;">
                            <input type="number" step="0.01" name="items[0][rate]" value="0" required min="0" oninput="calculateRowTotal(this)" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 10px; font-size: 13.5px;"/>
                        </td>
                        <td style="padding: 12px 8px; text-align: right; font-weight: 700; color: #1e293b; font-size: 14px;" class="row-total">
                            ₹0.00
                        </td>
                        <td style="padding: 12px 8px; text-align: center;">
                            <button type="button" onclick="removeItemRow(this)" style="background: transparent; border: 0; color: #ef4444; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Calculation Panel -->
            <div style="max-width: 400px; margin-left: auto; border-top: 2px solid #f1f5f9; padding-top: 16px; font-size: 14px; color: #334155;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-weight: 600; color: #64748b;">Subtotal:</span>
                    <span id="subtotalDisplay" style="font-weight: 700;">₹0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-weight: 600; color: #64748b;">Discount (₹):</span>
                    <input type="number" step="0.01" name="discount" id="discountInput" value="0" min="0" oninput="calculateGrandTotal()" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; font-size: 13px; width: 120px; text-align: right;"/>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-weight: 600; color: #64748b;">GST/Tax (%):</span>
                    <input type="number" step="0.1" name="tax_percent" id="taxInput" value="18" min="0" oninput="calculateGrandTotal()" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; font-size: 13px; width: 120px; text-align: right;"/>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 12px;">
                    <span style="font-size: 16px; font-weight: 800; color: #1e293b;">Grand Total:</span>
                    <span id="grandTotalDisplay" style="font-size: 18px; font-weight: 800; color: #3c50e0;">₹0.00</span>
                </div>
            </div>
        </div>

        <!-- Buttons Panel -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 40px;">
            <a href="{{ route('hws.admin.sales-leads.index') }}" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px;">
                Cancel
            </a>
            <button type="submit" style="background: #3c50e0; color: #fff; border: 0; padding: 12px 30px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(60,80,224,0.15);">
                Generate & Save
            </button>
        </div>
    </form>

</div>

<script>
    let rowIndex = 1;

    function addItemRow() {
        const container = document.getElementById('itemsContainer');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 12px 8px;">
                <input type="text" name="items[${rowIndex}][name]" required placeholder="Description of item" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 10px; font-size: 13.5px;"/>
            </td>
            <td style="padding: 12px 8px;">
                <input type="number" name="items[${rowIndex}][qty]" value="1" required min="1" oninput="calculateRowTotal(this)" style="width: 100%; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 10px; font-size: 13.5px;"/>
            </td>
            <td style="padding: 12px 8px;">
                <input type="number" step="0.01" name="items[${rowIndex}][rate]" value="0" required min="0" oninput="calculateRowTotal(this)" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 10px; font-size: 13.5px;"/>
            </td>
            <td style="padding: 12px 8px; text-align: right; font-weight: 700; color: #1e293b; font-size: 14px;" class="row-total">
                ₹0.00
            </td>
            <td style="padding: 12px 8px; text-align: center;">
                <button type="button" onclick="removeItemRow(this)" style="background: transparent; border: 0; color: #ef4444; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
            </td>
        `;
        container.appendChild(tr);
        rowIndex++;
    }

    function removeItemRow(btn) {
        const tr = btn.closest('tr');
        tr.remove();
        calculateGrandTotal();
    }

    function calculateRowTotal(input) {
        const tr = input.closest('tr');
        const qty = parseFloat(tr.querySelector('input[name*="[qty]"]').value) || 0;
        const rate = parseFloat(tr.querySelector('input[name*="[rate]"]').value) || 0;
        const total = qty * rate;
        
        tr.querySelector('.row-total').innerText = '₹' + total.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let subtotal = 0;
        document.querySelectorAll('#itemsContainer tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('input[name*="[qty]"]').value) || 0;
            const rate = parseFloat(tr.querySelector('input[name*="[rate]"]').value) || 0;
            subtotal += qty * rate;
        });

        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const taxPercent = parseFloat(document.getElementById('taxInput').value) || 0;
        
        const taxable = Math.max(0, subtotal - discount);
        const taxAmount = (taxable * taxPercent) / 100;
        const grandTotal = taxable + taxAmount;

        document.getElementById('subtotalDisplay').innerText = '₹' + subtotal.toFixed(2);
        document.getElementById('grandTotalDisplay').innerText = '₹' + grandTotal.toFixed(2);
    }
</script>
@stop
