@extends('admin::layouts.content')

@section('page_title')
    {{ request('_config.title', 'Projects') }}
@stop

@section('content')
    <div class="content">
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
