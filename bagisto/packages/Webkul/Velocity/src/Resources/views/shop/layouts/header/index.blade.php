@php
    $navCategories = app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);
    $supportPhone = '+91 98765 43210';
@endphp
<div class="hws-announcement">GST invoice on every order <span>•</span> Pan-India doorstep delivery <span>•</span> Free technical guidance</div>
<header class="hws-header">
    <div class="hws-header__main hws-container">
        <a class="hws-brand" href="{{ route('shop.home.index') }}" aria-label="Himalaya N Water Science home">
            <span class="hws-brand__mark">H</span><span><b>HIMALAYA N</b><small>WATER SCIENCE</small></span>
        </a>
        <form class="hws-search" action="{{ route('shop.search.index') }}" method="GET" role="search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
            <input name="term" type="search" value="{{ request('term') }}" placeholder="Search membranes, vessels, cartridges, SKU…" aria-label="Search products">
        </form>
        <div class="hws-header__actions">
            <a class="hws-help" href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}"><small>Engineer helpline</small><b>{{ $supportPhone }}</b></a>
            @guest('customer')<a class="hws-account" href="{{ route('customer.session.index') }}">Account</a>@else<a class="hws-account" href="{{ route('customer.profile.index') }}">{{ auth()->guard('customer')->user()->first_name }}</a>@endguest
            <div class="hws-cart">@include('shop::checkout.cart.mini-cart')</div>
        </div>
    </div>
    <nav class="hws-nav" aria-label="Main navigation"><div class="hws-container hws-nav__inner">
        <a href="{{ route('shop.home.index') }}">Home</a>
        @foreach ($navCategories as $category) @if ($category->slug)<a href="{{ route('shop.productOrCategory.index', $category->slug) }}">{{ $category->name }}</a>@endif @endforeach
        <a class="hws-nav__quote" href="mailto:info@himalayanwaterscience.com?subject=Bulk quotation request">Request bulk quote</a>
    </div></nav>
</header>
