@php
    $orderStatusMessages = [
        'pending' => trans('admin::app.notification.order-status-messages.pending'),
        'canceled' => trans('admin::app.notification.order-status-messages.canceled'),
        'closed' => trans('admin::app.notification.order-status-messages.closed'),
        'completed' => trans('admin::app.notification.order-status-messages.completed'),
        'processing' => trans('admin::app.notification.order-status-messages.processing'),
        'pending_payment' => trans('admin::app.notification.order-status-messages.pending_payment'),
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

        <button type="button" class="desktop-hamburger" @click="toggleMenu" title="Toggle Sidebar"
            style="background: transparent; border: 0; cursor: pointer; padding: 10px; display: flex; align-items: center; justify-content: center; margin-left: 6px; border-radius: 6px;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div class="brand-logo">
            <a href="{{ route('admin.dashboard.index') }}">
                @if (core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode()))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode())) }}"
                        alt="{{ config('app.name') }}" style="height: 40px; width: 110px;" />
                @else
                    <default-image light-theme-image-url="{{ asset('vendor/webkul/ui/assets/images/logo.png') }}?v=2"
                        dark-theme-image-url="{{ asset('vendor/webkul/ui/assets/images/logo_light.png') }}?v=2"
                        style="height: 40px; width: auto; max-width: 180px; object-fit: contain;"></default-image>
                @endif
            </a>
        </div>
    </div>

    <div class="navbar-top-right">
        <div class="profile" style="display:flex;align-items:center;gap:12px;">
            @if ($isSuperAdmin)
                <form method="POST" action="{{ route('hws.admin.branches.switch') }}"
                    style="margin:0;display:flex;align-items:center;">
                    @csrf
                    <div
                        style="display:flex;align-items:center;gap:6px;background:#f8fafc;border:1px solid #cbd5e1;padding:4px 10px;border-radius:8px;">
                        <span style="font-size:14px;">🏢</span>
                        <select name="branch_id" onchange="this.form.submit()"
                            style="border:none;background:transparent;font-weight:700;font-size:12px;color:#1e293b;cursor:pointer;outline:none;">
                            <option value="all" {{ empty($activeBranchId) ? 'selected' : '' }}>All Branches (HQ View)
                            </option>
                            @foreach ($allBranches as $b)
                                <option value="{{ $b->id }}" {{ $activeBranchId == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @elseif ($currentAdmin && $currentAdmin->branch_id)
                @php
                    $userBranch = $allBranches->firstWhere('id', $currentAdmin->branch_id);
                @endphp
                @if ($userBranch)
                    <div
                        style="display:flex;align-items:center;gap:6px;background:#e0e7ff;border:1px solid #c7d2fe;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:700;color:#3730a3;">
                        <span>🏢</span> {{ $userBranch->name }}
                    </div>
                @endif
            @endif

            @php
                $followUpQuery = \Hws\FieldService\Models\SiteSurvey::whereNotNull('next_follow_up_at')
                    ->where('status', '!=', 'won')
                    ->where('status', '!=', 'lost');
                \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($followUpQuery, 'hws_site_surveys');

                $dueFollowUps = $followUpQuery
                    ->where('next_follow_up_at', '<=', now()->endOfDay())
                    ->orderBy('next_follow_up_at', 'asc')
                    ->limit(5)
                    ->get();
                $dueFollowUpsCount = $dueFollowUps->count();
            @endphp

            <!-- Lead Follow-up Reminder Dropdown -->
            <div style="position: relative; display: inline-block;">
                <details style="position: relative;">
                    <summary
                        style="list-style: none; cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; width: 34px; height: 34px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px;"
                        title="{{ $dueFollowUpsCount }} Follow-ups Due Today/Overdue">
                        <span style="font-size: 16px;">⏰</span>
                        @if ($dueFollowUpsCount > 0)
                            <span
                                style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: #fff; font-size: 10px; font-weight: 800; border-radius: 999px; min-width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; padding: 0 4px; border: 1px solid #fff;">
                                {{ $dueFollowUpsCount }}
                            </span>
                        @endif
                    </summary>

                    <div
                        style="position: absolute; right: 0; top: 40px; width: 320px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); z-index: 9999; padding: 12px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 8px;">
                            <span
                                style="font-weight: 700; font-size: 12px; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px;">Follow-up
                                Reminders</span>
                            <span
                                style="font-size: 11px; background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ $dueFollowUpsCount }}
                                Due</span>
                        </div>

                        @forelse($dueFollowUps as $leadItem)
                            @php
                                $isOverdue = \Carbon\Carbon::parse($leadItem->next_follow_up_at)->isPast();
                            @endphp
                            <a href="{{ route('hws.admin.sales-leads.show', $leadItem->id) }}"
                                style="display: block; padding: 8px; border-radius: 6px; text-decoration: none; margin-bottom: 4px; background: {{ $isOverdue ? '#fff1f2' : '#f8fafc' }}; border: 1px solid {{ $isOverdue ? '#fecdd3' : '#e2e8f0' }}; transition: all 0.2s;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                                    <span
                                        style="font-weight: 700; font-size: 12px; color: #0f172a;">{{ $leadItem->customer_name }}</span>
                                    <span
                                        style="font-size: 10px; font-weight: 700; color: {{ $isOverdue ? '#e11d48' : '#0284c7' }};">
                                        {{ $isOverdue ? 'Overdue' : 'Today' }}
                                    </span>
                                </div>
                                <div style="font-size: 11px; color: #64748b;">
                                    📞 {{ $leadItem->customer_phone ?: 'No phone' }} · 🕒
                                    {{ \Carbon\Carbon::parse($leadItem->next_follow_up_at)->format('d M, h:i A') }}
                                </div>
                            </a>
                        @empty
                            <div style="padding: 12px; text-align: center; color: #94a3b8; font-size: 12px;">
                                ✓ No pending follow-ups due today!
                            </div>
                        @endforelse

                        <div
                            style="border-top: 1px solid #f1f5f9; padding-top: 8px; margin-top: 6px; text-align: center;">
                            <a href="{{ route('hws.admin.leads.trading') }}"
                                style="font-size: 11px; font-weight: 700; color: #3c50e0; text-decoration: none;">View
                                All Leads →</a>
                        </div>
                    </div>
                </details>
            </div>

            <div class="store">
                <div>
                    <a href="{{ route('shop.home.index') }}" target="_blank"
                        style="display: inline-block; vertical-align: middle;">
                        <span class="icon store-icon" data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('admin::app.layouts.visit-shop') }}"></span>
                    </a>
                </div>
            </div>

            <notification notif-title="{{ __('admin::app.notification.notification-title', ['read' => 0]) }}"
                get-notification-url="{{ route('admin.notification.get-notification') }}"
                view-all="{{ route('admin.notification.index') }}"
                order-view-url="{{ \URL::to('/') }}/{{ config('app.admin_url') }}/viewed-notifications/"
                pusher-key="{{ env('PUSHER_APP_KEY') }}" pusher-cluster="{{ env('PUSHER_APP_CLUSTER') }}"
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
                                    <img src="{{ auth()->guard('admin')->user()->image_url }}" />
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
                    <span
                        class="app-version">{{ __('admin::app.layouts.app-version', ['version' => 'v' . core()->version()]) }}</span>

                    <div class="dropdown-container">
                        <label>{{ __('admin::app.layouts.account-title') }}</label>
                        <ul>
                            <li>
                                <a
                                    href="{{ route('admin.account.edit') }}">{{ __('admin::app.layouts.my-account') }}</a>
                            </li>
                            <li>
                                <a
                                    href="{{ route('admin.session.destroy') }}">{{ __('admin::app.layouts.logout') }}</a>
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
