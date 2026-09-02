@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.sales.orders.view-title', ['order_id' => $order->increment_id]) }}
@stop

@section('content-wrapper')
    @php
        $actualPaidAmount = 0;
        $orderInvoiceIds = $order->invoices->pluck('id')->toArray();

        $transactionsList = \Webkul\Sales\Models\OrderTransaction::where('order_id', $order->id)
            ->when(!empty($orderInvoiceIds), function ($q) use ($orderInvoiceIds) {
                $q->orWhereIn('invoice_id', $orderInvoiceIds);
            })
            ->get();

        foreach ($transactionsList as $t) {
            $tAmt = (float) $t->amount;
            if ($tAmt <= 0 && $t->data) {
                $tData = is_array($t->data) ? $t->data : json_decode($t->data ?: '{}', true);
                if (is_array($tData)) {
                    $tAmt = (float) ($tData['amount'] ?? ($tData['payment_amount'] ?? ($tData['grand_total'] ?? 0)));
                }
            }
            $actualPaidAmount += $tAmt;
        }

        $actualDueAmount = max(0, (float) $order->base_grand_total - $actualPaidAmount);
    @endphp

    <div class="content full-page">

        <div class="page-header">

            <div class="page-title">
                <h1>
                    {!! view_render_event('sales.order.title.before', ['order' => $order]) !!}

                    <i class="icon angle-left-icon back-link" onclick="window.location = '{{ $order->sales_type === 'projects' ? route('hws.admin.projects.orders') : route('admin.sales.orders.index') }}'"></i>

                    {{ __('admin::app.sales.orders.view-title', ['order_id' => $order->increment_id]) }}

                    {!! view_render_event('sales.order.title.after', ['order' => $order]) !!}
                </h1>
            </div>

            <div class="page-action">
                {!! view_render_event('sales.order.page_action.before', ['order' => $order]) !!}

                <a href="{{ route('hws.admin.orders.edit', $order->id) }}" class="btn btn-lg btn-primary">Edit Order</a>

                @if (
                    $order->canCancel()
                    && bouncer()->hasPermission('sales.orders.cancel')
                )
                    <a href="{{ route('admin.sales.orders.cancel', $order->id) }}" class="btn btn-lg btn-primary" v-alert:message="'{{ __('admin::app.sales.orders.cancel-confirm-msg') }}'">
                        {{ __('admin::app.sales.orders.cancel-btn-title') }}
                    </a>
                @endif

                @if (
                    $order->canInvoice()
                    && $order->payment->method !== 'paypal_standard'
                )
                    <a href="{{ route('admin.sales.invoices.create', $order->id) }}" class="btn btn-lg btn-primary">
                        {{ __('admin::app.sales.orders.invoice-btn-title') }}
                    </a>
                @endif

                @if ($order->canRefund())
                    <a href="{{ route('admin.sales.refunds.create', $order->id) }}" class="btn btn-lg btn-primary">
                        {{ __('admin::app.sales.orders.refund-btn-title') }}
                    </a>
                @endif

                @php
                    $shippableItems = $order->items->filter(fn($i) => ($i->qty_to_ship > 0 || ($i->qty_ordered - $i->qty_shipped) > 0));
                    $anyItemPassed = $order->items->contains(fn($i) => ($i->qc_status ?? 'pending') === 'passed');
                    $isQcPassed = ($order->qc_status === 'passed') || ($shippableItems->isNotEmpty() && $shippableItems->contains(fn($i) => ($i->qc_status ?? 'pending') === 'passed')) || $anyItemPassed;
                @endphp

                @if ($order->canShip())
                    @if ($isQcPassed)
                        <a href="{{ $order->sales_type === 'projects' ? route('hws.admin.projects.shipments.create', $order->id) : route('admin.sales.shipments.create', $order->id) }}" class="btn btn-lg btn-primary">
                            {{ __('admin::app.sales.orders.shipment-btn-title') }}
                        </a>
                    @else
                        <button type="button" class="btn btn-lg" style="background: #94a3b8; color: #fff; cursor: not-allowed;" title="At least one ordered item must pass Quality Check (QC) before creating shipment" onclick="window.hwsShowToast ? window.hwsShowToast('Cannot ship order: Quality Check (QC) is pending. Please mark items as QC Passed first.', 'error') : alert('Cannot ship order: QC is pending.');">
                            QC Pending (Cannot Ship)
                        </button>
                    @endif
                @endif

                {!! view_render_event('sales.order.page_action.after', ['order' => $order]) !!}
            </div>
        </div>

        <div class="page-content">

            <tabs>
                {!! view_render_event('sales.order.tabs.before', ['order' => $order]) !!}

                <tab name="{{ __('admin::app.sales.orders.info') }}" :selected="true">
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
                                                    {{ __('admin::app.sales.orders.order-date') }}
                                                </span>

                                                <span class="value">
                                                    {{ $order->created_at }}
                                                </span>
                                            </div>

                                            {!! view_render_event('sales.order.created_at.after', ['order' => $order]) !!}

                                            <div class="row">
                                                <span class="title">
                                                    {{ __('admin::app.sales.orders.order-status') }}
                                                </span>

                                                <span class="value">
                                                    {{ __('admin::app.notification.order-status-messages.'.strtolower($order->status)) }}
                                                </span>
                                            </div>

                                            {!! view_render_event('sales.order.status_label.after', ['order' => $order]) !!}

                                            <div class="row">
                                                <span class="title">Sales Type</span>
                                                <span class="value">{{ ucfirst($order->sales_type ?: 'trading') }}</span>
                                            </div>

                                            <div class="row">
                                                <span class="title">
                                                    {{ __('admin::app.sales.orders.channel') }}
                                                </span>

                                                <span class="value">
                                                    {{ $order->channel_name }}
                                                </span>
                                            </div>

                                            {!! view_render_event('sales.order.channel_name.after', ['order' => $order]) !!}
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

                                            {!! view_render_event('sales.order.customer_full_name.after', ['order' => $order]) !!}

                                            <div class="row">
                                                <span class="title">
                                                    {{ __('admin::app.sales.orders.email') }}
                                                </span>

                                                <span class="value">
                                                    {{ $order->customer_email }}
                                                </span>
                                            </div>

                                            {!! view_render_event('sales.order.customer_email.after', ['order' => $order]) !!}

                                            <div class="row" style="align-items: center;">
                                                <span class="title">
                                                    Account Manager
                                                </span>

                                                <span class="value">
                                                    @php
                                                        $allAdmins = \Illuminate\Support\Facades\DB::table('admins')->select('id', 'name')->get();
                                                    @endphp
                                                    <select onchange="hwsQuickAssignOrderManager({{ $order->id }}, this.value, this)" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 13px; background: #f8fafc; font-weight: 600; color: #1e293b; cursor: pointer; outline: none;">
                                                        <option value="">Unassigned</option>
                                                        @foreach ($allAdmins as $adm)
                                                            <option value="{{ $adm->id }}" {{ ((string)$order->account_manager_id === (string)$adm->id) ? 'selected' : '' }}>
                                                                {{ $adm->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </span>
                                            </div>

                                            @if (
                                                ! is_null($order->customer)
                                                && ! is_null($order->customer->group)
                                            )
                                                <div class="row">
                                                    <span class="title">
                                                        {{ __('admin::app.customers.customers.customer_group') }}
                                                    </span>

                                                    <span class="value">
                                                        {{ $order->customer->group->name }}
                                                    </span>
                                                </div>
                                            @endif

                                            {!! view_render_event('sales.order.customer_group.after', ['order' => $order]) !!}
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
                                        @if($order->billing_address)
                                            <div class="sale-section">
                                                <div class="secton-title">
                                                    <span>{{ __('admin::app.sales.orders.billing-address') }}</span>
                                                </div>

                                                <div class="section-content">
                                                    @include ('admin::sales.address', ['address' => $order->billing_address])

                                                    {!! view_render_event('sales.order.billing_address.after', ['order' => $order]) !!}
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

                                                    {!! view_render_event('sales.order.shipping_address.after', ['order' => $order]) !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>                                   
                                </div>
                            </accordian>
                        @endif

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
                                                    {{ $order->payment->method_title ?: core()->getConfigData('sales.paymentmethods.' . $order->payment->method . '.title') ?: ucwords(str_replace('_', ' ', $order->payment->method)) }}
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

                                            @php
                                                $additionalDetails = [];
                                                $paymentMethodClass = config('paymentmethods.' . $order->payment->method . '.class');

                                                if ($paymentMethodClass) {
                                                    $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($order->payment->method);
                                                }
                                            @endphp

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

                                            {!! view_render_event('sales.order.payment-method.after', ['order' => $order]) !!}

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

                                                {!! view_render_event('sales.order.shipping-method.after', ['order' => $order]) !!}
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
                                                    <th>{{ __('admin::app.sales.orders.price') }}</th>
                                                    <th>{{ __('admin::app.sales.orders.item-status') }}</th>
                                                    <th>Quality Check (QC)</th>
                                                    <th>{{ __('admin::app.sales.orders.subtotal') }}</th>
                                                    <th>{{ __('admin::app.sales.orders.tax-percent') }}</th>
                                                    <th>{{ __('admin::app.sales.orders.tax-amount') }}</th>
                                                    @if ($order->base_discount_amount > 0)
                                                        <th>{{ __('admin::app.sales.orders.discount-amount') }}</th>
                                                    @endif
                                                    <th>{{ __('admin::app.sales.orders.grand-total') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach ($order->items as $item)

                                                    <tr>
                                                        <td>
                                                            {{ $item->product ? $item->getTypeInstance()->getOrderedItem($item)->sku : $item->sku }}
                                                        </td>

                                                        <td>
                                                            {{ $item->hsn_code }}
                                                        </td>

                                                        <td>
                                                            {{ $item->name }}

                                                            @if (isset($item->additional['attributes']))
                                                                <div class="item-options">

                                                                    @foreach ($item->additional['attributes'] as $attribute)
                                                                        <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}<br>
                                                                    @endforeach

                                                                </div>
                                                            @endif
                                                        </td>

                                                        <td>{{ core()->formatBasePrice($item->base_price) }}</td>

                                                        <td>
                                                            <span class="qty-row">
                                                                {{ $item->qty_ordered ? __('admin::app.sales.orders.item-ordered', ['qty_ordered' => $item->qty_ordered]) : '' }}
                                                            </span>

                                                            <span class="qty-row">
                                                                {{ $item->qty_invoiced ? __('admin::app.sales.orders.item-invoice', ['qty_invoiced' => $item->qty_invoiced]) : '' }}
                                                            </span>

                                                            <span class="qty-row">
                                                                {{ $item->qty_shipped ? __('admin::app.sales.orders.item-shipped', ['qty_shipped' => $item->qty_shipped]) : '' }}
                                                            </span>

                                                            <span class="qty-row">
                                                                {{ $item->qty_refunded ? __('admin::app.sales.orders.item-refunded', ['qty_refunded' => $item->qty_refunded]) : '' }}
                                                            </span>

                                                            <span class="qty-row">
                                                                {{ $item->qty_canceled ? __('admin::app.sales.orders.item-canceled', ['qty_canceled' => $item->qty_canceled]) : '' }}
                                                            </span>
                                                        </td>

                                                        <!-- Item-Level QC Column -->
                                                        <td style="min-width: 170px;">
                                                            @php
                                                                $qcItemStatus = $item->qc_status ?? 'pending';
                                                                $qcBadgeStyle = match($qcItemStatus) {
                                                                    'passed' => 'background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;',
                                                                    'failed' => 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;',
                                                                    default  => 'background:#fef3c7;color:#92400e;border:1px solid #fde68a;',
                                                                };
                                                            @endphp
                                                            <div style="margin-bottom: 6px;">
                                                                <span style="display:inline-block; padding: 3px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; {{ $qcBadgeStyle }}">
                                                                    {{ $qcItemStatus === 'passed' ? '✓ Passed' : ($qcItemStatus === 'failed' ? '✕ Failed' : '⏳ Pending') }}
                                                                </span>
                                                                @if($item->qc_serial_no)
                                                                    <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                                                                        <b>S/N:</b> {{ $item->qc_serial_no }}
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <details style="font-size: 12px; cursor: pointer;">
                                                                <summary style="color: #3c50e0; font-weight: 600;">Update QC</summary>
                                                                <form method="POST" action="{{ route('hws.admin.orders.item-qc', $order->id) }}" style="margin-top: 8px; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                                                    @csrf
                                                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                                    <div style="margin-bottom: 6px;">
                                                                        <label style="font-size: 10px; font-weight: 700; color: #475569; display: block;">STATUS</label>
                                                                        <select name="qc_status" style="width: 100%; font-size: 12px; padding: 4px; border-radius: 4px; border: 1px solid #cbd5e1;">
                                                                            <option value="passed" {{ $qcItemStatus === 'passed' ? 'selected' : '' }}>✓ Pass QC</option>
                                                                            <option value="failed" {{ $qcItemStatus === 'failed' ? 'selected' : '' }}>✕ Reject (Fail)</option>
                                                                            <option value="pending" {{ $qcItemStatus === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                                        </select>
                                                                    </div>
                                                                    <div style="margin-bottom: 6px;">
                                                                        <label style="font-size: 10px; font-weight: 700; color: #475569; display: block;">SERIAL/BATCH NO</label>
                                                                        <input type="text" name="qc_serial_no" value="{{ $item->qc_serial_no }}" placeholder="e.g. SN-8921" style="width: 100%; font-size: 11px; padding: 4px; border-radius: 4px; border: 1px solid #cbd5e1; box-sizing: border-box;">
                                                                    </div>
                                                                    <div style="margin-bottom: 8px;">
                                                                        <label style="font-size: 10px; font-weight: 700; color: #475569; display: block;">REMARKS</label>
                                                                        <input type="text" name="qc_notes" value="{{ $item->qc_notes }}" placeholder="QC inspection notes..." style="width: 100%; font-size: 11px; padding: 4px; border-radius: 4px; border: 1px solid #cbd5e1; box-sizing: border-box;">
                                                                    </div>
                                                                    <button type="submit" style="width: 100%; background: #3c50e0; color: #fff; border: 0; padding: 5px; border-radius: 4px; font-weight: 700; font-size: 11px; cursor: pointer;">
                                                                        Save QC
                                                                    </button>
                                                                </form>
                                                            </details>
                                                        </td>

                                                        <td>{{ core()->formatBasePrice($item->base_total) }}</td>

                                                        <td>{{ $item->tax_percent }}%</td>

                                                        <td>{{ core()->formatBasePrice($item->base_tax_amount) }}</td>

                                                        @if ($order->base_discount_amount > 0)
                                                            <td>{{ core()->formatBasePrice($item->base_discount_amount) }}</td>
                                                        @endif

                                                        <td>{{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                               

                                <div class="summary-comment-container">
                                    <div class="comment-container">
                                        <form action="{{ route('admin.sales.orders.comment', $order->id) }}" method="post" @submit.prevent="onSubmit">
                                            @csrf()

                                            <div class="control-group" :class="[errors.has('comment') ? 'has-error' : '']">
                                                <label for="comment" class="required">{{ __('admin::app.sales.orders.comment') }}</label>
                                                <textarea v-validate="'required'" class="control" id="comment" name="comment" data-vv-as="&quot;{{ __('admin::app.sales.orders.comment') }}&quot;"></textarea>
                                                <span class="control-error" v-if="errors.has('comment')">@{{ errors.first('comment') }}</span>
                                            </div>

                                            <div class="control-group">
                                                <span class="checkbox">
                                                    <input type="checkbox" name="customer_notified" id="customer-notified" name="checkbox[]">
                                                    <label class="checkbox-view" for="customer-notified"></label>
                                                    {{ __('admin::app.sales.orders.notify-customer') }}
                                                </span>
                                            </div>

                                            <button type="submit" class="btn btn-lg btn-primary">
                                                {{ __('admin::app.sales.orders.submit-comment') }}
                                            </button>
                                        </form>

                                        <ul class="comment-list">
                                            @foreach ($order->comments()->orderBy('id', 'desc')->get() as $comment)
                                                <li>
                                                    <span class="comment-info">
                                                        @if ($comment->customer_notified)
                                                            {!! __('admin::app.sales.orders.customer-notified', ['date' => $comment->created_at]) !!}
                                                        @else
                                                            {!! __('admin::app.sales.orders.customer-not-notified', ['date' => $comment->created_at]) !!}
                                                        @endif
                                                    </span>

                                                    <p>{{ $comment->comment }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <table class="sale-summary">
                                        <tr>
                                            <td>{{ __('admin::app.sales.orders.subtotal') }}</td>
                                            <td>-</td>
                                            <td>{{ core()->formatBasePrice($order->base_sub_total) }}</td>
                                        </tr>

                                        @if ($order->haveStockableItems())
                                            <tr>
                                                <td>{{ __('admin::app.sales.orders.shipping-handling') }}</td>
                                                <td>-</td>
                                                <td>{{ core()->formatBasePrice($order->base_shipping_amount) }}</td>
                                            </tr>
                                        @endif

                                        @if ($order->base_discount_amount > 0)
                                            <tr>
                                                <td>
                                                    {{ __('admin::app.sales.orders.discount') }}

                                                    @if ($order->coupon_code)
                                                        ({{ $order->coupon_code }})
                                                    @endif
                                                </td>
                                                <td>-</td>
                                                <td>{{ core()->formatBasePrice($order->base_discount_amount) }}</td>
                                            </tr>
                                        @endif

                                        <tr class="border">
                                            <td>{{ __('admin::app.sales.orders.tax') }}</td>
                                            <td>-</td>
                                            <td>{{ core()->formatBasePrice($order->base_tax_amount) }}</td>
                                        </tr>

                                        <tr class="bold">
                                            <td>{{ __('admin::app.sales.orders.grand-total') }}</td>
                                            <td>-</td>
                                            <td>{{ core()->formatBasePrice($order->base_grand_total) }}</td>
                                        </tr>

                                        <tr class="bold">
                                            <td>{{ __('admin::app.sales.orders.total-paid') }}</td>
                                            <td>-</td>
                                            <td style="color:#16a34a;">{{ core()->formatBasePrice($actualPaidAmount) }}</td>
                                        </tr>

                                        <tr class="bold">
                                            <td>{{ __('admin::app.sales.orders.total-refunded') }}</td>
                                            <td>-</td>
                                            <td>{{ core()->formatBasePrice($order->base_grand_total_refunded) }}</td>
                                        </tr>

                                        <tr class="bold">
                                            <td>{{ __('admin::app.sales.orders.total-due') }}</td>

                                            <td>-</td>

                                            @if($order->status !== 'canceled')
                                                <td style="color:{{ $actualDueAmount > 0 ? '#dc2626' : '#16a34a' }};">{{ core()->formatBasePrice($actualDueAmount) }}</td>
                                            @else
                                                <td id="due-amount-on-cancelled">{{ core()->formatBasePrice(0.00) }}</td>
                                            @endif
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </accordian>

                    </div>
                </tab>

                <tab name="{{ __('admin::app.sales.orders.invoices') }}">

                    <div class="table" style="padding: 20px 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('admin::app.sales.invoices.id') }}</th>
                                    <th>{{ __('admin::app.sales.invoices.date') }}</th>
                                    <th>{{ __('admin::app.sales.invoices.order-id') }}</th>
                                    <th>{{ __('admin::app.sales.invoices.customer-name') }}</th>
                                    <th>{{ __('admin::app.sales.invoices.status') }}</th>
                                    <th>{{ __('admin::app.sales.invoices.amount') }}</th>
                                    <th>{{ __('admin::app.sales.invoices.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($order->invoices as $invoice)
                                    <tr>
                                        <td>#{{ $invoice->increment_id ?? $invoice->id }}</td>
                                        <td>{{ $invoice->created_at }}</td>
                                        <td>#{{ $invoice->order->increment_id }}</td>
                                        <td>{{ $order->customer_full_name }}</td>
                                        <td>{{ $invoice->status_label }}</td>
                                        <td>{{ core()->formatBasePrice($invoice->base_grand_total) }}</td>
                                        <td class="action">
                                            <a href="{{ route('admin.sales.invoices.view', $invoice->id) }}">
                                                <i class="icon eye-icon"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if (! $order->invoices->count())
                                    <tr>
                                        <td class="empty" colspan="7">{{ __('admin::app.common.no-result-found') }}</td>
                                    <tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </tab>

                <tab name="{{ __('admin::app.sales.orders.shipments') }}">

                    <div class="table" style="padding: 20px 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('admin::app.sales.shipments.id') }}</th>
                                    <th>{{ __('admin::app.sales.shipments.date') }}</th>
                                    <th>{{ __('admin::app.sales.shipments.carrier-title') }}</th>
                                    <th>{{ __('admin::app.sales.shipments.tracking-number') }}</th>
                                    <th>{{ __('admin::app.sales.shipments.total-qty') }}</th>
                                    <th>{{ __('admin::app.sales.shipments.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($order->shipments as $shipment)
                                    <tr>
                                        <td>#{{ $shipment->id }}</td>
                                        <td>{{ $shipment->created_at }}</td>
                                        <td>{{ $shipment->carrier_title }}</td>
                                        <td>{{ $shipment->track_number }}</td>
                                        <td>{{ $shipment->total_qty }}</td>
                                        <td class="action">
                                            <a href="{{ route('admin.sales.shipments.view', $shipment->id) }}">
                                                <i class="icon eye-icon"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if (! $order->shipments->count())
                                    <tr>
                                        <td class="empty" colspan="7">{{ __('admin::app.common.no-result-found') }}</td>
                                    <tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </tab>

                <tab name="{{ __('admin::app.sales.orders.refunds') }}">

                    <div class="table" style="padding: 20px 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('admin::app.sales.refunds.id') }}</th>
                                    <th>{{ __('admin::app.sales.refunds.date') }}</th>
                                    <th>{{ __('admin::app.sales.refunds.order-id') }}</th>
                                    <th>{{ __('admin::app.sales.refunds.customer-name') }}</th>
                                    <th>{{ __('admin::app.sales.refunds.status') }}</th>
                                    <th>{{ __('admin::app.sales.refunds.refunded') }}</th>
                                    <th>{{ __('admin::app.sales.refunds.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($order->refunds as $refund)
                                    <tr>
                                        <td>#{{ $refund->id }}</td>
                                        <td>{{ $refund->created_at }}</td>
                                        <td>#{{ $refund->order->increment_id }}</td>
                                        <td>{{ $refund->order->customer_full_name }}</td>
                                        <td>{{ __('admin::app.sales.refunds.refunded') }}</td>
                                        <td>{{ core()->formatBasePrice($refund->base_grand_total) }}</td>
                                        <td class="action">
                                            <a href="{{ route('admin.sales.refunds.view', $refund->id) }}">
                                                <i class="icon eye-icon"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if (! $order->refunds->count())
                                    <tr>
                                        <td class="empty" colspan="7">{{ __('admin::app.common.no-result-found') }}</td>
                                    <tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </tab>

                <tab name="{{ __('admin::app.sales.orders.transactions') }}">

                    <div style="margin:20px 0;padding:18px;border:1px solid #dbe3ef;border-radius:10px;background:#fff;">
                        <h3 style="margin:0 0 14px;font-size:16px;color:#1f2937;">Add Manual Payment</h3>

                        <form method="POST" action="{{ route('hws.admin.orders.manual-payment', $order->id) }}">
                            @csrf

                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
                                <div class="control-group">
                                    <label for="hws-payment-method">Payment Method</label>
                                    <select id="hws-payment-method" name="payment_method" class="control" required>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="upi_manual">Manual UPI</option>
                                    </select>
                                </div>

                                <div class="control-group">
                                    <label for="hws-payment-amount">Amount (Remaining Due: ₹{{ number_format($actualDueAmount, 2) }})</label>
                                    <input id="hws-payment-amount" type="number" name="amount" class="control" value="{{ round($actualDueAmount, 2) }}" min="0.01" max="{{ round($actualDueAmount > 0 ? $actualDueAmount : (float) $order->grand_total, 2) }}" step="0.01" required>
                                </div>

                                <div class="control-group">
                                    <label for="hws-payment-reference">Receipt / Reference</label>
                                    <input id="hws-payment-reference" type="text" name="reference" class="control" maxlength="100" placeholder="Optional reference number">
                                </div>

                                <div class="control-group">
                                    <label for="hws-payment-notes">Notes</label>
                                    <input id="hws-payment-notes" type="text" name="notes" class="control" maxlength="500" placeholder="Optional payment note">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-lg btn-primary" style="margin-top:14px;">Save Manual Payment</button>
                        </form>
                    </div>

                    <div class="table" style="padding: 20px 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('admin::app.sales.transactions.transaction-id') }}</th>
                                    <th>{{ __('admin::app.sales.invoices.order-id') }}</th>
                                    <th>{{ __('admin::app.sales.transactions.payment-method') }}</th>
                                    <th>Amount</th>
                                    <th>Reference</th>
                                    <th>{{ __('admin::app.sales.transactions.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($transactionsList as $transaction)
                                    @php($hwsTransactionData = is_array($transaction->data) ? $transaction->data : json_decode($transaction->data ?: '{}', true))
                                    @php($tRowAmount = (float) ($transaction->amount ?: ($hwsTransactionData['amount'] ?? 0)))
                                    <tr>
                                        <td>#{{ $transaction->transaction_id }}</td>
                                        <td>{{ $transaction->order_id }}</td>
                                        <td>
                                             {{ core()->getConfigData('sales.paymentmethods.' . $transaction->payment_method . '.title') ?: ucwords(str_replace('_', ' ', $transaction->payment_method)) }}
                                        </td>
                                        <td>{{ $tRowAmount > 0 ? core()->formatPrice($tRowAmount, $order->order_currency_code) : '—' }}</td>
                                        <td>{{ $hwsTransactionData['reference'] ?? '—' }}</td>
                                        <td class="action">
                                            <a href="{{ route('admin.sales.transactions.view', $transaction->id) }}">
                                                <i class="icon eye-icon"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if (! $transactionsList->count())
                                    <tr>
                                        <td class="empty" colspan="6">{{ __('admin::app.common.no-result-found') }}</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>

                </tab>

                {!! view_render_event('sales.order.tabs.after', ['order' => $order]) !!}
            </tabs>
        </div>

    </div>
@stop
