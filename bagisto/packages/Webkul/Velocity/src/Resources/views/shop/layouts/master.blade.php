<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>@yield('page_title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url()->to('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {!! view_render_event('bagisto.shop.layout.head') !!}
    @yield('head')
    @yield('seo')
    @if ($favicon = core()->getCurrentChannel()->favicon_url)
        <link rel="icon" sizes="16x16" href="{{ $favicon }}">
    @endif
    @include('shop::layouts.styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kalnia:wght@300;400;500;600&family=Red+Hat+Display:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('hws-storefront.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('hws-storefront-polish.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('hws-spacing-fix.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('hws-category-sidebar.css') }}?v=1">
    @stack('css')
</head>
<body @if (core()->getCurrentLocale() && core()->getCurrentLocale()->direction === 'rtl') class="rtl" @endif>
    {!! view_render_event('bagisto.shop.layout.body.before') !!}
    <div id="app">
        <product-quick-view v-if="$root.quickView"></product-quick-view>
        <div class="hws-site">
            {!! view_render_event('bagisto.shop.layout.header.before') !!}
            @include('shop::layouts.header.index')
            {!! view_render_event('bagisto.shop.layout.header.after') !!}
            <main class="hws-main">
                {!! view_render_event('bagisto.shop.layout.content.before') !!}
                @yield('content-wrapper')
                @yield('full-content-wrapper')
                {!! view_render_event('bagisto.shop.layout.content.after') !!}
            </main>
            {!! view_render_event('bagisto.shop.layout.footer.before') !!}
            @include('shop::layouts.footer.index')
            {!! view_render_event('bagisto.shop.layout.footer.after') !!}
        </div>
        <velocity-overlay-loader></velocity-overlay-loader>
        <go-top bg-color="#E27A34"></go-top>
    </div>
    <div id="alert-container"></div>
    {!! view_render_event('bagisto.shop.layout.body.after') !!}
    @include('shop::layouts.scripts')
</body>
</html>
