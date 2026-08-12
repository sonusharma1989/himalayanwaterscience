@extends('hws::admin.layouts.menu')

@section('page_title')
    Service Requests
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
            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Service Requests & Tasks</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0;">Installation requests, product servicing, and client complaints.</p>
        </div>
        <div>
            <button onclick="document.getElementById('newTaskForm').style.display='block'; this.style.display='none';" id="addNewBtn" style="background: #3c50e0; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; border: 0; cursor: pointer; font-size: 14px;">
                + New Service Request
            </button>
        </div>
    </div>

    <!-- Create Request Form -->
    <div id="newTaskForm" style="display: none; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin: 0;">Create Service Request</h3>
            <button onclick="document.getElementById('newTaskForm').style.display='none'; document.getElementById('addNewBtn').style.display='block';" style="background: transparent; border: 0; color: #64748b; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
        </div>

        <form method="POST" action="{{ route('hws.admin.service-requests.store') }}">
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
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Type</label>
                    <select name="type" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                        <option value="service">Service</option>
                        <option value="installation">Installation</option>
                        <option value="amc_service">AMC Service</option>
                        <option value="complaint">Complaint</option>
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
                <button type="submit" style="background: #3c50e0; color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; border: 0; cursor: pointer; font-size: 14px;">Save Request</button>
            </div>
        </form>
    </div>

    <!-- Filters Section -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);">
        <form method="GET" action="{{ route('hws.admin.service-requests.index') }}" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Step Status</label>
                <select name="step" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 13.5px; background: #fff; min-width: 140px;">
                    <option value="">All Statuses</option>
                    <option value="0" {{ request('step') === '0' ? 'selected' : '' }}>Scheduled</option>
                    <option value="1" {{ request('step') === '1' ? 'selected' : '' }}>Started / Travel</option>
                    <option value="2" {{ request('step') === '2' ? 'selected' : '' }}>Job Commenced</option>
                    <option value="3" {{ request('step') === '3' ? 'selected' : '' }}>Sign-off Pending</option>
                    <option value="4" {{ request('step') === '4' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Priority</label>
                <select name="priority" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 13.5px; background: #fff; min-width: 140px;">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: #3c50e0; color: #fff; border: 0; border-radius: 8px; padding: 7px 16px; font-size: 13.5px; font-weight: 600; cursor: pointer;">
                    Filter
                </button>
                <a href="{{ route('hws.admin.service-requests.index') }}" style="background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 8px; padding: 7px 16px; font-size: 13.5px; font-weight: 600; text-align: center;">
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
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Task No</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Type</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Customer Details</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Assigned Technician</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Priority</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Step / Status</th>
                    <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $task)
                    <tr style="border-bottom: 1px solid #f8fafc;">
                        <td style="padding: 14px 20px; color: #334155; font-weight: 500; white-space: nowrap;">
                            {{ $task->created_at ? $task->created_at->format('M d, Y') : '—' }}
                        </td>
                        <td style="padding: 14px 20px; font-weight: 700; color: #1e293b;">
                            #{{ $task->task_no }}
                        </td>
                        <td style="padding: 14px 20px;">
                            <span style="display: inline-flex; border-radius: 6px; padding: 4px 8px; font-size: 11px; font-weight: 700;
                                background: #f1f5f9; color: #334155;">
                                {{ ucfirst(str_replace('_', ' ', $task->type)) }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="font-weight: 600; color: #334155;">{{ $task->customer_name }}</div>
                            <div style="font-size: 12px; color: #64748b;">📞 {{ $task->customer_phone }}</div>
                            <div style="font-size: 11px; color: #94a3b8; max-width: 250px; margin-top: 4px;" class="truncate" title="{{ $task->customer_address }}">
                                {{ $task->customer_address }}
                            </div>
                        </td>
                        <td style="padding: 14px 20px; color: #334155; font-weight: 500;">
                            {{ $task->assignee->name ?? 'Unassigned' }}
                        </td>
                        <td style="padding: 14px 20px;">
                            <span style="display: inline-flex; font-weight: 700; font-size: 11px; text-transform: uppercase;
                                color: {{ $task->priority === 'urgent' || $task->priority === 'high' ? '#dc2626' : '#475569' }}">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px;">
                            @php
                                $statusLabels = [
                                    0 => 'Scheduled',
                                    1 => 'Started / Travel',
                                    2 => 'Job Commenced',
                                    3 => 'Sign-off Pending',
                                    4 => 'Completed'
                                ];
                                $step = $task->step;
                            @endphp
                            <span style="display: inline-flex; border-radius: 100px; padding: 4px 10px; font-size: 11px; font-weight: 700;
                                background: {{ $step === 4 ? '#d1fae5' : '#fffbeb' }};
                                color: {{ $step === 4 ? '#065f46' : '#854d0e' }};">
                                {{ $statusLabels[$step] ?? 'Unknown' }} (Step {{ $step }}/4)
                            </span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                <button onclick="document.getElementById('editTask-{{ $task->id }}').style.display='block'" style="background: transparent; border: 0; color: #3c50e0; font-weight: 600; cursor: pointer; font-size: 13px;">
                                    Edit
                                </button>
                                <span>·</span>
                                <button onclick="document.getElementById('viewTask-{{ $task->id }}').style.display='block'" style="background: transparent; border: 0; color: #475569; font-weight: 600; cursor: pointer; font-size: 13px;">
                                    Details
                                </button>
                            </div>

                            <!-- Edit Modal -->
                            <div id="editTask-{{ $task->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 9999; align-items: center; justify-content: center;">
                                <div style="background: #fff; border-radius: 16px; padding: 24px; width: 400px; margin: 10% auto; text-align: left; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                        <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin: 0;">Edit Task #{{ $task->task_no }}</h3>
                                        <button onclick="document.getElementById('editTask-{{ $task->id }}').style.display='none'" style="background: transparent; border: 0; color: #64748b; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
                                    </div>
                                    <form method="POST" action="{{ route('hws.admin.service-requests.update', $task->id) }}">
                                        @csrf
                                        <div style="margin-bottom: 16px;">
                                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Assignee</label>
                                            <select name="assigned_to" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                                <option value="">Unassigned</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ $task->assigned_to == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div style="margin-bottom: 16px;">
                                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Priority</label>
                                            <select name="priority" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                                <option value="normal" {{ $task->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="urgent" {{ $task->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                                <option value="high" {{ $task->priority === 'high' ? 'selected' : '' }}>High</option>
                                                <option value="low" {{ $task->priority === 'low' ? 'selected' : '' }}>Low</option>
                                            </select>
                                        </div>
                                        <div style="margin-bottom: 24px;">
                                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Workflow Step</label>
                                            <select name="step" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                                <option value="0" {{ $task->step === 0 ? 'selected' : '' }}>0 · Scheduled</option>
                                                <option value="1" {{ $task->step === 1 ? 'selected' : '' }}>1 · Started / Travel</option>
                                                <option value="2" {{ $task->step === 2 ? 'selected' : '' }}>2 · Job Commenced</option>
                                                <option value="3" {{ $task->step === 3 ? 'selected' : '' }}>3 · Sign-off Pending</option>
                                                <option value="4" {{ $task->step === 4 ? 'selected' : '' }}>4 · Completed</option>
                                            </select>
                                        </div>
                                        <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                            <button type="button" onclick="document.getElementById('editTask-{{ $task->id }}').style.display='none'" style="background: #f1f5f9; color: #475569; border: 0; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">Cancel</button>
                                            <button type="submit" style="background: #3c50e0; color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- View Details Modal -->
                            <div id="viewTask-{{ $task->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 9999; align-items: center; justify-content: center; overflow-y: auto;">
                                <div style="background: #fff; border-radius: 16px; padding: 28px; width: 600px; max-width: 90%; margin: 5% auto; text-align: left; box-shadow: 0 10px 25px -3px rgba(0,0,0,0.1);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                                        <div>
                                            <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                {{ ucfirst($task->type) }}
                                            </span>
                                            <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 6px 0 0;">Task Details #{{ $task->task_no }}</h3>
                                        </div>
                                        <button onclick="document.getElementById('viewTask-{{ $task->id }}').style.display='none'" style="background: transparent; border: 0; color: #64748b; font-size: 22px; cursor: pointer; font-weight: 700;">×</button>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 20px;">
                                        <div>
                                            <h4 style="font-size: 11px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">Customer</h4>
                                            <p style="font-weight: 600; color: #334155; margin: 0;">{{ $task->customer_name }}</p>
                                            <p style="font-size: 13px; color: #64748b; margin: 2px 0 0;">📞 {{ $task->customer_phone }}</p>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 11px; text-transform: uppercase; color: #94a3b8; margin: 0 0 4px; font-weight: 700;">Address</h4>
                                            <p style="font-size: 13px; color: #334155; margin: 0;">{{ $task->customer_address }}</p>
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 20px; background: #f8fafc; border-radius: 12px; padding: 16px;">
                                        <h4 style="font-size: 11px; text-transform: uppercase; color: #94a3b8; margin: 0 0 8px; font-weight: 700;">Work Description</h4>
                                        <p style="font-size: 13.5px; color: #334155; line-height: 1.5; margin: 0;">
                                            {{ $task->work_description ?? 'No work description submitted yet.' }}
                                        </p>
                                    </div>

                                    <!-- Materials Used -->
                                    <div style="margin-bottom: 20px;">
                                        <h4 style="font-size: 11px; text-transform: uppercase; color: #94a3b8; margin: 0 0 8px; font-weight: 700;">Materials Consumed</h4>
                                        @if($task->materials->isEmpty())
                                            <p style="font-size: 13px; color: #94a3b8; margin: 0;">No materials used.</p>
                                        @else
                                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                                @foreach ($task->materials as $mat)
                                                    <span style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; font-size: 12.5px; font-weight: 600; color: #475569;">
                                                        {{ $mat->name }} x{{ $mat->quantity }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Before/After Photos -->
                                    <div style="margin-bottom: 20px;">
                                        <h4 style="font-size: 11px; text-transform: uppercase; color: #94a3b8; margin: 0 0 8px; font-weight: 700;">Service Photos</h4>
                                        @if($task->photos->isEmpty())
                                            <p style="font-size: 13px; color: #94a3b8; margin: 0;">No photos uploaded.</p>
                                        @else
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 12px;">
                                                @foreach ($task->photos as $photo)
                                                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #f8fafc; text-align: center;">
                                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($photo->file_path) }}" target="_blank">
                                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->file_path) }}" style="width: 100%; height: 80px; object-fit: cover; border-bottom: 1px solid #e2e8f0;"/>
                                                        </a>
                                                        <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; padding: 4px 0;">
                                                            {{ $photo->type }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Client Signature -->
                                    <div>
                                        <h4 style="font-size: 11px; text-transform: uppercase; color: #94a3b8; margin: 0 0 8px; font-weight: 700;">Client Handover Signature</h4>
                                        @if ($task->signature_path)
                                            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; width: fit-content; background: #f8fafc;">
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($task->signature_path) }}" style="height: 80px; max-width: 300px; object-fit: contain;"/>
                                            </div>
                                        @else
                                            <p style="font-size: 13px; color: #94a3b8; margin: 0;">Not signed yet.</p>
                                        @endif
                                    </div>
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
