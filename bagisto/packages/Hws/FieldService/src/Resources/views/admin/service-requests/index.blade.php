@extends('hws::admin.layouts.menu')

@section('page_title')
    Service Requests
@stop

@section('page-content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Service Requests & Tasks</h1>
            </div>

            <div class="page-action">
                <button type="button" onclick="document.getElementById('newTaskForm').style.display='block'; this.style.display='none';" id="addNewBtn" class="btn btn-lg btn-primary">
                    + New Service Request
                </button>
            </div>
        </div>

        <div class="page-content">
            @if (session('success'))
                <div style="padding: 12px 20px; background: #d1fae5; color: #065f46; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Create Request Form -->
            <div id="newTaskForm" style="display: none; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin: 0;">Create Service Request</h3>
                    <button type="button" onclick="document.getElementById('newTaskForm').style.display='none'; document.getElementById('addNewBtn').style.display='inline-flex';" style="background: transparent; border: 0; color: #64748b; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
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
                        <button type="submit" class="btn btn-lg btn-primary">Save Request</button>
                    </div>
                </form>
            </div>

            <datagrid-plus src="{{ route('hws.admin.service-requests.index') }}"></datagrid-plus>
        </div>
    </div>
@stop
