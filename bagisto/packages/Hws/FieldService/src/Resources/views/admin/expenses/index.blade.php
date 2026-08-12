@extends('hws::admin.layouts.menu')

@section('page_title')
    Expense Claims
@stop

@section('page-content')
<div style="padding: 0 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Expense Claims Review</h1>
        <p style="font-size: 14px; color: #64748b; margin: 0;">Employee travel, food, and emergency parts purchase expense claims.</p>
    </div>

    @if (session('success'))
        <div style="padding: 12px 20px; background: #d1fae5; color: #065f46; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters Section -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);">
        <form method="GET" action="{{ route('hws.admin.expenses.index') }}" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Category</label>
                <select name="category" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 13.5px; background: #fff; min-width: 140px;">
                    <option value="">All Categories</option>
                    <option value="travel" {{ request('category') === 'travel' ? 'selected' : '' }}>Travel</option>
                    <option value="food" {{ request('category') === 'food' ? 'selected' : '' }}>Food</option>
                    <option value="tools" {{ request('category') === 'tools' ? 'selected' : '' }}>Tools</option>
                    <option value="others" {{ request('category') === 'others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Status</label>
                <select name="status" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 13.5px; background: #fff; min-width: 140px;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: #3c50e0; color: #fff; border: 0; border-radius: 8px; padding: 7px 16px; font-size: 13.5px; font-weight: 600; cursor: pointer;">
                    Filter
                </button>
                <a href="{{ route('hws.admin.expenses.index') }}" style="background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 8px; padding: 7px 16px; font-size: 13.5px; font-weight: 600; text-align: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div style="border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Employee</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Category & Date</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Amount</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Description</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Receipt</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Status / Reviewer</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($claims as $claim)
                    <tr style="border-bottom: 1px solid #f8fafc;">
                        <td style="padding: 14px 20px;">
                            <span style="font-weight: 600; color: #334155;">{{ $claim->employee->name ?? 'Unknown' }}</span>
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="font-weight: 600; color: #475569;">{{ ucfirst($claim->category) }}</div>
                            <div style="font-size: 11.5px; color: #94a3b8; margin-top: 2px;">{{ $claim->created_at->format('M d, Y') }}</div>
                        </td>
                        <td style="padding: 14px 20px; font-weight: 700; color: #1e293b; font-size: 15px;">
                            ₹{{ number_format($claim->amount, 2) }}
                        </td>
                        <td style="padding: 14px 20px; color: #64748b; max-width: 250px;">
                            {{ $claim->description }}
                        </td>
                        <td style="padding: 14px 20px;">
                            @if ($claim->receipt_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($claim->receipt_path) }}" target="_blank" style="color: #3c50e0; font-weight: 600; text-decoration: none; font-size: 13px;">
                                    View Receipt ↗
                                </a>
                            @else
                                <span style="color: #94a3b8; font-size: 12.5px;">No Receipt</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="margin-bottom: 4px;">
                                <span style="display: inline-flex; border-radius: 100px; padding: 4px 10px; font-size: 11px; font-weight: 700;
                                    background: {{ $claim->status === 'approved' ? '#d1fae5' : ($claim->status === 'rejected' ? '#fee2e2' : '#f1f5f9') }};
                                    color: {{ $claim->status === 'approved' ? '#065f46' : ($claim->status === 'rejected' ? '#991b1b' : '#475569') }};">
                                    {{ ucfirst($claim->status) }}
                                </span>
                            </div>
                            @if ($claim->reviewer)
                                <span style="font-size: 11px; color: #94a3b8;">Reviewed by: {{ $claim->reviewer->name }}</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            @if ($claim->status === 'pending')
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <form method="POST" action="{{ route('hws.admin.expenses.status', $claim->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="approved"/>
                                        <button type="submit" style="background: #10b981; color: white; border: 0; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 12px;">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('hws.admin.expenses.status', $claim->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected"/>
                                        <button type="submit" style="background: #ef4444; color: white; border: 0; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 12px;">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="color: #94a3b8; font-size: 13px;">Locked</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
