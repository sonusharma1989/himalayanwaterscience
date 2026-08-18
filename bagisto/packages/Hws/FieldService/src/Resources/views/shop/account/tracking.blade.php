@extends('shop::customers.account.index')

@section('page_title', 'My Tracking Hub')

@section('page-detail-wrapper')
    @php
        $taskSteps = [0 => 'Assigned', 1 => 'Accepted', 2 => 'Engineer travelling', 3 => 'Work in progress', 4 => 'Completed'];
        $requestLabels = [
            'checkout' => 'Website order', 'bulk_quote' => 'Bulk quotation',
            'engineer_callback' => 'Engineer callback', 'site_survey' => 'Site survey',
        ];
    @endphp

    <div class="hws-account-shell">
        <div class="hws-account-hero">
            <div><span class="hws-eyebrow">Customer portal</span><h1>Hello, {{ $customer->first_name }}</h1><p>Track every order, quotation, service visit and installation from one place.</p></div>
            <button class="hws-btn hws-btn--primary" data-hws-request="service">New service request</button>
        </div>

        <div class="hws-account-stats">
            <div class="hws-stat-card"><span>Orders</span><b>{{ $orders->count() }}</b></div>
            <div class="hws-stat-card"><span>Requests</span><b>{{ $leads->where('request_type', '!=', 'checkout')->count() }}</b></div>
            <div class="hws-stat-card"><span>Service tasks</span><b>{{ $tasks->count() }}</b></div>
            <div class="hws-stat-card"><span>Quotations</span><b>{{ $quotations->count() }}</b></div>
        </div>

        <div class="hws-account-actions">
            <button data-hws-request="engineer_callback">Talk to engineer</button>
            <button data-hws-request="site_survey">Book site survey</button>
            <button data-hws-request="installation">Need installation</button>
            <button data-hws-request="service">Product service</button>
            <button data-hws-request="complaint">Report complaint</button>
            <button data-hws-request="amc_service">AMC service</button>
        </div>

        <section class="hws-tracking-section">
            <div class="hws-tracking-head"><h2>Orders</h2><a href="{{ route('customer.orders.index') }}">View order history →</a></div>
            <div class="hws-track-list">
                @forelse($orders->take(5) as $order)
                    <article class="hws-track-card">
                        <div><strong>Order #{{ $order->increment_id }}</strong><small>{{ $order->created_at->format('d M Y, h:i A') }} · {{ $order->total_item_count }} item(s)</small></div>
                        <div><strong>{{ core()->formatPrice($order->grand_total, $order->order_currency_code) }}</strong><small>Order total</small></div>
                        <span class="hws-status-pill">{{ str_replace('_', ' ', $order->status) }}</span>
                        <a href="{{ route('customer.orders.view', $order->id) }}">Details →</a>
                    </article>
                @empty
                    <div class="hws-track-empty">No orders yet.</div>
                @endforelse
            </div>
        </section>

        <section class="hws-tracking-section">
            <div class="hws-tracking-head"><h2>Service & installation</h2><div><button class="hws-btn hws-btn--outline" data-hws-request="installation">Request installation</button></div></div>
            <div class="hws-track-list">
                @forelse($tasks as $task)
                    <article class="hws-track-card">
                        <div><strong>{{ $task->reference_no ?: $task->task_no }} · {{ ucwords(str_replace('_', ' ', $task->type)) }}</strong><small>{{ $task->created_at->format('d M Y, h:i A') }}</small></div>
                        <div><strong>{{ $task->assignee?->name ?: 'Assignment pending' }}</strong><small>Service engineer</small></div>
                        <span class="hws-status-pill">{{ $taskSteps[$task->step] ?? 'In progress' }}</span>
                        <small>{{ $task->scheduled_at ? $task->scheduled_at->format('d M Y') : 'Schedule pending' }}</small>
                    </article>
                @empty
                    <div class="hws-track-empty">No service or installation requests yet.</div>
                @endforelse
            </div>
        </section>

        <section class="hws-tracking-section">
            <div class="hws-tracking-head"><h2>Quotes & enquiries</h2><button class="hws-btn hws-btn--outline" data-hws-request="bulk_quote">Request quote</button></div>
            <div class="hws-track-list">
                @forelse($leads->where('request_type', '!=', 'checkout') as $lead)
                    <article class="hws-track-card">
                        <div><strong>{{ $lead->reference_no ?: 'REQ-'.$lead->id }} · {{ $requestLabels[$lead->request_type] ?? ucwords(str_replace('_', ' ', $lead->request_type ?: 'enquiry')) }}</strong><small>{{ $lead->created_at->format('d M Y, h:i A') }}</small></div>
                        <div><strong>{{ $lead->assigned_to ? 'Team assigned' : 'Review pending' }}</strong><small>Owner</small></div>
                        <span class="hws-status-pill">{{ str_replace('_', ' ', $lead->status) }}</span>
                        <small>{{ $lead->next_follow_up_at ? $lead->next_follow_up_at->format('d M Y') : 'We will contact you' }}</small>
                    </article>
                @empty
                    <div class="hws-track-empty">No quotation or callback requests yet.</div>
                @endforelse

                @foreach($quotations as $quotation)
                    <article class="hws-track-card">
                        <div><strong>{{ $quotation->quote_no }}</strong><small>Quotation · {{ $quotation->created_at->format('d M Y') }}</small></div>
                        <div><strong>{{ core()->currency($quotation->grand_total) }}</strong><small>Quoted total</small></div>
                        <span class="hws-status-pill">{{ $quotation->status }}</span>
                        <a href="{{ route('hws.customer.account.quotations.pdf', $quotation->id) }}">Download PDF →</a>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@endsection

{{-- Bagisto default and Velocity account layouts use different section names. --}}
@section('account-content')
    @yield('page-detail-wrapper')
@endsection
