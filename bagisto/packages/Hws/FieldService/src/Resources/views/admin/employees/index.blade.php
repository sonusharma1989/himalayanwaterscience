@extends('hws::admin.layouts.menu')

@section('page_title')
    Field Service Employees
@stop

@section('page-content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Employees & Technicians</h1>
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ route('hws.admin.employees.index') }}"></datagrid-plus>
        </div>
    </div>
@stop
