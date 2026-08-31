{{--
    Field Service admin pages used to render their own hard-coded left
    sidebar here on top of the default admin sidebar (admin::layouts.master
    already includes admin::layouts.nav-left) - that produced two sidebars
    and a duplicate "Dashboard" link. The default sidebar now carries the
    same new look and already lists all Field Service links (via
    admin-menu.php), so this layout just forwards into the standard content
    area instead of building a second navigation panel.
--}}
@extends('admin::layouts.content')

@section('content')
    @yield('page-content')
@endsection
