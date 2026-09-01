@extends('admin::layouts.master')

@section('page_title')
    Project Dashboard
@stop

@section('content-wrapper')
<div class="content full-page">
    <div class="page-header"><div class="page-title"><h1>Project Dashboard</h1><p style="margin-top:6px;color:#64748b">Project leads, orders, fulfillment and revenue.</p></div></div>
    <div class="page-content">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px">
            @foreach([
                ['Project Leads', $stats['leads'], route('hws.admin.projects.leads')],
                ['Project Orders', $stats['orders'], route('hws.admin.projects.orders')],
                ['Project Revenue', core()->formatBasePrice($stats['revenue']), route('hws.admin.projects.orders')],
                ['Shipments', $stats['shipments'], route('hws.admin.projects.shipments')],
                ['Invoices', $stats['invoices'], route('hws.admin.projects.invoices')],
                ['Refunds', $stats['refunds'], route('hws.admin.projects.refunds')],
                ['Transactions', $stats['transactions'], route('hws.admin.projects.transactions')],
            ] as [$label, $value, $url])
                <a href="{{ $url }}" style="display:block;padding:20px;border:1px solid #e2e8f0;border-radius:12px;background:#fff;text-decoration:none;color:#0f172a">
                    <div style="font-size:13px;color:#64748b;font-weight:700">{{ $label }}</div>
                    <div style="margin-top:10px;font-size:27px;font-weight:800">{{ $value }}</div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@stop
