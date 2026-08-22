@php
    $navCategories = app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);
    $navServiceGroups = \Hws\FieldService\Support\ServiceCatalog::grouped();
    $supportPhone = '+91 97850 33795';
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
            @guest('customer')
                <a class="hws-account" href="{{ route('customer.session.index') }}" aria-label="Sign in to My Account">
                    <span class="hws-header-action__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.6-4 3-6 7-6s6.4 2 7 6"></path></svg>
                    </span>
                    <span class="hws-header-action__copy"><small>Sign in &amp; track</small><b>My account</b></span>
                </a>
            @else
                <a class="hws-account" href="{{ route('hws.customer.account.tracking') }}" aria-label="Open My Account">
                    <span class="hws-header-action__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.6-4 3-6 7-6s6.4 2 7 6"></path></svg>
                    </span>
                    <span class="hws-header-action__copy"><small>Welcome back</small><b>{{ auth()->guard('customer')->user()->first_name }}</b></span>
                </a>
            @endguest
            <div class="hws-cart">@include('shop::checkout.cart.mini-cart')</div>
        </div>
    </div>
    <nav class="hws-nav" aria-label="Main navigation"><div class="hws-container hws-nav__inner">
        <a href="{{ route('shop.home.index') }}">Home</a>
        
        {{-- Products Dropdown Menu --}}
        <div class="hws-dropdown-item">
            <span class="hws-nav-services__trigger">Products <svg viewBox="0 0 12 8" aria-hidden="true"><path d="m1 1 5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg></span>
            <div class="hws-nav-services__panel">
                <div class="hws-nav-services__intro">
                    <span>Indrayani Aquatech</span>
                    <h3>Products Catalog</h3>
                    <p>High performance water treatment, RO plants, chillers, STP/ETP & spare parts.</p>
                </div>
                <div class="hws-nav-services__groups">
                    @foreach ($navCategories as $category)
                        @if ($category->url_path || $category->slug)
                            <div>
                                <h4><a href="{{ route('shop.productOrCategory.index', $category->url_path ?? $category->slug) }}" style="color:inherit;text-decoration:none;">{{ $category->name }}</a></h4>
                                @if (count($category->children))
                                    <ul>
                                        @foreach ($category->children as $child)
                                            <li><a href="{{ route('shop.productOrCategory.index', $child->url_path ?? $child->slug) }}">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Services Dropdown Menu --}}
        <div class="hws-dropdown-item">
            <span class="hws-nav-services__trigger">Services <svg viewBox="0 0 12 8" aria-hidden="true"><path d="m1 1 5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg></span>
            <div class="hws-nav-services__panel">
                <div class="hws-nav-services__intro"><span>Engineering solutions</span><h3>Our core services</h3><p>Design, supply, installation and lifecycle support for complete water and hot-water systems.</p><a href="{{ route('hws.services.index') }}">Explore all services →</a></div>
                <div class="hws-nav-services__groups">
                    @foreach ($navServiceGroups as $group => $services)
                        <div><h4>{{ $group }}</h4><ul>@foreach ($services as $service)<li><a href="{{ route('hws.services.show', $service['slug']) }}">{{ $service['title'] }}</a></li>@endforeach</ul></div>
                    @endforeach
                </div>
            </div>
        </div>

        <a href="{{ route('hws.vision') }}">Our vision</a>
        <a href="{{ route('shop.home.index') }}#project-stories">Our projects</a>
        <a class="hws-nav__quote" href="#" data-hws-request="bulk_quote">Request bulk quote</a>
    </div></nav>
</header>
