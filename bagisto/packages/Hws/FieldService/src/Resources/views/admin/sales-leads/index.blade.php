@extends('hws::admin.layouts.menu')

@section('page_title')
    Sales CRM & Leads
@stop

@section('page-content')
<div style="padding: 0 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    @if (session('success'))
        <div style="padding: 12px 20px; background: #d1fae5; color: #065f46; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="padding: 12px 20px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
            {{ $errors->first() }}
        </div>
    @endif

    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Sales CRM & Leads</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0;">Manage your sales pipeline, log customer activities, send quotations, and convert won deals to tasks.</p>
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
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer / Business Name</label>
                    <input type="text" name="customer_name" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer Phone</label>
                    <input type="text" name="customer_phone" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Property Type</label>
                    <select name="property_type" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                        <option value="hotel">Hotel</option>
                        <option value="hospital">Hospital</option>
                        <option value="bungalow">Bungalow</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer Email</label>
                    <input type="email" name="customer_email" placeholder="client@example.com" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Temperature</label>
                    <select name="temperature" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                        <option value="warm">Warm</option>
                        <option value="hot">Hot</option>
                        <option value="cold">Cold</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Assign Agent</label>
                    <select name="assigned_to" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                        <option value="">Unassigned</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Lead Source</label>
                    <select name="source" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                        <option value="Field Survey">Field Survey</option>
                        <option value="Website">Website</option>
                        <option value="Reference">Reference</option>
                        <option value="Cold Call">Cold Call</option>
                        <option value="Social Media">Social Media</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Next Follow-up</label>
                    <input type="datetime-local" name="next_follow_up_at" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;"/>
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Address</label>
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
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Pipeline Stage</label>
                <select name="status" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 13.5px; background: #fff; min-width: 140px;">
                    <option value="">All Stages</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="proposal_sent" {{ request('status') === 'proposal_sent' ? 'selected' : '' }}>Proposal Sent</option>
                    <option value="negotiation" {{ request('status') === 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                    <option value="won" {{ request('status') === 'won' ? 'selected' : '' }}>Won (Closed)</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Temperature</label>
                <select name="temperature" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 13.5px; background: #fff; min-width: 140px;">
                    <option value="">All Temperatures</option>
                    <option value="hot" {{ request('temperature') === 'hot' ? 'selected' : '' }}>Hot</option>
                    <option value="warm" {{ request('temperature') === 'warm' ? 'selected' : '' }}>Warm</option>
                    <option value="cold" {{ request('temperature') === 'cold' ? 'selected' : '' }}>Cold</option>
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
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Created Date</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Lead ID</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Customer &amp; Phone</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Type</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Assigned Agent</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Temp</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Pipeline Status</th>
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
                            @if ($lead->reference_no)
                                {{ $lead->reference_no }}
                            @elseif ($lead->task)
                                #{{ $lead->task->task_no }}
                            @else
                                #SRV-{{ $lead->id }}
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="font-weight: 600; color: #334155;">{{ $lead->customer_name }}</div>
                            <div style="font-size: 12px; color: #64748b;">📞 {{ $lead->customer_phone }}</div>
                        </td>
                        <td style="padding: 14px 20px;">
                            <span style="display: inline-flex; border-radius: 6px; padding: 4px 8px; font-size: 11px; font-weight: 700;
                                background: #eff6ff; color: #1e40af;">
                                {{ $lead->request_type ? ucwords(str_replace('_', ' ', $lead->request_type)) : ucfirst($lead->property_type) }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px; color: #334155; font-weight: 500;">
                            {{ $lead->assigned_to ? $employees->firstWhere('id', $lead->assigned_to)->name : 'Unassigned' }}
                        </td>
                        <td style="padding: 14px 20px; font-weight: 700;">
                            @if ($lead->temperature === 'hot')
                                <span style="color: #ef4444;">🔥 HOT</span>
                            @elseif ($lead->temperature === 'warm')
                                <span style="color: #f59e0b;">⚡ WARM</span>
                            @else
                                <span style="color: #3b82f6;">❄ COLD</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            <span style="display: inline-flex; border-radius: 100px; padding: 4px 10px; font-size: 11px; font-weight: 700;
                                background: {{ $lead->status === 'won' ? '#d1fae5' : ($lead->status === 'lost' ? '#fee2fee' : '#fffbeb') }};
                                color: {{ $lead->status === 'won' ? '#065f46' : ($lead->status === 'lost' ? '#991b1b' : '#854d0e') }};">
                                {{ strtoupper(str_replace('_', ' ', $lead->status)) }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                @if ($lead->status !== 'won')
                                    <button onclick="document.getElementById('editLead-{{ $lead->id }}').style.display='flex'" style="background: transparent; border: 0; color: #3c50e0; font-weight: 600; cursor: pointer; font-size: 13px;">
                                        Status
                                    </button>
                                    <span>·</span>
                                @endif
                                <button onclick="document.getElementById('viewLead-{{ $lead->id }}').style.display='flex'" style="background: transparent; border: 0; color: #475569; font-weight: 600; cursor: pointer; font-size: 13px;">
                                    View Timeline
                                </button>
                            </div>

                            <!-- Edit Lead Modal -->
                            <div id="editLead-{{ $lead->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 9999; align-items: center; justify-content: center; overflow: hidden;">
                                <div style="background: #fff; border-radius: 16px; padding: 24px; width: 550px; text-align: left; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); max-height: 85vh; overflow-y: auto;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                        <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin: 0;">Update Lead @if($lead->task)#{{ $lead->task->task_no }}@else#SRV-{{ $lead->id }}@endif</h3>
                                        <button onclick="document.getElementById('editLead-{{ $lead->id }}').style.display='none'" style="background: transparent; border: 0; color: #64748b; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
                                    </div>
                                    <form onsubmit="submitEditLeadForm(event, this, '{{ $lead->id }}')" method="POST" action="{{ route('hws.admin.sales-leads.update', $lead->id) }}">
                                        @csrf
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                            <div>
                                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">Customer Name</label>
                                                <input type="text" name="customer_name" value="{{ $lead->customer_name }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 13px;"/>
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">Customer Phone</label>
                                                <input type="text" name="customer_phone" value="{{ $lead->customer_phone }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 13px;"/>
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">Customer Email</label>
                                                <input type="email" name="customer_email" value="{{ $lead->customer_email }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 13px;"/>
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">Lead Source</label>
                                                <select name="source" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 13px; background: #fff;">
                                                    <option value="Field Survey" {{ $lead->source === 'Field Survey' ? 'selected' : '' }}>Field Survey</option>
                                                    <option value="Website" {{ $lead->source === 'Website' ? 'selected' : '' }}>Website</option>
                                                    <option value="Website Checkout" {{ $lead->source === 'Website Checkout' ? 'selected' : '' }}>Website Checkout</option>
                                                    <option value="Reference" {{ $lead->source === 'Reference' ? 'selected' : '' }}>Reference</option>
                                                    <option value="Cold Call" {{ $lead->source === 'Cold Call' ? 'selected' : '' }}>Cold Call</option>
                                                    <option value="Social Media" {{ $lead->source === 'Social Media' ? 'selected' : '' }}>Social Media</option>
                                                </select>
                                            </div>
                                            <div style="grid-column: span 2;">
                                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">Customer Address</label>
                                                <textarea name="customer_address" required rows="2" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 13px; resize: vertical;">{{ $lead->customer_address }}</textarea>
                                            </div>
                                        </div>
                                        <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                            <button type="button" onclick="document.getElementById('editLead-{{ $lead->id }}').style.display='none'" style="background: #f1f5f9; color: #475569; border: 0; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">Cancel</button>
                                            <button type="submit" style="background: #3c50e0; color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- View Lead/Survey Details Modal -->
                            <div id="viewLead-{{ $lead->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 9999; align-items: center; justify-content: center; overflow: hidden;">
                                <div style="background: #fff; border-radius: 16px; padding: 28px; width: 680px; max-width: 95%; margin: 3% auto; text-align: left; box-shadow: 0 10px 25px -3px rgba(0,0,0,0.1); max-height: 85vh; overflow-y: auto;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                                        <div>
                                            <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                {{ $lead->request_type ? ucwords(str_replace('_', ' ', $lead->request_type)) : ucfirst($lead->property_type) }} Lead
                                            </span>
                                            <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 6px 0 0;">CRM Timeline &amp; Actions @if($lead->task)#{{ $lead->task->task_no }}@else#SRV-{{ $lead->id }}@endif</h3>
                                        </div>
                                        <button onclick="document.getElementById('viewLead-{{ $lead->id }}').style.display='none'" style="background: transparent; border: 0; color: #64748b; font-size: 22px; cursor: pointer; font-weight: 700;">×</button>
                                    </div>

                                    <!-- Action Workflow Buttons -->
                                    <div style="background: #f8fafc; border-radius: 12px; padding: 14px; margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                                        <a href="{{ route('hws.admin.quotations.create', $lead->id) }}" style="background: #3c50e0; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center;">
                                            📄 Create Quotation
                                        </a>

                                        @php
                                            $quotation = DB::table('hws_quotations')->where('lead_id', $lead->id)->first();
                                        @endphp

                                        @if ($quotation)
                                            <a href="{{ route('hws.admin.quotations.pdf', $quotation->id) }}" style="background: #0ea5e9; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center;">
                                                📥 Download PDF
                                            </a>
                                            <form method="POST" action="{{ route('hws.admin.quotations.email', $quotation->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="background: #10b981; color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer;">
                                                    📧 Email PDF
                                                </button>
                                            </form>
                                        @endif

                                        @if ($lead->status === 'won' && !$lead->task_id)
                                            <form method="POST" action="{{ route('hws.admin.sales-leads.convert-to-task', $lead->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="background: #f59e0b; color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer;">
                                                    ⚙ Convert to Service Task
                                                </button>
                                            </form>
                                        @elseif ($lead->task_id)
                                            <span style="background: #d1fae5; color: #065f46; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700;">
                                                ⚙ Service Task #{{ DB::table('hws_tasks')->where('id', $lead->task_id)->value('task_no') }} Linked
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Customer Details section -->
                                    <div style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #f1f5f9;">
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">CUSTOMER NAME</h4>
                                            <p style="font-weight: 600; color: #334155; margin: 0; font-size: 13px;">{{ $lead->customer_name }}</p>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">PHONE</h4>
                                            <p style="font-weight: 600; color: #334155; margin: 0; font-size: 13px;">{{ $lead->customer_phone }}</p>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">EMAIL</h4>
                                            <p style="font-weight: 600; color: #334155; margin: 0; font-size: 13px; word-break: break-all;">{{ $lead->customer_email ?? 'No email logged' }}</p>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">LEAD SOURCE</h4>
                                            <p style="font-weight: 600; color: #334155; margin: 0; font-size: 13px;">{{ $lead->source ?? 'Field Survey' }}</p>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">REFERENCE</h4>
                                            <p style="font-weight: 600; color: #334155; margin: 0; font-size: 13px;">{{ $lead->reference_no ?? 'SRV-'.$lead->id }}</p>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">ASSIGN AGENT</h4>
                                            <select onchange="updateLeadFieldInline('{{ $lead->id }}', 'assigned_to', this.value)" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; font-size: 12px; background: #fff; font-weight: 600; color: #475569;">
                                                <option value="">Unassigned</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ $lead->assigned_to == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">TEMPERATURE</h4>
                                            <select onchange="updateLeadFieldInline('{{ $lead->id }}', 'temperature', this.value)" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; font-size: 12px; background: #fff; font-weight: 600; color: #475569;">
                                                <option value="warm" {{ $lead->temperature === 'warm' ? 'selected' : '' }}>Warm</option>
                                                <option value="hot" {{ $lead->temperature === 'hot' ? 'selected' : '' }}>Hot</option>
                                                <option value="cold" {{ $lead->temperature === 'cold' ? 'selected' : '' }}>Cold</option>
                                            </select>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">PIPELINE STAGE</h4>
                                            <select onchange="updateLeadFieldInline('{{ $lead->id }}', 'status', this.value)" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; font-size: 12px; background: #fff; font-weight: 600; color: #475569;">
                                                <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                                                <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                                <option value="proposal_sent" {{ $lead->status === 'proposal_sent' ? 'selected' : '' }}>Proposal Sent</option>
                                                <option value="negotiation" {{ $lead->status === 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                                                <option value="won" {{ $lead->status === 'won' ? 'selected' : '' }}>Won</option>
                                                <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost</option>
                                            </select>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">NEXT FOLLOW-UP</h4>
                                            <input type="datetime-local" onchange="updateLeadFieldInline('{{ $lead->id }}', 'next_follow_up_at', this.value)" value="{{ $lead->next_follow_up_at ? date('Y-m-d\TH:i', strtotime($lead->next_follow_up_at)) : '' }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 3px 6px; font-size: 11px; background: #fff; font-weight: 600; color: #475569;"/>
                                        </div>
                                        <div style="grid-column: span 2;">
                                            <h4 style="font-size: 10px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">ADDRESS</h4>
                                            <p style="font-weight: 600; color: #334155; margin: 0; font-size: 13px;">{{ $lead->customer_address }}</p>
                                        </div>
                                    </div>

                                    <!-- Activity Timeline -->
                                    <h4 style="font-size: 12px; font-weight: 800; color: #475569; margin: 24px 0 12px; text-transform: uppercase;">Activity Log Timeline</h4>
                                    
                                    @php
                                        $activities = DB::table('hws_lead_activities')
                                            ->leftJoin('admins', 'hws_lead_activities.action_by', '=', 'admins.id')
                                            ->where('hws_lead_activities.survey_id', $lead->id)
                                            ->select('hws_lead_activities.*', 'admins.name as admin_name')
                                            ->orderByDesc('hws_lead_activities.created_at')
                                            ->get();
                                    @endphp

                                    <div style="border-left: 2px solid #e2e8f0; padding-left: 16px; margin-bottom: 24px; max-height: 220px; overflow-y: auto; padding-right: 8px;">
                                        @forelse($activities as $act)
                                            <div style="position: relative; margin-bottom: 16px;">
                                                <div style="position: absolute; left: -22px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #3c50e0;"></div>
                                                <span style="font-size: 11px; font-weight: 700; color: #94a3b8;">
                                                    {{ date('M d, Y h:i A', strtotime($act->created_at)) }} | By: {{ $act->admin_name ?? 'System' }} | Type: {{ strtoupper($act->activity_type) }}
                                                </span>
                                                <p style="margin: 4px 0 0; color: #334155; font-size: 13.5px; font-weight: 500;">
                                                    {{ $act->notes }}
                                                </p>
                                            </div>
                                        @empty
                                            <p style="font-size: 13px; color: #94a3b8; margin: 0; font-style: italic;">No activities logged yet.</p>
                                        @endforelse
                                    </div>

                                    @if ($lead->status !== 'won')
                                        <!-- Log New Activity Form -->
                                        <form onsubmit="submitActivityForm(event, this, '{{ $lead->id }}')" method="POST" action="{{ route('hws.admin.sales-leads.activity', $lead->id) }}" style="border-top: 1px solid #f1f5f9; padding-top: 20px;">
                                            @csrf
                                            <h4 style="font-size: 12px; font-weight: 800; color: #475569; margin: 0 0 12px; text-transform: uppercase;">Log New Interaction &amp; Reminder</h4>
                                            <div style="display: grid; grid-template-columns: 140px 1fr 180px auto; gap: 12px;">
                                                <select name="activity_type" required style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px; font-size: 13px; background: #fff;">
                                                    <option value="call">📞 Phone Call</option>
                                                    <option value="whatsapp">🟢 WhatsApp</option>
                                                    <option value="note">📝 Sales Note</option>
                                                    <option value="email">✉ Email sent</option>
                                                    <option value="meeting">🤝 Meeting Done</option>
                                                </select>
                                                <input type="text" name="notes" placeholder="Enter what was discussed with the client..." required style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 13px;"/>
                                                <input type="datetime-local" name="next_follow_up_at" title="Schedule Next Follow-up" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px; font-size: 13px; background: #fff;"/>
                                                <button type="submit" style="background: #3c50e0; color: #fff; border: 0; border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 700; cursor: pointer;">
                                                    Log
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div style="border-top: 1px solid #f1f5f9; padding-top: 16px; text-align: center; color: #065f46; font-weight: 700; font-size: 13px; background: #d1fae5; padding: 12px; border-radius: 8px; border: 1px dashed #34d399;">
                                            🔒 This lead is WON and successfully locked. No further modifications or logs allowed.
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

<script>
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.top = '24px';
        toast.style.right = '24px';
        toast.style.zIndex = '100000';
        toast.style.padding = '14px 28px';
        toast.style.borderRadius = '10px';
        toast.style.fontWeight = '700';
        toast.style.fontSize = '14px';
        toast.style.color = '#fff';
        toast.style.boxShadow = '0 10px 25px -5px rgba(0,0,0,0.15)';
        toast.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        
        if (type === 'success') {
            toast.style.background = '#10b981'; // Premium Emerald Green
            toast.innerText = '✓ ' + message;
        } else {
            toast.style.background = '#ef4444'; // Premium Crimson Red
            toast.innerText = '⚠ ' + message;
        }
        
        document.body.appendChild(toast);
        
        // Trigger slide/fade-in
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 50);
        
        // Slide/fade-out
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                toast.remove();
            }, 350);
        }, 3000);
    }

    function submitEditLeadForm(event, form, leadId) {
        event.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        
        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerText = 'Saving Changes...';
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
            
            if (data.success) {
                showToast(data.message || 'Lead updated successfully!', 'success');
                // Close modal
                document.getElementById(`editLead-${leadId}`).style.display = 'none';
                // Reload page after a short delay to sync UI lists
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('Something went wrong. Please try again.', 'error');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
            console.error('Error:', error);
            showToast('An error occurred while updating the lead.', 'error');
        });
    }

    function submitActivityForm(event, form, leadId) {
        event.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        
        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerText = 'Logging...';
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
            
            if (data.success) {
                // Clear input
                form.querySelector('input[name="notes"]').value = '';
                const followUpInput = form.querySelector('input[name="next_follow_up_at"]');
                if (followUpInput) {
                    followUpInput.value = '';
                }
                showToast(data.message || 'Activity logged successfully!', 'success');
                
                // Find timeline container for this lead and append the new activity dynamically
                const timelineModal = document.getElementById(`viewLead-${leadId}`);
                const timelineContainer = timelineModal.querySelector('div[style*="border-left"]');
                
                // If it had the "No activities logged yet" message, remove it
                const placeholder = timelineContainer.querySelector('p[style*="italic"]');
                if (placeholder) {
                    placeholder.remove();
                }
                
                const newActivityHtml = `
                    <div style="position: relative; margin-bottom: 16px;">
                        <div style="position: absolute; left: -22px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #3c50e0;"></div>
                        <span style="font-size: 11px; font-weight: 700; color: #94a3b8;">
                            ${data.activity.created_at} | By: ${data.activity.admin_name} | Type: ${data.activity.activity_type}
                        </span>
                        <p style="margin: 4px 0 0; color: #334155; font-size: 13.5px; font-weight: 500;">
                            ${data.activity.notes}
                        </p>
                    </div>
                `;
                
                timelineContainer.insertAdjacentHTML('afterbegin', newActivityHtml);
            } else {
                showToast('Failed to log activity. Please try again.', 'error');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
            console.error('Error:', error);
            showToast('An error occurred.', 'error');
        });
    }

    function updateLeadFieldInline(leadId, field, value) {
        const formData = new FormData();
        formData.append('field', field);
        formData.append('value', value);
        formData.append('_token', '{{ csrf_token() }}');

        // Show generic saving toast
        showToast('Saving update...', 'success');

        fetch(`/admin/field-service/sales-leads/${leadId}/patch`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Updated successfully!', 'success');
                
                // Update timeline list dynamically without page reload
                const timelineModal = document.getElementById(`viewLead-${leadId}`);
                const timelineContainer = timelineModal.querySelector('div[style*="border-left"]');
                if (timelineContainer && data.timelineHtml) {
                    timelineContainer.innerHTML = data.timelineHtml;
                }

                // If status was changed to Won, reload the page to apply locked UI status changes
                if (field === 'status' && data.status === 'won') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                showToast(data.message || 'Failed to update field.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while updating the field.', 'error');
        });
    }
</script>
@stop
