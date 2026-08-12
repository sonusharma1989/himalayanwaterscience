@extends('hws::admin.layouts.menu')

@section('page_title')
    Field Service Employees
@stop

@section('page-content')
<div style="padding: 0 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Employees & Technicians</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0;">Technician profiles, status, and active tasks.</p>
        </div>
    </div>

    <div style="border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Technician</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Email</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Role</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Today's Check In</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Active Task</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                    <tr style="border-bottom: 1px solid #f8fafc;">
                        <td style="padding: 14px 20px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #eff6ff; color: #3c50e0; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">
                                    {{ strtoupper(substr($employee['name'], 0, 2)) }}
                                </div>
                                <span style="font-weight: 600; color: #334155;">{{ $employee['name'] }}</span>
                            </div>
                        </td>
                        <td style="padding: 14px 20px; color: #64748b;">{{ $employee['email'] }}</td>
                        <td style="padding: 14px 20px; color: #475569;">{{ $employee['role'] }}</td>
                        <td style="padding: 14px 20px;">
                            @if ($employee['checked_in'])
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; color: #059669;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                                    Checked In ({{ \Carbon\Carbon::parse($employee['check_in'])->format('h:i A') }})
                                </span>
                            @else
                                <span style="color: #94a3b8; font-size: 12.5px;">Not Checked In</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; color: #475569; font-weight: 500;">
                            {{ $employee['active_task'] }}
                        </td>
                        <td style="padding: 14px 20px;">
                            <span style="display: inline-flex; border-radius: 100px; padding: 4px 10px; font-size: 11px; font-weight: 700;
                                background: {{ $employee['status'] === 'Active' ? '#d1fae5' : '#fee2e2' }};
                                color: {{ $employee['status'] === 'Active' ? '#065f46' : '#991b1b' }};">
                                {{ $employee['status'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
