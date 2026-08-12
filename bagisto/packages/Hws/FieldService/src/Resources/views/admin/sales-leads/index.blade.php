@extends('hws::admin.layouts.menu')

@section('page_title')
    Sales Visits & Leads
@stop

@section('page-content')
<div style="padding: 0 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    @if (session('success'))
        <div style="padding: 12px 20px; background: #d1fae5; color: #065f46; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Sales Visits & Leads</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0;">Sales team visits, leads, and detailed site surveys.</p>
        </div>
        <div>
            <button onclick="document.getElementById('newLeadForm').style.display='block'; this.style.display='none';" id="addLeadBtn" style="background: #3c50e0; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; border: 0; cursor: pointer; font-size: 14px;">
                + New Lead
            </button>
        </div>
    </div>

    <!-- Create Lead Form -->
    <div id="newLeadForm" style="display: none; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin: 0;">Create Sales Lead</h3>
            <button onclick="document.getElementById('newLeadForm').style.display='none'; document.getElementById('addLeadBtn').style.display='block';" style="background: transparent; border: 0; color: #64748b; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
        </div>

        <form method="POST" action="{{ route('hws.admin.sales-leads.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer Name</label>
                    <input type="text" name="customer_name" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer Phone</label>
                    <input type="text" name="customer_phone" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Lead Type</label>
                    <select name="type" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                        <option value="sales_visit">Sales Visit</option>
                        <option value="site_survey">Site Survey</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Priority</label>
                    <select name="priority" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                        <option value="high">High</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Assign Executive</label>
                    <select name="assigned_to" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                        <option value="">Unassigned</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer Address</label>
                <textarea name="customer_address" required rows="2" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; resize: vertical;"></textarea>
            </div>
            <div>
                <button type="submit" style="background: #3c50e0; color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; border: 0; cursor: pointer; font-size: 14px;">Save Lead</button>
            </div>
        </form>
    </div>

    <!-- Filters Section -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);">
        <form method="GET" action="{{ route('hws.admin.sales-leads.index') }}" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Lead Type</label>
                <select name="type" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 13.5px; background: #fff; min-width: 140px;">
                    <option value="">All Types</option>
                    <option value="sales_visit" {{ request('type') === 'sales_visit' ? 'selected' : '' }}>Sales Visit</option>
                    <option value="site_survey" {{ request('type') === 'site_survey' ? 'selected' : '' }}>Site Survey</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Step Status</label>
                <select name="step" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 13.5px; background: #fff; min-width: 140px;">
                    <option value="">All Statuses</option>
                    <option value="0" {{ request('step') === '0' ? 'selected' : '' }}>Scheduled</option>
                    <option value="1" {{ request('step') === '1' ? 'selected' : '' }}>Started / Travel</option>
                    <option value="2" {{ request('step') === '2' ? 'selected' : '' }}>Job Commenced</option>
                    <option value="3" {{ request('step') === '3' ? 'selected' : '' }}>Sign-off Pending</option>
                    <option value="4" {{ request('step') === '4' ? 'selected' : '' }}>Completed (Closed)</option>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: #3c50e0; color: #fff; border: 0; border-radius: 8px; padding: 7px 16px; font-size: 13.5px; font-weight: 600; cursor: pointer;">
                    Filter
                </button>
                <a href="{{ route('hws.admin.sales-leads.index') }}" style="background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 8px; padding: 7px 16px; font-size: 13.5px; font-weight: 600; text-align: center;">
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
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Date</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Lead Code</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Client</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Type</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Assigned Executive</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Site Survey Status</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Status / Closed Amount</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leads as $lead)
                    <tr style="border-bottom: 1px solid #f8fafc;">
                        <td style="padding: 14px 20px; color: #334155; font-weight: 500; white-space: nowrap;">
                            {{ $lead->created_at ? $lead->created_at->format('M d, Y') : '—' }}
                        </td>
                        <td style="padding: 14px 20px; font-weight: 700; color: #1e293b;">
                            #{{ $lead->task_no }}
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="font-weight: 600; color: #334155;">{{ $lead->customer_name }}</div>
                            <div style="font-size: 12px; color: #64748b;">📞 {{ $lead->customer_phone }}</div>
                        </td>
                        <td style="padding: 14px 20px;">
                            <span style="display: inline-flex; border-radius: 6px; padding: 4px 8px; font-size: 11px; font-weight: 700;
                                background: #eff6ff; color: #1e40af;">
                                {{ ucfirst(str_replace('_', ' ', $lead->type)) }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px; color: #334155; font-weight: 500;">
                            {{ $lead->assignee->name ?? 'Unassigned' }}
                        </td>
                        <td style="padding: 14px 20px;">
                            @if ($lead->survey)
                                <span style="color: #059669; font-weight: 600;">✓ Completed</span>
                            @else
                                <span style="color: #94a3b8;">Pending</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            @if ($lead->step === 4)
                                <div style="color: #059669; font-weight: 700; font-size: 14px;">
                                    Closed (₹{{ number_format($lead->sale_amount, 2) }})
                                </div>
                            @else
                                <span style="display: inline-flex; border-radius: 100px; padding: 4px 10px; font-size: 11px; font-weight: 700;
                                    background: #fffbeb; color: #854d0e;">
                                    In Progress (Step {{ $lead->step }}/4)
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                <button onclick="document.getElementById('editLead-{{ $lead->id }}').style.display='block'" style="background: transparent; border: 0; color: #3c50e0; font-weight: 600; cursor: pointer; font-size: 13px;">
                                    Edit
                                </button>
                                <span>·</span>
                                <button onclick="document.getElementById('viewLead-{{ $lead->id }}').style.display='block'" style="background: transparent; border: 0; color: #475569; font-weight: 600; cursor: pointer; font-size: 13px;">
                                    Details
                                </button>
                            </div>

                            <!-- Edit Lead Modal -->
                            <div id="editLead-{{ $lead->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 9999; align-items: center; justify-content: center;">
                                <div style="background: #fff; border-radius: 16px; padding: 24px; width: 400px; margin: 10% auto; text-align: left; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                        <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin: 0;">Edit Lead #{{ $lead->task_no }}</h3>
                                        <button onclick="document.getElementById('editLead-{{ $lead->id }}').style.display='none'" style="background: transparent; border: 0; color: #64748b; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
                                    </div>
                                    <form method="POST" action="{{ route('hws.admin.sales-leads.update', $lead->id) }}">
                                        @csrf
                                        <div style="margin-bottom: 16px;">
                                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Assignee</label>
                                            <select name="assigned_to" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                                <option value="">Unassigned</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ $lead->assigned_to == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div style="margin-bottom: 16px;">
                                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Priority</label>
                                            <select name="priority" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                                <option value="normal" {{ $lead->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="urgent" {{ $lead->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                                <option value="high" {{ $lead->priority === 'high' ? 'selected' : '' }}>High</option>
                                                <option value="low" {{ $lead->priority === 'low' ? 'selected' : '' }}>Low</option>
                                            </select>
                                        </div>
                                        <div style="margin-bottom: 16px;">
                                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Workflow Step</label>
                                            <select name="step" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                                <option value="0" {{ $lead->step === 0 ? 'selected' : '' }}>0 · Scheduled</option>
                                                <option value="1" {{ $lead->step === 1 ? 'selected' : '' }}>1 · Started / Travel</option>
                                                <option value="2" {{ $lead->step === 2 ? 'selected' : '' }}>2 · Job Commenced</option>
                                                <option value="3" {{ $lead->step === 3 ? 'selected' : '' }}>3 · Sign-off Pending</option>
                                                <option value="4" {{ $lead->step === 4 ? 'selected' : '' }}>4 · Completed (Closed)</option>
                                            </select>
                                        </div>
                                        <div style="margin-bottom: 24px;">
                                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Sale Amount (₹)</label>
                                            <input type="number" step="0.01" name="sale_amount" value="{{ $lead->sale_amount }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                                        </div>
                                        <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                            <button type="button" onclick="document.getElementById('editLead-{{ $lead->id }}').style.display='none'" style="background: #f1f5f9; color: #475569; border: 0; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">Cancel</button>
                                            <button type="submit" style="background: #3c50e0; color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- View Lead/Survey Details Modal -->
                            <div id="viewLead-{{ $lead->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 9999; align-items: center; justify-content: center; overflow-y: auto;">
                                <div style="background: #fff; border-radius: 16px; padding: 28px; width: 650px; max-width: 95%; margin: 5% auto; text-align: left; box-shadow: 0 10px 25px -3px rgba(0,0,0,0.1);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                                        <div>
                                            <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                {{ ucfirst(str_replace('_', ' ', $lead->type)) }}
                                            </span>
                                            <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 6px 0 0;">Lead Details #{{ $lead->task_no }}</h3>
                                        </div>
                                        <button onclick="document.getElementById('viewLead-{{ $lead->id }}').style.display='none'" style="background: transparent; border: 0; color: #64748b; font-size: 22px; cursor: pointer; font-weight: 700;">×</button>
                                    </div>

                                    @if ($lead->survey)
                                        @php $survey = $lead->survey; @endphp
                                        
                                        <!-- Property Information -->
                                        <div style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">PROPERTY TYPE</h4>
                                                <span style="background: #3c50e0; color: white; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 700; display: inline-block;">
                                                    {{ strtoupper($survey->property_type) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">BUSINESS NAME</h4>
                                                <p style="font-weight: 600; color: #334155; margin: 0; font-size: 14px;">{{ $lead->customer_name }}</p>
                                            </div>
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">CONTACT NUMBER</h4>
                                                <p style="font-weight: 600; color: #334155; margin: 0; font-size: 14px;">{{ $lead->customer_phone }}</p>
                                            </div>
                                        </div>

                                        <div style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">ROOMS / BEDS / UNITS</h4>
                                                <p style="font-weight: 600; color: #334155; margin: 0; font-size: 14px;">{{ $survey->rooms_units ?? '—' }} units</p>
                                            </div>
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">EST. WATER USE (KLD)</h4>
                                                <p style="font-weight: 600; color: #334155; margin: 0; font-size: 14px;">{{ $survey->water_use_kld ?? '—' }} KLD</p>
                                            </div>
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">FLOORS</h4>
                                                <p style="font-weight: 600; color: #334155; margin: 0; font-size: 14px;">{{ $survey->floors ?? '—' }} Floors</p>
                                            </div>
                                        </div>

                                        <!-- Water Source / Disposal -->
                                        <div style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; background: #f8fafc; border-radius: 12px; padding: 16px;">
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 6px; font-weight: 700;">CURRENT WATER SOURCE</h4>
                                                <span style="background: #e2e8f0; color: #334155; border-radius: 100px; padding: 4px 12px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                    {{ ucfirst($survey->water_source ?? 'N/A') }}
                                                </span>
                                            </div>
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 6px; font-weight: 700;">CURRENT WASTEWATER DISPOSAL</h4>
                                                <span style="background: #e2e8f0; color: #334155; border-radius: 100px; padding: 4px 12px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                    {{ ucfirst(str_replace('_', ' ', $survey->wastewater_disposal ?? 'N/A')) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Inquiry Types & Space Available -->
                                        <div style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 6px; font-weight: 700;">INQUIRY TYPE</h4>
                                                @if($survey->inquiryTypes->isEmpty())
                                                    <p style="font-size: 13px; color: #94a3b8; margin: 0;">None specified</p>
                                                @else
                                                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                                        @foreach ($survey->inquiryTypes as $inq)
                                                            <span style="background: #e0f2fe; color: #0369a1; border-radius: 6px; padding: 4px 8px; font-size: 11px; font-weight: 700;">
                                                                {{ strtoupper($inq->inquiry_type) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 6px; font-weight: 700;">SPACE AVAILABLE FOR PLANT</h4>
                                                <span style="background: #d1fae5; color: #065f46; border-radius: 100px; padding: 4px 12px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                    {{ ucfirst(str_replace('_', ' ', $survey->space_available ?? 'N/A')) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Notes & Follow-up Date -->
                                        <div style="margin-bottom: 20px; background: #fffbeb; border-radius: 12px; padding: 16px; border: 1px solid #fef3c7;">
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #b45309; margin: 0 0 6px; font-weight: 700;">NOTES FOR SALES TEAM</h4>
                                            <p style="font-size: 13px; color: #78350f; line-height: 1.5; margin: 0;">{{ $survey->notes ?? 'No notes submitted.' }}</p>
                                        </div>

                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                            <div>
                                                <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">FOLLOW-UP DATE</h4>
                                                <p style="font-weight: 600; color: #334155; margin: 0; font-size: 14px;">
                                                    📅 {{ $survey->follow_up_date ? $survey->follow_up_date->format('M d, Y') : '—' }}
                                                </p>
                                            </div>
                                            @if ($survey->latitude && $survey->longitude)
                                                <div>
                                                    <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">GEOLOCATION</h4>
                                                    <a href="https://www.google.com/maps?q={{ $survey->latitude }},{{ $survey->longitude }}" target="_blank" style="color: #3c50e0; text-decoration: none; font-weight: 600; font-size: 13.5px;">
                                                        📍 View on Google Maps ({{ round($survey->latitude, 4) }}, {{ round($survey->longitude, 4) }})
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <!-- No survey data yet -->
                                        <div style="padding: 30px; text-align: center; color: #94a3b8;">
                                            <p style="margin: 0; font-size: 14px;">This lead doesn't have an associated site survey record yet.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
