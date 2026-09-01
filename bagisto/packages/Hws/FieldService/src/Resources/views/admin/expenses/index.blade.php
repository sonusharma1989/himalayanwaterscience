@extends('hws::admin.layouts.menu')

@section('page_title')
    Expense Claims
@stop

@section('page-content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Expense Claims</h1>
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ route('hws.admin.expenses.index') }}"></datagrid-plus>
        </div>
    </div>
@stop
