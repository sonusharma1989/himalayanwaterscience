@php
    $orderStatusMessages = [
        'pending' => trans('admin::app.notification.order-status-messages.pending'),
        'canceled'=> trans('admin::app.notification.order-status-messages.canceled'),
        'closed' => trans('admin::app.notification.order-status-messages.closed'),
        'completed'=> trans('admin::app.notification.order-status-messages.completed'),
        'processing' => trans('admin::app.notification.order-status-messages.processing'),
        'pending_payment' => trans('admin::app.notification.order-status-messages.pending_payment')
    ];
    $allLocales = core()->getAllLocales()->pluck('name', 'code');
    $currentAdmin = auth()->guard('admin')->user();
    $isSuperAdmin = \Hws\FieldService\Helpers\BranchScopeHelper::isSuperAdmin();
    $allBranches = \Hws\FieldService\Models\Branch::where('status', 1)->get();
    $activeBranchId = \Hws\FieldService\Helpers\BranchScopeHelper::getActiveBranchId();
@endphp

<div class="navbar-top">
    <div class="navbar-top-left">
        @include ('admin::layouts.mobile-nav')

        <style>
            .brand-logo img {
                width: 30%;
                /* margin-top: 2%; */
            }
        </style>

        <div class="brand-logo">
            <a href="{{ route('admin.dashboard.index') }}">
                @if (core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode()))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode())) }}" alt="{{ config('app.name') }}" style="height: 40px; width: 110px;"/>
                @else
                    <default-image
                        light-theme-image-url="{{ asset('vendor/webkul/ui/assets/images/logo.png') }}?v=2"
                        dark-theme-image-url="{{ asset('vendor/webkul/ui/assets/images/logo_light.png') }}?v=2"
                    ></default-image>
                @endif
            </a>
        </div>
    </div>

    <div class="navbar-top-right">
        <div class="profile" style="display:flex;align-items:center;gap:12px;">
            @if ($isSuperAdmin)
                <form method="POST" action="{{ route('hws.admin.branches.switch') }}" style="margin:0;display:flex;align-items:center;">
                    @csrf
                    <div style="display:flex;align-items:center;gap:6px;background:#f8fafc;border:1px solid #cbd5e1;padding:4px 10px;border-radius:8px;">
                        <span style="font-size:14px;">🏢</span>
                        <select name="branch_id" onchange="this.form.submit()" style="border:none;background:transparent;font-weight:700;font-size:12px;color:#1e293b;cursor:pointer;outline:none;">
                            <option value="all" {{ empty($activeBranchId) ? 'selected' : '' }}>All Branches (HQ View)</option>
                            @foreach ($allBranches as $b)
                                <option value="{{ $b->id }}" {{ $activeBranchId == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @elseif ($currentAdmin && $currentAdmin->branch_id)
                @php($userBranch = $allBranches->firstWhere('id', $currentAdmin->branch_id))
                @if ($userBranch)
                    <div style="display:flex;align-items:center;gap:6px;background:#e0e7ff;border:1px solid #c7d2fe;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:700;color:#3730a3;">
                        <span>🏢</span> {{ $userBranch->name }}
                    </div>
                @endif
            @endif

            <div class="store">
                <div>
                    <a href="{{ route('shop.home.index') }}" target="_blank" style="display: inline-block; vertical-align: middle;">
                        <span class="icon store-icon" data-toggle="tooltip" data-placement="bottom" title="{{ __('admin::app.layouts.visit-shop') }}"></span>
                    </a>
                </div>
            </div>

            <notification
                notif-title="{{ __('admin::app.notification.notification-title', ['read' => 0]) }}"
                get-notification-url="{{ route('admin.notification.get-notification') }}"
                view-all="{{ route('admin.notification.index') }}"
                order-view-url="{{ \URL::to('/') }}/{{ config('app.admin_url')}}/viewed-notifications/"
                pusher-key="{{ env('PUSHER_APP_KEY') }}"
                pusher-cluster="{{ env('PUSHER_APP_CLUSTER') }}"
                title="{{ __('admin::app.notification.title-plural') }}"
                view-all-title="{{ __('admin::app.notification.view-all') }}"
                get-read-all-url="{{ route('admin.notification.read-all') }}"
                order-status-messages="{{ json_encode($orderStatusMessages) }}"
                read-all-title="{{ __('admin::app.notification.read-all') }}"
                locale-code={{ core()->getCurrentLocale()->code }}>

                <div class="notifications">
                    <div class="dropdown-toggle">
                        <i class="icon notification-icon active" style="margin-left: 0px;"></i>
                    </div>
                </div>

            </notification>

            <div class="profile-info">
                <div class="dropdown-toggle">
                    <div style="display: inline-block; vertical-align: middle;">
                        <div class="profile-info-div">
                            @if (auth()->guard('admin')->user()->image)
                                <div class="profile-info-icon">
                                    <img src="{{ auth()->guard('admin')->user()->image_url }}"/>
                                </div>
                            @else
                                <div class="profile-info-icon">
                                    <span>{{ substr(auth()->guard('admin')->user()->name, 0, 1) }}</span>
                                </div>
                            @endif

                            <div class="profile-info-desc">
                                <span class="name">
                                    {{ auth()->guard('admin')->user()->name }}
                                </span>

                                <span class="role">
                                    {{ auth()->guard('admin')->user()->role['name'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <i class="icon arrow-down-icon active"></i>
                </div>

                <div class="dropdown-list bottom-right">
                    <span class="app-version">{{ __('admin::app.layouts.app-version', ['version' => 'v' . core()->version()]) }}</span>

                    <div class="dropdown-container">
                        <label>{{ __('admin::app.layouts.account-title') }}</label>
                        <ul>
                            <li>
                                <a href="{{ route('admin.account.edit') }}">{{ __('admin::app.layouts.my-account') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.session.destroy') }}">{{ __('admin::app.layouts.logout') }}</a>
                            </li>
                            <li v-if="!isMobile()" style="display: flex;justify-content: space-between;">
                                <div style="margin-top:7px">{{ __('admin::app.layouts.mode') }}</div>
                                <dark style="margin-top: -9px;width: 83px;"></dark>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>