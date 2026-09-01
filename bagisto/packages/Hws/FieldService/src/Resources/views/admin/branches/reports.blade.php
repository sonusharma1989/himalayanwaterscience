@extends('hws::admin.layouts.menu')

@section('page_title')
    Branch Performance & Analytics Report
@stop

@section('page-content')
    <div class="content">
        <div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <div class="page-title">
                <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('hws.admin.branches.index') }}'"></i>
                <h1 style="font-size:22px;font-weight:700;color:#0f172a;margin:0;">📊 Branch Performance & Analytics Report</h1>
                <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Branch-wise sales, lead conversions, project tasks, and service operations</p>
            </div>

            <div class="page-action">
                <a href="{{ route('hws.admin.branches.index') }}" class="btn btn-lg btn-primary" style="font-weight:600;">
                    Manage Branches
                </a>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:16px;margin-bottom:24px;">
            <div style="background:#ffffff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Revenue</div>
                <div style="font-size:24px;font-weight:800;color:#0f172a;margin-top:6px;">{{ core()->formatBasePrice($totals->total_revenue) }}</div>
                <div style="font-size:12px;color:#16a34a;margin-top:4px;font-weight:600;">Across {{ $totals->total_branches }} Branches</div>
            </div>

            <div style="background:#ffffff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Collections (Paid)</div>
                <div style="font-size:24px;font-weight:800;color:#16a34a;margin-top:6px;">{{ core()->formatBasePrice($totals->total_paid) }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:4px;">Recorded payments</div>
            </div>

            <div style="background:#ffffff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Outstanding Dues</div>
                <div style="font-size:24px;font-weight:800;color:#dc2626;margin-top:6px;">{{ core()->formatBasePrice($totals->total_due) }}</div>
                <div style="font-size:12px;color:#dc2626;margin-top:4px;font-weight:600;">Pending collections</div>
            </div>

            <div style="background:#ffffff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Workforce</div>
                <div style="font-size:24px;font-weight:800;color:#2563eb;margin-top:6px;">{{ $totals->total_staff }} Staff</div>
                <div style="font-size:12px;color:#64748b;margin-top:4px;">{{ $totals->open_services }} active service tickets</div>
            </div>
        </div>

        {{-- Branch Comparison Matrix Table --}}
        <div style="background:#ffffff;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#0f172a;">Branch Performance Breakdown</h3>
            </div>

            <div class="table" style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;text-align:left;">
                    <thead>
                        <tr style="background:#f1f5f9;color:#475569;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;">
                            <th style="padding:12px 16px;">Branch Name & City</th>
                            <th style="padding:12px 16px;">Staff</th>
                            <th style="padding:12px 16px;">Orders</th>
                            <th style="padding:12px 16px;">Total Sales</th>
                            <th style="padding:12px 16px;">Paid</th>
                            <th style="padding:12px 16px;">Due Balance</th>
                            <th style="padding:12px 16px;">Leads Won</th>
                            <th style="padding:12px 16px;">Open Services</th>
                            <th style="padding:12px 16px;">Task Comp %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branches as $branch)
                            <tr style="border-bottom:1px solid #f1f5f9;font-size:13px;">
                                <td style="padding:14px 16px;">
                                    <div style="font-weight:700;color:#0f172a;">
                                        {{ $branch->name }}
                                        @if ($branch->is_head_office)
                                            <span style="background:#e0e7ff;color:#3730a3;font-size:10px;padding:2px 6px;border-radius:4px;margin-left:4px;">HQ</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11px;color:#64748b;">Code: {{ $branch->code }} • {{ $branch->city ?: 'N/A' }}</div>
                                </td>
                                <td style="padding:14px 16px;">
                                    <span style="font-weight:600;color:#334155;">{{ $branch->staff_count }}</span>
                                </td>
                                <td style="padding:14px 16px;">
                                    <span style="font-weight:600;color:#0f172a;">{{ $branch->total_orders }}</span>
                                </td>
                                <td style="padding:14px 16px;font-weight:700;color:#0f172a;">
                                    {{ core()->formatBasePrice($branch->total_revenue) }}
                                </td>
                                <td style="padding:14px 16px;font-weight:700;color:#16a34a;">
                                    {{ core()->formatBasePrice($branch->total_paid) }}
                                </td>
                                <td style="padding:14px 16px;font-weight:700;color:{{ $branch->total_due > 0 ? '#dc2626' : '#16a34a' }};">
                                    {{ core()->formatBasePrice($branch->total_due) }}
                                </td>
                                <td style="padding:14px 16px;">
                                    <span style="font-weight:600;color:#2563eb;">{{ $branch->lead_conv_rate }}%</span>
                                    <div style="font-size:11px;color:#64748b;">{{ $branch->total_leads }} leads</div>
                                </td>
                                <td style="padding:14px 16px;">
                                    <span style="padding:3px 8px;border-radius:6px;font-weight:700;font-size:12px;background:{{ $branch->open_service_reqs > 0 ? '#fef3c7' : '#f1f5f9' }};color:{{ $branch->open_service_reqs > 0 ? '#b45309' : '#64748b' }};">
                                        {{ $branch->open_service_reqs }} Open
                                    </span>
                                </td>
                                <td style="padding:14px 16px;">
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div style="flex:1;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;min-width:60px;">
                                            <div style="width:{{ $branch->task_comp_rate }}%;height:100%;background:#16a34a;"></div>
                                        </div>
                                        <span style="font-size:12px;font-weight:700;color:#334155;">{{ $branch->task_comp_rate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
