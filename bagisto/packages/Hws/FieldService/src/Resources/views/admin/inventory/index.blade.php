@extends('hws::admin.layouts.menu')

@section('page_title')
    Material Inventory & Consumption
@stop

@section('page-content')
<div style="padding: 0 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Material & Inventory Consumption</h1>
        <p style="font-size: 14px; color: #64748b; margin: 0;">Logs of items and materials consumed across client task check-outs.</p>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 24px;">
        <!-- Left: Summary Grid -->
        <div style="flex: 1; min-width: 300px; border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <h2 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0 0 16px;">Consolidated Summary</h2>
            @if ($aggregatedInventory->isEmpty())
                <p style="text-align: center; color: #94a3b8; padding: 20px 0; font-size: 14px;">No materials consumed yet.</p>
            @else
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
                    @foreach ($aggregatedInventory as $item)
                        <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 8px;">
                            <span style="font-weight: 600; color: #475569;">{{ $item->name }}</span>
                            <span style="background: #eff6ff; color: #3c50e0; font-weight: 700; font-size: 12px; padding: 4px 10px; border-radius: 100px;">
                                {{ $item->total_consumed }} units
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Right: Dispatch Log -->
        <div style="flex: 2; min-width: 450px; border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <h2 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0;">Detailed Consumption Log</h2>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 12px 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Material</th>
                        <th style="padding: 12px 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Qty</th>
                        <th style="padding: 12px 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Reference Task</th>
                        <th style="padding: 12px 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usageLogs as $log)
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td style="padding: 12px 20px; font-weight: 600; color: #334155;">{{ $log->name }}</td>
                            <td style="padding: 12px 20px; color: #475569; font-weight: 500;">{{ $log->quantity }}</td>
                            <td style="padding: 12px 20px;">
                                @if ($log->task)
                                    <a href="#" style="color: #3c50e0; font-weight: 600; text-decoration: none;">
                                        #{{ $log->task->task_no }} ({{ $log->task->customer_name }})
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="padding: 12px 20px; font-size: 12px; color: #94a3b8;">
                                {{ $log->created_at ? $log->created_at->format('M d, Y') : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
