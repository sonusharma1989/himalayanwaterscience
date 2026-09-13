@extends('hws::admin.layouts.menu')

@section('page_title')
    Attendance
@stop

@push('css')
    <!-- Fancybox 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <style>
        .fancybox__container {
            z-index: 99999 !important;
        }
    </style>
@endpush

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

@push('scripts')
    <!-- Fancybox 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Fancybox) {
                Fancybox.bind('[data-fancybox="attendance-gallery"]', {
                    loop: true,
                    Thumbs: false,
                    Toolbar: {
                        display: {
                            left: ["infobar"],
                            middle: ["zoomIn", "zoomOut", "toggle1to1", "rotateCCW", "rotateCW", "flipX", "flipY"],
                            right: ["slideshow", "fullscreen", "download", "thumbs", "close"],
                        },
                    },
                });
            }
        });
    </script>
@endpush
