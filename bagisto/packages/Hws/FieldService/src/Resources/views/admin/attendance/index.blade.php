@extends('hws::admin.layouts.menu')

@section('page_title')
    Attendance Logs
@stop

@section('page-content')
<div style="padding: 0 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Attendance Log History</h1>
        <p style="font-size: 14px; color: #64748b; margin: 0;">Full check-in and check-out logs, geolocations (click to view on maps), and check-in selfies.</p>
    </div>

    <div style="border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Employee</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Date</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Check In Details</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Check Out Details</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Selfie</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceRecords as $record)
                    <tr style="border-bottom: 1px solid #f8fafc;">
                        <td style="padding: 14px 20px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: #eff6ff; color: #3c50e0; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">
                                    {{ $record->employee ? strtoupper(substr($record->employee->name, 0, 2)) : '—' }}
                                </div>
                                <span style="font-weight: 600; color: #334155;">{{ $record->employee->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td style="padding: 14px 20px; color: #334155; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="font-weight: 600; color: #0f172a;">
                                {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '—' }}
                            </div>
                            @if ($record->check_in_lat && $record->check_in_lng)
                                <div style="font-size: 11px; margin-top: 4px;">
                                    <a href="https://www.google.com/maps?q={{ $record->check_in_lat }},{{ $record->check_in_lng }}" target="_blank" style="color: #3c50e0; text-decoration: none; font-weight: 600;">
                                        📍 View Map Location ({{ round($record->check_in_lat, 4) }}, {{ round($record->check_in_lng, 4) }})
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="font-weight: 600; color: #475569;">
                                {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : 'Active / On-Site' }}
                            </div>
                            @if ($record->check_out_lat && $record->check_out_lng)
                                <div style="font-size: 11px; margin-top: 4px;">
                                    <a href="https://www.google.com/maps?q={{ $record->check_out_lat }},{{ $record->check_out_lng }}" target="_blank" style="color: #3c50e0; text-decoration: none; font-weight: 600;">
                                        📍 View Map Location ({{ round($record->check_out_lat, 4) }}, {{ round($record->check_out_lng, 4) }})
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            @if ($record->check_in_selfie_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($record->check_in_selfie_path) }}" target="_blank">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($record->check_in_selfie_path) }}" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;"/>
                                </a>
                            @else
                                <span style="color: #94a3b8; font-size: 12px;">No Selfie</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
