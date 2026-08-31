@php

    $tree = \Webkul\Core\Tree::create();

    foreach (config('core') as $item) {
        $tree->add($item);
    }

    $tree->items = core()->sortItems($tree->items);

    $config = $tree;

    $allLocales = core()->getAllLocales()->pluck('name', 'code');

@endphp

{{-- New UI skin for the admin sidebar. Kept as an override layer (instead of
     editing the compiled/SCSS admin.css) so it renders without an asset
     rebuild, matching how the Field Service module ships its screens. --}}
<style>
    .navbar-left {
        background-color: #ffffff;
        border-right: 1px solid #eef1f6;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    .navbar-left ul.menubar {
        padding: 8px;
        box-sizing: border-box;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
    }

    .navbar-left ul.menubar li.menu-item {
        border-radius: 10px;
        margin-bottom: 2px;
    }

    .navbar-left ul.menubar li.menu-item > a.menubar-anchor {
        border-radius: 10px;
        display: flex;
        align-items: center;
        min-height: 46px;
        box-sizing: border-box;
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
        margin: 4px 0 6px;
        padding: 4px;
    }

    .navbar-left ul.sub-menubar li.sub-menu-item a {
        padding-left: 44px;
        font-size: 13px;
        min-height: 38px;
        display: flex;
        align-items: center;
        border-radius: 8px;
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

    .navbar-left:not(.open) .field-service-icon {
        margin-right: 0;
    }

    .navbar-left:not(.open) ul.menubar li.menu-item > a.menubar-anchor {
        justify-content: center;
    }
</style>

<div class="navbar-left" v-bind:class="{'open': isMenuOpen}">

    <ul class="menubar">
        @foreach ($menu->items as $menuItem)
        <li class="menu-item {{ $menu->getActive($menuItem) }}">
            <a class="menubar-anchor"  href="{{ $menuItem['url'] }}">
                <span class="icon-menu icon {{ $menuItem['icon-class'] }}"></span>

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
            $(".menubar-anchor").click(function(event) {
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
