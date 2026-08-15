@extends('shop::layouts.master')

@php
    $channel = core()->getCurrentChannel();
    $homeSEO = $channel->home_seo ? json_decode($channel->home_seo) : null;
    $categories = app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree($channel->root_category_id);
    $products = DB::table('product_flat')
        ->where('locale', app()->getLocale())
        ->where('channel', core()->getRequestedChannelCode())
        ->where('status', 1)
        ->where('visible_individually', 1)
        ->whereNotNull('url_key')
        ->orderByDesc('featured')
        ->orderByDesc('new')
        ->limit(8)
        ->get();
@endphp

@section('page_title'){{ $homeSEO->meta_title ?? 'Himalaya N Water Science — Water Treatment Components' }}@endsection

@section('head')
    <meta name="description" content="{{ $homeSEO->meta_description ?? 'Industrial and domestic water-treatment components, transparent pricing and practical engineering support across India.' }}">
@endsection

@section('content-wrapper')
<section class="hws-hero">
    <div class="hws-container hws-hero__grid">
        <div class="hws-hero__copy">
            <span class="hws-eyebrow">Water treatment components · Pan-India</span>
            <h1>Every component.<br><em>Zero guesswork.</em></h1>
            <p>Membranes, vessels, cartridges, chemicals and instruments—factory-direct, spec-accurate, with an engineer one call away.</p>
            <div class="hws-hero__buttons"><a class="hws-btn hws-btn--primary" href="#catalog">Browse the catalog</a><a class="hws-btn hws-btn--outline" href="mailto:info@himalayanwaterscience.com?subject=Technical consultation">Talk to an engineer</a></div>
            <div class="hws-hero__trust"><span>GST invoice on every order</span><span>Transparent pricing</span><span>Free technical support</span></div>
        </div>
        <div class="hws-hero__visual" aria-label="Industrial water treatment system illustration">
            <div class="hws-water-card"><span class="hws-water-card__label">ENGINEERED WATER SYSTEMS</span><div class="hws-water-card__drop"></div><div class="hws-water-card__stats"><span><b>99.9%</b><small>contaminant control</small></span><span><b>Pan-India</b><small>project delivery</small></span></div></div>
        </div>
    </div>
</section>

<section class="hws-usp"><div class="hws-container hws-usp__grid">
    <article><b>01</b><div><h3>Transparent project pricing</h3><p>Clear component pricing and GST-ready invoices for every order.</p></div></article>
    <article><b>02</b><div><h3>Specification accuracy</h3><p>Product selection backed by practical water-treatment experience.</p></div></article>
    <article><b>03</b><div><h3>Free technical support</h3><p>Sizing, installation and commissioning guidance from our team.</p></div></article>
</div></section>

<section class="hws-section" id="catalog"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Browse the range</span><h2>Shop by system</h2></div><a href="{{ route('shop.search.index') }}?term=water">View all products →</a></div>
    <div class="hws-category-grid">
        @forelse ($categories->take(6) as $index => $category)
            @if ($category->slug)
                <a class="hws-category" href="{{ route('shop.productOrCategory.index', $category->slug) }}"><span class="hws-category__number">0{{ $index + 1 }}</span><div><h3>{{ $category->name }}</h3><p>Explore components</p></div><span class="hws-category__arrow">↗</span></a>
            @endif
        @empty
            <a class="hws-category" href="{{ route('shop.search.index') }}?term=membrane"><span class="hws-category__number">01</span><div><h3>RO Membranes</h3><p>Explore components</p></div><span>↗</span></a>
        @endforelse
    </div>
</div></section>

<section class="hws-section hws-section--soft"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Field-proven essentials</span><h2>Most specified</h2></div></div>
    <div class="hws-product-grid">
        @forelse ($products as $product)
            @php $productUrl = $product->url_key ? route('shop.productOrCategory.index', $product->url_key) : route('shop.search.index', ['term' => $product->name]); @endphp
            <article class="hws-product">
                <a class="hws-product__media" href="{{ $productUrl }}"><div class="hws-product__symbol"><i></i><i></i><i></i></div><span>HWS SELECTED</span></a>
                <div class="hws-product__body"><span class="hws-product__type">Water treatment component</span><a href="{{ $productUrl }}"><h3>{{ $product->name }}</h3></a><p>Commercial grade · Technical support included</p><div class="hws-product__buy"><div><b>{{ core()->currency($product->price) }}</b><small>excl. applicable GST</small></div><a href="{{ $productUrl }}">View</a></div></div>
            </article>
        @empty
            <div class="hws-empty">Products added in Admin → Catalog will automatically appear here.</div>
        @endforelse
    </div>
</div></section>

<section class="hws-oem"><div class="hws-container hws-oem__inner"><div><span class="hws-eyebrow">For OEMs & system integrators</span><h2>Building plants at scale?</h2><p>Get project-site delivery, consolidated GST billing, datasheet packs and a dedicated engineer for sizing and commissioning.</p></div><a class="hws-btn hws-btn--primary" href="mailto:info@himalayanwaterscience.com?subject=Bulk project quotation">Request a bulk quote</a></div></section>
@endsection
