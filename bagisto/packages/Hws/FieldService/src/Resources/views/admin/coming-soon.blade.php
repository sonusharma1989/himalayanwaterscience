@extends('admin::layouts.master')

@section('page_title')
    {{ $title }}
@stop

@section('content-wrapper')
    <div class="content full-page">
    <div class="flex flex-col gap-4 p-4 sm:p-6">
        <div class="flex items-center justify-between">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                {{ $title }}
            </p>
        </div>

        <div class="flex flex-col items-center justify-center gap-3 rounded-lg bg-white p-16 text-center dark:bg-gray-900">
            <span class="icon-inventory text-4xl text-gray-300 dark:text-gray-600"></span>
            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                {{ $title }} is coming soon
            </p>
            <p class="max-w-sm text-sm text-gray-400 dark:text-gray-500">
                This section isn't built out yet. The dashboard is fully live — this page is a placeholder so navigation doesn't break.
            </p>
        </div>
    </div>
    </div>
@stop
