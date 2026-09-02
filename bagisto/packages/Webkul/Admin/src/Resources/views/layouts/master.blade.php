<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <title>@yield('page_title')</title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if ($favicon = core()->getConfigData('general.design.admin_logo.favicon', core()->getCurrentChannelCode()))
            <link rel="icon" sizes="16x16" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}" />
        @else
            <link rel="icon" sizes="16x16" href="{{ asset('vendor/webkul/ui/assets/images/favicon.ico') }}" />
        @endif

        <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/admin.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/hws/css/admin-field-service.css') }}?v=20260901-2">
        <link rel="stylesheet" href="{{ asset('vendor/hws/css/admin-menu-fixes.css') }}?v=20260901-2">
        <link rel="stylesheet" href="{{ asset('vendor/hws/css/admin-unified-theme.css') }}?v=20260902-3">
        <link rel="stylesheet" href="{{ asset('vendor/hws/css/admin-dashboard-modern.css') }}?v=20260901-1">

        @yield('head')

        @stack('css')

        {!! view_render_event('bagisto.admin.layout.head') !!}
    </head>

    <body @if (core()->getCurrentLocale() && core()->getCurrentLocale()->direction == 'rtl') class="rtl" @endif style="scroll-behavior: smooth;">

        {!! view_render_event('bagisto.admin.layout.body.before') !!}

        <div id="app">
            <flash-wrapper ref='flashes'></flash-wrapper>

            {!! view_render_event('bagisto.admin.layout.nav-top.before') !!}

            @include ('admin::layouts.nav-top')

            {!! view_render_event('bagisto.admin.layout.nav-top.after') !!}

            {!! view_render_event('bagisto.admin.layout.nav-left.before') !!}

            @include ('admin::layouts.nav-left')

            {!! view_render_event('bagisto.admin.layout.nav-left.after') !!}

            <div class="content-container" :class="isMenuOpen ? 'padding-container-navbar-expand' : 'padding-container-navbar-not-expand'">

                {!! view_render_event('bagisto.admin.layout.content.before') !!}

                @yield('content-wrapper')

                {!! view_render_event('bagisto.admin.layout.content.after') !!}

            </div>
        </div>

        <script type="text/javascript">
            window.flashMessages = [];

            if(localStorage.getItem('dark-mode') == 'true'){
                document.body.classList.toggle("dark-mode");
            }    

            @foreach (['success', 'warning', 'error', 'info'] as $key)
                @if ($value = session($key))
                    window.flashMessages.push({'type': 'alert-{{ $key }}', 'message': "{{ $value }}" });
                @endif
            @endforeach

            window.serverErrors = [];
            @if (isset($errors))
                @if (count($errors))
                    window.serverErrors = @json($errors->getMessages());
                @endif
            @endif
        </script>

        <script type="text/javascript" src="{{ asset('vendor/webkul/admin/assets/js/admin.js') }}"></script>

        <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>

        <script type="text/javascript">
            window.addEventListener('DOMContentLoaded', function() {
                moveDown = 60;
                moveUp =  -60;
                count = 0;
                countKeyUp = 0;
                pageDown = 60;
                pageUp = -60;
                scroll = 0;

                listLastElement = $('.menubar li:last-child').offset();

                if (listLastElement) {
                    lastElementOfNavBar = listLastElement.top;
                }

                navbarTop = $('.navbar-left').css("top");
                menuTopValue = $('.navbar-left').css('top');
                menubarTopValue = menuTopValue;

                documentHeight = $(document).height();
                menubarHeight = $('ul.menubar').height();
                navbarHeight = $('.navbar-left').height();
                windowHeight = $(window).height();
                contentHeight = $('.content').height();
                innerSectionHeight = $('.inner-section').height();
                gridHeight = $('.grid-container').height();
                pageContentHeight = $('.page-content').height();

                if (menubarHeight <= windowHeight) {
                    differenceInHeight = windowHeight - menubarHeight;
                } else {
                    differenceInHeight = menubarHeight - windowHeight;
                }

                if (menubarHeight > windowHeight) {
                    document.addEventListener("keydown", function(event) {
                        if ((event.keyCode == 38) && count <= 0) {
                            count = count + moveDown;

                            $('.navbar-left').css("top", count + "px");
                        } else if ((event.keyCode == 40) && count >= -differenceInHeight) {
                            count = count + moveUp;

                            $('.navbar-left').css("top", count + "px");
                        } else if ((event.keyCode == 33) && countKeyUp <= 0) {
                            countKeyUp = countKeyUp + pageDown;

                            $('.navbar-left').css("top", countKeyUp + "px");
                        } else if ((event.keyCode == 34) && countKeyUp >= -differenceInHeight) {
                            countKeyUp = countKeyUp + pageUp;

                            $('.navbar-left').css("top", countKeyUp + "px");
                        }
                    });

                    $("body").css({minHeight: $(".menubar").outerHeight() + 100 + "px"});

                    window.addEventListener('scroll', function() {
                        documentScrollWhenScrolled = $(document).scrollTop();

                        if (documentScrollWhenScrolled <= differenceInHeight + 200) {
                            $('.navbar-left').css('top', -documentScrollWhenScrolled + 60 + 'px');
                            scrollTopValueWhenNavBarFixed = $(document).scrollTop();
                        }
                    });
                }
            });

            window.hwsShowToast = function(message, type) {
                type = type || 'success';
                var toast = document.createElement('div');
                Object.assign(toast.style, {
                    position: 'fixed', top: '24px', right: '24px', zIndex: '100000',
                    padding: '12px 24px', borderRadius: '10px', fontWeight: '700',
                    fontSize: '13px', color: '#fff', boxShadow: '0 10px 25px -5px rgba(0,0,0,0.18)',
                    transition: 'all .35s cubic-bezier(.4,0,.2,1)', opacity: '0', transform: 'translateY(-20px)',
                    background: type === 'success' ? '#10b981' : '#ef4444',
                    display: 'flex', alignItems: 'center', gap: '8px'
                });
                toast.innerText = (type === 'success' ? '✓ ' : '⚠ ') + message;
                document.body.appendChild(toast);
                setTimeout(function() { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; }, 50);
                setTimeout(function() {
                    toast.style.opacity = '0'; toast.style.transform = 'translateY(-20px)';
                    setTimeout(function() { toast.remove(); }, 350);
                }, 3000);
            };

            window.hwsQuickAssignOrderManager = function(orderId, managerId, selectElem) {
                var originalBg = selectElem.style.background;
                selectElem.disabled = true;
                selectElem.style.background = '#e2e8f0';

                fetch('{{ url(config("app.admin_url", "admin")) }}/field-service/orders/' + orderId + '/assign-account-manager', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        account_manager_id: managerId || null
                    })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    selectElem.disabled = false;
                    selectElem.style.background = originalBg;
                    if (data.success) {
                        selectElem.style.borderColor = '#10b981';
                        setTimeout(function() { selectElem.style.borderColor = '#cbd5e1'; }, 2000);
                        window.hwsShowToast(data.message || 'Account manager assigned successfully!', 'success');
                    } else {
                        window.hwsShowToast('Failed to assign account manager.', 'error');
                    }
                })
                .catch(function() {
                    selectElem.disabled = false;
                    selectElem.style.background = originalBg;
                    window.hwsShowToast('Error assigning account manager.', 'error');
                });
            };
        </script>

        @stack('scripts')

        {!! view_render_event('bagisto.admin.layout.body.after') !!}

        <div class="modal-overlay"></div>

    </body>
</html>
