@extends('hws::admin.layouts.menu')

@section('page_title')
    Field Service Reports & Performance
@stop

@section('page-content')
<div style="padding: 0 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Analytical Reports</h1>
        <p style="font-size: 14px; color: #64748b; margin: 0;">Comprehensive data summaries of work completions, sales closed, and business expenses.</p>
    </div>

    <!-- Numerical Aggregates Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 28px;">
        <div style="border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <p style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0 0 8px;">Total Revenue</p>
            <p style="font-size: 24px; font-weight: 800; color: #10b981; margin: 0;">₹{{ number_format($totalSales, 2) }}</p>
        </div>

        <div style="border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <p style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0 0 8px;">Approved Expenses</p>
            <p style="font-size: 24px; font-weight: 800; color: #ef4444; margin: 0;">₹{{ number_format($approvedExpenses, 2) }}</p>
        </div>

        <div style="border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <p style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0 0 8px;">Pending Expense Claims</p>
            <p style="font-size: 24px; font-weight: 800; color: #f59e0b; margin: 0;">₹{{ number_format($pendingExpenses, 2) }}</p>
        </div>

        <div style="border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <p style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0 0 8px;">Total Attendance Logs</p>
            <p style="font-size: 24px; font-weight: 800; color: #3c50e0; margin: 0;">{{ $totalAttendanceLogs }} logs</p>
        </div>
    </div>

    <!-- Data Tables Flex Grid -->
    <div style="display: flex; flex-wrap: wrap; gap: 24px;">
        <!-- Left: Jobs Completed by Type -->
        <div style="flex: 1; min-width: 320px; border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <h3 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0;">Job Types Breakdown</h3>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 12px 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Job Type</th>
                        <th style="padding: 12px 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8; text-align: right;">Total Jobs</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasksByType as $task)
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td style="padding: 12px 20px; font-weight: 600; color: #475569;">
                                {{ ucfirst(str_replace('_', ' ', $task->type)) }}
                            </td>
                            <td style="padding: 12px 20px; font-weight: 700; color: #1e293b; text-align: right;">
                                {{ $task->total }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Right: Task Status Progression -->
        <div style="flex: 1; min-width: 320px; border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <h3 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0;">Step Status Breakdown</h3>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 12px 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Workflow Step</th>
                        <th style="padding: 12px 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8; text-align: right;">Job Count</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabels = [
                            0 => 'Scheduled',
                            1 => 'Started / Travel',
                            2 => 'Job Commenced',
                            3 => 'Sign-off Pending',
                            4 => 'Completed'
                        ];
                    @endphp
                    @foreach ($tasksByStep as $task)
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td style="padding: 12px 20px; font-weight: 600; color: #475569;">
                                {{ $statusLabels[$task->step] ?? 'Unknown' }} (Step {{ $task->step }})
                            </td>
                            <td style="padding: 12px 20px; font-weight: 700; color: #1e293b; text-align: right;">
                                {{ $task->total }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
