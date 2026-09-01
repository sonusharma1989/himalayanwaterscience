@extends('admin::layouts.master')

@section('page_title') Edit Order #{{ $order->increment_id }} @stop

@push('css')
<style>
.hws-order-items{overflow:visible!important}.hws-suggestions{position:relative}.hws-suggestions-list{display:none;position:absolute;left:0;top:calc(100% + 6px);width:max(100%,390px);max-height:250px;overflow-y:auto;z-index:99999;margin:0;padding:6px;list-style:none;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 18px 45px -12px rgba(15,23,42,.25)}
.hws-suggestions-list li{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:10px 12px;border-radius:8px;cursor:pointer;color:#334155}.hws-suggestions-list li:hover,.hws-suggestions-list li.is-active{background:#f8fafc;color:#4338ca}.hws-suggestions-list .price{padding:4px 8px;border-radius:999px;background:#eef2ff;color:#4338ca;font-size:12px;font-weight:700;white-space:nowrap}.hws-suggestions-list .empty{justify-content:center;color:#94a3b8;cursor:default}
.hws-item-name:focus{border-color:#6366f1!important;box-shadow:0 0 0 3px rgba(99,102,241,.12);outline:none}.hws-row-total{font-weight:800;white-space:nowrap;color:#1e293b}
.hws-order-items{padding:24px!important;border-radius:16px!important;box-shadow:0 4px 6px -1px rgba(0,0,0,.02)}
.hws-order-items .table{width:100%;overflow:visible!important}.hws-order-items table{width:100%;border-collapse:collapse;table-layout:fixed;margin-top:4px}
.hws-order-items thead tr{border-bottom:2px solid #e2e8f0}.hws-order-items th{padding:10px 8px!important;background:transparent!important;border:0!important;color:#64748b!important;font-size:11px!important;font-weight:700!important;letter-spacing:.35px;text-transform:uppercase}
.hws-order-items th:nth-child(1){width:28%}.hws-order-items th:nth-child(2){width:16%}.hws-order-items th:nth-child(3){width:11%;text-align:center}.hws-order-items th:nth-child(4){width:15%}.hws-order-items th:nth-child(5){width:14%;text-align:right}.hws-order-items th:nth-child(6){width:9%;text-align:center}.hws-order-items th:nth-child(7){width:42px}
.hws-order-items tbody tr{border-bottom:1px solid #edf2f7}.hws-order-items tbody tr:last-child{border-bottom:0}.hws-order-items td{padding:12px 8px!important;border:0!important;vertical-align:middle}
.hws-order-items input.control{display:block;width:100%!important;min-width:0!important;height:38px!important;box-sizing:border-box!important;padding:8px 10px!important;border:1px solid #cbd5e1!important;border-radius:6px!important;background:#fff!important;color:#1e293b!important;font-size:13.5px!important;line-height:20px!important;box-shadow:none!important}
.hws-order-items input.control:hover{border-color:#94a3b8!important}.hws-order-items input.control:focus{border-color:#6366f1!important;box-shadow:0 0 0 3px rgba(99,102,241,.12)!important;outline:0}.hws-order-items input.control[readonly]{background:#f8fafc!important;color:#64748b!important;cursor:not-allowed}
.hws-order-items .hws-item-qty{text-align:center}.hws-order-items .hws-row-total{text-align:right;font-size:14px}.hws-order-items td:nth-child(6){text-align:center;color:#64748b}.hws-remove-item{border:0;background:transparent;color:#ef4444;font-size:20px;font-weight:700;cursor:pointer;padding:4px 8px}
.hws-order-items h3{margin:0;color:#334155;font-size:15px;font-weight:700;letter-spacing:.5px;text-transform:uppercase}
@media(max-width:1100px){.hws-order-items .table{overflow-x:auto!important}.hws-order-items table{min-width:1050px}}
</style>
@endpush

@section('content-wrapper')
<div class="content full-page">
    <div class="page-header"><div class="page-title"><h1>Edit Order #{{ $order->increment_id }}</h1><p style="margin-top:6px;color:#64748b">{{ ucfirst($order->sales_type) }} order</p></div></div>
    <div class="page-content">
        @if($errors->any())<div style="padding:12px;margin-bottom:16px;background:#fee2e2;color:#991b1b;border-radius:8px">{{ $errors->first() }}</div>@endif
        @if($order->invoices->count() || $order->shipments->count())<div style="padding:12px;margin-bottom:16px;background:#fff7ed;color:#9a3412;border-radius:8px">This order has invoices or shipments. Quantity cannot be reduced below already fulfilled quantity.</div>@endif

        <form method="POST" action="{{ route('hws.admin.orders.update', $order->id) }}">@csrf
            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px">
                <section style="grid-column:1/-1;padding:18px;background:#fff;border:1px solid #e2e8f0;border-radius:12px"><h3>Customer &amp; GST Billing</h3>
                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px"><div class="control-group"><label>First Name</label><input class="control" name="customer_first_name" value="{{ old('customer_first_name',$order->customer_first_name) }}" required></div>
                    <div class="control-group"><label>Last Name</label><input class="control" name="customer_last_name" value="{{ old('customer_last_name',$order->customer_last_name) }}"></div>
                    <div class="control-group"><label>Email</label><input class="control" type="email" name="customer_email" value="{{ old('customer_email',$order->customer_email) }}" required></div></div>
                    <input type="hidden" name="is_gst_invoice" value="0"><label style="display:flex;align-items:center;gap:9px;margin:8px 0 14px;font-weight:700"><input id="gstInvoiceToggle" type="checkbox" name="is_gst_invoice" value="1" {{ old('is_gst_invoice',$order->is_gst_invoice)?'checked':'' }}> Generate GST Invoice</label>
                    <div id="gstBillingFields" style="display:{{ old('is_gst_invoice',$order->is_gst_invoice)?'grid':'none' }};grid-template-columns:1fr 1fr;gap:14px;padding:14px;background:#f8fafc;border-radius:10px">
                        <div class="control-group"><label>Legal Company Name</label><input class="control" name="billing_company_name" value="{{ old('billing_company_name',$order->billing_company_name) }}" placeholder="Company name on GST certificate"></div>
                        <div class="control-group"><label>GSTIN</label><input class="control" name="gstin" value="{{ old('gstin',$order->gstin) }}" maxlength="15" style="text-transform:uppercase" placeholder="22AAAAA0000A1Z5"></div>
                    </div>
                </section>
                @foreach(['billing'=>'Billing Address','shipping'=>'Shipping Address'] as $key=>$label)
                    @php($address=$key==='billing'?$order->billing_address:$order->shipping_address)
                    <section style="padding:18px;background:#fff;border:1px solid #e2e8f0;border-radius:12px"><h3>{{ $label }}</h3>
                        @foreach(['address1'=>'Address','city'=>'City','state'=>'State','postcode'=>'Postcode','phone'=>'Phone'] as $field=>$fieldLabel)
                            <div class="control-group"><label>{{ $fieldLabel }}</label><input class="control" name="{{ $key }}[{{ $field }}]" value="{{ old($key.'.'.$field,$address?->{$field}) }}" required></div>
                        @endforeach
                    </section>
                @endforeach
            </div>

            <section class="hws-order-items" style="margin-top:18px;padding:18px;background:#fff;border:1px solid #e2e8f0;border-radius:12px"><div style="display:flex;justify-content:space-between;align-items:center"><div><h3>Order Items</h3><p style="margin:4px 0 14px;color:#64748b;font-size:13px">Type a product name or SKU to search and auto-fill SKU and rate.</p></div><button id="hwsAddProduct" type="button" v-on:click="(function(btn){var box=btn.closest('.hws-order-items'),source=box.querySelector('#hwsNewProductRow tbody'),key='new_'+Date.now(),holder=source.cloneNode(true);holder.innerHTML=holder.innerHTML.split('__KEY__').join(key);box.querySelector('#hwsOrderItems').appendChild(holder.firstElementChild)})($event.currentTarget)" class="btn btn-md btn-primary">+ Add Product</button></div>
                <div class="table" style="overflow:visible"><table><thead><tr><th>Item Description (type to search)</th><th>SKU</th><th>Quantity</th><th>Rate</th><th>Total</th><th>Shipped</th><th></th></tr></thead><tbody id="hwsOrderItems" data-products='@json($products)' data-currency="{{ $order->order_currency_code }}">
                @foreach($order->items as $item)@php($itemProcessed=$item->qty_shipped>0||$item->qty_invoiced>0||$item->qty_refunded>0||$item->qty_canceled>0)<tr style="{{ $itemProcessed?'background:#f8fafc':'' }}">
                    <td><div class="hws-suggestions"><input type="hidden" class="hws-product-id" name="items[{{ $item->id }}][product_id]" value="{{ old('items.'.$item->id.'.product_id',$item->product_id) }}"><input autocomplete="off" list="hwsProductOptions" class="control hws-item-name" name="items[{{ $item->id }}][name]" value="{{ old('items.'.$item->id.'.name',$item->name) }}" onfocus="window.hwsShowProductSuggestions&amp;&amp;window.hwsShowProductSuggestions(this)" oninput="window.hwsProductNameChanged&amp;&amp;window.hwsProductNameChanged(this)" {{ $itemProcessed?'readonly':'' }} required><ul class="hws-suggestions-list"></ul></div>@if($itemProcessed)<small style="display:block;margin-top:5px;color:#b45309;font-weight:700">Locked — item processing started</small>@endif</td>
                    <td><input class="control hws-item-sku" name="items[{{ $item->id }}][sku]" value="{{ old('items.'.$item->id.'.sku',$item->sku) }}" {{ $itemProcessed?'readonly':'' }} required></td>
                    <td><input class="control hws-item-qty" type="number" step="0.01" min="{{ max(1,$item->qty_shipped,$item->qty_invoiced) }}" name="items[{{ $item->id }}][qty]" value="{{ old('items.'.$item->id.'.qty',$item->qty_ordered) }}" oninput="window.hwsUpdateOrderTotals()" {{ $itemProcessed?'readonly':'' }} required></td>
                    <td><input class="control hws-item-price" type="number" step="0.01" min="0" name="items[{{ $item->id }}][price]" value="{{ old('items.'.$item->id.'.price',$item->price) }}" oninput="window.hwsUpdateOrderTotals()" {{ $itemProcessed?'readonly':'' }} required></td>
                    <td class="hws-row-total">{{ core()->formatPrice($item->qty_ordered*$item->price,$order->order_currency_code) }}</td>
                    <td>{{ $item->qty_shipped }}</td>
                    <td></td>
                </tr>@endforeach
                </tbody></table></div>
                <datalist id="hwsProductOptions">@foreach($products as $product)<option value="{{ $product->name }}">{{ $product->sku }}</option>@endforeach</datalist>
                <div id="hwsNewProductRow" style="display:none"><table><tbody><tr class="hws-new-item"><td><div class="hws-suggestions"><input type="hidden" class="hws-product-id" name="items[__KEY__][product_id]"><input autocomplete="off" list="hwsProductOptions" class="control hws-item-name" name="items[__KEY__][name]" placeholder="Search product name or SKU" required><ul class="hws-suggestions-list"></ul></div></td><td><input class="control hws-item-sku" name="items[__KEY__][sku]" required></td><td><input class="control hws-item-qty" type="number" min="1" step="0.01" name="items[__KEY__][qty]" value="1" required></td><td><input class="control hws-item-price" type="number" min="0" step="0.01" name="items[__KEY__][price]" value="0" required></td><td class="hws-row-total">{{ $order->order_currency_code }} 0.00</td><td>0</td><td><button type="button" onclick="this.closest('tr').remove()" class="hws-remove-item" aria-label="Remove product">×</button></td></tr></tbody></table></div>
            </section>

            @php($initialTaxPercent = ($order->sub_total-$order->discount_amount)>0 ? ($order->tax_amount/($order->sub_total-$order->discount_amount))*100 : 0)
            <section style="margin-top:18px;padding:18px;background:#fff;border:1px solid #e2e8f0;border-radius:12px"><h3>Calculation</h3><div style="display:grid;grid-template-columns:minmax(0,1.6fr) minmax(260px,.8fr);gap:24px;align-items:start">
                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px">
                    <div class="control-group"><label>Discount Amount</label><input id="hwsDiscount" class="control" type="number" min="0" step="0.01" name="discount_amount" value="{{ old('discount_amount',$order->discount_amount) }}"></div>
                    <div class="control-group"><label>Tax (%)</label><input id="hwsTaxPercent" class="control" type="number" min="0" max="100" step="0.01" name="tax_percent" value="{{ old('tax_percent',round($initialTaxPercent,2)) }}"></div>
                    <div class="control-group"><label>Shipping Amount</label><input id="hwsShipping" class="control" type="number" min="0" step="0.01" name="shipping_amount" value="{{ old('shipping_amount',$order->shipping_amount) }}"></div>
                </div>
                <div style="padding:16px;background:#f8fafc;border-radius:10px;font-size:14px">
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px"><span>Subtotal</span><strong id="hwsSubtotal">—</strong></div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px"><span>Discount</span><strong id="hwsDiscountDisplay">—</strong></div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px"><span>Tax</span><strong id="hwsTaxDisplay">—</strong></div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:12px"><span>Shipping</span><strong id="hwsShippingDisplay">—</strong></div>
                    <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid #cbd5e1;font-size:18px"><span>Grand Total</span><strong id="hwsGrandTotal">—</strong></div>
                </div>
            </div></section>
            <div style="margin-top:18px;display:flex;gap:10px"><button class="btn btn-lg btn-primary" type="submit">Save Order</button><a class="btn btn-lg" href="{{ route('admin.sales.orders.view',$order->id) }}">Cancel</a></div>
        </form>
    </div>
</div>
@stop

@push('scripts')
<script type="text/x-template" id="hwsNewProductRow"><tr class="hws-new-item"><td><div class="hws-suggestions"><input type="hidden" class="hws-product-id" name="items[__KEY__][product_id]"><input autocomplete="off" list="hwsProductOptions" class="control hws-item-name" name="items[__KEY__][name]" placeholder="Search product name or SKU" required><ul class="hws-suggestions-list"></ul></div></td><td><input class="control hws-item-sku" name="items[__KEY__][sku]" required></td><td><input class="control hws-item-qty" type="number" min="1" step="0.01" name="items[__KEY__][qty]" value="1" required></td><td><input class="control hws-item-price" type="number" min="0" step="0.01" name="items[__KEY__][price]" value="0" required></td><td class="hws-row-total">{{ $order->order_currency_code }} 0.00</td><td>0</td><td><button type="button" onclick="this.closest('tr').remove()" class="hws-remove-item" aria-label="Remove product">×</button></td></tr></script>
<script src="{{ asset('vendor/hws/js/order-editor.js') }}?v=20260901-9"></script>
@endpush
