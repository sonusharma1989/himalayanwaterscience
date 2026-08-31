@php

    $tree = \Webkul\Core\Tree::create();

    foreach (config('core') as $item) {
        $tree->add($item);
    }

    $tree->items = core()->sortItems($tree->items);

    $config = $tree;

    $allLocales = core()->getAllLocales()->pluck('name', 'code');

    /* Field Service menu keys (Config/admin-menu.php in the FieldService
       package) don't ship a real icon image, so these render as inline SVG
       instead of the image-based classes the core menu items use. */
    $fieldServiceIcons = [
        'icon-service'   => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'icon-dashboard' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'icon-customers' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'icon-clock'     => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'icon-warning'   => '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        'icon-briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'icon-inventory' => '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/>',
        'icon-card'      => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'icon-document'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
    ];
@endphp

{{-- New UI skin for the admin sidebar. Kept as an override layer (instead of
     editing the compiled/SCSS admin.css) so it renders without an asset
     rebuild, matching how the Field Service module ships its screens. --}}
<style>
    .navbar-left {
        background-color: #ffffff;
        border-right: 1px solid #eef1f6;
    }

    .navbar-left ul.menubar {
        padding: 8px;
        box-sizing: border-box;
    }

    .navbar-left ul.menubar li.menu-item {
        border-radius: 10px;
        margin-bottom: 2px;
    }

    .navbar-left ul.menubar li.menu-item > a.menubar-anchor {
        border-radius: 10px;
        display: flex;
        align-items: center;
    }

    .navbar-left ul.menubar li.menu-item:hover {
        background: #f1f5f9 !important;
        border-radius: 10px;
    }

    .navbar-left ul.menubar li.menu-item .menu-label {
        font-weight: 600 !important;
        font-size: 13px;
    }

    .navbar-left.open ul.menubar li.menu-item.active {
        background: #3c50e0 !important;
        border-radius: 10px;
    }

    .navbar-left.open ul.menubar li.menu-item.active > a .menu-label {
        color: #ffffff !important;
    }

    .navbar-left:not(.open) ul.menubar li.menu-item.active {
        background: #eef1ff !important;
        border-radius: 10px;
    }

    .navbar-left ul.sub-menubar {
        border-radius: 10px;
        overflow: hidden;
    }

    .navbar-left ul.sub-menubar li.sub-menu-item a {
        padding-left: 44px;
        font-size: 13px;
    }

    .navbar-left.open ul.menubar li.menu-item.active ul.sub-menubar {
        background-color: #f8fafc !important;
        margin-top: 4px;
    }

    .navbar-left.open ul.menubar li.menu-item.active ul.sub-menubar li.sub-menu-item.active,
    .navbar-left.open ul.menubar li.menu-item.active ul.sub-menubar li.sub-menu-item:hover {
        background-color: #e0e7ff !important;
        border-radius: 8px;
    }

    .navbar-left.open ul.menubar li.menu-item.active ul.sub-menubar li.sub-menu-item.active .menu-label,
    .navbar-left.open ul.menubar li.menu-item.active ul.sub-menubar li.sub-menu-item:hover .menu-label {
        color: #3c50e0 !important;
    }

    /* Field Service menu icons render as real inline <svg> elements (see the
       @php $fieldServiceIcons map below) instead of background/mask images,
       so they don't depend on data-URI mask support. This just colors them. */
    .navbar-left .field-service-icon {
        flex-shrink: 0;
        margin-right: 12px;
        color: #64748b;
    }

    .navbar-left ul.menubar li.menu-item:hover .field-service-icon {
        color: #3c50e0;
    }

    .navbar-left ul.menubar li.menu-item.active .field-service-icon {
        color: #3c50e0;
    }

    .navbar-left.open ul.menubar li.menu-item.active .field-service-icon {
        color: #ffffff;
    }

    /* Long, multi-word menu names (e.g. "Service Operation") must stay on one
       line like every other top-level item instead of wrapping mid-word and
       crowding the row above/below it. */
    .navbar-left ul.menubar li.menu-item > a.menubar-anchor .menu-label {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
        flex: 1 1 auto;
    }
</style>

<div class="navbar-left" v-bind:class="{'open': isMenuOpen}">

    <ul class="menubar">
        @foreach ($menu->items as $menuItem)
        <li class="menu-item {{ $menu->getActive($menuItem) }}">
            <a class="menubar-anchor"  href="{{ $menuItem['url'] }}">
                @if (isset($fieldServiceIcons[$menuItem['icon-class']]))
                    <svg class="field-service-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $fieldServiceIcons[$menuItem['icon-class']] !!}</svg>
                @else
                    <span class="icon-menu icon {{ $menuItem['icon-class'] }}"></span>
                @endif

                <span class="menu-label" title="{{ trans($menuItem['name']) }}">{{ trans($menuItem['name']) }}</span>

                @if(count($menuItem['children']) || $menuItem['key'] == 'configuration' )
                    <span
                        class="icon arrow-icon {{ $menu->getActive($menuItem) == 'active' ? 'rotate-arrow-icon' : '' }} {{ ( core()->getCurrentLocale() && core()->getCurrentLocale()->direction == 'rtl' ) ? 'arrow-icon-right' :'arrow-icon-left' }}"
                        ></span>

                @endif
            </a>
            @if ($menuItem['key'] != 'configuration')
                @if (count($menuItem['children']))
                    <ul class="sub-menubar">
                        @foreach ($menuItem['children'] as $subMenuItem)
                            <li class="sub-menu-item {{ $menu->getActive($subMenuItem) }}">
                                <a href="{{ count($subMenuItem['children']) ? current($subMenuItem['children'])['url'] : $subMenuItem['url'] }}">
                                    <span class="menu-label">{{ trans($subMenuItem['name']) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @else
                <ul class="sub-menubar">
                    @foreach ($config->items as $key => $item)
                        <li class="sub-menu-item {{ $item['key'] == request()->route('slug') ? 'active' : '' }}">
                            <a href="{{ route('admin.configuration.index', $item['key']) }}">
                                <span class="menu-label"> {{ isset($item['name']) ? trans($item['name']) : '' }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
        @endforeach
    </ul>

    <nav-slide-button id="nav-expand-button" icon-class="accordian-right-icon"></nav-slide-button>
</div>

@push('scripts')

    <script>

        $(document).ready(function () {
            $(".menubar-anchor").click(function() {
                if ( $(this).parent().attr('class') == 'menu-item active' ) {
                    $(this).parent().removeClass('active');
                    $('.arrow-icon-left').removeClass('rotate-arrow-icon');
                    $('.arrow-icon-right').removeClass('rotate-arrow-icon');
                    $(".sub-menubar").hide();
                    event.preventDefault();
                }
            });
        });

    </script>

@endpush