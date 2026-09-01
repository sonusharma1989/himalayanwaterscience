@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.sales.invoices.add-title') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <form method="POST" action="{{ route('admin.sales.invoices.store', $order->id) }}" @submit.prevent="onSubmit">
            @csrf()

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('admin.sales.invoices.index') }}'"></i>

                        {{ __('admin::app.sales.invoices.add-title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.sales.invoices.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="sale-container">

                    <accordian title="{{ __('admin::app.sales.orders.order-and-account') }}" :active="true">
                        <div slot="body">

                            <div class="sale">
                                <div class="sale-section">
                                    <div class="secton-title">
                                        <span>{{ __('admin::app.sales.orders.order-info') }}</span>
                                    </div>

                                    <div class="section-content">
                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.invoices.order-id') }}
                                            </span>

                                            <span class="value">
                                                <a href="{{ route('admin.sales.orders.view', $order->id) }}">#{{ $order->increment_id }}</a>
                                            </span>
                                        </div>

                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.orders.order-date') }}
                                            </span>

                                            <span class="value">
                                                {{ $order->created_at }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.orders.order-status') }}
                                            </span>

                                            <span class="value">
                                                {{ $order->status_label }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.orders.channel') }}
                                            </span>

                                            <span class="value">
                                                {{ $order->channel_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="sale-section">
                                    <div class="secton-title">
                                        <span>{{ __('admin::app.sales.orders.account-info') }}</span>
                                    </div>

                                    <div class="section-content">
                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.orders.customer-name') }}
                                            </span>

                                            <span class="value">
                                                {{ $order->customer_full_name }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.orders.email') }}
                                            </span>

                                            <span class="value">
                                                {{ $order->customer_email }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </accordian>

                    @if (
                        $order->billing_address
                        || $order->shipping_address
                    )
                        <accordian title="{{ __('admin::app.sales.orders.address') }}" :active="true">
                            <div slot="body">
                                <div class="sale">
                                    @if ($order->billing_address)
                                        <div class="sale-section">
                                            <div class="secton-title">
                                                <span>{{ __('admin::app.sales.orders.billing-address') }}</span>
                                            </div>

                                            <div class="section-content">

                                                @include ('admin::sales.address', ['address' => $order->billing_address])

                                            </div>
                                        </div>
                                    @endif

                                    @if ($order->shipping_address)
                                        <div class="sale-section">
                                            <div class="secton-title">
                                                <span>{{ __('admin::app.sales.orders.shipping-address') }}</span>
                                            </div>

                                            <div class="section-content">

                                                @include ('admin::sales.address', ['address' => $order->shipping_address])

                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </accordian>
                    @endif

                    <accordian title="GST Invoice Details" :active="true">
                        <div slot="body">
                            @php
                                $supplierState = trim((string) core()->getConfigData('sales.shipping.origin.state'));
                                $placeOfSupply = trim((string) optional($order->shipping_address ?: $order->billing_address)->state);
                                $selectedGstTaxType = old('gst_tax_type', $order->gst_tax_type ?: ($supplierState && $placeOfSupply && strcasecmp($supplierState, $placeOfSupply) !== 0 ? 'inter_state' : 'intra_state'));
                                $isInterStateGst = $selectedGstTaxType === 'inter_state';
                            @endphp
                            <div style="padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:12px">
                                <input type="hidden" name="is_gst_invoice" value="0">
                                <label style="display:flex;align-items:center;gap:9px;margin-bottom:16px;font-size:14px;font-weight:700;color:#334155">
                                    <input type="checkbox" name="is_gst_invoice" value="1" {{ old('is_gst_invoice', $order->is_gst_invoice) ? 'checked' : '' }}>
                                    Generate GST Tax Invoice
                                </label>

                                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px">
                                    <div class="control-group">
                                        <label>Legal Company Name</label>
                                        <input class="control" name="billing_company_name" value="{{ old('billing_company_name', $order->billing_company_name) }}" placeholder="Company name on GST certificate">
                                    </div>

                                    <div class="control-group">
                                        <label>GSTIN</label>
                                        <input class="control" name="gstin" value="{{ old('gstin', $order->gstin) }}" maxlength="15" style="text-transform:uppercase" placeholder="22AAAAA0000A1Z5">
                                    </div>

                                    <div class="control-group">
                                        <label>Place of Supply (State)</label>
                                        <input class="control" name="gst_place_of_supply" value="{{ old('gst_place_of_supply', $order->gst_place_of_supply ?: $placeOfSupply) }}" placeholder="e.g. Rajasthan">
                                    </div>

                                    <div class="control-group">
                                        <label>GST Tax Type</label>
                                        <select class="control" name="gst_tax_type">
                                            <option value="intra_state" {{ $selectedGstTaxType === 'intra_state' ? 'selected' : '' }}>Intra-State — CGST + SGST</option>
                                            <option value="inter_state" {{ $selectedGstTaxType === 'inter_state' ? 'selected' : '' }}>Inter-State — IGST</option>
                                        </select>
                                    </div>

                                    <div class="control-group">
                                        <label>{{ $isInterStateGst ? 'IGST' : 'CGST + SGST' }}</label>
                                        <div class="control" style="display:flex;align-items:center;background:#f8fafc;font-weight:700">
                                            @if($isInterStateGst)
                                                {{ core()->formatPrice($order->tax_amount, $order->order_currency_code) }}
                                            @else
                                                CGST {{ core()->formatPrice($order->tax_amount / 2, $order->order_currency_code) }} + SGST {{ core()->formatPrice($order->tax_amount / 2, $order->order_currency_code) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-top:10px;color:#64748b;font-size:12px">
                                    Supplier State: <strong>{{ $supplierState ?: 'Not configured' }}</strong> &nbsp;•&nbsp;
                                    Address State: <strong>{{ $placeOfSupply ?: 'Not available' }}</strong>
                                </div>

                                @error('gstin')<div style="margin-top:8px;color:#dc2626;font-size:12px">{{ $message }}</div>@enderror
                                @error('billing_company_name')<div style="margin-top:8px;color:#dc2626;font-size:12px">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </accordian>

                    <accordian title="{{ __('admin::app.sales.orders.payment-and-shipping') }}" :active="true">
                        <div slot="body">
                            <div class="sale">
                                <div class="sale-section">
                                    <div class="secton-title">
                                        <span>{{ __('admin::app.sales.orders.payment-info') }}</span>
                                    </div>

                                    <div class="section-content">
                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.orders.payment-method') }}
                                            </span>

                                            <span class="value">
                                                {{ core()->getConfigData('sales.paymentmethods.' . $order->payment->method . '.title') }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.orders.currency') }}
                                            </span>

                                            <span class="value">
                                                {{ $order->order_currency_code }}
                                            </span>
                                        </div>

                                        @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($order->payment->method); @endphp

                                        @if (! empty($additionalDetails))
                                            <div class="row">
                                                <span class="title">
                                                    {{ $additionalDetails['title'] }}
                                                </span>

                                                <span class="value">
                                                    {{ $additionalDetails['value'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($order->shipping_address)
                                    <div class="sale-section">
                                        <div class="secton-title">
                                            <span>{{ __('admin::app.sales.orders.shipping-info') }}</span>
                                        </div>

                                        <div class="section-content">
                                            <div class="row">
                                                <span class="title">
                                                    {{ __('admin::app.sales.orders.shipping-method') }}
                                                </span>

                                                <span class="value">
                                                    {{ $order->shipping_title }}
                                                </span>
                                            </div>

                                            <div class="row">
                                                <span class="title">
                                                    {{ __('admin::app.sales.orders.shipping-price') }}
                                                </span>

                                                <span class="value">
                                                    {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </accordian>

                    <accordian title="{{ __('admin::app.sales.orders.products-ordered') }}" :active="true">
                        <div slot="body">

                            <div class="table">
                                <div class="table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>{{ __('admin::app.sales.orders.SKU') }}</th>
                                                <th>HSN</th>
                                                <th>{{ __('admin::app.sales.orders.product-name') }}</th>
                                                <th>{{ __('admin::app.sales.invoices.qty-ordered') }}</th>
                                                <th>CGST</th>
                                                <th>SGST</th>
                                                <th>IGST</th>
                                                <th>{{ __('admin::app.sales.invoices.qty-to-invoice') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach ($order->items as $item)
                                                @if ($item->qty_to_invoice > 0)
                                                    <tr>
                                                        <td>{{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</td>
                                                        <td>{{ $item->hsn_code }}</td>
                                                        <td>
                                                            {{ $item->name }}

                                                            @if (isset($item->additional['attributes']))
                                                                <div class="item-options">

                                                                    @foreach ($item->additional['attributes'] as $attribute)
                                                                        <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                                                    @endforeach

                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->qty_ordered }}</td>
                                                        @php
                                                            $itemTax = $item->qty_ordered > 0 ? ((float) $item->base_tax_amount / $item->qty_ordered) * $item->qty_to_invoice : 0;
                                                        @endphp
                                                        <td>{{ core()->formatBasePrice($selectedGstTaxType === 'intra_state' ? $itemTax / 2 : 0) }}</td>
                                                        <td>{{ core()->formatBasePrice($selectedGstTaxType === 'intra_state' ? $itemTax / 2 : 0) }}</td>
                                                        <td>{{ core()->formatBasePrice($selectedGstTaxType === 'inter_state' ? $itemTax : 0) }}</td>
                                                        <td>
                                                            <div class="control-group" :class="[errors.has('invoice[items][{{ $item->id }}]') ? 'has-error' : '']">
                                                                <input type="text" v-validate="'required|numeric|min:0'" class="control" id="invoice[items][{{ $item->id }}]" name="invoice[items][{{ $item->id }}]" value="{{ $item->qty_to_invoice }}" data-vv-as="&quot;{{ __('admin::app.sales.invoices.qty-to-invoice') }}&quot;"/>

                                                                <span class="control-error" v-if="errors.has('invoice[items][{{ $item->id }}]')">
                                                                    @verbatim
                                                                        {{ errors.first('invoice[items][<?php echo $item->id ?>]') }}
                                                                    @endverbatim
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </accordian>

                </div>
            </div>
        </form>
    </div>
@stop
