@extends('admin::layouts.master')

@section('page_title')
    {{ request('_config.title', 'Projects') }}
@stop

@section('content-wrapper')
    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ request('_config.title', 'Projects') }}</h1>
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ url()->current() }}"></datagrid-plus>
        </div>
    </div>
@stop
