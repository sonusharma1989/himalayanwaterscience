@extends('admin::layouts.content')

@section('page_title')
    Company Branches
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Company Branches</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('hws.admin.branches.reports') }}" class="btn btn-lg" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;font-weight:600;margin-right:8px;">
                    Branch Analytics
                </a>
                <a href="{{ route('hws.admin.branches.create') }}" class="btn btn-lg btn-primary">
                    + Add New Branch
                </a>
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ route('hws.admin.branches.index') }}"></datagrid-plus>
        </div>
    </div>
@stop
