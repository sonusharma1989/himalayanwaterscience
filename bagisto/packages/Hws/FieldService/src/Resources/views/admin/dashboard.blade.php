@extends('hws::admin.layouts.menu')

@section('page_title')
    Field Service Dashboard
@stop

@section('page-content')
<div style="padding:0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">

    <div style="margin-bottom:24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size:24px;font-weight:700;letter-spacing:-0.01em;color:#1e293b;margin:0 0 4px;">Field Service Dashboard</h1>
            <p style="font-size:14px;color:#64748b;margin:0;">Live team status, jobs and sales at a glance.</p>
        </div>
        <div>
            <a href="{{ route('hws.admin.service-requests.index') }}" style="background: #3c50e0; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px;">
                + New Service Request
            </a>
        </div>
    </div>

    {{-- ── KPI cards ─────────────────────────────────────────── --}}
    <div style="display:flex;flex-wrap:wrap;gap:14px;margin-bottom:24px;">

        <a href="{{ route('hws.admin.employees.index') }}" style="flex:1;min-width:150px;text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:16px;padding:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);transition:transform 0.2s;display:block;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:14px;">👥</div>
            <p style="font-size:20px;font-weight:700;color:#1e293b;margin:0;">{{ $employeesOnline }}<span style="font-size:14px;font-weight:700;color:#94a3b8;">/{{ $totalEmployees }}</span></p>
            <p style="font-size:11px;font-weight:600;color:#94a3b8;margin:2px 0 0;">Employees online</p>
        </a>

        <a href="{{ route('hws.admin.service-requests.index') }}" style="flex:1;min-width:150px;text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:16px;padding:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);transition:transform 0.2s;display:block;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width:32px;height:32px;border-radius:8px;background:#fffbeb;display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:14px;">⏱</div>
            <p style="font-size:20px;font-weight:700;color:#1e293b;margin:0;">{{ $pendingJobs }}</p>
            <p style="font-size:11px;font-weight:600;color:#94a3b8;margin:2px 0 0;">Pending jobs</p>
        </a>

        <a href="{{ route('hws.admin.service-requests.index') }}" style="flex:1;min-width:150px;text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:16px;padding:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);transition:transform 0.2s;display:block;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width:32px;height:32px;border-radius:8px;background:#ecfdf5;display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:14px;">✓</div>
            <p style="font-size:20px;font-weight:700;color:#1e293b;margin:0;">{{ $completedToday }}</p>
            <p style="font-size:11px;font-weight:600;color:#94a3b8;margin:2px 0 0;">Completed today</p>
        </a>

        <a href="{{ route('hws.admin.sales-leads.index') }}" style="flex:1;min-width:150px;text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:16px;padding:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);transition:transform 0.2s;display:block;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:14px;">📈</div>
            <p style="font-size:20px;font-weight:700;color:#1e293b;margin:0;">₹{{ number_format($salesThisMonth / 100000, 1) }}L</p>
            <p style="font-size:11px;font-weight:600;color:#94a3b8;margin:2px 0 0;">Sales this month</p>
        </a>

        <a href="{{ route('hws.admin.service-requests.index') }}" style="flex:1;min-width:150px;text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:16px;padding:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);transition:transform 0.2s;display:block;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width:32px;height:32px;border-radius:8px;background:#fff1f2;display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:14px;">⚠</div>
            <p style="font-size:20px;font-weight:700;color:#1e293b;margin:0;">{{ $openRequests }}</p>
            <p style="font-size:11px;font-weight:600;color:#94a3b8;margin:2px 0 0;">Open requests</p>
        </a>

        <a href="{{ route('hws.admin.service-requests.index') }}" style="flex:1;min-width:150px;text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:16px;padding:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);transition:transform 0.2s;display:block;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width:32px;height:32px;border-radius:8px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:14px;">📅</div>
            <p style="font-size:20px;font-weight:700;color:#1e293b;margin:0;">{{ $amcRenewalsDue }}</p>
            <p style="font-size:11px;font-weight:600;color:#94a3b8;margin:2px 0 0;">AMC renewals due</p>
        </a>

    </div>

    {{-- ── Chart + attendance donut ──────────────────────────── --}}
    <div style="display:flex;flex-wrap:wrap;gap:20px;margin-bottom:20px;">

        <div style="flex:2;min-width:320px;border:1px solid #e2e8f0;background:#fff;border-radius:16px;padding:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
            <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 16px;">Jobs completed — this week</p>
            @php $maxCount = collect($weekChart)->max('count') ?: 1; @endphp
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px;height:160px;border-top:1px solid #f1f5f9;padding-top:16px;">
                @foreach ($weekChart as $day)
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;height:100%;justify-content:flex-end;">
                        <div style="width:100%;border-radius:6px 6px 0 0;background:#3c50e0;height:{{ $day['count'] > 0 ? max(($day['count'] / $maxCount) * 100, 6) : 2 }}%;" title="{{ $day['label'] }}: {{ $day['count'] }} completed"></div>
                        <span style="font-size:9px;font-weight:500;color:#94a3b8;">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <a href="{{ route('hws.admin.attendance.index') }}" style="flex:1;min-width:220px;text-decoration:none;border:1px solid #e2e8f0;background:#fff;border-radius:16px;padding:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);display:flex;flex-direction:column;transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <p style="font-size:14px;font-weight:700;color:#334155;margin:0 0 8px;">Attendance today</p>
            <div style="flex:1;display:flex;align-items:center;justify-content:center;">
                <div style="position:relative;width:128px;height:128px;">
                    <svg viewBox="0 0 120 120" width="128" height="128" style="transform:rotate(-90deg);">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#E2E8F0" stroke-width="12"/>
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#3c50e0" stroke-width="12"
                                stroke-linecap="round"
                                stroke-dasharray="314"
                                stroke-dashoffset="{{ 314 - (314 * $attendancePct / 100) }}"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <p style="font-size:22px;font-weight:700;color:#1e293b;margin:0;">{{ $attendancePct }}%</p>
                        <p style="font-size:10px;font-weight:600;color:#94a3b8;margin:0;">{{ $attendedToday }} of {{ $totalEmployees }}</p>
                    </div>
                </div>
            </div>
        </a>

    </div>

    {{-- ── Live employee status ──────────────────────────────── --}}
    <div style="border:1px solid #e2e8f0;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding:16px 20px;">
            <p style="font-size:14px;font-weight:700;color:#334155;margin:0;">Live employee status</p>
            <a href="{{ route('hws.admin.attendance.index') }}" style="font-size:12px;font-weight:600;color:#3c50e0;text-decoration:none;">View all</a>
        </div>

        @if ($liveStatus->isEmpty())
            <p style="padding:32px 20px;text-align:center;font-size:14px;color:#94a3b8;">No attendance recorded yet today.</p>
        @else
            <table style="width:100%;font-size:14px;border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left;">
                        <th style="padding:10px 20px;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #f1f5f9;">Employee</th>
                        <th style="padding:10px 20px;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #f1f5f9;">Status</th>
                        <th style="padding:10px 20px;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #f1f5f9;">Current task</th>
                        <th style="padding:10px 20px;font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #f1f5f9;">Since</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($liveStatus as $status)
                        <tr style="border-bottom:1px solid #f8fafc;">
                            <td style="padding:12px 20px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:28px;height:28px;border-radius:50%;background:#eff6ff;color:#3c50e0;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;">
                                        {{ $status['employee'] ? strtoupper(substr($status['employee']->name, 0, 2)) : '—' }}
                                    </div>
                                    <span style="font-weight:600;color:#334155;">{{ $status['employee']->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td style="padding:12px 20px;">
                                <span style="display:inline-flex;border-radius:100px;padding:4px 10px;font-size:10.5px;font-weight:700;
                                    background:{{ $status['checked_in'] ? '#d1fae5' : '#f1f5f9' }};
                                    color:{{ $status['checked_in'] ? '#047857' : '#64748b' }};">
                                    {{ $status['checked_in'] ? 'On site' : 'Checked out' }}
                                </span>
                            </td>
                            <td style="padding:12px 20px;color:#64748b;">
                                @if ($status['current_task'])
                                    {{ $status['current_task']->task_no }} · {{ ucfirst(str_replace('_', ' ', $status['current_task']->type)) }}
                                @else
                                    No active job
                                @endif
                            </td>
                            <td style="padding:12px 20px;font-size:12px;color:#94a3b8;">
                                {{ $status['check_in'] ? \Illuminate\Support\Carbon::parse($status['check_in'])->diffForHumans(null, true) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@stop
