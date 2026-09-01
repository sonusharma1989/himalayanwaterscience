@extends('hws::admin.layouts.menu')

@section('page_title')
    Attendance
@stop

@section('page-content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Attendance</h1>
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ route('hws.admin.attendance.index') }}"></datagrid-plus>
        </div>
    </div>
@stop
