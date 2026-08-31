@extends('shop::customers.account.index')

@section('page_title')
    {{ __('shop::app.customer.account.profile.index.title') }}
@endsection

@section('account-content')
    <div class="account-layout">
        <div class="account-head">
            <span class="back-icon"><a href="{{ route('customer.profile.index') }}"><i class="icon icon-menu-back"></i></a></span>

            <span class="account-heading">{{ __('shop::app.customer.account.profile.index.title') }}</span>

            <span class="account-action">
                <a href="{{ route('customer.profile.edit') }}">{{ __('shop::app.customer.account.profile.index.edit') }}</a>
            </span>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('bagisto.shop.customers.account.profile.view.before', ['customer' => $customer]) !!}

        <div class="account-table-content hws-profile-content">
            <dl class="hws-profile-grid">
                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.before', ['customer' => $customer]) !!}

                <div class="hws-profile-field">
                    <dt>{{ __('shop::app.customer.account.profile.fname') }}</dt>
                    <dd>{{ $customer->first_name }}</dd>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.first_name.after', ['customer' => $customer]) !!}

                <div class="hws-profile-field">
                    <dt>{{ __('shop::app.customer.account.profile.lname') }}</dt>
                    <dd>{{ $customer->last_name }}</dd>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.last_name.after', ['customer' => $customer]) !!}

                <div class="hws-profile-field">
                    <dt>{{ __('shop::app.customer.account.profile.gender') }}</dt>
                    <dd>{{ $customer->gender ? __($customer->gender) : '—' }}</dd>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.gender.after', ['customer' => $customer]) !!}

                <div class="hws-profile-field">
                    <dt>{{ __('shop::app.customer.account.profile.dob') }}</dt>
                    <dd>{{ $customer->date_of_birth ?: '—' }}</dd>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.date_of_birth.after', ['customer' => $customer]) !!}

                <div class="hws-profile-field hws-profile-field--wide">
                    <dt>{{ __('shop::app.customer.account.profile.email') }}</dt>
                    <dd>{{ $customer->email }}</dd>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.after', ['customer' => $customer]) !!}
            </dl>
        </div>

        {!! view_render_event('bagisto.shop.customers.account.profile.view.after', ['customer' => $customer]) !!}
    </div>
@endsection
