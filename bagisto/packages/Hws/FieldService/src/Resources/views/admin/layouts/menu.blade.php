@extends('admin::layouts.master')

@section('content-wrapper')
<div style="display: flex; gap: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; align-items: flex-start; padding: 24px;">
    <!-- Custom Left Sidebar -->
    <div style="width: 240px; min-width: 240px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 8px; height: fit-content;">
        
        <a href="{{ route('hws.admin.dashboard.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;
            background: {{ request()->routeIs('hws.admin.dashboard.index') ? '#3c50e0' : 'transparent' }};
            color: {{ request()->routeIs('hws.admin.dashboard.index') ? '#fff' : '#64748b' }};">
            <span style="font-size: 16px;">📈</span> Dashboard
        </a>

        <a href="{{ route('hws.admin.employees.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;
            background: {{ request()->routeIs('hws.admin.employees.index') ? '#3c50e0' : 'transparent' }};
            color: {{ request()->routeIs('hws.admin.employees.index') ? '#fff' : '#64748b' }};">
            <span style="font-size: 16px;">👥</span> Employees
        </a>

        <a href="{{ route('hws.admin.attendance.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;
            background: {{ request()->routeIs('hws.admin.attendance.index') ? '#3c50e0' : 'transparent' }};
            color: {{ request()->routeIs('hws.admin.attendance.index') ? '#fff' : '#64748b' }};">
            <span style="font-size: 16px;">⏱</span> Attendance
        </a>

        <a href="{{ route('hws.admin.service-requests.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;
            background: {{ request()->routeIs('hws.admin.service-requests.index') ? '#3c50e0' : 'transparent' }};
            color: {{ request()->routeIs('hws.admin.service-requests.index') ? '#fff' : '#64748b' }};">
            <span style="font-size: 16px;">⚠️</span> Service requests
        </a>

        <a href="{{ route('hws.admin.sales-leads.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;
            background: {{ request()->routeIs('hws.admin.sales-leads.index') ? '#3c50e0' : 'transparent' }};
            color: {{ request()->routeIs('hws.admin.sales-leads.index') ? '#fff' : '#64748b' }};">
            <span style="font-size: 16px;">💼</span> Sales & leads
        </a>

        <a href="{{ route('hws.admin.inventory.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;
            background: {{ request()->routeIs('hws.admin.inventory.index') ? '#3c50e0' : 'transparent' }};
            color: {{ request()->routeIs('hws.admin.inventory.index') ? '#fff' : '#64748b' }};">
            <span style="font-size: 16px;">📦</span> Inventory
        </a>

        <a href="{{ route('hws.admin.expenses.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;
            background: {{ request()->routeIs('hws.admin.expenses.index') ? '#3c50e0' : 'transparent' }};
            color: {{ request()->routeIs('hws.admin.expenses.index') ? '#fff' : '#64748b' }};">
            <span style="font-size: 16px;">💳</span> Expenses
        </a>

        <a href="{{ route('hws.admin.reports.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;
            background: {{ request()->routeIs('hws.admin.reports.index') ? '#3c50e0' : 'transparent' }};
            color: {{ request()->routeIs('hws.admin.reports.index') ? '#fff' : '#64748b' }};">
            <span style="font-size: 16px;">📄</span> Reports
        </a>

    </div>

    <!-- Main Content Area -->
    <div style="flex: 1; min-width: 0;">
        @yield('page-content')
    </div>
</div>
@stop
